<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubLote extends Model
{
    protected $table="sub_lote";

    public function transportista()
    {
        return $this->belongsTo('App\Model\Transportista');
    }
    
}
