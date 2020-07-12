<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_place', 11)->nullable();
            $table->boolean('type')->default(false);
            $table->unsignedInteger('code_client_id');
            $table->unsignedInteger('place')->default(1);
            $table->float('kg', 10, 2)->unsigned()->default(0);
            $table->string('shop', 100)->nullable();
            $table->json('things')->nullable();
            $table->boolean('brand')->default(false);
            $table->string('notation')->nullable();
            $table->float('for_kg', 10, 2)->default(0);
            $table->float('for_place', 10, 2)->default(0);
            $table->float('sum', 10, 2)->default(0);
            $table->float('sale', 10, 2)->default(0);
            $table->unsignedInteger('fax_id')->default(0);
            $table->unsignedInteger('category_id')->default(0);
            $table->unsignedInteger('storehouse_id')->default(1);
            $table->unsignedTinyInteger('delivery_method_id')->default(0);
            $table->boolean('paid')->default(false);
            $table->string('department')->nullable();
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
        Schema::dropIfExists('cargos');
    }
}
