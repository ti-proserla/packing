<?php

namespace App\Http\Controllers;
use App\Model\Zpl;
use App\Http\Requests\ZplValidate;
use Illuminate\Http\Request;

class ZplController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $zpls=Zpl::all();
        }else{
            $zpls=Zpl::paginate(10);
        }
        return response()->json($zpls);
    }
   
    public function store(ZplValidate $request)
    {
        $zpls=new Zpl();
        $zpls->nombre_zpl=$request->nombre_zpl;
        $zpls->contenido=$request->contenido;
        $zpls->tipo=$request->tipo;
        $zpls->save();
        
        return response()->json([
            "status" => "OK"
        ]);
        
    }
    
    
    
    public function show($id)
    {
        $zpls=Zpl::where('id',$id)->first();
        return response()->json($zpls);
    }
    
    
    
    public function update(ZplValidate $request, $id)
    {
        $zpls=Zpl::where('id',$id)->first();
        $zpls->nombre_zpl=$request->nombre_zpl;
        $zpls->contenido=$request->contenido;
        $zpls->tipo=$request->tipo;
        $zpls->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function destroy($id)
    {
        $zpls=Zpl::where('id',$id)->first();
        $zpls->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
