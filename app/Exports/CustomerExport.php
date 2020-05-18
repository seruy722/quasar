<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;
    protected $headers = ['Клиент', 'Имя', 'Телефон', 'Город', 'Добавлен'];

    public function __construct(array $ids)
    {
        if (empty($ids)) {
            $this->data = \App\Customer::select('codes.code as client_name', 'customers.name', 'customers.phone', 'cities.name as city_name', 'customers.created_at')
                ->join('codes', 'customers.code_id', '=', 'codes.id')
                ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
                ->get();
        } else {
            $this->data = \App\Customer::select('codes.code as client_name', 'customers.name', 'customers.phone', 'cities.name as city_name', 'customers.created_at')
                ->join('codes', 'customers.code_id', '=', 'codes.id')
                ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
                ->whereIn('customers.code_id', $ids)
                ->get();
        }

        $this->data = $this->data->sort(function ($a, $b) {
            if ($a['client_name'] == $b['client_name']) {
                return 0;
            }

            return ($a['client_name'] < $b['client_name']) ? -1 : 1;
        })->map(function ($elem) {
            if (is_numeric($elem['client_name'])) {
                $elem['client_name'] = '007/' . $elem['client_name'];
            }
            return $elem;
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
