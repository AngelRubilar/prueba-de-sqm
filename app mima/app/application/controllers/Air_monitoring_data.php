<?php
/**
 * Archivo Controlador para submódulo "Registros" de módulo Monitoreo  (módulo nivel Cliente / Proyecto)
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
 * Archivo Controlador para submódulo "Registros" de módulo Monitoreo (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Monitoreo
 * @property private $id_modulo_cliente id del módulo Monitoreo (17)
 * @property private $id_submodulo_cliente id del submódulo Registros (32)
 * @author Christopher Sam Venegas
 * @version 1.0
 */
class Air_monitoring_data extends MY_Controller {
	
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
	 * Carga la página que muestra en tablas los valores capturados por las estaciones.
	 *
	 * @author Christopher Sam Venegas
	 * @access public
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return resource Vista principal del sub-módulo
	 */
    function index() {
		
		$id_proyecto = $this->session->project_context;
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		$id_cliente = $proyecto->client_id;

		### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_monitor_data');


		$air_sectors = array("" => "-") + $this->Air_sectors_model->get_dropdown_list(
			array("name"), 
			"id",
			array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
			)
		);

		$view_data["air_sectors"] = $air_sectors;
		$view_data["air_stations"] = array("" => "-");

		$days_dropdown = array(
			1 => lang("monday"),
			2 => lang("tuesday"),
			3 => lang("wednesday"),
			4 => lang("thursday"),
			5 => lang("friday"),
			6 => lang("saturday"),
			7 => lang("sunday"),
		);

		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07
		$hours_dropdown = array();
		for($i=0; $i < 24; $i++) { 
            $hours_dropdown[] = ($i < 10) ? '0'.$i : ''.$i;
		}

		$view_data["hours_dropdown"] = $hours_dropdown;
		$view_data["days_dropdown"] = $days_dropdown;

