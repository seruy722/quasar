<?php

namespace App\Http\Controllers\Api;

use App\CodesPrices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CodesPriceController extends Controller
{
    public function codePriceDataList()
    {
        return CodesPrices::select('code_prices.*', 'categories.name as category_name', 'codes.code as code_client_name')->leftJoin('categories', 'categories.id', '=', 'code_prices.category_id')->leftJoin('codes', 'codes.id', '=', 'code_prices.code_id');

    }

    public function getCodesPrices()
    {
        $codesPrices = $this->codePriceDataList();
        return response(['codesPrice' => $codesPrices->get()->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        })->groupBy('code_client_name')]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code_id' => 'required|numeric',
            'category_id' => 'required|numeric',
        ]);
        $updateData = [];
        if ($request->for_kg) {
            $updateData['for_kg'] = $request->for_kg;
        }
        if ($request->for_place) {
            $updateData['for_place'] = $request->for_place;
        }

        $codePrice = CodesPrices::updateOrCreate(['code_id' => $request->code_id, 'category_id' => $request->category_id], $updateData);
        $this->storeCodePriceHistory($codePrice->id, $request->all(), 'create');
        $codePriceCollection = $this->codePriceDataList();
        return response(['codePrice' => $codePriceCollection->where('code_prices.id', $codePrice->id)->first()]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'code_id' => 'required|numeric',
        ]);

        $entry = CodesPrices::where('code_id', $request->code_id)->where('category_id', $request->category_id)->first();
        if ($entry && $entry->id !== $request->id) {
            $category = \App\Category::find($request->category_id);
            return response(['error' => 'Категория ' . $category->name . ' уже есть у клиента', 'eid' => $entry]);
        }

        $data = $request->except('id');

        CodesPrices::where('id', $request->id)->update($data);
        $this->storeCodePriceHistory($request->id, $request->all(), 'update');
        $codePriceCollection = $this->codePriceDataList();
        return response(['updatedCodePrice' => $codePriceCollection->where('code_prices.id', $request->id)->first()]);
    }

    public function delete($id)
    {
        CodesPrices::destroy($id);
        $this->storeCodePriceHistory($id, [], 'delete');
        return response(['status' => true]);
    }

    public function storeCodePriceHistory($id, $data, $action)
    {
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
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => (new CodesPrices)->getTable(), 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }

    public function getCodePriceHistory($id)
    {
        $codePriceHistory = \App\History::where('table', (new CodesPrices)->getTable())->where('entry_id', $id)->get();
        return response(['codePriceHistory' => $codePriceHistory]);
    }

    public function getNewCodesPrices(Request $request)
    {
        $this->validate($request, [
            'created_at' => 'required|max:100',
            'updated_at' => 'required|date',
        ]);
        return response(['updatedAndNewCodesPricesData' => $this->codePriceDataList()
            ->where('code_prices.created_at', '>', date("Y-m-d H:i:s", strtotime($request->created_at)))
            ->orWhere('code_prices.updated_at', '>', $request->updated_at)
            ->get()]);
    }
}
