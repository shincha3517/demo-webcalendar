<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('method')->nullable();
            $table->string('request_url')->nullable();
            $table->text('request_string')->nullable();
            $table->text('response_string')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('request_ip')->nullable();
            $table->enum('device_type',['ANDROID','IOS','WEB'])->nullable();
            $table->string('platform')->nullable();
            $table->longText('request_header')->nullable();
            $table->longText('token')->nullable();
            $table->integer('duration')->nullable();
            $table->text('agent_info')->nullable();

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
        Schema::dropIfExists('api_logs');
    }
}
