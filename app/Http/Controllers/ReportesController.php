<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function cantidad_por_linea(){
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
                GROUP BY lote_ingreso.id,linea,lote_ingreso.created_at";
        $data=DB::select(DB::raw("$query"),[]);      
        return response()->json($data);  
    }
    public function lote(){
        $query="SELECT 
                        LT.id,
                        LT.codigo,
                        INGRESO.*,
                        SALIDA.*
                FROM lote_ingreso LT 
                LEFT JOIN (
                    SELECT SL.lote_id,
                                GROUP_CONCAT(SL.guia SEPARATOR '|' ) guias,
                                SUM(PE.peso * PE.num_jabas) as peso_total_ingreso 
                    FROM sub_lote SL
                    INNER JOIN palet_entrada PE on SL.id=PE.sub_lote_id
                    GROUP BY lote_id
                )INGRESO ON INGRESO.sub_lote_id=SLT.id
                LEFT JOIN (
                    SELECT 
                            PS.lote_id,
                            COUNT(DISTINCT(PS.id)) palets,
                            PS.producto_id,
                            PR.nombre_producto,
                            PR.peso_bruto,
                            PR.potes potes_x_caja,
                            SUM(cantidad) cantidad_cajas,
                            SUM(cantidad*PR.potes*PR.peso_bruto)/1000 tota_kilos
                    FROM palet_salida PS
                    INNER JOIN producto PR ON PR.id=PS.producto_id
                    GROUP BY PS.lote_id, PS.producto_id
                ) SALIDA on SALIDA.lote_id=LT.id
";
        $data=DB::select(DB::raw("$query"),[]);      
        return response()->json($data);  
    }
}
