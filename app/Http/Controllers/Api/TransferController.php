<?php

namespace App\Http\Controllers\Api;

use App\Code;
use App\CodesPrices;
use App\Debt;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\PushNotifications;

class TransferController extends Controller
{
    use PushNotifications;

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

    protected $transferStatus = [
        0 => 'Не выбрано',
        1 => 'Вопрос',
        2 => 'Не выдан',
        3 => 'Выдано',
        4 => 'Отменен',
        5 => 'Возврат',
        6 => 'Обработка',
        7 => 'Отменен клиентом',
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
        return Transfer::select('transfers.*', 'codes.code as client_name', 'users.name as user_name')
            ->join('codes', 'transfers.client_id', '=', 'codes.id')
            ->join('users', 'transfers.user_id', '=', 'users.id')
            ->orderBy('id', 'DESC');
    }

    public function index()
    {
        return response(['transfers' => $this->query()->get()]);
    }

    public function update(Request $request)
    {
        $data = $this->stripData($request->except('id'));
        if (array_key_exists('issued_by', $data)) {
//            $data['issued_by'] = date("Y-m-d H:i:s", strtotime($data['issued_by']));
//            $data['issued_by'] = \Illuminate\Support\Carbon::parse(strtotime($data['issued_by']))->toDateTimeString();
            if ($data['issued_by']) {
                $data['issued_by'] = date("Y-m-d H:i:s", strtotime($data['issued_by']));
            } else {
                $data['issued_by'] = null;
            }

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
        // PUSH
        $players = User::where([['code_id', '=', 0], ['id', '<>', 11], ['player_id', '<>', null], ['id', '<>', auth()->user()->id]])->get();
        $notificationData = null;
        if ($players) {
            $playersIds = $players->map(function ($item) {
                return json_decode($item->player_id);
            })->flatten();
            $transfer = Transfer::find($request->id);
            $customer = Code::where('id', $transfer->client_id)->first();
            if ($customer) {
                $notificationText = null;
                if ($request->sum && $request->status) {
                    $notificationText = 'Пользователь ' . auth()->user()->name . ' изменил сумму перевода на - <' . $request->sum . '>' . ' статус - <' . $this->transferStatus[$request->status] . '> для ' . $customer->code;
                } else if ($request->sum) {
                    $notificationText = 'Пользователь ' . auth()->user()->name . ' изменил сумму перевода на - <' . $request->sum . '> для ' . $customer->code;
                } else if ($request->status) {
                    $notificationText = ' Пользователь ' . auth()->user()->name . ' изменил статус перевода на - <' . $this->transferStatus[$request->status] . '> для (' . $customer->code . ' - ' . $transfer->sum . ')';
                }
                $notificationData = ['text' => $notificationText, 'player_ids' => $playersIds, 'url' => 'https://cargo007.net/#/moder/transfers'];
                $this->createNotification($notificationData);
            }
        } else if ((auth()->user()->player_id && auth()->user()->code_id)) {
            $notificationText = null;
            $transfer = Transfer::find($request->id);
            if ($request->sum && $request->status) {
                $notificationText = 'Изменены данные перевода для ' . $transfer->receiver_name . ' сумма - <' . $request->sum . '>' . ' статус - <' . $this->transferStatus[$request->status] . '>';
            } else if ($request->sum) {
                $notificationText = 'Изменена сумма перевода для ' . $transfer->receiver_name . ' на - <' . $request->sum . '>';
            } else if ($request->status) {
                $notificationText = 'Изменен статус перевода для ' . $transfer->receiver_name . ' - <' . $this->transferStatus[$request->status] . '> на сумму ' . $transfer->sum;
            }
            $notificationData = ['text' => $notificationText, 'player_ids' => json_decode(auth()->user()->player_id), 'url' => 'https://cargo007.net/#/moder/client-transfers'];
            $this->createNotification($notificationData);
        }
        return response(['transfer' => $this->query()->where('transfers.id', $request->id)->first()]);
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
            'issued_by' => null,
            'user_id' => auth()->user()->id,
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
        $players = User::where([['code_id', '=', 0], ['id', '<>', 11], ['player_id', '<>', null], ['id', '<>', auth()->user()->id]])->get();
        $notificationData = null;
        if ($players) {
            $playersIds = $players->map(function ($item) {
                return json_decode($item->player_id);
            })->flatten();
            $customer = Code::where('id', $transferArr['client_id'])->first();
            if ($customer) {
                $notificationData = ['text' => 'Пользователь ' . auth()->user()->name . ' добавил перевод клиенту - ' . $customer->code . ' на сумму - ' . $request->sum, 'player_ids' => $playersIds, 'url' => 'https://cargo007.net/#/moder/transfers'];
                $this->createNotification($notificationData);
            }
        } else if (auth()->user()->player_id && auth()->user()->code_id) {
            $notificationData = ['text' => 'Добавлен перевод для <' . $request->receiver_name . '> на сумму - ' . $request->sum . ' статус - <' . $this->transferStatus[$request->status] . '>', 'player_ids' => json_decode(auth()->user()->player_id), 'url' => 'https://cargo007.net/#/moder/client-transfers'];
            $this->createNotification($notificationData);
        }

        return response(['transfer' => $this->query()->where('transfers.id', $transfer->id)->first()]);
    }

    public function getNewTransfers(Request $request)
    {
        $this->validate($request, [
            'lastCreatedId' => 'required|integer',
            'updatedAt' => 'required|date',
        ]);
        return response(['transfers' => $this->query()
            ->where('transfers.id', '>', $request->lastCreatedId)
            ->orWhere('transfers.updated_at', '>', $request->updatedAt)
            ->get()]);
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
        $debts = Debt::all();
        $transfersData->each(function ($item) use ($debts, $date) {
            if (!$debts->contains('transfer_id', $item->id)) {
                $data = $item->toArray();
                $data['sum'] = $data['sum'] * -1;
                $data['code_client_id'] = $data['client_id'];
                $data['transfer_id'] = $data['id'];
                $data['created_at'] = date("Y-m-d H:i:s", strtotime($date));

                $codeCommission = CodesPrices::where('code_id', $data['client_id'])->first();
                if ($codeCommission && $codeCommission->commission) {
                    $data['commission'] = round(($data['sum'] / 100) * $codeCommission->commission);
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
        return response(['transfers' => $this->query()->where('transfers.client_id', auth()->user()->id)->get()]);
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
}
