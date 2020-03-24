<?php

namespace App\Exports\Fax\FaxData;

use App\Exports\Fax\FaxExportForModers\FaxCommonSheetExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxDataExport implements WithMultipleSheets
{
    use Exportable;

    protected $faxID;

    public function __construct($faxID)
    {
        $this->faxID = $faxID;
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxCommonSheetExport($this->faxID);
        $sheets[] = new FaxExport($this->faxID);
//        $sheets[] = new PriceSheet($this->data, $this->headers);
        return $sheets;
    }
}
