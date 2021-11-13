<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->enum('status', ['Started', 'Renewed', 'Cancelled'])->default('Started');
            $table->bigInteger("device_id")->unsigned()->index();
            $table->bigInteger("app_id")->unsigned()->index();
            $table->string("receipt");
            $table->datetime("expired_at");
            $table->timestamps();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->foreign('app_id')->references('id')->on('apps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
