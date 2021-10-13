<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParihuelaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parihuela', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('modelo_parihuela',50);
            $table->string('medidas_parihuela',50);
            $table->timestamps();
        });

        // Schema::table('palet_salida', function (Blueprint $table) {
        //     $table->integer('parihuela_id')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parihuela');
    }
}
