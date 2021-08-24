<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Produccion;
use App\Model\PaletSalida;

class ProduccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('estado')) {
            $Producciones=Produccion::join('cliente','cliente.id','=','Produccion.cliente_id')
                            ->where('estado','>=',$request->estado)
                            ->select('Produccion.*','cliente.descripcion as cliente')
                            ->get();
        }else{
            $Producciones=Produccion::join('cliente','cliente.id','=','Produccion.cliente_id')
                            ->where('fecha_produccion','>=',$request->desde)
                            ->where('fecha_produccion','<=',$request->hasta)
                            ->select('Produccion.*','cliente.descripcion as cliente')
                            ->paginate(10);
        }
        return response()->json($Producciones);
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
        $produccion=new Produccion();
        $produccion->estado='Pendiente';
        $produccion->productor_id=$request->productor_id;
        $produccion->fecha_produccion=$request->fecha_produccion;
        $produccion->save();
        return response()->json([
            "status"    =>  "OK",
            "message"   =>  "Programación Producción Creada.",
            "data"      =>  $Produccion
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Produccion=Produccion::join('cliente','cliente.id','=','Produccion.cliente_id')
                        ->where('Produccion.id',$id)
                        ->select('Produccion.*','cliente.descripcion as cliente')
                        ->first();    
        return response()->json($Produccion);
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
