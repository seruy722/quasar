<?php

namespace App\Http\Controllers\Api;

use App\Code;
use App\CodesSettings;
use App\Debt;
use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\PushNotifications;
use App\Traits\TraitTelegram;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    use PushNotifications, TraitTelegram;

    protected $rules = [
//        'client_id' => 'required|numeric',
        'receiver_name' => 'required|string|max:255',
        'receiver_phone' => 'required|string|max:100',
        'sum' => 'required|numeric',
        'method' => 'required|numeric',
        'status' => 'required|numeric',
        'issued_by' => 'nullable|date',
        'notation' => 'nullable|string|max:255',
    ];

    public function stripData($value)
    {
        $func = function ($value) {
            if (!$value) {
                return null;
            }
            return $value;
        };
        $arr = array_map("trim", $value);
        $arr = array_map("stripcslashes", $arr);
        $arr = array_map("strip_tags", $arr);
        $arr = array_map($func, $arr);
        return $arr;
    }

    public function query()
    {
        return Transfer::select('transfers.*', 'codes.code as client_name', 'users.name as user_name', 'codes_settings.transfer_commission as transfer_static_commission', 'statuses.name as status_label')
            ->join('codes', 'transfers.client_id', '=', 'codes.id')
            ->join('users', 'transfers.user_id', '=', 'users.id')
            ->leftJoin('codes_settings', 'transfers.client_id', '=', 'codes_settings.code_client_id')
            ->leftJoin('statuses', 'transfers.status', '=', 'statuses.id')
            ->orderByDesc('id');
    }

    public function index()
    {
        $yesterday = Carbon::yesterday();
        $yesterdayTransfers = $this->query()->whereDate('transfers.created_at', '>=', $yesterday->toDateString())->get();
        if ($yesterdayTransfers->count() >= 10) {
            return response(['transfers' => $yesterdayTransfers->unique()]);
        }
        $transfers = $this->query()->take(50)->get();
        return response(['transfers' => $transfers->unique()]);
    }

    public function store(Request $request)
    {
        $transferArr = [
            'client_id' => $request->client_id,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'sum' => $request->sum,
            'method' => $request['method'],
            'status' => $request->status,
            'transfer_commission' => $request->transfer_commission,
            'issued_by' => null,
            'user_id' => auth()->user()->id,
        ];

        if ($request->has('transfer_commission')) {
            CodesSettings::updateOrCreate(['code_client_id' => $request->client_id], ['transfer_commission' => $request->transfer_commission]);
        }

        if ($request->issued_by) {
            $transferArr['issued_by'] = date("Y-m-d H:i:s", strtotime($request->issued_by));
        }
        if ($request->notation) {
            $transferArr['notation'] = $request->notation;
        }

        $this->validate($request, $this->rules);

        $transfer = Transfer::create($this->stripData($transferArr));
        $this->storeTransferHistory($transfer->id, $this->stripData($transferArr), 'create');
        $transferData = $this->query()->where('transfers.id', $transfer->id)->first();
        // Реактивное обновление данных на странице
        event(new \App\Events\Transfers(['transfer' => $transferData, 'type' => 'store']));

        $message = 'Добавлен перевод №' . $transferData->id . ' на сумму: ' . $transferData->sum . ', статус: ' . $transferData->status_label;
        $this->sendToTelegram($transferData->client_id, $message);

        return response(['transfer' => $transferData]);
    }

    public function sendToTelegram($clientId, $message)
    {
        // Отправка сообщения в телеграмм
        $code = Code::with('customers')->where('id', $clientId)->first();
        if (!empty($code->customers)) {
            foreach ($code->customers as $customer) {
                $chatId = $customer->telegram_user_id;
                if ($chatId && intval($chatId)) {
                    $this->sendTelegramMessage($chatId, $message);
                }
            }
        }
    }

    public function update(Request $request)
    {
        $data = $this->stripData($request->except('id'));
        if (array_key_exists('issued_by', $data)) {
            if ($data['issued_by']) {
                $data['issued_by'] = date("Y-m-d H:i:s", strtotime($data['issued_by']));
            } else {
                $data['issued_by'] = null;
            }

        }

        if ($request->has('transfer_commission')) {
            $codeClientId = $request->client_id;
            if (!$request->has('client_id')) {
                $transfer = Transfer::find($request->id);
                $codeClientId = $transfer->client_id;
            }

            CodesSettings::updateOrCreate(['code_client_id' => $codeClientId], ['transfer_commission' => $request->transfer_commission]);
        }

        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        Transfer::where('id', $request->id)->update($data);
        $this->storeTransferHistory($request->id, $data, 'update');
        $transferData = $this->query()->where('transfers.id', $request->id)->first();

        event(new \App\Events\Transfers(['transfer' => $transferData, 'type' => 'update']));

        if ($request->has('sum') || $request->has('status')) {
            $message = 'Обновлен перевод №' . $transferData->id . ' сумма: ' . $transferData->sum . ', статус: ' . $transferData->status_label;
            $this->sendToTelegram($transferData->client_id, $message);
        }

        return response(['transfer' => $transferData]);
    }

    public function getTransferCodeCommission($id)
    {
        return response(['transfer' => CodesSettings::where('code_client_id', $id)->first()]);
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TransfersExport($request->ids), 'transfers.xlsx');
    }

    public function storeTransferHistory($id, $data, $action)
    {
        if (array_key_exists('client_id', $data)) {
            $code = \App\Code::find($data['client_id']);
            if ($code) {
                $data['client_name'] = $code->code;
            }

        }
        if (array_key_exists('issued_by', $data)) {
            $data['issued_by'] = \Illuminate\Support\Carbon::parse($data['issued_by'])->toAtomString();
        }
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => (new Transfer)->getTable(), 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }

    public function getTransferHistory($id)
    {
        $transfersHistory = \App\History::where('table', (new Transfer)->getTable())->where('entry_id', $id)->get();
        return response(['transferHistory' => $transfersHistory]);
    }

    public function addTransfersToDebts(Request $request)
    {
        $entryIds = $request->ids;
        $date = $request->date;
        $transfersData = Transfer::whereIn('id', $entryIds)->get();
        $transfersData->each(function ($item) use ($date) {
            if (!Debt::where('transfer_id', $item->id)->first()) {
                $data = $item->toArray();
                $data['sum'] = $data['sum'] * -1;
                $data['code_client_id'] = $data['client_id'];
                $data['transfer_id'] = $data['id'];
                $data['created_at'] = date("Y-m-d H:i:s", strtotime($date));

                $codeCommission = CodesSettings::where('code_client_id', $data['client_id'])->first();
                if ($codeCommission) {
                    $data['commission'] = round(($data['sum'] / 100) * $codeCommission->transfer_commission);
                } else {
                    $data['commission'] = round(($data['sum'] / 100) * 1);
                }

                Debt::create($data);
            }
        });
        return response()->json(null, 201);
    }

    public function indexClient()
    {
        return response(['transfers' => $this->query()->where('transfers.client_id', auth()->user()->code_id)->get()]);
    }

    public function storeClient(Request $request)
    {
        $transferArr = [
            'client_id' => $request->user()->id,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'sum' => $request->sum,
            'method' => $request['method'],
            'status' => $request->status,
            'issued_by' => null,
            'user_id' => $request->user()->id,
        ];

        if ($request->issued_by) {
            $transferArr['issued_by'] = date("Y-m-d H:i:s", strtotime($request->issued_by));
        }
        if ($request->notation) {
            $transferArr['notation'] = $request->notation;
        }

        $this->validate($request, $this->rules);

        $transfer = Transfer::create($this->stripData($transferArr));
        $this->storeTransferHistory($transfer->id, $this->stripData($transferArr), 'create');
        return response(['transfer' => $this->query()->where('transfers.id', $transfer->id)->get()]);
    }

    public function getStatistics(Request $request)
    {
        $statuses = DB::table('statuses')->get();
        $answer = [];
        $value = $request->selectValue;
        if ($value === -1) {
            foreach ($statuses as $status) {
                $transfers = DB::table('transfers')
                    ->select('transfers.sum', 'transfers.user_id', 'transfers.status', 'statuses.name as status_name', 'users.name as user_name')
                    ->leftJoin('users', 'transfers.user_id', '=', 'users.id')
                    ->leftJoin('statuses', 'transfers.status', '=', 'statuses.id')
                    ->where('transfers.status', $status->id)
                    ->get();
                if ($transfers->count()) {
                    $answer[$status->name] = ['count' => $transfers->count(), 'sum' => $transfers->sum('sum'), 'countUserTransfers' => $transfers];
                }
            }
        } else if ($value === 3 || $value === 0) {
            $date = date("Y-m-d", strtotime($request->values['day']));
            foreach ($statuses as $status) {
                $transfers = DB::table('transfers')
                    ->select('transfers.sum', 'transfers.user_id', 'transfers.status', 'statuses.name as status_name', 'users.name as user_name')
                    ->leftJoin('users', 'transfers.user_id', '=', 'users.id')
                    ->leftJoin('statuses', 'transfers.status', '=', 'statuses.id')
                    ->where('transfers.status', $status->id)
                    ->whereDate('transfers.created_at', $date)
                    ->get();
                if ($transfers->count()) {
                    $answer[$status->name] = ['count' => $transfers->count(), 'sum' => $transfers->sum('sum'), 'countUserTransfers' => $transfers];
                }
            }
        } else if ($value === 2) {
            $dateFrom = date("Y-m-d", strtotime($request->values['from']));
            $dateTo = date("Y-m-d", strtotime($request->values['to']));
            foreach ($statuses as $status) {
                $transfers = DB::table('transfers')
                    ->select('transfers.sum', 'transfers.user_id', 'transfers.status', 'statuses.name as status_name', 'users.name as user_name')
                    ->leftJoin('users', 'transfers.user_id', '=', 'users.id')
                    ->leftJoin('statuses', 'transfers.status', '=', 'statuses.id')
                    ->where('transfers.status', $status->id)
                    ->whereDate('transfers.created_at', '>=', $dateFrom)
                    ->whereDate('transfers.created_at', '<=', $dateTo)
                    ->get();
                if ($transfers->count()) {
                    $answer[$status->name] = ['count' => $transfers->count(), 'sum' => $transfers->sum('sum'), 'countUserTransfers' => $transfers];
                }
            }
        } else if ($value === 0) {
            $dateFrom = date("Y-m-d", strtotime($request->values['from']));
            $dateTo = date("Y-m-d", strtotime($request->values['to']));
            foreach ($statuses as $status) {
                $transfers = DB::table('transfers')
                    ->select('transfers.sum', 'transfers.user_id', 'transfers.status', 'statuses.name as status_name', 'users.name as user_name')
                    ->leftJoin('users', 'transfers.user_id', '=', 'users.id')
                    ->leftJoin('statuses', 'transfers.status', '=', 'statuses.id')
                    ->where('transfers.status', $status->id)
                    ->whereDate('transfers.created_at', '>=', $dateFrom)
                    ->whereDate('transfers.created_at', '<=', $dateTo)
                    ->get();
                if ($transfers->count()) {
                    $answer[$status->name] = ['count' => $transfers->count(), 'sum' => $transfers->sum('sum'), 'countUserTransfers' => $transfers->pluck('user_id')];
                }
            }
        }
        return response(['statistics' => $answer]);
    }

    public function search(Request $request)
    {
        ['value' => $value, 'field' => $field] = $request;

        if ($field === 'client_name') {
            return response(['transfers' => $this->query()->where('transfers.client_id', $value)->get()]);
        }

        if ($field === 'receiver_name') {
            return response(['transfers' => $this->query()->where('transfers.receiver_name', 'like', '%' . $value . '%')->get()]);
        }

        if ($field === 'receiver_phone') {
            return response(['transfers' => $this->query()->where('transfers.receiver_phone', 'like', '%' . $value . '%')->get()]);
        }

        if ($field === 'sum') {
            return response(['transfers' => $this->query()->where('transfers.sum', $value)->get()]);
        }

        if ($field === 'paid') {
            return response(['transfers' => $this->query()->where('transfers.paid', $value)->get()]);
        }

        if ($field === 'method') {
            return response(['transfers' => $this->query()->where('transfers.method', $value)->get()]);
        }

        if ($field === 'status') {
            return response(['transfers' => $this->query()->where('transfers.status', $value)->get()]);
        }

        if ($field === 'created_at') {
            return response(['transfers' => $this->query()->whereDate('transfers.created_at', $value)->get()]);
        }

        if ($field === 'issued_by') {
            return response(['transfers' => $this->query()->whereDate('transfers.issued_by', $value)->get()]);
        }

        if ($field === 'notation') {
            return response(['transfers' => $this->query()->where('transfers.notation', 'like', '%' . $value . '%')->get()]);
        }

        if ($field === 'user_name') {
            return response(['transfers' => $this->query()->where('users.id', $value)->get()]);
        }

        if ($value) {
            return response(['transfers' => $this->query()
                ->orWhere('codes.code', 'like', '%' . $value . '%')
                ->orWhere('transfers.sum', 'like', '%' . $value . '%')
                ->orWhere('transfers.receiver_name', 'like', '%' . $value . '%')
                ->orWhere('transfers.receiver_phone', 'like', '%' . $value . '%')
                ->orWhere('transfers.notation', 'like', '%' . $value . '%')
                ->orWhere('transfers.created_at', 'like', '%' . $value . '%')
                ->orWhere('transfers.issued_by', 'like', '%' . $value . '%')
                ->orWhere('users.name', 'like', '%' . $value . '%')
                ->orWhere('statuses.name', 'like', '%' . $value . '%')
                ->get()]);
        }

        return response(['not_variant' => 2222]);
    }
}
