<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodigoToOperacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operacion', function (Blueprint $table) {
            $table->string('codigo_operacion',12)->nullable();
            $table->integer('anio')->nullable();
            $table->integer('semana')->nullable();
            $table->integer('cantidad_cajas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operacion', function (Blueprint $table) {
            //
        });
    }
}
