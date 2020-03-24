<?php

namespace App\Http\Controllers\Api;

use App\TransporterFaxesPrice;
use App\TransporterPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransporterFaxesPriceController extends Controller
{
    public function updateData(Request $request)
    {
        $data = $request->data;
        Validator::make($data, [
            '*.category_price' => 'required|numeric',
            '*.category_id' => 'required|numeric',
            '*.fax_id' => 'required|numeric',
        ])->validate();

        foreach ($data as $item) {
            TransporterPrice::firstOrCreate(['category_id' => $item['category_id'], 'transporter_id' => $request->transporter_id], ['for_kg' => $item['category_price']]);

            TransporterFaxesPrice::updateOrCreate([
                'category_id' => $item['category_id'],
                'fax_id' => $item['fax_id'],
            ], ['category_price' => $item['category_price']]);
        }
        return response(null, 200);
    }

    public function getTransporterPriceData($id)
    {
        return response(['transporterPriceData' => TransporterFaxesPrice::where('fax_id', $id)->get()]);
    }

    public function saveCategoriesPrice(Request $request)
    {
        $data = $request->all();
        $faxId = 0;
        foreach ($data as $item) {
            $faxId = $item['fax_id'];
            TransporterFaxesPrice::updateOrCreate(['category_price' => $item['category_price']], ['category_id' => $item['category_id'], 'fax_id' => $item['fax_id']]);
        }
        return response(['transporterPriceData' => TransporterFaxesPrice::where('fax_id', $faxId)->get()]);
    }
}
