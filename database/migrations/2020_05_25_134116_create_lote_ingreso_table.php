<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoteIngresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lote_ingreso', function (Blueprint $table) {
            $table->id();
            $table->string('codigo',30);
            $table->integer('cliente_id');
            $table->integer('materia_id');
            $table->integer('variedad_id');
            $table->integer('tipo_id');
            $table->integer('fundo_id');
            $table->integer('parcela_id')->nullable();
            $table->date('fecha_cosecha');
            $table->date('fecha_proceso')->nullable();
            $table->string('estado',30)->nullable();
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
        Schema::dropIfExists('lote_ingreso');
    }
}
