<?php

if (!function_exists('get_transformed_value')) {
	
    function get_transformed_value($valor, $id_tipo_unidad_origen, $id_unidad_origen, $id_unidad_destino) {
		
		$ci = get_instance();
		$fila_conversion = $ci->Conversion_model->get_one_where(
			array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			)
		);
		$valor_transformacion = $fila_conversion->transformacion;
		$valor_final = $valor * $valor_transformacion;
		
		return $valor_final;

    }

}


/**
Retorna el total del valor de UF
**/
if (!function_exists('get_footprint_value')) {

    function get_functional_unit_value($id_cliente, $id_proyecto, $id_uf, $start_date = NULL, $end_date = NULL) {
		
		$ci = get_instance();

		$valor_final = 0;
		$elem_fuera_rango = 0;

		// Consultar el formulario fijo de tipo "Unidades Funcionales" del cliente / proyecto y traer sus valores
		$valores_form_fijo_uf = $ci->Fixed_form_values_model->get_functional_unit_value(array(
			"id_tipo_formulario" => 3,
			"id_uf" => $id_uf
		))->result();
		
		if(count($valores_form_fijo_uf)){
		
			foreach($valores_form_fijo_uf as $index => $valor){
				
				$datos = json_decode($valor->datos, TRUE);
				$periodo_form_fijo_uf 	= $datos[23]; // 23: id campo fijo "Periodo" (rango de fechas (array))
				$valor_form_fijo_uf 	= $datos[25]; // 25: id campo fijo "Valor"
					
				if(!$start_date && !$end_date){ // Si no llega el rango de fechas (Como en el módulo de Panel Principal)
				
					$valor_final = $valor_final + $valor_form_fijo_uf;
					
				} else {
				
					$start_date_query = strtotime($start_date);
					$end_date_query = strtotime($end_date);
					$cant_dias_rango_consulta = $end_date_query - $start_date_query;
					$cant_dias_rango_consulta = (round($cant_dias_rango_consulta / (60 * 60 * 24)) + 1);

					$start_date_form_fijo_uf = strtotime($periodo_form_fijo_uf["start_date"]);
					$end_date_form_fijo_uf = strtotime($periodo_form_fijo_uf["end_date"]);
					$cant_dias_periodo_elemento = $end_date_form_fijo_uf - $start_date_form_fijo_uf;
					$cant_dias_periodo_elemento = (round($cant_dias_periodo_elemento / (60 * 60 * 24))) + 1;

					// Si la fecha de inicio del elemento está entre las fechas de consulta
					if ( ($periodo_form_fijo_uf["start_date"] >= $start_date) && ($periodo_form_fijo_uf["start_date"] <= $end_date) ){
													
						// Si la fecha de término del elemento está entre las fechas de la consulta
						if(($periodo_form_fijo_uf["end_date"] >= $start_date) && ($periodo_form_fijo_uf["end_date"] <= $end_date)){
								
							// cantidad de días que hay entre la fecha de inicio del rango del elemento y la fecha final del rango de la consulta
							$cant_dias_entran_rango_consulta = $cant_dias_periodo_elemento;
							$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);
															
						} else { // Si la fecha de término del elemento NO está entre las fechas de la consulta
								
							// cantidad de días que hay entre la fecha de inicio del rango del periodo y la fecha final del rango de la consulta
							$cant_dias_entran_rango_consulta = $end_date_query - $start_date_form_fijo_uf;
							$cant_dias_entran_rango_consulta = (round($cant_dias_entran_rango_consulta / (60 * 60 * 24)) + 1);
							$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);

						}
						
					}
					
					// Si la fecha de termino del elemento está entre las fechas consultadas (incluyéndolas)
					elseif(($periodo_form_fijo_uf["end_date"] >= $start_date) && ($periodo_form_fijo_uf["end_date"] <= $end_date)){

						// cantidad de días que hay entre la fecha de término del elemento y la fecha de inicio del rango de la consulta
						$cant_dias_entran_rango_consulta = $end_date_form_fijo_uf - $start_date_query;
						$cant_dias_entran_rango_consulta = (round($cant_dias_entran_rango_consulta / (60 * 60 * 24)) + 1);
						$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);

					} else {

						if(($start_date >= $periodo_form_fijo_uf["start_date"]) && ($end_date <= $periodo_form_fijo_uf["end_date"])){
							$cant_dias_entran_rango_consulta = $cant_dias_rango_consulta;
							$valor_final = $valor_final + (($valor_form_fijo_uf / $cant_dias_periodo_elemento) * $cant_dias_entran_rango_consulta);
						} else {
							$elem_fuera_rango++;
						}
						
					}
	
				} // FIN else de if(!$start_date && !$end_date)
				
			} // FIN foreach($valores_form_fijo_uf as $valor)
			
		} else {// FIN if(count($valores_form_fijo_uf))
			$valor_final = 1;
		}
		
		if(count($valores_form_fijo_uf) == $elem_fuera_rango){
			$valor_final = 1;
		}
		
		//echo $valor_final;
		return $valor_final;

    }

}
