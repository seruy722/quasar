<?php

namespace App\Http\Controllers\Api;

use App\Fax;
use App\Transporter;
use App\TransporterFaxesPrice;
use App\TransporterPrice;
use App\User;
use App\UserPermissionRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\UserPermission;

class TransporterFaxesPriceController extends Controller
{
    use UserPermission;

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
        if (auth()->user()->hasPermissionTo('view transporter fax price')) {
            $transporterPrice = TransporterFaxesPrice::where('fax_id', $id)->get();
            if ($transporterPrice->isEmpty()) {
                $fax = Fax::find($id);
                if ($fax) {
                    $transporterPrice = TransporterPrice::select('for_kg as category_price', 'category_id')->where('transporter_id', $fax->transporter_id)->get();
                }
            }
            return response(['transporterPriceData' => $transporterPrice]);
        }
        return response(['transporterPriceData' => null]);
    }

    public function saveCategoriesPrice(Request $request)
    {
        $data = $request->all();
        $firstElem = current($data);
        $faxId = $firstElem['fax_id'];
        $fax = Fax::find($faxId);
        $transporterId = $fax ? $fax->transporter_id : null;
        foreach ($data as $item) {
            TransporterFaxesPrice::updateOrCreate(['fax_id' => $item['fax_id'], 'category_id' => $item['category_id']], ['category_price' => $item['category_price']]);
            if ($transporterId) {
                TransporterPrice::updateOrCreate(['transporter_id' => $transporterId, 'category_id' => $item['category_id']], ['for_kg' => $item['category_price']]);
            }
        }
        return response(['transporterPriceData' => TransporterFaxesPrice::where('fax_id', $faxId)->get()]);
    }
}
