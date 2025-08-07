<?php
	
	/*
	 * Helper creado para la generación de imágenes de gráficos a incluir en correos de alertas de pronósticos
	 */
	
	 /*
	 * Retorna imagen de gráfico
	 */
	if (!function_exists('air_forecast_comparison_get_chart_img')) {
	
		function air_forecast_comparison_get_chart_img() {
		
			$ci = & get_instance();

			$id_cliente = 1;
			$id_proyecto = 1;

			$air_stations = $ci->Air_stations_model->get_all_where(array("id_client" => $id_cliente, "id_project" => $id_proyecto))->result();
			$stations = array();
			foreach ($air_stations as $station) {
				if (in_array($station->id, array(2,3,4))) { // Hotel Mina, Chacay, Cuncumén
					$stations[] = $station;
				}
			}

			/* RANGO DE FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES */
			$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $id_proyecto);
			$first_datetime = new DateTime($first_datetime);
			$first_datetime->setTime(0,0,0);
			$first_datetime = $first_datetime->modify('+24 hours');
			$first_datetime = $first_datetime->format("Y-m-d H:i");
			
			$last_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $id_proyecto);
			$last_datetime = new DateTime($last_datetime);
			$last_datetime->setTime(0,0,0);
			// $last_datetime = $last_datetime->modify('+96 hours');
			$last_datetime = $last_datetime->modify('+96 hours');
			//$last_datetime = $last_datetime->modify('+24 hours');
			$last_datetime = $last_datetime->format("Y-m-d H:i");
			
			$first_date = date("Y-m-d", strtotime($first_datetime));
			$last_date = date("Y-m-d", strtotime($last_datetime));

			$period = new DatePeriod(
				new DateTime($first_date),
				new DateInterval('PT1H'),
				new DateTime($last_date)
			);

			$array_period = array();
			$array_times = array();
			$previous_date = $first_date;

			// ARREGLOS PARA GRÁFICOS
			$array_chart_categories = array();

			foreach($period as $datetime){
				
				$date = $datetime->format("Y-m-d");
				$time = $datetime->format("H");
				$short_day = lang(strtolower($datetime->format("D")));

				array_push($array_chart_categories, $short_day." ".$time." hrs");

				if($previous_date == $date){
					$array_times[] = "time_".$time;
					$array_times[] = "time_min_".$time;
					$array_times[] = "time_max_".$time;
					$array_times[] = "time_porc_conf_".$time;
				} else {
					$array_times = array();
					$array_times[] = "time_".$time;
					$array_times[] = "time_min_".$time;
					$array_times[] = "time_max_".$time;
					$array_times[] = "time_porc_conf_".$time;
				}

				$array_period[$date] = $array_times;
				$previous_date = $date;
			}
			/* FIN RANGO DE FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES */

			$array_chart_images = array();

			foreach($stations as $station){

				// LLAMAR A LA CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA LA ESTACIÓN ITERADA Y VARIABLE PM10
				$array_alerts_forecast = array();
				$array_alerts = array();
				$array_alerts_yAxis_tickPositions = array();

				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
					"id_client_submodule" => 0, // SIN SUBMÓDULO
					"alert_config" => array(
						"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
						"id_air_station" => $station->id,
						"id_air_sector" => $station->id_air_sector,
						"id_air_variable" => 9 // PM10
					),
				);

				$alert_config_air_forecast_alerts = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
					
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
				if(count($alert_config_forecast)){
					$alert_config = $alert_config_forecast->alert_config;
					if(count($alert_config)){
						foreach($alert_config as $config){
							if($config->nc_active){
								$array_alerts_forecast[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
								$array_alerts[] = array("color" => $config->nc_color, "value" => $config->min_value);
								$array_alerts_yAxis_tickPositions[] = (float)$config->min_value;
							}
						}
					}
				}

				if($station->id == 1){ // COM
					$array_alerts_yAxis_tickPositions[] = 1000;
					$array_alerts_yAxis_tickPositions[] = 1001;
				}
				if($station->id == 2){ // HOTEL MINA
					$array_alerts_yAxis_tickPositions[] = (float)end($array_alerts_yAxis_tickPositions)+50;
				}
				if($station->id == 3 || $station->id == 4 || $station->id == 13){ // CHACAY || CUNCUMEN || QUILLAYES
					$array_alerts_yAxis_tickPositions = array_slice($array_alerts_yAxis_tickPositions, 0, 3);   // returns "a", "b", and "c"
					$array_alerts_yAxis_tickPositions[] = (float)end($array_alerts_yAxis_tickPositions)+1;
				}
				
				// $view_data["array_alerts_yAxis_tickPositions"][$station->id] = $array_alerts_yAxis_tickPositions;

				// var_dump($array_alerts);
				// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
				// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
				$array_alerts_yAxis_plotBands = array();
				$i = 0;
				$prev_color = "";
				$prev_value = 0;
				foreach($array_alerts as $alert){
					if($i == 0){ //primer loop
						$prev_color = $alert["color"];
						$prev_value = $alert["value"];
						$i++;
						continue;
					} else {
						$array_alerts_yAxis_plotBands[] = array("from" => (float)$prev_value, "to" => (float)$alert["value"], "color" => hex2rgba($prev_color, 0.1));
					}
					$prev_color = $alert["color"];
					$prev_value = $alert["value"];
					$i++;
				}

				if($station->id == 1){ // COM
					$plotband_end_to = 1000;
					$yAxis_max = 1001;

					$array_alerts_yAxis_plotBands[] = array(
						"from" => (float)end($array_alerts)["value"], 
						"to" => $plotband_end_to, 
						"color" => hex2rgba((end($array_alerts)["color"]), 0.1)
					);
				}
				if($station->id == 2){ // HOTEL MINA
					$plotband_end_to = (float)end($array_alerts)["value"]+50;
					$yAxis_max = (float)end($array_alerts)["value"]+100;

					$array_alerts_yAxis_plotBands[] = array(
						"from" => (float)end($array_alerts)["value"], 
						"to" => $plotband_end_to, 
						"color" => hex2rgba((end($array_alerts)["color"]), 0.1)
					);
				}
				if($station->id == 3 || $station->id == 4 || $station->id == 13){ // CHACAY || CUNCUMEN || QUILLAYES
					$array_alerts_yAxis_plotBands = array_slice($array_alerts_yAxis_plotBands, 0, 3);   // returns "a", "b", and "c"
					$yAxis_max = (float)end($array_alerts_yAxis_tickPositions);
				}

				// $view_data["array_alerts_yAxis_plotBands"][$station->id] = $array_alerts_yAxis_plotBands;
				// $view_data["yAxis_max"][$station->id] = $yAxis_max;

				
				/* DATOS PRONÓSTICO */

				/* GRÁFICO MODELO NEURONAL */
				$options = array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"array_alerts_forecast" => $array_alerts_forecast,
					"array_period" => $array_period,
					"id_air_station" => $station->id, 
					"id_air_sector" => $station->id_air_sector, 
					"id_air_model" => 2, // NEURONAL
					"id_air_record_type" => 2, // PRONÓSTICO
					"id_variable" => 9 // PM10
				);
				
				$chart_neur_data_p = air_forecast_comparison_get_chart_data($options);

				// $view_data["chart_neur_values_p"][$station->id] = $chart_neur_data_p["chart_model_values_p"];
				// $view_data["chart_neur_ranges_p"][$station->id] = $chart_neur_data_p["chart_model_ranges_p"];
				// $view_data["chart_neur_intervalo_confianza"][$station->id] = $chart_neur_data_p["chart_model_intervalo_confianza"];
				// $view_data["chart_neur_porc_conf"][$station->id] = $chart_neur_data_p["chart_model_porc_conf"];
				// $view_data["chart_neur_formatted_dates"][$station->id] = $chart_neur_data_p["chart_model_formatted_dates"];
				/* FIN GRÁFICO MODELO NEURONAL */

				/* GRÁFICO MODELO MACHINE LEARNING */
				$options = array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"array_alerts_forecast" => $array_alerts_forecast,
					"array_period" => $array_period,
					"id_air_station" => $station->id,
					"id_air_sector" => $station->id_air_sector,
					"id_air_model" => 1, // MACHINE LEARNING
					"id_air_record_type" => 2, // PRONÓSTICO
					"id_variable" => 9 // PM10
				);

				$chart_ml_data_p = air_forecast_comparison_get_chart_data($options);
				
				// $view_data["chart_ml_values_p"][$station->id] = $chart_ml_data_p["chart_model_values_p"];
				// $view_data["chart_ml_ranges_p"][$station->id] = $chart_ml_data_p["chart_model_ranges_p"];
				// $view_data["chart_ml_intervalo_confianza"][$station->id] = $chart_ml_data_p["chart_model_intervalo_confianza"];
				// $view_data["chart_ml_porc_conf"][$station->id] = $chart_ml_data_p["chart_model_porc_conf"];
				// $view_data["chart_ml_formatted_dates"][$station->id] = $chart_ml_data_p["chart_model_formatted_dates"];
				/* FIN GRÁFICO MODELO MACHINE LEARNING */

				/* GRÁFICO MODELO NUMÉRICO */
				$options = array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"array_alerts_forecast" => $array_alerts_forecast,
					"array_period" => $array_period,
					"id_air_station" => $station->id,
					"id_air_sector" => $station->id_air_sector,
					"id_air_model" => 3, // NUMÉRICO
					"id_air_record_type" => 2, // PRONÓSTICO
					"id_variable" => 9 // PM10
				);

				$chart_num_data_p = air_forecast_comparison_get_chart_data($options);
				
				// $view_data["chart_num_values_p"][$station->id] = $chart_num_data_p["chart_model_values_p"];
				// $view_data["chart_num_ranges_p"][$station->id] = $chart_num_data_p["chart_model_ranges_p"];
				// $view_data["chart_num_intervalo_confianza"][$station->id] = $chart_num_data_p["chart_model_intervalo_confianza"];
				// $view_data["chart_num_porc_conf"][$station->id] = $chart_num_data_p["chart_model_porc_conf"];
				// $view_data["chart_num_formatted_dates"][$station->id] = $chart_num_data_p["chart_model_formatted_dates"];
				/* FIN GRÁFICO ESTACIÓN HOTEL MINA | MODELO NUMÉRICO */

				/* FIN DATOS PRONÓSTICO */


				/* GENERACIÓN DE IMAGEN DE GRÁFICO HIGHCHARTS */

				// $highchartsExportUrl = "https://dev.highcharts.mimasoft.cl:4001";
				$highchartsExportUrl = "https://export.highcharts.com";

				$options = [
					"chart" => [
						"height" => 390
					],
					"title" => [
						"text" => $station->name
					],
					"xAxis" => [
						"categories" => $array_chart_categories,
						"tickInterval" => 1
					],
					"yAxis" => [
						"title" => "",
						"min" => 0,
						"max" => $yAxis_max,
						"tickPositions" => $array_alerts_yAxis_tickPositions,
						"plotBands" => $array_alerts_yAxis_plotBands
					],
					"credits" => [
						"enabled" => false
					],
					"series" => [
						[
							"id" => "serie_data_ml",
							"name" => lang("machine_learning"),
							"data" => $chart_ml_data_p["chart_model_values_p"],
							"color" => "#0000FF",
							"marker" => [
								"radius" => 3,
                                "symbol" => 'square'
							]
						],
						[
							"id" => "serie_data_neur",
							"name" => lang("neuronal"),
							"data" => $chart_neur_data_p["chart_model_values_p"],
							"color" => "#FF0000",
							"marker" => [
								"radius" => 3,
                                "symbol" => 'diamond'
							]
						],
						[
							"id" => "serie_data_num",
							"name" => lang("numerical"),
							"data" => $chart_num_data_p["chart_model_values_p"],
							"color" => "#FFA500",
							"marker" => [
								"radius" => 3,
                                "symbol" => 'triangle'
							]
						],
						[
							"name" => "chart_ml_intervalo_confianza",
							"data" => $chart_ml_data_p["chart_model_intervalo_confianza"],
							"type" => "arearange",
							"lineWidth" => 0,
							"linkedTo" => "serie_data_ml",
							"color" => "#0000FF",
							"fillOpacity" => 0.3
						],
						[
							"name" => "chart_neur_intervalo_confianza",
							"data" => $chart_neur_data_p["chart_model_intervalo_confianza"],
							"type" => "arearange",
							"lineWidth" => 0,
							"linkedTo" => "serie_data_neur",
							"color" => "#FF0000",
							"fillOpacity" => 0.3
						],
						[
							"name" => "chart_num_intervalo_confianza",
							"data" => $chart_num_data_p["chart_model_intervalo_confianza"],
							"type" => "arearange",
							"lineWidth" => 0,
							"linkedTo" => "serie_data_num",
							"color" => "#FF0000",
							"fillOpacity" => 0.3
						],
					]
				];
				
				$obj = [
					'options' => json_encode($options),
					'type' => 'image/png',
					// 'async' => true
				];

				$data = http_build_query($obj); // Convierte el objeto en una cadena de consulta
				
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $highchartsExportUrl);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/text'));
				// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec($curl);
				curl_close($curl);

				// OBTENGO EL CONTENIDO BINARIO DECODIFICANDO EL BASE64 RETORNADO POR SERVIDOR HIGHCHARTS
				$binary_data = $response;
				// $filename = "chart_".str_replace(' ', '_', strtolower($station->name)).".png";

				// // Define la ruta completa del archivo en el directorio raíz del proyecto
				
				// // DEFINO LA RUTA DONDE SE GUARDARÁN LAS IMÁGENES PNG DE HIGHCHARTS
				// $temp_file_path = get_setting("temp_file_path");
				// $target_path = getcwd()."/".$temp_file_path."forecast_email_alert_charts";
				// $img_src = get_uri().$temp_file_path."forecast_email_alert_charts/".$filename;
				// if (!is_dir($target_path)) {
				// 	if (!mkdir($target_path, 0777, true)) {
				// 		die('Failed to create file folders.');
				// 	}
				// }
				// $file_path = $target_path."/".$filename;
				// $file = fopen($file_path, 'w'); // ABRO UN ARCHIVO NUEVO EN MODO DE ESCRITURA
				// fwrite($file, $binary_data); // ESCRIBO LOS DATOS BINARIOS EN EL ARCHIVO
				// fclose($file); // CIERRO EL ARCHIVO

				$array_chart_images[] = array(
					"id_station" => $station->id,
					"name_station" => $station->name,
					"binary_data" => $binary_data,
					// "img_src" => $img_src,
				);

				// echo "Gráfico de estación $station->name guardado.<br>";

				/* FIN GENERACIÓN DE IMAGEN DE GRÁFICO HIGHCHARTS */

			}

			// echo "<pre>";
			// var_dump($array_chart_images);
			// exit();

			return $array_chart_images;

		}
	
	}

	/*
	 * Retorna data de gráfico
	 */
	if (!function_exists('air_forecast_comparison_get_chart_data')) {
	
		function air_forecast_comparison_get_chart_data($options = array()) {
		
			$ci = & get_instance();

			$id_cliente = get_array_value($options, "id_cliente");
			$id_proyecto = get_array_value($options, "id_proyecto");
			$array_period = get_array_value($options, "array_period");
			$array_alerts_forecast = get_array_value($options, "array_alerts_forecast");
			$id_air_station = get_array_value($options, "id_air_station");
			$id_air_sector = get_array_value($options, "id_air_sector");
			$id_air_model = get_array_value($options, "id_air_model");
			$id_air_record_type = get_array_value($options, "id_air_record_type");
			$id_variable = get_array_value($options, "id_variable");

			$chart_model_values_p = array();
			$chart_model_ranges_p = array();
			$chart_model_intervalo_confianza = array();
			$chart_model_porc_conf = array();
			$chart_model_formatted_dates = array();

			$record = $ci->Air_records_model->get_details(array(
				"id_client"=> $id_cliente,
				"id_project" => $id_proyecto,
				"id_air_station" => $id_air_station,
				"id_air_sector" => $id_air_sector,
				"id_air_model" => $id_air_model,
				"id_air_record_type" => $id_air_record_type
			))->row();

			$array_data_times_values = array();

			foreach($array_period as $date => $times){

				// $array_data_times_values = array();
				$array_data_times_ranges = array();
				$array_data_times_values_min = array();
				$array_data_times_values_max = array();

				$chart_model_formatted_dates[$date] = get_date_format($date, $id_proyecto);

				$value_p = $ci->Air_records_values_p_model->get_last_record_of_upload_data(array(
					"id_variable" => $id_variable,
					"id_record" => $record->id,
					"date" => $date
				))->row();

				if($value_p->id){

					foreach($value_p as $field => $value){
						if(in_array($field, $times)){

							$range = "-";
							$prev_min_value = 0;
							foreach($array_alerts_forecast as $alert){
								if($value <= $alert["min_value"]){
									if($prev_min_value){
										$range = lang("between")." ".to_number_project_format($prev_min_value, $id_proyecto)." - ".to_number_project_format($alert["min_value"], $id_proyecto);
									} else {
										$range = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($alert["min_value"], $id_proyecto);
									}
									break;
								}
								$prev_min_value = $alert["min_value"];
							}

							if($value > end($array_alerts_forecast)["min_value"]){
								$range = lang("more_than")." ".to_number_project_format(end($array_alerts_forecast)["min_value"], $id_proyecto);
							}

							if(strpos($field, "min") !== false) { 
								$array_data_times_values_min[] = $value;
							} elseif(strpos($field, "max") !== false){
								$array_data_times_values_max[] = $value;
							} elseif(strpos($field, "porc_conf") !== false){
								$chart_model_porc_conf[] = (float)$value;
							} else {
								// $array_data_times_values[$field] = $value;
								array_push($array_data_times_values, (float)$value);
								$array_data_times_ranges[$field] = $range;
							}
							
						}
					}
					
				} else {

					foreach($times as $index => $time){

						if(strpos($time, "min") !== false) { 
							$array_data_times_values_min[] = 0;
						} elseif(strpos($time, "max") !== false){
							$array_data_times_values_max[] = 0;
						} elseif(strpos($time, "porc_conf") !== false){
							$chart_model_porc_conf[] = 0;
						} else {
							// $array_data_times_values[$time] = 0;
							array_push($array_data_times_values, 0);
						}

						if($array_alerts_forecast[0]["min_value"] > 0){
							$array_data_times_ranges[$time] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts_forecast[0]["min_value"], $id_proyecto);
						} else {
							$array_data_times_ranges[$time] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts_forecast[1]["min_value"], $id_proyecto);
						}
					}

				}

				// $chart_model_values_p[$date] = $array_data_times_values;
				$chart_model_values_p = $array_data_times_values;
				$chart_model_ranges_p[$date] = $array_data_times_ranges;

				foreach($array_data_times_values_min as $index => $value){
					$chart_model_intervalo_confianza[] = array((float)$value, (float)$array_data_times_values_max[$index]);
				}
				
			}

			$array_data = array(
				"chart_model_values_p" => $chart_model_values_p,
				"chart_model_ranges_p" => $chart_model_ranges_p,
				"chart_model_intervalo_confianza" => $chart_model_intervalo_confianza,
				"chart_model_porc_conf" => $chart_model_porc_conf,
				"chart_model_formatted_dates" => $chart_model_formatted_dates
			);

			return $array_data;

		}
	
	}
	
	
	