<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescarteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descarte', function (Blueprint $table) {
            $table->id();
            $table->integer('lote_id');
            $table->decimal('descarte_racimos',10,4)->nullable();
            $table->decimal('descarte_granos',10,4)->nullable();
            $table->integer('cantidad_jabas_descarte')->nullable();
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
        Schema::dropIfExists('descarte');
    }
}
