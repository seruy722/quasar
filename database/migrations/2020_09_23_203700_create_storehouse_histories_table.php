<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorehouseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storehouse_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('storehouse_entry_id');
            $table->string('code_place', 11);
            $table->unsignedInteger('code_client_id');
            $table->float('place', 10, 2)->default(1);
            $table->float('kg', 10, 2);
            $table->unsignedInteger('category_id');
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
        Schema::dropIfExists('storehouse_histories');
    }
}
