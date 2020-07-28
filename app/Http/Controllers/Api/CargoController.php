<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use App\Debt;
use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CargoController extends Controller
{
    public function getGeneralCargoData()
    {
        $cargo = Cargo::all();
        $debts = Debt::all();
        return response(['cargo' => ['sum' => $cargo->sum('sum'), 'kg' => $cargo->sum('kg'), 'place' => $cargo->sum('place'), 'sale' => $cargo->sum('sale')], 'debts' => ['sum' => $debts->sum('sum'), 'commission' => $debts->sum('commission')]]);
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
            ->orderBy('debts.created_at', 'DESC');
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
        $data['place'] = 0;
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

    public function createDebtPaymentEntry(Request $request)
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
        $entry = Debt::create($data);
        $entry = $this->queryDebt()->where('debts.id', $entry->id)->first();
        return response(['answer' => $entry]);
    }

    public function updateDebtPaymentEntry(Request $request)
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
        }
        if (array_key_exists('sum', $data) && $data['sum'] && $data['sum'] < 0) {
            $data['sum'] = $data['sum'] * -1;
        }
        if (array_key_exists('commission', $data) && $data['commission'] && $data['commission'] < 0) {
            $data['commission'] = $data['commission'] * -1;
        }
        Debt::where('id', $request->id)->update($data);
        $entry = $this->queryDebt()->where('debts.id', $request->id)->first();
        return response(['answer' => $entry]);
    }

    public function deleteDebtEntry(Request $request)
    {
        Debt::destroy($request->ids);
        return response()->json(null, 201);
    }

    public function createDebtEntry(Request $request)
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
        if (array_key_exists('sum', $data) && $data['sum'] > 0) {
            $data['sum'] = $data['sum'] * -1;
        }
        if (array_key_exists('commission', $data) && $data['commission'] > 0) {
            $data['commission'] = $data['commission'] * -1;
        } else {
            $data['commission'] = ($data['sum'] / 100) * 1;
        }

        $entry = Debt::create($data);
        $entry = $this->queryDebt()->where('debts.id', $entry->id)->first();
        return response(['answer' => $entry]);
    }

    public function updateDebtEntry(Request $request)
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
        if (array_key_exists('sum', $data) && $data['sum'] && $data['sum'] > 0) {
            $data['sum'] = $data['sum'] * -1;
        }
        if (array_key_exists('commission', $data) && $data['commission'] && $data['commission'] > 0) {
            $data['commission'] = $data['commission'] * -1;
        }
        Debt::where('id', $request->id)->update($data);
        $debt = Debt::find($request->id);
        if (array_key_exists('paid', $data)) {
            Transfer::where('id', $debt->transfer_id)->update(['paid' => $data['paid']]);
        }
        $entry = $this->queryDebt()->where('debts.id', $request->id)->first();
        return response(['answer' => $entry, '$debt' => $debt]);
    }

    public function cargoPayEntry(Request $request)
    {
        $data = $request->except('entry_id');
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
        $data['place'] = 0;
        $entry = Cargo::create($data);
        Cargo::where('id', $request->entry_id)->update(['paid' => true]);
        $entries = $this->query()->whereIn('cargos.id', [$entry->id, $request->entry_id])->get();
        return response(['answer' => $entries]);
    }

    public function debtPayEntry(Request $request)
    {
        $data = $request->except('entry_id');
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
        $entry = Debt::create($data);
        $debt = Debt::find($request->entry_id);
        $debt->paid = true;
        $debt->save();
        $newEntry = 0;
        if ($debt->commission < $data['commission'] * -1) {
            $newData = [];
            $newData['code_client_id'] = $data['code_client_id'];
            $newData['type'] = $data['type'];
            $newData['notation'] = 'списалы';
            $newData['commission'] = ($debt->commission + $data['commission']) * -1;
            $newEntry = Debt::create($newData);
        }
        Transfer::where('id', $debt->transfer_id)->update(['paid' => true]);
        $entries = $this->queryDebt()->whereIn('debts.id', [$entry->id, $debt->id, $newEntry->id])->get();
        return response(['answer' => $entries]);
    }

    public function exportCargoData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Cargo\CargoExport($request->data), 'transfers.xlsx');
    }

    public function exportDebtsData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Debts\DebtsExport($request->data), 'transfers.xlsx');
    }

    public function exportGeneralCargoData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Cargo\CargoGeneralDataExport($request->data), 'transfers.xlsx');
    }

    public function exportGeneralDebtsData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Debts\DebtsGeneralDataExport($request->data), 'transfers.xlsx');
    }

    public function exportGeneralDataByClients(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GeneralDataByClientsExport($request->model), 'transfers.xlsx');
    }

}