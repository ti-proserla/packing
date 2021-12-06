SELECT 	LI.fecha_cosecha,
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

where CL.id=1
AND LI.fecha_cosecha>='2021-11-01'
AND LI.fecha_cosecha<='2021-11-05'
GROUP BY LI.id
ORDER BY SL.id ASC