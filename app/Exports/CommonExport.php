<?php

namespace App\Exports;

use App\Code;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Customer;

class CommonExport implements FromCollection
{
    protected $ids;
    protected $model;

    public function __construct(array $ids, $model)
    {
        $this->ids = $ids;
        $this->model = $model;
    }

    public function collection()
    {
        switch ($this->model) {
            case 'codes':
                return Code::whereIn('id', $this->ids)->get();
            case 'brands':
                return \App\Test::where('brand', true)->get();
            case 'transfers':
                if(empty($this->ids)){
                    return \App\Transfer::all();
                }
                return \App\Transfer::whereIn('id', $this->ids)->get();
        }
    }
}
