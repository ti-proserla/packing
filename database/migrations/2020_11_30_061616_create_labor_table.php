<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_labor',2)->nullable();
            $table->string('codigo_auxiliar',100)->nullable();//separado de comas.
            $table->string('descripcion',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labor');
    }
}
