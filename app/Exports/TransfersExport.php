<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class TransfersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    protected $data;
    protected $headers = ['Деберц', 'Полч', 'Тел полч', 'Очки', 'Мет', 'Прим', 'Стс', 'Доб', 'Выд', 'Пол'];
    protected $transferStatus = [1 => 'Вопрос', 2 => 'Не выдан', 3 => 'Выдано', 4 => 'Отменен', 5 => 'Возврат'];
    protected $transferMethod = [1 => 'Деньги', 2 => 'Товар деньги'];

    public function __construct(array $ids)
    {
        if (empty($ids)) {
            $this->data = \App\Transfer::select('codes.code as client_name', 'transfers.receiver_name', 'transfers.receiver_phone', 'transfers.sum', 'transfers.method', 'transfers.notation', 'transfers.status', 'transfers.created_at as dates', 'transfers.issued_by as issued', 'users.name as user_name')
                ->join('codes', 'transfers.client_id', '=', 'codes.id')
                ->join('users', 'transfers.user_id', '=', 'users.id')
                ->orderBy('transfers.id', 'DESC')
                ->get();
        } else {
            $this->data = \App\Transfer::select('codes.code as client_name', 'transfers.receiver_name', 'transfers.receiver_phone', 'transfers.sum', 'transfers.method', 'transfers.notation', 'transfers.status', 'transfers.created_at as dates', 'transfers.issued_by as issued', 'users.name as user_name')
                ->join('codes', 'transfers.client_id', '=', 'codes.id')
                ->join('users', 'transfers.user_id', '=', 'users.id')
                ->orderBy('transfers.id', 'DESC')
                ->whereIn('transfers.id', $ids)
                ->get();
        }
        $this->data->map(function ($item) {
            if (is_numeric($item['client_name'])) {
                $item['client_name'] = '007/' . $item['client_name'];
            }
            $item['method'] = $this->transferMethod[$item['method']];
            $item['status'] = $this->transferStatus[$item['status']];
            if ($item['issued']) {
                $item['issued'] = \Illuminate\Support\Carbon::parse($item['issued'])->format('d-m-Y');
            }
            $item['dates'] = \Illuminate\Support\Carbon::parse($item['dates'])->format('d-m-Y');
            return $item;
        });

    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $header = 'A1:J1';
                $cellRange = 'A1:J' . ($this->data->count() + 1);

                $event->sheet->getDelegate()->getStyle($header)->getFont()->setBold(500);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER));
                $event->sheet->getDelegate()->getStyle($cellRange)->getBorders()->getAllBorders()->applyFromArray(array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN));
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
            }
        ];
    }
}
