<?php

namespace App\Http\Controllers\Api;

use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferController extends Controller
{
    public function index()
    {
        $transferData = Transfer::select('transfers.*', 'codes.code as client_name', 'users.name as user_name')
            ->join('codes', 'transfers.client_id', '=', 'codes.id')
            ->join('users', 'transfers.user_id', '=', 'users.id')
            ->orderBy('created_at', 'DESC')
            ->get();
        return response(['transfers' => $transferData]);
    }

    public function update(Request $request)
    {
        $data = $request->except('id');
        if ($request->issued_by) {
            $data['issued_by'] = date("Y-m-d H:i:s", strtotime($request->issued_by));
        }
        Transfer::where('id', $request->id)->update($data);
        $transferData = Transfer::select('transfers.*', 'codes.code as client_name', 'users.name as user_name')
            ->join('codes', 'transfers.client_id', '=', 'codes.id')
            ->join('users', 'transfers.user_id', '=', 'users.id')
            ->orderBy('created_at', 'DESC')
            ->where('transfers.id', $request->id)
            ->get();
        return response(['transfer' => $transferData]);
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

        $transfer = Transfer::create($transferArr);
        $transferData = Transfer::select('transfers.*', 'codes.code as client_name', 'users.name as user_name')
            ->join('codes', 'transfers.client_id', '=', 'codes.id')
            ->join('users', 'transfers.user_id', '=', 'users.id')
            ->orderBy('created_at', 'DESC')
            ->where('transfers.id', $transfer->id)
            ->get();
//        event(new \App\Events\TransferCreate($transferData));
        return response(['transfer' => $transferData]);
    }
}
