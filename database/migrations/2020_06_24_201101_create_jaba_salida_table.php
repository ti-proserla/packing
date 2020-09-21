<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJabaSalidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jaba_salida', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras',25);
            $table->string('linea',2)->nullable();
            $table->string('codigo_operador',8)->nullable();
            $table->integer('palet_salida_id');
            $table->integer('numero');
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
        Schema::dropIfExists('jaba_salida');
    }
}
