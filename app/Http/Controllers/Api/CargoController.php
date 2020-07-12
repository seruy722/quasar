<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CargoController extends Controller
{
    public function index($id)
    {
        $cargo = Cargo::select(
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
            ->where('code_client_id', $id)
            ->orderBy('id', 'DESC')
            ->get();
        return response(['answer' => $cargo]);
    }
}
