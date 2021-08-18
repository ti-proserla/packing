<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtiquetaCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etiqueta_caja', function (Blueprint $table) {
            $table->id();
            $table->integer('lote_ingreso_id');
            $table->integer('calibre_id');          //
            $table->integer('categoria_id');        //
            $table->integer('presentacion_id');     //
            $table->integer('marca_caja_id');       //
            $table->integer('plu_id')->nullable();  //
            $table->integer('tipo_bolsa_id')->nullable();
            $table->integer('marca_bolsa_id')->nullable();
            $table->date('fecha_empaque');
            $table->string('estado',30)->default('Pendiente');
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
        Schema::dropIfExists('etiqueta_caja');
    }
}
