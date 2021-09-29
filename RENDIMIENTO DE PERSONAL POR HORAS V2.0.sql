CREATE OR REPLACE PROCEDURE rendimiento_por_presentacion(IN pDesde date,IN pHasta date)
BEGIN
    
		SELECT  HP.fecha,
				HP.dni,
				HP.trabajador,
				HP.labor,
				RENDIMIENTO.nombre_presentacion,
				ROUND(HP.hora_laborada * RENDIMIENTO.proporcional,2) hora_laborada,
				RENDIMIENTO.cantidad
		FROM
		(
				SELECT 	OP.dni,
								CONCAT(OP.nom_operador,' ',OP.ape_operador) trabajador,
								TA.fecha,
								LA.codigo_labor,
								LA.descripcion labor, 
								SUM(TIMESTAMPDIFF(MINUTE,MA.ingreso,MA.salida)/60) hora_laborada
								-- ,TA.labor_id
				FROM db_asistencia_produccion.marcador MA 
				INNER JOIN db_asistencia_produccion.operador OP ON OP.dni=MA.codigo_operador 
				INNER JOIN db_asistencia_produccion.tareo TA ON MA.tareo_id=TA.id
				INNER JOIN labor LA on LA.codigo_auxiliar=TA.labor_id
				WHERE salida is NOT NULL
				GROUP BY OP.dni,MA.fecha_ref,LA.codigo_labor
				ORDER BY dni,fecha
		) HP 
		INNER JOIN 
		(
			SELECT 	COUNT(RP.caja_id) cantidad, 
							PRE.nombre_presentacion,  
							EC.fecha_empaque fecha_empaque,
							RP.codigo_operador,
							RP.codigo_labor,
							ROUND(PRE.peso_neto*COUNT(RP.caja_id)/TOTAL.peso_total,2) proporcional
							-- ,
							-- TOTAL.peso_total
			FROM caja CA
			INNER JOIN rendimiento_personal RP ON RP.caja_id=CA.id
			INNER JOIN etiqueta_caja EC ON EC.id=CA.etiqueta_caja_id
			INNER JOIN presentacion PRE ON PRE.id=EC.presentacion_id
			INNER JOIN (
					SELECT EC_IN.fecha_empaque, RP_IN.codigo_operador, RP_IN.codigo_labor, SUM(1*PRE_IN.peso_neto) peso_total
					FROM caja CA_IN
					INNER JOIN rendimiento_personal RP_IN ON RP_IN.caja_id=CA_IN.id
					INNER JOIN etiqueta_caja EC_IN ON EC_IN.id=CA_IN.etiqueta_caja_id
					INNER JOIN presentacion PRE_IN ON PRE_IN.id=EC_IN.presentacion_id
					WHERE EC_IN.fecha_empaque>=pDesde AND EC_IN.fecha_empaque<=pHasta
					GROUP BY RP_IN.codigo_operador, EC_IN.fecha_empaque,RP_IN.codigo_labor
					ORDER BY codigo_operador
			) TOTAL 
			ON TOTAL.fecha_empaque=EC.fecha_empaque AND TOTAL.codigo_operador=RP.codigo_operador AND TOTAL.codigo_labor=RP.codigo_labor
			WHERE EC.fecha_empaque>=pDesde AND EC.fecha_empaque<=pHasta
			GROUP BY RP.codigo_operador, EC.fecha_empaque,PRE.id,RP.codigo_labor
		) RENDIMIENTO ON 
			(
				RENDIMIENTO.codigo_operador = HP.dni 
				AND RENDIMIENTO.codigo_labor = HP.codigo_labor
				AND RENDIMIENTO.fecha_empaque = HP.fecha
			)
		ORDER BY dni ASC,fecha ASC;
		
END;

CALL rendimiento_por_presentacion('2021-09-27','2021-09-29');