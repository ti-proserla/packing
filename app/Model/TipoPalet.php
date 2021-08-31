<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TipoPalet extends Model
{
    protected $table='tipo_palet';
    protected $casts = ['id' => 'string'];
}
