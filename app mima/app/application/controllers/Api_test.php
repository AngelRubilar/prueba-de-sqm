<?php
set_time_limit(200);

//ini_set('LimitRequestBody', 100024000);
//ini_set('post_max_size', 100024000);
//ini_set('upload_max_filesize', 100024000);
/*if (!defined('BASEPATH'))
    exit('No direct script access allowed');*/
    //header('Access-Control-Allow-Origin: *');

require(APPPATH.'/libraries/REST_Controller.php');

class Api_test extends REST_Controller {

    public function __construct() {
        parent::__construct();
    }
	
	// ENDPOINT QUE SIMULA LA API DE MLP PARA OBTENER DATOS POR FRECUENCIA
    public function get_data_by_frequency_post() {

		//var_dump(scandir('/tmp/'));
		//$token = $this->input->get_request_header('auth', true);
		$token = $this->input->get_request_header('Authorization', true);

		// VALIDO SI VIENE EL TOKEN (TOPIC)
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}

		// VALIDO SI EXISTE TOKEN
		$existe_token = $token == "123456" ? true : false;
		if(!$existe_token){
			return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
			exit();
		}
		
		$frequency = $this->post("frequency");

		// $response = "Frequency: ".$frequency;
		// return $this->response($response);

		$now = new DateTime();
        $now->setTimezone(new DateTimeZone(get_setting('timezone')));
		$today_date = $now->format("Y-m-d");
		$current_hour = $now->format("H") - 1;
        $today_datetime = $now->format("Y-m-d H:i:s");

		$array_data = array();
		if($frequency == "1m"){

			for($i = 0; $i <= 59; $i++){

				$hora_minuto = $i < 10 ? $current_hour.":0".$i.":00" : $current_hour.":".$i.":00";

				$array_data[$frequency]["estacion_a"][$today_date." ".$hora_minuto] = array(
					"PM1" => rand(10,100) / 10,
					"PM2.5" => rand(10,100) / 10,
					"PM10" => rand(10,100) / 10,
					"PM100" => rand(10,100) / 10,
					"V" => rand(10,100) / 10,
					"dV" => rand(10,100) / 10,
					"T" => rand(10,100) / 10,
					"H" => rand(10,100) / 10,
				);
				$array_data[$frequency]["estacion_b"][$today_date." ".$hora_minuto] = array(
					"PM1" => rand(10,100) / 10,
					"PM2.5" => rand(10,100) / 10,
					"PM10" => rand(10,100) / 10,
					"PM100" => rand(10,100) / 10,
					"V" => rand(10,100) / 10,
					"dV" => rand(10,100) / 10,
					"T" => rand(10,100) / 10,
					"H" => rand(10,100) / 10,
				);
			}

		}

		else if($frequency == "5m"){

			for($i = 0; $i <= 59; $i+=5){

				$hora_minuto = $i < 10 ? $current_hour.":0".$i.":00" : $current_hour.":".$i.":00";

				$array_data[$frequency]["estacion_a"][$today_date." ".$hora_minuto] = array(
					"PM1" => rand(10,100) / 10,
					"PM2.5" => rand(10,100) / 10,
					"PM10" => rand(10,100) / 10,
					"PM100" => rand(10,100) / 10,
					"V" => rand(10,100) / 10,
					"T" => rand(10,100) / 10,
					"H" => rand(10,100) / 10,
				);
				$array_data[$frequency]["estacion_b"][$today_date." ".$hora_minuto] = array(
					"PM1" => rand(10,100) / 10,
					"PM2.5" => rand(10,100) / 10,
					"PM10" => rand(10,100) / 10,
					"PM100" => rand(10,100) / 10,
					"V" => rand(10,100) / 10,
					"T" => rand(10,100) / 10,
					"H" => rand(10,100) / 10,
				);
			}

		}

		else if($frequency == "15m"){

			for($i = 0; $i <= 59; $i+=15){

				$hora_minuto = $i < 10 ? $current_hour.":0".$i.":00" : $current_hour.":".$i.":00";

				$array_data[$frequency]["estacion_a"][$today_date." ".$hora_minuto] = array(
					"PM1" => rand(10,100) / 10,
					"PM2.5" => rand(10,100) / 10,
					"PM10" => rand(10,100) / 10,
					"PM100" => rand(10,100) / 10,
					"V" => rand(10,100) / 10,
					"T" => rand(10,100) / 10,
					"H" => rand(10,100) / 10,
				);
				$array_data[$frequency]["estacion_b"][$today_date." ".$hora_minuto] = array(
					"PM1" => rand(10,100) / 10,
					"PM2.5" => rand(10,100) / 10,
					"PM10" => rand(10,100) / 10,
					"PM100" => rand(10,100) / 10,
					"V" => rand(10,100) / 10,
					"T" => rand(10,100) / 10,
					"H" => rand(10,100) / 10,
				);
			}

		}

