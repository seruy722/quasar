<?php

namespace App\Exports\Fax;

use App\StorehouseData;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FaxCommonSheetExport implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    protected $id;
    protected $data;
    protected $categories;

    public function __construct($id)
    {
        $this->id = $id;
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
            ->where('storehouse_data.fax_id', $this->id)
            ->where('storehouse_data.destroyed', false)
            ->groupBy('category_id')
            ->get();

        $data = StorehouseData::select(
            'storehouse_data.code_place',
            'codes.code as code_client_name',
            'storehouse_data.place',
            'storehouse_data.kg',
            'categories.name as category_name',
            'storehouse_data.shop',
            'storehouse_data.notation',
            'storehouse_data.things'
        )
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.fax_id', $this->id)
            ->where('storehouse_data.destroyed', false)
            ->get();

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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rows = $event->sheet->getDelegate()->toArray();
                $countMainTableEntries = 0;
                foreach ($rows as $key => $value) {
                    if (!$value[2] && !$value[3] && !$value[7] && !$value[8]) {
                        break;
                    }
                    $countMainTableEntries++;
                }
                $header = 'A1:I1';
                $cellRange = 'A1:I' . ($countMainTableEntries);
                $cellRangeWithCategories = 'A1:I' . ($countMainTableEntries + 7);
                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRangeWithCategories)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                // CATEGORIES
                $categoriesCellRange = 'A' . ($countMainTableEntries + 4) . ':C' . ($countMainTableEntries + count($this->categories) + 4);
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $rows = $event->sheet->getDelegate()->toArray();
                foreach ($rows as $key => $value) {
                    if ($value[4] == 'Бренд' || $value[4] == 'Авиа') {
                        $event->sheet->getDelegate()->getStyle('A' . ($key + 1) . ':I' . ($key + 1))->getFont()->setBold(500);
                    }
                }
            }
        ];
    }
}
