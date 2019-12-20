<?php

namespace App\Exports\Fax\FaxData;

use App\FaxData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use function foo\func;

class FaxExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents, WithHeadings
{

    protected $faxID;
    protected $data;
    protected $headers = ['Клиент', 'Мест', 'Вес', 'За кг', 'За место', 'Сумма'];
    protected $countEntries;
    protected $sumPlace = 0;
    protected $sumKg = 0;
    protected $sum = 0;
    protected $moreEntries = 2;
    protected $categories;
    protected $red = 'FF0000';
    protected $yellow = 'FFFF00';
    protected $green = '92D050';
    protected $brands;
    protected $data2;

    public function __construct($faxID)
    {
        $this->faxID = $faxID;

        $this->data = FaxData::select('codes.code')
            ->selectRaw('SUM(fax_data.place) as place')
            ->selectRaw('SUM(fax_data.kg) as kg')
            ->selectRaw('AVG(fax_data.for_kg)')
            ->selectRaw('AVG(fax_data.for_place)')
            ->selectRaw('SUM(fax_data.kg) * AVG(fax_data.for_kg) + AVG(fax_data.for_place) * SUM(fax_data.place)  as sum')
            ->leftJoin('codes', 'codes.id', '=', 'fax_data.code_id')
            ->where('fax_data.fax_id', $this->faxID)
            ->orderByRaw('CONVERT(codes.code, UNSIGNED INTEGER)')
            ->groupBy('code_id', 'category_id')
            ->get();

        $this->countEntries = $this->countEntries();
        $this->sumPlace = $this->countSum($this->data, 'place');
        $this->sumKg = $this->countSum($this->data, 'kg');
        $this->sum = $this->countSum($this->data, 'sum');

        $this->brands = FaxData::select('codes.code')
            ->selectRaw('SUM(fax_data.place) as place')
            ->selectRaw('SUM(fax_data.kg) as kg')
            ->leftJoin('codes', 'codes.id', '=', 'fax_data.code_id')
            ->where('fax_data.fax_id', $this->faxID)
            ->where('fax_data.brand', true)
            ->groupBy('code_id', 'category_id')
            ->get();

        $this->categories = FaxData::select('categories.name')
            ->selectRaw('SUM(fax_data.place) as place')
            ->selectRaw('SUM(fax_data.kg) as kg')
            ->leftJoin('categories', 'categories.id', '=', 'fax_data.category_id')
            ->where('fax_data.fax_id', $this->faxID)
            ->orderBy('kg', 'DESC')
            ->groupBy('category_id')
            ->get();

        $this->data2 = FaxData::select('codes.code')
            ->selectRaw('SUM(fax_data.place) as place')
            ->selectRaw('SUM(fax_data.kg) as kg')
            ->selectRaw('SUM(fax_data.kg) * AVG(fax_data.for_kg) + AVG(fax_data.for_place) * SUM(fax_data.place)  as sum')
            ->leftJoin('codes', 'codes.id', '=', 'fax_data.code_id')
            ->where('fax_data.fax_id', $this->faxID)
            ->orderByRaw('CONVERT(codes.code, UNSIGNED INTEGER)')
            ->groupBy('code_id', 'category_id')
            ->get();

        $this->data2->prepend(['Клиент', 'Мест', 'Вес', 'Сумма']);
        $this->data2->prepend(['name' => null, 'code' => null]);
        $this->data2->prepend(['name' => null, 'code' => null]);
        $this->data2->prepend(['name' => null, 'code' => null]);
    }

    public function countEntries()
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
                $header = 'A1:F1';
////                $footer = 'A' . ($this->data->count() + $this->moreEntries) . ':H' . ($this->countEntries + $this->moreEntries);
                $cellRange = 'A1:F' . ($this->countEntries + $this->moreEntries);
                $callPlace = 'B' . ($this->countEntries + $this->moreEntries);
                $callKg = 'C' . ($this->countEntries + $this->moreEntries);
                $callForKg = 'D2:D' . ($this->countEntries + $this->moreEntries);
                $callForPlace = 'E2:E' . ($this->countEntries + $this->moreEntries);
                $callBold = 'D2:F' . ($this->countEntries + $this->moreEntries);
                $cellSum = 'F' . ($this->countEntries + $this->moreEntries);
////
                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
////                $event->sheet->getDelegate()->getStyle($footerEntry)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getCell($callPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getCell($callKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getCell($cellSum)->setValue($this->sum);
                $event->sheet->getDelegate()->getStyle($cellSum)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($this->yellow);
                $event->sheet->getDelegate()->getStyle($callPlace)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($callKg)->getFont()->setBold(500);

                // FOR_KG
                $event->sheet->getDelegate()->getStyle($callForKg)->getFont()->setBold(500)->getColor()->applyFromArray(array('rgb' => $this->red));
                $event->sheet->getDelegate()->getStyle($callForPlace)->getFont()->setBold(500)->getColor()->applyFromArray(array('rgb' => $this->red));
                $event->sheet->getDelegate()->getStyle($callBold)->getFont()->setBold(500);
//
////                // CATEGORIES
                $categoriesCellRange = 'B' . ($this->countEntries + $this->moreEntries + 4) . ':E' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesKg = 'D' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesPlace = 'C' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesSum = 'E' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);
                $cellCategoriesFooter = 'A' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4) . ':E' . ($this->countEntries + $this->moreEntries + count($this->categories) + 4);


                $foo = $this->categories;
                $map = $foo->map(function ($item) use (&$event) {
                    $item['price'] = 2.5;
                    return $item;
                })->map(function ($elem) {
                    $elem->{'sum'} = $elem['kg'] * $elem['price'];
                    return $elem;
                });
                $map->prepend(['name' => null, 'code' => null]);
                $map->prepend(['name' => null, 'code' => null]);
                $map->prepend(['name' => null, 'code' => null]);

                $event->sheet->appendRows($map, $event);

                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getCell($cellCategoriesKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getCell($cellCategoriesPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getCell($cellCategoriesSum)->setValue($map->sum('sum'));
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellCategoriesFooter)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($categoriesCellRange)->getFont()->setSize(10);

                // DATA_2
                $event->sheet->appendRows($this->data2, $event);

                // Выделение брендовых клиетов жирным шрифтом
                if ($this->brands->isNotEmpty()) {
                    $rows = $event->sheet->getDelegate()->toArray();
                    foreach ($rows as $key => $value) {
                        $this->brands->each(function ($item) use (&$event, &$value, &$key) {
                            if ($value[0] == $item->code && $value[1] == $item->place && $value[2] == $item->kg) {
                                $event->sheet->getDelegate()->getStyle('A' . ($key + 1) . ':F' . ($key + 1))->getFont()->setBold(500);
                            }
                        });
                    }
                }
            }
        ];
    }
}
