<?php

namespace App\Exports\Cargo;

use App\Customer;
use App\Cargo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExportReportOdessaData implements FromView, WithTitle, ShouldAutoSize
{
    protected $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    public function view(): View
    {
        return view('exports.cargo.report-odessa-data-export', [
            'collection' => $this->getCollection()
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        $faxEntries = Cargo::where('type', false)->where('fax_id', '>', 0)->whereMonth('created_at', substr($this->date, 0, 2))->whereYear('created_at', substr($this->date, -4, 4))->get();
        $codeClientId = $faxEntries->map(function ($item) {
            return $item->code_client_id;
        })->unique()->values()->all();
        $customers = Customer::select(
            'customers.*', 'cities.name AS city_name',
            'codes.code as code',
            'codes.id as code_client_id'
        )
            ->leftJoin('cities', 'cities.id', '=', 'customers.city_id')
            ->leftJoin('codes', 'codes.id', '=', 'customers.code_id')
            ->whereIn('customers.code_id', $codeClientId)
            ->get();
        $ids = [];
        foreach ($faxEntries as $elem) {
            $customer = $customers->firstWhere('code_client_id', $elem->code_client_id);
            if ($customer && $customer->city_name !== 'Харьков') {
                array_push($ids, $elem->id);
            }
        }
        $selected = Cargo::select(
            'cargos.*',
            'codes.code as code_client_name',
            'categories.name as category_name',
            'faxes.name as fax_name',
            'faxes.transport_id as transport_id'
        )
            ->leftJoin('codes', 'codes.id', '=', 'cargos.code_client_id')
            ->leftJoin('categories', 'categories.id', '=', 'cargos.category_id')
            ->leftJoin('faxes', 'faxes.id', '=', 'cargos.fax_id')
            ->whereIn('cargos.id', $ids)
            ->get();

        return $selected->filter(function ($item) {
            return $item->transport_id !== 2;
        });
    }

    public function title(): string
    {
        return $this->date;
    }
}
