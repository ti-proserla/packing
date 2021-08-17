<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NisiraPuertoEmbarque;

class ReplicacionController extends Controller
{
    public function puerto_embarque(){
        $nisiraPuertos=NisiraPuertoEmbarque::all();
        dd($nisiraPuertos);
        return "hola";
    }
}
