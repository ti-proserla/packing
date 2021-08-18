<?php

namespace App\Http\Controllers;
use App\Http\Requests\TipoEmpaqueValidate;
use App\Model\TipoEmpaque;
use Illuminate\Http\Request;

class TipoEmpaqueController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $TipoEmpaques=TipoEmpaque::all();
        }else {
            $TipoEmpaques=TipoEmpaque::paginate(10);
        }
        return response()->json($TipoEmpaques);
    }

    public function store(TipoEmpaqueValidate $request)
    {
        $TipoEmpaques=new TipoEmpaque();
        $TipoEmpaques->nombre_tipo_empaque=$request->nombre_tipo_empaque;
        $TipoEmpaques->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $TipoEmpaques=TipoEmpaque::where('id',$id)->first();
        return response()->json($TipoEmpaques);
    }


    
    public function update(TipoEmpaqueValidate $request, $id)
    {
        $TipoEmpaques=TipoEmpaque::where('id',$id)->first();
        $TipoEmpaques->nombre_tipo_empaque=$request->nombre_tipo_empaque;
        $TipoEmpaques->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $TipoEmpaques=TipoEmpaque::where('id',$id)->first();
        $TipoEmpaques->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
