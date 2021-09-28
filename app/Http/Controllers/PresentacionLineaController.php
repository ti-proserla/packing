<?php

namespace App\Http\Controllers;
use App\Http\Requests\PresentacionLineaValidate;
use App\Model\PresentacionLinea;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresentacionLineaController extends Controller
{
   
    public function index(Request $request)
    {
        if ($request->has('all')) {
            $PresentacionLineas=PresentacionLinea::join('presentacion','presentacion.id','=','presentacion_linea.presentacion_id')
                                ->select('presentacion_linea.*','presentacion.nombre_presentacion')
                                ->get();
        }else {
            $PresentacionLineas=PresentacionLinea::join('presentacion','presentacion.id','=','presentacion_linea.presentacion_id')
                                ->select('presentacion_linea.*','presentacion.nombre_presentacion')
                                ->orderBy('linea_id','ASC')
                                ->where('fecha_ref',$request->fecha_proceso)
                                ->paginate(10);
        }
        return response()->json($PresentacionLineas);
    }

    public function store(PresentacionLineaValidate $request)
    {
        $PresentacionLinea=new PresentacionLinea();
        $PresentacionLinea->presentacion_id=$request->presentacion_id;
        $PresentacionLinea->linea_id=$request->linea_id;
        $PresentacionLinea->fecha_ref=$request->fecha_ref;
        $PresentacionLinea->inicio=Carbon::now();
        $PresentacionLinea->save();
        return response()->json([
            "status" => "OK"
        ]);

    }
    public function cerrar(Request $request,$id){
        $PresentacionLinea=PresentacionLinea::where('id',$id)->first();
        $PresentacionLinea->fin=Carbon::now();
        $PresentacionLinea->save();
        return response()->json([
            "status" => "OK"
        ]);
    }

    public function show($id)
    {
        $PresentacionLineaes=PresentacionLinea::where('id',$id)->first();
        return response()->json($PresentacionLineaes);
    }


    
    public function update(PresentacionLineaValidate $request, $id)
    {
        $PresentacionLineaes=PresentacionLinea::where('id',$id)->first();
        $PresentacionLineaes->nombre_presentacion=$request->nombre_presentacion;
        $PresentacionLineaes->peso_neto=$request->peso_neto;
        $PresentacionLineaes->tope_cajas=$request->tope_cajas;
        $PresentacionLineaes->save();
        return response()->json([
            "status" => "OK"
        ]);
    }

    
    public function destroy($id)
    {
        $PresentacionLineaes=PresentacionLinea::where('id',$id)->first();
        $PresentacionLineaes->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
