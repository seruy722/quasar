<?php

namespace App\Http\Controllers\Api;

use App\Expense;
use App\Statistics;
use App\StatisticSumForMonth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function query()
    {
        return Statistics::select('statistics.*', 'expenses.name as expense_name')
            ->leftJoin('expenses', 'expenses.id', '=', 'statistics.expense_id');
    }

    public function index()
    {
        return response(['statistics' => $this->query()->get(), 'month' => StatisticSumForMonth::all()]);
    }

    public function store(Request $request)
    {
        $expense = Expense::firstOrCreate(['name' => $request->expense]);

        $data = [
            'expense_id' => $expense->id,
            'sum' => $request->sum,
            'description'=> $request->description
        ];

        if ($request->date) {
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($request->date));
        }

        $entry = Statistics::create($data);
        $entry = $this->query()->where('statistics.id', $entry->id)->first();
        return response(['expenseData' => $entry, 'expense' => $expense->name]);
    }
}
