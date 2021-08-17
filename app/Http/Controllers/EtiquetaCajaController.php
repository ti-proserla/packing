<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\EtiquetaCaja;

class EtiquetaCajaController extends Controller
{
    public function index(){
        $etiquetaCaja=EtiquetaCaja::orderBy('id','DESC')->paginate(10);
        return response()->json($etiquetaCaja);
    }
}
