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
            $table->string('codigo',15)->nullable();
            $table->string('campania_id',5)->nullable();
            $table->string('tipo_palet_id',3)->nullable();
            $table->integer('cliente_id');
            $table->integer('numero')->nullable();
            $table->integer('etapas');
            $table->string('estado',30); //Abierto, Cerrado
            $table->date('fecha_cierre'); // Cierre de palet , fecha sistema.
            $table->integer('operacion_id');
            $table->integer('nave')->nullable()->default(1);
            $table->integer('camara')->nullable();
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
