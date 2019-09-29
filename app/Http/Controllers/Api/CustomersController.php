<?php

namespace App\Http\Controllers\Api;

use App\Code;
use App\Customer;
use App\Http\Resources\CodeResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
//    public function index()
//    {
////        $codes = DB::table('codes')
////            ->join('customers', 'codes.id', '=', 'customers.code_id')
////            ->select('codes.*', 'customers.name')
////            ->get();
////        return response(['answ' => $codes->groupBy('code')]);
//        return CodeResource::collection(Code::all());
//    }

    public function checkValidCustomerData(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|unique:customers|max:13',
            'name' => 'required|max:255',
            'city' => 'required|max:255',
            'sex' => 'required|numeric|max:2',
        ]);

        return response(['status' => true]);
    }

    public function storeCustomers(Request $request)
    {
        $this->validate($request, [
            '*.phone' => 'required|unique:customers|max:13',
            '*.name' => 'required|unique:customers|max:255',
            '*.city' => 'required|max:255',
            '*.sex' => 'required|numeric|max:2',
            '*.codeId' => 'required|max:255',
        ]);

        foreach ($request->toArray() as $item) {
            Customer::create([
                'name' => $item['name'],
                'phone' => $item['phone'],
                'notify' => 1,
                'code_id' => $item['codeId'],
                'user_id' => $request->user()->id,
            ]);
        }

        return response(['status' => true]);
    }
}
