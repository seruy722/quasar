<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersActionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers_action_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('transfer_id');
            $table->unsignedInteger('client_id')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->float('sum', 23, 2)->nullable();
            $table->unsignedTinyInteger('method')->nullable();
            $table->string('notation')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->unsignedInteger('user_id');
            $table->dateTime('issued_by')->nullable();
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
        Schema::dropIfExists('transfers_action_histories');
    }
}
