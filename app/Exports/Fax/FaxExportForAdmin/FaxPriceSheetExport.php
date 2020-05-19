<?php

namespace App\Exports\Fax\FaxExportForAdmin;

use App\StorehouseData;
use App\TransporterFaxesPrice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FaxPriceSheetExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents, WithHeadings
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
    protected $categoriesPrice;
    protected $red = 'FF0000';
    protected $yellow = 'FFFF00';
    protected $green = '92D050';
    protected $brands;
    protected $data2;

    public function __construct($faxID)
    {
        $this->faxID = $faxID;

        $this->data = StorehouseData::select('codes.code as code')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->selectRaw('AVG(storehouse_data.for_kg)')
            ->selectRaw('AVG(storehouse_data.for_place)')
            ->selectRaw('SUM(storehouse_data.kg) * AVG(storehouse_data.for_kg) + AVG(storehouse_data.for_place) * SUM(storehouse_data.place)  as sum')
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->where('storehouse_data.fax_id', $this->faxID)
            ->orderByRaw('CONVERT(codes.code, UNSIGNED INTEGER)')
            ->groupBy('code_client_id', 'category_id')
            ->get();

        $this->data = $this->data->map(function ($item) {
            $item['sum'] = round($item['sum']);
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
        $this->sum = $this->countSum($this->data, 'sum');

        $this->brands = StorehouseData::select('codes.code')
            ->selectRaw('SUM(storehouse_data.place) as place')
            ->selectRaw('SUM(storehouse_data.kg) as kg')
            ->leftJoin('codes', 'codes.id', '=', 'storehouse_data.code_client_id')
            ->where('storehouse_data.fax_id', $this->faxID)
            ->where('storehouse_data.brand', true)
            ->groupBy('code_client_id', 'category_id')
            ->get();
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
        return 'Цены';
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
                $cellRange = 'A1:F' . ($this->countEntries + $this->moreEntries);
                $callPlace = 'B' . ($this->countEntries + $this->moreEntries);
                $callKg = 'C' . ($this->countEntries + $this->moreEntries);
                $callForKg = 'D2:D' . ($this->countEntries + $this->moreEntries);
                $callForPlace = 'E2:E' . ($this->countEntries + $this->moreEntries);
                $callBold = 'D2:F' . ($this->countEntries + $this->moreEntries);
                $cellSum = 'F' . ($this->countEntries + $this->moreEntries);
                $cellSumCategories = 'F' . ($this->countEntries + $this->moreEntries + 2);
                $cellSumDifference = 'G' . ($this->countEntries + $this->moreEntries + 1);
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
