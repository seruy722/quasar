<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Storehouse;
use App\StorehouseData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StorehouseDataController extends Controller
{
    protected $rules = [
        'code_place' => 'unique:storehouse_data',
        'code_client_id' => 'required|numeric',
        'kg' => 'required|numeric',
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
        return response()->json($arrData->get(), 200, [], JSON_NUMERIC_CHECK);
    }

    public function storehouseDataList($id)
    {
        $queryData = StorehouseData::select(
            'storehouse_data.id',
            'storehouse_data.code_place',
            'storehouse_data.code_client_id',
            'storehouse_data.place',
            'storehouse_data.kg',
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
            ->where('storehouse_data.fax_id', 0)
            ->where('storehouse_data.destroyed', false)
            ->orderBy('storehouse_data.created_at', 'desc');


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
            'code_place' => 'required|max:12|unique:storehouse_data',
            'code_client_id' => 'required|numeric',
            'kg' => 'required|numeric',
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
        }

        $storehouse = StorehouseData::create($saveData);
        $this->storehouseDataHistory($storehouse->id, $saveData, 'create');

        $arrData = $this->storehouseDataList(1);

        return response(['storehouseData' => $arrData->where('storehouse_data.id', $storehouse->id)->first()]);
    }

    public function update(Request $request)
    {
        $data = $this->stripData($request->except('id'));

        if (array_key_exists('category_id', $data)) {
            $category = Category::find($data['category_id']);
            if ($category->name === 'Бренд' || $category->name === 'Авиа') {
                $data['brand'] = true;
            } else {
                $data['brand'] = false;
            }
        }

        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        StorehouseData::where('id', $request->id)->update($data);
        $this->storehouseDataHistory($request->id, $data, 'update');

        $arrData = $this->storehouseDataList(1);

        return response(['storehouseData' => $arrData->where('storehouse_data.id', $request->id)->first()]);
    }

    public function getShopNames()
    {
        $shopNames = StorehouseData::orderBy('shop');
        $plucked = $shopNames->pluck('shop')->unique()->values()->all();
        return response($plucked);
    }

    public function getThingsList()
    {
        $plucked = StorehouseData::pluck('things')->all();
        $res = array_map('json_decode', $plucked);
        $res2 = [];
        foreach ($res as $item) {
            if (is_array($item) && count($item)) {
                foreach ($item as $elem) {
                    array_push($res2, $elem->title);
                }
            }
        }
        return response(array_values(array_unique($res2)));
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StorehouseData\StorehouseExport($request->ids), 'storehouseData.xlsx');
    }

    public function storehouseDataHistory($id, $data, $action)
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
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => (new StorehouseData)->getTable(), 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
        ]);
        $data = ['destroyed' => true];
        foreach ($request->ids as $id) {
            StorehouseData::where('id', $id)->update($data);
            $this->storehouseDataHistory($id, $data, 'destroy');
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
            ->where('storehouse_data.created_at', '>', date("Y-m-d H:i:s", strtotime($request->created_at)))
            ->orWhere('storehouse_data.updated_at', '>', $request->updated_at)
            ->get()]);
    }
}
