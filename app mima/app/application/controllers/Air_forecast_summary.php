<?php
/**
 * Archivo Controlador para Resumen de Pronósticos (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Resumen de Pronosticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Air_forecast_summary extends MY_Controller {
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 16;
		$this->id_submodulo_cliente = 0;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}

    }

    function index() {

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$view_data["user"] = $this->Users_model->get_one($this->login_user->id);
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["collapsed_left_menu"] = true;

		// CONFIGURACIÓN DE UNIDADES DE REPORTE PARA TIPO DE VARIABLE "CALIDAD DEL AIRE"
		$id_report_unit_setting = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, 
			"id_proyecto" => $id_proyecto, 
			"id_tipo_unidad" => 15, // Concentración (para SO2)
			"deleted" => 0
		))->id_unidad;
		$unit = $this->Unity_model->get_one($id_report_unit_setting);
		$view_data["unit"] = $unit->nombre;

		/* LEYENDA SEGÚN "Configuración de Alertas para Calidad del Aire - Pronósticos" (ADMIN) */
		$array_legend = array(); // ARRAY PARA MOSTRAR LEYENDA DE CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO EN LA VISTA

		// LLAMAR A LA CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO ESTACIÓN LO CAMPO (SE USARÁ LA MISMA CONFIGURACIÓN PARA AMBAS ESTACIONES)
		$array_alerts_forecast = array();
		$array_alerts = array();
		$config_options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
			"id_client_submodule" => 0, // SIN SUBMÓDULO
			"alert_config" => array(
				"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
				//"id_air_station" => 2, // LO CAMPO EN SISTEMA
				"id_air_station" => 7, // E7 EN DEV (DESCOMENTAR PARA PROBAR EN DEV)
				//"id_air_station" => 4, // Estación 1 EN QA (DESCOMENTAR PARA PROBAR EN QA)
				"id_air_sector" => 1,  // Sector 1 EN DEV, CHAGRES EN SISTEMA
				"id_air_variable" => 8
			),
		);

		$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
		$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		if(count($alert_config_forecast)){
			$alert_config = $alert_config_forecast->alert_config;
			if(count($alert_config)){
				foreach($alert_config as $config){
					if($config->nc_active){
						$array_alerts_forecast[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
						$array_alerts[] = array("color" => $config->nc_color, "value" => $config->min_value);
					}
				}
			}
		}

		foreach($array_alerts_forecast as $index_alert => $alert){
			if($index_alert == 0){ // primer loop
				$prev_min_value = $alert["min_value"];
				$prev_nc_name = $alert["nc_name"];
				$prev_nc_color = $alert["nc_color"];
				continue;
			}
			$array_legend[$prev_nc_name] =  array(
				"range" => lang("between")." ".$prev_min_value." y ".$alert["min_value"]." ".$unit->nombre,
				"color" => $prev_nc_color
			);
			$prev_min_value = $alert["min_value"];
			$prev_nc_name = $alert["nc_name"];
			$prev_nc_color = $alert["nc_color"];
			if($index_alert == count($array_alerts_forecast) -1){ // último loop
				$array_legend[$alert["nc_name"]] = array(
					"range" => lang("more_than")." ".$alert["min_value"]." ".$unit->nombre,
					"color" => $alert["nc_color"]
				);
			}
		}

		$view_data["array_legend"] = $array_legend;


		// PCMA pronosicado para hoy

		$date_today = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$date_today = new DateTime($date_today);
		$date_today->setTime(0,0,0);
		$date_yesterday = $date_today->modify('-24 hours');
		$date_yesterday = $date_yesterday->format("Y-m-d");

		$air_synoptic_data = $this->Air_synoptic_data_model->get_one_where(array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"date" => $date_yesterday,
			"deleted" => 0
		));

		$html_pmca_forecast_for_today = array();
		$html_pmca_forecast_for_today['pmca_24_hrs_t1'] = '-';
		$html_pmca_forecast_for_today['pmca_24_hrs_t2'] = '-';
		$html_pmca_forecast_for_today['pmca_24_hrs_t3'] = '-';
		
		if($air_synoptic_data->id){

			$pmca_24_hrs_t1 = json_decode($air_synoptic_data->pmca_24_hrs_t1);
			$pmca_24_hrs_t2 = json_decode($air_synoptic_data->pmca_24_hrs_t2);
			$pmca_24_hrs_t3 = json_decode($air_synoptic_data->pmca_24_hrs_t3);

			$array_pmca_24 = array(
				'pmca_24_hrs_t1' => $pmca_24_hrs_t1,
				'pmca_24_hrs_t2' => $pmca_24_hrs_t2,
				'pmca_24_hrs_t3' => $pmca_24_hrs_t3
			);

			foreach($array_pmca_24 as $pmca_name => $pmca){
				if($pmca->value == "1"){
					$label_background_color = "#91d052";
					$label_text = lang("low");
				} elseif($pmca->value == "2"){
					$label_background_color = "#fbdb66";
					$label_text = lang("medium");
				} elseif($pmca->value == "3"){
					$label_background_color = "#f0ad4e";
					$label_text = lang("high");
				} elseif($pmca->value == "4"){
					$label_background_color = "#fb0007";
					$label_text = lang("very_high");
				}

				if($pmca->value){
					$html_pmca_forecast_for_today[$pmca_name] = '<label class="label large" style="background-color: '.$label_background_color.';"> <span style="display: inline-block;">'.$label_text.'</span> </label>';
				}
			}
		}
		
		$view_data["html_pmca_forecast_for_today"] = $html_pmca_forecast_for_today;
		

		// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
		// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
		$array_alerts_final = array();
		$i = 0;
		$prev_color = "";
		foreach($array_alerts as $alert){
			if($i == 0){ //primer loop
				$prev_color = $alert["color"];
				$i++;
				continue;
			} else {
				$array_alerts_final[] = array("color" => $prev_color, "value" => $alert["value"]);
			}
			$prev_color = $alert["color"];
			$i++;
		}

		$array_alerts_final[] = array("color" => end($array_alerts)["color"]);
		$view_data["array_alerts"] = $array_alerts_final;
		/* FIN LEYENDA SEGÚN "Configuración de Alertas para Calidad del Aire - Pronósticos" (ADMIN) */


		/* FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES */
		$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $id_proyecto);
		$first_datetime = new DateTime($first_datetime);
		$first_datetime->setTime(0,0,0);
		$first_datetime = $first_datetime->format("Y-m-d H:i");
		$last_datetime = new DateTime($first_datetime);
		$last_datetime = $last_datetime->modify('+24 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");
		$first_date = date("Y-m-d", strtotime($first_datetime));
		$last_date = date("Y-m-d", strtotime($last_datetime));

		$array_period = array();
		$array_times = array();
		for($i = 0; $i <= 23; $i++){
			$array_times[] = ($i < 10) ? "time_0".$i : "time_".$i;
		}
		$array_period[$first_date] = $array_times;
		$array_period[$last_date] = $array_times;
		/* FIN FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES */
		

		/* DATOS GRÁFICO ESTACIÓN SANTA MARGARITA - SO2 - MODELO NEURONAL */
		$options = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"array_alerts_forecast" => $array_alerts_forecast,
			"array_period" => $array_period,
			"id_air_station" => 4, // SANTA MARGARITA EN SISTEMA
			//"id_air_station" => 7, // E7 EN DEV (DESCOMENTAR PARA PROBAR EN DEV)
			//"id_air_station" => 5, // Estación 2 EN QA (DESCOMENTAR PARA PROBAR EN QA)
			"id_air_sector" => 1, // Sector 1 EN DEV, CHAGRES EN SISTEMA
			"id_air_model" => 1, // NEURONAL
			"id_air_record_type" => 2, // PRONÓSTICO
			"id_variable" => 8 // SO2
		);
		$chart_e1_neur_data = $this->_get_data_chart($options);
		$view_data["chart_e1_neur_model_values_p"] = $chart_e1_neur_data["chart_model_values_p"];
		$view_data["chart_e1_neur_model_ranges_p"] = $chart_e1_neur_data["chart_model_ranges_p"];
		$view_data["chart_e1_neur_model_formatted_dates"] = $chart_e1_neur_data["chart_model_formatted_dates"];
		/* FIN DATOS GRÁFICO ESTACIÓN SANTA MARGARITA - SO2 - MODELO NEURONAL */


		/* DATOS GRÁFICO ESTACIÓN SANTA MARGARITA - SO2 - MODELO NUMÉRICO */
		$options = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"array_alerts_forecast" => $array_alerts_forecast,
			"array_period" => $array_period,
			"id_air_station" => 4, // SANTA MARGARITA EN SISTEMA
			//"id_air_station" => 7, // E7 EN DEV (DESCOMENTAR PARA PROBAR EN DEV)
			//"id_air_station" => 5, // Estación 2 EN QA (DESCOMENTAR PARA PROBAR EN QA)
			"id_air_sector" => 1, // Sector 1 EN DEV, CHAGRES EN SISTEMA
			"id_air_model" => 3, // NUMÉRICO
			"id_air_record_type" => 2, // PRONÓSTICO
			"id_variable" => 8 // SO2
		);
		$chart_e1_num_data = $this->_get_data_chart($options);
		$view_data["chart_e1_num_model_values_p"] = $chart_e1_num_data["chart_model_values_p"];
		$view_data["chart_e1_num_model_ranges_p"] = $chart_e1_num_data["chart_model_ranges_p"];
		$view_data["chart_e1_num_formatted_dates"] = $chart_e1_num_data["chart_model_formatted_dates"];
		/* FIN DATOS GRÁFICO ESTACIÓN SANTA MARGARITA - SO2 - MODELO NUMÉRICO */


		/* DATOS GRÁFICO ESTACIÓN LO CAMPO - SO2 - MODELO NEURONAL */
		$options = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"array_alerts_forecast" => $array_alerts_forecast,
			"array_period" => $array_period,
			"id_air_station" => 2, // LO CAMPO EN SISTEMA
			//"id_air_station" => 8, // E8 EN DEV (DESCOMENTAR PARA PROBAR EN DEV)
			//"id_air_station" => 4, // Estación 1 EN QA (DESCOMENTAR PARA PROBAR EN QA)
			"id_air_sector" => 1, // Sector 1 EN DEV, CHAGRES EN SISTEMA
			"id_air_model" => 1, // NEURONAL
			"id_air_record_type" => 2, // PRONÓSTICO
			"id_variable" => 8 // SO2
		);
		$chart_e2_neur_data = $this->_get_data_chart($options);
		$view_data["chart_e2_neur_model_values_p"] = $chart_e2_neur_data["chart_model_values_p"];
		$view_data["chart_e2_neur_model_ranges_p"] = $chart_e2_neur_data["chart_model_ranges_p"];
		$view_data["chart_e2_neur_formatted_dates"] = $chart_e2_neur_data["chart_model_formatted_dates"];
		/* FIN DATOS GRÁFICO ESTACIÓN SANTA MARGARITA - SO2 - MODELO NEURONAL */


		/* DATOS GRÁFICO ESTACIÓN LO CAMPO - SO2 - MODELO NEURONAL */
		$options = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"array_alerts_forecast" => $array_alerts_forecast,
			"array_period" => $array_period,
			"id_air_station" => 2, // LO CAMPO EN SISTEMA
			//"id_air_station" => 8, // E8 EN DEV (DESCOMENTAR PARA PROBAR EN DEV)
			//"id_air_station" => 4, // Estación 1 EN QA (DESCOMENTAR PARA PROBAR EN QA)
			"id_air_sector" => 1, // Sector 1 EN DEV, CHAGRES EN SISTEMA
			"id_air_model" => 3, // NUMÉRICO
			"id_air_record_type" => 2, // PRONÓSTICO
			"id_variable" => 8 // SO2
		);
		$chart_e2_num_data = $this->_get_data_chart($options);
		$view_data["chart_e2_num_model_values_p"] = $chart_e2_num_data["chart_model_values_p"];
		$view_data["chart_e2_num_model_ranges_p"] = $chart_e2_num_data["chart_model_ranges_p"];
		$view_data["chart_e2_num_formatted_dates"] = $chart_e2_num_data["chart_model_formatted_dates"];
		/* FIN DATOS GRÁFICO ESTACIÓN SANTA MARGARITA - SO2 - MODELO NEURONAL */

        $this->template->rander("air_forecast_summary/index", $view_data);
	}


	function _get_data_chart($options = array()){

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
		$chart_model_formatted_dates = array();

		$record = $this->Air_records_model->get_details(array(
			"id_client"=> $id_cliente,
			"id_project" => $id_proyecto,
			"id_air_station" => $id_air_station,
			"id_air_sector" => $id_air_sector,
			"id_air_model" => $id_air_model,
			"id_air_record_type" => $id_air_record_type
		))->row();

		foreach($array_period as $date => $times){
			$chart_model_formatted_dates[$date] = get_date_format($date, $id_proyecto);

			$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(array(
				"id_variable" => $id_variable,
				"id_record" => $record->id,
				"date" => $date
			))->row();

			if($value_p->id){
	
				$array_data_times_values = array();
				$array_data_times_ranges = array();

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

						$array_data_times_values[$field] = $value;
						$array_data_times_ranges[$field] = $range;
					}
				}
				$chart_model_values_p[$date] = $array_data_times_values;
				$chart_model_ranges_p[$date] = $array_data_times_ranges;
			} else {

				$array_data_times_values = array();
				$array_data_times_ranges = array();

				foreach($times as $index => $time){
					$array_data_times_values[$time] = 0;
					if($array_alerts_forecast[0]["min_value"] > 0){
						$array_data_times_ranges[$time] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts_forecast[0]["min_value"], $id_proyecto);
					} else {
						$array_data_times_ranges[$time] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts_forecast[1]["min_value"], $id_proyecto);
					}
				}
				$chart_model_values_p[$date] = $array_data_times_values;
				$chart_model_ranges_p[$date] = $array_data_times_ranges;
			}
			
		}

		$array_data = array(
			"chart_model_values_p" => $chart_model_values_p,
			"chart_model_ranges_p" => $chart_model_ranges_p,
			"chart_model_formatted_dates" => $chart_model_formatted_dates
		);

		return $array_data;

	}

}
