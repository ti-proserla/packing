<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Fundo extends Model
{
    protected $table='fundo';

    public function parcelas()
    {
        return $this->hasMany('App\Model\Parcela');
    }
}
