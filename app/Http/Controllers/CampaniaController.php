<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Campania;

class CampaniaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $campanias=Campania::join('materia','materia.id','=','campania.materia_id')
                            ->select('campania.*','materia.nombre_materia')
                            ->where('estado',$request->estado)
                            ->get();
        }else{
            $campanias=Campania::join('materia','materia.id','=','campania.materia_id')
                        ->select('campania.*','materia.nombre_materia')
                        ->paginate(10); 
        }
        return response()->json($campanias);
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
        $campania=new Campania();
        $campania->id=$request->id;
        $campania->materia_id=$request->materia_id;
        $campania->anio=$request->anio;
        $campania->estado="Abierto";
        $campania->save();
        return response()->json([
            "status" => "OK",
            "message"=> "Campaña Registrada."
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
        //
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
