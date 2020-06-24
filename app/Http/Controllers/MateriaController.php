<?php

namespace App\Http\Controllers;
use App\Model\Materia;
use App\Http\Requests\MateriaValidate;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materias=Materia::all();
        return response()->json($materias);
    }

    public function variedad(){
        $materias=Materia::with('variedad')->get();
        return response()->json($materias);
    }

   
    public function store(MateriaValidate $request)
    {
        $materias=new Materia();
        $materias->nombre_materia=$request->nombre_materia;
        $materias->save();

        return response()->json([
            "status" => "OK"
        ]);

    }



    public function show($id)
    {
        $materias=Materia::where('id',$id)->first();
        return response()->json($materias);
    }


  
    public function update(MateriaValidate $request, $id)
    {
        $materias=Materia::where('id',$id)->first();
        $materias->nombre_materia=$request->nombre_materia;
        $materias->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function destroy($id)
    {
        $materias=Materia::where('id',$id)->first();
        $materias->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
