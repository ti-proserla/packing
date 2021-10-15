<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\EtiquetaCaja;
use Illuminate\Support\Facades\DB;

class EtiquetaCajaController extends Controller
{
    public function index(Request $request){
        $etiquetaCaja=EtiquetaCaja::select(
                            DB::raw('CONCAT("C-",etiqueta_caja.id) codigo_caja'),
                            'etiqueta_caja.*',
                            'LI.codigo',
                            'LI.codigo as codigo_lote',
                            'CLI.descripcion as nombre_cliente',
                            'CL.nombre_calibre',
                            'MA.nombre_materia',
                            'PE.nombre_presentacion',
                            'VA.nombre_variedad',
                            'MAC.nombre_marca_caja',
                            'TIE.nombre_tipo_empaque',
                            'MAE.nombre_marca_empaque',
                            'plu.nombre_plu',
                            'CT.nombre_categoria')
                        ->join('calibre as CL','CL.id','=','etiqueta_caja.calibre_id')
                        ->join('categoria as CT','CT.id','=','etiqueta_caja.categoria_id')
                        ->join('lote_ingreso as LI','LI.id','=','etiqueta_caja.lote_ingreso_id')
                        ->join('cliente as CLI','CLI.id','=','LI.cliente_id')
                        ->join('presentacion as PE','PE.id','=','etiqueta_caja.presentacion_id')
                        ->join('materia as MA','MA.id','=','LI.materia_id')
                        ->join('variedad as VA','VA.id','=','LI.variedad_id')
                        ->join('marca_caja as MAC','MAC.id','=','etiqueta_caja.marca_caja_id')
                        ->join('tipo_empaque as TIE','TIE.id','=','etiqueta_caja.tipo_empaque_id')
                        ->join('marca_empaque as MAE','MAE.id','=','etiqueta_caja.marca_empaque_id')
                        ->join('plu','plu.id','=','etiqueta_caja.plu_id')
                        ->where('CLI.id',$request->productor_id)
                        ->where('etiqueta_caja.fecha_empaque',$request->fecha_empaque)
                        ->whereIn('etiqueta_caja.estado',explode(',',$request->estado))
                        ->orderBy('id','DESC');
        if ($request->has('all')) {
            $etiquetaCaja=$etiquetaCaja->get();
        }else{
            $etiquetaCaja=$etiquetaCaja->paginate(10);
        }
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
    public function update(Request $request,$id){
        $etiquetaCaja=EtiquetaCaja::where('id',$id)->first();
        $etiquetaCaja->estado=$request->estado;
        $etiquetaCaja->save();
        return response()->json([
            "status" => "OK"
        ]);

    }
}
