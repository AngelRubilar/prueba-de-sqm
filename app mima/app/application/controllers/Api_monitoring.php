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

    function monitoring_data_post(){

        $token = $this->input->get_request_header('Authorization', true);

        // VALIDO SI VIENE EL TOKEN (TOPIC)
        if (!$token) {
            return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
            exit();
        }

        // VALIDO SI EXISTE TOKEN
        $existe_token = $token == "ISCSL9bXp928IxQzM636fRwtx94YlSwv9vbz1OGh" ? true : false;
        //log_message('error', "tenemos token");
        if ($existe_token) {
            log_message('error', "sera que esta malo el token");
            return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
            exit();
        }

        //$data = $this->post("data"); // POSTMAN: Body - form-data
        //$array_data = json_decode($data, true);
        // return $this->response($array_data);
        // exit();
        $data = json_decode(file_get_contents("php://input"), true); // POSTMAN: Body - raw

        $array_data = $data;

        // 	// // Guardar los datos en un archivo JSON
        $json_data = json_encode($array_data);
        $filename = 'data_monitoring_' . date('YmdHis') . '.json';
        $file_path = FCPATH . 'files/API/' . $filename;
        file_put_contents($file_path, $json_data);



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

                    $array_data_row = array(
                        "id_station" => $station->id,
                        "timestamp" => $timestamp,
                        "date" => $dt->format('Y-m-d'),
                        "hour" => $dt->format('H'),
                        "minute" => $dt->format('i'),
                        "data" => json_encode($data_variables),
                        "created" => get_current_utc_time(),
                        "created_by" => 6 // API USER
                    );

                    $array_insert_5m[] = $array_data_row;

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

                    $array_data_row = array(
                        "id_station" =>  $station->id,
                        "timestamp" => $timestamp,
                        "date" => $dt->format('Y-m-d'),
                        "hour" => $dt->format('H'),
                        "minute" => $dt->format('i'),
                        "data" => json_encode($data_variables),
                        "created" => get_current_utc_time(),
                        "created_by" => 6 // API USER
                    );

                    $array_insert_15m[] = $array_data_row;

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

                    $array_data_row = array(
                        "id_station" => $station->id,
                        "timestamp" => $timestamp,
                        "date" => $dt->format('Y-m-d'),
                        "hour" => $dt->format('H'),
                        "minute" => $dt->format('i'),
                        "data" => json_encode($data_variables),
                        "created" => get_current_utc_time(),
                        "created_by" => 6 // API USER
                    );

                    $array_insert_1h[] = $array_data_row;

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

    function monitoring_data_1m_post()
    {

        /*$token = $this->input->get_request_header('Authorization', true);
        if (!$token || $token != "ISCSL9bXp928IxQzM636fRwtx94YlSwv9vbz1OGh") {
            return $this->response(["message" => "Fallo de autenticidad!", "success" => false]);
        }*/
        $token = $this->input->get_request_header('Authorization', true);

        // VALIDO SI VIENE EL TOKEN (TOPIC)
        if (!$token) {
            return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
            exit();
        }

        // VALIDO SI EXISTE TOKEN
        $existe_token = $token == "ISCSL9bXp928IxQzM636fRwtx94YlSwv9vbz1OGh" ? true : false;
        //log_message('error', "tenemos token");
        if ($existe_token) {
            log_message('error', "sera que esta malo el token");
            return $this->response(array("message" => "Fallo de autenticidad! token incorrecto.", "success" => false));
            exit();
        }


        $array_data = json_decode(file_get_contents("php://input"), true); // recibe en json y transforma a array asociativa
        if (empty($array_data["1m"])) {
            return $this->response(["1m" => "Info: No se ingresaron registros para la frecuencia."]);
        }


        // 	// // Guardar los datos en un archivo JSON
        $json_data = json_encode($array_data);
        $filename = 'data_monitoring_' . date('YmdHis') . '.json';
        $file_path = FCPATH . 'files/API/' . $filename;
        file_put_contents($file_path, $json_data);


        $load_codes_api = array_keys($array_data["1m"]);
        $stations = $this->Air_stations_model->get_stations_by_load_codes($load_codes_api)->result();// TODO: traer solo load_api_code y id
        if (empty($stations)) {
            $this->output->set_status_header(400);
            return $this->response(["1m" => "Error: Ninguna estación coincide."]); // TODO: validar HTTP response code
        }

        $station_ids_by_load_code = [];
        $guardar_datos_api = [];
        foreach ($stations as $station) {
            $station_ids_by_load_code[$station->load_code_api] = $station->id;
        }
        $variables_rel_station = $this->Air_stations_rel_variables_model->get_all_related_variables(array_values($station_ids_by_load_code))->result(); // ???
        $response = ["1m" => []];
        // Mapear sigla_api a id_air_variable
        $sigla_api_to_id_air_variable = [];
        foreach ($variables_rel_station as $variable) {
            // Verificamos si la sigla es 'PM100' o 'PM10' y la modificamos
            if ($variable->sigla_api == 'PM100') {
                $sigla_api_to_id_air_variable['pm100'] = $variable->id_air_variable;
            } elseif ($variable->sigla_api == 'PM10') {
                $sigla_api_to_id_air_variable['pm10'] = $variable->id_air_variable;
            } else {
                $sigla_api_to_id_air_variable[$variable->sigla_api] = $variable->id_air_variable;
            }
        }


        foreach ($array_data["1m"] as $station => $load_codes_api) {
            if (!isset($station_ids_by_load_code[$station])) {
                $response["1m"][$station] = "Error: No se encontró la estación.";
                continue;
            }

            $station_id = $station_ids_by_load_code[$station];
            $relevant_variables = $variables_rel_station[$station_id] ?? [];

            foreach ($load_codes_api as $timestamp => $sensor_values) {
                // log_message('error', 'Valores de sensor_values: ' . json_encode($sensor_values) . ' y VALOR timestamp: ' . $timestamp);
                // crear objeto DateTime con la zona horaria UTC
                $dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
                $dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

                // guardar los datos
                $data_variables = [];
                foreach ($sensor_values as $sigla => $value) {
                    if (isset($sigla_api_to_id_air_variable[$sigla])) {

                        $id_air_variable = $sigla_api_to_id_air_variable[$sigla];
                        $data_variables[$id_air_variable] = (string) $value;

                    } else {
                        $data_variables[$sigla] = (string) $value;
                    }

                }

                // bucamos si ya existe un registro con el mismo timestamp y estación
                $record = $this->Air_stations_values_1m_model->get_one_where(
                    array(
                        "id_station" => $station_id,
                        "timestamp" => $timestamp,
                        "deleted" => 0
                    )
                );

                if ($record->id) {
                    // con estas lineas se deberia actualizar el registro
                    $array_data_row = array(
                        "data" => json_encode($data_variables),
                        "modified" => get_current_utc_time(),
                        "modified_by" => 6 // API USER
                    );
                    $save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row, $record->id);
                } else {
                    // crea el registro si no existe
                    $array_data_row = array(
                        "id_station" => $station_id,
                        "timestamp" => $timestamp,
                        "date" => $dt->format('Y-m-d'),
                        "hour" => $dt->format('H'),
                        "minute" => $dt->format('i'),
                        "data" => json_encode($data_variables),
                        "created" => get_current_utc_time(),
                        "created_by" => 6 // API USER
                    );
                    $save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row);
                }

                //TODO: pendiente de revisar las response
                // $response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
                // $response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
                //
                // $array_data_row = array(
                //   "id_station" => $station_id,
                //   "timestamp" => $timestamp,
                //   "date" => $dt->format('Y-m-d'),
                //   "hour" => $dt->format('H'),
                //   "minute" => $dt->format('i'),
                //   "data" => json_encode($data_variables),
                //   "created" => get_current_utc_time(),
                //   "created_by" => 11 // API USER
                // );

                // $save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row);
                // log_message('error', 'AQUI ESTA GUARDANDO ' . $save_id_1m);
                // log_message('error', 'Variables almacenadas para BD: ' . json_encode($data_variables));

                // foreach ($variables_rel_station as $id_air_variable => $value) {

                //   $variable = $this->Air_variables_model->get_one($id_air_variable);
                //   if (array_key_exists($id_air_variable, $data_variables)) {
                //     $response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
                //   } else {
                //     $response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
                //   }

                // }
                // Verificar si ya existe un registro con el mismo timestamp


                ;
                //response

                // SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
                // $record = $this->Air_stations_values_1m_model->get_one_where_custom(
                //   array(
                //     "id_station" => $station_id,
                //     "timestamp" => $timestamp,
                //     "deleted" => 0
                //   )
                // );

                // if ($record->id) {
                //   // con estas lineas se deberia actualizar el dato
                //   // $array_data_row = array(
                //   // 	"data" => json_encode($data_variables),
                //   // 	"modified" => get_current_utc_time(),
                //   // 	"modified_by" => 11 // API USER
                //   // );
                //   // $save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row, $record->id);
                // } else {
                //   $array_data_row = array(
                //     "id_station" => $station_id,
                //     "timestamp" => $timestamp,
                //     "date" => $dt->format('Y-m-d'),
                //     "hour" => $dt->format('H'),
                //     "minute" => $dt->format('i'),
                //     "data" => json_encode($data_variables),
                //     "created" => get_current_utc_time(),
                //     "created_by" => 11 // API USER
                //   );
                //   log_message('error', 'NOVENO nivel: Valores de save_id_1m ' . $save_id_1m);
                //   $save_id_1m = $this->Air_stations_values_1m_model->save($array_data_row);
                // }

            }


        }
        // if ($save_id_1m) {
        if ($save_id_1m) {
            $response["success"] = true;
            return $this->response($response);
        } else {
            return $this->response(array("success" => false, "message" => "Info: No se ingresaron registros."));
        }


    }


}

