<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;


class GeneralDataByClientsExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $model;

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
        $clientsIds = DB::table('codes')->get();

        if ($this->model === 'cargo') {
            foreach ($clientsIds as $item) {
                $sum = DB::table('cargos')
                    ->select(
                        'cargos.*',
                        'codes.code as code_client_name'
                    )
                    ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
                    ->where('code_client_id', $item->id)->sum('sum');
                if ($sum) {
                    array_push($arr, ['code_client_name' => $item->code, 'sum' => $sum]);
                }
            }
        } else if ($this->model === 'debts') {
            foreach ($clientsIds as $item) {
                $sum = DB::table('debts')
                    ->select(
                        'debts.*',
                        'codes.code as code_client_name'

                    )
                    ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id')
                    ->where('code_client_id', $item->id)->sum('sum');
                if ($sum) {
                    array_push($arr, ['code_client_name' => $item->code, 'sum' => $sum]);
                }
            }
        } else if ($this->model === 'commission') {
            foreach ($clientsIds as $item) {
                $sum = DB::table('debts')
                    ->select(
                        'debts.*',
                        'codes.code as code_client_name'

                    )
                    ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id')
                    ->where('code_client_id', $item->id)->sum('commission');
                if (round($sum)) {
                    array_push($arr, ['code_client_name' => $item->code, 'sum' => round($sum)]);
                }
            }
        }
        return $arr;
    }

    public function title(): string
    {
        return 'ДОЛГИ';
    }
}
