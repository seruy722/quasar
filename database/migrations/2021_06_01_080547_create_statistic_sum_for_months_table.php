<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticSumForMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistic_sum_for_months', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date', 50)->nullable();
            $table->float('start_sum', 10, 2)->default(0);
            $table->float('end_sum', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistic_sum_for_months');
    }
}
