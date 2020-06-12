<?php

namespace App\Exports\Fax;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxSheetsExport implements WithMultipleSheets
{
    use Exportable;

    protected $id;
    protected $ids;

    public function __construct($id, $ids)
    {
        $this->id = $id;
        $this->ids = $ids;
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxCommonSheetExport($this->id, $this->ids);
        $sheets[] = new FaxMainSheetExport($this->id, $this->ids);
        return $sheets;
    }
}
