<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaletEntradaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('palet_entrada', function (Blueprint $table) {
            $table->id();
            $table->integer('numero')->nullable();
            $table->integer('sub_lote_id');
            $table->decimal('peso',8,4);
            $table->integer('num_jabas');
            $table->decimal('peso_palet',8,4);
            $table->decimal('peso_jaba',8,4);
            $table->integer('producto_id');
            $table->string('estado',30)->default('Pendiente');
            $table->integer('linea_lanzado')->nullable();
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
        Schema::dropIfExists('palet_entrada');
    }
}
