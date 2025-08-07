<?php
set_time_limit(200);

//ini_set('LimitRequestBody', 100024000);
//ini_set('post_max_size', 100024000);
//ini_set('upload_max_filesize', 100024000);
/*if (!defined('BASEPATH'))
    exit('No direct script access allowed');*/
    //header('Access-Control-Allow-Origin: *');

require(APPPATH.'/libraries/REST_Controller.php');

class Api_monitoring extends REST_Controller {

    public function __construct() {
        parent::__construct();
    }
	
	function monitoring_data_post(){

		$token = $this->input->get_request_header('Authorization', true);

		// VALIDO SI VIENE EL TOKEN (TOPIC)
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}

		// GENERAR UN TOKEN
		// $token = bin2hex(random_bytes(20));
		// return $this->response($token);

		// VALIDO SI EXISTE TOKEN
		$existe_token = $token == "4b24b94a8df79c5656652fbe8ca8d3939165985b" ? true : false;
		if(!$existe_token){
			return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
			exit();
		}

		//$data = $this->post("data"); // POSTMAN: Body - form-data
		//$array_data = json_decode($data, true);
		// return $this->response($array_data);
		// exit();
		$data = json_decode(file_get_contents("php://input"), true); // POSTMAN: Body - raw
		
		$array_data = $data;
		// return $this->response($array_data);
		// exit();

        $array_frequency_5m = $array_data["5m"];
        $array_frequency_15m = $array_data["15m"];
        $array_frequency_1h = $array_data["1h"];

        $array_insert_5m = array();
        $array_insert_15m = array();
        $array_insert_1h = array();

        foreach($array_frequency_5m as $load_code_api => $array_data){

			$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

			if($station->id){
				foreach($array_data as $timestamp => $array_values){

					// $dt = new DateTime('now', new DateTimeZone('UTC'));
					// $dt->setTimestamp($timestamp);
					$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
					$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile
	
					$data_variables = array();
					foreach($array_values as $sigla_variable => $value){

						$id_variable = $this->Air_variables_model->get_one_where(array(
							"sigla_api" => $sigla_variable,
							"deleted" => 0
						))->id;

						$is_variable_in_station = $this->Air_stations_rel_variables_model->get_one_where(array(
							"id_air_station" => $station->id,
							"id_air_variable" => $id_variable,
							"deleted" => 0
						))->id;

						if($is_variable_in_station){
							$data_variables[$id_variable] = (string)$value;
						} else {
							$data_variables[$sigla_variable] = (string)$value;
						}

					}

					// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
					$record = $this->Air_stations_values_5m_model->get_one_where(array(
						"id_station" => $station->id,
						"timestamp" => $timestamp,
						"deleted" => 0
					));

					if($record->id){

						$array_data_row = array(
							"data" => json_encode($data_variables),
							"modified" => get_current_utc_time(),
							"modified_by" => 11 // API USER
						);
						$save_id = $this->Air_stations_values_5m_model->save($array_data_row, $record->id);

					} else {

						$array_data_row = array(
							"id_station" => $station->id,
							"timestamp" => $timestamp,
							"date" => $dt->format('Y-m-d'),
							"hour" => $dt->format('H'),
							"minute" => $dt->format('i'),
							"data" => json_encode($data_variables),
							"created" => get_current_utc_time(),
							"created_by" => 11 // API USER
						);
		
						$array_insert_5m[] = $array_data_row;

					}
	
				}
			} else {
				$response = array("success" => false, "message" => "No existe una estación con el código ".$load_code_api.".");
				return $this->response($response);
				exit();
			}

        }

        foreach($array_frequency_15m as $load_code_api => $array_data){

			$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

			if($station->id){
				foreach($array_data as $timestamp => $array_values){

					// $dt = new DateTime('now', new DateTimeZone('UTC'));
					// $dt->setTimestamp($timestamp);
					$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
					$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

					$data_variables = array();
					foreach($array_values as $sigla_variable => $value){
						
						$id_variable = $this->Air_variables_model->get_one_where(array(
							"sigla_api" => $sigla_variable,
							"deleted" => 0
						))->id;

						$is_variable_in_station = $this->Air_stations_rel_variables_model->get_one_where(array(
							"id_air_station" => $station->id,
							"id_air_variable" => $id_variable,
							"deleted" => 0
						))->id;

						if($is_variable_in_station){
							$data_variables[$id_variable] = (string)$value;
						} else {
							$data_variables[$sigla_variable] = (string)$value;
						}

					}

					// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
					$record = $this->Air_stations_values_15m_model->get_one_where(array(
						"id_station" => $station->id,
						"timestamp" => $timestamp,
						"deleted" => 0
					));

					if($record->id){

						$array_data_row = array(
							"data" => json_encode($data_variables),
							"modified" => get_current_utc_time(),
							"modified_by" => 11 // API USER
						);
						$save_id = $this->Air_stations_values_15m_model->save($array_data_row, $record->id);

					} else {

						$array_data_row = array(
							"id_station" =>  $station->id,
							"timestamp" => $timestamp,
							"date" => $dt->format('Y-m-d'),
							"hour" => $dt->format('H'),
							"minute" => $dt->format('i'),
							"data" => json_encode($data_variables),
							"created" => get_current_utc_time(),
							"created_by" => 11 // API USER
						);
	
						$array_insert_15m[] = $array_data_row;

					}

				}
			} else {
				$response = array("success" => false, "message" => "No existe una estación con el código ".$load_code_api.".");
				return $this->response($response);
				exit();
			}

        }

