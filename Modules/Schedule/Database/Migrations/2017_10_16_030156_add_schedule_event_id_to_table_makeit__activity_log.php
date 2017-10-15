<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScheduleEventIdToTableMakeitActivityLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('makeit__activity_log', function (Blueprint $table) {
            $table->integer('schedule_event_id')->after('schedule_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('makeit__activity_log', function (Blueprint $table) {
            $table->dropColumn('schedule_event_id');
        });
    }
}
