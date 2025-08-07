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

class Air_forecast_comparison extends MY_Controller {
	
    function __construct() {
        parent::__construct();

		$this->load->library('NuSOAP');
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 19;
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
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;

		$view_data["user"] = $this->Users_model->get_one($this->login_user->id);
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		// CONFIGURACIÓN DE UNIDADES DE REPORTE PARA TIPO DE VARIABLE "CALIDAD DEL AIRE"
		$id_report_unit_setting = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, 
			"id_proyecto" => $id_proyecto, 
			"id_tipo_unidad" => 15, // Concentración (para PM10)
			"deleted" => 0
		))->id_unidad;
		$unit = $this->Unity_model->get_one($id_report_unit_setting);
		$view_data["unit"] = $unit->nombre;

        $air_stations = $this->Air_stations_model->get_all_where(array("id_client" => $id_cliente, "id_project" => $id_proyecto))->result();

        $stations = array();
		$stations_api_code = array();
        foreach ($air_stations as $station) {
			// Si la estación no es una estación de Eye3, sino que de SGS.
			if (in_array($station->id, CONST_ARRAY_NO_EYE3_STATIONS_IDS)) {

                $stations[] = $station;
				// Se obtienen los códigos de la api SGS para cada estación
				$stations_api_code[$station->id] = CONST_ARRAY_NO_EYE3_STATIONS_API_CODE[$station->id];
			}
		}
        $view_data["stations"] = $stations;
		$view_data["stations_api_code"] = $stations_api_code;
        // echo '<pre>'; var_dump($stations_api_code);exit;

		/* RANGO DE FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES */
		$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $id_proyecto);
		$first_datetime = new DateTime($first_datetime);
		$first_datetime->setTime(0,0,0);
		// $first_datetime = $first_datetime->modify('-48 hours');
		$first_datetime = $first_datetime->format("Y-m-d H:i");
		
		$last_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $id_proyecto);
		$last_datetime = new DateTime($last_datetime);
		$last_datetime->setTime(0,0,0);
		$last_datetime = $last_datetime->modify('+24 hours');
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

		$chart_default_data_m = array();

		foreach($period as $datetime){
			
			$date = $datetime->format("Y-m-d");
			$time = $datetime->format("H");

			$date_m = $datetime->format("d/m/Y");
			$chart_default_data_m[$date_m." ".$time.":00 hrs"] = 0;

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

		// DATOS MONITOREO POR DEFECTO PARA PRIMERA CARGA AL INGRESAR AL MÓDULO
		$view_data["chart_default_data_m"] = $chart_default_data_m;

		foreach($stations as $station){


			// LLAMAR A LA CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA LA ESTACIÓN ITERADA Y VARIABLE PM10
			$array_alerts_forecast = array();
			$array_alerts = array();
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
			$view_data["array_alerts"][$station->id] = $array_alerts_final;
			
			

			/* GRÁFICO ESTACIÓN HOTEL MINA | MODELO NEURONAL */
			/* DATOS PRONÓSTICO */
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
			
			$chart_e1_neur_data_p = $this->_get_data_chart_forecast($options);

			$view_data["chart_e1_neur_model_values_p"][$station->id] = $chart_e1_neur_data_p["chart_model_values_p"];
			$view_data["chart_e1_neur_model_ranges_p"][$station->id] = $chart_e1_neur_data_p["chart_model_ranges_p"];
			$view_data["chart_e1_neur_model_intervalo_confianza"][$station->id] = $chart_e1_neur_data_p["chart_model_intervalo_confianza"];
			$view_data["chart_e1_neur_model_porc_conf"][$station->id] = $chart_e1_neur_data_p["chart_model_porc_conf"];
			$view_data["chart_e1_neur_formatted_dates"][$station->id] = $chart_e1_neur_data_p["chart_model_formatted_dates"];
			/* FIN DATOS PRONÓSTICO */
			/* FIN GRÁFICO ESTACIÓN HOTEL MINA | MODELO NEURONAL */


			/* GRÁFICO ESTACIÓN HOTEL MINA | MODELO MACHINE LEARNING */
			/* DATOS PRONÓSTICO */
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

			$chart_e2_ml_data_p = $this->_get_data_chart_forecast($options);
			
			$view_data["chart_e2_ml_model_values_p"][$station->id] = $chart_e2_ml_data_p["chart_model_values_p"];
			$view_data["chart_e2_ml_model_ranges_p"][$station->id] = $chart_e2_ml_data_p["chart_model_ranges_p"];
			$view_data["chart_e2_ml_model_intervalo_confianza"][$station->id] = $chart_e2_ml_data_p["chart_model_intervalo_confianza"];
			$view_data["chart_e2_ml_model_porc_conf"][$station->id] = $chart_e2_ml_data_p["chart_model_porc_conf"];
			$view_data["chart_e2_ml_model_formatted_dates"][$station->id] = $chart_e2_ml_data_p["chart_model_formatted_dates"];
			/* FIN DATOS PRONÓSTICO */
			/* FIN GRÁFICO ESTACIÓN HOTEL MINA | MODELO MACHINE LEARNING */


			/* GRÁFICO ESTACIÓN HOTEL MINA | MODELO NUMÉRICO */
			/* DATOS PRONÓSTICO */
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

			$chart_e3_num_data_p = $this->_get_data_chart_forecast($options);
			
			$view_data["chart_e3_num_model_values_p"][$station->id] = $chart_e3_num_data_p["chart_model_values_p"];
			$view_data["chart_e3_num_model_ranges_p"][$station->id] = $chart_e3_num_data_p["chart_model_ranges_p"];
			$view_data["chart_e3_num_model_intervalo_confianza"][$station->id] = $chart_e3_num_data_p["chart_model_intervalo_confianza"];
			$view_data["chart_e3_num_model_porc_conf"][$station->id] = $chart_e3_num_data_p["chart_model_porc_conf"];
			$view_data["chart_e3_num_model_formatted_dates"][$station->id] = $chart_e3_num_data_p["chart_model_formatted_dates"];
			/* FIN DATOS PRONÓSTICO */
			/* FIN GRÁFICO ESTACIÓN HOTEL MINA | MODELO NUMÉRICO */
		}

            // echo '<pre>'; var_dump($view_data);exit;
        $this->template->rander("air_forecast_comparison/index", $view_data);
    
    }


	function _get_data_chart_forecast($options = array()){

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

		$record = $this->Air_records_model->get_details(array(
			"id_client"=> $id_cliente,
			"id_project" => $id_proyecto,
			"id_air_station" => $id_air_station,
			"id_air_sector" => $id_air_sector,
			"id_air_model" => $id_air_model,
			"id_air_record_type" => $id_air_record_type
		))->row();

		foreach($array_period as $date => $times){

			$array_data_times_values = array();
			$array_data_times_ranges = array();
			$array_data_times_values_min = array();
			$array_data_times_values_max = array();

			$chart_model_formatted_dates[$date] = get_date_format($date, $id_proyecto);

			$value_p = $this->Air_records_values_p_model->get_last_record_of_upload_data(array(
				"id_variable" => $id_variable,
				"id_record" => $record->id,
				"date" => $date
			))->row();
			
			// echo "<pre>";
			// print_r($value_p);
			// echo "</pre>";

			if($value_p->id){

				foreach($value_p as $field => $value){
					if(in_array($field, $times)){

						// echo "<pre>";
						// print_r($field." ".$times);
						// echo "</pre>";

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
							$array_data_times_values[$field] = $value;
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
						$array_data_times_values[$time] = 0;
					}

					if($array_alerts_forecast[0]["min_value"] > 0){
						$array_data_times_ranges[$time] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts_forecast[0]["min_value"], $id_proyecto);
					} else {
						$array_data_times_ranges[$time] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts_forecast[1]["min_value"], $id_proyecto);
					}
				}

			}

			$chart_model_values_p[$date] = $array_data_times_values;
			$chart_model_ranges_p[$date] = $array_data_times_ranges;

			// if($record->id == 31){
			// 	var_dump($array_data_times_values_min);
			// 	echo "<br>";
			// 	var_dump($array_data_times_values_max);
			// 	echo "<br>";
			// }

			foreach($array_data_times_values_min as $index => $value){
				$chart_model_intervalo_confianza[] = array((float)$value, (float)$array_data_times_values_max[$index]);
			}
			
		}

		// if($record->id == 31){
		// 	echo "<pre>";
		// 	print_r($chart_model_intervalo_confianza);
		// 	echo "</pre>";
		// 	exit();
		// }
		

		$array_data = array(
			"chart_model_values_p" => $chart_model_values_p,
			"chart_model_ranges_p" => $chart_model_ranges_p,
			"chart_model_intervalo_confianza" => $chart_model_intervalo_confianza,
			"chart_model_porc_conf" => $chart_model_porc_conf,
			"chart_model_formatted_dates" => $chart_model_formatted_dates
		);

		return $array_data;

	}

	function get_sgs_monitoring_data(){

        // OBTENER FUNCIONES DISPONIBLES EN API
        // $soapclient = new SoapClient('https://qmonitor.sgs.com/WSDataQMonitorDynamic/services.asmx?WSDL');
        // $functions = $soapclient->__getFunctions();
        // var_dump($functions);
        // exit();

		// $id_project = $this->session->project_context;
		$api_station_code = $this->input->post("api_station_code");

		$first_datetime = new DateTime('now', new DateTimeZone('UTC'));
		$first_datetime->setTimezone(new DateTimeZone('America/Santiago'));
		// $first_datetime = $first_datetime->modify('-48 hours');
		$first_date = $first_datetime->format("d-m-Y");

		$last_datetime = new DateTime('now', new DateTimeZone('UTC'));
		$last_datetime->setTimezone(new DateTimeZone('America/Santiago'));
		$last_date = $last_datetime->format("d-m-Y");

        $wsdl_url = 'https://qmonitor.sgs.com/WSDataQMonitorDynamic/services.asmx?WSDL';
        $client = new nusoap_client($wsdl_url, true);

        $params = array(
            "user" => "sgs-admin",
            "password" => '$sgs',
            "Oricod" => "018",
            "Ciacod" => "3000002717",
            "LocCod" => "003-AIRE-800500",
            "LocUbiNum" => $api_station_code,
            "Fec_Desde" => $first_date,
            "Fec_Hasta" => $last_date,
            "Frecuencia" => "H"
        );

        $result = $client->call('ServicioDataMLP', $params);
		$result_data = $result["ServicioDataMLPResult"]["_DataResponse"]["_DataSet"]["diffgram"]["NewDataSet"]["Table"];
    	$result_message = $result["ServicioDataMLPResult"]["_MessageResponse"];

        if ($client->fault) {
			echo json_encode(array("success" => false, 'message' => "Error en la solicitud a API: ".$result));
			exit();
        } else if($result_message["Status"] == true && $result_message["Code"] == "200"){ // SI LA SOLICITUD ES EXISTOSA
            
            $error = $client->getError();
            if($error){ // SI HAY UN ERROR EN LA RESPUESTA, LO MUESTRA
				echo json_encode(array("success" => false, 'message' => "Error en la respuesta de API: ".$error));
				exit();
            } else { // SI LA RESPUESTA ES EXITOSA

				$chart_data_m = array();
                
                foreach($result_data as $key => $rd){

                    $sigla_variable = trim(utf8_encode($rd["SrvAbr"]));

                    if($sigla_variable != "MP10 Esampler" && $sigla_variable != "MP10" ){ continue; } // SOLO MOSTRAR DATOS DE VARIABLE MP10

                    $datetime = $rd["Fec_FechaCaptura"];
					$array_datetime = explode("T", $datetime);
					$date = date("d/m/Y", strtotime($array_datetime[0]));
					$hour = substr($array_datetime[1], 0, 5)." hrs";
					$datetime_chart = $date." ".$hour;
                    $value = (float)$rd["Num_ValorMedicion"];

					$chart_data_m[$datetime_chart] = $value;

                }
				
				$update_datetime = new DateTime('now', new DateTimeZone('UTC'));
				$update_datetime = format_to_relative_time($update_datetime->format("d-m-Y H:i:s"));

				echo json_encode(array("success" => true, 'data' => $chart_data_m, 'message' => "Última actualización: ".$update_datetime));
				exit();

            }

        } else {
			echo json_encode(array("success" => false, 'message' => "Error en la solicitud a API. Código de respuesta: ".$result_message["Code"]." Mensaje: ".$result_message["Message"]));
			exit();
		}

	}



	

	function index2() {
        $id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;

		$view_data["user"] = $this->Users_model->get_one($this->login_user->id);
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		// CONFIGURACIÓN DE UNIDADES DE REPORTE PARA TIPO DE VARIABLE "CALIDAD DEL AIRE"
		$id_report_unit_setting = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, 
			"id_proyecto" => $id_proyecto, 
			"id_tipo_unidad" => 15, // Concentración (para PM10)
			"deleted" => 0
		))->id_unidad;
		$unit = $this->Unity_model->get_one($id_report_unit_setting);
		$view_data["unit"] = $unit->nombre;

        $air_stations = $this->Air_stations_model->get_all_where(array("id_client" => $id_cliente, "id_project" => $id_proyecto))->result();

        $stations = array();
		$stations_api_code = array();
        foreach ($air_stations as $station) {
			// Si la estación no es una estación de Eye3, sino que de SGS.
			if (in_array($station->id, CONST_ARRAY_NO_EYE3_STATIONS_IDS)) {

                $stations[] = $station;
				// Se obtienen los códigos de la api SGS para cada estación
				$stations_api_code[$station->id] = CONST_ARRAY_NO_EYE3_STATIONS_API_CODE[$station->id];
			}
		}
        $view_data["stations"] = $stations;
		$view_data["stations_api_code"] = $stations_api_code;
		
		// echo '<pre>'; var_dump($view_data);exit;
		$this->template->rander("air_forecast_comparison/index_2", $view_data);

	}
}