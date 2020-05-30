<?php

namespace App\Exports\Fax;

use App\StorehouseData;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FaxCommonSheetExport implements FromView, ShouldAutoSize, WithTitle
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
        return view('exports.fax-common-sheet', [
            'collection' => $this->getCollection()
        ]);
    }

    private function getCollection()
    {
        $this->categories = StorehouseData::select('categories.name')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->orderBy('place', 'DESC')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.destroyed', false)
            ->groupBy('category_id');

        $data = StorehouseData::select(
            'storehouse_data.code_place',
            'codes.code as code_client_name',
            'storehouse_data.place',
            'storehouse_data.kg',
            'categories.name as category_name',
            'storehouse_data.shop',
            'storehouse_data.notation',
            'storehouse_data.things',
            'storehouse_data.brand'
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.destroyed', false);

        if (!empty($this->ids)) {
            $this->categories = $this->categories->whereIn('storehouse_data.id', $this->ids)->get();
            $data = $data->whereIn('storehouse_data.id', $this->ids)->get();
        } else {
            $this->categories = $this->categories->where('storehouse_data.fax_id', $this->id)->get();
            $data = $data->where('storehouse_data.fax_id', $this->id)->get();
        }

        $this->data = $data->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        })->map(function ($elem) {
            if (is_numeric($elem['code_client_name'])) {
                $elem['code_client_name'] = '007/' . $elem['code_client_name'];
            }
            return $elem;
        });

        return [$this->data, $this->categories];
    }

    public function title(): string
    {
        return 'Общее';
    }
}
