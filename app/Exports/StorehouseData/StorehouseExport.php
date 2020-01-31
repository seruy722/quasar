<?php

namespace App\Exports\StorehouseData;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StorehouseExport implements WithMultipleSheets
{
    use Exportable;

    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new StorehouseDataCommonExport($this->ids);
        $sheets[] = new StorehouseDataExport();
        return $sheets;
    }
}
