<?php

namespace App\Exports\Fax;

use App\Exports\Fax\FaxMainSheetExport;
use App\Exports\Fax\FaxExportForAdmin\FaxPriceSheetExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxSheetsCommonExport implements WithMultipleSheets
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
        $sheets[] = new FaxPriceSheetExport($this->id, $this->ids);
        $sheets[] = new FaxPostSheetModerExport($this->id, $this->ids);
        return $sheets;
    }
}
