<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubLote extends Model
{
    protected $table="sub_lote";

    public function palets()
    {
        return $this->hasMany('App\Model\PaletEntrada');
    }
    
}
