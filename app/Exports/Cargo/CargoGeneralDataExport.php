<?php

namespace App\Exports\Cargo;

use App\Cargo;
use App\Customer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CargoGeneralDataExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents
{
    protected $countData;
    protected $enterData;
    protected $cityId;
    protected $headers = ['Дата', 'Тип', 'Деберц', 'М', 'В', 'За к', 'За м', 'Очки', 'Скд', 'Опл', 'Кат', 'Фак', 'Прим', 'Пол'];

    public function __construct($enterData, $cityId)
    {
        $this->enterData = $enterData;
        $this->cityId = $cityId;
    }

    public function getArray()
    {
        $data = Cargo::select(
            'cargos.*',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'delivery_methods.name as delivery_method_name',
            'users.name as user_name',
            'faxes.name as fax_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'cargos.category_id')
            ->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'cargos.delivery_method_id')
            ->leftJoin('users', 'users.id', '=', 'cargos.get_pay_user_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id');


        if ($this->cityId) {
            $codeIds = Customer::where('city_id', $this->cityId)->pluck('code_id')->unique()->toArray();
            $data = $data->whereIn('cargos.code_client_id', $codeIds);
        }

        $codes = $data->pluck('code_client_id')->unique()->toArray();
        $newCodes = [];
        foreach ($codes as $code) {
            $countSum = Cargo::selectRaw('SUM(sum) as summa')->where('code_client_id', $code)->first();
            if ($countSum->summa != 0) {
                array_push($newCodes, $code);
            }
        }
        $res = null;
        if ($this->enterData['type'] === 1 && $this->enterData['day']) {
            $res = $data->where('cargos.type', true)->whereDate('cargos.created_at', $this->getDateWithTimeZone($this->enterData['day'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['day']) {
            $res = $data->where('cargos.type', false)->whereDate('cargos.created_at', $this->getDateWithTimeZone($this->enterData['day'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['day']) {
            $res = $data->whereDate('cargos.created_at', $this->getDateWithTimeZone($this->enterData['day'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->whereDate('cargos.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->whereDate('cargos.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $res = $data->whereDate('.cargos.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->whereDate('cargos.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['to']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['to']) {
            $res = $data->whereDate('cargos.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from']) {
            $res = $data->where('cargos.type', $this->enterData['type'])->whereDate('cargos.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from']) {
            $res = $data->whereDate('cargos.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 || $this->enterData['type'] === 0) {
            $res = $data->where('cargos.type', $this->enterData['type'])->get();
        } else {
            $res = $data->whereIn('cargos.code_client_id', $newCodes)->get();
        }

        $this->countData = count($res);
        return $res->map(function ($item) {
            return ['created_at' => $this->getDateWithTimeZone($item->created_at, 'd-m-Y'), 'type' => $item->type ? 'Оплата' : 'Долг', 'code_client_name' => $item->code_client_name, 'place' => $item->place, 'kg' => $item->kg, 'for_kg' => $item->for_kg, 'for_place' => $item->for_place, 'sum' => $item->sum, 'sale' => $item->sale, 'paid' => $item->paid ? 'Да' : 'Нет', 'category_name' => $item->category_name, 'fax_name' => $item->fax_name, 'notation' => $item->notation, 'user_name' => $item->user_name];
        })->all();
    }

    public function array(): array
    {
        return $this->getArray();
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function getDateWithTimeZone($date, $format)
    {
        return Carbon::parse($date)->setTimezone($this->enterData['timeZone'])->format($format);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $header = 'A1:N1';
                $cellRange = 'A1:N' . ($this->countData + 1);

                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
            }
        ];
    }
}
