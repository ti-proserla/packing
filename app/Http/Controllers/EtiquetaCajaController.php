<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\EtiquetaCaja;
use Illuminate\Support\Facades\DB;

class EtiquetaCajaController extends Controller
{
    public function index(){
        $etiquetaCaja=EtiquetaCaja::select(
                            DB::raw('CONCAT("C-",etiqueta_caja.id) codigo_caja'),
                            'etiqueta_caja.*',
                            'LI.codigo',
                            'CLI.descripcion as nombre_cliente',
                            'CL.nombre_calibre',
                            'MA.nombre_materia',
                            'VA.nombre_variedad',
                            'CT.nombre_categoria')
                        ->join('calibre as CL','CL.id','=','etiqueta_caja.calibre_id')
                        ->join('categoria as CT','CT.id','=','etiqueta_caja.categoria_id')
                        ->join('lote_ingreso as LI','LI.id','=','etiqueta_caja.lote_ingreso_id')
                        ->join('cliente as CLI','CLI.id','=','LI.cliente_id')
                        ->join('presentacion as PE','PE.id','=','etiqueta_caja.presentacion_id')
                        ->join('materia as MA','MA.id','=','LI.materia_id')
                        ->join('variedad as VA','VA.id','=','LI.variedad_id')
                        ->orderBy('id','DESC')->paginate(10);
        return response()->json($etiquetaCaja);
    }

    public function store(Request $request){
        $etiquetaCaja=new EtiquetaCaja();
        $etiquetaCaja->lote_ingreso_id=$request->lote_id;
        $etiquetaCaja->fecha_empaque=$request->fecha_empaque;
        $etiquetaCaja->categoria_id=$request->categoria_id;
        $etiquetaCaja->calibre_id=$request->calibre_id;
        $etiquetaCaja->presentacion_id=$request->presentacion_id;
        $etiquetaCaja->marca_caja_id=$request->marca_caja_id;
        $etiquetaCaja->tipo_empaque_id=$request->tipo_empaque_id;
        $etiquetaCaja->marca_empaque_id=$request->marca_empaque_id;
        $etiquetaCaja->plu_id=$request->plu_id;
        $etiquetaCaja->save();
        return response()->json([
            "status" => "OK"
        ]);
    }
}
