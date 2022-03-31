<?php

namespace App\Http\Controllers;

use App\Exports\GeneralExcel;
use App\Exports\GeneralExcelHeading;
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
                        RP.fecha_empaque,
                        DATE_FORMAT(MIN(RP.created_at),'%H:%i') primera_lectura,
                        DATE_FORMAT(MAX(RP.created_at),'%H:%i') ultima_lectura,				 
                        TIMESTAMPDIFF(MINUTE, MIN(RP.created_at), MAX(RP.created_at))/60 diferencia
                FROM caja CA 
                INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
                INNER JOIN lote_ingreso LI on LI.id=EC.lote_ingreso_id
                INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
                INNER JOIN (SELECT * FROM labor group by codigo_labor) LA ON LA.codigo_labor=RP.codigo_labor
                LEFT JOIN db_asistencia_produccion.operador op ON op.dni=RP.codigo_operador
                WHERE RP.fecha_empaque=?
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
        $query="CALL rendimiento_por_presentacion(?,?);";
        $data=DB::select(DB::raw("$query"),[$request->desde,$request->hasta]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Rendimiento Personal Presentación.xlsx");
        }else{
            return response()->json($data);  
        }   
    }
    public function consolidado_bonos(Request $request){
        $query="CALL consolidado_bonos(?,?);";
        $data=DB::select(DB::raw("$query"),[$request->desde,$request->hasta]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte consolidado Bonos ".$request->desde." - ".$request->hasta.".xlsx");
        }else{
            return response()->json($data);  
        }   
    }

    public function bono_personal(Request $request){
        $query="CALL rendimiento_por_presentacion(?,?);";
        $data=DB::select(DB::raw("$query"),[$request->desde,$request->hasta]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Bono Personal ".$request->desde." - ".$request->hasta.".xlsx");
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
                        SL.placa,
                        FU.cod_lugar_produccion cod_lugar_produccion,
                        WEEK(SL.fecha_recepcion) semana,
                        DATE(SL.fecha_recepcion) fecha_recepcion,
                        DATE_FORMAT(SL.fecha_recepcion,'%H:%i') hora_ingreso,
                        DATE_FORMAT(MIN(PE.fecha_lanzado),'%H:%i') primer_lanzado,
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
                GROUP BY LI.id, SL.id
                ORDER BY SL.id ASC";
        $data=DB::select(DB::raw("$query"),[$cliente_id,$desde,$hasta]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Acopio $desde - $hasta.xlsx");
        }else{
            return response()->json($data);  
        }
    }

    public function lanzado(Request $request){
        $fecha_proceso=$request->fecha_proceso;
        $linea=$request->linea;
        $query="CALL lanzado_por_linea(?,?);";
        $data=DB::select(DB::raw("$query"),[$fecha_proceso,$linea]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Lanzado $fecha_proceso - Linea $linea.xlsx");
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
                        LI.fecha_cosecha,
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
                        PAR.modelo_parihuela,
                        DAYOFYEAR(DATE_FORMAT(LI.fecha_cosecha, '2016-%m-%d')) juliano,
                        LI.codigo codigo_lote,
                        CL.descripcion nombre_productor,
                        PS.id palet_id,
                        COUNT(CA.id) numero_cajas,
                        OPE.descripcion operacion 
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
                LEFT JOIN parihuela PAR ON PAR.id=PS.parihuela_id
                LEFT JOIN operacion OPE ON OPE.id=PS.operacion_id
                WHERE DATE(EC.fecha_empaque)>=?
                AND DATE(EC.fecha_empaque)<=?
                $queryProductor
                AND PS.estado <> 'Remonte'
                GROUP BY PS.id,EC.id 
                ORDER BY PS.numero ASC, EC.id ASC";
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
    public function consumo_viaje(Request $request){
        $cliente_id=$request->cliente_id;
        $desde=$request->desde;
        $hasta=$request->hasta;
        $queryProductor=($cliente_id==null) ? '' : ' AND CL.id=?';
        $query="SELECT 	CL.descripcion nombre_productor,
                    FU.nombre_fundo,
                    SL.viaje,
                    SL.guia,
                    SL.placa,
                    WEEK(SL.fecha_recepcion) semana,
                    DATE(SL.fecha_recepcion) fecha_recepcion,
                    DATE_FORMAT(SL.fecha_recepcion,'%H:%i') hora_ingreso,
                    DATE(MIN(PE.fecha_lanzado)) fecha_lanzado,
                    DATE_FORMAT(MIN(PE.fecha_lanzado),'%H:%i') hora_lanzado,
                    MA.nombre_materia,
                    VA.nombre_variedad,
                    LI.codigo lote_materia,
                    SUM(PE.num_jabas) numero_jabas,
                    ROUND(SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)/SUM(PE.num_jabas),2) peso_promedio_jaba,
                    -- SL.peso_guia,
                    SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) peso_neto,
                    SUM( CASE WHEN PE.estado='Lanzado' THEN PE.num_jabas ELSE 0 END) jabas_lanzadas,
                    SUM( CASE WHEN PE.estado='Lanzado' THEN PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba ELSE 0 END) peso_lanzado,
                    ROUND((
                        SUM( CASE WHEN PE.estado='Lanzado' THEN PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba ELSE 0 END)
                        /
                        SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)
                    )*100,2) `%_lanzado`
            FROM lote_ingreso LI
            LEFT JOIN sub_lote SL ON LI.id = SL.lote_id
            INNER JOIN fundo FU ON FU.id = LI.fundo_id
            LEFT JOIN parcela PA ON PA.id = LI.parcela_id 
            INNER JOIN cliente CL ON CL.id=LI.cliente_id
            INNER JOIN materia MA ON MA.id=LI.materia_id
            INNER JOIN variedad VA ON VA.id=LI.variedad_id
            LEFT JOIN tipo TI ON TI.id=LI.tipo_id
            LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
            where LI.fecha_cosecha>=?
            AND LI.fecha_cosecha<=?
            $queryProductor
            GROUP BY LI.id, SL.id
            ORDER BY SL.id ASC";
        $data=DB::select(DB::raw("$query"),[$desde,$hasta,$cliente_id]);   
        if ($request->has('excel')) {
            return (new GeneralExcel($data))->download("Reporte Consumo de Materia por Viaje $desde - $hasta.xlsx");
        }else{
            return response()->json($data);  
        }
    }

    public function rendimiento_linea(Request $request){
        $desde=$request->desde;
        $hasta=$request->hasta;
        $query_linea="";
        if ($request->has('linea')) {
            $linea=$request->linea;
            if ($linea!=NULL) {
                $query_linea="AND CA.linea=$linea";
            }
        }
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
                $query_linea
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
    public function balance_materia(Request $request){
        $desde=$request->desde;
        $hasta=$request->hasta;
        $cliente_id=$request->cliente_id;

        $queryColumnas="SELECT PRE.id presentacion_id,
                                CAL.id calibre_id,
                                PRE.nombre_presentacion,
                                CAL.nombre_calibre,
                                REPLACE(CONCAT(PRE.nombre_presentacion,'_^_',CAL.nombre_calibre),' ','_') nom_columna
                        FROM lote_ingreso LI
                        INNER JOIN etiqueta_caja EC ON LI.id=EC.lote_ingreso_id 
                        INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                        INNER JOIN calibre CAL ON CAL.id=EC.calibre_id
                        where LI.cliente_id=?
                        AND LI.fecha_cosecha>=?
                        AND LI.fecha_cosecha<=?
                        GROUP BY PRE.id,CAL.id";
        $dataColumnas=DB::select(DB::raw("$queryColumnas"),[$cliente_id,$desde,$hasta]);
        
        $nameColumnas="";
        $subQueryColumnas="";
        foreach ($dataColumnas as $key => $columna) {
            $calibre_id=$columna->calibre_id;
            $presentacion_id=$columna->presentacion_id;
            $nom_columna=$columna->nom_columna;

            $nameColumnas.=",`$nom_columna`";
            $subQueryColumnas.=", sum(IF(EC.presentacion_id=$presentacion_id AND EC.calibre_id=$calibre_id ,1,0)) `$nom_columna`";
        }
        
        
        
        $query="SELECT 	LI.fecha_cosecha,
                        LI.codigo,
                        CL.descripcion nombre_productor,
                        MA.nombre_materia,
                        VA.nombre_variedad,
                        FU.nombre_fundo,
                        COUNT(DISTINCT SL.viaje) viajes,
                        WEEK(SL.fecha_recepcion) semana,
                        SUM(PE.num_jabas) numero_jabas,
                        ROUND(SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)/SUM(PE.num_jabas),2) peso_promedio_jaba,
                        ROUND(SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba),2) peso_neto,
                        DE.descarte_granos +
                        DE.descarte_racimos descarte,
                        ROUND(
                            (
                                (DE.descarte_granos+DE.descarte_racimos)/
                                SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)
                            )*100
                        ,2) `descarte_%`,
                        PRODUCCION.acumulado produccion_kg,
                        ROUND(
                            (
                                PRODUCCION.acumulado/
                                SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)
                            )*100
                        ,2) `produccion_%`,
                        ROUND(
                            (
                                ( SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) - PRODUCCION.acumulado - (DE.descarte_granos+DE.descarte_racimos) )/
                                SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba)
                            )*100
                        ,2) `merma_%`
                        $nameColumnas
                FROM lote_ingreso LI
                LEFT JOIN descarte DE ON DE.lote_id=LI.id 
                LEFT JOIN sub_lote SL ON LI.id = SL.lote_id
                INNER JOIN fundo FU ON FU.id = LI.fundo_id
                INNER JOIN cliente CL ON CL.id=LI.cliente_id
                INNER JOIN materia MA ON MA.id=LI.materia_id
                INNER JOIN variedad VA ON VA.id=LI.variedad_id
                LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
                LEFT JOIN 
                (
                    SELECT EC.lote_ingreso_id,SUM(PRE.peso_neto) acumulado
                    FROM
                    etiqueta_caja EC 
                    INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                    LEFT JOIN caja CA ON CA.etiqueta_caja_id=EC.id
                    GROUP BY EC.lote_ingreso_id
                ) PRODUCCION
                ON PRODUCCION.lote_ingreso_id=LI.id
                LEFT JOIN 
                    (
                        SELECT EC.lote_ingreso_id
                                $subQueryColumnas
                        FROM etiqueta_caja EC 
                        INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
                        LEFT JOIN caja CA ON CA.etiqueta_caja_id=EC.id
                        GROUP BY EC.lote_ingreso_id
                    ) CAJAS_PESENTACION
                    ON CAJAS_PESENTACION.lote_ingreso_id=LI.id
                    where CL.id=?
                AND LI.fecha_cosecha>=?
                AND LI.fecha_cosecha<=?
                GROUP BY LI.id
                ORDER BY SL.id ASC";
        $data=DB::select(DB::raw("$query"),[$cliente_id,$desde,$hasta]);   

        if ($request->has('excel')) {
            /**
             * GENERADOR DE COLUMNAS PARA EL EXCEL
             */

            $heading_1=[];
            $heading_2=[];
            $headings=[];
            $groupHeadings=[];
            if (count($data)>0) {
                $index=0;
                foreach ($data[0] as $key => $value) {
                    $index++;
                    $headings[$key]=[$key];
                    array_push($heading_1," ");
                    array_push($heading_2,$key);
                    if($index==16){
                        break;
                    }
                }
            }
            $oldPresentacion="";
            foreach ($dataColumnas as $key => $columna) {
                // if ($oldPresentacion=="") {
                //     $oldPresentacion=$columna->nombre_presentacion;
                // }
                
                if ($oldPresentacion!=$columna->nombre_presentacion) {
                    array_push($heading_1,$columna->nombre_presentacion);
                    array_push($heading_2,$columna->nombre_calibre);
                    // $headings[$oldPresentacion]=$groupHeadings;
                    $oldPresentacion=$columna->nombre_presentacion;
                    // $groupHeadings=[];    
                }else{
                    array_push($heading_1," ");
                    array_push($heading_2,$columna->nombre_calibre);
                }
                // array_push($groupHeadings,$columna->nombre_calibre);
            }
            // $headings[$oldPresentacion]=$groupHeadings;
            $headings=[
                "Heading_1"=>$heading_1,
                "Heading_2"=>$heading_2
            ];
            // dd($headings);
            /**
             * FIN DE GENERADOR DE COLUMNAS PARA EL EXCEL
             */
            return (new GeneralExcelHeading($data,$headings))->download("Balance Materia $desde - $hasta.xlsx");
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
    public function aforo(Request $request){
        $fecha_produccion=$request->fecha_produccion;
        $query="SELECT LB.codigo_labor,
                    LB.descripcion labor,
                    SUM(CASE WHEN TR.linea_id=1 THEN 1 ELSE 0 END) linea_1,
                    SUM(CASE WHEN TR.linea_id=2 THEN 1 ELSE 0 END) linea_2,
                    SUM(CASE WHEN TR.linea_id=3 THEN 1 ELSE 0 END) linea_3,
                    SUM(CASE WHEN TR.linea_id=4 THEN 1 ELSE 0 END) linea_4,
                    SUM(CASE WHEN TR.linea_id=5 THEN 1 ELSE 0 END) linea_5,
                    SUM(CASE WHEN TR.linea_id=6 THEN 1 ELSE 0 END) linea_6
                FROM 
                (
                    SELECT MAX(id) id 
                    FROM db_asistencia_produccion.tareo 
                    where fecha=?
                    AND turno_id=?
                    GROUP BY codigo_operador
                ) UT INNER JOIN db_asistencia_produccion.tareo TR ON UT.id=TR.id
                INNER JOIN labor LB 
                ON LB.codigo_auxiliar=TR.labor_id
                WHERE linea_id>0
                GROUP BY LB.codigo_labor";
        $data=DB::select(DB::raw("$query"),[$request->fecha_produccion,$request->turno]);      
        return response()->json($data);
    }
    public function cantidad_labor(Request $request){
        $fecha_produccion=$request->fecha_produccion;
        $query="SELECT 	LB.nom_labor,
                        COUNT(TR.id) cantidad
                FROM 
                (
                    SELECT MAX(id) id 
                    FROM db_asistencia_produccion.tareo 
                    where fecha=?
                    AND turno_id=?
                    GROUP BY codigo_operador
                ) UT 
                INNER JOIN db_asistencia_produccion.tareo TR ON UT.id=TR.id
                INNER JOIN db_asistencia_produccion.labor LB ON LB.id=TR.labor_id
                GROUP BY TR.labor_id
                ORDER BY nom_labor ASC";
        $data=DB::select(DB::raw("$query"),[$request->fecha_produccion,$request->turno]);      
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
