<?php

namespace App\Exports\Fax\FaxData;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaxDataExport implements WithMultipleSheets
{
    use Exportable;

    protected $faxID;
    protected $transporterID;

    public function __construct($faxID, $transporterID)
    {
        $this->faxID = $faxID;
        $this->transporterID = $transporterID;
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new FaxDataCommonExport($this->faxID);
        $sheets[] = new FaxExport($this->faxID);
//        $sheets[] = new PriceSheet($this->data, $this->headers);
        return $sheets;
    }
}
