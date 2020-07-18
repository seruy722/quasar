<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use App\Debt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CargoController extends Controller
{
    public function getGeneralCargoData()
    {
        $data = Cargo::all();
        return response(['sum' => $data->sum('sum'), 'kg' => $data->sum('kg'), 'place' => $data->sum('place'), 'sale' => $data->sum('sale')]);
    }

    protected $rules = [
        'created_at' => 'required|string|max:100',
        'code_client_id' => 'required|numeric',
        'sum' => 'required|numeric',
        'sale' => 'required|numeric',
        'notation' => 'nullable|string|max:255',
    ];

    public function query()
    {
        return Cargo::select(
            'cargos.*',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'delivery_methods.name as delivery_method_name',
            'faxes.name as fax_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'cargos.category_id')
            ->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'cargos.delivery_method_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id')
            ->orderBy('id', 'DESC');
    }

    public function queryDebt()
    {
        return Debt::select(
            'debts.*',
            'codes.code as code_client_name',
            'users.name as user_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id')
            ->leftJoin('users', 'users.id', '=', 'debts.user_id')
            ->orderBy('id', 'DESC');
    }

    public function index($id)
    {
        $cargo = $this->query()
            ->where('code_client_id', $id)
            ->get();
        $debt = $this->queryDebt()
            ->where('code_client_id', $id)
            ->get();
        return response(['cargo' => $cargo, 'debts' => $debt]);
    }

    public function createCargoPaymentEntry(Request $request)
    {
        $data = $request->all();
        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        if (array_key_exists('created_at', $data)) {
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        }
        $data['type'] = true;
        $entry = Cargo::create($data);
        $entry = $this->query()->where('cargos.id', $entry->id)->first();
        return response(['answer' => $entry]);
    }

    public function updateCargoPaymentEntry(Request $request)
    {
        $data = $request->except('id');
        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        if (array_key_exists('created_at', $data)) {
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        } else {
            $data['created_at'] = date("Y-m-d H:i:s");
        }
        Cargo::where('id', $request->id)->update($data);
        $entry = $this->query()->where('cargos.id', $request->id)->first();
        return response(['answer' => $entry]);
    }

    public function createCargoDebtEntry(Request $request)
    {
        $data = $request->all();
        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        if (array_key_exists('created_at', $data)) {
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        }
        $entry = Cargo::create($data);
        if (array_key_exists('for_kg', $data) || array_key_exists('for_place', $data) || array_key_exists('kg', $data) || array_key_exists('place', $data)) {
            if ($entry) {
                $entry->sum = round($entry->for_kg * $entry->kg + $entry->for_place * $entry->place) * -1;
                $entry->save();
            }
        }
        $entry = $this->query()->where('cargos.id', $entry->id)->first();
        return response(['answer' => $entry]);
    }

    public function updateCargoDebtEntry(Request $request)
    {
        $data = $request->except('id');
        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        if (array_key_exists('created_at', $data)) {
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        } else {
            $data['created_at'] = date("Y-m-d H:i:s");
        }
        if (array_key_exists('sum', $data) && $data['sum'] > 0) {
            $data['sum'] = $data['sum'] * -1;
        }

        Cargo::where('id', $request->id)->update($data);
        if (array_key_exists('for_kg', $data) || array_key_exists('for_place', $data) || array_key_exists('kg', $data) || array_key_exists('place', $data)) {
            $entry = Cargo::find($request->id);
            if ($entry) {
                $entry->sum = round($entry->for_kg * $entry->kg + $entry->for_place * $entry->place);
                $entry->save();
            }
        }
        $entry = $this->query()->where('cargos.id', $request->id)->first();
        return response(['answer' => $entry]);
    }

    public function deleteCargoEntry(Request $request)
    {
        Cargo::destroy($request->ids);
        return response()->json(null, 201);
    }
}
