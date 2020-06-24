<?php

namespace App\Http\Controllers;
use App\Http\Requests\TransportistaValidate;
use Illuminate\Http\Request;
use App\Model\Transportista;

class TransportistaController extends Controller
{
 
    public function index()
    {
        $transportistas=Transportista::all();
        return response()->json($transportistas);
    }


    public function store(TransportistaValidate $request)
    {
        $transportistas=new transportista();
        $trasportistas->documento=$request->documento;
        $trasportistas->nombre_transportista=$request->nombre_transportista;
        $transportistas->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function show($id)
    {
        $transportistas=Transportista::where('id',$id)->first();
        return response()->json($transportistas);
    }

 

  
    public function update(TransportistaValidate $request, $id)
    {
        $transportistas=Transportista::where('id',$id)->first();
        $trasportistas->documento=$request->documento;
        $trasportistas->nombre_transportista=$request->nombre_transportista;
        $transportistas->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $transportistas=Transportista::where('id',$id)->first();
        $transportistas->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
