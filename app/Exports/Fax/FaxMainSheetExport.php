<?php

namespace App\Exports\Fax;

use App\StorehouseData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FaxMainSheetExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents, WithHeadings
{
    protected $data;
    protected $headers = ['Клиент', 'Мест', 'Вес', 'Категория'];
    protected $countEntries;
    protected $sumPlace = 0;
    protected $sumKg = 0;
    protected $moreEntries = 2;

    public function __construct($id)
    {
        $data = StorehouseData::select('codes.code')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->selectRaw('AVG(storehouse_data.category_id) as category')
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'storehouse_data.category_id')
            ->where('storehouse_data.storehouse_id', 1)
            ->where('storehouse_data.fax_id', $id)
            ->where('storehouse_data.destroyed', false)
            ->groupBy('code_client_id', 'category_id')
            ->get();


        $categories = \App\Category::select('id as value', 'name as label')->get();

        $this->data = $data->map(function ($item) use (&$categories) {
            $filtered = $categories->firstWhere('value', $item['category']);
            $item['category'] = $filtered->label;
            return $item;
        })->sort(function ($a, $b) {
            if ($a['code'] == $b['code']) {
                return 0;
            }

            return ($a['code'] < $b['code']) ? -1 : 1;
        });

        $this->countEntries = $this->countEntries();
        $this->sumPlace = $this->countSum($this->data, 'place');
        $this->sumKg = $this->countSum($this->data, 'kg');
    }

    public function countEntries()
    {
        return $this->data->count();
    }

    public function countSum($data, string $field)
    {
        return $data->sum($field);
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
        return 'Клиенты';
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $header = 'A1:D1';
                $cellRange = 'A1:D' . ($this->countEntries + $this->moreEntries);
                $callPlace = 'B' . ($this->countEntries + $this->moreEntries);
                $callKg = 'C' . ($this->countEntries + $this->moreEntries);

                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
//                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getCell($callPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getCell($callKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getStyle($callPlace)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($callKg)->getFont()->setBold(500);

                // Выделение брендовых клиетов жирным шрифтом
                $rows = $event->sheet->getDelegate()->toArray();
                foreach ($rows as $key => $value) {
                    if ($value[3] == 'Бренд' || $value[3] == 'Авиа') {
                        $event->sheet->getDelegate()->getStyle('A' . ($key + 1) . ':D' . ($key + 1))->getFont()->setBold(500);
                    }
                }
            }
        ];
    }
}
