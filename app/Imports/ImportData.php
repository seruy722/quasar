<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportData implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }
}
