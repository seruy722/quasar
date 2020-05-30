<?php

namespace App\Exports\Fax\FaxExportForAdmin;

use App\Customer;
use App\StorehouseData;
use App\TransporterFaxesPrice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FaxMainSheetExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $faxID;
    protected $ids;
    protected $data;
    protected $categories;

    public function __construct($faxID, $ids)
    {
        $this->faxID = $faxID;
        $this->ids = $ids;
    }

    public function view(): View
    {
        return view('exports.fax-main-sheet-export', [
            'collection' => $this->getCollection()
        ]);
    }

    private function getCollection()
    {
        $this->data = StorehouseData::select('codes.code as code_place')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->selectRaw('AVG(storehouse_data.for_kg) as for_kg')
            ->selectRaw('AVG(storehouse_data.for_place) as for_place')
            ->selectRaw('SUM(storehouse_data.kg) * AVG(storehouse_data.for_kg) + AVG(storehouse_data.for_place) * SUM(storehouse_data.place)  as sum')
            ->selectRaw('AVG(storehouse_data.brand) as brand')
            ->selectRaw('AVG(storehouse_data.code_client_id) as code_id')
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->groupBy('code_client_id', 'category_id');

        $this->categories = StorehouseData::select('categories.name')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->orderBy('kg', 'DESC')
            ->groupBy('category_id');

        if (!empty($this->ids)) {
            $this->categories = $this->categories->whereIn('storehouse_data.id', $this->ids)->get();
            $this->data = $this->data->whereIn('storehouse_data.id', $this->ids)->get();
        } else {
            $this->categories = $this->categories->where('storehouse_data.fax_id', $this->faxID)->get();
            $this->data = $this->data->where('storehouse_data.fax_id', $this->faxID)->get();
        }

        $categoriesPrice = TransporterFaxesPrice::select('categories.name', 'transporter_faxes_prices.category_price as price')
            ->leftJoin('categories', 'categories.id', '=', 'transporter_faxes_prices.category_id')
            ->where('fax_id', $this->faxID)->get();

        $this->categories = $this->categories->map(function ($item) use ($categoriesPrice) {
            $price = $categoriesPrice->firstWhere('name', $item['name']);
            if ($price) {
                $item['price'] = $price->price;
                $item['sum'] = $price->price * $item['kg'];
            } else {
                $item['price'] = null;
                $item['sum'] = null;
            }

            return $item;
        });

        $sdf = $this->data->map(function ($item) {
            return $item['code_id'];
        });
        $customers = Customer::select('customers.*', 'cities.name AS city_name', 'codes.code as code')
            ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
            ->leftJoin('codes', 'codes.id', '=', 'customers.code_id')
            ->whereIn('customers.code_id', $sdf)
            ->get();

        $this->data = $this->data->map(function ($elem) use ($customers) {
            $city = $customers->firstWhere('code_id', $elem['code_id']);
            if ($city) {
                $elem['city_name'] = $city->city_name;
            } else {
                $elem['city_name'] = null;
            }
            return $elem;
        })->sort(function ($a, $b) {
            if ($a['code_place'] == $b['code_place']) {
                return 0;
            }

            return ($a['code_place'] < $b['code_place']) ? -1 : 1;
        });

        return [$this->data, $this->categories];
    }


    public function title(): string
    {
        return 'Клиенты';
    }
}
