<?php

namespace App\Exports\Fax\FaxExportForAdmin;

use App\StorehouseData;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FaxPriceSheetExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $faxID;
    protected $ids;
    protected $data;

    public function __construct($faxID, $ids)
    {
        $this->faxID = $faxID;
        $this->ids = $ids;
    }

    public function view(): View
    {
        return view('exports.fax-price-sheet-export', [
            'collection' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $this->data = StorehouseData::select('codes.code as code')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->selectRaw('AVG(storehouse_data.for_kg) as for_kg')
            ->selectRaw('AVG(storehouse_data.for_place) as for_place')
            ->selectRaw('SUM(storehouse_data.kg) * AVG(storehouse_data.for_kg) + AVG(storehouse_data.for_place) * SUM(storehouse_data.place)  as sum')
            ->selectRaw('AVG(storehouse_data.brand) as brand')
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->groupBy('code_client_id', 'category_id', 'delivery_method_id');

        if (!empty($this->ids)) {
            $this->data = $this->data->whereIn('storehouse_data.id', $this->ids)->get();
        } else {
            $this->data = $this->data->where('storehouse_data.fax_id', $this->faxID)->get();
        }

        $this->data = $this->data->sort(function ($a, $b) {
            if ($a['code'] == $b['code']) {
                return 0;
            }

            return ($a['code'] < $b['code']) ? -1 : 1;
        });
        return $this->data;
    }

    public function title(): string
    {
        return 'Цены';
    }
}