		$this->template->rander("air_monitoring_data/index", $view_data);
    }
    
    /**
	 * get_stations_of_sector
	 * 
	 * Se genera un dropdown list con las estaciones pertenecientes a un sector.
	 * 
	 * @author Christopher Mauricio Sam Venegas
 	 * @access public
	 * @uses int $this->input->post('id_sector') Id del sector seleccionado.
	 * @return HTML Dropdown list con estaciones de un sector.
	*/
	function get_stations_of_sector(){

		$air_stations = $this->Air_stations_model->get_details(array(
			"id_air_sector" => $this->input->post('id_sector'), 
			'is_active' => true,
			'is_monitoring' => true,
			'order_by' => array("is_receptor", "DESC")
		))->result();

		$stations_dropdown = array();
		foreach ($air_stations as $station) {
			// Si la estación no es una estación de Eye3 no se muestra
			if (in_array($station->id, CONST_ARRAY_NO_EYE3_STATIONS_IDS)) {
				continue;
			}
			$stations_dropdown[$station->id] = $station->name;
		}

		$html = '<div class="form-group col-md-3">';
		$html .= '<label for="station" class="">'.lang('station').'</label>';
		$html .= 	'<div class="">';
		$html .= 		form_dropdown("station", $stations_dropdown,"", "id='station' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
	
	}

	/**
	 * get_report
	 *
	 * Carga la vista que se encarga de mostrar la tabla con registros de variables, específicamente air_monitoring_data/report. Antes de dicha carga se preparan datos como las variables asociadas al sector y estación a consultar separados por frecuencia (minutos, horas y días). También se valida que el rango de tiempo ingresado para la consulta no supere un máximo especifico.
	 *
	 * @author Christopher Mauricio Sam Venegas
	 * @access public
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @uses int $this->input->post('id_estacion') Id de estacion
	 * @uses array $this->input->post('days') Días de la semana
	 * @uses array $this->input->post('hours') Horas del día
	 * @uses string $this->input->post('start_date') Fecha de inicio
	 * @uses string $this->input->post('end_date') Fecha de termino
	 * @return resource se obtiene la vista air_monitoring_data/report.
	*/
	function get_report(){
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$id_station = $this->input->post('id_station');
		$days = $this->input->post('days');
		$hours = $this->input->post('hours');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		
		$view_data["id_station"] = $id_station;
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;
		$view_data["days"] = $days;
		$view_data["hours"] = $hours;
		
		$id_proyecto = $this->session->project_context;
		$options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$station_info = $this->Air_stations_model->get_one($id_station);
		$view_data["station_info"] = $station_info;
		/*$id_proyecto = $this->session->project_context;
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		*/

		// CALCULO DE FRECUENCIAS DE TIEMPO ENTRE 2 FECHAS
		// MINUTOS
		$start_date_min = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_date_min = DateTime::createFromFormat('Y-m-d H:i', $end_date);
		$diff_mins = round((strtotime($end_date_min->format('Y-m-d H:i')) - strtotime($start_date_min->format('Y-m-d H:i'))) /60); // Cantidad de minutos
		
		// Datos para decidir si mostrar cada tabla
		$view_data["view_1min"] = ($diff_mins <= 10080)?1:0; // 1 Semana
		$view_data["view_5min"] = ($diff_mins <= 43200)?1:0; // 1 Mes
		$view_data["view_15min"] = ($diff_mins <= 129600)?1:0; // 3 Meses
		$view_data["view_1hour"] = ($diff_mins <= 518400)?1:0; // 12 Meses
		


		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_station)->result();

		$array_variables_unidad = array();
		$array_variables = array();

		foreach($estacion_variables as $variable){
			$id_unidad = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $client_info->id, 
					"id_proyecto" => $project_info->id, 
					"id_tipo_unidad" => $variable->id_unit_type, 
					"deleted" => 0
				)
			)->id_unidad;
			$unidad = $this->Unity_model->get_one($id_unidad)->nombre;

			$array_variables_unidad[$variable->id_air_variable] = $unidad;
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		$view_data["variables_unidad"] = $array_variables_unidad;
		$view_data["variables"] = $array_variables;

		
		if($client_info->habilitado){
			echo $this->load->view("air_monitoring_data/report", $view_data, TRUE);
		}else{
			$this->session->sess_destroy();
			redirect('signin/index/disabled');
		}
		
	}

	/**
	 * list_data_minutes
	 *
	 * Obtiene datos de registros de variables para la frecuencia minutos, capturados por una estación dentro del rango de fechas, días y horas especificados
	 *
	 * @author Christopher Sam Venegas
	 * @access public
	 * @uses int $this->input->post('id_estacion') Id de estacion
	 * @uses array $this->input->post('days') Días de la semana
	 * @uses array $this->input->post('hours') Horas del día
	 * @uses string $this->input->post('start_date') Fecha de inicio
	 * @uses string $this->input->post('end_date') Fecha de termino
	 * @return JSON datos de registros de variables para la frecuencia minutos, capturados por una estación
	*/
	function list_data_minutes(){
		
		$id_estacion = $this->input->post('station');
		$days = $this->input->post('days');
		$hours = $this->input->post('hours');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

        $start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date);
		
		$start_date_f = $start_datetime->format('Y-m-d');
		$start_hour_f = $start_datetime->format('H');
		$start_minute_f = $start_datetime->format('i');
		
		$end_date_f = $end_datetime->format('Y-m-d');
		$end_hour_f = $end_datetime->format('H');
		$end_minute_f = $end_datetime->format('i');
        
		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_estacion)->result();
		$array_variables = array();
		foreach($estacion_variables as $variable){
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		
		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07, ya que la vista envía los indices del campo select
		$array_hours = array();
		foreach($hours as $h){
			$array_hours[] = ($h < 10) ? '0'.$h : ''.$h;
		}

		// Se convierte el indice del día de la semana al valor numerico de mysql (va de 0 a 6)
		$array_days = array();
		foreach ($days as $d) {
			$array_days[] = $d - 1;
		}
		
		$options = array(
			"id_estacion" => $id_estacion,
			"days" => $array_days,
			"hours" => $array_hours,
			"start_date" => $start_date_f,
			"end_date" => $end_date_f,
			"start_hour" => $start_hour_f,
			"end_hour" => $end_hour_f,
			"start_minute" => $start_minute_f,
			"end_minute" => $end_minute_f,
			"variables" => $array_variables,
		);

        $list_data = $this->Air_records_model->get_details_minutes($options)->result_array();
        // echo '<pre>'; var_dump($list_data);exit;
        echo json_encode(array("data" => $list_data));
		
	}

    function list_data_5_min(){
        $id_estacion = $this->input->post('station');
		$days = $this->input->post('days');
		$hours = $this->input->post('hours');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

        $start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date);
		
		$start_date_f = $start_datetime->format('Y-m-d');
		$start_hour_f = $start_datetime->format('H');
		$start_minute_f = $start_datetime->format('i');
		
		$end_date_f = $end_datetime->format('Y-m-d');
		$end_hour_f = $end_datetime->format('H');
		$end_minute_f = $end_datetime->format('i');
        
		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_estacion)->result();
		$array_variables = array();
		foreach($estacion_variables as $variable){
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		
		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07, ya que la vista envía los indices del campo select
		$array_hours = array();
		foreach($hours as $h){
			$array_hours[] = ($h < 10) ? '0'.$h : ''.$h;
		}

		// Se convierte el indice del día de la semana al valor numerico de mysql (va de 0 a 6)
		$array_days = array();
		foreach ($days as $d) {
			$array_days[] = $d - 1;
		}
		
		$options = array(
			"id_estacion" => $id_estacion,
			"days" => $array_days,
			"hours" => $array_hours,
			"start_date" => $start_date_f,
			"end_date" => $end_date_f,
			"start_hour" => $start_hour_f,
			"end_hour" => $end_hour_f,
			"start_minute" => $start_minute_f,
			"end_minute" => $end_minute_f,
			"variables" => $array_variables,
		);

        $list_data = $this->Air_records_model->get_details_5_min($options)->result_array();
        // echo '<pre>'; var_dump($list_data);exit;
        echo json_encode(array("data" => $list_data));
		
    }

	function list_data_15_min(){
        $id_estacion = $this->input->post('station');
		$days = $this->input->post('days');
		$hours = $this->input->post('hours');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

        $start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date);
		
		$start_date_f = $start_datetime->format('Y-m-d');
		$start_hour_f = $start_datetime->format('H');
		$start_minute_f = $start_datetime->format('i');
		
		$end_date_f = $end_datetime->format('Y-m-d');
		$end_hour_f = $end_datetime->format('H');
		$end_minute_f = $end_datetime->format('i');
        
		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_estacion)->result();
		$array_variables = array();
		foreach($estacion_variables as $variable){
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		
		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07, ya que la vista envía los indices del campo select
		$array_hours = array();
		foreach($hours as $h){
			$array_hours[] = ($h < 10) ? '0'.$h : ''.$h;
		}

		// Se convierte el indice del día de la semana al valor numerico de mysql (va de 0 a 6)
		$array_days = array();
		foreach ($days as $d) {
			$array_days[] = $d - 1;
		}
		
		$options = array(
			"id_estacion" => $id_estacion,
			"days" => $array_days,
			"hours" => $array_hours,
			"start_date" => $start_date_f,
			"end_date" => $end_date_f,
			"start_hour" => $start_hour_f,
			"end_hour" => $end_hour_f,
			"start_minute" => $start_minute_f,
			"end_minute" => $end_minute_f,
			"variables" => $array_variables,
		);

        $list_data = $this->Air_records_model->get_details_15_min($options)->result_array();
        // echo '<pre>'; var_dump($list_data);exit;
        echo json_encode(array("data" => $list_data));
		
    }

    function list_data_hour(){
        $id_estacion = $this->input->post('station');
		$days = $this->input->post('days');
		$hours = $this->input->post('hours');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

        $start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date);
		
		$start_date_f = $start_datetime->format('Y-m-d');
		$start_hour_f = $start_datetime->format('H');
		
		$end_date_f = $end_datetime->format('Y-m-d');
		$end_hour_f = $end_datetime->format('H');
        
		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_estacion)->result();
		$array_variables = array();
		foreach($estacion_variables as $variable){
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		
		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07, ya que la vista envía los indices del campo select
		$array_hours = array();
		foreach($hours as $h){
			$array_hours[] = ($h < 10) ? '0'.$h : ''.$h;
		}

		// Se convierte el indice del día de la semana al valor numerico de mysql (va de 0 a 6)
		$array_days = array();
		foreach ($days as $d) {
			$array_days[] = $d - 1;
		}
		
		$options = array(
			"id_estacion" => $id_estacion,
			"days" => $array_days,
			"hours" => $array_hours,
			"start_date" => $start_date_f,
			"end_date" => $end_date_f,
			"start_hour" => $start_hour_f,
			"end_hour" => $end_hour_f,
			"variables" => $array_variables,
		);

        $list_data = $this->Air_records_model->get_details_1_hour($options)->result_array();
        // echo '<pre>'; var_dump($list_data);exit;
        echo json_encode(array("data" => $list_data));
    }

	function export_custom_csv(){

		$id_estacion = $this->input->post('station');
		$days = explode(',', $this->input->post('days'));
		$hours = explode(',', $this->input->post('hours'));
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$time_range = $this->input->post('time_range');

		$start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date);

		$start_date_f = $start_datetime->format('Y-m-d');
		$start_hour_f = $start_datetime->format('H');
		$start_minute_f = $start_datetime->format('i');
		
		$end_date_f = $end_datetime->format('Y-m-d');
		$end_hour_f = $end_datetime->format('H');
		$end_minute_f = $end_datetime->format('i');

		$id_usuario = $this->session->user_id;
		$options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();
		$id_proyecto = $this->session->project_context;
		$project_info = $this->Projects_model->get_one($id_proyecto);

		$station_info = $this->Air_stations_model->get_one($id_estacion);


		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_estacion)->result();
		$array_variables = array();
		foreach($estacion_variables as $variable){
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		
		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07, ya que la vista envía los indices del campo select
		$array_hours = array();
		foreach($hours as $h){
			$array_hours[] = ($h < 10) ? '0'.$h : ''.$h;
		}

		// Se convierte el indice del día de la semana al valor numerico de mysql (va de 0 a 6)
		$array_days = array();
		foreach ($days as $d) {
			$array_days[] = $d - 1;
		}

		if($time_range == '1min'){
			
			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"start_minute" => $start_minute_f,
				"end_minute" => $end_minute_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_minutes($options)->result_array();

			
			// CREO EL CSV EN CARPETA TEMP DE MIMASOFT
			$nombre = $client_info->sigla.'_'.$station_info->name.'_'.'1min.csv';
			$tmp = get_setting("temp_file_path");
			$df = fopen(getcwd() . '/' . $tmp.$nombre, 'w');

			// HEADER
			$header = array();
			$header[] = 'Datetime';
			foreach($array_variables as $variable){
				$header[] = $variable;
			}
			
			fputcsv($df, $header);

			//CONTENT
			foreach($list_data as $row){
				$content = array();
				$content[] = $row["date"].' '.$row["hour"].':'.$row["minute"];
				foreach($array_variables as $variable){
					$content[] = str_replace( '"', '', $row[$variable]);
				}
				fputcsv($df, $content);
			}

			fclose($df);

			echo json_encode(array("success" => true, 'message' => lang(''), 'name' => $nombre));
			exit;
			
		}

		if($time_range == '5min'){

			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"start_minute" => $start_minute_f,
				"end_minute" => $end_minute_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_5_min($options)->result_array();
			// var_dump($list_data);exit;
			
			// CREO EL CSV EN CARPETA TEMP DE MIMASOFT
			$nombre = $client_info->sigla.'_'.$station_info->name.'_'.'5min.csv';
			$tmp = get_setting("temp_file_path");
			$df = fopen(getcwd() . '/' . $tmp.$nombre, 'w');

			// HEADER
			$header = array();
			$header[] = 'Datetime';
			foreach($array_variables as $variable){
				$header[] = $variable;
			}
			
			fputcsv($df, $header);

			//CONTENT
			foreach($list_data as $row){
				$content = array();
				$content[] = $row["date"].' '.$row["hour"].':'.$row["minute"];
				foreach($array_variables as $variable){
					$content[] = str_replace( '"', '', $row[$variable]);
				}
				fputcsv($df, $content);
			}

			fclose($df);

			echo json_encode(array("success" => true, 'message' => lang(''), 'name' => $nombre));
			exit;
			
		}

		if($time_range == '15min'){

			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"start_minute" => $start_minute_f,
				"end_minute" => $end_minute_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_15_min($options)->result_array();

			
			// CREO EL CSV EN CARPETA TEMP DE MIMASOFT
			$nombre = $client_info->sigla.'_'.$station_info->name.'_'.'15min.csv';
			$tmp = get_setting("temp_file_path");
			$df = fopen(getcwd() . '/' . $tmp.$nombre, 'w');

			// HEADER
			$header = array();
			$header[] = 'Datetime';
			foreach($array_variables as $variable){
				$header[] = $variable;
			}
			
			fputcsv($df, $header);

			//CONTENT
			foreach($list_data as $row){
				$content = array();
				$content[] = $row["date"].' '.$row["hour"].':'.$row["minute"];
				foreach($array_variables as $variable){
					$content[] = str_replace( '"', '', $row[$variable]);
				}
				fputcsv($df, $content);
			}

			fclose($df);

			echo json_encode(array("success" => true, 'message' => lang(''), 'name' => $nombre));
			exit;
			
		}

		if($time_range == '1hour'){

			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_1_hour($options)->result_array();

			
			// CREO EL CSV EN CARPETA TEMP DE MIMASOFT
			$nombre = $client_info->sigla.'_'.$station_info->name.'_'.'1hour.csv';
			$tmp = get_setting("temp_file_path");
			$df = fopen(getcwd() . '/' . $tmp.$nombre, 'w');

			// HEADER
			$header = array();
			$header[] = 'Datetime';
			foreach($array_variables as $variable){
				$header[] = $variable;
			}
			
			fputcsv($df, $header);

			//CONTENT
			foreach($list_data as $row){
				$content = array();
				$content[] = $row["date"].' '.$row["hour"].':'.$row["minute"];
				foreach($array_variables as $variable){
					$content[] = str_replace( '"', '', $row[$variable]);
				}
				fputcsv($df, $content);
			}

			fclose($df);

			echo json_encode(array("success" => true, 'message' => lang(''), 'name' => $nombre));
			exit;
			
		}

	}

	function export_custom_excel(){

		$id_estacion = $this->input->post('station');
		$days = explode(',', $this->input->post('days'));
		$hours = explode(',', $this->input->post('hours'));
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$time_range = $this->input->post('time_range');

		$start_datetime = DateTime::createFromFormat('Y-m-d H:i', $start_date);
		$end_datetime = DateTime::createFromFormat('Y-m-d H:i', $end_date);

		$start_date_f = $start_datetime->format('Y-m-d');
		$start_hour_f = $start_datetime->format('H');
		$start_minute_f = $start_datetime->format('i');
		
		$end_date_f = $end_datetime->format('Y-m-d');
		$end_hour_f = $end_datetime->format('H');
		$end_minute_f = $end_datetime->format('i');

		// $id_usuario = $this->session->user_id;
		$options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();
		$id_proyecto = $this->session->project_context;
		$project_info = $this->Projects_model->get_one($id_proyecto);

		$station_info = $this->Air_stations_model->get_one($id_estacion);


		$estacion_variables = $this->Air_stations_rel_variables_model->get_variables_of_station($id_estacion)->result();
		$array_variables = array();
		foreach($estacion_variables as $variable){
			$array_variables[$variable->id_air_variable] = $variable->name;
		}
		
		// Se les agrega un 0 a las horas inferiores a las 10 ej: 07, ya que la vista envía los indices del campo select
		$array_hours = array();
		foreach($hours as $h){
			$array_hours[] = ($h < 10) ? '0'.$h : ''.$h;
		}

		// Se convierte el indice del día de la semana al valor numerico de mysql (va de 0 a 6)
		$array_days = array();
		foreach ($days as $d) {
			$array_days[] = $d - 1;
		}


			
		$this->load->library('excel');		
			
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							->setLastModifiedBy("Mimasoft")
							->setTitle("")
							->setSubject("")
							->setDescription("")
							->setKeywords("mimasoft")
							->setCategory("excel");
		
			
		if($client_info->id){
			if($client_info->color_sitio){
				$color_sitio = str_replace('#', '', $client_info->color_sitio);
			} else {
				$color_sitio = "00b393";
			}
		} else {
			$color_sitio = "00b393";
		}
		// ESTILOS
		$styleArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'rotation' => 90,
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => $color_sitio)
			),
		);
		// LOGO
		if($client_info->id){
			if($client_info->logo){
				$url_logo = "files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png";
			} else {
				$url_logo = "files/system/default-site-logo.png";
			}
		} else {
			$url_logo = "files/system/default-site-logo.png";
		}
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('./'.$url_logo);
		$objDrawing->setHeight(35);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($project_info->id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $project_info->id_proyecto));
		
		if($time_range == '1hour'){
			$letra = PHPExcel_Cell::stringFromColumnIndex(2 + count($array_variables) - 1);
		}else{
			$letra = PHPExcel_Cell::stringFromColumnIndex(3 + count($array_variables) - 1);
		}

		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		
		if($time_range == '1min'){
			$doc->getActiveSheet()->setCellValue('C1', 'Registros por minuto');
		}
		if($time_range == '5min'){
			$doc->getActiveSheet()->setCellValue('C1', 'Registros cada 5 minutos');
		}
		if($time_range == '15min'){
			$doc->getActiveSheet()->setCellValue('C1', 'Registros cada 15 minutos');
		}
		if($time_range == '1hour'){
			$doc->getActiveSheet()->setCellValue('C1', 'Registros por hora');
		}
		
		$doc->getActiveSheet()
			->setCellValue('C2', $station_info->name)
			->setCellValue('C3', lang("date").': '.$fecha.' '.lang("at").' '.$hora);

	
		$doc->setActiveSheetIndex(0);
	
		// SETEO DE CABECERAS DE CONTENIDO A LA HOJA DE EXCEL
		if($time_range == '1hour'){
			$doc->setActiveSheetIndex(0)->setCellValue('A5', lang("date"));
			$doc->setActiveSheetIndex(0)->setCellValue('B5', lang("hour"));
			$doc->getActiveSheet()->fromArray($array_variables, NULL,"C5");
		}else{
			$doc->setActiveSheetIndex(0)->setCellValue('A5', lang("date"));
			$doc->setActiveSheetIndex(0)->setCellValue('B5', lang("hour"));
			$doc->setActiveSheetIndex(0)->setCellValue('C5', lang("minute"));
			$doc->getActiveSheet()->fromArray($array_variables, NULL,"D5");	
		}

		$alignment_left = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
		);

		
		// CARGA DE CONTENIDO A LA HOJA DE EXCEL
	
		if($time_range == '1min'){
			
			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"start_minute" => $start_minute_f,
				"end_minute" => $end_minute_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_minutes($options)->result_array();
			
		}

		if($time_range == '5min'){

			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"start_minute" => $start_minute_f,
				"end_minute" => $end_minute_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_5_min($options)->result_array();
		}

		if($time_range == '15min'){

			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"start_minute" => $start_minute_f,
				"end_minute" => $end_minute_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_15_min($options)->result_array();

		}

		if($time_range == '1hour'){

			$options = array(
				"id_estacion" => $id_estacion,
				"days" => $array_days,
				"hours" => $array_hours,
				"start_date" => $start_date_f,
				"end_date" => $end_date_f,
				"start_hour" => $start_hour_f,
				"end_hour" => $end_hour_f,
				"variables" => $array_variables,
			);

			$list_data = $this->Air_records_model->get_details_1_hour($options)->result_array();
		}

		$row = 6; // EMPEZANDO DE LA FILA 6 
		foreach($list_data as $fila){

			$doc->setActiveSheetIndex(0)->setCellValue('A'.$row, get_date_format($fila['date'], $id_proyecto));
			$doc->setActiveSheetIndex(0)->setCellValue('B'.$row, $fila['hour']);
			$doc->getActiveSheet()->getStyle('B'.$row)->applyFromArray($alignment_left);
			
			if($time_range != '1hour'){
				$col = 3;// EMPEZANDO DE LA COLUMNA 'D'
				$doc->setActiveSheetIndex(0)->setCellValue('C'.$row, $fila['minute']);
				$doc->getActiveSheet()->getStyle('C'.$row)->applyFromArray($alignment_left);
			}else{
				$col = 2; // EMPEZANDO DE LA COLUMNA 'C'
			}

			foreach($array_variables as $id_variable => $variable){
				
				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				if($fila[$variable] == '-'){
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $fila[$variable]);
				}else{
					$value = str_replace( '"', '', $fila[$variable]);
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, to_number_project_format($value, $id_proyecto));
				}

				$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(true);
				$col++;
			}
			
			$row++;

		}
		
		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');
		
		// ANCHO COLUMNAS
		/* $lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		} */


		if($time_range == '1min'){
			$sufix = '_1min';
		}elseif($time_range == '5min'){
			$sufix = '_5min';
		}elseif($time_range == '15min'){
			$sufix = '_15min';
		}elseif($time_range == '1hour'){
			$sufix = '_1hour';
		}

		$name = $client_info->sigla.'_'.$station_info->name.$sufix;
		
		$sheet_name = strlen($name) > 31 ? substr($name, 0, 28).'...' : $name;
		$sheet_name = $sheet_name ? $sheet_name : " ";
		$doc->getActiveSheet()->setTitle($sheet_name);
		
		$filename = $client_info->sigla.'_'.$station_info->name.$sufix;
		$filename = $filename.'.xlsx'; //save our workbook as this file name

		$filename = preg_replace('/\s+/', '_', $filename);
        $filename = str_replace("’", "_", $filename);
        $filename = str_replace("'", "_", $filename);
        // $filename = str_replace("(", "_", $filename);
        // $filename = str_replace(")", "_", $filename);
		$filename = str_replace(",", "_", $filename);
				
		$filepath = get_setting("temp_file_path") . $filename;
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save($filepath);

		echo json_encode(array("success" => true, 'name' => $filename));
		exit;

	}

	
	/**
	 * borrar_temporal
	 * 
	 * Funcion para borrar un archivo especifico en la carpeta temp.
	 * 
	 * @author Alvaro Cristobal Donoso Albornoz 
	 * @access public
	 * @uses $this->input->post('uri'); Ruta del archivo temporal a borrar.
	 */
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}


}

