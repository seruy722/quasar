<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInCargoToStorehouseDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storehouse_data', function (Blueprint $table) {
            $table->boolean('in_cargo')->default(false)->after('destroyed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storehouse_data', function (Blueprint $table) {
            $table->dropColumn('in_cargo');
        });
    }
}
