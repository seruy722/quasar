<?php

namespace App\Exports\Fax\FaxExportForAdmin;

use App\Exports\Fax\FaxCommonSheetExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxSheetsExport implements WithMultipleSheets
{
    use Exportable;

    protected $faxID;
    protected $transporterID;

    public function __construct($faxID)
    {
        $this->faxID = $faxID;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxCommonSheetExport($this->faxID);
        $sheets[] = new FaxMainSheetExport($this->faxID);
        $sheets[] = new FaxPriceSheetExport($this->faxID);
        return $sheets;
    }
}
