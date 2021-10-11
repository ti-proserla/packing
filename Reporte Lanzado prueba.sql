DELIMITER //
CREATE OR REPLACE PROCEDURE lanzado_por_linea(IN pFechaProceso date,IN pLinea INTEGER)
BEGIN

			SET @fecha_proceso=pFechaProceso;
			
			SET @linea=pLinea;

			SET @lanzado:= (SELECT PE.fecha_lanzado

			FROM palet_entrada PE
			INNER JOIN sub_lote SL ON SL.id=PE.sub_lote_id
			INNER JOIN lote_ingreso LI ON LI.id=SL.lote_id 
			WHERE PE.estado='Lanzado'
			AND LI.fecha_proceso=@fecha_proceso
			AND PE.linea_lanzado=@linea
			ORDER BY fecha_lanzado ASC
			LIMIT 1);

			-- DROP TEMPORARY TABLE new_tbl;
			CREATE OR REPLACE TEMPORARY TABLE new_tbl 
			SELECT 	linea_lanzado,
							fecha_lanzado,
							fecha_fin_lanzado,
							num_jabas,
							num_palet,
							PE.peso_jaba,
							PE.peso_palet,
							SL.viaje,
							CL.descripcion nombre_productor,
							FU.nombre_fundo,
							MA.nombre_materia,
							VA.nombre_variedad,
							LI.materia_id
			FROM palet_entrada PE
			INNER JOIN sub_lote SL ON SL.id=PE.sub_lote_id
			INNER JOIN lote_ingreso LI ON LI.id=SL.lote_id 
			INNER JOIN cliente CL ON CL.id=LI.cliente_id
			INNER JOIN fundo FU ON FU.id=LI.fundo_id
			INNER JOIN materia MA ON MA.id=LI.materia_id
			INNER JOIN variedad VA ON VA.id=LI.variedad_id
			WHERE PE.estado='Lanzado'
			AND LI.fecha_proceso=@fecha_proceso
			AND PE.linea_lanzado=@linea
			ORDER BY fecha_lanzado ASC;

			-- SELECT * FROM new_tbl;
			SELECT fecha_lanzado,
							nombre_productor,
							nombre_fundo,
							nombre_materia,
							nombre_variedad,
							DATE_FORMAT(IF(
								TIMESTAMPDIFF(HOUR,@lanzado,fecha_lanzado)>0,
								@lanzado:=DATE_ADD(@lanzado, INTERVAL 1 HOUR),
								@lanzado
							),'%H:%i') hora_inicio,
							DATE_FORMAT(
								IF(
										(SELECT fecha_lanzado FROM new_tbl where fecha_lanzado>=MAX(N.fecha_fin_lanzado) LIMIT 1) IS NOT NULL ,
										MAX(DATE_ADD(@lanzado, INTERVAL 1 HOUR)),
										MAX(fecha_fin_lanzado)
								),'%H:%i') hora_fin,
							SUM(num_jabas) num_jabas
			FROM new_tbl N
			GROUP BY hora_inicio;
			
			DROP TEMPORARY TABLE new_tbl;

END;
//

CALL lanzado_por_linea('2021-09-27',4);