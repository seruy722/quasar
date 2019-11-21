<?php

namespace App\Http\Controllers\Api;

use App\Fax;
use App\FaxData;
use App\Http\Resources\FaxDataCommonExportResource;
use App\Http\Resources\FaxResource;
use App\Http\Resources\TransportResource;
use App\Transport;
use App\Transporter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FaxController extends Controller
{

    protected function faxesList()
    {
//        $fax1 = FaxData::select('categories.name')
//            ->selectRaw('SUM(fax_data.place) as place')
//            ->selectRaw('SUM(fax_data.kg) as kg')
//            ->selectRaw('categories.id as category_id')
//            ->leftJoin('categories', 'categories.id', '=', 'fax_data.category_id')
//            ->where('fax_data.fax_id', $id)
//            ->groupBy('category_id')
//            ->get();
//        return Fax::select('faxes.*, users.name')->leftJoin('users', 'users.id', '=', 'user_id')->get();
        return Fax::orderBy('departure_date', 'DESC')->get();
//        return FaxResource::collection(Fax::with(['transport', 'transporter', 'user'])->orderBy('departure_date', 'DESC')->get());
    }

    public function getFaxesList()
    {
        $queryData = FaxData::select(
            'fax_data.*',
            'codes.code as customer_code'
        )
            ->join('codes', function ($join) {
                $join->on('codes.id', '=', 'fax_data.code_id');
            })
            ->where('fax_id', 1)
            ->orderByRaw('customer_code + 0')
            ->get();

        $data = FaxDataCommonExportResource::collection($queryData);
        $data = $data->collection;


        return response(['faxesList' => $this->faxesList(), 'res' => $data]);
    }

    public function getOptionsData()
    {
        return response(['transporter' => TransportResource::collection(Transporter::all()), 'transport' => TransportResource::collection(Transport::all())]);
    }

    public function addFax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:faxes',
            'transport_id' => 'required|numeric',
            'transporter_id' => 'required|numeric',
            'status' => 'required|numeric',
        ]);

        $arr = [
            'name' => $request->name,
            'transport_id' => $request->transport_id,
            'transporter_id' => $request->transporter_id,
            'status' => $request->status,
            'user_id' => auth()->user()->id,
        ];

        if ($request->status === 2) {
            $arr['departure_date'] = date('Y-m-d H:i:s');
        }

        $fax = Fax::create($arr);

        return response(['status' => true, 'faxID' => $fax->id, 'fax' => new FaxResource($fax)]);
    }

    public function deleteFax(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
        ]);

        Fax::destroy($request->ids);
        return response(['status' => true]);
    }

    public function updateFaxes(Request $request)
    {
        $this->validate($request, [
            '*.name' => 'required|max:255',
            '*.transport_id' => 'required|numeric',
            '*.transporter_id' => 'required|numeric',
        ]);

        $data = $request->all();


        foreach ($data as $item) {
            $arr = [
                'name' => $item['name'],
                'transport_id' => $item['transport_id'],
                'transporter_id' => $item['transporter_id'],
                'user_id' => auth()->user()->id,
            ];

            if ($item['departure_date']) {
                $arr['departure_date'] = \Illuminate\Support\Carbon::parse($item['departure_date'])->format('Y-m-d H:i:s');
            } else {
                $arr['departure_date'] = null;
            }

            Fax::where('id', $item['id'])->update($arr);
        }
        return response(['faxesList' => $this->faxesList()]);
    }
}
