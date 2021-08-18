<?php

namespace App\Http\Controllers;
use App\Http\Requests\MarcaEmpaqueValidate;
use App\Model\MarcaEmpaque;
use Illuminate\Http\Request;

class MarcaEmpaqueController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $marcaEmpaques=MarcaEmpaque::all();
        }else {
            $marcaEmpaques=MarcaEmpaque::paginate(10);
        }
        return response()->json($marcaEmpaques);
    }

    public function store(MarcaEmpaqueValidate $request)
    {
        $marcaEmpaques=new MarcaEmpaque();
        $marcaEmpaques->nombre_marca_empaque=$request->nombre_marca_empaque;
        $marcaEmpaques->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $marcaEmpaques=MarcaEmpaque::where('id',$id)->first();
        return response()->json($marcaEmpaques);
    }


    
    public function update(MarcaEmpaqueValidate $request, $id)
    {
        $marcaEmpaques=MarcaEmpaque::where('id',$id)->first();
        $marcaEmpaques->nombre_marca_empaque=$request->nombre_marca_empaque;
        $marcaEmpaques->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $marcaEmpaques=MarcaEmpaque::where('id',$id)->first();
        $marcaEmpaques->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
