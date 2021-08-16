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
        $num_palet=PaletEntrada::where('sub_lote_id',$sub_lote_id)->count();
        $paletEntradas=new PaletEntrada();
        $paletEntradas->num_palet=$num_palet+1;
        $paletEntradas->sub_lote_id=$sub_lote_id;
        $paletEntradas->peso=$request->peso;
        $paletEntradas->peso_palet=$request->peso_palet;
        $paletEntradas->peso_jaba=$request->peso_jaba;
        $paletEntradas->num_jabas = $request->num_jabas;
        $paletEntradas->save();
        return response()->json([
            "status"    => "OK",
            "data"      =>  $paletEntradas
        ]);
    }
}
