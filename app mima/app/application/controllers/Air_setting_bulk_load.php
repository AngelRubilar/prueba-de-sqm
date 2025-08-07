<?php
/**
 * Archivo Controlador para Carga Masiva MIMAire (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Carga Masiva
 * @author Álvaro Donoso
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Carga Masiva MIMAire (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Carga Masiva
 * @property private $id_modulo_cliente id del módulo Administración Cliente Mimaire (15)
 * @property private $id_submodulo_cliente id del submódulo Carga Masiva MIMAire (27)
 * @author Álvaro Donoso
 * @version 1.0
 */
class Air_setting_bulk_load extends MY_Controller {
	
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
		$this->load->helper('email');

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 15;
		$this->id_submodulo_cliente = 27;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    /**
	 * index
	 * 
	 * Carga datos para el primer dropdown (filtro) de la vista principal del módulo
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @return resource Vista principal del módulo
	 */
    function index() {
		
		//phpinfo(); exit();

		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);

        //$this->access_only_allowed_members();

        $id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);

		// TIPOS DE REGISTROS
		$array_tipos_registros = array("" => "-");
		$tipos_de_registros = $this->Air_records_types_model->get_all()->result();
		foreach($tipos_de_registros as $tipo_registro){
			$array_tipos_registros[$tipo_registro->id] = lang($tipo_registro->name);
		}
		
		$view_data["tipos_de_registros"] = $array_tipos_registros;
		$view_data["project_info"] = $proyecto;
		
		//Configuración perfil de usuario
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_editar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");

		### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_bulk_load');

		
        $this->template->rander("air_setting_bulk_load/index", $view_data);
    }

	/**
	 * get_models_of_record_type
	 * 
	 * Carga una lista de Tipos de Modelo (Numérico, Estadístico, Neuronal). 
	 * Se utiliza en la vista principal del módulo, y se consulta via Ajax
	 * mediante el evento on_change del selector Tipo de registro. Consulta los Modelos
	 * cuando el Tipo de registro que se selecciona en su selector es Pronóstico.
	 * Luego arma y retorna un HTML que contiene un dropdown que enlista los Modelos. 
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->input->post('id_record_type') id de Tipo de registro
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @return HTML dropdown con una lista de Tipos de Modelo
	 */
    function get_models_of_record_type() {
        $id_record_type = $this->input->post('id_record_type');
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

        if (!$this->login_user->id) {
            redirect("forbidden");
		}
		
		$array_modelos = array();
		$array_modelos[] = array("id" => "", "text" => "-");

		if($id_proyecto){

			if(/*$id_record_type == 1 || */$id_record_type == 2){
				$modelos = $this->Air_models_model->get_all()->result();
				foreach($modelos as $modelo){
					$array_modelos[] = array("id" => $modelo->id, "text" => lang($modelo->name));
				}
			}
		}
        
        echo json_encode($array_modelos);
		
	}
	
	/**
	 * get_sectors_of_model
	 * 
	 * Carga una lista de Sectores asociados a un Modelo. Se utiliza en la vista principal del módulo, 
	 * y se consulta via Ajax mediante el evento on_change del selector Modelo.
	 * Luego arma y retorna un HTML que contiene un dropdown que enlista los Sectores. 
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->input->post('id_model') id de Modelo
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @return HTML dropdown con una lista de Sectores asociados a un Modelo
	 */
	function get_sectors_of_model() {

        $id_model = $this->input->post('id_model');
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_sectores = array();
        $array_sectores[] = array("id" => "", "text" => "-");
        
		$sectores = $this->Air_sectors_model->get_all_where(array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"deleted" => 0
		))->result();
		
        if($id_proyecto){
			if($id_model){
				foreach($sectores as $sector){
					$array_modelos = json_decode($sector->air_models);
					if(in_array($id_model, $array_modelos)){
						$array_sectores[] = array("id" => $sector->id, "text" => $sector->name);
					}
				}
			} else {
				foreach($sectores as $sector){
					$array_sectores[] = array("id" => $sector->id, "text" => $sector->name);
				}
			}    
        }
        
        echo json_encode($array_sectores);
		
	}
	
	/**
	 * get_receptors_of_sector
	 * 
	 * Carga una lista de Estaciones (receptoras y no receptoras) asociados a un Modelo / Sector.
	 * Si el modelo en el filtro de consulta no es de tipo Numérico, no carga estaciones Receptoras.
	 * Se utiliza en la vista principal del módulo, y se consulta via Ajax mediante el evento on_change del selector Sector.
	 * Luego arma y retorna un HTML que contiene un dropdown que enlista las Estaciones / Receptores. 
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->input->post('id_model') id de Modelo
	 * @uses int $this->input->post('id_sector') id de Sector
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @return HTML dropdown con una lista de Receptor asociados a un Modelo / Sector
	 */
	function get_receptors_of_sector() {
		
		$id_model = $this->input->post('id_model');
		$id_sector = $this->input->post('id_sector');
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_receptores = array();
		$array_receptores[] = array("id" => "", "text" => "-");
		if($id_model == 3){
			$array_receptores[] = array("id" => 0, "text" => lang("not_a_receptor"));
		}
        
		$receptores = $this->Air_stations_model->get_all_where(array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_air_sector" => $id_sector,
			"deleted" => 0
		))->result();
		
        if($id_proyecto){
            foreach($receptores as $receptor){
				if($receptor->is_receptor){
					$tipo = lang("receptor");
				}else{
					$tipo = lang("station");
				}

				// SI EL MODELO NO ES NUMERICO, NO ACEPTA DATOS DE RECEPTORES
				if($id_model < 3 && $receptor->is_receptor == 1){continue;}
				$array_receptores[] = array("id" => $receptor->id, "text" => $tipo.' - '.$receptor->name);
            }
        }
        
        echo json_encode($array_receptores);
		
	}

	/**
	 * get_variables_of_receptor
	 * 
	 * Carga una lista de Variables asociados a un Sector / Estación.
	 * Se utiliza en la vista principal del módulo, y se consulta via Ajax mediante el evento on_change del selector Sector.
	 * Luego arma y retorna un HTML que contiene un dropdown que enlista las Variables. 
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->input->post('id_sector') id de Sector
	 * @uses int $this->input->post('id_receptor') id de Receptor
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return HTML dropdown con una lista de Variables asociados a un Sector / Estación
	 */
	function get_variables_of_receptor() {

		$id_record_type = $this->input->post('id_record_type');
		$id_model = $this->input->post('id_model');
		$id_sector = $this->input->post('id_sector');
		$id_receptor = $this->input->post('id_receptor');
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

        if (!$this->login_user->id) {
            redirect("forbidden");
		}
		
		
		if($id_receptor == 0){// Numerico - Mapa
			$variables = $this->Air_variables_model->get_variables_of_sector($id_sector)->result();
		}else{
			$variables = $this->Air_variables_model->get_variables_of_station($id_receptor)->result();
		}
		
		$array_variables = array();
		$array_variables[] = array("id" => "", "text" => "-");
		
        if($id_proyecto){
            foreach($variables as $variable){
				$array_variables[] = array(
					"id" => $variable->id_variable, 
					"text" => $variable->variable_name,
					"icon" => $variable->icono
				);
            }
        }
        
        echo json_encode($array_variables);
		
	}

	
	/**
	 * get_excel_template
	 * 
	 * Retorna una plantilla Excel para la Carga Masiva MIMAire con el formato de subida de datos.
	 * La plantilla retornada depende del Tipo de registro que reciba el método.
	 * Se utiliza en la vista principal del módulo, y se consulta via Ajax mediante el evento on_change del selector Variable.
	 * Luego arma y retorna un HTML que contiene un link para descargar la plantilla Excel.
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->input->post('id_record_type') id de Tipo de registro
	 * @uses int $this->input->post('id_model') id de Modelo
	 * @uses int $this->input->post('id_receptor') id de Receptor o Estación
	 * @uses int $this->input->post('id_variable') id de Variable
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return HTML link de descarga de plantilla Excel
	 */
	function get_excel_template() {
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$id_record_type = $this->input->post('id_record_type');
		$id_model = $this->input->post('id_model');
		$id_sector = $this->input->post('id_sector');
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
				
		if(!$info_cliente->id && !$info_proyecto->id) {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			exit();
		}

        if (!$this->login_user->id) {
            redirect("forbidden");
		}

		$prefijo_nombre_archivo = "";
		if($id_model == 1){
			$prefijo_nombre_archivo = "ml_";
		} 
		if($id_model == 2){
			$prefijo_nombre_archivo = "redes_";
		}
		if($id_model == 3){
			$prefijo_nombre_archivo = "num_";
		}
		
		$html = '';
		
		// SI EL TIPO DE REGISTRO ES PRONÓSTICO
		if($id_record_type == 2){

			//  SI EL MODELO ES NUMÉRICO, SE MUESTRA LA INFORMACIÓN ASOCIADA A LA CARGA DE DATOS AL MAPA
			if($id_model == 3){

				$nombre_archivo_map = $prefijo_nombre_archivo."mapa";
				if(!file_exists(__DIR__.'/../../files/system/'.$nombre_archivo_map.'.xlsx')) {
					echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
					exit();
				}
	
				$html .= '<div class="form-group">';
	
				$html .= '<div class="col-xs-12 col-md-3">';
				$html .= lang("not_a_receptor");
				$html .= "<br>";
				$html .= '<i class="fa fa-file-excel-o fa-sm font-16 mr10"></i>';
				$html .= '<a href="'.get_uri("air_setting_bulk_load/download_template/".$nombre_archivo_map.".xlsx").'">'.$nombre_archivo_map.'.xlsx</a>';
				$html .= '</div>';
	
				$variables = $this->Air_variables_model->get_variables_of_sector($id_sector)->result();
					
				$html .= '<div class="col-xs-12 col-sm-12 col-md-9">';
					$html .= '<label for="form" class="col-xs-12 col-md-1">'.lang('variables').': <i id="bulk_load_help" class="fa fa-question-circle" data-container="body" data-toggle="tooltip" title="'.lang('template_variable_initials').'"></i></label> ';
	
					if($id_proyecto){
						foreach($variables as $variable){
							
							$icono = $variable->icono ? base_url("assets/images/air_variables/".$variable->icono) : base_url("assets/images/impact-category/empty.png");
							$html .= '<div class="text-center p15 col-xs-6 col-sm-6 col-md-1 b-l b-r">';
								$html .= '<img src="'.$icono.'" alt="..." height="30" width="30" class="mCS_img_loaded" data-container="body" data-toggle="tooltip" title="'.$variable->variable_name.'">';
								$html .= '<div class=""> '.$variable->sigla.'</div>';
							$html .= '</div>';
	
						}
					}
				$html .= '</div>';
				$html .= '</div>';

			}

			$array_variables_by_station = array();
			$stations = $this->Air_stations_model->get_all_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_air_sector" => $id_sector,
				"is_active" => 1,
				"is_forecast" => 1,
				"deleted" => 0
			))->result();

			foreach($stations as $station){
				if($id_model < 3 && $station->is_receptor == 1){continue;}
				$variables = $this->Air_variables_model->get_variables_of_station($station->id)->result();
				$array_variables = array();
				foreach($variables as $variable){
					$array_variables[] = $variable;
				}
				$array_variables_by_station[$station->id] = $array_variables;
			}

			foreach($array_variables_by_station as $id_station => $data_variable){

				$station = $this->Air_stations_model->get_one($id_station);
				$nombre_archivo = $prefijo_nombre_archivo.$station->load_code;

				/*if(!file_exists(__DIR__.'/../../files/system/'.$nombre_archivo.'.xlsx')) {
					echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
					exit();
				}*/
				
				$html .= '<div class="form-group">';

				$html .= '<div class="col-xs-12 col-md-3">';
				$html .= lang("station").": ".$station->name;
				$html .= "<br>";
				$html .= '<i class="fa fa-file-excel-o fa-sm font-16 mr10"></i>';
				$html .= '<a href="'.get_uri("air_setting_bulk_load/download_template/".$nombre_archivo.".xlsx").'">'.$nombre_archivo.'.xlsx</a>';
				$html .= '</div>';

				// $html .= '<div class="col-xs-12 col-md-3">';
				// $html .= '<div class="fa fa-file-excel-o font-22 mr10"></div>';
				// $html .= '<a href="'.get_uri("air_setting_bulk_load/download_template/".$num_formato).'">'.$nombre_archivo.'.xlsx</a>';
				// $html .= '</div>';

				$html .= '<div class="col-xs-12 col-sm-12 col-md-9">';
					$html .= '<label for="form" class="col-xs-12 col-md-1">'.lang('variables').': <i id="bulk_load_help" class="fa fa-question-circle" data-container="body" data-toggle="tooltip" title="'.lang('template_variable_initials').'"></i></label> ';

					if($id_proyecto){
						foreach($data_variable as $variable){
							
							$icono = $variable->icono ? base_url("assets/images/air_variables/".$variable->icono) : base_url("assets/images/impact-category/empty.png");
							$html .= '<div class="text-center p15 col-xs-6 col-sm-6 col-md-1 b-l b-r">';
								$html .= '<img src="'.$icono.'" alt="..." height="30" width="30" class="mCS_img_loaded" data-container="body" data-toggle="tooltip" title="'.$variable->variable_name.'">';
								$html .= '<div class=""> '.$variable->sigla.'</div>';
							$html .= '</div>';

						}
					}
				$html .= '</div>';
				$html .= '</div>';

			}

		}


		// // SI EL TIPO DE REGISTRO ES MONITOREO
		// if($id_record_type == 1){

		// 	$stations = $this->Air_stations_model->get_all_where(array(
		// 		"id_client" => $id_cliente,
		// 		"id_project" => $id_proyecto,
		// 		"id_air_sector" => $id_sector,
		// 		"is_receptor" => 0,
		// 		"deleted" => 0
		// 	))->result();

		// 	// $sigla_variable = "so2";
		// 	// $nombre_archivo = $prefijo_nombre_archivo.$sigla_variable;
		// 	$nombre_archivo = "monitoreo_so2";

		// 	// Valida que el archivo de template exista
		// 	if(!file_exists(__DIR__.'/../../files/system/'.$nombre_archivo.'.xlsx')) {
		// 		echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
		// 		exit();
		// 	}

		// 	$html .= '<div class="form-group">';
		// 	$html .= 	'<div class="col-xs-12 col-md-4">';
		// 	$html .= 		'<div class="fa fa-file-excel-o font-22 mr10"></div>';
		// 	$html .= 		'<a href="'.get_uri("air_setting_bulk_load/download_template/".$nombre_archivo.".xlsx").'">'.$nombre_archivo.'.xlsx</a>';
		// 	$html .= 	'</div>';
		// 	$html .= '</div>';

		// 	$html .= '<div class="form-group">';
		// 	$html .= 	'<div class="col-xs-12 col-sm-12 col-md-6">';
		// 	//$html .= 		'<label for="form" class="">'.lang('stations').': <i id="bulk_load_help" class="fa fa-question-circle" data-container="body" data-toggle="tooltip" title="'.lang('template_stations_load_codes').'"></i></label> ';

		// 	// MOSTRAR LOS CÓDIGOS DE ESTACIÓN PARA QUE EL USUARIO SEPA COMO NOMBRAR LAS HOJAS DEL ARCHIVO DE CARGA
			
		// 	$html .= '<table class="table table-bordered">';
		// 	$html .= 	'<tr>';
		// 	$html .= 		'<th>'.lang("station").'</th>';
		// 	$html .= 		'<th>'.'<i id="bulk_load_help" class="fa fa-question-circle" data-container="body" data-toggle="tooltip" title="'.lang('template_stations_load_codes').'"></i> '.lang("code").'</th>';

		// 	foreach($stations as $station){
		// 		if($station->id == 5){ continue; } // SI LA ESTACIÓN ES Estación Meteorológica, OMITIR
		// 		$html .= 	'<tr>';
		// 		$html .=		'<td>'.$station->name.'</td>';
		// 		$html .= 		'<td>'.$station->load_code.'</td>';
		// 		$html .= 	'</tr>';
		// 	}

		// 	$html .= 	'</tr>';
		// 	$html .= '</table>';
			
		// }
		
		echo json_encode($html);
		exit();
		
    }

	function get_excel_template_synoptic_data(){

		$id_client = $this->login_user->client_id;
		$id_project = $this->session->project_context;
		$id_record_type = $this->input->post('id_record_type');

		if($id_record_type == 3){ // Datos Sinópticos

			$file_name = 'air_upload_template_pmca';

			// Valida que el archivo de template exista
			if(!file_exists(__DIR__.'/../../files/system/'.$file_name.'.csv')) {
				echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
				exit();
			}

			$html = '<div class="form-group">';
			$html .= 	'<div class="col-xs-12 col-md-4">';
			$html .= 		'<div class="fa fa-file-excel-o font-22 mr10"></div>';
			$html .= 		'<a href="'.get_uri("air_setting_bulk_load/download_template/".$file_name.".csv").'">'.$file_name.'.csv</a>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			echo json_encode($html);
			exit();

		} 


	}
	
	/**
	 * no se usa
	 * @ignore
	 */
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.	    
	}
	
	/**
	 * download_template
	 * 
	 * Permite realizar la descarga de plantillas de Excel con formato de subida de datos
	 * según un Modelo / Tipo de registro
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @param int $num_formato El formato de plantilla Excel que se genera, armado en el método get_excel_template
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @return resource archivo Excel
	 */
	function download_template($file_name) {

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		if(!$id_cliente && !$id_proyecto && !$file_name){
			redirect("forbidden");
		}
		
        $file_data = serialize(array(array("file_name" => $file_name)));
        download_app_files("files/system/", $file_data, false);
		
    }
	
	/**
	 * no se usa
	 * @ignore
	 */
	function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}
	
	/**
	 * validateDate
	 * 
	 * Valida que la fecha recibida sea válida y la retorna en un formato determinado 
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @param string $date Fecha a formatear
	 * @param string $format Formato de fecha
	 * @return string fecha formateada
	 */
	function validateDate($date, $format){
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	/**
	 * no se usa
	 * @ignore
	 */
	function validateYear($year){
		$year = (int)$year;
		
		if($year >= 2020 && $year <= 2022){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * no se usa
	 * @ignore
	 */
	function validateMonth($year, $month){

		$date = $month.'-'.$year;

		// Create a DateTime object pointing to the 1st of your given month and year
		$d = DateTime::createFromFormat('d-m-Y', '01-' . $date);
		if($d && $d->format('m-Y') == $date){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * no se usa
	 * @ignore
	 */
	function validateDay($year, $month, $day){
		$d = checkdate($month, $day, $year);
		return $d;
	}

	/**
	 * no se usa
	 * @ignore
	 */
	function validateHour($hour){
		if(!preg_match("/^(?:2[0-3]|[01][0-9])$/", $hour)){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * save
	 * 
	 * Valida que la plantilla de Excel con datos de pronóstico cargada por el usuario, tenga el formato correcto.
	 * Si el formato es el correcto, ejecuta el método bulk_load para cargar los datos en la base de datos.
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @uses int $this->input->post('record_type') id de Tipo de Registro
	 * @uses int $this->input->post('model') id de Modelo
	 * @uses int $this->input->post('sector') id de Sector
	 * @uses int $this->input->post('receptor') id de Estación o Receptor
	 * @uses int $this->input->post('variable') id de Variable
	 * @uses string $this->input->post('archivo_importado') plantilla Excel de Carga Masiva subida por el Usuario
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @return boolean Si el formato de plantilla es validado correctamente, ejecuta el método bulk_load para cargar 
	 * los datos en la base de datos, de lo contrario, retorna un mensaje de error.
	 */
    function save() {

		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);

		// set_time_limit(100);
		// ini_set('memory_limit','512m');

		// var_dump(ini_get('max_execution_time'));
		// exit();
		// var_dump(ini_get('upload_max_filesize'));
		// exit();

		// var_dump("save ;)"); exit();
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_record_type = $this->input->post('record_type');
		// $id_model = $this->input->post('model');
		// $id_sector = $this->input->post('sector');
		// $id_receptor = $this->input->post('receptor');
		// $id_variable = $this->input->post('variable');
		// $id_tipo_variable = $this->Air_variables_model->get_one($id_variable)->id_air_variable_type;

        // validate_submitted_data(array(
        //     "record_type" => "numeric",
		// 	"model" => "numeric",
		// 	"sector" => "numeric",
		// 	"receptor" => "numeric",
		// 	"variable" => "numeric",
		// 	"file" => "required",
		// ));
		
		
		// // CARGA MASIVA DE MONITOREO
		// if($id_record_type == 1){

		// 	$id_model = $this->input->post('model');
		// 	$id_sector = $this->input->post('sector');
		// 	$file = $this->input->post('archivo_importado');

		// 	$options = array(
		// 		"id_cliente" => $id_cliente,
		// 		"id_proyecto" => $id_proyecto,
		// 		"id_record_type" => $id_record_type,
		// 		"id_model" => $id_model,
		// 		"id_sector" => $id_sector,
		// 		"file" => $file,
		// 	);
		// 	$this->save_monitoring_data($options);

		// }

		// CARGA MASIVA DE PRONÓSTICOS
		if($id_record_type == 2){ 

			$id_model = $this->input->post('model');
			$id_sector = $this->input->post('sector');
			$files = $this->input->post('archivo_importado');

			$options = array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_record_type" => $id_record_type,
				"id_model" => $id_model,
				"id_sector" => $id_sector,
				"files" => $files,
			);
			$this->save_forecast_data($options);

		} 
		// FIN CARGA MASIVA DE PRONÓSTICOS

		// CARGA MASIVA DE DATOS SINÓPTICOS (PMCA) CON ARCHIVO CSV
		if($id_record_type == 3){ // DATOS SINÓPTICOS

			$file = $this->input->post('archivo_importado');
			$this->save_synoptic_data($id_cliente, $id_proyecto, $file);

		}
		// FIN CARGA MASIVA DE DATOS SINÓPTICOS (PMCA)
		

    }

	function save_synoptic_data($id_cliente, $id_proyecto, $file = ""){
		
		$file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		// VALIDA QUE EL USUARIO HAYA SUBIDO UN ARCHIVO
		if(!$file){
			echo json_encode(array("success" => false, 'message' => lang('upload_file_not_uploaded_msj')));
			exit();
		}

		// VALIDA QUE LA EXTENSIÓN DEL ARCHIVO SEA CSV
		if($file_ext != 'csv'){ // DATOS SINÓPTICOS
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file)"));
			exit();
		}

		$array_data_tramo = array();
		$uploaded_file = move_temp_file($file, "files/carga_masiva/", "", "", $file);
		$fp = fopen("files/carga_masiva/".$file, "r");
		$csv_row = 1;
		while($data = fgetcsv($fp, 1000, ",")){

			// COLUMNAS
			if($csv_row == 1){

				// VALIDACIÓN DE COLUMNAS. VALIDO QUE NOMBRES DE COLUMNAS SEAN LOS CORRECTOS Y QUE LA CANTIDAD DE COLUMNAS SEA LA CORRECTA.
				if($data[0] != "date" || $data[1] != "fin_turno" || $data[2] != "PMCA" || $data[3] != "ws_margarita_str" || $data[4] != "hora_ws_min"
					|| count($data) != 5){
					echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => "Nombre de columnas incorrecto."));
					exit();
				}
			} else { // FILAS

				// VALIDACIÓN DE FILAS. VALIDO QUE LA CANTIDAD DE DATOS DENTRO DE LA FILA SEA LA CORRECTA.
				if(count($data) != 5){
					echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => "Cantidad de datos incorrectos en la fila ".$csv_row." del archivo"));
					exit();
				}

				$date = date_create($data[0]);
				$date = date_format($date,"Y-m-d");

				// DATOS DE 72 HRS.
				if($csv_row <= 10){

					// TRAMO 1
					if($csv_row == 2 || $csv_row == 5 || $csv_row == 8){
						$array_data_tramo[$date]["t1"] = json_encode(array(
							"value" => $data[2], // PMCA,
							"ws_margarita_str" => $data[3], // ws_margarita_str
							"hora_ws_min" => $data[4]
						));
					}
					
					// TRAMO 2
					if($csv_row == 3 || $csv_row == 6 || $csv_row == 9){
						$array_data_tramo[$date]["t2"] = json_encode(array(
							"value" => $data[2], // PMCA,
							"ws_margarita_str" => $data[3], // ws_margarita_str
							"hora_ws_min" => $data[4]
						));
					}

					// TRAMO 3
					if($csv_row == 4 || $csv_row == 7 || $csv_row == 10){
						$array_data_tramo[$date]["t3"] = json_encode(array(
							"value" => $data[2], // PMCA,
							"ws_margarita_str" => $data[3], // ws_margarita_str
							"hora_ws_min" => $data[4]
						));
					}

				}
				
			}

			$csv_row++;
			
		}
		fclose($fp);

		$count_dates = 1;
		$array_bulk_load_data = array();
		

		foreach($array_data_tramo as $date => $data){

			if($count_dates == 1){ // TRAMOS 24 HRS.

				// SE SETEA EN LA PRIMERA FECHA EL CLIENTE, PROYECTO, Y FECHA PARA EL REGISTRO.
				// LA FECHA PARA EL REGISTRO, DEBE SER LA ANTERIOR A LA PRIMERA FECHA DEL ARCHIVO CSV,
				// YA QUE LA PRIMERA FECHA ES UN PRONÓSTICO DE 24 HRS.
				$array_bulk_load_data["id_client"] = $id_cliente;
				$array_bulk_load_data["id_project"] = $id_proyecto;
				$array_bulk_load_data["date"] = date( 'Y-m-d', strtotime( $date . ' -1 day' ) );

				$array_bulk_load_data["pmca_24_hrs_t1"] = $data["t1"];
				$array_bulk_load_data["pmca_24_hrs_t2"] = $data["t2"];
				$array_bulk_load_data["pmca_24_hrs_t3"] = $data["t3"];
			}

			if($count_dates == 2){ // TRAMOS 48 HRS.
				$array_bulk_load_data["pmca_48_hrs_t1"] = $data["t1"];
				$array_bulk_load_data["pmca_48_hrs_t2"] = $data["t2"];
				$array_bulk_load_data["pmca_48_hrs_t3"] = $data["t3"];
			}

			if($count_dates == 3){ // TRAMOS 72 HRS.
				$array_bulk_load_data["pmca_72_hrs_t1"] = $data["t1"];
				$array_bulk_load_data["pmca_72_hrs_t2"] = $data["t2"];
				$array_bulk_load_data["pmca_72_hrs_t3"] = $data["t3"];
			}

			$count_dates++;
		}

		$array_bulk_load_data["created_by"] = $this->login_user->id;
		$array_bulk_load_data["created"] =get_current_utc_time();

		if(count($array_bulk_load_data)){

			$bulk_load = $this->Air_synoptic_data_model->save($array_bulk_load_data);
			
			if($bulk_load){
				echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
			}else{
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
			}
		} else {
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}

		exit();

	}

	function save_forecast_data($options = array()){

		$id_cliente = get_array_value($options, "id_cliente");
		$id_proyecto = get_array_value($options, "id_proyecto");
		$id_record_type = get_array_value($options, "id_record_type");
		$id_model = get_array_value($options, "id_model");
		$id_sector = get_array_value($options, "id_sector");
		$files = get_array_value($options, "files");

		// ITERAR SOBRE LOS ARCHIVOS SUBIDOS POR EL USUARIO

		// $archivos_subidos = array();
		$array_bulk_load_data_final = array();
		foreach($files as $file_number){

			$file = $this->input->post("file_name_".$file_number);
			$archivo_subido = move_temp_file($file, "files/carga_masiva/", "", "", $file);
			
			if($archivo_subido){

				$file_ext = pathinfo("files/carga_masiva/".$file, PATHINFO_EXTENSION);
				$file_basename = basename("files/carga_masiva/".$file,".".$file_ext);
				$file_prefix = explode("_", $file_basename);
				$file_prefix = $file_prefix[0];
				$load_code = substr($file_basename, strpos($file_basename, "_") + 1); // CÓDIGO QUE IDENTIFICA UNA ESTACIÓN

				// VALIDAR QUE EL PREFIJO DEL ARCHIVO CORRESPONDA AL MODELO SELECCIONADO
				if( ($id_model == 1 && $file_prefix != "ml") || ($id_model == 2 && $file_prefix != "redes") || ($id_model == 3 && $file_prefix != "num")){
					echo json_encode(array("success" => false, 'message' => "El archivo ".$file." no corresponde al modelo seleccionado."));
					exit();
				}

				// SI EL MODELO NO ES NUMÉRICO O EL ARCHIVO ES 1D, BUSCA LA ESTACIÓN ASOCIADA AL CÓDIGO DE CARGA
				if($id_model != 3 || $file_basename != "num_mapa"){

					$station = $this->Air_stations_model->get_one_where(array(
						"load_code" => $load_code,
						"is_active" => 1,
						"is_forecast" => 1,
						"deleted" => 0
					));
					
					$id_receptor = $station->id;

					if(!$id_receptor){
						echo json_encode(array("success" => false, 'message' => "El nombre del archivo '".$file_basename."' no está asociado a ninguna estación de pronóstico."));
						exit();
					}

				}
				

				$this->load->library('excel');
				//$this->load->library('excelreadfilter');

				//initialize cache, so the phpExcel will not throw memory overflow
				ini_set('memory_limit', '-1');
				ini_set('max_execution_time', 180); // 180 seconds of execution time maximum
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
				$cacheSettings = array(' memoryCacheSize ' => '512MB');
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				
				$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
				//$excelReader = PHPExcel_IOFactory::createReader('Excel2007');
				//$excelReader = new PHPExcel_Reader_Excel5();

				//read only data (without formating) for memory and time performance
				//$excelReader->setReadDataOnly(true);
				//$excelReader->setLoadSheetsOnly(0);

				//$filterSubset = new ExcelReadFilter(1, 5000, range('A','Z'));
				//$excelReader->setReadFilter($filterSubset);

				/*
				$chunkSize = 5000;
				$chunkFilter = new Chunk();
				$excelReader->setReadFilter($chunkFilter);
				for ($startRow = 1; $startRow <= 5000; $startRow += $chunkSize) {
					// Tell the Read Filter, the limits on which rows we want to read this iteration
					$chunkFilter->setRows($startRow, $chunkSize);
					// Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
					$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);

					$worksheet = $excelObj->getSheet(0);

					$ultima_columna_lat = $worksheet->getHighestColumn(3);
					$ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat));

					// CELDA FECHA MODELO
					$fecha_excel = $worksheet->getCell('B2')->getValue();
					if($this->validateDate($fecha_excel, 'd-m-Y')){
						
					}else{
						$html .= '<tr>';
						$html .= '<td>B2</td>';
						$html .= '<td>'.$fecha_excel.'</td>';
						$html .= '<td>'.$msg_formato.'</td>';
						$html .= '</tr>';
						$num_errores++;
					}

					echo $fecha_excel;

				}
				exit();*/

				$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);

				// iterar las hojas, leer el nombre de la hoja (sigla variable), buscar en bd la variable por la sigla,
				// si el registro de variable que pertenece a la sigla existe, ver qué tipo de variable es ($num_formato)
				
				$array_bulk_load_data = array();
				foreach($excelObj->getAllSheets() as $worksheet) {

					$variable = $this->Air_variables_model->get_one_where(array(
						"sigla" => $worksheet->getTitle(),
						"deleted" => 0
					));

					// SI LA VARIABLE NO EXISTE, MOSTRAR MENSAJE DE ERROR
					if(!$variable->id){
						echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => "No existe la variable con sigla ".$worksheet->getTitle()));
						exit();
					}

					// COMPROBACION DE DATOS CORRECTOS
					$num_errores = 0;
					$msg_obligatorio = lang('air_bulk_load_obligatory_field');
					$msg_formato = lang('air_bulk_load_invalid_format_field');

					$lastRow = $worksheet->getHighestRow();

					// COMIENZO A RENDERIZAR
					$html = '<table class="table table-responsive table-striped">';
					$html .= '<thead><tr><th>Pestaña</th><th>Celda</th><th>Valor</th><th>Error</th></tr></thead>';
					$html .= '<tbody>';

					if($id_model == 3 && $file_basename == "num_mapa"){ // Numérico && Ninguno (Mapa)

						if($variable->id_air_variable_type == 1){ // Variable Meteorológica

							// $ultima_columna_lat = $worksheet->getHighestColumn(3); // AI
							// $ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat)); // 35

							$ultima_columna_lat = $worksheet->getHighestColumn(1); // AC
							// var_dump("ultima_columna_lat: ".$ultima_columna_lat);
							$ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat)); // 29
							// var_dump("ultima_columna_lat_n: ".$ultima_columna_lat_n);
							// exit();
			
			
							// FILA 2 - LATITUDES
							for($column = 3; $column <= $ultima_columna_lat_n; $column++){
			
								$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);
								$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
								
								if(strlen(trim($valor_columna)) > 0){
												
									if(is_numeric($valor_columna)){
										
									}else{
										$html .= '<tr>';
										$html .= '<td>'.$worksheet->getTitle().'</td>';
										$html .= '<td>'.$letra_columna.'3</td>';
										$html .= '<td>'.$valor_columna.'</td>';
										$html .= '<td>'.$msg_formato.'</td>';
										$html .= '</tr>';
										$num_errores++;
									}
											
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>'.$letra_columna.'3</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_obligatorio.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
							}
							// exit();

							// FILA > 3 - DATOS
							$qty_records = 0;
							for($row = 2; $row <= $lastRow; $row++){
								
								// CELDA FECHA
								$valor_columna = $worksheet->getCell('A'.$row)->getValue();
			
								// VALIDO QUE VENGA SI O SI, UNA FECHA EN LA CELDA A4
								if($row == 2 && $valor_columna == NULL){
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>A'.$row.'</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_formato.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
			
								// DETECTAR SI ES SERIAL NUMBER
								/*if(is_numeric($valor_columna)){
									$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($valor_columna);
									$valor_columna = $datetime->format('Y-m-d G:i');
								}
								// VALIDO RESTO DE CELDAS EN LA COLUMNA A
								if($valor_columna == NULL || $this->validateDate($valor_columna, 'Y-m-d G:i')){
									
								}*/
			
								//Y-m-d G:i
								if($this->validateDate($valor_columna, 'Y-m-d H:i')){
									$qty_records++;
								}elseif($this->validateDate($valor_columna, 'Y-m-d G:i')){
									$qty_records++;
								}elseif($this->validateDate($valor_columna, 'Y-m-d H:i:s')){
									$qty_records++;
								}elseif($this->validateDate($valor_columna, 'd-m-Y H:i')){
									$qty_records++;
								}elseif($this->validateDate($valor_columna, 'd-m-Y G:i')){
									$qty_records++;
								}elseif($this->validateDate($valor_columna, 'd-m-Y H:i:s')){
									$qty_records++;
								}elseif(is_numeric($valor_columna)){
									$qty_records++;
								}elseif($valor_columna == NULL){
									
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>A'.$row.'</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_formato.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
			
								// CELDA LATITUD
								$valor_columna = $worksheet->getCell('B'.$row)->getValue();
								if(strlen(trim($valor_columna)) > 0){
												
									if(is_numeric($valor_columna)){
										
									}else{
										$html .= '<tr>';
										$html .= '<td>'.$worksheet->getTitle().'</td>';
										$html .= '<td>B'.$row.'</td>';
										$html .= '<td>'.$valor_columna.'</td>';
										$html .= '<td>'.$msg_formato.'</td>';
										$html .= '</tr>';
										$num_errores++;
									}
											
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>B'.$row.'</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_obligatorio.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
			
								// CELDA > B - VALORES
								for($column = 3; $column <= $ultima_columna_lat_n; $column++){
			
									$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);
									$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
									
									if(strlen(trim($valor_columna)) > 0){
													
										if(is_numeric($valor_columna)){
											
										}else{
											$html .= '<tr>';
											$html .= '<td>'.$worksheet->getTitle().'</td>';
											$html .= '<td>'.$letra_columna.$row.'</td>';
											$html .= '<td>'.$valor_columna.'</td>';
											$html .= '<td>'.$msg_formato.'</td>';
											$html .= '</tr>';
											$num_errores++;
										}
												
									}else{
										$html .= '<tr>';
										$html .= '<td>'.$worksheet->getTitle().'</td>';
										$html .= '<td>'.$letra_columna.$row.'</td>';
										$html .= '<td>'.$valor_columna.'</td>';
										$html .= '<td>'.$msg_obligatorio.'</td>';
										$html .= '</tr>';
										$num_errores++;
									}
								}
							}

							// exit();
			
							// Si los registros son mayores a 24 (ANTES: Si los registros no son 72 if($qty_records != 72) )
							if($qty_records > 24){
								echo json_encode(array("success" => false, 'message' => lang('only_up_to_24_hrs_data').". Revisar archivo '".$file."'"));
								exit();
							}

						} elseif($variable->id_air_variable_type == 2){ // Variable Calidad del aire

							$ultima_columna_lat = $worksheet->getHighestColumn(1); // AHH
							$ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat)); // 892
							
							// LATITUDES Y LONGITUDES
							for($column = 2; $column <= $ultima_columna_lat_n; $column++){
								
								// FILA LATITUDES
								$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);
								$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
			
								if(strlen(trim($valor_columna)) > 0){
												
									if(is_numeric($valor_columna)){
										
									}else{
										$html .= '<tr>';
										$html .= '<td>'.$worksheet->getTitle().'</td>';
										$html .= '<td>'.$letra_columna.'3</td>';
										$html .= '<td>'.$valor_columna.'</td>';
										$html .= '<td>'.$msg_formato.'</td>';
										$html .= '</tr>';
										$num_errores++;
									}
											
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>'.$letra_columna.'3</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_obligatorio.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
			
								// FILA LONGITUDES
								$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);
								$valor_columna = $worksheet->getCell($letra_columna.'2')->getValue();
			
								if(strlen(trim($valor_columna)) > 0){
												
									if(is_numeric($valor_columna)){
										
									}else{
										$html .= '<tr>';
										$html .= '<td>'.$worksheet->getTitle().'</td>';
										$html .= '<td>'.$letra_columna.'4</td>';
										$html .= '<td>'.$valor_columna.'</td>';
										$html .= '<td>'.$msg_formato.'</td>';
										$html .= '</tr>';
										$num_errores++;
									}
											
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>'.$letra_columna.'4</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_obligatorio.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
							}
			
							// FILA >= 4 - DATOS
							$qty_records = 0;
							for($row = 4; $row <= $lastRow; $row++){
			
								// CELDA FECHA
								$valor_columna = $worksheet->getCell('A'.$row)->getValue();
								//$valor_columna = PHPExcel_Shared_Date::ExcelToPHPObject($valor_columna);
								//$valor_columna = $valor_columna->format('Y-m-d G:i');
			
								// DETECTAR SI ES SERIAL NUMBER
								/*if(is_numeric($valor_columna)){
									$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($valor_columna);
									$valor_columna = $datetime->format('Y-m-d G:i');
								}*/
								
								//Y-m-d G:i
								if($this->validateDate($valor_columna, 'Y-m-d H:i')){
							
								}elseif($this->validateDate($valor_columna, 'Y-m-d G:i')){
								
								}elseif($this->validateDate($valor_columna, 'Y-m-d H:i:s')){
								
								}elseif($this->validateDate($valor_columna, 'd-m-Y H:i')){
								
								}elseif($this->validateDate($valor_columna, 'd-m-Y G:i')){
								
								}elseif($this->validateDate($valor_columna, 'd-m-Y H:i:s')){
								
								}elseif(is_numeric($valor_columna)){
			
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>A'.$row.'</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_formato.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
			
								// COLUMNAS >= B
								for($column = 2; $column <= ($ultima_columna_lat_n); $column++){
								
									$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);
									$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
			
									if(strlen(trim($valor_columna)) > 0){
													
										if(is_numeric($valor_columna)){
											
										}else{
											$html .= '<tr>';
											$html .= '<td>'.$worksheet->getTitle().'</td>';
											$html .= '<td>'.$letra_columna.$row.'</td>';
											$html .= '<td>'.$valor_columna.'</td>';
											$html .= '<td>'.$msg_formato.'</td>';
											$html .= '</tr>';
											$num_errores++;
										}
												
									}else{
										$html .= '<tr>';
										$html .= '<td>'.$worksheet->getTitle().'</td>';
										$html .= '<td>'.$letra_columna.$row.'</td>';
										$html .= '<td>'.$valor_columna.'</td>';
										$html .= '<td>'.$msg_obligatorio.'</td>';
										$html .= '</tr>';
										$num_errores++;
									}
			
								}
								// exit();
			
								$qty_records++;
			
							}
			
							// var_dump($qty_records);
							// exit();

							// Si los registros son mayores a 24 (ANTES: Si los registros no son 72 if($qty_records != 72) )
							if($qty_records > 24){
								echo json_encode(array("success" => false, 'message' => lang('only_up_to_24_hrs_data').". Revisar archivo '".$file."'"));
								exit();
							}

						}

					} else { // Datos 1D

						// VALIDA QUE LAS VARIABLES INDICADAS EN EL NOMBRE DE LAS PESTAÑAS ESTÉN ASOCIADAS A LA ESTACIÓN
						$station_rel_variable = $this->Air_stations_rel_variables_model->get_one_where(array(
							"id_air_station" => $id_receptor,
							"id_air_variable" => $variable->id,
							"deleted" => 0
						));

						if(!$station_rel_variable->id){
							echo json_encode(array("success" => false, 'message' => "La variable con sigla ".$variable->sigla." no pertenece a la estación ".$station->name));
							exit();
						}

						$ultima_columna_lat = $worksheet->getHighestColumn(1);
						$ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat));
		
						// CELDA FECHA MODELO
						$fecha_excel = $worksheet->getCell('B1')->getValue();
						//Y-m-d H:i
						if($this->validateDate($fecha_excel, 'Y-m-d H:i')){
						
						}elseif($this->validateDate($fecha_excel, 'Y-m-d G:i')){
						
						}elseif($this->validateDate($fecha_excel, 'Y-m-d H:i:s')){
						
						}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i')){
						
						}elseif($this->validateDate($fecha_excel, 'd-m-Y G:i')){
						
						}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i:s')){
						
						}elseif(is_numeric($fecha_excel)){
						
						}else{
							$html .= '<tr>';
							$html .= '<td>'.$worksheet->getTitle().'</td>';
							$html .= '<td>B1</td>';
							$html .= '<td>'.$fecha_excel.'</td>';
							$html .= '<td>'.$msg_formato.'</td>';
							$html .= '</tr>';
							$num_errores++;
						}
		
						// FILA >= 2 - FECHA Y DATO
						$qty_records = 0;
						for($row = 2; $row <= $lastRow; $row++){
							
							// CELDA FECHA
							$valor_columna = $worksheet->getCell('A'.$row)->getValue();
							//d-m-Y G:i
							if($this->validateDate($valor_columna, 'Y-m-d H:i')){
						
							}elseif($this->validateDate($valor_columna, 'Y-m-d G:i')){
							
							}elseif($this->validateDate($valor_columna, 'Y-m-d H:i:s')){
							
							}elseif($this->validateDate($valor_columna, 'd-m-Y H:i')){
							
							}elseif($this->validateDate($valor_columna, 'd-m-Y G:i')){
							
							}elseif($this->validateDate($valor_columna, 'd-m-Y H:i:s')){
							
							}elseif(is_numeric($valor_columna)){
		
							}else{
								$html .= '<tr>';
								$html .= '<td>'.$worksheet->getTitle().'</td>';
								$html .= '<td>A'.$row.'</td>';
								$html .= '<td>'.$valor_columna.'</td>';
								$html .= '<td>'.$msg_formato.'</td>';
								$html .= '</tr>';
								$num_errores++;
							}
		
							// CELDA VALOR
							$valor_columna = $worksheet->getCell('B'.$row)->getValue();
							if(strlen(trim($valor_columna)) > 0){
											
								if(is_numeric($valor_columna)){
									
								}else{
									$html .= '<tr>';
									$html .= '<td>'.$worksheet->getTitle().'</td>';
									$html .= '<td>B'.$row.'</td>';
									$html .= '<td>'.$valor_columna.'</td>';
									$html .= '<td>'.$msg_formato.'</td>';
									$html .= '</tr>';
									$num_errores++;
								}
										
							}else{
								$html .= '<tr>';
								$html .= '<td>'.$worksheet->getTitle().'</td>';
								$html .= '<td>B'.$row.'</td>';
								$html .= '<td>'.$valor_columna.'</td>';
								$html .= '<td>'.$msg_obligatorio.'</td>';
								$html .= '</tr>';
								$num_errores++;
							}


							// // SI LA VARIABLE ES PM10, SE VALIDAN LAS COLUMNAS DE INTERVALO DE CONFIANZA
							// if($variable->id == 9){

							// 	// CELDA RANGO MINIMO - INTERVALO DE CONFIANZA
							// 	$valor_columna = $worksheet->getCell('C'.$row)->getValue();
							// 	if(strlen(trim($valor_columna)) > 0){
												
							// 		if(is_numeric($valor_columna)){
										
							// 		}else{
							// 			$html .= '<tr>';
							// 			$html .= '<td>'.$worksheet->getTitle().'</td>';
							// 			$html .= '<td>C'.$row.'</td>';
							// 			$html .= '<td>'.$valor_columna.'</td>';
							// 			$html .= '<td>'.$msg_formato.'</td>';
							// 			$html .= '</tr>';
							// 			$num_errores++;
							// 		}
											
							// 	}else{
							// 		$html .= '<tr>';
							// 		$html .= '<td>'.$worksheet->getTitle().'</td>';
							// 		$html .= '<td>C'.$row.'</td>';
							// 		$html .= '<td>'.$valor_columna.'</td>';
							// 		$html .= '<td>'.$msg_obligatorio.'</td>';
							// 		$html .= '</tr>';
							// 		$num_errores++;
							// 	}

							// 	// CELDA RANGO MÁXIMO - INTERVALO DE CONFIANZA
							// 	$valor_columna = $worksheet->getCell('D'.$row)->getValue();
							// 	if(strlen(trim($valor_columna)) > 0){
												
							// 		if(is_numeric($valor_columna)){
										
							// 		}else{
							// 			$html .= '<tr>';
							// 			$html .= '<td>'.$worksheet->getTitle().'</td>';
							// 			$html .= '<td>D'.$row.'</td>';
							// 			$html .= '<td>'.$valor_columna.'</td>';
							// 			$html .= '<td>'.$msg_formato.'</td>';
							// 			$html .= '</tr>';
							// 			$num_errores++;
							// 		}
											
							// 	}else{
							// 		$html .= '<tr>';
							// 		$html .= '<td>'.$worksheet->getTitle().'</td>';
							// 		$html .= '<td>D'.$row.'</td>';
							// 		$html .= '<td>'.$valor_columna.'</td>';
							// 		$html .= '<td>'.$msg_obligatorio.'</td>';
							// 		$html .= '</tr>';
							// 		$num_errores++;
							// 	}
							
							// }

							$qty_records++;
						}

						// Si los registros no son 72
						if($qty_records != 72){
							echo json_encode(array("success" => false, 'message' => lang('only_72_hrs_data').". Revisar archivo '".$file."'"));
							exit();
						}

					}

					$html .= '</tbody>';
					$html .= '</table>';

					

					if($num_errores > 0){
						echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
						exit();
					}else{

						$array_bulk_load_data[] = array(
							'id_cliente' => $id_cliente, 
							'id_proyecto' => $id_proyecto, 
							'id_record_type' => $id_record_type, 
							'id_model' => $id_model,
							'id_sector' => $id_sector,
							'id_receptor' => $id_receptor,
							'id_variable' => $variable->id,
							'id_tipo_variable' => $variable->id_air_variable_type,
							'archivo_subido' => $archivo_subido,
							//'num_formato' => $num_formato
						);

						/*$this->bulk_load(
							array(
								'id_cliente' => $id_cliente, 
								'id_proyecto' => $id_proyecto, 
								'id_record_type' => $id_record_type, 
								'id_model' => $id_model,
								'id_sector' => $id_sector,
								'id_receptor' => $id_receptor,
								'id_variable' => $id_variable,
								'id_tipo_variable' => $id_tipo_variable,
								'archivo_subido' => $archivo_subido,
								'num_formato' => $num_formato
							)
						);*/
						//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
					}
					


				} // END FOREACH HOJAS EXCEL
				
				if(count($array_bulk_load_data)){

					// $this->bulk_load($array_bulk_load_data, $archivo_subido);
					// var_dump($array_bulk_load_data);

					$array_bulk_load_data_final[] = $array_bulk_load_data;

				}

				// exit();
			} // END IF ARCHIVO SUBIDO


		} // END FOREACH ARCHIVOS

		if(count($files) == count($array_bulk_load_data_final)){
			// var_dump("BULK LOAD :D");
			// var_dump(count($files), $files);
			// var_dump(count($array_bulk_load_data_final), $array_bulk_load_data_final);
			// exit();
			// $this->bulk_load($array_bulk_load_data_final, $files);

			foreach($array_bulk_load_data_final as $array_bulk_load_data){
				// var_dump($array_bulk_load_data);
				// var_dump($array_bulk_load_data[0]["archivo_subido"]);
				$bulk_load = $this->bulk_load($array_bulk_load_data, $array_bulk_load_data[0]["archivo_subido"]);
			}

		}

		if($bulk_load){
			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}
		exit();

	}


	// function save_monitoring_data($options = array()){

	// 	$id_cliente = get_array_value($options, "id_cliente");
	// 	$id_proyecto = get_array_value($options, "id_proyecto");
	// 	$id_record_type = get_array_value($options, "id_record_type");
	// 	// $id_model = get_array_value($options, "id_model");
	// 	$id_sector = get_array_value($options, "id_sector");
	// 	$file = get_array_value($options, "file");

	// 	$archivo_subido = move_temp_file($file, "files/carga_masiva/", "", "", $file);
			
	// 	if($archivo_subido){
			
	// 		$file_ext = pathinfo("files/carga_masiva/".$file, PATHINFO_EXTENSION);
	// 		$file_basename = basename("files/carga_masiva/".$file,".".$file_ext);
	// 		$file_prefix = explode("_", $file_basename);
	// 		$file_prefix = $file_prefix[0];
	// 		$sigla_variable = substr($file_basename, strpos($file_basename, "_") + 1); // CÓDIGO QUE IDENTIFICA LA VARIABLE SO2

	// 		// VALIDAR QUE EL PREFIJO DEL ARCHIVO CORRESPONDA AL MODELO SELECCIONADO
	// 		// if( ($id_model == 1 && $file_prefix != "ml") || ($id_model == 2 && $file_prefix != "redes") || ($id_model == 3 && $file_prefix != "num")){
	// 		// 	echo json_encode(array("success" => false, 'message' => "El archivo ".$file." no corresponde al modelo seleccionado."));
	// 		// 	exit();
	// 		// }

	// 		$variable = $this->Air_variables_model->get_one_where(array(
	// 			"sigla" => strtoupper($sigla_variable),
	// 			"deleted" => 0
	// 		));
			
	// 		if($sigla_variable != "so2"){
	// 			echo json_encode(array("success" => false, 'message' => "El nombre del archivo debe ser '".$file_prefix."_so2.xlsx'"));
	// 			exit();
	// 		}

	// 		$stations = $this->Air_stations_model->get_all_where(array(
	// 			"id_client" => $id_cliente,
	// 			"id_project" => $id_proyecto,
	// 			"id_air_sector" => $id_sector,
	// 			"deleted" => 0
	// 		))->result();


	// 		$this->load->library('excel');

	// 		//initialize cache, so the phpExcel will not throw memory overflow
	// 		ini_set('memory_limit', '-1');
	// 		ini_set('max_execution_time', 180); // 180 seconds of execution time maximum
	// 		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	// 		$cacheSettings = array(' memoryCacheSize ' => '512MB');
	// 		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			
	// 		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
	// 		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);


	// 		// iterar las hojas, leer el nombre de la hoja (código estación), buscar en bd la estación por el código
	// 		$array_bulk_load_data = array();
	// 		foreach($excelObj->getAllSheets() as $worksheet) {

	// 			$station = $this->Air_stations_model->get_one_where(array(
	// 				"load_code" => $worksheet->getTitle(),
	// 				"deleted" => 0
	// 			));

	// 			// SI LA ESTACIÓN NO EXISTE, MOSTRAR MENSAJE DE ERROR
	// 			if(!$station->id){
	// 				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => "No existe la estación con el código ".$worksheet->getTitle()));
	// 				exit();
	// 			}

	// 			// COMPROBACION DE DATOS CORRECTOS
	// 			$num_errores = 0;
	// 			$msg_obligatorio = lang('air_bulk_load_obligatory_field');
	// 			$msg_formato = lang('air_bulk_load_invalid_format_field');

	// 			$lastRow = $worksheet->getHighestRow();

	// 			// COMIENZO A RENDERIZAR
	// 			$html = '<table class="table table-responsive table-striped">';
	// 			$html .= '<thead><tr><th>Pestaña</th><th>Celda</th><th>Valor</th><th>Error</th></tr></thead>';
	// 			$html .= '<tbody>';


	// 			// VALIDA QUE LA VARIABLE SO2 ESTÉ ASOCIADA A LA ESTACIÓN
	// 			$station_rel_variable = $this->Air_stations_rel_variables_model->get_one_where(array(
	// 				"id_air_station" => $station->id,
	// 				"id_air_variable" => 8, // SO2
	// 				"deleted" => 0
	// 			));

	// 			if(!$station_rel_variable->id){
	// 				echo json_encode(array("success" => false, 'message' => "La estación ".$station->name." no tiene asociada la variable SO2"));
	// 				exit();
	// 			}


	// 			// CELDA FECHA MODELO
	// 			$fecha_excel = $worksheet->getCell('B1')->getValue();
	// 			//Y-m-d H:i
	// 			if($this->validateDate($fecha_excel, 'Y-m-d H:i')){
				
	// 			}elseif($this->validateDate($fecha_excel, 'Y-m-d G:i')){
				
	// 			}elseif($this->validateDate($fecha_excel, 'Y-m-d H:i:s')){
				
	// 			}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i')){
				
	// 			}elseif($this->validateDate($fecha_excel, 'd-m-Y G:i')){
				
	// 			}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i:s')){
				
	// 			}elseif(is_numeric($fecha_excel)){
				
	// 			}else{
	// 				$html .= '<tr>';
	// 				$html .= '<td>'.$worksheet->getTitle().'</td>';
	// 				$html .= '<td>B1</td>';
	// 				$html .= '<td>'.$fecha_excel.'</td>';
	// 				$html .= '<td>'.$msg_formato.'</td>';
	// 				$html .= '</tr>';
	// 				$num_errores++;
	// 			}

	// 			// FILA >= 2 - FECHA Y DATO
	// 			$qty_records = 0;
	// 			for($row = 2; $row <= $lastRow; $row++){
					
	// 				// CELDA FECHA
	// 				$valor_columna = $worksheet->getCell('A'.$row)->getValue();
	// 				//d-m-Y G:i
	// 				if($this->validateDate($valor_columna, 'Y-m-d H:i')){
				
	// 				}elseif($this->validateDate($valor_columna, 'Y-m-d G:i')){
					
	// 				}elseif($this->validateDate($valor_columna, 'Y-m-d H:i:s')){
					
	// 				}elseif($this->validateDate($valor_columna, 'd-m-Y H:i')){
					
	// 				}elseif($this->validateDate($valor_columna, 'd-m-Y G:i')){
					
	// 				}elseif($this->validateDate($valor_columna, 'd-m-Y H:i:s')){
					
	// 				}elseif(is_numeric($valor_columna)){

	// 				}else{
	// 					$html .= '<tr>';
	// 					$html .= '<td>'.$worksheet->getTitle().'</td>';
	// 					$html .= '<td>A'.$row.'</td>';
	// 					$html .= '<td>'.$valor_columna.'</td>';
	// 					$html .= '<td>'.$msg_formato.'</td>';
	// 					$html .= '</tr>';
	// 					$num_errores++;
	// 				}

	// 				// CELDA VALOR
	// 				$valor_columna = $worksheet->getCell('B'.$row)->getValue();
	// 				if(strlen(trim($valor_columna)) > 0){
									
	// 					if(is_numeric($valor_columna)){
							
	// 					}else{
	// 						$html .= '<tr>';
	// 						$html .= '<td>'.$worksheet->getTitle().'</td>';
	// 						$html .= '<td>B'.$row.'</td>';
	// 						$html .= '<td>'.$valor_columna.'</td>';
	// 						$html .= '<td>'.$msg_formato.'</td>';
	// 						$html .= '</tr>';
	// 						$num_errores++;
	// 					}
								
	// 				}else{
	// 					$html .= '<tr>';
	// 					$html .= '<td>'.$worksheet->getTitle().'</td>';
	// 					$html .= '<td>B'.$row.'</td>';
	// 					$html .= '<td>'.$valor_columna.'</td>';
	// 					$html .= '<td>'.$msg_obligatorio.'</td>';
	// 					$html .= '</tr>';
	// 					$num_errores++;
	// 				}
	// 				$qty_records++;
	// 			}

	// 			$html .= '</tbody>';
	// 			$html .= '</table>';

	// 			// Si los registros no son 72
	// 			if($qty_records != 72){
	// 				echo json_encode(array("success" => false, 'message' => lang('only_72_hrs_data').". Revisar archivo '".$file."'"));
	// 				exit();
	// 			}

	// 			if($num_errores > 0){
	// 				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
	// 				exit();
	// 			}else{

	// 				$array_bulk_load_data[] = array(
	// 					'id_cliente' => $id_cliente, 
	// 					'id_proyecto' => $id_proyecto, 
	// 					'id_record_type' => $id_record_type, 
	// 					// 'id_model' => $id_model,
	// 					'id_sector' => $id_sector,
	// 					'id_receptor' => $station->id,
	// 					'id_variable' => 8, // SO2
	// 					'id_tipo_variable' => 2, // CALIDAD DEL AIRE
	// 					'archivo_subido' => $archivo_subido,
	// 					//'num_formato' => $num_formato
	// 				);

	// 			}

	// 		} // END FOREACH HOJA EXCEL

	// 		if(count($array_bulk_load_data)){
	// 			$bulk_load = $this->bulk_load_monitoring($array_bulk_load_data, $archivo_subido);
	// 		}

	// 		if($bulk_load){
	// 			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
	// 		}else{
	// 			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
	// 		}
	// 		exit();

	// 	}

	// }
	
	/**
	 * bulk_load
	 * 
	 * Guarda en la base de datos, los datos de Pronósticos por Sector, subidos por el Usuario
	 * a través de una plantilla Excel
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @param array $array <br/>
     *      $options['id_cliente'] => (int) id de Cliente. <br/>
     *      $options['id_proyecto'] => (int) id de Proyecto. <br/>
     *      $options['id_record_type'] => (int) id de Tipo de Registro. <br/>
     *      $options['id_model'] => (int) id de Modelo. <br/>
     *      $options['id_sector'] => (int) id de Sector. <br/>
     *      $options['id_receptor'] => (int) id de Receptor o Estación. <br/>
	 * 		$options['id_variable'] => (int) id de Variable. <br/>
	 * 		$options['id_tipo_variable'] => (int) id de Tipo de Variable. <br/>
	 * 		$options['archivo_subido'] => (string) plantilla Excel de Carga Masiva subida por el Usuario. <br/>
	 * 		$options['num_formato'] => (int) El formato de plantilla Excel. <br/>

	 * @uses int $this->login_user->id id del Usuario en sesión
	 * @return JSON Con un mensaje de éxito o error según sea el caso.
	 */
	function bulk_load($array_bulk_load_data = array(), $archivo_subido){

		// var_dump("bulk_load");
		// var_dump($array_bulk_load_data);
		// exit();

		$file_ext = pathinfo("files/carga_masiva/".$archivo_subido, PATHINFO_EXTENSION);
		$file_basename = basename("files/carga_masiva/".$archivo_subido,".".$file_ext);
		//$load_code = substr($file_basename, strpos($file_basename, "_") + 1); // CÓDIGO QUE IDENTIFICA UNA ESTACIÓN

		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);

		foreach($array_bulk_load_data as $array){
			
			//$this->load->library('excel');
			$id_cliente = $array['id_cliente'];
			$id_proyecto = $array['id_proyecto'];
			$id_record_type = $array['id_record_type']; 
			$id_model = $array['id_model'];
			$id_sector = $array['id_sector'];
			$id_receptor = $array['id_receptor'];
			$id_variable = $array['id_variable'];
			$id_tipo_variable = $array['id_tipo_variable'];
			$archivo_subido = $array['archivo_subido'];
			//$num_formato = $array['num_formato'];

			//$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
			//$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);

			foreach($excelObj->getAllSheets() as $worksheet) {

				$variable = $this->Air_variables_model->get_one_where(array(
					"sigla" => $worksheet->getTitle(),
					"deleted" => 0
				));

				if($variable->id == $id_variable){
					
					//$worksheet = $excelObj->getSheet(0);
					$lastRow = $worksheet->getHighestRow();

					$array_insert = array();
					
					if($id_model == 3 && $file_basename == "num_mapa"){ // Numérico && Ninguno (Mapa)

						if($variable->id_air_variable_type == 1){ // Variable Meteorológica

							$ultima_columna_lat = $worksheet->getHighestColumn(1); // AC
							$ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat)); // 29

							// DATOS TABLA air_records_values_uploads
							$array_data_air_records_values_uploads = array(
								"model_creation_date" => get_current_utc_time(),
								"upload_format" => $file_basename,
								"created" => get_current_utc_time(),
								"created_by" => $this->login_user->id
							);

							// REGISTRO DE PRONÓSTICO ASOCIADO AL CLIENTE, PROYECTO, SECTOR, MODELO, TIPO DE REGISTRO.
							$air_record = $this->Air_records_model->get_one_where(array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								"id_air_sector" => $id_sector,
								//"id_air_station" => ($id_receptor == 0) ? NULL : $id_receptor,
								"id_air_station" => null,
								"id_air_model" => $id_model,
								"id_air_record_type" => $id_record_type,
								"deleted" => 0
							));

							$array_data_air_records_values_uploads["id_record"] = $air_record->id;
							$save_id_air_record_values_upload = $this->Air_records_values_uploads_model->save($array_data_air_records_values_uploads);


							// REORDENAR DATOS
							$array_datos = array();
							$fecha_anterior;
							for($row = 2; $row <= $lastRow; $row++){

								//CELDA FECHA
								$fecha = $worksheet->getCell('A'.$row)->getValue();

								if($fecha != NULL){

									// DETECTAR SI ES SERIAL NUMBER
									/*if(is_numeric($fecha)){
										$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha);
										$fecha = $datetime->format('Y-m-d G:i');
									}

									$fecha_anterior = $fecha;
									$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha);*/

									$fecha_anterior = $fecha;
									if($this->validateDate($fecha, 'Y-m-d H:i')){
										$datetime = DateTime::createFromFormat('Y-m-d H:i', $fecha);
									}elseif($this->validateDate($fecha, 'Y-m-d G:i')){
										$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha);
										//$fecha = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha, 'Y-m-d H:i:s')){
										$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
										//$fecha = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha, 'd-m-Y H:i')){
										$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha);
										//$fecha = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha, 'd-m-Y G:i')){
										$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha);
										//$fecha = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha, 'd-m-Y H:i:s')){
										$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha);
										//$fecha = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha_excel, 'Y-m-d  H:i')){
										$datetime = DateTime::createFromFormat('Y-m-d  H:i', $fecha_excel);
										$fecha_excel = $datetime->format('Y-m-d H:i');
									}elseif(is_numeric($fecha)){
										$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha);
										//$fecha = $datetime->format('Y-m-d H:i');
									}else{
						
									}
									$date = $datetime->format('Y-m-d');
									$hour = $datetime->format('H');
								} else {
									//$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_anterior);
									if($this->validateDate($fecha_anterior, 'Y-m-d H:i')){
										$datetime = DateTime::createFromFormat('Y-m-d H:i', $fecha_anterior);
									}elseif($this->validateDate($fecha_anterior, 'Y-m-d G:i')){
										$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_anterior);
										//$fecha_anterior = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha_anterior, 'Y-m-d H:i:s')){
										$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_anterior);
										//$fecha_anterior = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha_anterior, 'd-m-Y H:i')){
										$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha_anterior);
										//$fecha_anterior = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha_anterior, 'd-m-Y G:i')){
										$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha_anterior);
										//$fecha_anterior = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha_anterior, 'd-m-Y H:i:s')){
										$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha_anterior);
										//$fecha_anterior = $datetime->format('Y-m-d H:i');
									}elseif($this->validateDate($fecha_excel, 'Y-m-d  H:i')){
										$datetime = DateTime::createFromFormat('Y-m-d  H:i', $fecha_excel);
										$fecha_excel = $datetime->format('Y-m-d H:i');
									}elseif(is_numeric($fecha_anterior)){
										$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_anterior);
										//$fecha_anterior = $datetime->format('Y-m-d H:i');
									}else{
						
									}
									$date = $datetime->format('Y-m-d');
									$hour = $datetime->format('H');
								}
								
								//LATITUD
								$latitud = $worksheet->getCell('B'.$row)->getValue();

								// COLUMNAS >= C
								for($column = 3; $column <= $ultima_columna_lat_n; $column++){

									$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);

									// LONGITUD
									$longitud = $worksheet->getCell($letra_columna.'1')->getValue();

									// VALOR
									$valor = $worksheet->getCell($letra_columna.$row)->getValue();

									$array_datos[$date][$latitud.':'.$longitud][(string)$hour] = $valor;
								}

							}
							
							// ARMAR ARRAY FINAL
							foreach ($array_datos as $fecha => $latlon) {

								foreach($latlon as $latylong => $horas){

									$coordenadas = explode(':', $latylong);
									$latitud = $coordenadas[0];
									$longitud = $coordenadas[1];

									$array_row = array();
									$array_row["id_client"] = $id_cliente;
									$array_row["id_project"] = $id_proyecto;
									$array_row["id_record"] = $air_record->id;
									$array_row["id_variable"] = $id_variable;
									$array_row["id_upload"] = $save_id_air_record_values_upload;
									$array_row["latitude"] = $latitud;
									$array_row["longitude"] = $longitud;
									$array_row["date"] = $fecha;

									// Se fuerza el seteo de todos los campos hora para el insert 
									for($hora = 0; $hora <= 23; $hora++){
										$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
										$array_row[$time_field] = null;
									}

									foreach ($horas as $hora => $valor) {
										$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
									}
									
									$array_insert[] = $array_row;

								}

							}

						} elseif($variable->id_air_variable_type == 2){ // Variable Calidad del aire

							$ultima_columna_lat = $worksheet->getHighestColumn(1); // AHH
							$ultima_columna_lat_n = PHPExcel_Cell::columnIndexFromString($ultima_columna_lat); // 892
		
							// DATOS TABLA air_records_values_uploads
							$array_data_air_records_values_uploads = array(
								"model_creation_date" => get_current_utc_time(),
								"upload_format" => $file_basename,
								"created" => get_current_utc_time(),
								"created_by" => $this->login_user->id
							);

		
							// REGISTRO DE PRONÓSTICO ASOCIADO AL CLIENTE, PROYECTO, SECTOR, MODELO, TIPO DE REGISTRO.
							$air_record = $this->Air_records_model->get_one_where(array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								"id_air_sector" => $id_sector,
								//"id_air_station" => ($id_receptor == 0) ? NULL : $id_receptor,
								"id_air_station" => null,
								"id_air_model" => $id_model,
								"id_air_record_type" => $id_record_type,
								"deleted" => 0
							));
		
							$array_data_air_records_values_uploads["id_record"] = $air_record->id;
							$save_id_air_record_values_upload = $this->Air_records_values_uploads_model->save($array_data_air_records_values_uploads);
		
							// FILA >= 4 - DATOS
							$array_datos = array();
							for($row = 4; $row <= $lastRow; $row++){
		
								$fecha = $worksheet->getCell('A'.$row)->getValue();
								//$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha);
		
								if($this->validateDate($fecha, 'Y-m-d H:i')){
									$datetime = DateTime::createFromFormat('Y-m-d H:i', $fecha);
								}elseif($this->validateDate($fecha, 'Y-m-d G:i')){
									$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha);
									//$fecha = $datetime->format('Y-m-d H:i');
								}elseif($this->validateDate($fecha, 'Y-m-d H:i:s')){
									$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
									//$fecha = $datetime->format('Y-m-d H:i');
								}elseif($this->validateDate($fecha, 'd-m-Y H:i')){
									$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha);
									//$fecha = $datetime->format('Y-m-d H:i');
								}elseif($this->validateDate($fecha, 'd-m-Y G:i')){
									$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha);
									//$fecha = $datetime->format('Y-m-d H:i');
								}elseif($this->validateDate($fecha, 'd-m-Y H:i:s')){
									$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha);
									//$fecha = $datetime->format('Y-m-d H:i');
								}elseif(is_numeric($fecha)){
									$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha);
									//$fecha = $datetime->format('Y-m-d H:i');
								}else{
					
								}
		
								/*if(is_numeric($fecha)){
									$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($valor_columna);
									$valor_columna = $datetime->format('Y-m-d G:i');
								}else{
									$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha);
								}*/
								$date = $datetime->format('Y-m-d');
								$hour = $datetime->format('H');
								$hour_n = (int)$datetime->format('G');
		
								// COLUMNAS >= B
								for($column = 2; $column <= ($ultima_columna_lat_n); $column++){
								
									$letra_columna = PHPExcel_Cell::stringFromColumnIndex($column - 1);
									$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
		
									//$letra_columna_latlong = PHPExcel_Cell::stringFromColumnIndex($column - 1);
									$valor_columna_lat = $worksheet->getCell($letra_columna.'1')->getValue();
									$valor_columna_long = $worksheet->getCell($letra_columna.'2')->getValue();
		
									$array_datos[$date][$valor_columna_lat.":".$valor_columna_long][(string)$hour] = $valor_columna;
		
								}
								
							}
		
		
							// ARMAR ARRAY FINAL
							foreach ($array_datos as $fecha => $latlon) {
								
								foreach($latlon as $latylong => $horas){
									
									$coordenadas = explode(':', $latylong);
									$latitud = $coordenadas[0];
									$longitud = $coordenadas[1];
		
									$array_row = array();
									$array_row["id_client"] = $id_cliente;
									$array_row["id_project"] = $id_proyecto;
									$array_row["id_record"] = $air_record->id;
									$array_row["id_variable"] = $id_variable;
									$array_row["id_upload"] = $save_id_air_record_values_upload;
									$array_row["latitude"] = $latitud;
									$array_row["longitude"] = $longitud;
									$array_row["date"] = $fecha;
		
									// Se fuerza el seteo de todos los campos hora para el insert 
									for($hora = 0; $hora <= 23; $hora++){
										$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
										$array_row[$time_field] = null;
									}
		
									foreach ($horas as $hora => $valor) {
										$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
									}
									
									$array_insert[] = $array_row;
								}
		
							}

						}	

						$bulk_load_map = $this->Air_records_values_p_model->bulk_load($array_insert);

					} else { // Datos 1D

						// $ultima_columna_lat = $worksheet->getHighestColumn(1);
						// $ultima_columna_lat_n = (PHPExcel_Cell::columnIndexFromString($ultima_columna_lat));

						$array_insert_min = array();
						$array_insert_max = array();
						$array_insert_porc_conf = array();

						// DATOS TABLA air_records_values_uploads
						$array_data_air_records_values_uploads = array(
							"upload_format" => $file_basename,
							"created" => get_current_utc_time(),
							"created_by" => $this->login_user->id
						);

						// CELDA FECHA CREACIÓN MODELO
						$fecha_excel = $worksheet->getCell('B1')->getValue();
						if($this->validateDate($fecha_excel, 'Y-m-d H:i')){
							
						}elseif($this->validateDate($fecha_excel, 'Y-m-d G:i')){
							$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
							$fecha_excel = $datetime->format('Y-m-d H:i');
						}elseif($this->validateDate($fecha_excel, 'Y-m-d H:i:s')){
							$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_excel);
							$fecha_excel = $datetime->format('Y-m-d H:i');
						}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i')){
							$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha_excel);
							$fecha_excel = $datetime->format('Y-m-d H:i');
						}elseif($this->validateDate($fecha_excel, 'd-m-Y G:i')){
							$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha_excel);
							$fecha_excel = $datetime->format('Y-m-d H:i');
						}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i:s')){
							$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha_excel);
							$fecha_excel = $datetime->format('Y-m-d H:i');
						}elseif(is_numeric($fecha_excel)){
							$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_excel);
							$fecha_excel = $datetime->format('Y-m-d H:i');
						}else{

						}
						//$fecha_excel_datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
						$array_data_air_records_values_uploads["model_creation_date"] = $fecha_excel;

						// REGISTRO DE PRONÓSTICO ASOCIADO AL CLIENTE, PROYECTO, SECTOR, MODELO, TIPO DE REGISTRO.
						$air_record = $this->Air_records_model->get_one_where(array(
							"id_client" => $id_cliente,
							"id_project" => $id_proyecto,
							"id_air_sector" => $id_sector,
							"id_air_station" => ($id_receptor == 0) ? NULL : $id_receptor,
							"id_air_model" => $id_model,
							"id_air_record_type" => $id_record_type,
							"deleted" => 0
						));

						$array_data_air_records_values_uploads["id_record"] = $air_record->id;
						$save_id_air_record_values_upload = $this->Air_records_values_uploads_model->save($array_data_air_records_values_uploads);


						// FILA >= 2 - FECHA Y DATOS
						$array_ids_values_uploads = array();
						$array_datos = array();
						$array_datos_min = array();
						$array_datos_max = array();
						$array_datos_porc_conf = array();

						for($row = 2; $row <= $lastRow; $row++){
							
							// CELDA FECHA
							$fecha_excel = $worksheet->getCell('A'.$row)->getValue();

							/*if(is_numeric($fecha_excel)){
								$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_excel);
							}else{
								$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
							}*/

							if($this->validateDate($fecha_excel, 'Y-m-d H:i')){
								$datetime = DateTime::createFromFormat('Y-m-d H:i', $fecha_excel);
							}elseif($this->validateDate($fecha_excel, 'Y-m-d G:i')){
								$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
								//$fecha_excel = $datetime->format('Y-m-d H:i');
							}elseif($this->validateDate($fecha_excel, 'Y-m-d H:i:s')){
								$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_excel);
								//$fecha_excel = $datetime->format('Y-m-d H:i');
							}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i')){
								$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha_excel);
								//$fecha_excel = $datetime->format('Y-m-d H:i');
							}elseif($this->validateDate($fecha_excel, 'd-m-Y G:i')){
								$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha_excel);
								//$fecha_excel = $datetime->format('Y-m-d H:i');
							}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i:s')){
								$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha_excel);
								//$fecha_excel = $datetime->format('Y-m-d H:i');
							}elseif(is_numeric($fecha_excel)){
								$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_excel);
								//$fecha_excel = $datetime->format('Y-m-d H:i');
							}else{
				
							}

							$date = $datetime->format('Y-m-d');
							$time = $datetime->format('H');

							// CELDA VALOR
							$valor_columna = $worksheet->getCell('B'.$row)->getValue();
							$array_datos[$date][$time] = $valor_columna;

							$valor_columna_min = $worksheet->getCell('C'.$row)->getValue();
							if($valor_columna_min){
								$array_datos_min[$date][$time] = $valor_columna_min;
							}
							
							$valor_columna_max = $worksheet->getCell('D'.$row)->getValue();
							if($valor_columna_max){
								$array_datos_max[$date][$time] = $valor_columna_max;
							}

							$valor_columna_porc_conf = $worksheet->getCell('E'.$row)->getValue();
							if($valor_columna_porc_conf){
								$array_datos_porc_conf[$date][$time] = $valor_columna_porc_conf;
							}

						}

						// ARMAR ARRAY FINAL
						foreach ($array_datos as $fecha => $horas) {

							$array_row = array();
							$array_row["id_client"] = $id_cliente;
							$array_row["id_project"] = $id_proyecto;
							$array_row["id_record"] = $air_record->id;
							$array_row["id_variable"] = $id_variable;
							$array_row["id_upload"] = $save_id_air_record_values_upload;
							//$array_row["latitude"] = $latitud;
							//$array_row["longitude"] = $longitud;
							$array_row["date"] = $fecha;

							// Se fuerza el seteo de todos los campos hora para el insert 
							for($hora = 0; $hora <= 23; $hora++){
								$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
								$array_row[$time_field] = null;
							}

							foreach($horas as $hora => $valor){
								$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
							}

							//$array_insert[] = $array_row;
							$save_id = $this->Air_records_values_p_model->save($array_row);
							// $array_ids_values_uploads[] = $save_id;
							$array_ids_values_uploads[] = array(
								"id_values_p" => $save_id,
								"id_upload" => $save_id_air_record_values_upload
							);

						}

						//$bulk_load = $this->Air_records_values_p_model->bulk_load($array_insert);


						// SI LA VARIABLE ES PM10, SE INGRESAN LAS COLUMNAS DE INTERVALO DE CONFIANZA Y LA DEL PORCENTAJE DE CONFIABILIDAD
						if($id_variable == 9){

							$index_save_id = 0;
							foreach ($array_datos_min as $fecha => $horas) {

								$array_row = array();
								$array_row["id_values_p"] = $array_ids_values_uploads[$index_save_id]["id_values_p"];
								$array_row["id_upload"] = $array_ids_values_uploads[$index_save_id]["id_upload"];

								// Se fuerza el seteo de todos los campos hora para el insert 
								for($hora = 0; $hora <= 23; $hora++){
									$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
									$array_row[$time_field] = null;
								}

								foreach($horas as $hora => $valor){
									$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
								}

								$array_insert_min[] = $array_row;
								$index_save_id++;
							}

							if(count($array_insert_min)){
								$bulk_load = $this->Air_records_values_p_min_model->bulk_load($array_insert_min);
							}

							$index_save_id = 0;
							foreach ($array_datos_max as $fecha => $horas) {

								$array_row = array();
								$array_row["id_values_p"] = $array_ids_values_uploads[$index_save_id]["id_values_p"];
								$array_row["id_upload"] = $array_ids_values_uploads[$index_save_id]["id_upload"];

								// Se fuerza el seteo de todos los campos hora para el insert 
								for($hora = 0; $hora <= 23; $hora++){
									$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
									$array_row[$time_field] = null;
								}

								foreach($horas as $hora => $valor){
									$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
								}

								$array_insert_max[] = $array_row;
								$index_save_id++;
							}

							if(count($array_insert_max)){
								$bulk_load = $this->Air_records_values_p_max_model->bulk_load($array_insert_max);
							}

							$index_save_id = 0;
							foreach ($array_datos_porc_conf as $fecha => $horas) {

								$array_row = array();
								$array_row["id_values_p"] = $array_ids_values_uploads[$index_save_id]["id_values_p"];
								$array_row["id_upload"] = $array_ids_values_uploads[$index_save_id]["id_upload"];

								// Se fuerza el seteo de todos los campos hora para el insert 
								for($hora = 0; $hora <= 23; $hora++){
									$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
									$array_row[$time_field] = null;
								}

								foreach($horas as $hora => $valor){
									$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
								}

								$array_insert_porc_conf[] = $array_row;
								$index_save_id++;
							}

							if(count($array_insert_porc_conf)){
								$bulk_load = $this->Air_records_values_p_porc_conf_model->bulk_load($array_insert_porc_conf);
							}
							
						}

					}

				}

			}

		} // END FOREACH $array_bulk_load_data

		// if($bulk_load){
		// 	echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		// }else{
		// 	echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		// }

		if($bulk_load_map || $save_id){
			return true;
		} else {
			return false;
		}
		// return $save_id;

	}
	

	// function bulk_load_monitoring($array_bulk_load_data = array(), $archivo_subido){

	// 	$file_ext = pathinfo("files/carga_masiva/".$archivo_subido, PATHINFO_EXTENSION);
	// 	$file_basename = basename("files/carga_masiva/".$archivo_subido,".".$file_ext);
	// 	//$load_code = substr($file_basename, strpos($file_basename, "_") + 1); // CÓDIGO QUE IDENTIFICA UNA ESTACIÓN

	// 	$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
	// 	$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);

	// 	foreach($array_bulk_load_data as $array){
			
	// 		//$this->load->library('excel');
	// 		$id_cliente = $array['id_cliente'];
	// 		$id_proyecto = $array['id_proyecto'];
	// 		$id_record_type = $array['id_record_type']; 
	// 		// $id_model = $array['id_model'];
	// 		$id_sector = $array['id_sector'];
	// 		$id_receptor = $array['id_receptor'];
	// 		$id_variable = $array['id_variable'];
	// 		$id_tipo_variable = $array['id_tipo_variable'];
	// 		$archivo_subido = $array['archivo_subido'];

	// 		foreach($excelObj->getAllSheets() as $worksheet) {

	// 			$station = $this->Air_stations_model->get_one_where(array(
	// 				"load_code" => $worksheet->getTitle(),
	// 				"deleted" => 0
	// 			));

	// 			if($station->id == $id_receptor){

	// 				//$worksheet = $excelObj->getSheet(0);
	// 				$lastRow = $worksheet->getHighestRow();

	// 				$array_insert = array();

	// 				// DATOS TABLA air_records_values_uploads_m
	// 				$array_data_air_records_values_uploads = array(
	// 					"id_station" => $station->id,
	// 					"upload_format" => $file_basename,
	// 					"created" => get_current_utc_time(),
	// 					"created_by" => $this->login_user->id
	// 				);

	// 				// CELDA FECHA CREACIÓN MODELO
	// 				$fecha_excel = $worksheet->getCell('B1')->getValue();
	// 				if($this->validateDate($fecha_excel, 'Y-m-d H:i')){
						
	// 				}elseif($this->validateDate($fecha_excel, 'Y-m-d G:i')){
	// 					$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
	// 					$fecha_excel = $datetime->format('Y-m-d H:i');
	// 				}elseif($this->validateDate($fecha_excel, 'Y-m-d H:i:s')){
	// 					$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_excel);
	// 					$fecha_excel = $datetime->format('Y-m-d H:i');
	// 				}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i')){
	// 					$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha_excel);
	// 					$fecha_excel = $datetime->format('Y-m-d H:i');
	// 				}elseif($this->validateDate($fecha_excel, 'd-m-Y G:i')){
	// 					$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha_excel);
	// 					$fecha_excel = $datetime->format('Y-m-d H:i');
	// 				}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i:s')){
	// 					$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha_excel);
	// 					$fecha_excel = $datetime->format('Y-m-d H:i');
	// 				}elseif(is_numeric($fecha_excel)){
	// 					$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_excel);
	// 					$fecha_excel = $datetime->format('Y-m-d H:i');
	// 				}else{

	// 				}

	// 				$array_data_air_records_values_uploads["model_creation_date"] = $fecha_excel;
	// 				$save_id_air_record_values_upload = $this->Air_records_values_uploads_m_model->save($array_data_air_records_values_uploads);

	// 				// FILA >= 2 - FECHA Y DATO
	// 				$array_datos = array();
	// 				for($row = 2; $row <= $lastRow; $row++){
						
	// 					// CELDA FECHA
	// 					$fecha_excel = $worksheet->getCell('A'.$row)->getValue();

	// 					/*if(is_numeric($fecha_excel)){
	// 						$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_excel);
	// 					}else{
	// 						$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
	// 					}*/

	// 					if($this->validateDate($fecha_excel, 'Y-m-d H:i')){
	// 						$datetime = DateTime::createFromFormat('Y-m-d H:i', $fecha_excel);
	// 					}elseif($this->validateDate($fecha_excel, 'Y-m-d G:i')){
	// 						$datetime = DateTime::createFromFormat('Y-m-d G:i', $fecha_excel);
	// 						//$fecha_excel = $datetime->format('Y-m-d H:i');
	// 					}elseif($this->validateDate($fecha_excel, 'Y-m-d H:i:s')){
	// 						$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_excel);
	// 						//$fecha_excel = $datetime->format('Y-m-d H:i');
	// 					}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i')){
	// 						$datetime = DateTime::createFromFormat('d-m-Y H:i', $fecha_excel);
	// 						//$fecha_excel = $datetime->format('Y-m-d H:i');
	// 					}elseif($this->validateDate($fecha_excel, 'd-m-Y G:i')){
	// 						$datetime = DateTime::createFromFormat('d-m-Y G:i', $fecha_excel);
	// 						//$fecha_excel = $datetime->format('Y-m-d H:i');
	// 					}elseif($this->validateDate($fecha_excel, 'd-m-Y H:i:s')){
	// 						$datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecha_excel);
	// 						//$fecha_excel = $datetime->format('Y-m-d H:i');
	// 					}elseif(is_numeric($fecha_excel)){
	// 						$datetime = PHPExcel_Shared_Date::ExcelToPHPObject($fecha_excel);
	// 						//$fecha_excel = $datetime->format('Y-m-d H:i');
	// 					}else{
			
	// 					}

	// 					$date = $datetime->format('Y-m-d');
	// 					$time = $datetime->format('H');

	// 					// CELDA VALOR
	// 					$valor_columna = $worksheet->getCell('B'.$row)->getValue();

	// 					$array_datos[$date][$time] = $valor_columna;

	// 				}

	// 				// ARMAR ARRAY FINAL
	// 				foreach ($array_datos as $fecha => $horas) {

	// 					$array_row = array();
	// 					$array_row["id_client"] = $id_cliente;
	// 					$array_row["id_project"] = $id_proyecto;
	// 					$array_row["id_station"] = $station->id;
	// 					$array_row["id_variable"] = $id_variable;
	// 					$array_row["id_upload"] = $save_id_air_record_values_upload;
	// 					//$array_row["latitude"] = $latitud;
	// 					//$array_row["longitude"] = $longitud;
	// 					$array_row["date"] = $fecha;

	// 					// Se fuerza el seteo de todos los campos hora para el insert 
	// 					for($hora = 0; $hora <= 23; $hora++){
	// 						$time_field = ($hora < 10) ? "time_0".$hora : "time_".$hora;
	// 						$array_row[$time_field] = null;
	// 					}

	// 					foreach($horas as $hora => $valor){
	// 						$array_row["time_".$hora] = (string)number_format($valor, 10, '.', '');
	// 					}

	// 					$array_insert[] = $array_row;

	// 				}

	// 				$bulk_load = $this->Air_records_values_m_model->bulk_load($array_insert);

	// 			}

	// 		}

	// 	}

	// 	return $bulk_load;

	// }


	/**
	 * no se usa
	 * @ignore
	 */
	function create_client_folder($client_id) {
		if(!file_exists(__DIR__.'/../../files/client_'.$client_id)) {
			if(mkdir(__DIR__.'/../../files/client_'.$client_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
	}

    /**
	 * no se usa
	 * @ignore
	 */
    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            redirect("forbidden");
        }
        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->file_name)));

        download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /**
	 * upload_file
	 * 
	 * Ejecuta el método helper upload_file_to_temp() para guardar el archivo subido
	 * por el Usuario en el directorio de archivos temporales (/files/temp) del proyecto
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @return void
	 */
    function upload_file() {
        upload_file_to_temp();
    }

    /**
	 * validate_file
	 * 
	 * Valida que archivo a subir por el Usuario al FTP, tenga la extensión xlsx (Excel)
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @return JSON Con un mensaje de éxito o error según sea el caso.
	 */
    function validate_file() {
		
		$file_name = $this->input->post("file_name");
		
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'xlsx' || $file_ext == 'csv') {
			echo json_encode(array("success" => true));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		
    }

    /**
	 * no se usa
	 * @ignore
	 */
    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            redirect("forbidden");
        }

        if ($this->General_files_model->delete($id)) {

            delete_file_from_directory(get_general_file_path("client", $info->client_id) . $info->file_name);

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }
	
	/**
	 * get_intructions
	 * 
	 * Carga instrucciones de descarga y subida de datos a través de una plantilla Excel de Carga Masiva.
	 * Se utiliza en la vista principal del módulo, y se consulta via Ajax al momento de ingresar al módulo.
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @return HTML Con el contenido de las instrucciones
	 */
    function get_intructions() {
        //$this->access_only_allowed_members();
		$html = $this->load->view('setting_bulk_load/intructions', $view_data, true);
		echo $html;
    }

	function get_file_field_for_bulk_load(){

		$id_record_type = $this->input->post("id_record_type");

		// SI EL TIPO DE REGISTRO ES PRONÓSTICO, TRAER CAMPO ARCHIVOS MÚLTIPLES
		if($id_record_type == 2){

			$html = $this->load->view("includes/multiple_files_uploader", array(
                "upload_url" =>get_uri("air_setting_bulk_load/upload_file"),
                "validation_url" =>get_uri("air_setting_bulk_load/validate_file"),
                "html_name" => "archivo_importado",
                //"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
                "obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required").'"',
                //"obligatorio" => "",
                "id_campo" => "archivo_importado"
                
            ), true);

		} else { // TRAER CAMPO ARCHIVO SIMPLE

			$html = $this->load->view("includes/bulk_file_uploader", array(
				"upload_url" => get_uri("air_setting_bulk_load/upload_file"),
				"validation_url" =>get_uri("air_setting_bulk_load/validate_file"),
				//"html_name" => 'test',
				//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
			), true);

		}

		echo $html;
	}

	function upload_multiple_file($file_type = "") {

		$id_campo = $this->input->post("cid");
		//$number = uniqid();
		
		if($id_campo){
			upload_file_to_temp("file", array("id_campo" => $id_campo));
		}else {
			upload_file_to_temp();
		}
		/*
		if($id_campo){
			upload_file_to_temp("file", array("id_campo" => $id_campo. "_" . $number));
		}else {
			upload_file_to_temp();
		}
		*/
		
	}

}
