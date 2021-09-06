SELECT *
			FROM sub_lote SL
			LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
			LEFT JOIN (
					SELECT 	DE.*,
									SUM(PE.peso-PE.peso_palet-PE.num_jabas*PE.peso_jaba) peso_total_lote FROM descarte DE
					LEFT JOIN sub_lote SL ON DE.lote_id = SL.lote_id
					LEFT JOIN palet_entrada PE ON SL.id=PE.sub_lote_id
					GROUP BY DE.lote_id
			) DES ON DES.lote_id=SL.lote_id