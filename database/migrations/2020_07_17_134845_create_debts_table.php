<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('code_client_id');
            $table->boolean('type')->default(false);
            $table->float('sum', 23, 2)->default(0);
            $table->float('commission', 10, 2)->default(0);
            $table->string('notation')->nullable();
            $table->unsignedInteger('user_id')->default(false);
            $table->unsignedInteger('transfer_id')->default(false);
            $table->boolean('paid')->default(false);
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
        Schema::dropIfExists('debts');
    }
}
