<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoPaletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_palet', function (Blueprint $table) {
            $table->string('id',3); //TER , SAL, MUE
            $table->string('descripcion',50)->nullable();
            $table->string('exportable',20)->default('NO');
            $table->timestamps();
        });

        DB::table('tipo_palet')->insert([
            ['id'=> 'TER','descripcion'=> 'Palet Terminado' ,'exportable'=> 'SI'],
            ['id'=> 'SAL','descripcion'=> 'Palet Saldo'     ,'exportable'=> 'NO'],
            ['id'=> 'MUE','descripcion'=> 'Palet Muestra'   ,'exportable'=> 'NO'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_palet');
    }
}
