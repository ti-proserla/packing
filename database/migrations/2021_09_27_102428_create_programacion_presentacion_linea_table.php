<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramacionPresentacionLineaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programacion_presentacion_linea', function (Blueprint $table) {
            $table->id();
            $table->string('linea_id');
            $table->datetime('inicio');
            $table->datetime('fin');
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
