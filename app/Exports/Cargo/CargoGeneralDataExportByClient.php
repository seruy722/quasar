<?php

namespace App\Exports\Cargo;

use App\Cargo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CargoGeneralDataExportByClient implements FromView, ShouldAutoSize
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function view(): View
    {
        return view('exports.cargo.cargo-detail-data-export', [
            'collection' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $data = Cargo::select(
            'cargos.*',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'delivery_methods.name as delivery_method_name',
            'faxes.name as fax_name'
        )
            ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'cargos.category_id')
            ->leftJoin('delivery_methods', 'delivery_methods.id', '=', 'cargos.delivery_method_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id')
            ->orderBy('cargos.created_at', 'DESC')
            ->whereIn('cargos.id', $this->ids)->get();

        return $data;
    }

    public function title(): string
    {
        return 'КАРГО';
    }
}
