<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDateToMakeitScheduleDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('makeit__schedule_dates', function (Blueprint $table) {
            $table->dateTime('start_date')->nullable()->after('day_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('makeit__schedule_dates', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
}