        foreach($array_frequency_1h as $load_code_api => $array_data){

			$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

			if($station->id){
				foreach($array_data as $timestamp => $array_values){

					// $dt = new DateTime('now', new DateTimeZone('UTC'));
					// $dt->setTimestamp($timestamp);
					$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
					$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

					$data_variables = array();
					foreach($array_values as $sigla_variable => $value){
						
						$id_variable = $this->Air_variables_model->get_one_where(array(
							"sigla_api" => $sigla_variable,
							"deleted" => 0
						))->id;

						$is_variable_in_station = $this->Air_stations_rel_variables_model->get_one_where(array(
							"id_air_station" => $station->id,
							"id_air_variable" => $id_variable,
							"deleted" => 0
						))->id;

						if($is_variable_in_station){
							$data_variables[$id_variable] = (string)$value;
						} else {
							$data_variables[$sigla_variable] = (string)$value;
						}

					}

					// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
					$record = $this->Air_stations_values_1h_model->get_one_where(array(
						"id_station" => $station->id,
						"timestamp" => $timestamp,
						"deleted" => 0
					));

					if($record->id){

						$array_data_row = array(
							"data" => json_encode($data_variables),
							"modified" => get_current_utc_time(),
							"modified_by" => 11 // API USER
						);
						$save_id = $this->Air_stations_values_1h_model->save($array_data_row, $record->id);

					} else {

						$array_data_row = array(
							"id_station" => $station->id,
							"timestamp" => $timestamp,
							"date" => $dt->format('Y-m-d'),
							"hour" => $dt->format('H'),
							"minute" => $dt->format('i'),
							"data" => json_encode($data_variables),
							"created" => get_current_utc_time(),
							"created_by" => 11 // API USER
						);
	
						$array_insert_1h[] = $array_data_row;

					}
				
				}
			} else {
				$response = array("success" => false, "message" => "No existe una estación con el código ".$load_code_api.".");
				return $this->response($response);
				exit();
			}

        }
        
		// BULK LOAD
        $bulk_load_5m = $this->Air_stations_values_5m_model->bulk_load($array_insert_5m);
        $bulk_load_15m = $this->Air_stations_values_15m_model->bulk_load($array_insert_15m);
        $bulk_load_1h = $this->Air_stations_values_1h_model->bulk_load($array_insert_1h);

        if($bulk_load_5m || $bulk_load_15m || $bulk_load_1h){

            $response = array(
				"success" => true, 
				"message" => "Registros ingresados",
				"5m" => $bulk_load_5m ? "Ingresado Ok" : "No se ingresaron registros",
				"15m" => $bulk_load_15m ? "Ingresado Ok" : "No se ingresaron registros",
				"1h" => $bulk_load_1h ? "Ingresado Ok" : "No se ingresaron registros",
			);

        } else {
            $response = array("success" => false, "message" => "No se ingresó ningún registro");
        }

		return $this->response($response);

	}

	function monitoring_data_1m_post(){

		$token = $this->input->get_request_header('Authorization', true);

		// VALIDO SI VIENE EL TOKEN (TOPIC)
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}

		// VALIDO SI EXISTE TOKEN
		$existe_token = $token == "4b24b94a8df79c5656652fbe8ca8d3939165985b" ? true : false;
		if(!$existe_token){
			return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
			exit();
		}

		$array_data = json_decode(file_get_contents("php://input"), true); // POSTMAN: Body - raw

		$array_frequency_1m = $array_data["1m"];
		$array_insert_1m = array();

		foreach($array_frequency_1m as $load_code_api => $array_data){

			$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

			if($station->id){
				foreach($array_data as $timestamp => $array_values){

					// $dt = new DateTime('now', new DateTimeZone('UTC'));
					// $dt->setTimestamp($timestamp);
					$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
					$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile
					
					$data_variables = array();
					foreach($array_values as $sigla_variable => $value){
						
						$id_variable = $this->Air_variables_model->get_one_where(array(
							"sigla_api" => $sigla_variable,
							"deleted" => 0
						))->id;

						$is_variable_in_station = $this->Air_stations_rel_variables_model->get_one_where(array(
							"id_air_station" => $station->id,
							"id_air_variable" => $id_variable,
							"deleted" => 0
						))->id;

						if($is_variable_in_station){
							$data_variables[$id_variable] = (string)$value;
						} else {
							$data_variables[$sigla_variable] = (string)$value;
						}

					}

					// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
					$record = $this->Air_stations_values_1m_model->get_one_where(array(
						"id_station" => $station->id,
						"timestamp" => $timestamp,
						"deleted" => 0
					));

					if($record->id){

						$array_data_row = array(
							"data" => json_encode($data_variables),
							"modified" => get_current_utc_time(),
							"modified_by" => 11 // API USER
						);
						$save_id = $this->Air_stations_values_1m_model->save($array_data_row, $record->id);

					} else {

						$array_data_row = array(
							"id_station" => $station->id,
							"timestamp" => $timestamp,
							"date" => $dt->format('Y-m-d'),
							"hour" => $dt->format('H'),
							"minute" => $dt->format('i'),
							"data" => json_encode($data_variables),
							"created" => get_current_utc_time(),
							"created_by" => 11 // API USER
						);
	
						$array_insert_1m[] = $array_data_row;

					}

				}
			} else {
				$response = array("success" => false, "message" => "No existe una estación con el código ".$load_code_api.".");
				return $this->response($response);
				exit();
			}

        }

		// BULK LOAD
        $bulk_load_1m = $this->Air_stations_values_1m_model->bulk_load($array_insert_1m);
		
        if($bulk_load_1m){
            $response = array("success" => true, "message" => "Registros ingresados correctamente");
        } else {
            $response = array("success" => false, "message" => "Error en el ingreso de registros");
        }

		return $this->response($response);

	}

}
