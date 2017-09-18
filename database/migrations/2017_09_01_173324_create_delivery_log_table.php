<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryLogTable extends Migration {

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
            $table->tinyInteger('is_delivered')->nullable();
            $table->string('delivery_method');
            $table->text('delivery_location');
            $table->smallInteger('delivery_attempts');
            $table->text('response_body')->nullable();
            $table->smallInteger('response_code')->nullable();
            $table->double('delivery_time_microseconds', 16, 6)->nullable();
            $table->double('response_time_microseconds', 16, 6)->nullable();
            $table->double('delivery_datetime', 16, 6)->nullable();
            $table->double('response_datetime', 16, 6)->nullable();

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
