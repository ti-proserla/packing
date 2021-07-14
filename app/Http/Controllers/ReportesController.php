<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function cantidad_por_linea(Request $request){
        $query="SELECT 	lote_ingreso.id,
                        lote_ingreso.codigo,
                        cliente.descripcion cliente, 
                        date(lote_ingreso.created_at) fecha_produccion,
                        linea, 
                        COUNT(DISTINCT(jaba_salida.numero),palet_salida.id) cantidad
                FROM lote_ingreso
                INNER JOIN cliente on cliente.id=lote_ingreso.cliente_id
                INNER JOIN palet_salida on lote_ingreso.id = palet_salida.lote_id 
                INNER JOIN jaba_salida on palet_salida.id = jaba_salida.palet_salida_id
                WHERE date(lote_ingreso.created_at)=?
                GROUP BY lote_ingreso.id,linea,lote_ingreso.created_at";
        $data=DB::select(DB::raw("$query"),[$request->fecha_produccion]);      
        return response()->json($data);  
    }
    public function lote(Request $request){
        $query="SELECT 
        LT.id,
        MA.nombre_materia,
        VA.nombre_variedad,
        LT.codigo codigo_lote,
        INGRESO.*,
        SALIDA.*
        FROM lote_ingreso LT 
        INNER JOIN materia MA on MA.id=LT.materia_id
        INNER JOIN variedad VA on VA.id=LT.variedad_id
        LEFT JOIN (
        SELECT  SL.lote_id,
                        GROUP_CONCAT( 
                                DISTINCT SL.guia 
                                ORDER BY SL.guia ASC 
                                SEPARATOR ' \\n' ) guias,
                        SUM(PE.peso) as peso_total_ingreso 
        FROM sub_lote SL
        INNER JOIN palet_entrada PE on SL.id=PE.sub_lote_id
        GROUP BY lote_id
        ) INGRESO ON INGRESO.lote_id=LT.id
        LEFT JOIN (
        SELECT 
                        PS.lote_id,
                        COUNT(DISTINCT(PS.id)) palets,
                        PS.producto_id,
                        PR.nombre_producto,
                        PR.peso_neto,
                        PR.potes potes_x_caja,
                        SUM(cantidad) cantidad_cajas,
                        SUM(cantidad*PR.potes*PR.peso_neto)/1000 peso_total_salida
        FROM palet_salida PS
        INNER JOIN producto PR ON PR.id=PS.producto_id
        GROUP BY PS.lote_id, PS.producto_id
        ) SALIDA on SALIDA.lote_id=LT.id
        where date(LT.created_at)=?
        ";
        $data=DB::select(DB::raw("$query"),[$request->fecha_produccion]);      
        return response()->json($data);  
    }
    public function rendimiento_personal(Request $request){
        $query="SELECT 
                        js.codigo_operador,
                        op.nom_operador,
                        op.ape_operador,
                        COUNT(*) conteo
                FROM jaba_salida js 
                INNER JOIN palet_salida ps ON ps.id=js.palet_salida_id
                INNER JOIN db_asistencia_produccion.operador op ON op.dni=js.codigo_operador
                WHERE ps.fecha=?
                GROUP BY js.codigo_operador
                ORDER BY conteo DESC";
        $data=DB::select(DB::raw("$query"),[$request->fecha_produccion]);      
        return response()->json($data);  
    }
    public function acopio(Request $request){
        $query="SELECT 
        CL.descripcion cliente,
        FU.nombre_fundo fundo,
        PA.nombre_parcela parcela,
        SL.viaje,
        SL.guia,
        WEEK(SL.fecha_recepcion) semana,
        DATE(SL.fecha_recepcion) fecha_recepcion,
        CONCAT(HOUR(SL.fecha_recepcion),':',MINUTE(SL.fecha_recepcion)) hora_ingreso,
        fecha_proceso,
        MA.nombre_materia materia,
        VA.nombre_variedad variedad,
        TI.nombre_tipo tipo,
        LI.codigo lote_materia,
        SUM(PE.num_jabas) numero_jabas,
        SUM(PE.peso-PE.peso_palet)/SUM(PE.num_jabas) peso_promedio_jaba,
        SL.peso_guia,
        SUM(PE.peso-PE.peso_palet) peso_neto,
        SUM(PE.peso-PE.peso_palet) peso_neto_proceso,
        SL.descarte_granos,
        SL.descarte_racimos,
        SL.descarte_granos+SL.descarte_racimos total_descarte,
        (SL.descarte_granos+SL.descarte_racimos)/SUM(PE.peso-PE.peso_palet) descarte_porcentaje,
        SL.cantidad_jabas_descarte
FROM lote_ingreso LI
LEFT JOIN sub_lote SL ON LI.id = SL.lote_id
INNER JOIN fundo FU ON FU.id = LI.fundo_id
LEFT JOIN parcela PA ON PA.id = LI.parcela_id 
INNER JOIN cliente CL ON CL.id=LI.cliente_id
INNER JOIN materia MA ON MA.id=LI.materia_id
INNER JOIN variedad VA ON VA.id=LI.variedad_id
INNER JOIN tipo TI ON TI.id=LI.tipo_id
LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
GROUP BY LI.id, SL.id";
        $data=DB::select(DB::raw("$query"),[]);      
        return response()->json($data);  
    }
}
