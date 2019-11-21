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
            'phone' => 'required|unique:customers|max:20',
            'name' => 'required|unique:customers|max:255',
//            'city' => 'required|max:255',
            'sex' => 'required|numeric|max:2',
            'codeId' => 'required|numeric|max:20000',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'code_id' => $request->codeId,
            'user_id' => auth()->user()->id,
        ]);

        return response(['status' => true, 'customer' => $customer]);
    }
}
