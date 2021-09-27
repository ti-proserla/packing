SELECT 
			OP.dni,
			OP.nom_operador,
			OP.ape_operador,
			LA.codigo_labor,
			PPL.presentacion_id, 
			SUM( IF (
				TIMESTAMPDIFF(MINUTE,IF(PPL.inicio <= MA.ingreso,MA.ingreso,PPL.inicio) , IF(MA.salida <= PPL.fin, MA.salida, PPL.fin))/60 < 0,
				0,
				TIMESTAMPDIFF(MINUTE,IF(PPL.inicio <= MA.ingreso,MA.ingreso,PPL.inicio) , IF(MA.salida <= PPL.fin, MA.salida, PPL.fin))/60
			)) horas,
			TA.linea_id
			  
FROM 
db_asistencia_produccion.marcador MA 
INNER JOIN db_asistencia_produccion.operador OP ON OP.dni=MA.codigo_operador 
INNER JOIN db_asistencia_produccion.tareo TA ON MA.tareo_id=TA.id
LEFT JOIN programacion_presentacion_linea PPL ON  PPL.linea_id=TA.linea_id
LEFT JOIN labor LA ON LA.codigo_auxiliar=TA.labor_id
AND TA.fecha=PPL.fecha_ref
/**
INNER JOIN (SELECT EC.fecha_empaque, EC.presentacion_id,RP.codigo_operador, PRE.nombre_presentacion,RP.codigo_labor,RP.linea, COUNT(CA.id) cantidad
							FROM caja CA
							INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
							INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
							INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
							where  EC.fecha_empaque >= '2021-09-20' AND EC.fecha_empaque <= '2021-09-23'
							GROUP BY EC.fecha_empaque, RP.codigo_operador, PRE.nombre_presentacion,RP.codigo_labor,RP.linea 
							
) REN_CAJ ON 
			REN_CAJ.fecha_empaque=TA.fecha 
			AND REN_CAJ.codigo_operador = OP.dni 
			-- AND CONVERT(REN_CAJ.codigo_labor,UNSIGNED INTEGER)=LA.codigo_labor 
			-- AND REN_CAJ.presentacion_id= PPL.presentacion_id
			**/
WHERE MA.fecha_ref >= '2021-09-20' AND MA.fecha_ref <= '2021-09-23'
GROUP BY OP.dni, TA.proceso_id,TA.labor_id,presentacion_id ,TA.linea_id