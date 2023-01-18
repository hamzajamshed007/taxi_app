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
        Schema::create('ride_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_id');
            $table->decimal('distance_total', 10, 2)->nullable();
            $table->decimal('pause_total', 10, 2)->nullable();
            $table->decimal('pause_per_min_rate', 10, 2);
            $table->decimal('per_km_rate', 10, 2);
            $table->decimal('total_distance_traveled', 10, 2)->nullable();
            $table->decimal('total_pause_time', 10, 2)->nullable();
            $table->datetime('pause_start')->nullable();
            $table->datetime('pause_end')->nullable();
            $table->timestamps();
            $table->unique('ride_id');


            $table->foreign('ride_id')
            ->references('id')
            ->on('rides')
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
        Schema::dropIfExists('ride_calculations');
    }
};