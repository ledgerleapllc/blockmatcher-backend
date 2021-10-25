<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_offers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('amount');
            $table->tinyInteger('type');
            $table->bigInteger('user_id');
            $table->bigInteger('batch_id')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('desired_price')->nullable();
            $table->tinyInteger('is_batch')->default(0);
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
        Schema::dropIfExists('buy_offers');
    }
}
