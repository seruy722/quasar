<?php

namespace App\Http\Controllers\Api;

use App\CategoriesPrice;
use App\Category;
use App\Code;
use App\CodePrice;
use App\Exports\Fax\FaxData\FaxDataExport;
use App\Fax;
use App\FaxData;
use App\Shop;
use App\StorehouseData;
use App\Thingslist;
use App\TransporterFaxesPrice;
use App\TransporterPrice;
use App\Http\Resources\FaxDataResource;
use App\Imports\ImportData;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use function foo\func;

class FaxDataController extends Controller
{
    public function storeFaxData(Request $request)
    {
        $this->validate($request, [
            'upload' => 'required|file',
            'fax_id' => 'required|numeric',
        ]);
        if ($request->hasFile('upload')) {
            $ImportedFaxArray = Excel::toArray(new ImportData, $request->file('upload'));
            foreach ($ImportedFaxArray as $item) {
                foreach ($item as $key => $elem) {
                    if ($key === 0 || $key === 1) {
                        continue;
                    }

                    $trimElem = array_map('trim', $elem);
                    if (!$trimElem[0] && !$trimElem[1] && !$trimElem[2] && !$trimElem[3] && !$trimElem[4] && !$trimElem[5] && !$trimElem[6] && !$trimElem[7] && !$trimElem[8]) {
                        break;
                    }
                    // Если первые 5 полей пустые - то дописываем к предыдущей записи товары
                    if (!$trimElem[0] && !$trimElem[1] && !$trimElem[2] && !$trimElem[3] && !$trimElem[4]) {
                        if ($trimElem[5] || $trimElem[6]) {
                            $this->createThingsListElem($trimElem[5]);
                            $lastEntry = FaxData::orderBy('id', 'desc')->first();
                            $things = json_decode($lastEntry->things, true);
                            array_push($things, ['label' => $trimElem[5], 'value' => $trimElem[6]]);
//                            $things[$trimElem[5]] = $trimElem[6];
                            $lastEntry->things = json_encode($things);
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
                        $category = Category::firstOrCreate(['name' => $trimElem[8]], ['user_id' => auth()->user()->id]);
                        // PRICE
                        $price = CodePrice::firstOrCreate(['code_id' => $code->id, 'category_id' => $category->id], ['for_kg' => 0, 'for_place' => 5, 'user_id' => auth()->user()->id]);
                        // SHOP
                        $shop = Shop::firstOrCreate(['name' => $trimElem[4]]);
                        // THINGS
                        $listThings = null;
                        if ($trimElem[5]) {
                            $listThings = json_encode([['label' => $trimElem[5], 'value' => $trimElem[6]]]);
                            $this->createThingsListElem($trimElem[5]);
                        }

                        $arr = [
                            'code' => $trimElem[0],
                            'code_id' => $code->id,
                            'fax_id' => $request->fax_id,
                            'place' => (int)$trimElem[2],
                            'kg' => (int)$trimElem[3],
                            'for_kg' => $price->for_kg,
                            'for_place' => $price->for_place,
                            'shop_id' => $shop->id,
                            'things' => $listThings,
                            'category_id' => $category->id,
                            'brand' => stripos($trimElem[8], 'Бренд') !== false,
                            'notation' => $trimElem[7],
                        ];
                        // Если у клиента по категории нет цен то берем цену с общей таблицы цен по категориям
                        if (!$price->for_kg) {
                            $categoryPrice = CategoriesPrice::where('category_id', $category->id)->first();
                            if ($categoryPrice) {
                                $price->for_kg = $categoryPrice->category_price;
                                if ($trimElem[3] <= 10) {
                                    $arr['for_place'] = 0;
                                }
                                $arr['for_kg'] = $price->for_kg;

                                $price->save();
                            }
                        }

                        FaxData::create($arr);
                    }
                }
            }
        }
        return response(['status' => true]);
    }

    public function createThingsListElem($elem)
    {
        Thingslist::firstOrCreate(['name' => $elem]);
    }

    public function groupedFaxData($id)
    {
        $faxesData = FaxDataResource::collection(FaxData::with(['customer', 'fax', 'category'])->where('fax_id', $id)->get());
        return $faxesData->groupBy('code_id')->map(function (\Illuminate\Support\Collection $code) {
            return $code->groupBy('category_id')->map(function (\Illuminate\Support\Collection $category) {
                return $category->groupBy(function ($for_kg) {
                    return (string)$for_kg->for_kg;
                });
            });
        });
    }

    public function setReplacePrice($faxData)
    {
        if ($faxData->isNotEmpty()) {
            return $faxData->map(function ($item) {
                $item->replacePrice = false;
                return $item;
            });
        }
        return $faxData;
    }

    public function getFaxDataQuery()
    {
        $queryData = StorehouseData::select(
            'storehouse_data.id',
            'storehouse_data.code_place',
            'storehouse_data.code_client_id',
            'storehouse_data.place',
            'storehouse_data.kg',
            'storehouse_data.for_kg',
            'storehouse_data.for_place',
            'storehouse_data.fax_id',
            'storehouse_data.shop',
            'storehouse_data.things',
            'storehouse_data.notation',
            'storehouse_data.category_id',
            'storehouse_data.brand',
            'storehouse_data.created_at',
            'storehouse_data.updated_at',
            'codes.code as code_client_name',
            'categories.name as category_name'
        );
        if (!auth()->user()->hasPermissionTo('view fax price')) {
            $queryData = StorehouseData::select(
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
                'storehouse_data.updated_at',
                'codes.code as code_client_name',
                'categories.name as category_name'
            );
        }
        return $queryData->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.destroyed', false)
            ->orderBy('storehouse_data.id', 'desc');
    }

    public function getFaxData($id)
    {
//        $fax = Fax::with('transporter')->find($id);
//        $faxCategories = FaxData::select('categories.name')
//            ->selectRaw('SUM(fax_data.place) as place')
//            ->selectRaw('SUM(fax_data.kg) as kg')
//            ->selectRaw('categories.id as category_id')
//            ->leftJoin('categories', 'categories.id', '=', 'fax_data.category_id')
//            ->where('fax_data.fax_id', $id)
//            ->orderBy('place', 'DESC')
//            ->groupBy('category_id')
//            ->get();

//        $price = TransporterFaxesPrice::where('fax_id', $id)->get();
//
//        $faxCategoriesWithForKg = $faxCategories->map(function ($category) use (&$price) {
//            $priceData = $price->firstWhere('category_id', $category->category_id);
//            if ($priceData) {
//                $category->for_kg = $priceData->category_price;
//            } else {
//                $category->for_kg = 0;
//            }
//            return $category;
//        });

        $queryData = $this->getFaxDataQuery();
        $faxData = $queryData->where('storehouse_data.fax_id', $id)->get();

        return response(['faxData' => $this->setReplacePrice($faxData)]);
    }

    public function updateFaxData(Request $request)
    {
        $this->validate($request, [
            '*.code' => 'max:255',
            '*.code_id' => 'required|numeric',
            '*.fax_id' => 'required|numeric',
            '*.place' => 'required|numeric',
            '*.kg' => 'required|numeric',
            '*.for_kg' => 'required|numeric',
            '*.for_place' => 'required|numeric',
            '*.shop' => 'max:255',
            '*.things' => 'max:1500',
            '*.category_id' => 'required|numeric',
            '*.brand' => 'required|numeric',
            '*.notation' => 'max:255',
        ]);

        $data = $request->all();
        $faxID = $data[0]['fax_id'];

        foreach ($data as $item) {
            $price = CodePrice::where('code_id', $item['code_id'])->where('category_id', $item['category_id'])->first();
            if ($price) {
                if (($item['for_kg'] && !$price->for_kg) || $item['change']) {
                    $price->for_kg = $item['for_kg'];
                }

                if (($item['for_place'] && !$price->for_place) || $item['change']) {
                    $price->for_place = (float)$item['for_place'];
                }

                $price->save();
            }

//            $client = User::firstOrCreate(['name' => $arrvalue['name'], 'id' => $arrvalue['client_id']], ['password' => 'default']);
//            $category = Category::firstOrCreate(['id' => $item['category_id'], ]);
//            $clientID = $client->id;
            FaxData::where('id', $item['id'])->update([
                'code' => $item['code'],
                'code_id' => $item['code_id'],
                'fax_id' => $item['fax_id'],
                'place' => $item['place'],
                'kg' => $item['kg'],
                'for_kg' => $item['for_kg'],
                'for_place' => $item['for_place'],
                'shop' => $item['shop'],
                'things' => $item['things'],
                'category_id' => $item['category_id'],
                'brand' => $item['brand'],
                'notation' => $item['notation'],
            ]);
        }
//        FaxData::where('id', $data['id']);
        return response(['faxData' => $this->setReplacePrice($this->groupedFaxData($faxID))]);
    }

//    public function export(Request $request)
//    {
////        return response(['answ' => new FaxDataExport($request->faxID, $request->transporterID)]);
//        return Excel::download(new FaxDataExport($request->faxID, $request->transporterID), 'users.xlsx');
//    }

    public function updatePricesInFax($id)
    {
//        $fax = Fax::find($id);
        $faxData = StorehouseData::where('fax_id', $id)->get();
        $faxData->each(function ($item) {
            $price = CodePrice::where('code_id', $item->code_client_id)->where('category_id', $item->category_id)->first();
            if ($price) {
                $item->for_kg = $price->for_kg;
                $item->for_place = $price->for_place;
                $item->save();
            }
        });
        $resultData = $this->setReplacePrice($this->getFaxDataQuery()->where('fax_id', $id)->get());
        return response(['faxData' => $resultData]);
    }

    public function exportModer(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Fax\FaxExportForModers\FaxSheetsExport($request->id), 'storehouseData.xlsx');
    }

    public function exportAdmin(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Fax\FaxExportForAdmin\FaxSheetsExport($request->id), 'storehouseData.xlsx');
    }
}
