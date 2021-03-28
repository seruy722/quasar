<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use App\Category;
use App\City;
use App\Customer;
use App\Debt;
use App\Fax;
use App\PaymentArrear;
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
            'faxes.name as fax_name',
            'users.name as get_pay_user_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'cargos.category_id')
            ->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'cargos.delivery_method_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id')
            ->leftJoin('users', 'users.id', '=', 'cargos.get_pay_user_id')
            ->orderBy('id', 'DESC');
    }

    public function queryDebt()
    {
        return Debt::select(
            'debts.*',
            'codes.code as code_client_name',
            'users.name as user_name',
            'authors.name as get_pay_user_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id')
            ->leftJoin('users', 'users.id', '=', 'debts.user_id')
            ->leftJoin('users as authors', 'authors.id', '=', 'debts.get_pay_user_id')
            ->orderBy('debts.created_at', 'DESC');
    }

    public function index($id)
    {
        if (auth()->user()->hasRole('assistant')) {
            $cityId = City::where('name', 'Одесса')->first('id');
            $customersCodes = Customer::where('city_id', $cityId->id)->get('code_id');
            $access = $customersCodes->map(function ($item) {
                return $item->code_id;
            })->search($id);
            if ($access !== false) {
                $cargo = $this->query()
                    ->where('code_client_id', $id)
                    ->get();
                $debt = $this->queryDebt()
                    ->where('code_client_id', $id)
                    ->get();
                return response(['cargo' => $cargo, 'debts' => $debt]);
            }

            return response(['cargo' => [], 'debts' => []]);
        }
        if ($id == 'null') {
            $id = auth()->user()->code_id;
        }
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
        if (array_key_exists('sum', $data) && $data['sum'] > 0) {
            $data['sum'] = $data['sum'] * -1;
        }
        if (array_key_exists('category_id', $data)) {
            $category = Category::find($data['category_id']);
            if ($category) {
                $data['brand'] = true;
            }
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
        $entryId = $request->entry_id;
        if (is_array($entryId)) {
            Cargo::whereIn('id', $entryId)->update(['paid' => true]);
            array_push($entryId, $entry->id);
            $entries = $this->query()->whereIn('cargos.id', $entryId)->get();
            return response(['answer' => $entries]);
        }
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
            if (array_key_exists('created_at', $data)) {
                $newData['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
            }
            $newEntry = Debt::create($newData);
        }
        if ($debt->transfer_id > 0) {
            Transfer::where('id', $debt->transfer_id)->update(['paid' => true]);
        }
        $arrData = [$entry->id, $debt->id];
        if ($newEntry) {
            array_push($arrData, $newEntry->id);
        }
        $entries = $this->queryDebt()->whereIn('debts.id', $arrData)->get();
        return response(['answer' => $entries]);
    }

    public function cargoPaymentsAllForClient($id)
    {
        Cargo::where('type', false)->where('code_client_id', $id)->update(['paid' => true]);
        $data = $this->query()
            ->where('cargos.type', false)
            ->where('cargos.code_client_id', $id)
            ->get();
        return response(['cargo' => $data]);
    }

    public function debtsPaymentsAllForClient($id)
    {
        Debt::where('type', false)->where('code_client_id', $id)->update(['paid' => true]);
        $debts = Debt::where('type', false)->where('code_client_id', $id)->get();
        $ids = [];
        foreach ($debts as $item) {
            if ($item->transfer_id) {
                array_push($ids, $item->transfer_id);
            }
        }
        if (!empty($ids)) {
            Transfer::whereIn('id', $ids)->update(['paid' => true]);
        }

        $data = $this->queryDebt()
            ->where('debts.type', false)
            ->where('debts.code_client_id', $id)
            ->get();
        return response(['debts' => $data]);
    }

    public function getPaymentArrears()
    {
        $cargo = PaymentArrear::where('table_name', (new Cargo)->getTable())->first();
        $debts = PaymentArrear::where('table_name', (new Debt())->getTable())->first();
        $cargoIds = [];
        $debtsIds = [];
        if ($cargo) {
            $cargoIds = json_decode($cargo->entries_id);
        }
        if ($debts) {
            $debtsIds = json_decode($debts->entries_id);
        }
        return response(['cargo' => $this->query()->whereIn('cargos.id', $cargoIds)->get(), 'debts' => $this->queryDebt()->whereIn('debts.id', $debtsIds)->get()]);
    }

    public function exportCargoData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Cargo\CargoExport($request->data), 'transfers.xlsx');
    }

    public function exportDetailCargoData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Cargo\CargoGeneralDataExportByClient($request->ids), 'transfers.xlsx');
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

    public function exportGeneralDataByClientsOdessa(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OdessaUsersExport($request->model), 'transfers.xlsx');
    }

    public function exportReportOdessaData(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Cargo\ExportReportOdessaData($request->date), 'transfers.xlsx');
    }

    public function getNotDeliveredCargo()
    {
        $faxes = Fax::where('status', '>', 2)->where('uploaded_to_cargo', true)->get('id');
        $faxesIds = $faxes->map(function ($item) {
            return $item->id;
        });
        $data = $this->query()->whereIn('cargos.fax_id', $faxesIds)->where('cargos.in_cargo', false)->get();
        return response(['cargo' => $data]);
    }

    public function setDeliveredCargo(Request $request)
    {
        Cargo::whereIn('id', $request->ids)->update(['in_cargo' => $request->flag]);
        $data = $this->query()->whereIn('cargos.id', $request->ids)->get();
        return response(['cargo' => $data]);
    }
}
