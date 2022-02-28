<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCamaraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camara', function (Blueprint $table) {
            $table->string('codigo',10)->unique();
            $table->string('nombre',50);
            $table->integer('matriz_x');
            $table->integer('matriz_y');
            $table->integer('pisos');
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
        Schema::dropIfExists('camara');
    }
}
