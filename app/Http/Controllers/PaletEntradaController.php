<?php

namespace App\Http\Controllers;
use App\Http\Requests\PaletEntradaValidate;
use Illuminate\Http\Request;
use App\Model\PaletEntrada;

class PaletEntradaController extends Controller
{
    
    public function index($sub_lote_id)
    {
        $paletEntradas=PaletEntrada::where('sub_lote_id',$sub_lote_id)->get();
        return response()->json($paletEntradas);
    }

    
  
    public function store(PaletEntradaValidate $request,$sub_lote_id)
    {
        $paletEntradas=new PaletEntrada();
        $paletEntradas->sub_lote_id=$sub_lote_id;
        $paletEntradas->peso=$request->peso;
        $paletEntradas->num_jabas = $request->num_jabas;
        $paletEntradas->save();
        return response()->json([
            "status" => "OK"
        ]);
    }  
}
