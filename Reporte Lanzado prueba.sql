SET @lanzado='';
SET @linea=3;

SELECT @lanzado:= PE.fecha_lanzado FROM palet_entrada PE
WHERE estado='Lanzado'
AND date(fecha_lanzado)='2021-10-05'
AND PE.linea_lanzado=@linea
ORDER BY fecha_lanzado ASC
LIMIT 1;

SELECT 	PE.id,
				TIMESTAMPDIFF(HOUR,@lanzado,PE.fecha_lanzado) item,
				DATE_ADD(@lanzado, INTERVAL TIMESTAMPDIFF(HOUR,@lanzado,PE.fecha_lanzado) HOUR) desde,
				DATE_ADD(@lanzado, INTERVAL TIMESTAMPDIFF(HOUR,@lanzado,PE.fecha_lanzado)+1 HOUR) hasta,
-- if( @salida_anterior = PE.fecha_lanzado,'Si','No') tr,
				-- @salida_anterior:=PE.fecha_fin_lanzado,
				-- LI.codigo,
				PE.linea_lanzado,
				PE.fecha_lanzado,
				PE.fecha_fin_lanzado,
				PE.num_palet,
				SUM(PE.num_jabas) jabas
FROM palet_entrada PE
WHERE estado='Lanzado'
AND date(fecha_lanzado)='2021-10-05'
AND linea_lanzado=@linea
GROUP BY item,linea_lanzado
ORDER BY linea_lanzado,fecha_lanzado ASC
;