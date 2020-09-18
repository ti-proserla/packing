<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cliente')->insert([
            'ruc'           => 10773829786,
            'descripcion'   => 'PROSERLA',
        ]); 
        DB::table('cliente')->insert([
            'ruc'           => 10773829787,
            'descripcion'   => 'JAYANCA',
        ]); 

        DB::table('materia')->insert([
            'nombre_materia'=> 'UVA'
        ]); 
        DB::table('variedad')->insert([
            'nombre_variedad'=> 'GLOBE',
            'materia_id'=> 1,
        ]); 
        DB::table('transportista')->insert([
            'documento'=> 15487952162,
            'nombre_transportista'=> 'JAMPAR',
        ]); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
