<?php

namespace App\Exports\Debts;

use App\Code;
use App\Customer;
use App\Debt;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class DebtsGeneralDataExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents, WithTitle
{
    protected $countData;
    protected $enterData;
    protected $cityId;
    protected $headers = ['Дата', 'Тип', 'Деберц', 'Очки', 'Ком', 'Прим', 'Пол'];

    public function __construct($data, $cityId)
    {
        $this->enterData = $data;
        $this->cityId = $cityId;
    }

    public function getArray()
    {
        $data = Debt::select(
            'debts.*',
            'codes.code as code_client_name',
            'users.name as user_name'

        )
            ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id')
            ->leftJoin('users', 'users.id', '=', 'debts.user_id');

        if ($this->cityId) {
            $codeIds = Customer::where('city_id', $this->cityId)->pluck('code_id')->unique()->toArray();
            $data = $data->whereIn('debts.code_client_id', $codeIds);
        }

        $query = null;
        if ($this->enterData['type'] === 1 && $this->enterData['day']) {
            $query = $data->where('debts.type', true)->whereDate('debts.created_at', $this->getDateWithTimeZone($this->enterData['day'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['day']) {
            $query = $data->where('debts.type', false)->whereDate('debts.created_at', $this->getDateWithTimeZone($this->enterData['day'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['day']) {
            $query = $data->whereDate('debts.created_at', $this->getDateWithTimeZone($this->enterData['day'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->whereDate('debts.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->whereDate('debts.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $query = $data->whereDate('.debts.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->whereDate('debts.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['to']) {
            $query = $data->whereDate('debts.created_at', '<=', $this->getDateWithTimeZone($this->enterData['period']['to'], 'Y-m-d'));
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from']) {
            $query = $data->whereDate('debts.created_at', '>=', $this->getDateWithTimeZone($this->enterData['period']['from'], 'Y-m-d'))->get();
        } else if ($this->enterData['type'] === 1 || $this->enterData['type'] === 0) {
            $query = $data->where('debts.type', $this->enterData['type'])->get();
        } else if ($this->enterData['type'] === 1 || $this->enterData['type'] === 0) {
            $query = $data->where('debts.type', $this->enterData['type'])->get();
        } else {
            $query = $data->get();
        }

        $this->countData = count($query);

        return $query->map(function ($item) {
            return ['created_at' => $this->getDateWithTimeZone($item->created_at, 'd-m-Y'), 'type' => $item->type ? 'Оплата' : 'Долг', 'code_client_name' => $item->code_client_name, 'sum' => $item->sum, 'commission' => $item->commission, 'notation' => $item->notation, 'user_name' => $item->user_name];
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
                $header = 'A1:G1';
                $cellRange = 'A1:G' . ($this->countData + 1);

                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
            }
        ];
    }

    public function title(): string
    {
        return 'ДОЛГИ';
    }
}
