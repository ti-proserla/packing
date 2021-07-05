<?php

namespace App\Http\Controllers;

use App\Model\LoteIngreso;
use App\Model\PaletSalida;
use App\Model\JabaSalida;
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
        $palet_contar=(PaletSalida::select(DB::raw('count(id) contar'))
                            ->where('lote_id',$request->lote_id)
                            ->first()->contar)+1;
        $paletSalida=new PaletSalida();
        $paletSalida->lote_id=$request->lote_id;
        $paletSalida->producto_id=$request->producto_id;
        $paletSalida->proceso_id=1;
        $paletSalida->numero=$palet_contar;
        $paletSalida->fecha=Carbon::now();
        $paletSalida->estado="Abierto";
        $paletSalida->save();
        $lote=LoteIngreso::where('id',$request->lote_id)->first();
        $paletSalida->fecha=$lote->fecha_cosecha;
        $paletSalida->save();
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }
    
    public function show(Request $request,$id){
        $paletSalida=PaletSalida::with('jabas')
                                ->join('producto','palet_salida.producto_id','=','producto.id')
                                ->where('palet_salida.id',$id)
                                ->select('palet_salida.*','producto.etapas')
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
