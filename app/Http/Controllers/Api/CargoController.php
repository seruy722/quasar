<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CargoController extends Controller
{
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

    public function index($id)
    {
        $cargo = $this->query()
            ->where('code_client_id', $id)
            ->get();
        return response(['answer' => $cargo]);
    }

    public function updateCargoPaymentEntry(Request $request)
    {
        $data = $request->except('id');
        if (array_key_exists('created_at', $data)) {
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        }
        Cargo::where('id', $request->id)->update($data);
        $entry = $this->query()->where('cargos.id', $request->id)->first();
        return response(['answer' => $entry]);
    }

    public function deleteCargoEntry(Request $request)
    {
        Cargo::destroy($request->ids);
        return response()->json(null, 201);
    }
}
