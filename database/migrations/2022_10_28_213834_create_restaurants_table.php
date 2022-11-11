<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('type');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('mobile');
            $table->string('email');
            $table->string('password');
            $table->string('region');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->float('min_order')->nullable();
            $table->float('working_hours')->nullable();
            $table->float('delivery_time')->nullable();
            $table->float('delivery_fee')->default(0);
            $table->boolean('online_tracking')->default(0);
            $table->decimal('latitude')->nullable();
            $table->decimal('longitude')->nullable();
            $table->boolean('roles')->default(1);
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
        Schema::dropIfExists('restaurants');
    }
};
