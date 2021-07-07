<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table='materia';

    public function variedad()
    {
        return $this->hasMany('App\Model\Variedad');
    }
    public function tipo()
    {
        return $this->hasMany('App\Model\Tipo');
    }
}
