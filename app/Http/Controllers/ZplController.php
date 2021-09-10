<?php

namespace App\Http\Controllers;
use App\Model\zpl;
use App\Http\Requests\zplValidate;
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
            $zpls=zpl::all();
        }else{
            $zpls=zpl::paginate(10);
        }
        return response()->json($zpls);
    }

    public function variedad(){
        $zpls=zpl::with('variedad')->get();
        return response()->json($zpls);
    }
    
    public function detallado(){
        $zpls=zpl::with('variedad')
                            ->with('tipo')
                            ->with('calibre')
                            ->get();
        return response()->json($zpls);
    }

   
    public function store(zplValidate $request)
    {
        $zpls=new zpl();
        $zpls->cod_cartilla=$request->cod_cartilla;
        $zpls->nombre_zpl=$request->nombre_zpl;
        $zpls->save();
        
        return response()->json([
            "status" => "OK"
        ]);
        
    }
    
    
    
    public function show($id)
    {
        $zpls=zpl::where('id',$id)->first();
        return response()->json($zpls);
    }
    
    
    
    public function update(zplValidate $request, $id)
    {
        $zpls=zpl::where('id',$id)->first();
        $zpls->cod_cartilla=$request->cod_cartilla;
        $zpls->nombre_zpl=$request->nombre_zpl;
        $zpls->save();

        return response()->json([
            "status" => "OK"
        ]);
    }


    public function destroy($id)
    {
        $zpls=zpl::where('id',$id)->first();
        $zpls->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
