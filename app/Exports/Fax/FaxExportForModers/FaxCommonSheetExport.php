<?php

namespace App\Exports\Fax\FaxExportForModers;

use App\StorehouseData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FaxCommonSheetExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents, WithHeadings
{
    protected $id;
    protected $headers = ['Код', 'Клиент', 'Мест', 'Вес', 'Категория', 'Магазин', 'Примечания', 'Опись вложения'];
    protected $data;
    protected $countEntries;
    protected $sumPlace;
    protected $sumKg;
    protected $moreEntries = 2;
    protected $categories;

    public function __construct($id)
    {
        $this->id = $id;

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
            ->where('storehouse_data.fax_id', $id)
            ->where('storehouse_data.destroyed', false)
            ->get();

        $this->data = $data->map(function ($item) {
            $arr = json_decode($item['things']);
            if ($item['things'] && is_array($arr)) {
                $newArr = '';
                foreach ($arr as $el) {
                    $newArr = $newArr . $el->title . ': ' . $el->count . '; ';
                }
                $item['things'] = $newArr;
            }
            return $item;
        })->sort(function ($a, $b) {
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


        $this->countEntries = $this->countEntriesFunc();
        $this->sumPlace = $this->countSum($this->data, 'place');
        $this->sumKg = $this->countSum($this->data, 'kg');

        $this->categories = StorehouseData::select('categories.name')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->orderBy('place', 'DESC')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.fax_id', $id)
            ->where('storehouse_data.destroyed', false)
            ->groupBy('category_id')
            ->get();
    }

    public function countEntriesFunc()
    {
        return $this->data->count();
    }

    public function countSum($data, string $field)
    {
        return $data->sum($field);
    }

    public function categories($data)
    {

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Общее';
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $header = 'A1:H1';
                $cellRange = 'A1:G' . ($this->countEntries + $this->moreEntries);
                $callPlace = 'C' . ($this->countEntries + $this->moreEntries);
                $callKg = 'D' . ($this->countEntries + $this->moreEntries);

                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getCell($callPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getCell($callKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getStyle($callPlace)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($callKg)->getFont()->setBold(500);

                $event->sheet->getDelegate()->getStyle('H1:H' . ($this->countEntries + $this->moreEntries))->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));

//                // CATEGORIES
                $categoriesCellRange = 'A' . ($this->countEntries + $this->moreEntries + 4) . ':C' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesKg = 'C' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesPlace = 'B' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesFooter = 'A' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4) . ':C' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);

                $arrCat = $this->categories->toArray();
                array_push($arrCat, ['name' => '', 'place' => $this->countSum($this->categories, 'place'), 'kg' => $this->countSum($this->categories, 'kg')]);
                array_unshift($arrCat, array(''), array(''), array(''));
                $event->sheet->appendRows($arrCat, $event);

                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getCell($cellCategoriesKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getCell($cellCategoriesPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellCategoriesFooter)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getFont()->setSize(10);

                $rows = $event->sheet->getDelegate()->toArray();
                foreach ($rows as $key => $value) {
                    if ($value[4] == 'Бренд' || $value[4] == 'Авиа') {
                        $event->sheet->getDelegate()->getStyle('A' . ($key + 1) . ':W' . ($key + 1))->getFont()->setBold(500);
                    }
                }
            }
        ];
    }
}
