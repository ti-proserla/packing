<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForaneanToEtiquetaCaja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoria', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('calibre', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('presentacion', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('marca_caja', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('plu', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('tipo_empaque', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('marca_empaque', function (Blueprint $table) {
            $table->integer('id')->unsigned()->change();
        });
        Schema::table('etiqueta_caja', function (Blueprint $table) {
            $table->integer('categoria_id')->unsigned()->change();
            $table->integer('calibre_id')->unsigned()->change();
            $table->integer('presentacion_id')->unsigned()->change();
            $table->integer('marca_caja_id')->unsigned()->change();
            $table->integer('plu_id')->unsigned()->change();
            $table->integer('tipo_empaque_id')->unsigned()->change();
            $table->integer('marca_empaque_id')->unsigned()->change();
            
            // $table->foreign('calibre_id')->references('id')->on('calibre');
            $table->foreign('categoria_id')->references('id')->on('categoria')->change();
            $table->foreign('calibre_id')->references('id')->on('calibre')->change();
            $table->foreign('presentacion_id')->references('id')->on('presentacion')->change();
            $table->foreign('marca_caja_id')->references('id')->on('marca_caja')->change();
            $table->foreign('plu_id')->references('id')->on('plu')->change();
            $table->foreign('tipo_empaque_id')->references('id')->on('tipo_empaque')->change();
            $table->foreign('marca_empaque_id')->references('id')->on('marca_empaque')->change();
                // ->onDelete('cascade');
            // $table->foreign('presentacion_id')->references('id')->on('presentacion');
            // $table->foreign('marca_caja_id')->references('id')->on('marca_caja');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etiqueta_caja', function (Blueprint $table) {
            //
        });
    }
}
