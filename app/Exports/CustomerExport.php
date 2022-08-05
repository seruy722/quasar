<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;

    public function view(): View
    {
        return view('exports.customers-data-export', [
            'collection' => $this->getCollection()
        ]);
    }

    public function __construct(array $ids)
    {
        if (empty($ids)) {
            $this->data = \App\Customer::select('codes.code as client_name', 'customers.name', 'customers.phone', 'customers.code_id', 'cities.name as city_name', 'customers.created_at', 'customers.sex')
                ->join('codes', 'customers.code_id', '=', 'codes.id')
                ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
                ->get();
        } else {
            $this->data = \App\Customer::select('codes.code as client_name', 'customers.name', 'customers.phone', 'customers.code_id', 'cities.name as city_name', 'customers.created_at', 'customers.sex')
                ->join('codes', 'customers.code_id', '=', 'codes.id')
                ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
                ->whereIn('customers.code_id', $ids)
                ->get();
        }

        $this->data->map(function ($elem) {
            if (is_numeric($elem['client_name'])) {
                $elem['client_name'] = '007/' . $elem['client_name'];
            }
            return $elem;
        });
//        $codeHasDeliveryMethods = \App\CodeHasDeliveryMethod::select('delivery_methods.name', 'code_has_delivery_methods.code_id')->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'code_has_delivery_methods.delivery_method_id')->get();
//        $departments = \App\Department::all();
//
//        $this->data = $this->data->map(function ($elem) use ($codeHasDeliveryMethods, $departments) {
//            $deliveryMethod = $codeHasDeliveryMethods->firstWhere('code_id', $elem['code_id']);
//            $department = $departments->firstWhere('code_id', $elem['code_id']);
//
//            if ($deliveryMethod) {
//                $elem['delivery_method_name'] = $deliveryMethod->name;
//            } else {
//                $elem['delivery_method_name'] = null;
//            }
//            if ($department) {
//                $elem['department'] = $department->department;
//            } else {
//                $elem['department'] = null;
//            }
//            return $elem;
//        })->map(function ($elem) {
//            if (is_numeric($elem['client_name'])) {
//                $elem['client_name'] = '007/' . $elem['client_name'];
//            }
//            return $elem;
//        });
    }

    public function getCollection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Клиенты';
    }
}