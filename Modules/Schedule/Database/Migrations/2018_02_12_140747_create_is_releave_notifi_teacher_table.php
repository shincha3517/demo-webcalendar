<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIsReleaveNotifiTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('makeit__teachers', function (Blueprint $table) {
            $table->tinyInteger('is_leave_notify');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('makeit__teachers', function (Blueprint $table) {
            $table->dropColumn('is_leave_notify');
        });
    }
}
