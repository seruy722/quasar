<?php

namespace App\Exports\Fax;

use App\Customer;
use App\StorehouseData;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FaxPostSheetModerExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $id;
    protected $ids;
    protected $data;
    protected $categories;

    public function __construct($id, $ids)
    {
        $this->id = $id;
        $this->ids = $ids;
    }

    public function view(): View
    {
        return view('exports.fax-post-sheet-export', [
            'collection' => $this->getCollection()
        ]);
    }

    private function getCollection()
    {
        $data = StorehouseData::select(
            'storehouse_data.code_place',
            'codes.code as code_client_name',
            'storehouse_data.code_client_id',
            'storehouse_data.delivery_method_id',
            'storehouse_data.kg',
            'storehouse_data.for_kg',
            'storehouse_data.for_place',
            'storehouse_data.department',
            'storehouse_data.notation'
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.destroyed', false);

        if (!empty($this->ids)) {
            $data = $data->whereIn('storehouse_data.id', $this->ids)->get();
        } else {
            $data = $data->where('storehouse_data.fax_id', $this->id)->get();
        }

        $sdf = $data->map(function ($item) {
            return $item['code_client_id'];
        });
        $deliveryMethodsId = $data->map(function ($item) {
            return $item['delivery_method_id'];
        });
        $customers = Customer::select('customers.*', 'cities.name AS city_name', 'codes.code as code')
            ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
            ->leftJoin('codes', 'codes.id', '=', 'customers.code_id')
            ->whereIn('customers.code_id', $sdf)
            ->get();
        $deliveryMethods = \App\DeliveryMethod::whereIn('id', $deliveryMethodsId)->get();

        $this->data = $data->map(function ($elem) use ($customers, $deliveryMethods) {
            $city = $customers->firstWhere('code_id', $elem['code_client_id']);
            $deliveryMethod = $deliveryMethods->firstWhere('id', $elem['delivery_method_id']);
            if ($city) {
                if ($city->city_name !== 'Харьков') {
                    $elem['phone'] = $city->phone;
                } else {
                    $elem['phone'] = null;
                }
                $elem['city_name'] = $city->city_name;
                $elem['name'] = $city->name;
            } else {
                $elem['city_name'] = null;
                $elem['phone'] = null;
                $elem['name'] = null;
            }

            if ($deliveryMethod) {
                $elem['delivery_method_name'] = $deliveryMethod->name;
            } else {
                $elem['delivery_method_name'] = null;
            }
            return $elem;
        })->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        });
        return $this->data;
    }

    public function title(): string
    {
        return 'Почта';
    }
}
