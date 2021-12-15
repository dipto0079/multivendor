<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingPickUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_pick_ups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_id');
            $table->integer('seller_id');
            $table->string('pickup_title');
            $table->string('pickup_address');
            $table->integer('country_id');
            $table->integer('city_id');
            $table->integer('state');
            $table->integer('zip');
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
        Schema::dropIfExists('shipping_pick_ups');
    }
}
