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
    public function count()
    {
        return $this->hasMany('App\Model\PaletEntrada')->count();
    }
}
