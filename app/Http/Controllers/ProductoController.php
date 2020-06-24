<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProductoValidate;
use Illuminate\Http\Request;
use App\Model\Producto;

class ProductoController extends Controller
{

    public function index()
    {
        $productos=Producto::all();
        return response()->json($productos);
    }


    public function store(ProductoValidate $request)
    {
        $productos=new Producto();
        $productos->nombre_producto=$request->nombre_producto;
        $productos->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    public function show($id)
    {
        $productos=Producto::where('id',$id)->first();
        return response()->json($productos);
    }




    public function update(ProductoValidate $request, $id)
    {
        $produtos=Producto::where('id',$id)->first();
        $productos->nombre_producto=$request->nombre_producto;
        $productos->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $productos=Producto::where('id',$id)->first();
        $productos->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
