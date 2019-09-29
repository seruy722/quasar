<?php

use Illuminate\Database\Seeder;
use App\Code;
use Illuminate\Support\Facades\DB;

class CodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('codes')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $randCodes = range(1001, 2000);
        shuffle($randCodes);
        $randID = range(1, 1000);
        shuffle($randID);

        for ($i = 0; $i < 999; $i++) {
            Code::create([
                'user_id' => $randID[$i],
                'code' => $randCodes[$i],
            ]);
        }
    }
}
