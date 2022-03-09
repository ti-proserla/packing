<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Operacion;
use App\Model\PaletSalida;
use Illuminate\Support\Facades\DB;

class OperacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('estado')) {
            $operaciones=Operacion::join('cliente','cliente.id','=','operacion.cliente_id')
                            ->where('estado','=',$request->estado)
                            ->select('operacion.*','cliente.descripcion as cliente');

            if ($request->cliente_id!=null) {
                $operaciones=$operaciones->where('cliente_id',$request->cliente_id);
            }
            $operaciones=$operaciones->get();
        }else{
            $operaciones=Operacion::join('cliente','cliente.id','=','operacion.cliente_id')
                            ->where('fecha_operacion','>=',$request->desde)
                            ->where('fecha_operacion','<=',$request->hasta)
                            ->select('operacion.*','cliente.descripcion as cliente')
                            ->orderBy('operacion.anio','DESC')
                            ->orderBy('operacion.semana','DESC')
                            ->orderBy('operacion.id','DESC')
                            ->paginate(20);
        }
        return response()->json($operaciones);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $numerador=Operacion::where('semana',$request->semana)->where('anio',$request->anio)->select(DB::raw('count(*)+1 numerador'))->first()->numerador;
        // dd($numerador);
        $operacion=new Operacion();
        $operacion->estado='Pendiente';
        $operacion->anio=$request->anio;
        $operacion->semana=$request->semana;
        $operacion->cantidad_cajas=$request->cantidad_cajas;
        $operacion->codigo_operacion=$operacion->anio.'.'.str_pad($request->semana, 2, "0", STR_PAD_LEFT).'.'.str_pad($numerador, 2, "0", STR_PAD_LEFT);
        $operacion->descripcion=$request->descripcion;
        $operacion->cliente_id=$request->cliente_id;
        $operacion->fecha_operacion=$request->fecha_operacion;
        $operacion->save();
        return response()->json([
            "status"    =>  "OK",
            "message"   =>  "Operación Creada.",
            "data"      =>  $operacion
        ]);
    }
    public function addPalet(Request $request){
        // dd($request->codigo);
        $palet_id=explode('-',$request->codigo)[1];
        $palet_salida=PaletSalida::where('id',$palet_id)->first();
        dd($request->all(),$palet_salida);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $operacion=Operacion::join('cliente','cliente.id','=','operacion.cliente_id')
                        ->where('operacion.id',$id)
                        ->select('operacion.*','cliente.descripcion as cliente')
                        ->first();    
        return response()->json($operacion);
    }
    public function showCodigo($codigo)
    {
        $operacion=Operacion::join('cliente','cliente.id','=','operacion.cliente_id')
                        ->where('operacion.codigo_operacion',$codigo)
                        ->select('operacion.*','cliente.descripcion as cliente')
                        ->first();    
        return response()->json($operacion);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        switch ($request->estado) {
            case 'Despachado':
                $operacion=Operacion::find($id);
                $operacion->estado='Despachado';
                $operacion->fecha_despachado=$request->fecha_despachado;
                $operacion->save();
                PaletSalida::where('operacion_id',$id)
                ->update(['estado' => 'Despachado']);

                break;
            
            default:
                
                break;
        }
        
        return response()->json([
            "status"    =>  "OK",
            "message"   =>  "Operación Actualizada.",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
