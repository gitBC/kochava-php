<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('original_redis_key');
            $table->string('delivery_method');
            $table->text('delivery_location');
            $table->smallInteger('delivery_attempts');
            $table->double('delivery_time_microseconds', 24, 14);
            $table->text('response_body');
            $table->smallInteger('response_code');
            $table->double('response_time_microseconds', 24, 14);
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
        Schema::drop('delivery_logs');
    }
}
