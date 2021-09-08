<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('code_id');
            $table->string('code_name');
            $table->string('city_name')->nullable();
            $table->string('client_name')->nullable();
            $table->string('last_active_cargo')->nullable();
            $table->string('client_created_at')->nullable();
            $table->unsignedInteger('index')->default(0);
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
        Schema::dropIfExists('codes_statistics');
    }
}
