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
                        RP.codigo_operador,
                        op.nom_operador,
                        op.ape_operador,
                        COUNT(*) conteo,
                        LA.descripcion labor,
                        EC.fecha_empaque
                FROM caja CA 
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN lote_ingreso LI on LI.id=EC.lote_ingreso_id
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN (SELECT * FROM labor group by codigo_labor) LA ON LA.codigo_labor=RP.codigo_labor
                LEFT JOIN db_asistencia_produccion.operador op ON op.dni=RP.codigo_operador
                WHERE fecha_empaque=?
                GROUP BY RP.codigo_operador,EC.fecha_empaque, RP.codigo_labor
                ORDER BY conteo DESC";
        $data=DB::select(DB::raw("$query"),[$request->fecha_produccion]);      
        return response()->json($data);  
    }
    public function acopio(Request $request){
        $cliente_id=$request->cliente_id;
        $desde=$request->desde;
        $hasta=$request->hasta;
        $queryProductor="";
        $query="SELECT 
                        CL.descripcion nombre_productor,
                        FU.nombre_fundo,
                        FU.lugar_produccion lugar_produccion,
                        SL.viaje,
                        SL.guia,
                        FU.cod_lugar_produccion cod_lugar_produccion,
                        WEEK(SL.fecha_recepcion) semana,
                        DATE(SL.fecha_recepcion) fecha_recepcion,
                        CONCAT(HOUR(SL.fecha_recepcion),':',MINUTE(SL.fecha_recepcion)) hora_ingreso,
                        LI.fecha_proceso,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        LI.codigo lote_materia,
                        SUM(PE.num_jabas) numero_jabas,
                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)/SUM(PE.num_jabas) peso_promedio_jaba,
                        SL.peso_guia,
                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) peso_neto,
                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) peso_neto_proceso,
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
                LEFT JOIN tipo TI ON TI.id=LI.tipo_id
                LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
                where CL.id=?
                AND DATE(SL.fecha_recepcion)>=?
                AND DATE(SL.fecha_recepcion)<=?
                GROUP BY LI.id, SL.id";
        $data=DB::select(DB::raw("$query"),[$cliente_id,$desde,$hasta]);   

        return response()->json($data);  
    }

    public function avance_lote(Request $request){
        $fecha_produccion=$request->fecha_produccion;
        $query="SELECT 	LI.codigo,
                        CL.descripcion,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        SUM(PE.peso-PE.peso_palet-PE.peso_jaba*PE.num_jabas) peso_entrada,
                        SUM(PE.num_jabas) num_jabas_entrada,
                        SUM(IF(PE.estado='LANZADO',PE.peso-PE.peso_palet-PE.peso_jaba*PE.num_jabas,0)) peso_lanzado,
                        SUM(IF(PE.estado='LANZADO',PE.num_jabas,0)) num_jabas_lanzadas
                FROM lote_ingreso LI 
                INNER JOIN materia MA ON MA.id=LI.materia_id
                INNER JOIN variedad VA ON VA.id=LI.variedad_id
                INNER JOIN cliente CL ON CL.id=LI.cliente_id
                LEFT JOIN sub_lote SL ON SL.lote_id=LI.id
                LEFT JOIN palet_entrada PE ON PE.sub_lote_id=SL.id";
        $data=DB::select(DB::raw("$query"),[]);      
        return response()->json($data);
    }

    public function avance_personal(){
        $query="SELECT RP.linea,RP.codigo_labor,L.descripcion,COUNT(RP.id) contador
                FROM rendimiento_personal RP
                INNER JOIN (SELECT * FROM labor GROUP BY codigo_labor)
                L ON L.codigo_labor=RP.codigo_labor
                GROUP BY RP.linea,RP.codigo_labor";
        $data=DB::select(DB::raw("$query"),[]);      
        return response()->json($data);
    }
}
