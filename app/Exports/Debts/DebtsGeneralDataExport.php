<?php

namespace App\Exports\Debts;

use App\Debt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class DebtsGeneralDataExport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents, WithTitle
{
    protected $data;
    protected $enterData;
    protected $headers = ['Дата', 'Тип', 'Клиент', 'Сумма', 'Комиссия', 'Оплачен', 'Примечания', 'Пользователь'];

    public function __construct($data)
    {
        $this->enterData = $data;
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
        $query = null;
        if ($this->enterData['type'] === 1 && $this->enterData['day']) {
            $query = $data->where('debts.type', true)->whereDate('debts.created_at', $this->enterData['day'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['day']) {
            $query = $data->where('debts.type', false)->whereDate('debts.created_at', $this->enterData['day'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['day']) {
            $query = $data->whereDate('debts.created_at', $this->enterData['day'])->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->enterData['period']['from'])->whereDate('debts.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->enterData['period']['from'])->whereDate('debts.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from'] && $this->enterData['period']['to']) {
            $query = $data->whereDate('.debts.created_at', '>=', $this->enterData['period']['from'])->whereDate('debts.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['to']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '<=', $this->enterData['period']['to'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['to']) {
            $query = $data->whereDate('debts.created_at', '<=', $this->enterData['period']['to']);
        } else if ($this->enterData['type'] === 1 && $this->enterData['period']['from']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->enterData['period']['from'])->get();
        } else if ($this->enterData['type'] === 0 && $this->enterData['period']['from']) {
            $query = $data->where('debts.type', $this->enterData['type'])->whereDate('debts.created_at', '>=', $this->enterData['period']['from'])->get();
        } else if ($this->enterData['type'] === -1 && $this->enterData['period']['from']) {
            $query = $data->whereDate('debts.created_at', '>=', $this->enterData['period']['from'])->get();
        } else if ($this->enterData['type'] === 1 || $this->enterData['type'] === 0) {
            $query = $data->where('debts.type', $this->enterData['type'])->get();
        } else if ($this->enterData['type'] === 1 || $this->enterData['type'] === 0) {
            $query = $data->where('debts.type', $this->enterData['type'])->get();
        } else {
            $query = $data->get();
        }

        $res2 = $query->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        })->map(function ($item) {
            return ['created_at' => Carbon::parse($item->created_at)->setTimezone($this->enterData['timeZone'])->format('d-m-Y'), 'type' => $item->type ? 'Оплата' : 'Долг', 'code_client_name' => $item->code_client_name, 'sum' => $item->sum, 'commission' => $item->commission, 'paid' => $item->paid ? 'Да' : $item->type ? null : 'Нет', 'notation' => $item->notation, 'user_name' => $item->user_name];
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
                $header = 'A1:H1';
                $cellRange = 'A1:H' . (count($this->data) + 1);

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
