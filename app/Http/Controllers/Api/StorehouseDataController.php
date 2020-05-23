<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\CodesPrices;
use App\Shop;
use App\StorehouseData;
use App\Thingslist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StorehouseDataController extends Controller
{
    protected $rules = [
        'code_place' => 'required|max:12|unique:code_places',
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
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
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
            'code_place' => 'required|max:12|unique:code_places',
            'code_client_id' => 'required|numeric',
            'kg' => 'required|numeric',
            'for_kg' => 'numeric',
            'for_place' => 'numeric',
            'shop' => 'nullable|max:100',
            'things' => 'nullable|json|max:1000',
            'notation' => 'nullable|max:255',
            'category_id' => 'required|numeric',
            'storehouse_id' => 'numeric',
        ]);

        $data = $request->all();


        $category = Category::find($data['category_id']);

        $saveData = [
            'code_place' => $data['code_place'],
            'code_client_id' => $data['code_client_id'],
            'kg' => $data['kg'],
            'category_id' => $data['category_id'],
            'storehouse_id' => 1,
            'brand' => $category->name === 'Бренд',
        ];


        if (array_key_exists('things', $data)) {
            $saveData['things'] = $data['things'];
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
        $this->storehouseDataHistory($storehouse->id, $saveData, 'create', (new StorehouseData)->getTable());

        $arrData = $this->storehouseDataList(1);

        return response(['storehouseData' => $arrData->where('storehouse_data.id', $storehouse->id)->where('storehouse_data.fax_id', 0)->first(), 'shopNames' => $this->getShopNames('data'), 'thingsList' => $this->getThingsList('data')]);
    }

    public function update(Request $request)
    {
        $data = $this->stripData($request->except('id', 'replacePrice'));

        if (array_key_exists('category_id', $data)) {
            $category = Category::find($data['category_id']);
            if ($category->name === 'Бренд' || $category->name === 'Авиа') {
                $data['brand'] = true;
            } else {
                $data['brand'] = false;
            }
        }
        $entry = StorehouseData::find($request->id);
        $price = CodesPrices::where('code_id', $entry->code_client_id)->where('category_id', $entry->category_id)->first();
        if ($price) {
            if ($request->replacePrice && array_key_exists('for_kg', $data)) {
                $price->for_kg = $data['for_kg'];
            }
            if ($request->replacePrice && array_key_exists('for_place', $data)) {
                $price->for_kg = $data['for_place'];
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

        if (array_key_exists('for_kg', $data) && is_null($data['for_kg'])) {
            $data['for_kg'] = 0;
        }

        if (array_key_exists('for_place', $data) && is_null($data['for_place'])) {
            $data['for_place'] = 0;
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
        $this->storehouseDataHistory($request->id, $data, 'update', (new StorehouseData)->getTable());

        $arrData = $this->storehouseDataList(1);

        $entry = StorehouseData::find($request->id);
        if ($entry && $entry->fax_id > 0) {
            return response(['storehouseData' => $arrData->where('storehouse_data.id', $request->id)->first()]);
        }

        return response(['storehouseData' => $arrData->where('storehouse_data.id', $request->id)->first(), 'shopNames' => $this->getShopNames('data'), 'thingsList' => $this->getThingsList('data')]);
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

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Fax\FaxSheetsExport(0), 'storehouseData.xlsx');
    }

    public function storehouseDataHistory($id, $data, $action, $table)
    {
        if (array_key_exists('code_client_id', $data)) {
            $code = \App\Code::find($data['code_client_id']);
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
        foreach ($request->ids as $id) {
            $entry = StorehouseData::find($id);
            if ($entry) {
                $entry->destroyed = true;
                $entry->save();
                $this->storehouseDataHistory($id, $entry->toArray(), 'destroy', (new StorehouseData)->getTable());
                $entry->delete();
            }
        }
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
                    if ($elem['replacePrice'] && array_key_exists('for_kg', $elem)) {
                        $price->for_kg = $elem['for_kg'];
                    }
                    if ($elem['replacePrice'] && array_key_exists('for_place', $elem)) {
                        $price->for_kg = $elem['for_place'];
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
                    CodesPrices::create($arrForCreateCodePrice);
                }
                if (array_key_exists('category_id', $item)) {
                    $category = Category::find($item['category_id']);
                    if ($category && $category->name === 'Бренд') {
                        $needData['brand'] = true;
                    } else {
                        $needData['brand'] = false;
                    }
                }
                unset($needData['replacePrice']);
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
        $d2 = StorehouseData::whereIn('id', $request->ids)->get();
        $d2->each(function ($item) use ($faxId) {
            $item->fax_id = $faxId;
            $item->save();
            $this->storehouseDataHistory($item['id'], ['fax_id' => $faxId], 'update', (new StorehouseData)->getTable());
        });
        return response(['status' => true]);
    }
}
