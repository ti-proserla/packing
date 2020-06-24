<?php

namespace App\Http\Controllers;
use App\Http\Requests\PaletEntradaValidate;
use Illuminate\Http\Request;
use App\Model\PaletEntrada;

class PaletEntradaController extends Controller
{
    
    public function index()
    {
        $paletEntradas=PaletEntrada::all();
        return response()->json($paletEntradas);
    }

    
  
    public function store(PaletEntradaValidate $request)
    {
        $paletEntradas=new PaletEntrada();
        $paletEntradas->sub_lote_id=$request->sub_lote_id;
        $paletEntradas->peso=$request->peso;
        $paletEntradas->num_jabas = $request->num_jabas;
        $paletEntradas->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

   
    public function show($id)
    {
        //
    }


 
    public function update(Request $request, $id)
    {
        //
    }

   
}
