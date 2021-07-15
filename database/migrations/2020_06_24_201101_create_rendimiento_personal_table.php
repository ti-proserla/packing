<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRendimientoPersonalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rendimiento_personal', function (Blueprint $table) {
            $table->id();
            $table->integer('caja_id');
            $table->string('codigo_barras',25);
            $table->string('codigo_operador',8)->nullable();
            $table->string('linea',2)->nullable();
            $table->string('turno',2)->nullable();
            $table->string('codigo_labor',8)->nullable();
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
        Schema::dropIfExists('rendimiento_personal');
    }
}
