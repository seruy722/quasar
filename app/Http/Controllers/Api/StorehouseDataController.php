<?php

namespace App\Http\Controllers\Api;

use App\Cargo;
use App\Category;
use App\CodePlace;
use App\CodesPrices;
use App\Fax;
use App\FaxData;
use App\Shop;
use App\StorehouseData;
use App\StorehouseHistory;
use App\Thingslist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\PushNotifications;
use Illuminate\Validation\ValidationException;
use function foo\func;

class StorehouseDataController extends Controller
{
    use PushNotifications;

    protected $rules = [
        'code_place' => 'required|max:12',
        'code_client_id' => 'required|numeric',
        'kg' => 'required|numeric',
        'for_kg' => 'numeric',
        'for_place' => 'numeric',
        'shop' => 'nullable|string|max:255',
        'things' => 'nullable|json|max:1000',
        'notation' => 'nullable|string|max:255',
        'category_id' => 'required|numeric',
        'destroyed' => 'required|boolean',
        'storehouse_id' => 'numeric',
        'department' => 'nullable|string|max:150',
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

    public function getStorehouseData($id)
    {
        $arrData = $this->storehouseDataList($id);
        return response()->json($arrData->where('storehouse_data.fax_id', 0)->get());
    }

    public function storehouseDataList($id)
    {
        $queryData = StorehouseData::select(
            'storehouse_data.id',
            'storehouse_data.code_place',
            'storehouse_data.code_client_id',
            'storehouse_data.place',
            'storehouse_data.kg',
            'storehouse_data.cube',
            'storehouse_data.for_kg',
            'storehouse_data.for_place',
            'storehouse_data.fax_id',
            'storehouse_data.shop',
            'storehouse_data.things',
            'storehouse_data.notation',
            'storehouse_data.category_id',
            'storehouse_data.brand',
            'storehouse_data.delivery_method_id',
            'storehouse_data.department',
            'storehouse_data.created_at',
            'storehouse_data.updated_at',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'delivery_methods.name as delivery_method_name',
            'faxes.name as fax_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'storehouse_data.fax_id')
            ->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'storehouse_data.delivery_method_id')
            ->where('storehouse_data.storehouse_id', $id)
            ->where('storehouse_data.destroyed', false)
            ->orderBy('storehouse_data.id', 'desc');


//        $faxesData = $queryData->groupBy('code_client_id')->map(function (\Illuminate\Support\Collection $code) {
//            return $code->groupBy('category_id')->map(function (\Illuminate\Support\Collection $category) {
//                return $category->groupBy(function ($for_kg) {
//                    return (string)$for_kg->for_kg;
//                });
//            });
//        });

//        $categories = StorehouseData::select('categories.name')
//            ->selectRaw('SUM(storehouse_data.place) as place')
//            ->selectRaw('SUM(storehouse_data.kg) as kg')
//            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
//            ->where('storehouse_data.storehouse_id', $id)
//            ->where('storehouse_data.fax_id', 0)
//            ->where('storehouse_data.destroyed', false)
//            ->orderBy('place', 'DESC')
//            ->groupBy('category_id')
//            ->get();

        return $queryData;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code_place' => 'required|max:12',
            'code_client_id' => 'required|numeric',
            'kg' => 'required|numeric',
            'for_kg' => 'numeric',
            'for_place' => 'numeric',
            'shop' => 'nullable|max:100',
            'things' => 'nullable|json|max:1000',
            'notation' => 'nullable|max:255',
            'category_id' => 'required|numeric',
            'storehouse_id' => 'numeric',
            'department' => 'nullable|string|max:150',
        ]);

