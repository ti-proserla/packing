<?php

namespace App\Http\Controllers;
use App\Http\Requests\CategoriaValidate;
use App\Model\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $Categoriaes=Categoria::all();
        }else {
            $Categoriaes=Categoria::paginate(10);
        }
        return response()->json($Categoriaes);
    }

    public function store(CategoriaValidate $request)
    {
        $Categoriaes=new Categoria();
        $Categoriaes->nombre_categoria=$request->nombre_categoria;
        $Categoriaes->materia_id=$request->materia_id;
        $Categoriaes->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $Categoriaes=Categoria::where('id',$id)->first();
        return response()->json($Categoriaes);
    }


    
    public function update(CategoriaValidate $request, $id)
    {
        $Categoriaes=Categoria::where('id',$id)->first();
        $Categoriaes->nombre_categoria=$request->nombre_categoria;
        $Categoriaes->materia_id=$request->materia_id;
        $Categoriaes->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $Categoriaes=Categoria::where('id',$id)->first();
        $Categoriaes->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
