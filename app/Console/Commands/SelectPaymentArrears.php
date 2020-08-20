<?php

namespace App\Console\Commands;

use App\Cargo;
use App\Debt;
use App\PaymentArrear;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SelectPaymentArrears extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'select:payment:arrears';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'select payment arrears';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        PaymentArrear::truncate();
        $cargoIds = [];
        $debtsIds = [];
        $cargo = Cargo::where('paid', false)->whereDate('created_at', '>=', '2020-08-01')->get();
        $debts = Debt::where('paid', false)->whereDate('created_at', '>=', '2020-08-01')->get();
        foreach ($cargo as $item) {
            $dt = Carbon::parse($item->created_at);
            if ($dt->diffInDays($dt->copy()->addWeek()) >= 7) {
                array_push($cargoIds, $item);
            }
        }
        foreach ($debts as $item) {
            $dt = Carbon::parse($item->created_at);
            if ($dt->diffInDays($dt->copy()->addWeek()) >= 7) {
                array_push($debtsIds, $item);
            }
        }
        if (!empty($cargoIds)) {
            PaymentArrear::create(['table_name' => (new Cargo)->getTable(), 'entries_id' => json_encode($cargoIds->pluck('id'))]);
        }
        if (!empty($debtsIds)) {
            PaymentArrear::create(['table_name' => (new Debt)->getTable(), 'entries_id' => json_encode($debtsIds->pluck('id'))]);
        }
        return 'Успешно';
    }
}
