<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldScheduleTypeToAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('makeit__assignment', function (Blueprint $table) {
            $table->enum('schedule_type',['old','event'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('makeit__assignment', function (Blueprint $table) {
            $table->dropColumn('schedule_type');
        });
    }
}
