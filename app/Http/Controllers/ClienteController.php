<?php

namespace App\Http\Controllers;
use App\Model\Cliente;
use App\Http\Requests\ClienteValidate;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('proceso')) {
            $clientes=Cliente::join('lote_ingreso as L','L.cliente_id','=','cliente.id')
                        ->where('L.estado','Pendiente')
                        ->select('cliente.*')
                        ->groupBy('cliente.id')
                        ->get();
            return response()->json($clientes);
        }
        if ($request->has('all')) {
            $clientes=Cliente::all();
        }else{
            $clientes=Cliente::paginate(10); 
        }
        return response()->json($clientes);
    }

 


    public function store(ClienteValidate $request)
    {
        $clientes=new Cliente();
        $clientes->ruc=$request->ruc;
        $clientes->descripcion=$request->descripcion;
        $clientes->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

   
    public function show($id)
    {
        $clientes=Cliente::where('id',$id)->first();
        return response()->json($clientes);
    }

 
    public function update(ClienteValidate $request, $id)
    {
        $clientes=Cliente::where('id',$id)->first();
        $clientes->ruc=$request->ruc;
        $clientes->descripcion=$request->descripcion;
        $clientes->save();

        return response()->json([
            "status" => "OK"
        ]);
    }

 
    public function destroy($id)
    {
        $clientes=Cliente::where('id',$id)->first();
        $clientes->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
