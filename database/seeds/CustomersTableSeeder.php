<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Customer;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('customers')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $randCodes = range(1001, 2000);
        shuffle($randCodes);
        $randID = range(1, 1000);
        shuffle($randID);
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 999; $i++) {
            Customer::create([
                'name' => $faker->name,
                'email' => $faker->email . $i,
                'phone' => $faker->phoneNumber,
                'city' => $faker->city,
                'sex' => 1,
                'code_id' => $i + 1,
                'user_id' => $randCodes[$i],
            ]);
        }

        for ($i = 0; $i < 999; $i++) {
            Customer::create([
                'name' => $faker->name,
                'email' => $faker->email . $i,
                'phone' => $faker->phoneNumber,
                'city' => $faker->city,
                'sex' => 1,
                'code_id' => $i + 1,
                'user_id' => $randCodes[$i],
            ]);
        }
    }
}
