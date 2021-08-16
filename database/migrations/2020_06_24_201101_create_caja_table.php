<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->id();
            $table->integer('palet_salida_id');
            $table->integer('lote_ingreso_id');
            $table->string('calibre',10);
            $table->string('categoria',10);
            $table->string('presentacion',20);
            $table->string('marca_caja',30);
            $table->string('plu',10);
            $table->string('tipo_bolsa',10);
            $table->string('marca_bolsa',10);
            
            $table->date('fecha_empaque'); // fecha sistema.
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
        Schema::dropIfExists('caja');
    }
}
