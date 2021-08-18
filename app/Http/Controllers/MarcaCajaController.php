<?php

namespace App\Http\Controllers;
use App\Http\Requests\MarcaCajaValidate;
use App\Model\MarcaCaja;
use Illuminate\Http\Request;

class MarcaCajaController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $marcaCajas=MarcaCaja::all();
        }else {
            $marcaCajas=MarcaCaja::paginate(10);
        }
        return response()->json($marcaCajas);
    }

    public function store(MarcaCajaValidate $request)
    {
        $marcaCajas=new MarcaCaja();
        $marcaCajas->nombre_marca_caja=$request->nombre_marca_caja;
        $marcaCajas->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $marcaCajas=MarcaCaja::where('id',$id)->first();
        return response()->json($marcaCajas);
    }


    
    public function update(MarcaCajaValidate $request, $id)
    {
        $marcaCajas=MarcaCaja::where('id',$id)->first();
        $marcaCajas->nombre_marca_caja=$request->nombre_marca_caja;
        $marcaCajas->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $marcaCajas=MarcaCaja::where('id',$id)->first();
        $marcaCajas->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
