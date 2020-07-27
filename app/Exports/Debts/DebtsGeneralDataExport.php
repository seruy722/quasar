<?php

namespace App\Exports\Debts;

use App\Debt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class DebtsGeneralDataExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;
    protected $query;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.debts.debts-general-data-export', [
            'collection' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $data = Debt::select(
            'debts.*',
            'codes.code as code_client_name',
            'users.name as user_name'

        )
            ->leftJoin('codes', 'codes.id', '=', 'debts.code_client_id')
            ->leftJoin('users', 'users.id', '=', 'debts.user_id');

        if ($this->data['type'] === 1 && $this->data['day']) {
            $this->query = $data->where('debts.type', true)->whereDate('debts.created_at', $this->data['day'])->get();
        } else if ($this->data['type'] === 0 && $this->data['day']) {
            $this->query = $data->where('debts.type', false)->whereDate('debts.created_at', $this->data['day'])->get();
        } else if ($this->data['type'] === -1 && $this->data['day']) {
            $this->query = $data->whereDate('debts.created_at', $this->data['day'])->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['from'] && $this->data['period']['to']) {
            $this->query = $data->where('debts.type', $this->data['type'])->whereDate('debts.created_at', '>=', $this->data['period']['from'])->whereDate('debts.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['from'] && $this->data['period']['to']) {
            $this->query = $data->where('debts.type', $this->data['type'])->whereDate('debts.created_at', '>=', $this->data['period']['from'])->whereDate('debts.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['from'] && $this->data['period']['to']) {
            $this->query = $data->whereDate('.debts.created_at', '>=', $this->data['period']['from'])->whereDate('debts.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 1 && $this->data['period']['to']) {
            $this->query = $data->where('debts.type', $this->data['type'])->whereDate('debts.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['to']) {
            $this->query = $data->where('debts.type', $this->data['type'])->whereDate('debts.created_at', '<=', $this->data['period']['to'])->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['to']) {
            $this->query = $data->whereDate('debts.created_at', '<=', $this->data['period']['to']);
        } else if ($this->data['type'] === 1 && $this->data['period']['from']) {
            $this->query = $data->where('debts.type', $this->data['type'])->whereDate('debts.created_at', '>=', $this->data['period']['from'])->get();
        } else if ($this->data['type'] === 0 && $this->data['period']['from']) {
            $this->query = $data->where('debts.type', $this->data['type'])->whereDate('debts.created_at', '>=', $this->data['period']['from'])->get();
        } else if ($this->data['type'] === -1 && $this->data['period']['from']) {
            $this->query = $data->whereDate('debts.created_at', '>=', $this->data['period']['from'])->get();
        } else if ($this->data['type'] === 1 || $this->data['type'] === 0) {
            $this->query = $data->where('debts.type', $this->data['type'])->get();
        } else {
            $this->query = $data->get();
        }

        return $this->query->sort(function ($a, $b) {
            if ($a['code_client_name'] == $b['code_client_name']) {
                return 0;
            }

            return ($a['code_client_name'] < $b['code_client_name']) ? -1 : 1;
        });
    }

    public function title(): string
    {
        return 'ДОЛГИ';
    }
}
