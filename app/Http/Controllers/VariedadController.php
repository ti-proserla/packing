<?php

namespace App\Http\Controllers;
use App\Http\Requests\VariedadValidate;
use App\Model\Variedad;
use Illuminate\Http\Request;

class VariedadController extends Controller
{
   
    public function index()
    {
        $variedades=Variedad::all();
        return response()->json($variedades);
    }

    public function store(VariedadValidate $request)
    {
        $variedades=new Variedad();
        $variedades->nombre_variedad=$request->nombre_variedad;
        $variedades->materia_id=$request->materia_id;
        $variedades->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $variedades=Variedad::where('id',$id)->first();
        return response()->json($variedades);
    }


    
    public function update(VariedadValidate $request, $id)
    {
        $variedades=Variedad::where('id',$id)->first();
        $variedades->nombre_variedad=$request->nombre_variedad;
        $variedades->materia_id=$request->materia_id;
        $variedades->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $variedades=Variedad::where('id',$id)->first();
        $variedades->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
