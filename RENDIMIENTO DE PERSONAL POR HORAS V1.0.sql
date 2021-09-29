SELECT 	EC.fecha_empaque,
        RP.codigo_operador,
        CONCAT(ASIS.nom_operador,' ',ASIS.ape_operador) trabajador,
        PRE.nombre_presentacion,
        LA.descripcion nombre_labor, 
        ROUND(ASIS.horas, 2) horas,
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
WHERE ma.fecha_ref>='2021-09-20' AND ma.fecha_ref<='2021-09-26' 
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

GROUP BY EC.fecha_empaque,RP.codigo_operador,PRE.nombre_presentacion,RP.codigo_labor
                ORDER BY RP.codigo_operador, EC.fecha_empaque