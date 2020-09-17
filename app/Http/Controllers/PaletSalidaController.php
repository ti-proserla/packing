<?php

namespace App\Http\Controllers;

use App\Model\PaletSalida;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaletSalidaController extends Controller
{
    public function index($sub_lote_id)
    {
        $paletSalidas=PaletSalida::all();
        return response()->json($paletSalidas);
    }  

    public function store(Request $request)
    {
        $paletSalida=new PaletSalida();
        $paletSalida->lote_id=$request->lote_id;
        $paletSalida->producto_id=$request->producto_id;
        $paletSalida->proceso_id=1;
        $paletSalida->fecha=Carbon::now();
        $paletSalida->save();
        return response()->json([
            "status" => "OK"
        ]);
    }  
}
