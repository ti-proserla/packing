<?php

namespace App\Http\Controllers;
use App\Http\Requests\PresentacionValidate;
use App\Model\Presentacion;
use Illuminate\Http\Request;

class PresentacionController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $Presentaciones=Presentacion::all();
        }else {
            $Presentaciones=Presentacion::paginate(10);
        }
        return response()->json($Presentaciones);
    }

    public function store(PresentacionValidate $request)
    {
        $Presentaciones=new Presentacion();
        $Presentaciones->nombre_presentacion=$request->nombre_presentacion;
        $Presentaciones->peso_neto=$request->peso_neto;
        $Presentaciones->tope_cajas=$request->tope_cajas;
        $Presentaciones->save();

        return response()->json([
            "status" => "OK"
        ]);

    }

    public function show($id)
    {
        $Presentaciones=Presentacion::where('id',$id)->first();
        return response()->json($Presentaciones);
    }


    
    public function update(PresentacionValidate $request, $id)
    {
        $Presentaciones=Presentacion::where('id',$id)->first();
        $Presentaciones->nombre_presentacion=$request->nombre_presentacion;
        $Presentaciones->peso_neto=$request->peso_neto;
        $Presentaciones->tope_cajas=$request->tope_cajas;
        $Presentaciones->save();
        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $Presentaciones=Presentacion::where('id',$id)->first();
        $Presentaciones->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
