<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMakeitAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('makeit__assignment', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('teacher_id')->nullable();
            $table->string('teacher_name')->nullable();

            $table->integer('replaced_teacher_id')->nullable();
            $table->string('replaced_teacher_name')->nullable();

            $table->integer('schedule_id')->nullable();
            $table->integer('schedule_event_id')->nullable();
            $table->string('lesson')->nullable();
            $table->string('subject')->nullable();
            $table->integer('slot_id')->nullable();

            $table->string('day_name')->nullable();
            $table->date('selected_date')->nullable();

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->string('reason')->nullable();
            $table->string('additionalRemark')->nullable();

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
        Schema::dropIfExists('makeit__assignment');
    }
}
