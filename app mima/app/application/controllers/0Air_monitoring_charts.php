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
		// $this->member_allowed($id_proyecto);

		// $user = $this->Users_model->get_one($this->login_user->id);
		// $view_data["user"] = $user;

		$stations = $this->Air_stations_model->get_all_where( 
			array(
				'id_client' => $id_cliente, 
				'id_project' => $id_proyecto, 
				'is_active' => true,
				'is_monitoring' => true,
				'deleted' => 0
			) 
		)->result();

		$view_data['stations'] = $stations;

		$this->template->rander("air_monitoring_charts/index", $view_data);
    }

	function get_station_charts(){
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
		$result_1m = $this->Air_stations_values_1m_model->get_all_where( array('id_station' => $id_station, 'deleted' => 0) )->result();
		
		$array_variables_data_1m = $this->generate_chart_data($station_variables, $result_1m, 'values_1m');
		
		$array_charts_data['1min'] = $array_variables_data_1m;
		

		// OBTENER DATOS DE FRECUENCIA 5 MINUTOS
		$result_5m = $this->Air_stations_values_5m_model->get_all_where( array('id_station' => $id_station, 'deleted' => 0) )->result();
		
		$array_variables_data_5m = $this->generate_chart_data($station_variables, $result_5m, 'values_5m');
		
		$array_charts_data['5min'] = $array_variables_data_5m;


		// OBTENER DATOS DE FRECUENCIA 15 MINUTOS
		$result_15m = $this->Air_stations_values_15m_model->get_all_where( array('id_station' => $id_station, 'deleted' => 0) )->result();
		
		$array_variables_data_15m = $this->generate_chart_data($station_variables, $result_15m, 'values_15m');
		
		$array_charts_data['15min'] = $array_variables_data_15m;

		
		// OBTENER DATOS DE FRECUENCIA 1 HORA
		$result_1h = $this->Air_stations_values_1h_model->get_all_where( array('id_station' => $id_station, 'deleted' => 0) )->result();
		
		$array_variables_data_1h = $this->generate_chart_data($station_variables, $result_1h, 'values_1h');
		
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

	function generate_chart_data($station_variables, $result_data, $time_range_name){
		
		$array_variables_data = array();
		foreach($station_variables as $variable){
			
			$array_variables_data[$variable->id_variable] = array(
				'name' => $variable->variable_name,
				'data' => array()
			);

			foreach($result_data as $result){

				if($time_range_name == 'values_1h'){
					$str_fecha = $result->date." ".$result->hour.":00:00";
				}else{
					$str_fecha = $result->date." ".$result->hour.":".$result->minute.":00";
				}
				
				$datetime = date_to_timestamp_millis_utc($str_fecha);
			
				$json_values = json_decode($result->data, true);
				$value = (float)$json_values[$variable->id_variable];

				$array_variables_data[$variable->id_variable]['data'][] = array($datetime, $value);
				
			}
		}
		return $array_variables_data;
	}


}

