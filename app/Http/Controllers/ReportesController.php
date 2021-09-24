<?php

namespace App\Http\Controllers;

use App\Exports\GeneralExcel;
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
        $fecha_produccion=$request->fecha_produccion;
        $query_labor=($request->has('codigo_labor')) ? "AND RP.codigo_labor=".$request->codigo_labor : "";
        $query="SELECT 
                        RP.codigo_operador,
                        op.nom_operador,
                        op.ape_operador,
                        COUNT(*) conteo,
                        LA.descripcion labor,
                        EC.fecha_empaque,
                        DATE_FORMAT(MIN(RP.created_at),'%H:%i') primera_lectura,
                        DATE_FORMAT(MAX(RP.created_at),'%H:%i') ultima_lectura,				 
                        TIMESTAMPDIFF(MINUTE, MIN(RP.created_at), MAX(RP.created_at))/60 diferencia
                FROM caja CA 
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN lote_ingreso LI on LI.id=EC.lote_ingreso_id
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN (SELECT * FROM labor group by codigo_labor) LA ON LA.codigo_labor=RP.codigo_labor
                LEFT JOIN db_asistencia_produccion.operador op ON op.dni=RP.codigo_operador
                WHERE fecha_empaque=?
                $query_labor
                GROUP BY RP.codigo_operador,EC.fecha_empaque, RP.codigo_labor
                ORDER BY conteo DESC";
        $data=DB::select(DB::raw("$query"),[$fecha_produccion]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Rendimiento Personal $fecha_produccion.xlsx");
        }else{
            return response()->json($data);  
        }   
    }

    public function rendimiento_personal_presentacion(Request $request){
        $query="SELECT EC.fecha_empaque,RP.codigo_operador,ASIS.nom_operador,ASIS.ape_operador,PRE.nombre_presentacion,LA.descripcion nombre_labor,ASIS.horas, COUNT(CA.id) num_cajas
                FROM caja CA
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                INNER JOIN labor LA ON LA.codigo_labor=RP.codigo_labor
                INNER JOIN (
                    SELECT op.dni,op.nom_operador,op.ape_operador,ma.fecha_ref,ta.labor_id, SUM(TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) horas 
                    FROM db_asistencia_produccion.operador op 
                    INNER JOIN db_asistencia_produccion.marcador ma ON op.dni=ma.codigo_operador
                    INNER JOIN (
                            SELECT * FROM db_asistencia_produccion.tareo 
                            GROUP BY codigo_operador,labor_id,fecha
                    ) ta ON ta.codigo_operador=ma.codigo_operador AND ma.fecha_ref = ta.fecha
                    WHERE ma.fecha_ref>='2021-09-15' AND ma.fecha_ref<='2021-09-21' 
                    GROUP BY op.dni, ma.fecha_ref,ta.labor_id
                ) ASIS ON EC.fecha_empaque= ASIS.fecha_ref AND LA.codigo_auxiliar=ASIS.labor_id AND ASIS.dni=RP.codigo_operador
                
                GROUP BY EC.fecha_empaque,RP.codigo_operador,PRE.nombre_presentacion,RP.codigo_labor";
        $query="SELECT DATE(CA.created_at) fecha_empaque,RP.codigo_operador,ASIS.nom_operador,ASIS.ape_operador,PRE.nombre_presentacion,LA.descripcion nombre_labor, ASIS.horas, COUNT(CA.id) num_cajas
                FROM caja CA
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                INNER JOIN labor LA ON LA.codigo_labor=RP.codigo_labor
                INNER JOIN (
                    SELECT op.dni,op.nom_operador,op.ape_operador,ma.fecha_ref,ta.labor_id, SUM(TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) horas 
                    FROM db_asistencia_produccion.operador op 
                    INNER JOIN db_asistencia_produccion.marcador ma ON op.dni=ma.codigo_operador
                    INNER JOIN (
                            SELECT * FROM db_asistencia_produccion.tareo 
                            GROUP BY codigo_operador,labor_id,fecha
                    ) ta ON ta.codigo_operador=ma.codigo_operador AND ma.fecha_ref = ta.fecha
                    WHERE ma.fecha_ref>='2021-09-13' AND ma.fecha_ref<='2021-09-22' 
                    GROUP BY op.dni, ma.fecha_ref,ta.labor_id
                ) ASIS ON DATE(CA.created_at)= ASIS.fecha_ref AND LA.codigo_auxiliar=ASIS.labor_id AND ASIS.dni=RP.codigo_operador
                
                GROUP BY DATE(CA.created_at),RP.codigo_operador,PRE.nombre_presentacion,RP.codigo_labor
                ";     
        $query="SELECT 	DATE(CA.created_at) fecha_empaque,
                        RP.codigo_operador,
                        ASIS.nom_operador,
                        ASIS.ape_operador,
                        PRE.nombre_presentacion,
                        LA.descripcion nombre_labor, 
                        ASIS.horas,
                        COUNT(CA.id) num_cajas,
                        ROUND(ASIS.horas * (PRE.peso_neto*COUNT(CA.id)/SUB_LOTE.total), 2) hora_proporcional
                FROM caja CA
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                INNER JOIN labor LA ON LA.codigo_labor=RP.codigo_labor
                INNER JOIN (
                SELECT op.dni,op.nom_operador,op.ape_operador,ma.fecha_ref,ta.labor_id, SUM(TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) horas 
                FROM db_asistencia_produccion.operador op 
                INNER JOIN db_asistencia_produccion.marcador ma ON op.dni=ma.codigo_operador
                INNER JOIN (
                                SELECT * FROM db_asistencia_produccion.tareo 
                                GROUP BY codigo_operador,labor_id,fecha
                ) ta ON ta.codigo_operador=ma.codigo_operador AND ma.fecha_ref = ta.fecha
                WHERE ma.fecha_ref>='2021-09-13' AND ma.fecha_ref<='2021-09-25' 
                GROUP BY op.dni, ma.fecha_ref,ta.labor_id
                ) ASIS ON DATE(CA.created_at)= ASIS.fecha_ref AND LA.codigo_auxiliar=ASIS.labor_id AND ASIS.dni=RP.codigo_operador

                INNER JOIN ( 
                SELECT fecha_empaque,codigo_operador,SUM(sub_peso) total 
                FROM(
                    SELECT COUNT(CA_IN.id)*PRE_IN.peso_neto sub_peso,DATE(CA_IN.created_at) fecha_empaque,RP_IN.codigo_operador
                    FROM caja CA_IN
                    INNER JOIN rendimiento_personal RP_IN ON RP_IN.caja_id=CA_IN.id
                    INNER JOIN etiqueta_caja EC_IN ON EC_IN.id=CA_IN.etiqueta_caja_id
                    INNER JOIN presentacion PRE_IN ON PRE_IN.id=EC_IN.presentacion_id
                    GROUP BY RP_IN.codigo_operador, DATE(CA_IN.created_at), EC_IN.presentacion_id
                ) SUB 
                GROUP BY SUB.fecha_empaque, SUB.codigo_operador
                ) SUB_LOTE ON SUB_LOTE.fecha_empaque = DATE(CA.created_at) AND SUB_LOTE.codigo_operador=RP.codigo_operador

                GROUP BY DATE(CA.created_at),RP.codigo_operador,PRE.nombre_presentacion,RP.codigo_labor";
        $query="SELECT 	EC.fecha_empaque,
                        RP.codigo_operador,
                        ASIS.nom_operador,
                        ASIS.ape_operador,
                        PRE.nombre_presentacion,
                        LA.descripcion nombre_labor, 
                        ASIS.horas,
                        COUNT(CA.id) num_cajas,
                        ROUND(ASIS.horas * (PRE.peso_neto*COUNT(CA.id)/SUB_LOTE.total), 2) hora_proporcional
                FROM caja CA
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                INNER JOIN labor LA ON LA.codigo_labor=RP.codigo_labor
                INNER JOIN (
                SELECT op.dni,op.nom_operador,op.ape_operador,ma.fecha_ref,ta.labor_id, SUM(TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) horas 
                FROM db_asistencia_produccion.operador op 
                INNER JOIN db_asistencia_produccion.marcador ma ON op.dni=ma.codigo_operador
                INNER JOIN (
                                SELECT * FROM db_asistencia_produccion.tareo 
                                GROUP BY codigo_operador,labor_id,fecha
                ) ta ON ta.codigo_operador=ma.codigo_operador AND ma.fecha_ref = ta.fecha
                WHERE ma.fecha_ref>='2021-09-13' AND ma.fecha_ref<='2021-09-22' 
                GROUP BY op.dni, ma.fecha_ref,ta.labor_id
                ) ASIS ON EC.fecha_empaque= ASIS.fecha_ref AND LA.codigo_auxiliar=ASIS.labor_id AND ASIS.dni=RP.codigo_operador

                INNER JOIN ( 
                SELECT fecha_empaque,codigo_operador,SUM(sub_peso) total 
                FROM(
                    SELECT COUNT(CA_IN.id)*PRE_IN.peso_neto sub_peso,EC_IN.fecha_empaque fecha_empaque,RP_IN.codigo_operador
                    FROM caja CA_IN
                    INNER JOIN rendimiento_personal RP_IN ON RP_IN.caja_id=CA_IN.id
                    INNER JOIN etiqueta_caja EC_IN ON EC_IN.id=CA_IN.etiqueta_caja_id
                    INNER JOIN presentacion PRE_IN ON PRE_IN.id=EC_IN.presentacion_id
                    GROUP BY RP_IN.codigo_operador, EC_IN.fecha_empaque, EC_IN.presentacion_id
                ) SUB 
                GROUP BY SUB.fecha_empaque, SUB.codigo_operador
                ) SUB_LOTE ON SUB_LOTE.fecha_empaque = DATE(CA.created_at) AND SUB_LOTE.codigo_operador=RP.codigo_operador

                GROUP BY EC.fecha_empaque,RP.codigo_operador,PRE.nombre_presentacion,RP.codigo_labor";
        $data=DB::select(DB::raw("$query"),[]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Rendimiento Personal Presentación.xlsx");
        }else{
            return response()->json($data);  
        }   
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
                        DATE_FORMAT(SL.fecha_recepcion,'%H:%i') hora_ingreso,
                        LI.fecha_proceso,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        LI.codigo lote_materia,
                        SUM(PE.num_jabas) numero_jabas,
                        ROUND(SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)/SUM(PE.num_jabas),2) peso_promedio_jaba,
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
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Acopio $desde - $hasta.xlsx");
        }else{
            return response()->json($data);  
        }
    }

    public function lanzado(Request $request){
        $fecha_proceso=$request->fecha_proceso;
        $query="SELECT 	PE.id,
                        LI.codigo,
                        LI.fecha_proceso,
                        LI.fecha_cosecha,
                        CL.descripcion nombre_productor,
                        FU.nombre_fundo,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        PE.linea_lanzado,
                        DATE(PE.fecha_lanzado) fecha,
                        CONCAT(HOUR(PE.fecha_lanzado),':00') hora_inicio,
                        CONCAT(HOUR(DATE_ADD(PE.fecha_lanzado, INTERVAL 1 HOUR)),':00') hora_fin,
                        COUNT(PE.num_palet) num_pallets,
                        SUM(PE.num_jabas) num_jabas
                FROM palet_entrada PE 
                INNER JOIN sub_lote SL ON SL.id=PE.sub_lote_id
                INNER JOIN lote_ingreso LI ON LI.id=SL.lote_id
                INNER JOIN cliente CL ON CL.id=LI.cliente_id
                INNER JOIN materia MA ON MA.id=LI.materia_id
                INNER JOIN variedad VA ON VA.id=LI.variedad_id
                INNER JOIN fundo FU ON FU.id=LI.fundo_id
                WHERE PE.estado='Lanzado'
                AND LI.fecha_proceso=?
                GROUP BY DATE(PE.fecha_lanzado),HOUR(PE.fecha_lanzado)
                ORDER BY fecha ASC, hora_inicio ASC";
        $data=DB::select(DB::raw("$query"),[$fecha_proceso]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Lanzado $fecha_proceso.xlsx");
        }else{
            return response()->json($data);  
        }
    }

    public function producto_terminado(Request $request){
        $cliente_id=$request->cliente_id;
        $desde=$request->desde;
        $hasta=$request->hasta;
        $queryProductor=($cliente_id==null) ? '' : ' AND CL.id=?';
        // dd($queryProductor);
        $query="SELECT 	EC.fecha_empaque,
                        PS.tipo_palet_id,
                        PS.numero,
                        CAL.nombre_calibre,
                        CAT.nombre_categoria,
                        FUN.cod_cartilla codigo_fundo,
                        VAR.cod_cartilla codigo_variedad,
                        PRE.nombre_presentacion,
                        MAC.nombre_marca_caja,
                        TIE.nombre_tipo_empaque,
                        MAE.nombre_marca_empaque,
                        plu.nombre_plu,
                        DAYOFYEAR(DATE_FORMAT(LI.fecha_cosecha, '2016-%m-%d')) juliano,
                        LI.codigo codigo_lote,
                        CL.descripcion nombre_productor,
                        PS.id palet_id,
                        COUNT(CA.id) numero_cajas
                FROM palet_salida PS 
                INNER JOIN cliente CL ON PS.cliente_id=CL.id
                INNER JOIN caja CA ON CA.palet_salida_id=PS.id
                INNER JOIN etiqueta_caja EC ON CA.etiqueta_caja_id=EC.id
                INNER JOIN calibre CAL ON CAL.id = EC.calibre_id
                INNER JOIN categoria CAT ON CAT.id = EC.categoria_id
                INNER JOIN presentacion PRE ON PRE.id = EC.presentacion_id
                INNER JOIN marca_caja MAC ON MAC.id = EC.marca_caja_id
                INNER JOIN tipo_empaque TIE ON TIE.id = EC.tipo_empaque_id
                INNER JOIN marca_empaque MAE ON MAE.id = EC.marca_empaque_id
                INNER JOIN plu ON plu.id = EC.plu_id
                INNER JOIN lote_ingreso LI ON EC.lote_ingreso_id=LI.id
                INNER JOIN variedad VAR ON VAR.id = LI.variedad_id
                INNER JOIN fundo FUN ON FUN.id=LI.fundo_id
                WHERE DATE(EC.fecha_empaque)>=?
                AND DATE(EC.fecha_empaque)<=?
                $queryProductor
                AND PS.estado <> 'Remonte'
                GROUP BY PS.id,LI.id 
                ORDER BY PS.numero ASC, EC.fecha_empaque ASC";
        $data=DB::select(DB::raw("$query"),[$desde,$hasta,$cliente_id]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Producto Terminado $desde - $hasta.xlsx");
        }else{
            return response()->json($data);  
        }
    }

    public function producto_terminado_linea(Request $request){
        $cliente_id=$request->cliente_id;
        $desde=$request->desde;
        $hasta=$request->hasta;
        $queryProductor=($cliente_id==null) ? '' : ' AND CL.id=?';
        $query="SELECT 	EC.fecha_empaque,
                        PS.tipo_palet_id,
                        PS.numero,
                        CAL.nombre_calibre,
                        CAT.nombre_categoria,
                        FUN.cod_cartilla codigo_fundo,
                        VAR.cod_cartilla codigo_variedad,
                        PRE.nombre_presentacion,
                        MAC.nombre_marca_caja,
                        TIE.nombre_tipo_empaque,
                        MAE.nombre_marca_empaque,
                        plu.nombre_plu,
                        DAYOFYEAR(DATE_FORMAT(LI.fecha_cosecha, '2016-%m-%d')) juliano,
                        LI.codigo codigo_lote,
                        CL.descripcion nombre_productor,
                        PS.id palet_id,
                        CA.linea,
                        COUNT(CA.id) numero_cajas
                FROM palet_salida PS 
                INNER JOIN cliente CL ON PS.cliente_id=CL.id
                INNER JOIN caja CA ON CA.palet_salida_id=PS.id
                INNER JOIN etiqueta_caja EC ON CA.etiqueta_caja_id=EC.id
                INNER JOIN lote_ingreso LI ON EC.lote_ingreso_id=LI.id
                INNER JOIN fundo FUN ON FUN.id=LI.fundo_id
                INNER JOIN variedad VAR ON VAR.id = LI.variedad_id
                INNER JOIN calibre CAL ON CAL.id = EC.calibre_id
                INNER JOIN categoria CAT ON CAT.id = EC.categoria_id
                INNER JOIN presentacion PRE ON PRE.id = EC.presentacion_id
                INNER JOIN marca_caja MAC ON MAC.id = EC.marca_caja_id
                INNER JOIN tipo_empaque TIE ON TIE.id = EC.tipo_empaque_id
                INNER JOIN marca_empaque MAE ON MAE.id = EC.marca_empaque_id
                INNER JOIN plu ON plu.id = EC.plu_id
                WHERE DATE(EC.fecha_empaque)>=?
                AND DATE(EC.fecha_empaque)<=?
                $queryProductor
                AND PS.estado <> 'Remonte'
                GROUP BY PS.id,LI.id, CA.linea
                ORDER BY PS.numero ASC, EC.fecha_empaque ASC";
        // dd($query);
        $data=DB::select(DB::raw("$query"),[$desde,$hasta,$cliente_id]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Producto Terminado $desde - $hasta.xlsx");
        }else{
            return response()->json($data);  
        }
    }

    public function rendimiento_linea(Request $request){
        $desde=$request->desde;
        $hasta=$request->hasta;
        $query="SELECT 	CONCAT(HOUR(CA.created_at),':00') hora_inicio,
                        CONCAT(HOUR(DATE_ADD(CA.created_at, INTERVAL 1 HOUR)),':00') hora_fin,
                        EC.fecha_empaque,
                        CA.linea,
                        PR.nombre_presentacion,
                        SUM(peso_neto) salida 
                FROM etiqueta_caja EC 
                INNER JOIN presentacion PR ON PR.id= EC.presentacion_id
                INNER JOIN caja CA ON CA.etiqueta_caja_id=EC.id
                INNER JOIN palet_salida PS ON PS.id=CA.palet_salida_id
                WHERE EC.fecha_empaque>=?
                AND EC.fecha_empaque<=?
                AND PS.estado <> 'Remonte'
                GROUP BY HOUR(CA.created_at),EC.fecha_empaque,CA.linea,EC.presentacion_id 
                ORDER BY fecha_empaque ASC,CA.created_at ASC";
        $data=DB::select(DB::raw("$query"),[$desde,$hasta]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Rendimiento Linea $desde - $hasta.xlsx");
        }else{
            return response()->json($data);  
        }
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
