<?php
set_time_limit(200);

//ini_set('LimitRequestBody', 100024000);
//ini_set('post_max_size', 100024000);
//ini_set('upload_max_filesize', 100024000);
/*if (!defined('BASEPATH'))
    exit('No direct script access allowed');*/
//header('Access-Control-Allow-Origin: *');

require(APPPATH . '/libraries/REST_Controller.php');

class Api_monitoring extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	function monitoring_data_post()
	{

		$token = $this->input->get_request_header('Authorization', true);

		// VALIDO SI VIENE EL TOKEN (TOPIC)
		if (!$token) {
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}

		// GENERAR UN TOKEN
		// $token = bin2hex(random_bytes(20));
		// return $this->response($token);

		// VALIDO SI EXISTE TOKEN
		$existe_token = $token == "4b24b94a8df79c5656652fbe8ca8d3939165985b" ? true : false;
		if (!$existe_token) {
			return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
			exit();
		}

		//$data = $this->post("data"); // POSTMAN: Body - form-data
		//$array_data = json_decode($data, true);
		// return $this->response($array_data);
		// exit();
		$array_data = json_decode(file_get_contents("php://input"), true); // POSTMAN: Body - raw

		$array_frequency_5m = $array_data["5m"];
		$array_frequency_15m = $array_data["15m"];
		$array_frequency_1h = $array_data["1h"];
		$response = array();

		// FRECUENCIA 5 MINUTOS
		if (count($array_frequency_5m)) {

			foreach ($array_frequency_5m as $load_code_api => $array_data) {

				$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

				if ($station->id) {

					$variables_rel_station = $this->Air_stations_rel_variables_model->get_dropdown_list(
						array("id_air_variable"), // value
						"id_air_variable", // key
						array("id_air_station" => $station->id, "deleted" => 0) // where
					);
					log_message('error', 'variables_rel_station: ' . json_encode($variables_rel_station));

					foreach ($array_data as $timestamp => $array_values) {

						// $dt = new DateTime('now', new DateTimeZone('UTC'));
						// $dt->setTimestamp($timestamp);
						$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
						$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

						$data_variables = array();

						foreach ($array_values as $sigla => $value) {

							$id_variable = $this->Air_variables_model->get_one_where(
								array(
									"sigla_api" => $sigla,
									"deleted" => 0
								)
							)->id;

							if (in_array($id_variable, $variables_rel_station)) {
								$data_variables[$id_variable] = (string) $value;
							} else {
								$data_variables[$sigla] = (string) $value;
							}

						}

						foreach ($variables_rel_station as $id_variable) {
							$variable = $this->Air_variables_model->get_one($id_variable);
							if (array_key_exists($id_variable, $data_variables)) {
								$response["5m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
							} else {
								$response["5m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
							}
						}

						// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
						$record = $this->Air_stations_values_5m_model->get_one_where(
							array(
								"id_station" => $station->id,
								"timestamp" => $timestamp,
								"deleted" => 0
							)
						);

						if ($record->id) {
							$array_data_row = array(
								"data" => json_encode($data_variables),
								"modified" => get_current_utc_time(),
								"modified_by" => 11 // API USER
							);
							$save_id_5m = $this->Air_stations_values_5m_model->save($array_data_row, $record->id);
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
							$save_id_5m = $this->Air_stations_values_5m_model->save($array_data_row);
						}

					}

				} else {

					$response["5m"][$load_code_api] = "Error: Estación " . $load_code_api . " no existe. Datos no ingresados.";

				}

			}

		} else {
			$response["5m"] = "Info: No se ingresaron registros para la frecuencia.";
		}


		// FRECUENCIA 15 MINUTOS
		if (count($array_frequency_15m)) {

			foreach ($array_frequency_15m as $load_code_api => $array_data) {

				$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

				if ($station->id) {

					$variables_rel_station = $this->Air_stations_rel_variables_model->get_dropdown_list(
						array("id_air_variable"), // value
						"id_air_variable", // key
						array("id_air_station" => $station->id, "deleted" => 0) // where
					);

					foreach ($array_data as $timestamp => $array_values) {

						// $dt = new DateTime('now', new DateTimeZone('UTC'));
						// $dt->setTimestamp($timestamp);
						$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
						$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

						$data_variables = array();

						foreach ($array_values as $sigla => $value) {

							$id_variable = $this->Air_variables_model->get_one_where(
								array(
									"sigla_api" => $sigla,
									"deleted" => 0
								)
							)->id;

							if (in_array($id_variable, $variables_rel_station)) {
								$data_variables[$id_variable] = (string) $value;
							} else {
								$data_variables[$sigla] = (string) $value;
							}

						}

						foreach ($variables_rel_station as $id_variable) {
							$variable = $this->Air_variables_model->get_one($id_variable);
							if (array_key_exists($id_variable, $data_variables)) {
								$response["15m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
							} else {
								$response["15m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
							}
						}

						// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
						$record = $this->Air_stations_values_15m_model->get_one_where(
							array(
								"id_station" => $station->id,
								"timestamp" => $timestamp,
								"deleted" => 0
							)
						);

						if ($record->id) {
							$array_data_row = array(
								"data" => json_encode($data_variables),
								"modified" => get_current_utc_time(),
								"modified_by" => 11 // API USER
							);
							$save_id_15m = $this->Air_stations_values_15m_model->save($array_data_row, $record->id);
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
							$save_id_15m = $this->Air_stations_values_15m_model->save($array_data_row);
						}

					}

				} else {

					$response["15m"][$load_code_api] = "Error: Estación " . $load_code_api . " no existe. Datos no ingresados.";

				}

			}

		} else {
			$response["15m"] = "Info: No se ingresaron registros para la frecuencia.";
		}



		// FRECUENCIA 1 HORA
		if (count($array_frequency_1h)) {

			foreach ($array_frequency_1h as $load_code_api => $array_data) {

				$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

				if ($station->id) {

					$variables_rel_station = $this->Air_stations_rel_variables_model->get_dropdown_list(
						array("id_air_variable"), // value
						"id_air_variable", // key
						array("id_air_station" => $station->id, "deleted" => 0) // where
					);

					foreach ($array_data as $timestamp => $array_values) {

						// $dt = new DateTime('now', new DateTimeZone('UTC'));
						// $dt->setTimestamp($timestamp);
						$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
						$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

						$data_variables = array();

						foreach ($array_values as $sigla => $value) {

							$id_variable = $this->Air_variables_model->get_one_where(
								array(
									"sigla_api" => $sigla,
									"deleted" => 0
								)
							)->id;

							if (in_array($id_variable, $variables_rel_station)) {
								$data_variables[$id_variable] = (string) $value;
							} else {
								$data_variables[$sigla] = (string) $value;
							}

						}

						foreach ($variables_rel_station as $id_variable) {
							$variable = $this->Air_variables_model->get_one($id_variable);
							if (array_key_exists($id_variable, $data_variables)) {
								$response["1h"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
							} else {
								$response["1h"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
							}
						}

						// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
						$record = $this->Air_stations_values_1h_model->get_one_where(
							array(
								"id_station" => $station->id,
								"timestamp" => $timestamp,
								"deleted" => 0
							)
						);

						if ($record->id) {
							$array_data_row = array(
								"data" => json_encode($data_variables),
								"modified" => get_current_utc_time(),
								"modified_by" => 11 // API USER
							);
							$save_id_1h = $this->Air_stations_values_1h_model->save($array_data_row, $record->id);
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
							$save_id_1h = $this->Air_stations_values_1h_model->save($array_data_row);
						}

					}

				} else {

					$response["1h"][$load_code_api] = "Error: Estación " . $load_code_api . " no existe. Datos no ingresados.";

				}

			}

		} else {
			$response["1h"] = "Info: No se ingresaron registros para la frecuencia.";
		}

		// RESPONSE
		if ($save_id_5m || $save_id_15m || $save_id_1h) {
			$response["success"] = true;
			return $this->response($response);
		} else {
			return $this->response(array("success" => false, "message" => "Info: No se ingresaron registros."));
		}

	}


	function monitoring_data_1m_post()
	{

		$token = $this->input->get_request_header('Authorization', true);

		// VALIDO SI VIENE EL TOKEN (TOPIC)
		if (!$token) {
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}

		// VALIDO SI EXISTE TOKEN
		$existe_token = $token == "4b24b94a8df79c5656652fbe8ca8d3939165985b" ? true : false;
		if (!$existe_token) {
			return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
			exit();
		}

		$array_data = json_decode(file_get_contents("php://input"), true); // POSTMAN: Body - raw

		// // Guardar los datos en un archivo JSON
		// $json_data = json_encode($array_data);
		// $filename = 'data_monitoring_' . date('YmdHis') . '.json';
		// $file_path = FCPATH . 'files/API/' . $filename;
		// file_put_contents($file_path, $json_data);


		$array_frequency_1m = $array_data["1m"];
		$response = array();

		// FRECUENCIA 1 MINUTO
		if (count($array_frequency_1m)) {

			foreach ($array_frequency_1m as $load_code_api => $array_data) {

				$station = $this->Air_stations_model->get_one_where(array("load_code_api" => $load_code_api, "deleted" => 0));

				if ($station->id) {

					$variables_rel_station = $this->Air_stations_rel_variables_model->get_dropdown_list(
						array("id_air_variable"), // value
						"id_air_variable", // key
						array("id_air_station" => $station->id, "deleted" => 0) // where
					);

					foreach ($array_data as $timestamp => $array_values) {

						// $dt = new DateTime('now', new DateTimeZone('UTC'));
						// $dt->setTimestamp($timestamp);
						$dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
						$dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

						$data_variables = array();

						foreach ($array_values as $sigla => $value) {

							$id_variable = $this->Air_variables_model->get_one_where(
								array(
									"sigla_api" => $sigla,
									"deleted" => 0
								)
							)->id;

							if (in_array($id_variable, $variables_rel_station)) {
								$data_variables[$id_variable] = (string) $value;
							} else {
								$data_variables[$sigla] = (string) $value;
							}

						}

						foreach ($variables_rel_station as $id_variable) {
							$variable = $this->Air_variables_model->get_one($id_variable);
							if (array_key_exists($id_variable, $data_variables)) {
								$response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
							} else {
								$response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
							}
						}

						// SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
						$record = $this->Air_stations_values_1m_model->get_one_where(
							array(
								"id_station" => $station->id,
								"timestamp" => $timestamp,
								"deleted" => 0
							)
						);

						if ($record->id) {
							// con estas lineas se deberia actualizar el dato 
							// $array_data_row = array(
							// 	"data" => json_encode($data_variables),
							// 	"modified" => get_current_utc_time(),
							// 	"modified_by" => 11 // API USER
							// );
							// $save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row, $record->id);
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
							$save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row);
						}

					}

				} else {

					$response["1m"][$load_code_api] = "Error: Estación " . $load_code_api . " no existe. Datos no ingresados.";

				}

			}

		} else {
			$response["1m"] = "Info: No se ingresaron registros para la frecuencia.";
		}

		// RESPONSE
		if ($save_id_1m) {
			$response["success"] = true;
			return $this->response($response);
		} else {
			return $this->response(array("success" => false, "message" => "Info: No se ingresaron registros."));
		}

	}

}
