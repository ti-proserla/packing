<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresentacionIdToRendimientoPersonal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //se tiene que actualizar manualmente el nullable de caja_id
        Schema::table('rendimiento_personal', function (Blueprint $table) {
            $table->unsignedBigInteger('caja_id')->nullable()->change();
            $table->integer('presentacion_id')->unsigned()->nullable();
            $table->date('fecha_empaque')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rendimiento_personal', function (Blueprint $table) {
            //
        });
    }
}
