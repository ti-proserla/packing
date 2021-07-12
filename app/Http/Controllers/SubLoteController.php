<?php

namespace App\Http\Controllers;
use App\Http\Requests\SubLoteValidate;
use Illuminate\Http\Request;
use App\Model\SubLote;

class SubLoteController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('estado')) {
            $subLotes=SubLote::join('lote_ingreso','lote_ingreso.id','=','sub_lote.lote_id')
                        ->where('sub_lote.estado',$request->estado)    
                        ->get();
        }else{
            
        }
        return response()->json($subLotes);
    }

    
   
    public function store(SubLoteValidate $request)
    {
        // dd($request->all());
        $subLotes=new SubLote();
        $subLotes->lote_id=$request->lote_id;
        $subLotes->viaje=$request->viaje;
        $subLotes->guia=$request->guia;
        $subLotes->fecha_recepcion=$request->fecha_recepcion;
        $subLotes->peso_guia=$request->peso_guia;
        $subLotes->estado='Pendiente';
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
