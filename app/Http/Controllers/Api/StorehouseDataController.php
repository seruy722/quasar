<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\StorehouseData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StorehouseDataController extends Controller
{
    public function getStorehouseData($id)
    {
        return response()->json($this->storehouseDataList($id), 200, [], JSON_NUMERIC_CHECK);
    }

    public function storehouseDataList($id)
    {
        $queryData = StorehouseData::select(
            'storehouse_data.*',
            'codes.code as code_client_name',
            'categories.name as category_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_id', $id)
            ->where('destroyed', false)
            ->orderByRaw('code_client_name')
            ->get();


//        $faxesData = $queryData->groupBy('code_client_id')->map(function (\Illuminate\Support\Collection $code) {
//            return $code->groupBy('category_id')->map(function (\Illuminate\Support\Collection $category) {
//                return $category->groupBy(function ($for_kg) {
//                    return (string)$for_kg->for_kg;
//                });
//            });
//        });

        $categories = StorehouseData::select('categories.name')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_data.storehouse_id', $id)
            ->orderBy('place', 'DESC')
            ->groupBy('category_id')
            ->get();

        return ['storehouseData' => $queryData, 'categories' => $categories];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code_place' => 'required|max:255|unique:storehouse_data',
            'code_client_id' => 'required|numeric',
            'kg' => 'required|numeric',
            'shop' => 'max:255',
            'things' => 'json|max:1000',
            'notation' => 'max:255',
            'for_kg' => 'numeric',
            'for_place' => 'numeric',
            'category_id' => 'required|numeric',
            'storehouse_id' => 'numeric',
        ]);

        $data = $request->all();


        $category = Category::find($data['category_id']);

        $saveData = [
            'code_place' => $data['code_place'],
            'code_client_id' => $data['code_client_id'],
            'kg' => $data['kg'],
            'shop' => $data['shop'],
            'notation' => $data['notation'],
            'for_kg' => $data['for_kg'],
            'for_place' => $data['for_place'],
            'category_id' => $data['category_id'],
            'storehouse_id' => 1,
            'brand' => $category->name === 'Бренд',
        ];


        if (array_key_exists('things', $data) && $data['things']) {
            $saveData['things'] = $data['things'];
//            array_push($saveData, $data['things']);
        }

        StorehouseData::create($saveData);

        return response($this->storehouseDataList(1));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            '*.code_place' => 'unique:storehouse_data,id',
            '*.code_client_id' => 'required|numeric',
            '*.kg' => 'required|numeric',
            '*.shop' => 'max:255',
            '*.things' => 'json|max:1000',
            '*.notation' => 'max:255',
            '*.category_id' => 'required|numeric',
            '*.destroyed' => 'required|boolean',
            '*.storehouse_id' => 'numeric',
        ]);

        $data = $request->all();
        $storehouseDataID = $data[0]['storehouse_id'];

//        return response(['oK' => $storehouseDataID]);


        foreach ($data as $item) {
            $category = Category::find($item['category_id']);

            $updateData = [
                'code_place' => $item['code_place'],
                'code_client_id' => $item['code_client_id'],
                'kg' => $item['kg'],
                'shop' => $item['shop'],
                'notation' => $item['notation'],
                'category_id' => $item['category_id'],
                'storehouse_id' => $item['storehouse_id'],
                'destroyed' => $item['destroyed'],
                'brand' => $category->name === 'Бренд',
            ];

            if (array_key_exists('things', $item) && $item['things']) {
                $updateData['things'] = $item['things'];
            }

            StorehouseData::where('id', $item['id'])->update($updateData);
        }

        return response($this->storehouseDataList($storehouseDataID));
    }
}
