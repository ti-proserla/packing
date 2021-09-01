<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campania', function (Blueprint $table) {
            $table->string('id',5);
            $table->string('estado',30)->default('Abierto'); //Cerrado
            $table->integer('materia_id');
            $table->integer('anio');
            // $table->integer('productor_id');
            // $table->integer('conteo_palet_terminado')->default(0);
            // $table->integer('conteo_palet_saldo')->default(0);
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
        Schema::dropIfExists('campania');
    }
}
