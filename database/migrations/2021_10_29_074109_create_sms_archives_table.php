<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_archives', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 36);
            $table->unsignedInteger('fax_id');
            $table->unsignedInteger('user_id');
            $table->string('type', 50);
            $table->json('result');
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
        Schema::dropIfExists('sms_archives');
    }
}
