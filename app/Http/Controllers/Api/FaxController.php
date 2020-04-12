<?php

namespace App\Http\Controllers\Api;

use App\Fax;
use App\FaxData;
use App\Http\Resources\FaxDataCommonExportResource;
use App\Http\Resources\FaxResource;
use App\Http\Resources\TransportResource;
use App\StorehouseData;
use App\Transport;
use App\Transporter;
use App\TransporterFaxesPrice;
use App\TransporterPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FaxController extends Controller
{

    protected $rules = [
        'name' => 'required|max:255|unique:faxes',
        'transport_id' => 'required|numeric',
        'transporter_id' => 'required|numeric',
        'status' => 'required|numeric',
        'departure_date' => 'nullable|string|max:50',
        'arrival_date' => 'nullable|string|max:50',
        'notation' => 'nullable|string|max:255',
    ];

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
        return Fax::select(
            'faxes.*',
            'users.name as user_name',
            'transporters.name as transporter_name',
            'transports.name as transport_name'
        )
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('transporters', 'transporters.id', '=', 'transporter_id')
            ->leftJoin('transports', 'transports.id', '=', 'transport_id')
            ->orderBy('created_at', 'DESC');
//        return FaxResource::collection(Fax::with(['transport', 'transporter', 'user'])->orderBy('departure_date', 'DESC')->get());
    }

    public function getFaxesList()
    {
//        $queryData = FaxData::select(
//            'fax_data.*',
//            'codes.code as customer_code'
//        )
//            ->join('codes', function ($join) {
//                $join->on('codes.id', '=', 'fax_data.code_id');
//            })
//            ->where('fax_id', 1)
//            ->orderByRaw('customer_code + 0')
//            ->get();
//
//        $data = FaxDataCommonExportResource::collection($queryData);
//        $data = $data->collection;


        return response(['faxesList' => $this->faxesList()->get()]);
    }

    public function getOptionsData()
    {
        return response(['transporter' => TransportResource::collection(Transporter::all()), 'transport' => TransportResource::collection(Transport::all())]);
    }

    public function addFax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
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

        if ($request->has('departure_date') && $request->departure_date) {
            $arr['departure_date'] = date("Y-m-d H:i:s", strtotime($request->departure_date));
        } else if ($request->has('departure_date') && !$request->departure_date) {
            $arr['departure_date'] = null;
        }

        if ($request->has('arrival_date') && $request->arrival_date) {
            $arr['arrival_date'] = date("Y-m-d H:i:s", strtotime($request->arrival_date));
        } else if ($request->has('arrival_date') && !$request->arrival_date) {
            $arr['arrival_date'] = null;
        }

        $fax = Fax::create($arr);
        $this->storeFaxHistory($fax->id, $arr, 'create');

        // Добавление цен по категориям в зависимости от факса
        $transporterPrice = TransporterPrice::where('transporter_id', $request->transporter_id)->get();
        if ($transporterPrice->isNotEmpty()) {
            $transporterPrice->each(function ($item) use (&$fax) {
                TransporterFaxesPrice::create(['fax_id' => $fax->id, 'category_id' => $item->category_id, 'category_price' => $item->for_kg]);
            });
        }

        return response(['status' => true, 'faxID' => $fax->id, 'fax' => $this->faxesList()->where('faxes.id', $fax->id)->first()]);
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
        $data = $request->except('id');
        $rul = [];
        foreach ($this->rules as $key => $value) {
            if (array_key_exists($key, $data)) {
                $rul[$key] = $value;
            }
        }

        $this->validate($request, $rul);

        if (array_key_exists('departure_date', $data) && $data['departure_date']) {
            $data['departure_date'] = date("Y-m-d H:i:s", strtotime($data['departure_date']));
        } else if (array_key_exists('departure_date', $data) && !$data['departure_date']) {
            $data['departure_date'] = null;
        }

        if (array_key_exists('arrival_date', $data) && $data['arrival_date']) {
            $data['arrival_date'] = date("Y-m-d H:i:s", strtotime($data['arrival_date']));
        } else if (array_key_exists('arrival_date', $data) && !$data['arrival_date']) {
            $data['arrival_date'] = null;
        }

        $data['user_id'] = auth()->user()->id;

        Fax::where('id', $request->id)->update($data);
        $this->storeFaxHistory($request->id, $data, 'update');
        return response(['fax' => $this->faxesList()->where('faxes.id', $request->id)->first()]);
    }

    public function storeFaxHistory($id, $data, $action)
    {
        if (array_key_exists('transport_id', $data)) {
            $transport = \App\Transport::find($data['transport_id']);
            if ($transport) {
                $data['transport_name'] = $transport->name;
            }

        }
        if (array_key_exists('transporter_id', $data)) {
            $transporter = \App\Transporter::find($data['transporter_id']);
            if ($transporter) {
                $data['transporter_name'] = $transporter->name;
            }

        }
        if (array_key_exists('departure_date', $data)) {
            $data['departure_date'] = \Illuminate\Support\Carbon::parse($data['departure_date'])->toAtomString();
        }

        if (array_key_exists('arrival_date', $data)) {
            $data['arrival_date'] = \Illuminate\Support\Carbon::parse($data['arrival_date'])->toAtomString();
        }
        $data['user_name'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $saveData = ['table' => (new Fax)->getTable(), 'action' => $action, 'entry_id' => $id, 'history_data' => json_encode($data)];
        \App\History::create($saveData);
    }

    public function uploadToCargo(Request $request)
    {
        if ($request->value) {
            \App\StorehouseData::where('fax_id', $request->id)->update([
                'in_cargo' => false
            ]);
            Fax::where('id', $request->id)->update(['uploaded_to_cargo' => false]);
            $this->storeFaxHistory($request->id, ['uploaded_to_cargo' => false], 'update');
        } else {
            \App\StorehouseData::where('fax_id', $request->id)->update([
                'in_cargo' => true
            ]);
            Fax::where('id', $request->id)->update(['uploaded_to_cargo' => true]);
            $this->storeFaxHistory($request->id, ['uploaded_to_cargo' => true], 'update');
        }

        return response(['fax' => $this->faxesList()->where('faxes.id', $request->id)->first()]);
    }

    public function faxHistory($id)
    {
        $faxHistory = \App\History::where('table', (new Fax)->getTable())->where('entry_id', $id)->get();
        return response(['faxHistory' => $faxHistory]);
    }

    public function getNewFax(Request $request)
    {
        $this->validate($request, [
            'created_at' => 'required|max:100',
            'updated_at' => 'required|date',
        ]);
        return response(['newData' => $this->faxesList()
            ->where('faxes.created_at', '>', date("Y-m-d H:i:s", strtotime($request->created_at)))
            ->orWhere('faxes.updated_at', '>', $request->updated_at)
            ->get()]);
    }

    public function getFax($id)
    {
        return response(['fax' => Fax::where('id', $id)->first()]);
    }

    public function combineFaxes(Request $request)
    {
        $items = $request->all();
        $firstElem = current($items);
        $faxIds = array_map(function ($item) {
            return $item['id'];
        }, $items);

        // Добавление факса
        $fax = Fax::create([
            'name' => 'JOIN_Факс ',
            'transport_id' => $firstElem['transport_id'],
            'transporter_id' => $firstElem['transporter_id'],
            'status' => $firstElem['status'],
            'user_id' => auth()->user()->id,
        ]);
        $fax->join_faxes_ids = implode(",", $faxIds);
        // Добавление цен по категориям в зависимости от факса
        $transporterPrice = TransporterPrice::where('transporter_id', $firstElem['transporter_id'])->get();
        if ($transporterPrice->isNotEmpty()) {
            $transporterPrice->each(function ($item) use ($fax) {
                TransporterFaxesPrice::create(['fax_id' => $fax->id, 'category_id' => $item->category_id, 'category_price' => $item->for_kg]);
            });
        }

        $faxData = StorehouseData::whereIn('fax_id', $faxIds)->get();

        $transporter = Transporter::find($firstElem['transporter_id']);
        $fax->name = 'JOIN_Факс ' . $transporter->name . ' ' . $faxData->sum('place') . 'м_' . $faxData->sum('kg') . 'кг';
        $fax->save();

        return response()->json(['fax' => $this->faxesList()->where('faxes.id', $fax->id)->first()]);
    }

    public function updatePricesInFax($id)
    {
        return response(['res' => $id]);
    }
}
