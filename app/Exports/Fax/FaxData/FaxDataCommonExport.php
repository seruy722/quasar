<?php

namespace App\Exports\Fax\FaxData;

use App\FaxData;
use App\Http\Resources\FaxDataCommonExportResource;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FaxDataCommonExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents, WithHeadings
{
    protected $faxID;
    protected $headers = ['Код', 'Клиент', 'Мест', 'Вес', 'Магазин', 'Категория', 'Город/клиента', 'Опись вложения'];
    protected $data;
    protected $countEntries;
    protected $sumPlace;
    protected $sumKg;
    protected $moreEntries = 2;
    protected $categories;
    protected $brands;

    public function __construct($faxID)
    {
        $this->faxID = $faxID;

        $data = FaxDataCommonExportResource::collection(
            FaxData::select('fax_data.code', 'fax_data.place', 'fax_data.kg',
                'fax_data.shop', 'fax_data.things', 'fax_data.category_id', 'fax_data.notation')
                ->selectRaw('codes.code as codesCode')
                ->where('fax_data.fax_id', $this->faxID)
                ->join('codes', 'codes.id', '=', 'fax_data.code_id')
                ->orderByRaw('CONVERT(codes.code, UNSIGNED INTEGER)')
                ->get()
        );
        $this->data = $data->collection;

        $this->countEntries = $this->countEntriesFunc();
        $this->sumPlace = $this->countSum($this->data, 'place');
        $this->sumKg = $this->countSum($this->data, 'kg');

        $this->categories = FaxData::select('categories.name')
            ->selectRaw('SUM(fax_data.place) as place')
            ->selectRaw('SUM(fax_data.kg) as kg')
            ->leftJoin('categories', 'categories.id', '=', 'fax_data.category_id')
            ->where('fax_data.fax_id', $this->faxID)
            ->orderBy('kg', 'DESC')
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
//                $footer = 'A' . ($this->data->count() + $this->moreEntries) . ':H' . ($this->countEntries + $this->moreEntries);
                $cellRange = 'A1:H' . ($this->countEntries + $this->moreEntries);
                $callPlace = 'C' . ($this->countEntries + $this->moreEntries);
                $callKg = 'D' . ($this->countEntries + $this->moreEntries);
//
                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
//                $event->sheet->getDelegate()->getStyle($footerEntry)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getCell($callPlace)->setValue($this->sumPlace);
                $event->sheet->getDelegate()->getCell($callKg)->setValue($this->sumKg);
                $event->sheet->getDelegate()->getStyle($callPlace)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($callKg)->getFont()->setBold(500);

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
                    if ($value[5] == 'Бренд') {
                        $event->sheet->getDelegate()->getStyle('A' . ($key + 1) . ':W' . ($key + 1))->getFont()->setBold(500);
                    }
                }
            }
        ];
    }
}
