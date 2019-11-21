<?php

namespace App\Http\Controllers;

use App\cargoTable;
use App\Category;
use App\Code;
use App\debtsTable;
use App\Fax;
use App\FaxData;
use App\Http\Resources\FaxDataResource;
use App\Http\Resources\FaxResource;
use App\Http\Resources\SkladResource;
use App\Imports\ImportData;
use App\Sklad;
use App\Test;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use mysql_xdevapi\Collection;

class CommonController extends Controller
{
    public function storeCargoTable(Request $request)
    {
        if ($request->hasFile('upload')) {
            DB::statement("SET foreign_key_checks=0");
            cargoTable::truncate();
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
                    cargoTable::create([
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

    public function getData($codeID)
    {
        $cargo = cargoTable::where('code_id', $codeID)->get();
        $debts = debtsTable::where('code_id', $codeID)->get();
        $sklad = SkladResource::collection(Sklad::with(['customer', 'category'])->get());
//        $faxesData = FaxDataResource::collection(FaxData::where('code_id', $codeID)->orderBy('created_at', 'DESC')->get());
        $faxesData = FaxDataResource::collection(FaxData::with(['customer', 'category', 'fax'])->where('code_id', $codeID)->orderBy('id', 'DESC')->get());
        $faxesData = $faxesData->groupBy('category_id')->map(function (\Illuminate\Support\Collection $category) {
            return $category->groupBy(function ($kg) {
                return (string)$kg->for_kg;
            });
        });

        return response(['cargo' => $cargo, 'debts' => $debts, 'storehouse' => $sklad, 'faxes' => $faxesData, 'faxesCount' => FaxData::where('code_id', $codeID)->count(), 'sumCargo' => $cargo->sum('sum'), 'sumDebts' => $debts->sum('sum')]);
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
