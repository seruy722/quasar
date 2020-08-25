<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use App\City;
use App\Code;
use App\Customer;
use App\Http\Resources\CodeResource;
use App\Imports\ImportData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use function foo\func;

class CodesController extends Controller
{
    public function uploadCodesData(Request $request)
    {
        if ($request->hasFile('upload')) {
            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));
            Customer::truncate();
            foreach ($ImportedFaxArray as $item) {
//                return response(['answ' => $item[0]]);
//                Customer::create([
//                    'name' => $item[0],
//                    'phone' => $item[1],
//                    'code_id' => rand(1, 50),
//                ]);
//                return response(['answ' => $item]);
//                foreach ($item as $elem) {
////                    $trimElem = trim(implode($elem));
////                    return response(['answ' => $elem[30]]);
//                    $arr = ['code_id' => rand(1, 50), 'city_id' => 1];
//                    if (trim($elem[0])) {
//                        $arr['name'] = trim($elem[0]);
//                    } else {
//                        $arr['name'] = 'trim($elem[0])';
//                    }
//                    if (trim($elem[30])) {
//                        $str = preg_replace("/[^0-9]/", '', $elem[1]);
//                        $arr['phone'] = $str;
//                    } else {
//                        $arr['phone'] = 'trim($elem[1])';
//                    }
//
//                    Customer::create($arr);
//                }
            }
        }
//        $baza = DB::table('bazas')->whereNotNull('code')->get();
//        return response(['count' => $baza->count()]);
//        $unique = $baza->unique('phone');
//        $unique->each(function ($item) {
//            $code = Code::where('code', $item->code)->first();
//            Customer::create([
//                'name'=> $item->name,
//                'phone'=> $item->phone,
//                'notify'=> 1,
//                'code_id'=> $code ? $code->id : 0,
//                'user_id'=> 1,
//            ]);
//        });
    }

    public function checkCodeExist($code)
    {
        if (!$code) {
            throw ValidationException::withMessages([
                'code' => [trans('validation.required')],
            ]);
        } else {
            $code = Code::where('code', $code)->first();
            if ($code) {
                throw ValidationException::withMessages([
                    'code' => [trans('validation.codeExist')],
                ]);
            }
        }

        return response(['status' => true]);
    }

    public function codeWithCustomersQuery()
    {
        return Code::with('customers');
    }

    public function sortCodesWithCustomers($data)
    {
        return $data->map(function ($item) {
            $item['phones'] = $item->customers->map(function ($elem) {
                return $elem['phone'];
            })->unique()->values()->all();
            $item['cities'] = $item->customers->map(function ($elem) {
                return $elem['city_name'];
            })->unique();
            return $item;
        })->sort(function ($a, $b) {
            if ($a['code'] == $b['code']) {
                return 0;
            }

            return ($a['code'] < $b['code']) ? -1 : 1;
        })->values()->all();
    }

    public function storeCode(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|unique:codes|max:100',
        ]);

        $code = Code::create([
            'code' => $request->code,
        ]);

        $this->storeCodesHistory($code->id, $request->all(), 'create');
        $codeWithCustomers = $this->codeWithCustomersQuery()->where('id', $code->id)->get();

        return response(['code' => ['label' => $code->code, 'value' => $code->id], 'codeWithCustomers' => $this->sortCodesWithCustomers($codeWithCustomers)]);

    }

    public function index()
    {
        return response(['codeList' => Code::select('id as value', 'code as label')->get()]);
    }

    public function getCodesWithCustomers()
    {
        $codes = $this->codeWithCustomersQuery()->get();
        return response(['codesData' => $this->sortCodesWithCustomers($codes)]);
    }

    public function storeCodesHistory($id, $data, $action)
    {
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => (new Code)->getTable(), 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }

//    public function getNewCodes(Request $request)
//    {
//        $this->validate($request, [
//            'maxCodeEntryId' => 'required|numeric',
//            'updatedAtCode' => 'required|date',
//        ]);
//        $clients = Customer::where('id', '>', $request->maxClientEntryId)
//            ->orWhere('updated_at', '>', $request->updatedAtClient)
//            ->get();
//        return response(['codesData' => Code::with('customers')
//            ->where('id', '>', $request->maxCodeEntryId)
//            ->orWhere('updated_at', '>', $request->updatedAtCode)
//            ->get(), 'clients' => $clients]);
//    }
    public function getCodeHistory($id)
    {
        $codeHistory = \App\History::where('table', (new Code)->getTable())->where('entry_id', $id)->get();

        return response(['codeHistory' => $codeHistory]);
    }

    public function codesAssistantList()
    {
        $cityId = City::where('name', 'Одесса')->first('id');
        $customersCodes = Customer::where('city_id', $cityId->id)->get('code_id');
        $access = $customersCodes->map(function ($item) {
            return $item->code_id;
        });
        return response(['codes' => Code::select('id as value', 'code as label')->whereIn('id', $access->all())->get()]);
    }

    public function getCustomersWhoGetTheBrand()
    {
        $entries = Cargo::where('type', false)->where('brand', true)->get('code_client_id');
        $res = $entries->map(function ($entry) {
            return $entry->code_client_id;
        })->unique()->values()->all();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomerExport($res), 'customers.xlsx');
    }

    public function exportCustomersWhoLeft()
    {
        $entries = Code::all('id');
        $res = $entries->map(function ($entry) {
            return $entry->id;
        })->unique()->values()->all();
        $ids = [];
        foreach ($res as $id) {
            $cargoEntry = Cargo::where('type', false)->where('code_client_id', $id)->orderBy('created_at', 'desc')->first();
            if ($cargoEntry) {
                $dt = Carbon::parse($cargoEntry->created_at);
                if ($dt->diffInDays(Carbon::now()) >= 31) {
                    array_push($ids, $id);
                }
            } else {
                array_push($ids, $id);
            }
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomerExport($ids), 'customers.xlsx');
    }
}
