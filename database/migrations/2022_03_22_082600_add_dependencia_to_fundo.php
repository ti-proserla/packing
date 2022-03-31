<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDependenciaToFundo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fundo', function (Blueprint $table) {
            $table->string('distrito',100)->nullable();
            $table->string('provincia',100)->nullable();
            $table->string('departamento',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fundo', function (Blueprint $table) {
            //
        });
    }
}
