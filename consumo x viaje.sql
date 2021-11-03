SELECT 	CL.descripcion nombre_productor,
				FU.nombre_fundo,
				-- FU.lugar_produccion lugar_produccion,
				SL.viaje,
				SL.guia,
				SL.placa,
				-- FU.cod_lugar_produccion cod_lugar_produccion,
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
where CL.id=1
AND LI.fecha_cosecha>='2021-11-03'
AND LI.fecha_cosecha<='2021-11-03'
GROUP BY LI.id, SL.id
ORDER BY SL.id ASC