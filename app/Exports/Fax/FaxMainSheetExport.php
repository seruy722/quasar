<?php

namespace App\Exports\Fax;

use App\StorehouseData;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FaxMainSheetExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $id;
    protected $ids;

    public function __construct($id, $ids)
    {
        $this->id = $id;
        $this->ids = $ids;
    }

    public function view(): View
    {
        return view('exports.moder.fax-main-sheet-export', [
            'collection' => $this->getCollection()
        ]);
    }

    private function getCollection()
    {
        $data = StorehouseData::select('codes.code as code_place')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->selectRaw('AVG(storehouse_data.category_id) as category')
            ->selectRaw('AVG(storehouse_data.brand) as brand')
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.destroyed', false)
            ->groupBy('code_client_id', 'category_id');

        if (!empty($this->ids)) {
            $data = $data->whereIn('storehouse_data.id', $this->ids)->get();
        } else {
            $data = $data->where('storehouse_data.fax_id', $this->id)->get();
        }


        $categories = \App\Category::select('id as value', 'name as label')->get();

        $data = $data->map(function ($item) use (&$categories) {
            $category = $categories->firstWhere('value', $item['category']);
            if ($category) {
                $item['category'] = $category->label;
            } else {
                $item['category'] = null;
            }
            return $item;
        })->sort(function ($a, $b) {
            if ($a['code_place'] == $b['code_place']) {
                return 0;
            }

            return ($a['code_place'] < $b['code_place']) ? -1 : 1;
        });

        return [$data];
    }


    public function title(): string
    {
        return 'Клиенты';
    }
}
