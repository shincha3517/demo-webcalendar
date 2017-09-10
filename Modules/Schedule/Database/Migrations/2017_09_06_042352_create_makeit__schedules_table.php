<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMakeitSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('makeit__schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id');
            $table->string('date_id');
            $table->string('subject_code');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->time('start_time');
            $table->time('end_time');

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
        Schema::dropIfExists('makeit__schedules');
    }
}
