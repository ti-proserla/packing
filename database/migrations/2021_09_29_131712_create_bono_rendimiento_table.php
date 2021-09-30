<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonoRendimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bono_rendimiento', function (Blueprint $table) {
            $table->id();
            $table->integer('presentacion_id')->unsigned();
            $table->string('codigo_labor',2);
            $table->integer('cajas')->unsigned();
            $table->decimal('bono',10,4);
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
        Schema::dropIfExists('bono_rendimiento');
    }
}
