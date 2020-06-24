<?php

namespace App\Http\Controllers;

use App\Model\LoteIngreso;
use App\Http\Requests\LoteIngresoValidate;
use Illuminate\Http\Request;

class LoteIngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lotesIngreso=LoteIngreso::join('cliente','cliente.id','=','lote_ingreso.cliente_id')
                        ->join('materia','materia.id','=','lote_ingreso.materia_id')
                        ->join('variedad','variedad.id','=','lote_ingreso.variedad_id')
                        ->select('lote_ingreso.*','cliente.descripcion','materia.nombre_materia','variedad.nombre_variedad')
                        ->get();
        return response()->json($lotesIngreso); 
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
    public function store(LoteIngresoValidate $request)
    {
        $loteIngreso=new LoteIngreso();
        $loteIngreso->codigo=$request->codigo;
        $loteIngreso->cliente_id=$request->cliente_id;
        $loteIngreso->materia_id=$request->materia_id;
        $loteIngreso->variedad_id=$request->variedad_id;
        $loteIngreso->fecha_cosecha=$request->fecha_cosecha;
        $loteIngreso->estado="Registrado";
        $loteIngreso->save();
        return response()->json([
            "status" => "OK",
            "data"   => "Lote Ingreso Registrado."
        ]);
    }

    public function show($id)
    {
        $loteIngreso=LoteIngreso::where('id',$id)->first();
        return response()->json($loteIngreso);
    }
   
    

    public function update(LoteIngresoValidate $request, $id)
    {
        $loteIngreso=LoteIngreso::where('id',$id)->first();
        $loteIngreso->codigo=$request->codigo;
        $loteIngreso->cliente_id=$request->cliente_id;
        $loteIngreso->materia_id=$request->materia_id;
        $loteIngreso->variedad_id=$request->variedad_id;
        $loteIngreso->fecha_cosecha=$request->fecha_cosecha;
        $loteIngreso->estado="Registrado";
        $loteIngreso->save();
        return response()->json([
            "status" => "OK",
            "data"   => "Lote Ingreso Registrado."
        ]);
    }

   
    public function destroy($id)
    {
        $loteIngreso=LoteIngreso::where('id',$id)->first();
        $loteIngreso->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
    
}

