<?php

namespace App\Http\Controllers;
use App\Http\Requests\SubLoteValidate;
use Illuminate\Http\Request;
use App\Model\SubLote;

class SubLoteController extends Controller
{
   
    public function index($id)
    {
        $subLotes=SubLote::with('transportista')
                            ->join('materia','materia.id','=','sub_lote.materia_id')
                            ->join('variedad','variedad.id','=','sub_lote.variedad_id')
                            ->join('tipo','tipo.id','=','sub_lote.tipo_id')
                            ->where('lote_id',$id)
                            ->select('sub_lote.*','materia.nombre_materia','variedad.nombre_variedad','tipo.nombre_tipo')
                            ->get();
        return response()->json($subLotes);
    }

    
   
    public function store(SubLoteValidate $request)
    {
        // dd($request->all());
        $subLotes=new SubLote();
        $subLotes->lote_id=$request->lote_id;
        $subLotes->guia=$request->guia;
        $subLotes->transportista_id=$request->transportista_id;
        $subLotes->viaje=$request->viaje;
        $subLotes->fecha_recepcion=$request->fecha_recepcion;
        $subLotes->peso_guia=$request->peso_guia;
        $subLotes->materia_id=$request->materia_id;
        $subLotes->variedad_id=$request->variedad_id;
        $subLotes->tipo_id=$request->tipo_id;
        $subLotes->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    public function show($id)
    {
        //
    }

   
   
    public function update(SubLoteValidate $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }
}
