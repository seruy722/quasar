<?php

namespace App\Exports;

use App\Cargo;
use App\Code;
use App\Debt;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;


class GeneralDataByClientsExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $model;
    protected $query;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    public function view(): View
    {
        return view('exports.general-data-by-clients-export', [
            'collection' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $arr = [];
        $clientsIds = Code::get();

        foreach ($clientsIds as $item) {
            if ($this->model === 'cargo') {
                $this->query = Cargo::select(
                    'cargos.*',
                    'codes.code as code_client_name'
                )
                    ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id');
                $col = $this->query->where('code_client_id', $item->id)->get();
                $sum = $col->sum('sum');
                if ($sum) {
                    array_push($arr, ['code_client_name' => $item->code, 'sum' => $sum]);
                }
            } else if ($this->model === 'debts') {
                $this->query = Debt::select(
                    'debts.*',
                    'codes.code as code_client_name'

                )
                    ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id');
                $col = $this->query->where('code_client_id', $item->id)->get();
                $sum = $col->sum('sum');
                if ($sum) {
                    array_push($arr, ['code_client_name' => $item->code, 'sum' => $sum]);
                }
            } else if ($this->model === 'commission') {
                $this->query = Debt::select(
                    'debts.*',
                    'codes.code as code_client_name'

                )
                    ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id');
                $col = $this->query->where('code_client_id', $item->id)->get();
                $sumComm = $col->sum('commission');
                if ($sumComm) {
                    array_push($arr, ['code_client_name' => $item->code, 'sum' => round($sumComm)]);
                }
            }
        }

        usort($arr, function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        });
        return $arr;
    }

    public function title(): string
    {
        return 'ДОЛГИ';
    }
}
