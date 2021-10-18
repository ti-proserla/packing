<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo',20);
            $table->integer('productor_id');
            $table->integer('cliente_destino_id');
            $table->integer('pais_id');
            $table->integer('puerto_id');
            $table->integer('variedad_id');
            $table->integer('producto_id');
            $table->integer('categoria_id');
            $table->integer('tipo_empaque_id');
            $table->integer('marca_empaque_id');
            $table->string('',100);
            $table->date('fecha_operacion');
            $table->string('estado',30)->default('Generado');
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
        Schema::dropIfExists('operacion');
    }
}
