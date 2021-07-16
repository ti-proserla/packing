<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaletSalidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('palet_salida', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente_id');
            $table->integer('etapas');
            $table->integer('numero')->nullable();
            $table->string('estado',30); //Abierto, Cerrado
            $table->date('fecha_cierre'); // Cierre de palet , fecha sistema.
            $table->integer('operacion_id');
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
        Schema::dropIfExists('palet_salida');
    }
}
