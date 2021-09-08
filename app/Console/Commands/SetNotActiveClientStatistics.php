<?php

namespace App\Console\Commands;

use App\Cargo;
use App\Code;
use App\CodesStatistics;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SetNotActiveClientStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:not:active:client:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get token for bot telegram and whatsapp';

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
        $codesIds = Code::pluck('id');
        $last_active_cargo = null;
        $index = 0;

        foreach ($codesIds as $id) {
            $cargoEntry = Cargo::where('type', false)->where('code_client_id', $id)->orderBy('created_at', 'desc')->first();
            if ($cargoEntry) {
                if ($cargoEntry->fax_id || !$cargoEntry->in_cargo) {
                    continue;
                }
                $cargoEntry = Cargo::where('type', false)->where('code_client_id', $id)->where('in_cargo', true)->orderBy('created_at', 'desc')->first();
                $data = [];
                if ($cargoEntry) {
                    $dt = Carbon::parse($cargoEntry->created_at);
                    if ($dt->diffInDays(Carbon::now()) >= 31) {
                        $last_active_cargo = str_replace('после', 'назад', Carbon::now()->locale('ru')->diffForHumans(Carbon::parse($cargoEntry->created_at)));
                        $index = $dt->diffInDays(Carbon::now());
                    }
                } else {
                    array_push($data, ['index' => 0]);
                }

                if (!empty($data)) {
                    $code = Code::where('id', $id)->with('customers')->first();
                    $saveData = [
                        'code_id' => $code->id,
                        'code_name' => $code->code,
                        'last_active_cargo' => $last_active_cargo,
                        'index' => $index,
                        'client_created_at' => $code->created_at
                    ];
                    foreach ($code->customers as $customer) {
                        $saveData['city_name'] = $customer['city_name'];
                        $saveData['client_name'] = $customer['name'];
                    }
                    CodesStatistics::truncate();
                    CodesStatistics::create($saveData);
                }
            }

        }
        return true;
    }
}
