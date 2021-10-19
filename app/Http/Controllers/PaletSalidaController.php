<?php

namespace App\Http\Controllers;

use App\Model\LoteIngreso;
use App\Model\PaletSalida;
use App\Model\JabaSalida;
use App\Model\RendimientoPersonal;
use App\Model\EtiquetaCaja;
use App\Model\TransferenciaCaja;
use App\Model\Caja;
use App\Model\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests\PaletSalidaValidate;

class PaletSalidaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('tipo')) {
            $paletSalidas=PaletSalida::select(
                                    'palet_salida.*',
                                    DB::raw('COUNT(caja.id) cajas_contadas'),
                                    'CL.nombre_calibre as calibre',
                                    'MA.nombre_materia as materia',
                                    'PE.peso_neto',
                                    'VA.nombre_variedad as variedad',
                                    'CT.nombre_categoria as categoria',
                                    DB::raw('GROUP_CONCAT(DISTINCT PRE.nombre_presentacion) as presentacion')
                                )
                                ->join('cliente','cliente.id','=','palet_salida.cliente_id')
                                ->join('caja','caja.palet_salida_id','=','palet_salida.id')
                                ->join('etiqueta_caja','etiqueta_caja.id','=','caja.etiqueta_caja_id')
                                ->join('lote_ingreso as LI','LI.id','=','etiqueta_caja.lote_ingreso_id')
                                ->join('calibre as CL','CL.id','=','etiqueta_caja.calibre_id')
                                ->join('categoria as CT','CT.id','=','etiqueta_caja.categoria_id')
                                ->join('presentacion as PE','PE.id','=','etiqueta_caja.presentacion_id')
                                ->join('materia as MA','MA.id','=','LI.materia_id')
                                ->join('variedad as VA','VA.id','=','LI.variedad_id')
                                ->join('presentacion as PRE','PRE.id','=','etiqueta_caja.presentacion_id')
                                ->groupBy('palet_salida.id')
                                ->where('palet_salida.estado','Cerrado')
                                ->where('tipo_palet_id','SAL')
                                ->where('palet_salida.cliente_id',$request->cliente_id)
                                ->get();
            return response()->json($paletSalidas);
        }
        if ($request->has('estado')) {
            $paletSalidas=PaletSalida::join('cliente','cliente.id','=','palet_salida.cliente_id')
                                ->leftJoin('caja','caja.palet_salida_id','=','palet_salida.id')
                                ->leftJoin('etiqueta_caja as EC','caja.etiqueta_caja_id','=','EC.id')
                                ->leftJoin('presentacion as PRE','PRE.id','=','EC.presentacion_id')
                                ->leftJoin('calibre as CAL','CAL.id','=','EC.calibre_id')
                                ->leftJoin('marca_caja as MC','MC.id','=','EC.marca_caja_id')
                                ->leftJoin('lote_ingreso as LO','EC.lote_ingreso_id','=','LO.id')
                                ->leftJoin('parihuela as PAR','palet_salida.parihuela_id','=','PAR.id')
                                ->select(
                                    'palet_salida.*',
                                    'cliente.descripcion as cliente',
                                    'modelo_parihuela as parihuela',
                                    DB::raw('COUNT(caja.id) cajas_contadas'),
                                    DB::raw("GROUP_CONCAT(DISTINCT CONCAT(LO.codigo,' | ',nombre_presentacion,' | ',nombre_calibre,' | ',nombre_marca_caja)) as detalles"),
                                )
                                ->groupBy('palet_salida.id')
                                ->whereIn('palet_salida.estado',explode(',',$request->estado))
                                ->orderBy('palet_salida.updated_at','DESC')
                                ->limit(100)
                                ->get();
        }else{
            $paletSalidas=PaletSalida::join('cliente','cliente.id','=','palet_salida.cliente_id')
                                ->leftJoin('caja','caja.palet_salida_id','=','palet_salida.id')
                                ->select('palet_salida.*','cliente.descripcion as cliente',DB::raw('COUNT(caja.id) cajas_contadas'))
                                ->groupBy('palet_salida.id')
                                ->get();
        }

        return response()->json($paletSalidas);
    }  
    /**
     * La cantidad se Agrega por defecto 0 y va creciendo conforme se escanean Jabas.
     */
    public function store(PaletSalidaValidate $request)
    {
        $siguiente=PaletSalida::select(DB::raw('MAX(numero) numero'))
            ->where('tipo_palet_id',$request->tipo_palet_id)
            ->where('campania_id',$request->campania_id)
            ->where('cliente_id',$request->cliente_id)
            ->first();
        $paletSalida=new PaletSalida();
        $paletSalida->campania_id=$request->campania_id;
        $paletSalida->tipo_palet_id=$request->tipo_palet_id;
        $paletSalida->cliente_id=$request->cliente_id;
        $paletSalida->etapas=$request->etapas;
        $paletSalida->tope_cajas=$request->tope_cajas;
        $paletSalida->nave=1;
        $paletSalida->numero=$siguiente->numero+1;
        $paletSalida->camara=null;
        $paletSalida->estado="Pendiente";
        $paletSalida->parihuela_id=$request->parihuela_id;
        $paletSalida->etiqueta_adicional=$request->etiqueta_adicional;
        $paletSalida->save();
        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }
    
    public function caja_store($id,Request $request){
        // dd($request->all());
        $codigo_palet = $request->codigo_palet;
        $array_palet= explode('-',$codigo_palet);
        
        $caja=new Caja();
        $caja->palet_salida_id=(int)$id;
        $caja->etiqueta_caja_id=$array_palet[1];
        $caja->save();
        $etiqueta_caja=EtiquetaCaja::where('id',$array_palet[1])->first();
        foreach ($request->codigos_trabajador as $key => $codigo) {
            if (!strpos($codigo,"00000000")) {
                $rendimientoPersonal=new RendimientoPersonal();
                $rendimientoPersonal->caja_id=$caja->id;
                $rendimientoPersonal->codigo_barras=$codigo;
                $rendimientoPersonal->codigo_operador=substr($codigo,4,8);
                $rendimientoPersonal->linea=substr($codigo,0,2);
                $rendimientoPersonal->codigo_labor=substr($codigo,2,2);
                $rendimientoPersonal->presentacion_id=$etiqueta_caja->presentacion_id;
                $rendimientoPersonal->fecha_empaque=$etiqueta_caja->fecha_empaque;
                $rendimientoPersonal->save();
                $caja->linea=$rendimientoPersonal->linea;
                $caja->save();
            }
        }

        return response()->json([
            "status" => "OK",
            "data"  => [
                "calibre" => $caja->calibre,
                "categoria" => $caja->categoria,
                "presentacion" => $caja->presentacion,
            ]
        ]);
        
        // $caja->
    }

    

    public function show(Request $request,$id){
        $paletSalida=PaletSalida::with('cajas')
            ->where('palet_salida.id',$id)
            ->select('palet_salida.*')
            ->first();
        if ($request->has('cajas')) {
            $paletSalida->detalle=Caja::where('palet_salida_id',$paletSalida->id)
                ->join('etiqueta_caja as EC','EC.id','=','caja.etiqueta_caja_id')
                ->join('calibre as CL','CL.id','=','EC.calibre_id')
                ->join('categoria as CT','CT.id','=','EC.categoria_id')    
                ->join('presentacion as PE','PE.id','=','EC.presentacion_id')
                ->join('marca_caja as MC','MC.id','=','EC.marca_caja_id')
                ->join('marca_empaque as ME','ME.id','=','EC.marca_empaque_id')
                ->join('tipo_empaque as TE','TE.id','=','EC.tipo_empaque_id')
                ->join('lote_ingreso as LI','LI.id','=','EC.lote_ingreso_id')
                ->join('plu as PLU','PLU.id','=','EC.plu_id')
                ->select(
                    'caja.id',
                    'LI.codigo as codigo_lote',
                    'EC.fecha_empaque',
                    'caja.palet_salida_id',
                    'CL.nombre_calibre',
                    'CT.nombre_categoria',
                    'PE.nombre_presentacion',
                    'TE.nombre_tipo_empaque',
                    'ME.nombre_marca_empaque',
                    'MC.nombre_marca_caja',
                    'PLU.nombre_plu'
                )
                ->get();
        }
        return response()->json($paletSalida);
    }
    public function search(Request $request){
        $paletSalida=PaletSalida::where('tipo_palet_id',$request->tipo_palet_id)
                        ->where('numero',$request->numero)
                        ->where('cliente_id',$request->cliente_id)
                        ->first();
        $paletSalida->detalle=Caja::where('palet_salida_id',$paletSalida->id)
            ->join('etiqueta_caja as EC','EC.id','=','caja.etiqueta_caja_id')
            ->join('calibre as CL','CL.id','=','EC.calibre_id')
            ->join('categoria as CT','CT.id','=','EC.categoria_id')    
            ->join('presentacion as PE','PE.id','=','EC.presentacion_id')
            ->join('marca_caja as MC','MC.id','=','EC.marca_caja_id')
            ->join('marca_empaque as ME','ME.id','=','EC.marca_empaque_id')
            ->join('tipo_empaque as TE','TE.id','=','EC.tipo_empaque_id')
            ->join('lote_ingreso as LI','LI.id','=','EC.lote_ingreso_id')
            ->join('plu as PLU','PLU.id','=','EC.plu_id')
            ->select(
                'caja.id',
                'LI.codigo as codigo_lote',
                'EC.fecha_empaque',
                'caja.palet_salida_id',
                'CL.nombre_calibre',
                'CT.nombre_categoria',
                'PE.nombre_presentacion',
                'TE.nombre_tipo_empaque',
                'ME.nombre_marca_empaque',
                'MC.nombre_marca_caja',
                'PLU.nombre_plu'
            )
            ->get();
            // $paletSalida->detalle=$paletSalida->detalle==null ? [] : $paletSalida->detalle;
        return response()->json($paletSalida);
    }
    public function codigos(Request $request,$id){
        $paletSalida=PaletSalida::join('caja as CA','CA.palet_salida_id','=','palet_salida.id')
                                ->select(DB::raw('GROUP_CONCAT(RP.codigo_barras) codigos'))
                                ->join('rendimiento_personal as RP','RP.caja_id','=','CA.id')
                                ->where('palet_salida.id',$id)
                                ->groupBy('palet_salida.id')
                                ->first();
        return response()->json($paletSalida);
    }

    public function remonte(Request $request){
        

        $paletSalida=new PaletSalida();
        $paletSalida->campania_id=$request->campania_id;
        $paletSalida->tipo_palet_id='TER';
        $paletSalida->cliente_id=$request->cliente_id;
        $paletSalida->etapas=1;
        $paletSalida->nave=1;
        $paletSalida->camara=null;
        $paletSalida->estado="Cerrado";
        $paletSalida->parihuela_id=$request->parihuela_id;
        $paletSalida->etiqueta_adicional=$request->etiqueta_adicional;
        $siguiente=PaletSalida::select(DB::raw('MAX(numero) numero'))
            ->where('tipo_palet_id','TER')
            ->where('campania_id',$request->campania_id)
            ->where('cliente_id',$request->cliente_id)
            ->first();
        $paletSalida->numero=$siguiente->numero+1;
        $paletSalida->fecha_cierre=Carbon::now();
        $paletSalida->save();


        $palets=$request->palets_id;
        
        for ($i=0; $i < count($palets); $i++) { 
            $id=$palets[$i];
            $cajas=Caja::where('palet_salida_id',$id)->get();
            foreach ($cajas as $key => $caja) {
                $caja->palet_salida_id=$paletSalida->id;
                $caja->save();
            }
            $paletSalidaOld=PaletSalida::where('id',$id)->first();
            $paletSalidaOld->estado="Remonte";
            $paletSalidaOld->detalle='Remonte en '.$paletSalida->tipo_palet_id.'-'.$paletSalida->numero;
            $paletSalidaOld->save();
        }

        return response()->json([
            "status" => "OK",
            "data"  => $paletSalida
        ]);
    }

    public function transferencia(Request $request){
        $cajas_id=$request->cajas_id; //ids separados por comas
        $palet_destino_id=$request->palet_destino_id;
        $etiqueta_caja_id=$request->etiqueta_caja_id;
        $cajas=Caja::whereIn('id',explode(',',$cajas_id))->get();
        foreach ($cajas as $key => $caja) {
            $transferencia=new TransferenciaCaja();
            $transferencia->palet_destino_id=$palet_destino_id;
            $transferencia->palet_salida_id=$caja->palet_salida_id;
            $transferencia->etiqueta_caja_id=$caja->etiqueta_caja_id;
            $transferencia->caja_id=$caja->id;
            $transferencia->save();
            $caja->palet_salida_id=$palet_destino_id;
            $caja->etiqueta_caja_id=($etiqueta_caja_id==null)?$caja->etiqueta_caja_id : $etiqueta_caja_id;
            $caja->save();
        }
        return response()->json([
            "status" => "OK"
        ]);
    }

    public function update(Request $request,$id){
        switch ($request->estado) {
            case 'Cerrado':
                $paletSalida=PaletSalida::where('id',$id)->first();
                // $conteo=PaletSalida::whereIn('estado',['Cerrado','Frio','Remonte'])
                //     ->where('tipo_palet_id',$paletSalida->tipo_palet_id)
                //     ->where('campania_id',$paletSalida->campania_id)
                //     ->where('cliente_id',$paletSalida->cliente_id)
                //     ->count();
                // $paletSalida->numero=$conteo+1;
                $paletSalida->estado=$request->estado;
                $paletSalida->fecha_cierre=Carbon::now();
                $paletSalida->save();
                break;
            
            case 'Frio':
                $paletSalida=PaletSalida::where('id',$id)->first();
                $paletSalida->estado=$request->estado;
                $paletSalida->camara=$request->camara;
                $paletSalida->save();
                break;

            default:
                $paletSalida=PaletSalida::where('id',$id)->first();
                $paletSalida->estado=$request->estado;
                $paletSalida->save();
                break;
        }
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
