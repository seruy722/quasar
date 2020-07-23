<?php

namespace App\Exports\Debts;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class DebtsExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.debts.debts-data-export', [
            'collection' => $this->data
        ]);
    }

    public function title(): string
    {
        $sheetName = current($this->data);
        return $sheetName['code_client_name'];
    }
}
