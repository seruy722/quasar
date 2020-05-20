<?php

namespace App\Exports\Fax;

use App\Exports\Fax\FaxCommonSheetExport;
use App\Exports\Fax\FaxMainSheetExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxSheetsExport implements WithMultipleSheets
{
    use Exportable;

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxCommonSheetExport($this->id);
        $sheets[] = new FaxMainSheetExport($this->id);
        return $sheets;
    }
}
