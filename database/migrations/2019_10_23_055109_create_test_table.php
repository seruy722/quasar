<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('client')->nullable();
            $table->integer('place')->unsigned()->default(0);
            $table->float('kg', 10, 2)->unsigned()->default(0);
            $table->string('shop')->nullable();
            $table->text('things')->nullable();
            $table->string('category')->nullable();
            $table->boolean('brand')->default(false);
            $table->string('notation')->nullable();
            $table->string('fax_name')->nullable();
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
        Schema::dropIfExists('test');
    }
}
