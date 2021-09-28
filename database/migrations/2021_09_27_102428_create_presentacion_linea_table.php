<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresentacionLineaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presentacion_linea', function (Blueprint $table) {
            $table->id();
            $table->integer('linea_id');
            $table->integer('presentacion_id')->unsigned();
            $table->datetime('inicio');
            $table->datetime('fin')->nullable();
            $table->date('fecha_ref');
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
        Schema::dropIfExists('programacion_presentacion_linea');
    }
}
