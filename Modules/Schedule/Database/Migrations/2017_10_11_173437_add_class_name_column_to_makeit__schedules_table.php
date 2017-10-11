<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClassNameColumnToMakeitSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('makeit__schedules', function (Blueprint $table) {
            $table->string('class_name')->after('subject_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('makeit__schedules', function (Blueprint $table) {
            $table->dropColumn('class_name');
        });
    }
}
