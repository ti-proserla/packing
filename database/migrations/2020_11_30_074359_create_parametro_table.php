<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametro', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion',100);
            $table->string('valor',100)->nullable();
            $table->timestamps();
        });
        DB::table('parametro')->insert([
            [
                'descripcion'=> 'index_codigo_trabajador', 
                'valor'  => '0',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametro');
    }
}
