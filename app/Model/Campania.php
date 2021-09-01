<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Campania extends Model
{
    protected $table='campania';
    protected $casts = ['id' => 'string'];
}
