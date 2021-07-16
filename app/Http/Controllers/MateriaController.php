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
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $materias=Materia::all();
        }else{
            $materias=Materia::paginate(10);
        }
        return response()->json($materias);
    }

    public function variedad(){
        $materias=Materia::with('variedad')->get();
        return response()->json($materias);
    }
    
    public function detallado(){
        $materias=Materia::with('variedad')
                            ->with('tipo')
                            ->get();
        return response()->json($materias);
    }

   
    public function store(MateriaValidate $request)
    {
        $materias=new Materia();
        $materias->cod_cartilla=$request->cod_cartilla;
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
        $materias->cod_cartilla=$request->cod_cartilla;
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
