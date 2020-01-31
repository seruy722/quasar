<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorehouseDataHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storehouse_data_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('action', 50);
            $table->unsignedInteger('storehouse_data_id');
            $table->string('code_place')->nullable();
            $table->unsignedInteger('code_client_id')->default(0);
            $table->unsignedInteger('place')->default(1);
            $table->float('kg', 10, 2)->unsigned()->default(0);
            $table->string('shop')->nullable();
            $table->json('things')->nullable();
            $table->boolean('brand')->default(false);
            $table->string('notation')->nullable();
            $table->float('for_kg', 10, 2)->default(0);
            $table->float('for_place', 10, 2)->default(0);
            $table->unsignedInteger('fax_id')->default(0);
            $table->unsignedInteger('category_id')->default(0);
            $table->unsignedInteger('storehouse_id')->default(1);
            $table->unsignedTinyInteger('status')->default(0);
            $table->boolean('destroyed')->default(false);
            $table->unsignedInteger('user_id');
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
        Schema::dropIfExists('storehouse_data_histories');
    }
}
