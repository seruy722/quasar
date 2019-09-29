<?php

namespace App\Http\Controllers\Api;

use App\TransporterPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransporterPriceController extends Controller
{
    public function updateData(Request $request)
    {
        $this->validate($request, [
            '*.for_kg' => 'required|numeric',
            '*.category_id' => 'required|numeric',
            '*.transporter_id' => 'required|numeric',
        ]);

        $data = $request->all();
        $transporterID = $data[0]['transporter_id'];

        foreach ($data as $item) {
            TransporterPrice::updateOrCreate([
                'category_id' => $item['category_id'],
                'transporter_id' => $item['transporter_id'],
            ], ['for_kg' => $item['for_kg']]);
        }
//        FaxData::where('id', $data['id']);
        return response(['transporterPriceData' => TransporterPrice::where('transporter_id', $transporterID)->get()]);
    }
}
