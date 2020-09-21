<?php

namespace App\Http\Controllers;

use App\Model\PaletSalida;
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
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }
    
    public function show(Request $request,$id){
        $paletSalida=PaletSalida::with('jabas')
                                ->where('id',$id)
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
}
