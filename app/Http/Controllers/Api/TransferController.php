<?php

namespace App\Http\Controllers\Api;

use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferController extends Controller
{
    protected $rules = [
        'client_id' => 'required|numeric',
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
        if (array_key_exists('issued_by', $data) && $data['issued_by']) {
//            $data['issued_by'] = date("Y-m-d H:i:s", strtotime($data['issued_by']));
            $data['issued_by'] = \Illuminate\Support\Carbon::parse(strtotime($data['issued_by']))->toDateTimeString();
        }

        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        Transfer::where('id', $request->id)->update($data);
        $this->storeTransferHistory($request->id, $data);
        return response(['transfer' => $this->query()->where('transfers.id', $request->id)->get()]);
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
        $this->storeTransferHistory($transfer->id, $this->stripData($transferArr));
//        event(new \App\Events\TransferCreate($transferData));
        return response(['transfer' => $this->query()->where('transfers.id', $transfer->id)->get()]);
    }

    public function getNewTransfers(Request $request)
    {
        $this->validate($request, [
            'created_at' => 'required|max:100',
            'updated_at' => 'required|date',
        ]);
        return response(['transfers' => $this->query()
            ->where('transfers.created_at', '>', \Illuminate\Support\Carbon::parse($request->created_at)->toDateTimeString())
            ->orWhere('transfers.updated_at', '>', $request->updated_at)
            ->get()]);
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TransfersExport($request->ids), 'transfers.xlsx');
    }

    public function storeTransferHistory($id, $data)
    {
        if (!array_key_exists('client_id', $data)) {
            $transfer = Transfer::find($id);
            if ($transfer) {
                $data['client_id'] = $transfer->client_id;
            }
        }
        $data['transfer_id'] = $id;
        $data['user_id'] = auth()->user()->id;
        \App\TransfersActionHistory::create($data);
    }

    public function getTransferHistory($id)
    {
        $transfersHistory = \App\TransfersActionHistory::select('transfers_action_histories.*', 'codes.code as client_name', 'users.name as user_name')
            ->join('codes', 'transfers_action_histories.client_id', '=', 'codes.id')
            ->join('users', 'transfers_action_histories.user_id', '=', 'users.id')
            ->orderBy('id')
            ->where('transfers_action_histories.transfer_id', $id)
            ->get();
//        if (!empty($transfersHistory)) {
//            $transfer = $this->query()->where('transfers.id', $id)->first();
//            return response(['transferHistory' => $transfersHistory, 'transfer' => $transfer]);
//        }
        return response(['transferHistory' => $transfersHistory]);
    }
}
