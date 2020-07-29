<?php

namespace App\Exports\Cargo;

use App\Cargo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class CargoGeneralDataExport1 implements FromView, ShouldAutoSize, WithTitle
{
    // НЕ ИСПОЛЬЗУЕТСЯ
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
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id');
            $res = null;
        if ($this->data['type'] === 1 && $this->data['day']) {
            $res = $data->where('cargos.type', true)->whereDate('cargos.created_at', $this->data['day'])->get();
        } else if ($this->data['type'] === 0 && $this->data['day']) {
            $res = $data->where('cargos.type', false)->whereDate('cargos.created_at', $this->data['day'])->get();
        } else if ($this->data['type'] === -1 && $this->data['day']) {
            $res = $data->whereDate('cargos.created_at', $this->data['day'])->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['from'] && $this->data['period']['to']) {
            $res = $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['from'] && $this->data['period']['to']) {
            $res = $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['from'] && $this->data['period']['to']) {
            $res = $data->whereDate('.cargos.created_at', '>=', $this->data['period']['from'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['to']) {
            $res = $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['to']) {
            $res = $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['to']) {
            $res = $data->whereDate('cargos.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['from']) {
            $res = $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['from']) {
            $res = $data->where('cargos.type', $this->data['type'])->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['from']) {
            $res = $data->whereDate('cargos.created_at', '>=', $this->data['period']['from'])->get();
        } else if ($this->data['type'] === 1 || $this->data['type'] === 0) {
            $res = $data->where('cargos.type', $this->data['type'])->get();
        } else {
            $res = $data->get();
        }

//        if ($this->data['type'] === 0 || $this->data['type'] === -1) {
//            $codes = $res->map(function ($item) {
//                return $item->code_client_id;
//            });
//            $codeRes = [];
//            foreach ($codes as $key => $code) {
//                $codeEntries = $res->where('code_client_id', $code)->where('type', false)->where('fax_id', '>', 0)->groupBy('fax_id', 'category_id');
//                $remainder = $res->where('code_client_id', $code)->where('fax_id', 0);
//                foreach ($codeEntries->toArray() as $item) {
//                    $cur = current($item);
//                    $cur['sum'] = array_sum(array_column($item, 'sum'));
//                    $cur['place'] = array_sum(array_column($item, 'place'));
//                    array_push($codeRes, $cur);
//                }
//                foreach ($remainder->toArray() as $item) {
//                    array_push($codeRes, $item);
//                }
//            }
//            usort($codeRes, function ($a, $b) {
//                if ($a['code_client_name'] == $b['code_client_name']) {
//                    return 0;
//                }
//
//                return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
//            });
//            return collect($codeRes);
//        }

        return $res->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        });
    }

    public function title(): string
    {
        return 'КАРГО';
    }
}
