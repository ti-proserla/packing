SELECT EC.fecha_empaque,RP.codigo_operador,ASIS.nom_operador,ASIS.ape_operador,PRE.nombre_presentacion,LA.descripcion nombre_labor, COUNT(CA.id) num_cajas
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
	WHERE ma.fecha_ref>'2021-09-16' AND ma.fecha_ref<'2021-09-22' 
	GROUP BY op.dni, ma.fecha_ref,ta.labor_id
) ASIS ON EC.fecha_empaque= ASIS.fecha_ref AND LA.codigo_auxiliar=ASIS.labor_id AND ASIS.dni=RP.codigo_operador

GROUP BY EC.fecha_empaque,RP.codigo_operador,PRE.nombre_presentacion,RP.codigo_labor
