<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMakeitActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('makeit__activity_log', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('teacher_id');
            $table->integer('replaced_teacher_id');
            $table->integer('schedule_id');
            $table->date('selected_date');

            $table->integer('status');
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
        Schema::dropIfExists('makeit__activity_log');
    }
}
