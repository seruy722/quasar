<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaxDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fax_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->integer('code_id')->unsigned();
            $table->integer('fax_id')->unsigned();
            $table->integer('place')->unsigned()->default(0);
            $table->float('kg', 10, 2)->unsigned()->default(0);
            $table->string('shop')->nullable();
            $table->text('things')->nullable();
            $table->integer('category_id')->unsigned();
            $table->boolean('brand')->default(false);
            $table->string('notation')->nullable();
            $table->float('for_kg', 10, 2)->default(0);
            $table->float('for_place', 10, 2)->default(0);
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
        Schema::dropIfExists('fax_data');
    }
}
