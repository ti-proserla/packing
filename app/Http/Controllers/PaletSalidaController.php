<?php

namespace App\Http\Controllers;

use App\Model\LoteIngreso;
use App\Model\PaletSalida;
use App\Model\JabaSalida;
use App\Model\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaletSalidaController extends Controller
{
    public function index($sub_lote_id)
    {
        $paletSalidas=PaletSalida::all();
        return response()->json($paletSalidas);
    }  
    /**
     * La cantidad se Agrega por defecto 0 y va creciendo conforme se escanean Jabas.
     */
    public function store(Request $request)
    {
        $paletSalida=new PaletSalida();
        $paletSalida->cliente_id=$request->cliente_id;
        $paletSalida->estado="Pendiente";
        $paletSalida->save();
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }
    
    public function caja_store(Request $request,$id){
        // dd($request->all());
        $codigo_palet = $request->codigo_palet;
        $array_palet= explode('-',$codigo_palet);
        // dd($array_palet);
        $caja=new Caja();
        $caja->palet_salida_id=$id;
        $caja->calibre=$array_palet[2];
        $caja->categoria=$array_palet[3];
        $caja->presentacion=$array_palet[4];
        $caja->marca_caja=$array_palet[5];
        $caja->plu=$array_palet[6];
        $caja->tipo_bolsa=$array_palet[7];
        $caja->marca_bolsa=$array_palet[8];
        $caja->save();
        return response()->json([
            "status" => "OK",
            "data"  => $caja
        ]);
        
        // $caja->
    }

    public function show(Request $request,$id){
        $paletSalida=PaletSalida::with('cajas')
                                // ->join('producto','palet_salida.producto_id','=','producto.id')
                                ->where('palet_salida.id',$id)
                                ->select('palet_salida.*')
                                ->first();
        return response()->json($paletSalida);
    }

    public function update(Request $request,$id){
        $paletSalida=PaletSalida::where('id',$id)->first();
        $paletSalida->estado="Cerrado";
        $paletSalida->save();
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }

    public function destroy($id){
        $paletSalida=PaletSalida::where('id',$id)->first();
        $jabas=JabaSalida::where('palet_salida_id',$paletSalida->id)->get();
        foreach ($jabas as $key => $jaba) {
            $jaba->delete();
        }
        $paletSalida->delete();
        return response()->json([
            "status" => "OK"
        ]);
    }

}
