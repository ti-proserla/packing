<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EtiquetaCaja extends Model
{
    protected $table="etiqueta_caja";
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i' 
    ];
}
