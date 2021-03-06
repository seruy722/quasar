<?php

namespace App\Http\Controllers\Api;

use App\Code;
use App\Customer;
use App\Http\Resources\CodeResource;
use Cassandra\Custom;
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

    protected $rules = [
        'phone' => 'required|max:20',
        'name' => 'required|max:255',
        'city_id' => 'nullable|max:255',
        'sex' => 'required|numeric|max:2',
        'code_id' => 'required|numeric|max:20000',
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
            'phone' => 'required|max:20',
            'name' => 'required|max:255',
            'city_id' => 'nullable|max:255',
            'sex' => 'required|numeric|max:2',
            'code_id' => 'required|numeric|max:20000',
        ]);

        $data = $this->stripData($request->all());

        $customer = Customer::create($data);

        $this->storeCustomerHistory($customer->id, $data, 'create');

        return response(['status' => true, 'customer' => $customer]);
    }

    public function update(Request $request)
    {
        $data = $this->stripData($request->except('id'));

        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        Customer::where('id', $request->id)->update($data);
        $this->storeCustomerHistory($request->id, $data, 'update');

        return response(['customer' => Customer::find($request->id)]);
    }

    public function destroy($id)
    {
        Customer::where('id', $id)->delete();
        $this->storeCustomerHistory($id, [], 'destroy');
        return response(['status' => true]);
    }

    public function storeCustomerHistory($id, $data, $action)
    {
        if (array_key_exists('code_id', $data)) {
            $code = \App\Code::find($data['code_id']);
            if ($code) {
                $data['client_name'] = $code->code;
            }

        }

        if (array_key_exists('city_id', $data)) {
            $city = \App\City::find($data['city_id']);
            if ($city) {
                $data['city_name'] = $city->name;
            }

        }
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => (new Customer)->getTable(), 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }
}
