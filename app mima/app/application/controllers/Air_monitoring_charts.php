<?php
/**
 * Archivo Controlador para submódulo "Gráficos" de módulo Monitoreo  (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Monitoreo
 * @author Christopher Sam Venegas
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Archivo Controlador para submódulo "Gráficos" de módulo Monitoreo (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Monitoreo
 * @property private $id_modulo_cliente id del módulo Monitoreo (17)
 * @property private $id_submodulo_cliente id del submódulo Gráficos (31)
 * @author Christopher Sam Venegas
 * @version 1.0
 */
class Air_monitoring_charts extends MY_Controller {
	
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
		$this->id_submodulo_cliente = 31;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		

		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
		
    }

	/**
	 * index
	 * 
	 * Carga la página que gráfica los valores capturados por las estaciones.
	 *
	 * @author Christopher Sam Venegas
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
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_monitor_charts');

		// $this->member_allowed($id_proyecto);

		// $user = $this->Users_model->get_one($this->login_user->id);
		// $view_data["user"] = $user;

		$stations = $this->Air_stations_model->get_details(array(
			'id_client' => $id_cliente, 
			'id_project' => $id_proyecto, 
			'is_active' => true,
			'is_monitoring' => true,
			'order_by' => array("is_receptor", "DESC")
		))->result();
		
		
		$view_data['stations'] = $stations;

		$this->template->rander("air_monitoring_charts/index", $view_data);
    }

	/** Se obtiene la vista con los gráficos de una estación */
	function get_station_charts(){
		$contador = 0;
		//log_message('error', 'pasa por el controlador get_station_chart()'.$contador);
;		$contador++;
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$id_station = $this->input->post('id_station');
		$view_data['id_station'] = $id_station;

		$station_variables = $this->Air_variables_model->get_variables_of_station($id_station)->result();

		$view_data['station_variables'] = $station_variables;
		
		// Arreglo para almacenar los datos de todos los gráficos de la estación
		$array_charts_data = array(
			'1min' => array(),
			'5min' => array(),
			'15min' => array(),
			'1hour' => array(),
		);
		
		// OBTENER DATOS DE FRECUENCIA 1 MINUTO

		/* $date = new DateTime();
		$date->sub(new DateInterval('PT1H')); // tiempo actual menos 1 hora
		$start_timestamp = $date->getTimestamp();

		$options = array('id_station' => $id_station, 'start_timestamp' => $start_timestamp);
		$result_1m = $this->Air_stations_values_1m_model->get_details($options)->result();
		 */
		$seconds = 60*60; // Una hora en segundos
		$options = array('id_station' => $id_station, 'seconds' => $seconds);
		
		$result_1m = $this->Air_stations_values_1m_model->get_last_period_values( $options )->result();
		
		$array_variables_data_1m = $this->generate_chart_data($station_variables, $result_1m, 'values_1m');
		
		$array_charts_data['1min'] = $array_variables_data_1m;


		// OBTENER DATOS DE FRECUENCIA 5 MINUTOS

		/* $date = new DateTime();
		$date->sub(new DateInterval('P1D')); // tiempo actual menos 1 día
		$start_timestamp = $date->getTimestamp();

		$options = array('id_station' => $id_station, 'start_timestamp' => $start_timestamp);
		
		$result_5m = $this->Air_stations_values_5m_model->get_details( $options )->result() 
		*/;
		
		$seconds = 60*60*24; // Un día en segundos
		$options = array('id_station' => $id_station, 'seconds' => $seconds);
		
		$result_5m = $this->Air_stations_values_5m_model->get_last_period_values( $options )->result();
		
		$array_variables_data_5m = $this->generate_chart_data($station_variables, $result_5m, 'values_5m');
		
		$array_charts_data['5min'] = $array_variables_data_5m;


		// OBTENER DATOS DE FRECUENCIA 15 MINUTOS
		
		/* $date = new DateTime();
		$date->sub(new DateInterval('P1W')); // tiempo actual menos 1 semana
		$start_timestamp = $date->getTimestamp();

		$options = array('id_station' => $id_station, 'seconds' => $start_timestamp);
		$result_15m = $this->Air_stations_values_15m_model->get_details( $options )->result(); */
		
		$seconds = 60*60*24*7; // Una semana en segundos
		$options = array('id_station' => $id_station, 'seconds' => $seconds);
		
		$result_15m = $this->Air_stations_values_15m_model->get_last_period_values( $options )->result();

		$array_variables_data_15m = $this->generate_chart_data($station_variables, $result_15m, 'values_15m');
		
		$array_charts_data['15min'] = $array_variables_data_15m;

		
		// OBTENER DATOS DE FRECUENCIA 1 HORA
		
		/* $date = new DateTime();
		$date->sub(new DateInterval('P1M')); // tiempo actual menos 1 mes
		$start_timestamp = $date->getTimestamp();

		$options = array('id_station' => $id_station, 'start_timestamp' => $start_timestamp);

		$result_1h = $this->Air_stations_values_1h_model->get_details( $options )->result();
 		*/
		$seconds = 60*60*24*30; // Un mes en segundos
		$options = array('id_station' => $id_station, 'seconds' => $seconds);
		
		$result_1h = $this->Air_stations_values_1h_model->get_last_period_values( $options )->result();
		
		$array_variables_data_1h = $this->generate_chart_data($station_variables, $result_1h);
		
		$array_charts_data['1hour'] = $array_variables_data_1h;

		
		$view_data['array_charts_data'] = $array_charts_data;

		
		// OBTENER UNIDAD DE MEDIDA PARA PONER EN EL EJE Y
		$variable_unidad = array();
		foreach ($station_variables as $variable) {

			$options = array(
				'id_cliente' => $id_cliente,
				'id_proyecto' => $id_proyecto,
				'id_tipo_unidad' => $variable->id_unit_type,
			);
			$units = $this->Reports_units_settings_model->get_units($options)->result();
			
			$variable_unidad[$variable->id_variable]['nombre'] = $units[0]->nombre;
			$variable_unidad[$variable->id_variable]['nombre_real'] = $units[0]->nombre_real;
		}
		$view_data['variable_unidad'] = $variable_unidad;

		$this->load->view("air_monitoring_charts/station_charts", $view_data);
	}

	/** Se genearan los datos necesario para los gráficos de una estación */
	function generate_chart_data($station_variables, $result_data, $time_range_name = ''){
		
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

				// SI LA VARIABLE ES VELOCIDAD DEL VIENTO (ID 1) DIVIDIR POR 3.6
				// ESTO ES PORQUE LOS DATOS DESDE LA API VIENEN EN KM/H Y EN EN MÓDULO HAY QUE MOSTRARLO COMO M/S
				if($variable->id_variable == 1){
					$value = (float)$json_values[$variable->id_variable]/3.6;
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

	/** Se obtiene la última fecha y valor registrado de una variable en una estación */
	function get_last_value(){

		$id_station = $this->input->post('id_station');
		$id_variable = $this->input->post('id_variable');

		$options = array(
			'id_station' => $id_station,
			'id_variable' => $id_variable
		);

		$result = $this->Air_stations_values_1m_model->get_last_value($options)->row();
		
		if($result){
			$str_fecha = $result->date." ".$result->hour.":".$result->minute.":00";
			$datetime = date_to_timestamp_millis_utc($str_fecha);

			// $datetime = intval($result->timestamp) * 1000;

			$var_value = floatval(str_replace('"', '',$result->var_value));
			
			$array_last_data = array(
				'id' => $result->id,
				'id_station' => $result->id_station,
				'id_variable' => $id_variable,
				'data' => array($datetime, $var_value)
			);
			
			echo json_encode($array_last_data);
		}else {
			echo null;
		}
	}

	/** Se obtienen las últimas fechas y valores registrados de una variable en una estación que tengan una fecha o timestamp mayor que el recibido */
	function get_last_values(){

		$id_station = $this->input->post('id_station');
		$id_variable = $this->input->post('id_variable');
		$str_timestamp = $this->input->post('str_timestamp'); 
		$str_datetime = $this->input->post('str_datetime');

		$options = array(
			'id_station' => $id_station,
			'id_variable' => $id_variable);

		if($str_timestamp){
			$options['str_timestamp'] = $str_timestamp;
		}else{
			$options['str_datetime'] = $str_datetime;
		}

		$results = $this->Air_stations_values_1m_model->get_last_values($options)->result();

		$array_last_data = array();

		foreach($results as $result){
			$str_fecha = $result->date." ".$result->hour.":".$result->minute.":00";
			$datetime = date_to_timestamp_millis_utc($str_fecha);

			$array_last_data[] = array($datetime, $result->var_value);
		}
		
		echo json_encode(array());
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

		if($time_range == '1min'){
			$results = $this->Air_stations_values_1m_model->get_details($options)->result();
			
			$array_variables_data = $this->generate_chart_data($variable, $results, 'values_1m');
		}

		if($time_range == '5min'){
			$results = $this->Air_stations_values_5m_model->get_details($options)->result();
			
			$array_variables_data = $this->generate_chart_data($variable, $results, 'values_5m');
		}

		if($time_range == '15min'){
			$results = $this->Air_stations_values_15m_model->get_details($options)->result();
			
			$array_variables_data = $this->generate_chart_data($variable, $results, 'values_15m');
		}
		
		if($time_range == '1hour'){
			$results = $this->Air_stations_values_1h_model->get_details($options)->result();
			
			$array_variables_data = $this->generate_chart_data($variable, $results);
		}	
		
		
		echo json_encode(array("success" => true, "data" => $array_variables_data));

	}

	function clean_data(){
		
		$time_range = $this->input->post('time_range');
		$id_station = $this->input->post('id_station');
		$id_variable = $this->input->post('id_variable');

		$variable = $this->Air_variables_model->get_variables_of_station($id_station, $id_variable)->result();

		$array_variables_data = array();
		
		if($time_range == '1min'){
			// OBTENER DATOS DE FRECUENCIA 1 MINUTO			
			$seconds = 60*60; // Una hora en segundos
			$options = array('id_station' => $id_station, 'seconds' => $seconds);
			$result_1m = $this->Air_stations_values_1m_model->get_last_period_values( $options )->result();
		
			$array_variables_data = $this->generate_chart_data($variable, $result_1m, 'values_1m');
				
		}

		if($time_range == '5min'){
			// OBTENER DATOS DE FRECUENCIA 5 MINUTOS
			$seconds = 60*60*24; // Una día en segundos
			$options = array('id_station' => $id_station, 'seconds' => $seconds);
			$result_5m = $this->Air_stations_values_5m_model->get_last_period_values( $options )->result();
		
			$array_variables_data = $this->generate_chart_data($variable, $result_5m, 'values_5m');
				
		}
		if($time_range == '15min'){
			// OBTENER DATOS DE FRECUENCIA 15 MINUTOS
			$seconds = 60*60*24*7; // Una semana en segundos
			$options = array('id_station' => $id_station, 'seconds' => $seconds);
			$result_15m = $this->Air_stations_values_15m_model->get_last_period_values( $options )->result();
		
			$array_variables_data = $this->generate_chart_data($variable, $result_15m, 'values_15m');
				
		}
		if($time_range == '1hour'){
			// OBTENER DATOS DE FRECUENCIA 1 HORA
			$seconds = 60*60*24*30; // Un mes en segundos
			$options = array('id_station' => $id_station, 'seconds' => $seconds);
			$result_1h = $this->Air_stations_values_1h_model->get_last_period_values( $options )->result();
		
			$array_variables_data = $this->generate_chart_data($variable, $result_1h);
				
		}
		
		echo json_encode(array("success" => true, "data" => $array_variables_data));
	}

	function test_sp(){

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$stations = $this->Air_stations_model->get_details(array(
			'id_client' => $id_cliente, 
			'id_project' => $id_proyecto, 
			'is_active' => true,
			'is_monitoring' => true,
			'order_by' => array("is_receptor", "DESC")
		))->result();
		
		$cont=0;
		$con_sp = false;
		$gen_chart = false;
		$cont=0;
		$ciclos = 6;
		$tiempo = time();
		for ($i=0; $i < $ciclos; $i++) { 
		
		
			foreach($stations as $station){

				$seconds = 60*60; // Una hora en segundos
				$options = array('id_station' => $station->id, 'seconds' => $seconds);
				
				if($con_sp){
					$result_1m = $this->Air_stations_values_1m_model->get_last_period_values_sp( $options );
				}else{
					$result_1m = $this->Air_stations_values_1m_model->get_last_period_values( $options )->result();
				}
				if($gen_chart){
					$array_variables_data_1m = $this->generate_chart_data($station_variables, $result_1m, 'values_1m');
				}

				
				$seconds = 60*60*24; // Un día en segundos
				$options = array('id_station' => $station->id, 'seconds' => $seconds);
				if($con_sp){
					$result_5m = $this->Air_stations_values_5m_model->get_last_period_values_sp( $options );
				}else{
					$result_5m = $this->Air_stations_values_5m_model->get_last_period_values( $options )->result();
				}
				if($gen_chart){
					$array_variables_data_5m = $this->generate_chart_data($station_variables, $result_5m, 'values_5m');
				}


				$seconds = 60*60*24*7; // Una semana en segundos
				$options = array('id_station' => $station->id, 'seconds' => $seconds);
				if($con_sp){
					$result_15m = $this->Air_stations_values_15m_model->get_last_period_values_sp( $options );
				}else{
					$result_15m = $this->Air_stations_values_15m_model->get_last_period_values( $options )->result();
				}
				if($gen_chart){
					$array_variables_data_15m = $this->generate_chart_data($station_variables, $result_15m, 'values_15m');
				}
				
				

				$seconds = 60*60*24*30; // Un mes en segundos
				$options = array('id_station' => $station->id, 'seconds' => $seconds);
				
				if($con_sp){
					$result_1h = $this->Air_stations_values_1h_model->get_last_period_values_sp( $options );
				}else{
					$result_1h = $this->Air_stations_values_1h_model->get_last_period_values( $options )->result();
				}
					// echo '<pre>'; var_dump($result_1h);exit;
				if($gen_chart){
					$array_variables_data_1h = $this->generate_chart_data($station_variables, $result_1h);
				}
				
				$cont++;
			}
		}
		echo "Tiempo: ".(time() - $tiempo) .'<br>Cont: '.$cont;
	}

}

