<?php
/**
 * Archivo Controlador para Descarga Masiva MIMAire (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Descarga Masiva
 * @author Álvaro Donoso
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Descarga Masiva MIMAire (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Descarga Masiva
 * @property private $id_modulo_cliente id del módulo Administración Cliente Mimaire (15)
 * @property private $id_submodulo_cliente id del submódulo Descarga Masiva MIMAire (29)
 * @author Álvaro Donoso
 * @version 1.0
 */
class Air_setting_bulk_download extends MY_Controller {
	
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
		$this->id_submodulo_cliente = 29;
		
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
			if($tipo_registro->id == 3) continue;
			$array_tipos_registros[$tipo_registro->id] = lang($tipo_registro->name);
		}
		
		$view_data["tipos_de_registros"] = $array_tipos_registros;
		$view_data["project_info"] = $proyecto;
		
		//Configuración perfil de usuario
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_editar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");

		### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_bulk_download');

		
        $this->template->rander("air_setting_bulk_download/index", $view_data);
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

			if($id_record_type == 2){
				$modelos = $this->Air_models_model->get_all()->result();
				foreach($modelos as $modelo){
					$array_modelos[] = array("id" => $modelo->id, "text" => lang($modelo->name));
				}
			}
		}
        
        echo json_encode($array_modelos);
		
	}
	
	
	
	/**
	 * get_data
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
	function get_data() {
		ini_set('memory_limit', '-1'); 
		/*ini_set('memory_limit', '1024M'); 
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);*/
		//phpinfo(INFO_MODULES);exit;

		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$id_record_type = $this->input->post('id_record_type');

		if($id_record_type == "2"){ // Pronóstico

			$info_cliente = $this->Clients_model->get_one($id_cliente);
			$info_proyecto = $this->Projects_model->get_one($id_proyecto);
					
			if(!$info_cliente->id && !$info_proyecto->id) {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
				exit();
			}

			if (!$this->login_user->id) {
				redirect("forbidden");
			}

			// CREO EL CSV EN CARPETA TEMP DE MIMASOFT
			$nombre = $info_cliente->sigla.'_data.csv';
			$tmp = get_setting("temp_file_path");
			$df = fopen(getcwd() . '/' . $tmp.$nombre, 'w');
			fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));

			// HEADER
			$header = array();
			$header[] = 'Proyecto';
			$header[] = 'Fecha de  creación del modelo';
			$header[] = 'Modelo';
			$header[] = 'Estación';
			$header[] = 'Variable';
			$header[] = 'Sigla variable';
			$header[] = 'Latitud';
			$header[] = 'Longitud';
			$header[] = 'Fecha';
			$header[] = 'time_00';
			$header[] = 'time_01';
			$header[] = 'time_02';
			$header[] = 'time_03';
			$header[] = 'time_04';
			$header[] = 'time_05';
			$header[] = 'time_06';
			$header[] = 'time_07';
			$header[] = 'time_08';
			$header[] = 'time_09';
			$header[] = 'time_10';
			$header[] = 'time_11';
			$header[] = 'time_12';
			$header[] = 'time_13';
			$header[] = 'time_14';
			$header[] = 'time_15';
			$header[] = 'time_16';
			$header[] = 'time_17';
			$header[] = 'time_18';
			$header[] = 'time_19';
			$header[] = 'time_20';
			$header[] = 'time_21';
			$header[] = 'time_22';
			$header[] = 'time_23';

			fputcsv($df, $header, ";");

			//CONTENT
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto
			);
			$list_data = $this->Air_records_model->get_data($options)->result_array();
			foreach($list_data as $row){
				$content = array();
				$content[] = $row["project_name"];
				$content[] = $row["model_creation_date"];
				$content[] = lang($row["model"]);
				$content[] = $row["station"];
				$content[] = $row["variable"];
				$content[] = $row["variable_initials"];
				$content[] = $row["latitude"];
				$content[] = $row["longitude"];
				$content[] = $row["date"];
				$content[] = $row["time_00"];
				$content[] = $row["time_01"];
				$content[] = $row["time_02"];
				$content[] = $row["time_03"];
				$content[] = $row["time_04"];
				$content[] = $row["time_05"];
				$content[] = $row["time_06"];
				$content[] = $row["time_07"];
				$content[] = $row["time_08"];
				$content[] = $row["time_09"];
				$content[] = $row["time_10"];
				$content[] = $row["time_11"];
				$content[] = $row["time_12"];
				$content[] = $row["time_13"];
				$content[] = $row["time_14"];
				$content[] = $row["time_15"];
				$content[] = $row["time_16"];
				$content[] = $row["time_17"];
				$content[] = $row["time_18"];
				$content[] = $row["time_19"];
				$content[] = $row["time_20"];
				$content[] = $row["time_21"];
				$content[] = $row["time_22"];
				$content[] = $row["time_23"];
				
				fputcsv($df, $content, ";");
			}

			fclose($df);

			// ARCHIVO CON DATOS SINÓPTICOS
			// CREO EL CSV EN CARPETA TEMP DE MIMASOFT
			$nombre_pmca = $info_cliente->sigla.'_pmca.csv';
			$tmp = get_setting("temp_file_path");
			$df_pmca = fopen(getcwd() . '/' . $tmp.$nombre_pmca, 'w');
			fprintf($df_pmca, chr(0xEF).chr(0xBB).chr(0xBF));

			// HEADER
			$header_pmca = array();
			$header_pmca[] = lang('date');

			$header_pmca[] = lang('pmca_24_hrs_t1');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_24_hrs_t2');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_24_hrs_t3');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_48_hrs_t1');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_48_hrs_t2');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_48_hrs_t3');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_72_hrs_t1');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_72_hrs_t2');
			$header_pmca[] = lang('probability');
			
			$header_pmca[] = lang('pmca_72_hrs_t3');
			$header_pmca[] = lang('probability');

			$header_pmca[] = lang('backup_document');
			$header_pmca[] = lang('observations');
			$header_pmca[] = lang('created_date');
			$header_pmca[] = lang('modified_date');
			fputcsv($df_pmca, $header_pmca, ";");

			//CONTENT
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto
			);
			$list_data = $this->Air_synoptic_data_model->get_details($options)->result_array();
			
			foreach($list_data as $row){
				$pmca_24_hrs_t1 = json_decode($row['pmca_24_hrs_t1']);
				$pmca_24_hrs_t2 = json_decode($row['pmca_24_hrs_t2']);
				$pmca_24_hrs_t3 = json_decode($row['pmca_24_hrs_t3']);
				$pmca_48_hrs_t1 = json_decode($row['pmca_48_hrs_t1']);
				$pmca_48_hrs_t2 = json_decode($row['pmca_48_hrs_t2']);
				$pmca_48_hrs_t3 = json_decode($row['pmca_48_hrs_t3']);
				$pmca_72_hrs_t1 = json_decode($row['pmca_72_hrs_t1']);
				$pmca_72_hrs_t2 = json_decode($row['pmca_72_hrs_t2']);
				$pmca_72_hrs_t3 = json_decode($row['pmca_72_hrs_t3']);


				$content_pmca = array();
				$content_pmca[] = $row['date'] ? $row['date'] : '-';
				
				$content_pmca[] = $pmca_24_hrs_t1->value ? $pmca_24_hrs_t1->value : '-';
				$content_pmca[] = $pmca_24_hrs_t1->percentage ? $pmca_24_hrs_t1->percentage : '-';
				$content_pmca[] = $pmca_24_hrs_t2->value ? $pmca_24_hrs_t2->value : '-';
				$content_pmca[] = $pmca_24_hrs_t2->percentage ? $pmca_24_hrs_t2->percentage : '-';
				$content_pmca[] = $pmca_24_hrs_t3->value ? $pmca_24_hrs_t3->value : '-';
				$content_pmca[] = $pmca_24_hrs_t3->percentage ? $pmca_24_hrs_t3->percentage : '-';

				$content_pmca[] = $pmca_48_hrs_t1->value ? $pmca_48_hrs_t1->value : '-';
				$content_pmca[] = $pmca_48_hrs_t1->percentage ? $pmca_48_hrs_t1->percentage : '-';
				$content_pmca[] = $pmca_48_hrs_t2->value ? $pmca_48_hrs_t2->value : '-';
				$content_pmca[] = $pmca_48_hrs_t2->percentage ? $pmca_48_hrs_t2->percentage : '-';
				$content_pmca[] = $pmca_48_hrs_t3->value ? $pmca_48_hrs_t3->value : '-';
				$content_pmca[] = $pmca_48_hrs_t3->percentage ? $pmca_48_hrs_t3->percentage : '-';

				$content_pmca[] = $pmca_72_hrs_t1->value ? $pmca_72_hrs_t1->value : '-';
				$content_pmca[] = $pmca_72_hrs_t1->percentage ? $pmca_72_hrs_t1->percentage : '-';
				$content_pmca[] = $pmca_72_hrs_t2->value ? $pmca_72_hrs_t2->value : '-';
				$content_pmca[] = $pmca_72_hrs_t2->percentage ? $pmca_72_hrs_t2->percentage : '-';
				$content_pmca[] = $pmca_72_hrs_t3->value ? $pmca_72_hrs_t3->value : '-';
				$content_pmca[] = $pmca_72_hrs_t3->percentage ? $pmca_72_hrs_t3->percentage : '-';

				$content_pmca[] = $row['evidence_file'] ? remove_file_prefix($row['evidence_file']) : '-';
				$content_pmca[] = $row['observations'] ? $row['observations'] : '-';
				$content_pmca[] = $row['created'] ? $row['created'] : '-';
				$content_pmca[] = $row['modified'] ? $row['modified'] : '-';

				fputcsv($df_pmca, $content_pmca, ";");		
			}
		
			fclose($df_pmca);

			$array_files[] = array("file_name" => $nombre);
			$array_files[] = array("file_name" => $nombre_pmca);
			$file_data = serialize($array_files);
			download_app_files($tmp, $file_data, false, $nombre);

			echo json_encode(array("success" => true, 'message' => "test", 'name' => $nombre));
			exit();

		}
		
    }

	// /**
	//  * clean_data
	//  * 
	//  * Trunca las tabla de uploads y valores de pronosticos cargados masivamente.
	//  * Con el fin de optimizar los tiempos de consulta de los demos modulos.
	//  *
	//  * @author Álvaro Donoso
	//  * @access public
	//  * @return HTML link de descarga de plantilla Excel
	//  */
	// function clean_data() {

	// 	ini_set('memory_limit', '-1'); 
	// 	$id_record_type = $this->input->post('id_record_type');

	// 	// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
	// 	$today_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
	// 	$yesterday_datetime = new DateTime($today_datetime);
	// 	$yesterday_datetime->setTime(0,0,0);
	// 	$yesterday_datetime = $yesterday_datetime->modify('-24 hours');
	// 	$yesterday_datetime = $yesterday_datetime->format("Y-m-d H:i");

	// 	$yesterday_date = date("Y-m-d", strtotime($yesterday_datetime));

	// 	if($id_record_type == "2"){ // Pronóstico

	// 		$air_records_values_uploads_rows = $this->Air_records_values_uploads_model->get_all()->num_rows();
	// 		$air_records_values_p_rows = $this->Air_records_values_p_model->get_all()->num_rows();

	// 		// SI HAY REGISTROS
	// 		if($air_records_values_uploads_rows || $air_records_values_p_rows){

	// 			$filename_backup = "air_records_values_uploads";
	// 			$tables = array("air_records_values_uploads");
	// 			$backup_air_records_values_uploads = $this->generate_backup($tables, $filename_backup);
		
	// 			$filename_backup = "air_records_values_p";
	// 			$tables = array("air_records_values_p");
	// 			$backup_air_records_values_p = $this->generate_backup($tables, $filename_backup);
		
	// 			if($backup_air_records_values_uploads && $backup_air_records_values_p){ // SI AMBAS TABLAS ESTÁN RESPALDADAS OK

	// 				$array_ids_last_values_uploads = array();
	// 				$array_insert_last_values_uploads = array();
	// 				$array_insert_last_values_p = array();
	// 				$variables = $this->Air_variables_model->get_all()->result();
	// 				//$modelos = $this->Air_models_model->get_all()->result();
	// 				$new_id_upload = 1; // VARIABLE PARA INGRESAR ÚLTIMA CARGA DE DATOS CON NUEVOS ID

	// 				// ITERAR LAS VARIABLES
	// 				foreach($variables as $variable){

	// 					$last_values_p = $this->Air_records_values_p_model->get_last_upload_data_from_yesterday(array(
	// 						"id_variable" => $variable->id,
	// 						"yesterday_date" => $yesterday_date
	// 					))->result_array();
						
	// 					// SI LA VARIABLE TIENE DATOS
	// 					if(count($last_values_p)){

	// 						foreach($last_values_p as $last_value_p){

	// 							if(!in_array($last_value_p["id_upload"], $array_ids_last_values_uploads)){
									
	// 								array_push($array_ids_last_values_uploads, $last_value_p["id_upload"]);

	// 								$last_value_upload = $this->Air_records_values_uploads_model->get_one($last_value_p["id_upload"]);
	// 								// REGISTRO PARA TABLA air_records_values_uploads
	// 								$array_insert_last_values_uploads[] = array(
	// 									"id" => $new_id_upload,
	// 									//"id" => $last_value_upload->id,
	// 									"id_record" => $last_value_upload->id_record,
	// 									"model_creation_date" => $last_value_upload->model_creation_date,
	// 									"upload_format" => $last_value_upload->upload_format,
	// 									"created" => $last_value_upload->created,
	// 									"created_by" => $last_value_upload->created_by,
	// 									"deleted" => $last_value_upload->deleted
	// 								);

	// 								$prev_new_id_upload = $new_id_upload;
	// 								$new_id_upload++;	

	// 							}

	// 							unset($last_value_p["id"]);
	// 							$last_value_p["id_upload"] = $prev_new_id_upload;
	// 							$array_insert_last_values_p[] = $last_value_p;

	// 						}

	// 					}

	// 				}

					
	// 				$truncate_values_uploads = $this->Air_records_values_uploads_model->truncate();
	// 				$truncate_values_p = $this->Air_records_values_p_model->truncate();

	// 				// SI AMBAS TABLAS SON TRUNCADAS
	// 				if($truncate_values_uploads && $truncate_values_p){

	// 					// GUARDA ÚLTIMA CARGA DE DATOS DE CADA VARIABLE CON NUEVOS ID
	// 					$bulk_load_values_uploads = $this->Air_records_values_uploads_model->bulk_load($array_insert_last_values_uploads);
	// 					$bulk_load_values_p = $this->Air_records_values_p_model->bulk_load($array_insert_last_values_p);

	// 					if($bulk_load_values_uploads && $bulk_load_values_p){
	// 						echo json_encode(array("success" => true, "message" => lang("forecast_data_clean_msj")));
	// 					} else {
	// 						echo json_encode(array("message" => lang("error_occurred")));
	// 					}

	// 				} else {
	// 					echo json_encode(array("message" => lang("error_occurred")));
	// 				}

	// 			} else {
	// 				echo json_encode(array("message" => lang("error_occurred")));
	// 			}

	// 		} else {
	// 			echo json_encode(array("message" => lang("no_forecast_data_clean_msj")));
	// 		}

	// 	} else {
	// 		echo json_encode(array("message" => lang("no_monitoring_data_clean_msj")));
	// 	}

	// }










	/**
	 * clean_data
	 * 
	 * Trunca las tabla de uploads y valores de pronosticos cargados masivamente.
	 * Con el fin de optimizar los tiempos de consulta de los demos modulos.
	 *
	 * @author Álvaro Donoso
	 * @access public
	 * @return HTML link de descarga de plantilla Excel
	 */
	function clean_data() {

		ini_set('memory_limit', '-1'); 
		$id_record_type = $this->input->post('id_record_type');

		// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
		$last_upload_date = $this->Air_records_values_uploads_model->get_first_id_to_delete()->row()->created;

		// $today_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$first_datetime = new DateTime($last_upload_date);
		$first_datetime->setTime(0,0,0);
		$first_datetime = $first_datetime->modify('-48 hours');
		$first_date = $first_datetime->format("Y-m-d");

		if($id_record_type == "2"){ // Pronóstico

			$air_records_values_uploads_rows = $this->Air_records_values_uploads_model->get_all()->num_rows();
			$air_records_values_p_rows = $this->Air_records_values_p_model->get_all()->num_rows();

			// SI HAY REGISTROS
			if($air_records_values_uploads_rows || $air_records_values_p_rows){

				// BUSCAR EL ÚLTIMO REGISTRO DE LA TABLA air_records_values_uploads EN DONDE EL CAMPO created SEA MENOR A $first_datetime Y OBTENER EL id
				$first_id_to_delete = $this->Air_records_values_uploads_model->get_first_id_to_delete(array("created" => $first_date))->row()->id;
				$delete_values_p_min =  $this->Air_records_values_p_min_model->delete_old_values_from_an_id_upload($first_id_to_delete);
				$delete_values_p_max =  $this->Air_records_values_p_max_model->delete_old_values_from_an_id_upload($first_id_to_delete);
				$delete_values_p_porc_conf =  $this->Air_records_values_p_porc_conf_model->delete_old_values_from_an_id_upload($first_id_to_delete);
				$delete_values_p = $this->Air_records_values_p_model->delete_old_values_from_an_id_upload($first_id_to_delete);
				$delete_values_uploads = $this->Air_records_values_uploads_model->delete_old_values_from_an_id($first_id_to_delete);

				// SI AMBAS TABLAS SON LIMPIADAS
				if($delete_values_p && $delete_values_uploads){

					// REINICIAR IDS
					$reset_ids_values_uploads = $this->Air_records_values_uploads_model->reset_ids();
					// $reset_ids_values_p = $this->Air_records_values_p_model->reset_ids();
					// $reset_ids_values_p_min = $this->Air_records_values_p_min_model->reset_ids();
					// $reset_ids_values_p_max = $this->Air_records_values_p_max_model->reset_ids();

					echo json_encode(array("success" => true, "message" => lang("forecast_data_clean_msj")));
				} else {
					echo json_encode(array("message" => lang("no_forecast_data_clean_msj")));
				}

			} else {
				echo json_encode(array("message" => lang("no_forecast_data_clean_msj")));
			}

		} else {
			echo json_encode(array("message" => lang("no_monitoring_data_clean_msj")));
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
		if ($file_ext == 'xlsx') {
			echo json_encode(array("success" => true));
		}else{
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

	/**
	 * borrar_temporal
	 * 
	 * Borra el archivo temporal indicado
	 * 
	 * @author Alvaro Cristobal Donoso Albornoz 
	 * @access public
	 * @uses $this->input->post('uri'); Ruta del archivo temporal a borrar.
	 */
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}

	function generate_backup($tables = array(), $filename_backup = "backup"){

		$this->load->dbutil();
		$prefixes = array(
			"tables" => $tables,
			"format" => "txt",
			//"filename" => "db_backup.sql",
			"add_drop" => false
		);
		$backup = $this->dbutil->backup($prefixes);

		$backups_path = 'files/backups/';
		$target_path = getcwd() . '/' . $backups_path;
		if (!is_dir($target_path)) {
			if (!mkdir($target_path, 0777, true)) {
				die('Failed to create file folders.');
			}
		}

		$filename_backup = $filename_backup."_".date("Ymd_His").'.sql';
		$save_file = $backups_path.$filename_backup;
		
		return write_file($save_file, $backup);

	}

}
