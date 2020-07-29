<?php

namespace App\Exports\Cargo;

use App\Cargo;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class CargoGeneralDataExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents, WithTitle
{
    protected $data;
    protected $enterData;
    protected $headers = ['Дата', 'Тип', 'Клиент', 'Мест', 'Вес', 'За кг', 'За место', 'Сумма', 'Скидки', 'Оплачен', 'Категория', 'Факс', 'Примечания'];

    public function __construct($enterData)
    {
        $this->enterData = $enterData;
    }

    public function getArray()
    {
        $data = Cargo::select(
            'cargos.*',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'delivery_methods.name as delivery_method_name',
            'faxes.name as fax_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'cargos.category_id')
            ->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'cargos.delivery_method_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id');
        $res = null;
        if ($this->enterData['type'] === 1 && $this->enterData['day']) {
            $res = $data->where('cargos.type', true)->whereDate('cargos.created_at', $this->enterData['day'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['day']) {
            $res = $data->where('cargos.type', false)->whereDate('cargos.created_at', $this->enterData['day'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['day']) {
            $res = $data->whereDate('cargos.created_at', $this->enterData['day'])->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->enterData['period']['from'])->whereDate('cargos.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->enterData['period']['from'])->whereDate('cargos.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $res = $data->whereDate('.cargos.created_at', '>=', $this->enterData['period']['from'])->whereDate('cargos.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['to']) {
            $res = $data->whereDate('cargos.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->enterData['period']['from'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->enterData['period']['from'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from']) {
            $res = $data->whereDate('cargos.created_at', '>=', $this->enterData['period']['from'])->get();
        } else if ($this->enterData['type'] === 1 || $this->enterData['type'] === 0) {
            $res = $data->where('cargos.type', $this->enterData['type'])->get();
        } else {
            $res = $data->get();
        }

        if ($this->data['type'] === 0 || $this->data['type'] === -1) {
            $codes = $res->map(function ($item) {
                return $item->code_client_id;
            });
            $codeRes = [];
            foreach ($codes as $key => $code) {
                $codeEntries = $res->where('code_client_id', $code)->where('type', false)->where('fax_id', '>', 0)->groupBy('fax_id', 'category_id');
                $remainder = $res->where('code_client_id', $code)->where('fax_id', 0);
                foreach ($codeEntries->toArray() as $item) {
                    $cur = current($item);
                    $cur['sum'] = array_sum(array_column($item, 'sum'));
                    $cur['place'] = array_sum(array_column($item, 'place'));
                    array_push($codeRes, $cur);
                }
                foreach ($remainder->toArray() as $item) {
                    array_push($codeRes, $item);
                }
            }
            usort($codeRes, function ($a, $b) {
                if ($a['code_client_name'] == $b['code_client_name']) {
                    return 0;
                }

                return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
            });
            $this->data = $codeRes;
            return $this->data;
        }

        $res2 = $res->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        })->map(function ($item) {
            return ['created_at' => Carbon::parse($item->created_at)->setTimezone($this->enterData['timeZone'])->format('d-m-Y'), 'type' => $item->type ? 'Оплата' : 'Долг', 'code_client_name' => $item->code_client_name, 'place' => $item->place, 'kg' => $item->place, 'for_kg' => $item->for_kg, 'for_place' => $item->for_place, 'sum' => $item->sum, 'sale' => $item->sale, 'paid' => $item->paid ? 'Да' : $item->type ? null : 'Нет', 'category_name' => $item->category_name, 'fax_name' => $item->fax_name, 'notation' => $item->notation];
        });
        $this->data = $res2->all();
        return $this->data;
    }

    public function array(): array
    {
        return $this->getArray();
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $header = 'A1:M1';
                $cellRange = 'A1:M' . (count($this->data) + 1);

                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
            }
        ];
    }

    public function title(): string
    {
        return 'КАРГО';
    }
}
