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
        Schema::create('driver_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('rating')->nullable();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('ride_id');
            $table->timestamps();
            $table->unique(['ride_id', 'driver_id']);


            // $table->foreign('ride_id')
            // ->references('id')
            // ->on('rides')
            // ->onDelete('cascade');
            $table->foreign('driver_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        });
    }
  
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_reviews');
    }
};