		else if($frequency == "1h"){

			$hora_minuto = $current_hour < 10 ? "0".$current_hour.":00:00" : $current_hour.":00:00";

			$array_data[$frequency] = array(
				"estacion_a" => array(
					"datetime" => $today_date." ".$hora_minuto,
					"data" => array(
						"PM1" => rand(10,100) / 10,
						"PM2.5" => rand(10,100) / 10,
						"PM10" => rand(10,100) / 10,
						"PM100" => rand(10,100) / 10,
						"V" => rand(10,100) / 10,
						"DV" => rand(10,100) / 10,
						"T" => rand(10,100) / 10,
						"H" => rand(10,100) / 10,
						// "lat" => "-31.734282",
						// "lot" => "-70.490844",
						// "Aceleracion" => rand(10,100) / 10,
					)
				),
				"estacion_b" => array(
					"datetime" => $today_date." ".$hora_minuto,
					"data" => array(
						"PM1" => rand(10,100) / 10,
						"PM2.5" => rand(10,100) / 10,
						"PM10" => rand(10,100) / 10,
						"PM100" => rand(10,100) / 10,
						"V" => rand(10,100) / 10,
						"DV" => rand(10,100) / 10,
						"T" => rand(10,100) / 10,
						"H" => rand(10,100) / 10,
						// "lat" => "-31.734282",
						// "lot" => "-70.490844",
						// "Aceleracion" => rand(10,100) / 10,
					)
				)
			);

		}

		else if($frequency == "1h_v2"){

			$hora_minuto = $current_hour < 10 ? "0".$current_hour.":00:00" : $current_hour.":00:00";

			$array_data[$frequency]["estacion_a"][$today_date." ".$hora_minuto] = array(
				"PM1" => rand(10,100) / 10,
				"PM2.5" => rand(10,100) / 10,
				"PM10" => rand(10,100) / 10,
				"PM100" => rand(10,100) / 10,
				"V" => rand(10,100) / 10,
				"DV" => rand(10,100) / 10,
				"T" => rand(10,100) / 10,
				"H" => rand(10,100) / 10,
				// "lat" => "-31.734282",
				// "lot" => "-70.490844",
				// "Aceleracion" => rand(10,100) / 10,
			);
				
			$array_data[$frequency]["estacion_b"][$today_date." ".$hora_minuto] = array(
				"PM1" => rand(10,100) / 10,
				"PM2.5" => rand(10,100) / 10,
				"PM10" => rand(10,100) / 10,
				"PM100" => rand(10,100) / 10,
				"V" => rand(10,100) / 10,
				"DV" => rand(10,100) / 10,
				"T" => rand(10,100) / 10,
				"H" => rand(10,100) / 10,
				// "lat" => "-31.734282",
				// "lot" => "-70.490844",
				// "Aceleracion" => rand(10,100) / 10,
			);

		}

		else {
			return $this->response("La frecuencia ".$frequency. " no está disponible.");
		}
		// if($frequency == "8h"){

		// }
		// if($frequency == "12h"){

		// }
		// if($frequency == "24h"){

		// }
		return $this->response($array_data);

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

        foreach($array_frequency_5m as $station_code_api => $array_data){

			if($station_code_api == "761"){
                $id_station = 1;
            }
            if($station_code_api == "789"){
                $id_station = 2;
            }
            if($station_code_api == "Meteorologia001"){
                $id_station = 3;
            }

            foreach($array_data as $timestamp => $array_values){

                $dt = new DateTime('now', new DateTimeZone('UTC'));
				$dt->setTimestamp($timestamp);

                $data_variables = array();
                foreach($array_values as $sigla_variable => $value){
                    $data_variables[$sigla_variable] = $value;
                }

                $array_data_row = array(
                    "id_station" => $id_station,
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

        foreach($array_frequency_15m as $station_code_api => $array_data){

			if($station_code_api == "761"){
                $id_station = 1;
            }
            if($station_code_api == "789"){
                $id_station = 2;
            }
            if($station_code_api == "Meteorologia001"){
                $id_station = 3;
            }

            foreach($array_data as $timestamp => $array_values){

                $dt = new DateTime('now', new DateTimeZone('UTC'));
				$dt->setTimestamp($timestamp);

                $data_variables = array();
                foreach($array_values as $sigla_variable => $value){
                    $data_variables[$sigla_variable] = $value;
                }

                $array_data_row = array(
                    "id_station" => $id_station,
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

        foreach($array_frequency_1h as $station_code_api => $array_data){

			if($station_code_api == "761"){
                $id_station = 1;
            }
            if($station_code_api == "789"){
                $id_station = 2;
            }
            if($station_code_api == "Meteorologia001"){
                $id_station = 3;
            }

            foreach($array_data as $timestamp => $array_values){

				$dt = new DateTime('now', new DateTimeZone('UTC'));
				$dt->setTimestamp($timestamp);

                $data_variables = array();
                foreach($array_values as $sigla_variable => $value){
                    $data_variables[$sigla_variable] = $value;
                }

                $array_data_row = array(
                    "id_station" => $id_station,
					"date" => $dt->format('Y-m-d'),
					"hour" => $dt->format('H'),
                    "data" => json_encode($data_variables, true),
                    "created" => get_current_utc_time(),
                    "created_by" => 11 // API USER
                );

                $array_insert_1h[] = $array_data_row;

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

		foreach($array_frequency_1m as $station_code_api => $array_data){

			if($station_code_api == "761"){
                $id_station = 1;
            }
            if($station_code_api == "789"){
                $id_station = 2;
            }
            if($station_code_api == "Meteorologia001"){
                $id_station = 3;
            }

            foreach($array_data as $timestamp => $array_values){

                $dt = new DateTime('now', new DateTimeZone('UTC'));
				$dt->setTimestamp($timestamp);
				
                $data_variables = array();
                foreach($array_values as $sigla_variable => $value){
                    $data_variables[$sigla_variable] = $value;
                }

                $array_data_row = array(
                    "id_station" => $id_station,
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
