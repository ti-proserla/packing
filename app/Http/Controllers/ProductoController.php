<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProductoValidate;
use Illuminate\Http\Request;
use App\Model\Producto;

class ProductoController extends Controller
{

    public function index(Request $request)
    {
        if ($request->has('all')) {
            $productos=Producto::all();
        }else {
            $productos=Producto::paginate(10);
        }
        return response()->json($productos);
    }


    public function store(ProductoValidate $request)
    {
        $producto=new Producto();
        $producto->nombre_producto=$request->nombre_producto;
        $producto->peso_bruto=$request->peso_bruto;
        $producto->peso_pote=$request->peso_pote;
        $producto->potes=$request->potes;
        $producto->save();
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
        $producto=Producto::where('id',$id)->first();
        $producto->nombre_producto=$request->nombre_producto;
        $producto->peso_bruto=$request->peso_bruto;
        $producto->peso_pote=$request->peso_pote;
        $producto->potes=$request->potes;
        $producto->save();
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
