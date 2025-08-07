<?php
/**
 * Archivo Controlador para submódulo "Eficiencia" de módulo Monitoreo  (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Monitoreo
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Archivo Controlador para submódulo "Eficiencia" de módulo Monitoreo (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Monitoreo
 * @property private $id_modulo_cliente id del módulo Monitoreo (17)
 * @property private $id_submodulo_cliente id del submódulo Eficiencia (35)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_monitoring_efficiency extends MY_Controller {
	
	/**
	 * id_modulo_cliente
	 * @var int $id_modulo_cliente
	 */
	private $id_modulo_cliente;
	/**
	 * id_submodulo_cliente
	 * @var int $id_submodulo_cliente
	 */
	private $id_submodulo_cliente;
	
	/**
	 * __construct
	 * 
	 * Constructor
	 */
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 17;
		$this->id_submodulo_cliente = 35;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		

		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
		
    }

	/**
	 * index
	 * 
	 * Carga la página que grafica los valores capturados por las estaciones.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return resource Vista principal del sub-módulo
	 */
    function index() {
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_monitor_efficiency');


		/*
			Mostrar estaciones:
			- ID 14 (Estación MP - Chancador 1, equipo fijo (826)) 				| Basal = 6350 [ug/m3]
			- ID 7  (Estación MP - Correa 7, equipo fijo área TP2 (803)) 		| Basal = 4783.6 [ug/m3]
			- ID 6  (Estación MP - Molienda, equipo fijo área molienda (789)) 	| Basal = 1126.6 [ug/m3]
			- ID 11 (Estación MP - Stock Pile, equipo fijo (794)) 				| Basal = 548.1 [ug/m3]
		*/

		$array_data = array();
		$ids_stations = array(1, 2, 3, 4, 5, 6);

		foreach($ids_stations as $id_station){

			// $view_data["id_station"] = $id_station;
			$station = $this->Air_stations_model->get_one($id_station);
			$array_data[$id_station]["station"] = $station;
			$station_variables = $this->Air_variables_model->get_variables_of_station($id_station)->result();
			$array_data[$id_station]['station_variables'] = $station_variables;

			// OBTENER DATOS DE FRECUENCIA 1 HORA
			$seconds = 60*60*24*30; // Un mes en segundos
			$options = array('id_station' => $id_station, 'seconds' => $seconds);
			$result_1h = $this->Air_stations_values_1h_model->get_last_period_values($options)->result();
			$array_variables_data_1h = $this->generate_chart_data($id_station, $station_variables, $result_1h);
			$array_charts_data['1hour'] = $array_variables_data_1h;
			$array_data[$id_station]['array_charts_data'] = $array_charts_data;

			// OBTENER UNIDAD DE MEDIDA PARA PONER EN EL EJE Y
			$variable_unidad = array();
			foreach($station_variables as $variable) {
				$options = array(
					'id_cliente' => $id_cliente,
					'id_proyecto' => $id_proyecto,
					'id_tipo_unidad' => $variable->id_unit_type,
				);
				$units = $this->Reports_units_settings_model->get_units($options)->result();
				$variable_unidad[$variable->id_variable]['nombre'] = $units[0]->nombre;
				$variable_unidad[$variable->id_variable]['nombre_real'] = $units[0]->nombre_real;
			}
			$array_data[$id_station]['variable_unidad'] = $variable_unidad;

		}

		$view_data["array_data"] = $array_data;

		$this->template->rander("air_monitoring_efficiency/index", $view_data);
    }


	/** Se genearan los datos necesario para los gráficos de una estación */
	function generate_chart_data($id_station, $station_variables, $result_data, $time_range_name = ''){
		
		/*
			- ID 14 (Estación MP - Chancador 1, equipo fijo (826)) 				| Basal = 6350 [ug/m3]
			- ID 7  (Estación MP - Correa 7, equipo fijo área TP2 (803)) 		| Basal = 4783.6 [ug/m3]
			- ID 6  (Estación MP - Molienda, equipo fijo área molienda (789)) 	| Basal = 1126.6 [ug/m3]
			- ID 11 (Estación MP - Stock Pile, equipo fijo (794)) 				| Basal = 548.1 [ug/m3]
		*/
		$base_value = null;
		if($id_station == 1){ // Estación MP - Chancador 1, equipo fijo (826)
			$base_value = 6350;
		} elseif($id_station == 2){ // Estación MP - Correa 7, equipo fijo área TP2 (803))
			$base_value = 4783.6;
		} elseif($id_station == 3){ // Estación MP - Molienda, equipo fijo área molienda (789)
			$base_value = 1126.6;
		} elseif($id_station == 6){ // Estación MP - Stock Pile, equipo fijo (794)
			$base_value = 548.1;
		} elseif($id_station == 4){
			$base_value = 4783.6;
		} elseif($id_station == 5){
			$base_value = 6350;
		}


		$array_variables_data = array();
		foreach($station_variables as $variable){
			$array_variables_data[$variable->id_variable] = array(
				'name' => $variable->sigla,
				'data' => array()
			);
			
			$array_datetimes_values = array();
			foreach($result_data as $result){

				if($time_range_name == 'values_1h'){
					$str_fecha = $result->date." ".$result->hour.":00:00";
				}else{
					$str_fecha = $result->date." ".$result->hour.":".$result->minute.":00";
				}
				
				$datetime = date_to_timestamp_millis_utc($str_fecha);
			
				$json_values = json_decode($result->data, true);

				// SI LA VARIABLE ES MP10 (ID 9) DIVIDIR POR 6350 Y MULTIPLICAR POR 100
				if($variable->id_variable == 9){

					if($base_value != null && (float)$json_values[$variable->id_variable] > $base_value){
						$value = 0;
					} else {
						$value = ( ( $base_value - (float)$json_values[$variable->id_variable] ) / $base_value ) * 100;
					}
					
				} else {
					$value = (float)$json_values[$variable->id_variable];
				}

				$array_datetimes_values[] = array($datetime, $value);
				// $array_variables_data[$variable->id_variable]['data'][] = array($datetime, $value);
				
			}

			// ORDENAR DATA POR FECHAS
			usort($array_datetimes_values, function($array1, $array2){
				return $array1[0] - $array2[0];
			});

			$array_variables_data[$variable->id_variable]['data'] = $array_datetimes_values;

		}
		return $array_variables_data;
	}


	function get_values_by_date(){

		$time_range = $this->input->post('time_range');
		$id_station = $this->input->post('id_station');
		$id_variable = $this->input->post('id_variable');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		
		// $start_timestamp = strtotime($start_date);
		// $end_timestamp = strtotime($end_date);
		// echo $start_timestamp.'<br>';
		// echo $end_timestamp;exit;

		if(!$start_date || !$end_date){
			echo json_encode(array("success" => false, 'message' => lang('startdate_enddate_msg')));exit;
		}
		if($start_date > $end_date){
			echo json_encode(array("success" => false, 'message' => lang('startdate_enddate_msg_2')));exit;

		}

		$variable = $this->Air_variables_model->get_variables_of_station($id_station, $id_variable)->result();

		$options = array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'id_station' => $id_station,
			'id_variable' => $id_variable
		);
		$array_variables_data = array();
		
		if($time_range == '1hour'){
			$results = $this->Air_stations_values_1h_model->get_details($options)->result();
			
			$array_variables_data = $this->generate_chart_data($id_station, $variable, $results);
		}	

		echo json_encode(array("success" => true, "data" => $array_variables_data));

	}

	function clean_data(){
		
		$time_range = $this->input->post('time_range');
		$id_station = $this->input->post('id_station');
		$id_variable = $this->input->post('id_variable');

		$variable = $this->Air_variables_model->get_variables_of_station($id_station, $id_variable)->result();

		$array_variables_data = array();

		if($time_range == '1hour'){
			// OBTENER DATOS DE FRECUENCIA 1 HORA
			$seconds = 60*60*24*30; // Un mes en segundos
			$options = array('id_station' => $id_station, 'seconds' => $seconds);
			$result_1h = $this->Air_stations_values_1h_model->get_last_period_values( $options )->result();
		
			$array_variables_data = $this->generate_chart_data($id_station, $variable, $result_1h);
				
		}
		
		echo json_encode(array("success" => true, "data" => $array_variables_data));
	}

}

