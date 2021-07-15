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
            // dd($request->cliente_id);
            $request->estado;
            $subLotes=SubLote::join('lote_ingreso','lote_ingreso.id','=','sub_lote.lote_id')
                        ->join('cliente','cliente.id','=','lote_ingreso.cliente_id')
                        ->join('materia','materia.id','=','lote_ingreso.materia_id')
                        ->join('variedad','variedad.id','=','lote_ingreso.variedad_id')
                        ->join('tipo','tipo.id','=','lote_ingreso.tipo_id')
                        ->join('fundo','fundo.id','=','lote_ingreso.fundo_id')
                        ->leftJoin('parcela','parcela.id','=','lote_ingreso.parcela_id')
                        ->whereIn('sub_lote.estado',explode(',',$request->estado))
                        // ->where('cliente_id',$request->cliente_id)    
                        ->select('sub_lote.*',
                            'lote_ingreso.codigo',
                            'cliente.descripcion as cliente',
                            'materia.nombre_materia as materia',
                            'variedad.nombre_variedad as variedad',
                            'tipo.nombre_tipo as tipo',
                            'fundo.nombre_fundo as fundo',
                            'parcela.nombre_parcela as parcela'
                        )
                        ->get();
        }else{
            
        }
    
        return response()->json($subLotes);
    }

    
   
    public function store(SubLoteValidate $request)
    {
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
    
    public function estado(Request $request,$id){
        // dd($request->all());
        $subLotes=SubLote::where('id',$id)->first();
        $subLotes->estado=$request->estado;
        $subLotes->save();
        return response()->json([
            "status" => "OK",
        ]);
    }

    public function show($id)
    {
        
    }
    public function palets($id){
        $subLote=SubLote::with('palets')
                    ->where('id',$id)
                    ->first();
        return response()->json($subLote);
        
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
