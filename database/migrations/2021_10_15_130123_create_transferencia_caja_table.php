<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferenciaCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia_caja', function (Blueprint $table) {
            $table->id();
            $table->integer('palet_destino_id');
            $table->integer('palet_salida_id');
            $table->unsignedBigInteger('caja_id');
            $table->unsignedInteger('etiqueta_caja_id');
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
        Schema::dropIfExists('transferencia_caja');
    }
}