        $data = $request->all();
        $codePlaces = CodePlace::where('code_place', $data['code_place'])->whereYear('created_at', date('Y'))->first();
        if (!$codePlaces) {
            $category = Category::find($data['category_id']);

            $saveData = [
                'code_place' => $data['code_place'],
                'code_client_id' => $data['code_client_id'],
                'kg' => $data['kg'],
                'category_id' => $data['category_id'],
                'storehouse_id' => 1,
                'brand' => $category->name === 'Бренд',
            ];

            $deliveryMethod = \App\CodeHasDeliveryMethod::where('code_id', $data['code_client_id'])->first();
            if ($deliveryMethod) {
                $saveData['delivery_method_id'] = $deliveryMethod->delivery_method_id;
            } else {
                \App\CodeHasDeliveryMethod::create(['code_id' => $data['code_client_id'], 'delivery_method_id' => 0]);
            }

            $department = \App\Department::where('code_id', $data['code_client_id'])->first();
            if ($department) {
                $saveData['department'] = $department->department;
            } else {
                \App\Department::create(['code_id' => $data['code_client_id'], 'department' => null]);
            }


            if (array_key_exists('things', $data)) {
                $saveData['things'] = $data['things'];
            }
            if (array_key_exists('cube', $data)) {
                $saveData['cube'] = $data['cube'];
            }
            if (array_key_exists('notation', $data)) {
                $saveData['notation'] = $data['notation'];
            }
            if (array_key_exists('shop', $data)) {
                $saveData['shop'] = $data['shop'];
                Shop::firstOrCreate(['name' => $data['shop']]);
            }
            if (array_key_exists('things', $data) && $data['things']) {
                $things = json_decode($data['things']);
                foreach ($things as $item) {
                    Thingslist::firstOrCreate(['name' => $item->{'title'}]);
                }
            }
            if (array_key_exists('for_kg', $data) && array_key_exists('for_place', $data)) {
                $saveData['for_kg'] = $data['for_kg'];
                $saveData['for_place'] = $data['for_place'];
                $price = CodesPrices::updateOrCreate(['code_id' => $data['code_client_id'], 'category_id' => $category->id], ['for_kg' => $data['for_kg'], 'for_place' => $data['for_place']]);
                $priceData = ['code_client_id' => $data['code_client_id'], 'category_id' => $category->id, 'for_kg' => $data['for_kg'], 'for_place' => $data['for_place']];
                $this->storehouseDataHistory($price->id, $priceData, 'updateOrCreate', (new CodesPrices)->getTable());
            } else if (array_key_exists('for_kg', $data)) {
                $saveData['for_kg'] = $data['for_kg'];
                $price = CodesPrices::updateOrCreate(['code_id' => $data['code_client_id'], 'category_id' => $category->id], ['for_kg' => $data['for_kg']]);
                $priceData = ['code_client_id' => $data['code_client_id'], 'category_id' => $category->id, 'for_kg' => $data['for_kg']];
                $this->storehouseDataHistory($price->id, $priceData, 'updateOrCreate', (new CodesPrices)->getTable());
            } else if (array_key_exists('for_place', $data)) {
                $saveData['for_place'] = $data['for_place'];
                $price = CodesPrices::updateOrCreate(['code_id' => $data['code_client_id'], 'category_id' => $category->id], ['for_place' => $data['for_place']]);
                $priceData = ['code_client_id' => $data['code_client_id'], 'category_id' => $category->id, 'for_place' => $data['for_place']];
                $this->storehouseDataHistory($price->id, $priceData, 'updateOrCreate', (new CodesPrices)->getTable());
            } else {
                $price = CodesPrices::where('code_id', $data['code_client_id'])->where('category_id', $category->id)->first();
                if ($price) {
                    $saveData['for_kg'] = $price->for_kg;
                    $saveData['for_place'] = $price->for_place;
                }
            }

            $storehouse = StorehouseData::create($saveData);
            \App\CodePlace::create(['code_place' => $saveData['code_place']]);
            $saveDataForStorehouseDataHistory = [
                'code_place' => $saveData['code_place'],
                'storehouse_entry_id' => $storehouse->id,
                'code_client_id' => $saveData['code_client_id'],
                'kg' => $saveData['kg'],
                'category_id' => $saveData['category_id'],
                'place' => 1,
            ];
            StorehouseHistory::create($saveDataForStorehouseDataHistory);
            $this->storehouseDataHistory($storehouse->id, $saveData, 'create', (new StorehouseData)->getTable());

            $arrData = $this->storehouseDataList(1);

            return response(['storehouseData' => $arrData->where('storehouse_data.id', $storehouse->id)->where('storehouse_data.fax_id', 0)->first(), 'shopNames' => $this->getShopNames('data'), 'thingsList' => $this->getThingsList('data')]);
        } else {
            $this->throwError();
        }
    }

    public function update(Request $request)
    {
        $data = $this->stripData($request->except('id', 'replacePrice'));
        $codePlaces = null;
        if (array_key_exists('code_place', $data)) {
            $codePlaces = CodePlace::where('code_place', $data['code_place'])->whereYear('created_at', date('Y'))->first();
        }

        if (!$codePlaces) {


            if (array_key_exists('category_id', $data)) {
                $category = Category::find($data['category_id']);
                if ($category->name === 'Бренд' || $category->name === 'Авиа') {
                    $data['brand'] = true;
                } else {
                    $data['brand'] = false;
                }
            }
            if (array_key_exists('for_kg', $data) && is_null($data['for_kg'])) {
                $data['for_kg'] = 0;
            }

            if (array_key_exists('for_place', $data) && is_null($data['for_place'])) {
                $data['for_place'] = 0;
            }
            $entry = StorehouseData::find($request->id);
            $price = CodesPrices::where('code_id', $entry->code_client_id)->where('category_id', $entry->category_id)->first();
            if ($price) {
                if ($request->replacePrice && array_key_exists('for_kg', $data)) {
                    $price->for_kg = $data['for_kg'];
                } else if (array_key_exists('for_kg', $data) && !$price->for_kg) {
                    $price->for_kg = $data['for_kg'];
                }
                if ($request->replacePrice && array_key_exists('for_place', $data)) {
                    $price->for_place = $data['for_place'];
                } else if (array_key_exists('for_place', $data) && !$price->for_place) {
                    $price->for_place = $data['for_place'];
                }
                $price->save();
            } else {
                $arrForCreateCodePrice = [
                    'code_id' => $entry->code_client_id,
                    'category_id' => $entry->category_id,
                ];
                if (array_key_exists('for_kg', $data)) {
                    $arrForCreateCodePrice['for_kg'] = $data['for_kg'];
                }
                if (array_key_exists('for_place', $data)) {
                    $arrForCreateCodePrice['for_place'] = $data['for_place'];
                }
                CodesPrices::create($arrForCreateCodePrice);
            }

            if (array_key_exists('delivery_method_id', $data)) {
                $entry = StorehouseData::find($request->id);
                if ($entry) {
                    \App\CodeHasDeliveryMethod::updateOrCreate(['code_id' => $entry->code_client_id], ['delivery_method_id' => $data['delivery_method_id']]);
                }
            }

            if (array_key_exists('shop', $data) && $data['shop']) {
                Shop::firstOrCreate(['name' => $data['shop']]);
            } else if (array_key_exists('shop', $data) && !$data['shop']) {
                $data['shop'] = null;
            }


            $rul = [];
            foreach ($this->rules as $key => $value) {
                if (array_key_exists($key, $data)) {
                    $rul[$key] = $value;
                }
            }

            $this->validate($request, $rul);

            if (array_key_exists('code_place', $data)) {
                $entry = StorehouseData::find($request->id);
                if ($entry) {
                    \App\CodePlace::where(['code_place' => $entry->code_place])->delete();
                }

                \App\CodePlace::create(['code_place' => $data['code_place']]);
            }

            StorehouseData::where('id', $request->id)->update($data);
            $storehouseHistoryData = [];
            $storehouseHistoryUpdateKeys = ['code_place', 'code_client_id', 'place', 'kg', 'category_id'];
            foreach ($storehouseHistoryUpdateKeys as $item) {
                if (array_key_exists($item, $data)) {
                    $storehouseHistoryData[$item] = $data[$item];
                }
            }
            StorehouseHistory::where('storehouse_entry_id', $request->id)->update($storehouseHistoryData);
            $this->storehouseDataHistory($request->id, $data, 'update', (new StorehouseData)->getTable());

            $arrData = $this->storehouseDataList(1);

            $entry = StorehouseData::find($request->id);
            if ($entry && $entry->fax_id > 0) {
                return response(['storehouseData' => $arrData->where('storehouse_data.id', $request->id)->first()]);
            }

            return response(['storehouseData' => $arrData->where('storehouse_data.id', $request->id)->first(), 'shopNames' => $this->getShopNames('data'), 'thingsList' => $this->getThingsList('data')]);
        } else {
            $this->throwError();
        }
    }

    public function throwError()
    {
        throw ValidationException::withMessages([
            'code_place' => [trans('Этот код занят')],
        ]);
    }

    public function getShopNames($value = null)
    {
        $shopNames = Shop::orderBy('name');
        $plucked = $shopNames->pluck('name')->unique()->values()->all();
        if ($value === 'data') {
            return $plucked;
        }
        return response($plucked);
    }

    public function getThingsList($value = null)
    {
        $thingsNames = Thingslist::orderBy('name');
        $plucked = $thingsNames->pluck('name')->unique()->values()->all();
        if ($value === 'data') {
            return $plucked;
        }
        return response($plucked);
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Fax\FaxSheetsExport(0, $request->ids), 'storehouseData.xlsx');
    }

    public function storehouseDataHistory($id, $data, $action, $table)
    {
        if (array_key_exists('code_client_id', $data)) {
            $code = \App\Code::find($data['code_client_id']);
            if ($code) {
                $data['code_client_name'] = $code->code;
            }

        }
        if (array_key_exists('code_id', $data)) {
            $code = \App\Code::find($data['code_id']);
            if ($code) {
                $data['code_client_name'] = $code->code;
            }

        }
        if (array_key_exists('category_id', $data)) {
            $category = \App\Category::find($data['category_id']);
            if ($category) {
                $data['category_name'] = $category->name;
            }

        }

        if (array_key_exists('fax_id', $data)) {
            $fax = \App\Fax::find($data['fax_id']);
            if ($fax) {
                $data['fax_name'] = $fax->name;
            } else {
                $data['fax_name'] = 'Возврат на склад';
            }
        }
        if (array_key_exists('delivery_method_id', $data)) {
            $deliveryMethod = \App\CodeHasDeliveryMethod::find($data['delivery_method_id']);
            if ($deliveryMethod) {
                $data['delivery_method_name'] = $deliveryMethod->name;
            }
        }
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => $table, 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        $arrayData = $this->storehouseDataList(1)->whereIn('storehouse_data.id', $request->ids)->get();
        foreach ($arrayData as $entry) {
            if ($entry) {
                CodePlace::where('code_place', $entry->code_place)->delete();
                $entry->destroyed = true;
                $entry->save();
                $this->storehouseDataHistory($entry->id, $entry->toArray(), 'destroy', (new StorehouseData)->getTable());
                $entry->delete();
            }
        }
        StorehouseHistory::whereIn('storehouse_entry_id', $request->ids)->delete();
        $this->storehouseDataHistory(0, $arrayData->toArray(), 'destroy', (new FaxData())->getTable());
        return response(['status' => true]);
    }

    public function getStorehouseDataHistory($id)
    {
        $storehouseDataHistory = \App\History::where('table', (new StorehouseData)->getTable())->where('entry_id', $id)->get();
        return response(['storehouseDataHistory' => $storehouseDataHistory]);
    }

    public function getNewStorehouseData(Request $request)
    {
        $this->validate($request, [
            'created_at' => 'required|max:100',
            'updated_at' => 'required|date',
        ]);
//        return response(['asc' => date("Y-m-d H:i:s", strtotime($request->created_at))]);
        return response(['newData' => $this->storehouseDataList(1)
            ->where('storehouse_data.fax_id', 0)
            ->where('storehouse_data.created_at', '>', date("Y-m-d H:i:s", strtotime($request->created_at)))
            ->orWhere('storehouse_data.updated_at', '>', $request->updated_at)
            ->get()]);
    }

    public function setTransfersStorehouseFax(Request $request)
    {
        $storehouseIds = $request->storehouseIds;
        $faxIds = $request->faxIds;
        $faxId = $request->id;
        $d1 = StorehouseData::whereIn('id', $storehouseIds)->get();
        $d2 = StorehouseData::whereIn('id', $faxIds)->get();
        $d1->each(function ($item) {
            if ($item->fax_id) {
                $item->fax_id = 0;
                $item->save();
                $this->storehouseDataHistory($item['id'], ['fax_id' => 0], 'update', (new StorehouseData)->getTable());
            }
        });

        $d2->each(function ($item) use ($faxId) {
            if ($item->fax_id !== $faxId) {
                $item->fax_id = $faxId;
                $item->save();
                $this->storehouseDataHistory($item['id'], ['fax_id' => $faxId], 'update', (new StorehouseData)->getTable());
            }
        });

        return response(['status' => true, 'd1' => $d1, 'd2' => $d2]);
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

    public function updateFaxCombineData(Request $request)
    {
        $data = $request->all();
        $ids = [];
        foreach ($data as $elem) {
            array_push($ids, $elem['id']);
            $needData = array_diff_key($elem, array_flip(["arr", "id"]));
            foreach ($elem['arr'] as $item) {
                array_push($ids, $item['id']);
                $price = CodesPrices::where('code_id', $item['code_client_id'])->where('category_id', $item['category_id'])->first();
                if ($price) {
                    $priceDataCollection = collect($price->toArray());
                    if ($elem['replacePrice'] && array_key_exists('for_kg', $elem)) {
                        $price->for_kg = $elem['for_kg'];
                        $priceData = $priceDataCollection->except(['created_at', 'updated_at', 'for_place']);
                        $this->storehouseDataHistory($price->id, $priceData->all(), 'update', (new CodesPrices)->getTable());
                    } else if (array_key_exists('for_kg', $elem) && !$price->for_kg) {
                        $price->for_kg = $elem['for_kg'];
                        $priceData = $priceDataCollection->except(['created_at', 'updated_at', 'for_place']);
                        $this->storehouseDataHistory($price->id, $priceData->all(), 'update', (new CodesPrices)->getTable());
                    }
                    if ($elem['replacePrice'] && array_key_exists('for_place', $elem)) {
                        $price->for_place = $elem['for_place'];
                        $priceData = $priceDataCollection->except(['created_at', 'updated_at', 'for_kg']);
                        $this->storehouseDataHistory($price->id, $priceData->all(), 'update', (new CodesPrices)->getTable());
                    } else if (array_key_exists('for_place', $elem) && !$price->for_place) {
                        $price->for_place = $elem['for_place'];
                        $priceData = $priceDataCollection->except(['created_at', 'updated_at', 'for_kg']);
                        $this->storehouseDataHistory($price->id, $priceData->all(), 'update', (new CodesPrices)->getTable());
                    }
                    $price->save();
                } else {
                    $arrForCreateCodePrice = [
                        'code_id' => $item['code_client_id'],
                        'category_id' => $item['category_id'],
                    ];
                    if (array_key_exists('for_kg', $elem)) {
                        $arrForCreateCodePrice['for_kg'] = $elem['for_kg'];
                    }
                    if (array_key_exists('for_place', $elem)) {
                        $arrForCreateCodePrice['for_place'] = $elem['for_place'];
                    }
                    $newPrice = CodesPrices::create($arrForCreateCodePrice);
                    $this->storehouseDataHistory($newPrice->id, $arrForCreateCodePrice, 'create', (new CodesPrices)->getTable());
                }
                if (array_key_exists('category_id', $item)) {
                    $category = Category::find($item['category_id']);
                    if ($category && $category->name === 'Бренд') {
                        $needData['brand'] = true;
                    } else {
                        $needData['brand'] = false;
                    }
                }
                if (array_key_exists('delivery_method_id', $elem)) {
                    $entry = StorehouseData::find($item['id']);
                    if ($entry) {
                        \App\CodeHasDeliveryMethod::updateOrCreate(['code_id' => $entry->code_client_id], ['delivery_method_id' => $elem['delivery_method_id']]);
                    }
                }
                if (array_key_exists('department', $elem)) {
                    $entry = StorehouseData::find($item['id']);
                    if ($entry) {
                        \App\Department::updateOrCreate(['code_id' => $entry->code_client_id], ['department' => $elem['department']]);
                    }
                }
                if (isset($needData['replacePrice'])) {
                    unset($needData['replacePrice']);
                }

                StorehouseData::where('id', $item['id'])->update($needData);
                $this->storehouseDataHistory($item['id'], $needData, 'update', (new StorehouseData)->getTable());
            }
        }

        $arrData = $this->storehouseDataList(1);

        return response(['updatedData' => $this->setReplacePrice($arrData->whereIn('storehouse_data.id', $ids)->get())]);
    }

    public function moveFromStorehouseToFax(Request $request)
    {
        $faxId = $request->faxId;
        $data = $this->storehouseDataList(1)->whereIn('storehouse_data.id', $request->ids)->get();
        $moveFromFaxId = $data->first()->fax_id;
        $data->each(function ($item) use ($faxId) {
            $item->fax_id = $faxId;
            $item->save();
            $this->storehouseDataHistory($item['id'], ['fax_id' => $faxId], 'update', (new StorehouseData)->getTable());
        });
        $arrayData = $data->toArray();
        $moveFromFax = Fax::find($moveFromFaxId);
        $moveFromFaxName = 'пусто';
        if ($moveFromFax) {
            $moveFromFaxName = $moveFromFax->name;
        }
        $arrayData['moveData'] = ['moveToFax' => Fax::find($faxId)->name, 'moveFromFax' => $moveFromFaxName];
        $this->storehouseDataHistory(0, $arrayData, 'move', (new FaxData())->getTable());
        return response(['status' => true]);
    }

    public function storehouseHistoryDataList()
    {
        $queryData = StorehouseHistory::select(
            'storehouse_histories.id',
            'storehouse_histories.code_place',
            'storehouse_histories.code_client_id',
            'storehouse_histories.place',
            'storehouse_histories.kg',
            'storehouse_histories.category_id',
            'storehouse_histories.created_at',
            'storehouse_histories.updated_at',
            'codes.code as code_client_name',
            'categories.name as category_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_histories.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_histories.category_id')
            ->orderBy('storehouse_histories.id', 'desc');
        return $queryData;
    }

    public function getStorehousePeriodData(Request $request)
    {
        $data = $this->storehouseHistoryDataList()
            ->whereDate('storehouse_histories.created_at', '>=', $request->from)
            ->whereDate('storehouse_histories.created_at', '<=', $request->to)->get();
        return response(['cargo' => $data]);
    }

    public function getClientStorehouseData()
    {
        $data = $this->storehouseDataList(1)->whereIn('storehouse_data.code_client_id', json_decode(auth()->user()->code_ids))->where('storehouse_data.fax_id', 0)->get();
        $faxesIds = $this->storehouseDataList(1)->pluck('fax_id')->unique()->toArray();
        $faxData = Fax::whereIn('id', $faxesIds)->where('status', '!=', 3)->pluck('status', 'id');
        $fIds = array_keys($faxData->all());
        $data2 = $this->storehouseDataList(1)->whereIn('storehouse_data.code_client_id', json_decode(auth()->user()->code_ids))->whereIn('storehouse_data.fax_id', $fIds)->get();
        $data3 = $data->concat($data2);
        $data4 = $data3->map(function ($item) use ($faxData) {
            if (array_key_exists($item->fax_id, $faxData->all())) {
                $item->status = $faxData[$item->fax_id];
            } else {
                $item->status = -1;
            }

            return $item;
        });

        return response(['clientStorehouseData' => $data4]);
    }

    public function getEntriesWithPayNotation()
    {
        $data = $this->storehouseDataList(1)->where('storehouse_data.notation', 'like', '%' . 'опл' . '%')->get();
        $arr = [];
        $clientsIds = $data->map(function ($item) {
            return $item->code_client_id;
        })->unique()->values()->all();
        foreach ($clientsIds as $id) {
            $data = Cargo::where('code_client_id', $id)->get();
            if ($data->sum('sum')) {
                array_push($arr, $id);
            }
        }
        $data = $this->storehouseDataList(1)->where('storehouse_data.notation', 'like', '%' . 'опл' . '%')->whereIn('storehouse_data.code_client_id', $arr)->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Fax\FaxSheetsExport(0, $data->map(function ($item) {
            return $item->id;
        })), 'storehouseData.xlsx');
    }
}
