<?php

namespace App\Http\Controllers;
use App\Http\Requests\ImpresoraValidate;
use App\Model\Impresora;
use Illuminate\Http\Request;

class ImpresoraController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $impresoras=Impresora::all();
        }else {
            $impresoras=Impresora::paginate(10);
        }
        return response()->json($impresoras);
    }

    public function store(ImpresoraValidate $request)
    {
        $impresora=new Impresora();
        $impresora->ip=$request->ip;
        $impresora->nombre=$request->nombre;
        $impresora->estado='Activo';
        $impresora->save();
        return response()->json([
            "status" => "OK"
        ]);
    }

    public function show($id)
    {
        $impresora=Impresora::where('id',$id)->first();
        return response()->json($impresora);
    }
    
    public function update(ImpresoraValidate $request, $id)
    {
        $impresora=Impresora::where('id',$id)->first();
        $impresora->ip=$request->ip;
        $impresora->nombre=$request->nombre;
        $impresora->save();

        return response()->json([
            "status" => "OK"
        ]);
    }
}
