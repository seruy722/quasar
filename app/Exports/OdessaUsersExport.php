<?php

namespace App\Exports;

use App\Cargo;
use App\Code;
use App\Customer;
use App\Debt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class OdessaUsersExport implements FromView, ShouldAutoSize, WithTitle
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
        $odessaId = Customer::where('city_id', 380)->pluck('code_id')->unique()->toArray();
        $clientsIds = Code::whereIn('id', $odessaId)->get();

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
            }
        }
        return $arr;
    }

    public function title(): string
    {
        return 'Одесса';
    }
}
