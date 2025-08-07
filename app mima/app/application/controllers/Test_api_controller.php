<?php
set_time_limit(200);

//ini_set('LimitRequestBody', 100024000);
//ini_set('post_max_size', 100024000);
//ini_set('upload_max_filesize', 100024000);
/*if (!defined('BASEPATH'))
    exit('No direct script access allowed');*/
//header('Access-Control-Allow-Origin: *');

require(APPPATH . '/libraries/REST_Controller.php');

class Test_api_controller extends REST_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index_get()
  {
    $data = [
      'response' => 'Este es un Get Response',
    ];
    $this->response($data, REST_Controller::HTTP_OK); // estatus 200 OK
  }

  public function index_post()
  {
    $postData = $this->input->post();
    $response = ['recibido' => $postData];
    $this->response($response, REST_Controller::HTTP_OK); // estatus 200 OK
  }

  public function hola_get()
  {
    $data = [
      'response' => 'Hola Mundo',
    ];
    $this->response($data, REST_Controller::HTTP_OK); // estatus 200 OK
  }
  public function monitoring_data_1m_refactor_post()
  {

    $token = $this->input->get_request_header('Authorization', true);
    if (!$token || $token != "4b24b94a8df79c5656652fbe8ca8d3939165985b") {
      return $this->response(["message" => "Fallo de autenticidad!", "success" => false]);
    }

    $array_data = json_decode(file_get_contents("php://input"), true); // recibe en json y transforma a array asociativa
    if (empty($array_data["1m"])) {
      return $this->response(["1m" => "Info: No se ingresaron registros para la frecuencia."]);
    }
    // log_message('error', 'primer nivel: superado ');
    $load_codes_api = array_keys($array_data["1m"]);
    // log_message('error', 'load code apis ' . json_encode($load_codes_api));
    $stations = $this->Air_stations_model->get_stations_by_load_codes($load_codes_api)->result();// TODO: traer solo load_api_code y id
    if (empty($stations)) {
      return $this->response(["1m" => "Error: Ninguna estación coincide."]); // TODO: validar HTTP response code
    }

    $station_ids_by_load_code = [];
    foreach ($stations as $station) {
      $station_ids_by_load_code[$station->load_code_api] = $station->id;
    }

    log_message('error', 'Tercer nivel: Asignar ids de estación a códigos de carga para evitar búsquedas repetitivas. ' . implode(',', $station_ids_by_load_code));
    // Obtener todas las variables relacionadas de las estaciones
    $variables_rel_station = $this->Air_stations_rel_variables_model->get_all_related_variables(array_values($station_ids_by_load_code))->result(); // ???
    log_message('error', 'Cuarto nivel: Valores de get_all_related_variables ' . json_encode($variables_rel_station));
    // Procesar datos
    $response = ["1m" => []];
    // return $this->response($response);
    foreach ($array_data["1m"] as $load_code_api => $station_data) {
      if (!isset($station_ids_by_load_code[$load_code_api])) {
        $response["1m"][$load_code_api] = "Error: Estación no existe.";
        continue;
      }

      $station_id = $station_ids_by_load_code[$load_code_api]; // value
      // log_message('error', 'Quinto nivel: Valores de station_id ' . $station_id);
      $relevant_variables = $variables_rel_station[$station_id] ?? [];
      log_message('error', 'Sexto nivel: Valores de relevant_variables ' . json_encode($relevant_variables));

      foreach ($station_data as $timestamp => $sensor_values) {
        // Procesamiento de los datos...
        // Lógica para manejar los datos de la estación y las variables
        // Este es un buen lugar para manejar las inserciones o actualizaciones en la base de datos
        $dt = new DateTime("@$timestamp", new DateTimeZone('UTC')); // Crear un objeto DateTime con la zona horaria UTC
        $dt->setTimeZone(new DateTimeZone('America/Santiago')); // Cambiar la zona horaria a Santiago de Chile

        $data_variables = [];
        foreach ($sensor_values as $sigla => $value) {

          $id_air_variable = $this->Air_variables_model->get_one_where(
            array(
              "sigla_api" => $sigla,
              "deleted" => 0
            )
          )->id;
          if (array_key_exists($id_air_variable, $variables_rel_station)) {
            $data_variables[$id_air_variable] = (string) $value;
          } else {
            $data_variables[$sigla] = (string) $value;
          }
          log_message('error', 'Septimo nivel: Valores de data_variables ' . json_encode($data_variables));
        }
        foreach ($variables_rel_station as $id_air_variable => $value) {

          $variable = $this->Air_variables_model->get_one($id_air_variable);
          if (array_key_exists($id_air_variable, $data_variables)) {
            $response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: Ok";
          } else {
            $response["1m"][$load_code_api][$timestamp][$variable->sigla_api] = "Info: No se recibió variable " . $variable->sigla_api . " (" . $variable->name . ")";
          }

        }

        // SI YA SE HA REGISTRADO DATA PARA UNA ESTACIÓN CON EL MISMO TIMESTAMP, SE ACTUALIZA EL REGISTRO. SI NO, SE INGRESA EL NUEVO REGISTRO
        $record = $this->Air_stations_values_1m_model->get_one_where(
          array(
            "id_station" => $station_id,
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
            "id_station" => $station_id,
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


      if ($save_id_1m) {
        $response["success"] = true;
        return $this->response($response);
      } else {
        return $this->response(array("success" => false, "message" => "Info: No se ingresaron registros."));
      }

    }


  }
  public function monitoring_data_1m_refactor_get()
  {
    $data = [
      'response' => 'MONITORING REFACTOR 1M FUNCIONA GET',
    ];
    $this->response($data, REST_Controller::HTTP_OK); // estatus 200 OK


  }
}
