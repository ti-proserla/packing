<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Operacion;
use App\Model\PaletSalida;

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
                            ->where('estado','>=',$request->estado)
                            ->select('operacion.*','cliente.descripcion as cliente')
                            ->get();
        }else{
            $operaciones=Operacion::join('cliente','cliente.id','=','operacion.cliente_id')
                            ->where('fecha_operacion','>=',$request->desde)
                            ->where('fecha_operacion','<=',$request->hasta)
                            ->select('operacion.*','cliente.descripcion as cliente')
                            ->paginate(10);
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
        $operacion=new Operacion();
        $operacion->estado='Pendiente';
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
        //
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
