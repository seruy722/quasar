<?php

namespace App\Http\Controllers;

use App\Cargo;
use App\Category;
use App\Code;
use App\Debt;
use App\debtsTable;
use App\History;
use App\Imports\ImportData;
use App\Sklad;
use App\StorehouseData;
use App\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CommonController extends Controller
{
    public function storeCargoTable(Request $request)
    {
        // CARGO
//        if ($request->hasFile('upload')) {
//            DB::statement("SET foreign_key_checks=0");
//            Cargo::truncate();
//            DB::statement("SET foreign_key_checks=1");
//            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));
//            foreach ($ImportedFaxArray as $item) {
//                foreach ($item as $elem) {
//                    $trimElem = array_map('trim', $elem);
//                    $code = Code::firstOrCreate(['code' => $trimElem[3]]);
//                    Cargo::create([
//                        'code_client_id' => $code->id,
//                        'type' => $trimElem[1] === 'Доход',
//                        'sum' => (int)$trimElem[2],
//                        'notation' => $trimElem[4],
//                        'created_at' => date("Y-m-d H:i:s", strtotime($trimElem[0])),
//                    ]);
//
//                }
//            }
//        }
        // DEBTS
//        if ($request->hasFile('upload')) {
//            DB::statement("SET foreign_key_checks=0");
//            Debt::truncate();
//            DB::statement("SET foreign_key_checks=1");
//            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));
//
//            foreach ($ImportedFaxArray as $item) {
//                foreach ($item as $elem) {
//                    $trimElem = array_map('trim', $elem);
//                    $code = Code::firstOrCreate(['code' => $trimElem[3]]);
//                    Debt::create([
//                        'type' => $trimElem[1] === 'Доход',
//                        'code_client_id' => $code->id,
//                        'sum' => (int)$trimElem[2],
//                        'notation' => $trimElem[4],
//                        'created_at' => date("Y-m-d H:i:s", strtotime($trimElem[0])),
//                    ]);
//                }
//            }
//        }
        if ($request->hasFile('upload')) {
            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));

            foreach ($ImportedFaxArray as $item) {
                foreach ($item as $elem) {
                    $trimElem = array_map('trim', $elem);
                    $code = Code::firstOrCreate(['code' => $trimElem[0]]);

                    $entry = Debt::where('code_client_id', $code->id)->where('type', false)->first();
                    $entry->commission = $trimElem[1] * -1;
                    $entry->save();
                }
            }
        }
        return response()->json(null, 201);
    }

    public function storeDebtsTable(Request $request)
    {
        if ($request->hasFile('upload')) {
            DB::statement("SET foreign_key_checks=0");
            debtsTable::truncate();
            DB::statement("SET foreign_key_checks=1");
            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));

            foreach ($ImportedFaxArray as $item) {
                foreach ($item as $elem) {
                    $trimElem = array_map('trim', $elem);
                    $code = Code::where('code', $trimElem[3])->first();
                    if (!$code) {
                        $code = Code::create([
                            'code' => $trimElem[3],
                            'user_id' => $request->user()->id,
                        ]);
                    }
                    debtsTable::create([
                        'type' => $trimElem[1] === 'Доход' ? 'Оплата' : 'Долг',
                        'code_id' => $code->id,
                        'sum' => (int)$trimElem[2],
                        'notation' => $trimElem[4],
                        'created_at' => date("Y-m-d H:i:s", strtotime($trimElem[0])),
                    ]);
                }
            }
        }
        return response(['status' => true]);
    }

    public function storeSkladTable(Request $request)
    {
        if ($request->hasFile('upload')) {
            DB::statement("SET foreign_key_checks=0");
            Sklad::truncate();
            DB::statement("SET foreign_key_checks=1");
            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));

            foreach ($ImportedFaxArray as $item) {
                foreach ($item as $elem) {
                    $trimElem = array_map('trim', $elem);
                    if (!$trimElem[0] && !$trimElem[1] && !$trimElem[2] && !$trimElem[3] && !$trimElem[4] && !$trimElem[5] && !$trimElem[6] && !$trimElem[7] && !$trimElem[8]) {
                        break;
                    }
                    // Если первые 5 полей пустые - то дописываем к предыдущей записи товары
                    if (!$trimElem[0] && !$trimElem[1] && !$trimElem[2] && !$trimElem[3] && !$trimElem[4]) {
                        if ($trimElem[5]) {
                            $lastEntry = Sklad::orderBy('id', 'desc')->first();
                            $things = $lastEntry->things;
                            $lastEntry->things = $things . ',' . $trimElem[5] . ':' . $trimElem[6];
                            $lastEntry->save();
                        }
                    } else {
                        // CODE
                        $startPos = stripos($trimElem[1], '007/');
                        $codeName = substr($trimElem[1], $startPos + 4);
                        if (!is_numeric($startPos)) {
                            $codeName = $trimElem[1];
                        }
                        $code = Code::firstOrCreate(['code' => $codeName], ['user_id' => auth()->user()->id]);
                        // CATEGORY
                        $category = Category::firstOrCreate(['name' => $trimElem[8]]);
                        // THINGS
                        $listThings = null;

                        if ($trimElem[5]) {
                            $listThings = $trimElem[5] . ':' . $trimElem[6];
                        }
                        Sklad::create([
                            'code' => $trimElem[0],
                            'code_id' => $code->id,
                            'place' => (int)$trimElem[2],
                            'kg' => (int)$trimElem[3],
                            'shop' => $trimElem[4],
                            'things' => $listThings,
                            'category_id' => $category->id,
                            'notation' => $trimElem[7],
                        ]);
                    }
                }
            }
        }
        return response(['status' => true]);
    }

    public function getData(Request $request)
    {
        $storehouseData = StorehouseData::select(
            'storehouse_data.id',
            'storehouse_data.code_place',
            'storehouse_data.code_client_id',
            'storehouse_data.place',
            'storehouse_data.kg',
            'storehouse_data.fax_id',
            'storehouse_data.shop',
            'storehouse_data.things',
            'storehouse_data.notation',
            'storehouse_data.category_id',
            'storehouse_data.brand',
            'storehouse_data.created_at',
            'codes.code as code_client_name',
            'categories.name as category_name'
        )
            ->where('fax_id', 0)
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id');
        $faxesData = StorehouseData::select(
            'storehouse_data.id',
            'storehouse_data.code_place',
            'storehouse_data.code_client_id',
            'storehouse_data.place',
            'storehouse_data.kg',
            'storehouse_data.fax_id',
            'storehouse_data.shop',
            'storehouse_data.things',
            'storehouse_data.notation',
            'storehouse_data.category_id',
            'storehouse_data.brand',
            'storehouse_data.created_at',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'faxes.name as fax_name',
            'faxes.status as fax_status'
        )
            ->where('fax_id', '>', 0)
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->join('faxes', 'faxes.id', '=', 'storehouse_data.fax_id');
        $destroyedEntries = [];
        if ($request->codeID) {
            $storehouseData = $storehouseData->where('code_client_id', $request->codeID)->orderBy('id', 'desc')->get();
            $faxesData = $faxesData->where('code_client_id', $request->codeID)->orderBy('id', 'desc')->get();
            $destroyedEntries = History::where('action', 'destroy')->where('history_data', 'like', '%' . '"code_client_id": ' . $request->codeID . '%')->orderBy('id', 'desc')->get();
        } else if ($request->codePlace) {
            $storehouseData = $storehouseData->where('code_place', $request->codePlace)->orderBy('id', 'desc')->get();
            $faxesData = $faxesData->where('code_place', $request->codePlace)->orderBy('id', 'desc')->get();
            $destroyedEntries = History::where('action', 'destroy')->where('history_data', 'like', '%' . '"code_place": ' . $request->codePlace . '%')->orderBy('id', 'desc')->get();
        }

        return response(['destroyed' => $destroyedEntries, 'storehouse' => $storehouseData, 'faxes' => $faxesData]);
    }

    public function uploadFaxes(Request $request)
    {
        foreach ($request->files as $file) {
            $ImportedFaxArray = Excel::toArray(new ImportData, $file);

            foreach ($ImportedFaxArray[0] as $key => $elem) {
                $trimElem = array_map('trim', $elem);

                if (count($elem) === 8) {
                    if ($key === 0) {
                        continue;
                    }

                    if (!$trimElem[0]) {
                        break;
                    }

                    $arr2 = [
                        'code' => $trimElem[0],
                        'client' => $trimElem[1],
                        'place' => 1,
//                        'kg' => $trimElem[3],
                        'shop' => $trimElem[4],
                        'category' => $trimElem[5],
                        'things' => $trimElem[7],
                        'brand' => $trimElem[5] === 'Бренд',
                        'fax_name' => $file->getClientOriginalName(),
                    ];
                    if ($trimElem[3]) {
                        $arr2['kg'] = $trimElem[3];
                    }

                    Test::create($arr2);
                } else {
                    // Загрузка данных файла на сервер_2
                    // Пропускаем первые 2 строки
                    if ($key === 0 || $key === 1) {
                        continue;
                    }
                    // Если все поля пустые выходим из итерации
                    if (!$trimElem[0] && !$trimElem[1] && !$trimElem[2] && !$trimElem[3] && !$trimElem[4] && !$trimElem[5]) {
                        break;
                    }
                    // Если первые 5 полей пустые - то дописываем к предыдущей записи товары
                    if (!$trimElem[0] && !$trimElem[1] && !$trimElem[2] && !$trimElem[3] && !$trimElem[4]) {
                        $lastEntry = Test::orderBy('id', 'desc')->first();
                        $things = $lastEntry->things;
//                    $things->{$trimElem[5]} = $trimElem[6];
                        $lastEntry->things = $things . ',' . $trimElem[5] . ':' . $trimElem[6];
                        $lastEntry->save();
                    } else {
                        // Обрезаем из имени клиента приставку 007/
                        $startPos = stripos($trimElem[1], '007/');
                        $userName = substr($trimElem[1], $startPos + 4);
                        if (!is_numeric($startPos)) {
                            $userName = $trimElem[1];
                        }
                        $arr = [
                            'code' => $trimElem[0],
                            'client' => $userName,
                            'place' => 1,
//                        'kg' => $trimElem[3],
                            'shop' => $trimElem[4],
//                        'category' => $trimElem[5],
                            'things' => $trimElem[5],
//                        'brand' => $trimElem[5] === 'Бренд',
                            'fax_name' => $file->getClientOriginalName(),
                        ];

                        if ($trimElem[3]) {
                            $arr['kg'] = $trimElem[3];
                        }

                        Test::create($arr);
                    }
                }
            }
        }


        return response(['files' => $request->files]);
    }

    public function searchInFaxes(Request $request)
    {
        $s = $request->search;
        return response(['searchData' => Test::where('client', $s)->get()]);
    }
}
