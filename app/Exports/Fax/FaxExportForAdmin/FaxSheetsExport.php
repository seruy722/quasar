<?php

namespace App\Exports\Fax\FaxExportForAdmin;

use App\Exports\Fax\FaxCommonSheetExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxSheetsExport implements WithMultipleSheets
{
    use Exportable;

    protected $faxID;
    protected $ids;

    public function __construct($faxID, $ids)
    {
        $this->faxID = $faxID;
        $this->ids = $ids;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxCommonSheetExport($this->faxID, $this->ids);
        $sheets[] = new FaxMainSheetExport($this->faxID, $this->ids);
        $sheets[] = new FaxPriceSheetExport($this->faxID, $this->ids);
        $sheets[] = new FaxPostSheetExport($this->faxID, $this->ids);
        return $sheets;
    }
}
