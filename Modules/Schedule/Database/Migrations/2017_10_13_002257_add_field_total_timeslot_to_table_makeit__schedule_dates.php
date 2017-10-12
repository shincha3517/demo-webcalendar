<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTotalTimeslotToTableMakeitScheduleDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('makeit__schedule_dates', function (Blueprint $table) {
            $table->integer('old_total_timeslots')->nullable();
            $table->integer('event_total_timeslots')->nullable();
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
            $table->dropColumn('old_total_timeslots');
            $table->dropColumn('event_total_timeslots');
        });
    }
}
