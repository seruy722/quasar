<?php

namespace App\Exports\Cargo;

use App\Cargo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class CargoGeneralDataExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.cargo.cargo-general-data-export', [
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
            ->orderBy('created_at', 'DESC');

        if ($this->data['type'] === 1 && $this->data['day']) {
            return $data->where('cargos.type', true)->whereDate('cargos.created_at', $this->data['day'])->take(500)->get();
        } else if ($this->data['type'] === 0 && $this->data['day']) {
            return $data->where('cargos.type', false)->whereDate('cargos.created_at', $this->data['day'])->take(500)->get();
        } else if ($this->data['type'] === -1 && $this->data['day']) {
            return $data->whereDate('cargos.created_at', $this->data['day'])->take(500)->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['from'] && $this->data['period']['to']) {
            return $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->take(500)->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['from'] && $this->data['period']['to']) {
            return $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->take(500)->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['from'] && $this->data['period']['to']) {
            return $data->whereDate('.cargos.created_at', '>=', $this->data['period']['from'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->take(500)->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['to']) {
            return $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->take(500)->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['to']) {
            return $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->take(500)->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['to']) {
            return $data->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->take(500)->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['from']) {
            return $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->take(500)->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['from']) {
            return $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->take(500)->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['from']) {
            return $data->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->take(500)->get();
        }

        return $data->take(500)->get();
    }

    public function title(): string
    {
        return 'КАРГО';
    }
}
