<?php
/**
 * Archivo Controlador para Estaciones (módulo de Administración)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Estaciones
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Estaciones (módulo de Administración)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Estaciones
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_stations extends MY_Controller {
	
	/**
	 * id_admin_module
	 * @var int $id_admin_module
	 */
	private $id_admin_module;
	/**
	 * id_admin_submodule
	 * @var int $id_admin_submodule
	 */
	private $id_admin_submodule;

	/**
	 * __construct
	 * 
	 * Constructor
	 */
    function __construct() {
        parent::__construct();
		
		$this->id_admin_module = 5; // Registros
		$this->id_admin_submodule = 44; // Estaciones

    	//check permission to access this module
        $this->init_permission_checker("client");
		$this->load->helper('directory');
    }

	/**
	 * index
	 * 
	 * Carga datos para los filtros de appTable de la vista principal del módulo
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @return resource Vista principal del módulo
	 */
    function index() {

        $this->access_only_allowed_members();
		
		// FILTRO CLIENTE		
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		$this->template->rander("air_stations/index", $view_data);
		
    }

	/**
	 * modal_form
	 * 
	 * Carga datos asociados a una Estación en la vista de modal de ingreso/edición
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id') id de la Estación
	 * @return resource Vista del contenido del modal de ingreso/edición de una Estación
	 */
    function modal_form() {

        $this->access_only_allowed_members();
        $id_air_station = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

		$model_info = $this->Air_stations_model->get_one($id_air_station);
		$view_data['model_info'] = $model_info;
		$view_data["clients_dropdown"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["projects_dropdown"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_client));
		$view_data["air_sectors_dropdown"] = array("" => "-") + $this->Air_sectors_model->get_dropdown_list(array("name"), "id", array("id_project" => $view_data['model_info']->id_project));
		$view_data["air_variables_multiselect_availables"] = $this->Air_variables_model->get_dropdown_list(array("name"), "id");

		if($id_air_station){
			
			$array_air_variables_multiselect_selected = array();
			$options_variables = array("id_air_station" => $id_air_station);
			$air_variables = $this->Air_stations_model->get_variables_of_station($options_variables)->result();
			foreach($air_variables as $air_variable){
				$array_air_variables_multiselect_selected[] = $air_variable->id_air_variable;
			}
			$view_data["air_variables_multiselect_selected"] = $array_air_variables_multiselect_selected;

		}

        $this->load->view('air_stations/modal_form', $view_data);
    }
	
	/**
	 * save
	 * 
	 * Guarda el ingreso/edición de una Estación en base de datos.
	 * En caso de recibir via POST el id de la Estación, se realiza un update
	 * al registro de la Estación asociada a ese id, de lo contrario, crea un nuevo registro.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id') id de la Estación
	 * @uses string $this->input->post('name') nombre de la Estación
	 * @uses int $this->input->post('client') id de Cliente asociado a la Estación
	 * @uses int $this->input->post('project') id de Proyecto asociado a la Estación
	 * @uses int $this->input->post('air_sector') id del Sector asociado a la Estación
	 * @uses boolean $this->input->post('tipo_estacion') Indica si la estación es Receptora (1) o no (0)
	 * @uses string $this->input->post('description') descripción de la Estación
	 * @uses string $this->input->post('latitude') latitud de la Estación
	 * @uses string $this->input->post('longitude') longitud de la Estación
	 * @uses int $this->login_user->id id del Usuario en Sesión
	 * @return JSON Con datos asociados al registro de la Estación ingresada/editada para actualizar 
	 * el appTable de la vista principal y mostrar mensaje de éxito o error en la operación
	 * 
	 */
	function save() {

		$id_air_station = $this->input->post('id');
		$name = $this->input->post('name');
		$id_client = $this->input->post('client');
		$id_project = $this->input->post('project');
		$id_air_sector = $this->input->post('air_sector');
		$is_receptor = (int)$this->input->post('tipo_estacion');
		$description = $this->input->post('description');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$load_code = $this->input->post('load_code');
		$load_code_api = $this->input->post('load_code_api');
		$air_variables = (array)$this->input->post('air_variables');

		/*
		// Validación: la Estación debe tener al menos una variable de tipo Meteorológica y otra de Calidad del aire
		$variable_met = FALSE;
		$variable_ca = FALSE;

		foreach($air_variables as $id_air_variable){
			$air_variable = $this->Air_variables_model->get_one($id_air_variable);
			if($air_variable->id_air_variable_type == 1){ // Meteorológica
				$variable_met = TRUE;
				break;
			}
		}
		foreach($air_variables as $id_air_variable){
			$air_variable = $this->Air_variables_model->get_one($id_air_variable);
			if($air_variable->id_air_variable_type == 2){ // Calidad del aire
				$variable_ca = TRUE;
			}
		}

		if(!$variable_met || !$variable_ca){
			echo json_encode(array("success" => false, "message" => lang("station_validation_msj")));
			exit();
		}
		*/

		if($this->Air_stations_model->is_load_code_exists($load_code, $id_air_station)){
			echo json_encode(array("success" => false, 'message' => lang('duplicate_load_code')));
			exit();
		}

		if($this->Air_stations_model->is_load_code_api_exists($load_code_api, $id_air_station)){
			echo json_encode(array("success" => false, 'message' => lang('duplicate_load_code_api')));
			exit();
		}
		
		$data = array(
			"id_client" => $id_client,
			"id_project" => $id_project,
			"id_air_sector" => $id_air_sector,
			"name" => $name,
			"is_receptor" => $is_receptor,
			"description" => $description,
			"latitude" => $latitude,
			"longitude" => $longitude,
			"load_code" => $load_code,
			"load_code_api" => $load_code_api

		);

		if($id_air_station){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}

		$save_id = $this->Air_stations_model->save($data, $id_air_station);
		
		if($id_air_station){ // Editar
			if(count($air_variables)){
				$air_station_rel_variable = $this->Air_stations_rel_variables_model->get_all_where(array(
					"id_air_station" => $id_air_station,
					"deleted" => 0
				))->result();
				foreach($air_station_rel_variable as $rel){
					$this->Air_stations_rel_variables_model->delete($rel->id);
				}
			}
		} 

		if(count($air_variables)){
			foreach($air_variables as $id_air_variable){
				$data_air_station_rel_variable = array(
					"id_air_station" => $save_id,
					"id_air_variable" => $id_air_variable
				);
				$save_air_station_rel_variable = $this->Air_stations_rel_variables_model->save($data_air_station_rel_variable);
			}
		}

		if ($save_id) {

			// Crear registros de MONITOREO y PRONÓSTICO asociados
			$air_sector_of_station = $this->Air_sectors_model->get_one($id_air_sector);
			$array_air_models_of_sector = json_decode($air_sector_of_station->air_models);

			// Crear registros por modelos seleccionados en el Sector (Machine Learning (id 1) y Neuronal (id 2))

			// Si el Sector de la Estación tiene el modelo Machine Learning (id 1), crear registros de MONITOREO y PRONÓSTICO asociados
			if(in_array(1, $array_air_models_of_sector)){

				$air_model = $this->Air_models_model->get_one(1); // Machine Learning
				$air_model_name = lang($air_model->name);

				$air_record_monitoring = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $id_air_sector,
					"id_air_station" => $id_air_station,
					"id_air_model" => 1, // Machine Learning
					"id_air_record_type" => 1, // Monitoreo
					"deleted" => 0
				));

				// Si el registro no existe, lo crea
				if(!$air_record_monitoring->id){

					// REGISTRO MONITOREO
					// Número: Es un correlativo, que se va calculando de acuerdo a la cantidad de registros por sector 
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $id_air_sector,
						"deleted" => 0
					))->result();

					if(count($air_records_of_sector) >= 1){
						$number = count($air_records_of_sector) + 1;
						$air_record_number = "0".$number;
					} else {
						$air_record_number = "01";
					}

					// Nombre: Compuesto por el nombre de la Estación, el Modelo y el Tipo de Registro. Ej: Nombre_Estación | Machine Learning - Monitoreo
					$air_record_type_monitoring = $this->Air_records_types_model->get_one(1); // Monitoreo
					$air_record_type_monitoring_name = lang($air_record_type_monitoring->name);

					// Código: Formado por el nombre de la Estación, el Sector, el número correlativo generado y el Tipo de Registro
					$air_record_code_name = str_replace(" ", "", $name);
					$air_record_code_number = $air_record_number;
					$air_record_code_record_type = 1; // Monitoreo
					$air_record_code_monitoring = strtoupper($air_record_code_name.$air_sector_of_station->name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record_monitoring = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $id_air_sector,
						"id_air_station" => $save_id,
						"id_air_model" => 1, // Machine Learning
						"id_air_record_type" => 1, // Monitoreo
						"number" => $air_record_number,
						"name" => $name." | ".$air_model_name." - ".$air_record_type_monitoring_name,
						"description" => $this->input->post('description'),
						"code" => $air_record_code_monitoring,
						"icon" => "steel.png"
					);

					$save_data_air_record = $this->Air_records_model->save($data_air_record_monitoring);

				}


				$air_record_forecast = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $id_air_sector,
					"id_air_station" => $id_air_station,
					"id_air_model" => 1, // Machine Learning
					"id_air_record_type" => 2, // Pronóstico
					"deleted" => 0
				));

				// Si el registro no existe, lo crea
				if(!$air_record_forecast->id){

					// REGISTRO PRONÓSTICO
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $id_air_sector,
						"deleted" => 0
					))->result();

					if(count($air_records_of_sector) >= 1){
						$number = count($air_records_of_sector) + 1;
						$air_record_number = "0".$number;
					} else {
						$air_record_number = "01";
					}

					$air_record_type_pronostic = $this->Air_records_types_model->get_one(2); // Pronóstico
					$air_record_type_pronostic_name = lang($air_record_type_pronostic->name);

					$air_record_code_name = str_replace(" ", "", $name);
					$air_record_code_number = $air_record_number;
					$air_record_code_record_type = 2; // Pronóstico
					$air_record_code_pronostic = strtoupper($air_record_code_name.$air_sector_of_station->name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $id_air_sector,
						"id_air_station" => $save_id,
						"id_air_model" => 1, // Machine Learning
						"id_air_record_type" => 2, // Pronóstico
						"number" => $air_record_number,
						"name" => $name." | ".$air_model_name." - ".$air_record_type_pronostic_name,
						"description" => $this->input->post('description'),
						"code" => $air_record_code_pronostic,
						"icon" => "steel.png"
					);

					$save_data_air_record = $this->Air_records_model->save($data_air_record);

				}

			}

			// Si el Sector de la Estación tiene el modelo Neuronal (id 2), crear registros de MONITOREO y PRONÓSTICO asociados
			if(in_array(2, $array_air_models_of_sector)){

				$air_model = $this->Air_models_model->get_one(2); // Neuronal
				$air_model_name = lang($air_model->name);

				$air_record_monitoring = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $id_air_sector,
					"id_air_station" => $id_air_station,
					"id_air_model" => 2, // Neuronal
					"id_air_record_type" => 1, // Monitoreo
					"deleted" => 0
				));

				// Si el registro no existe, lo crea
				if(!$air_record_monitoring->id){

					// REGISTRO MONITOREO
					// Número: Es un correlativo, que se va calculando de acuerdo a la cantidad de registros por sector 
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $id_air_sector,
						"deleted" => 0
					))->result();

					if(count($air_records_of_sector) >= 1){
						$number = count($air_records_of_sector) + 1;
						$air_record_number = "0".$number;
					} else {
						$air_record_number = "01";
					}

					// Nombre: Compuesto por el nombre de la Estación, el Modelo y el Tipo de Registro. Ej: Nombre_Estación | Machine Learning - Monitoreo
					$air_record_type_monitoring = $this->Air_records_types_model->get_one(1); // Monitoreo
					$air_record_type_monitoring_name = lang($air_record_type_monitoring->name);

					// Código: Formado por el nombre de la Estación, el Sector, el número correlativo generado y el Tipo de Registro
					$air_record_code_name = str_replace(" ", "", $name);
					$air_record_code_number = $air_record_number;
					$air_record_code_record_type = 1; // Monitoreo
					$air_record_code_monitoring = strtoupper($air_record_code_name.$air_sector_of_station->name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record_monitoring = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $id_air_sector,
						"id_air_station" => $save_id,
						"id_air_model" => 2, // Neuronal
						"id_air_record_type" => 1, // Monitoreo
						"number" => $air_record_number,
						"name" => $name." | ".$air_model_name." - ".$air_record_type_monitoring_name,
						"description" => $this->input->post('description'),
						"code" => $air_record_code_monitoring,
						"icon" => "steel.png"
					);

					$save_data_air_record = $this->Air_records_model->save($data_air_record_monitoring);

				}

				
				$air_record_forecast = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $id_air_sector,
					"id_air_station" => $id_air_station,
					"id_air_model" => 2, // Neuronal
					"id_air_record_type" => 2, // Pronóstico
					"deleted" => 0
				));

				// Si el registro no existe, lo crea
				if(!$air_record_forecast->id){

					// REGISTRO PRONÓSTICO
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $id_air_sector,
						"deleted" => 0
					))->result();

					if(count($air_records_of_sector) >= 1){
						$number = count($air_records_of_sector) + 1;
						$air_record_number = "0".$number;
					} else {
						$air_record_number = "01";
					}

					$air_record_type_pronostic = $this->Air_records_types_model->get_one(2); // Pronóstico
					$air_record_type_pronostic_name = lang($air_record_type_pronostic->name);

					$air_record_code_name = str_replace(" ", "", $name);
					$air_record_code_number = $air_record_number;
					$air_record_code_record_type = 2; // Pronóstico
					$air_record_code_pronostic = strtoupper($air_record_code_name.$air_sector_of_station->name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $id_air_sector,
						"id_air_station" => $save_id,
						"id_air_model" => 2, // Neuronal
						"id_air_record_type" => 2, // Pronóstico
						"number" => $air_record_number,
						"name" => $name." | ".$air_model_name." - ".$air_record_type_pronostic_name,
						"description" => $this->input->post('description'),
						"code" => $air_record_code_pronostic,
						"icon" => "steel.png"
					);

					$save_data_air_record = $this->Air_records_model->save($data_air_record);

				}
				
			}

			// Si el Sector de la Estación tiene el modelo Numérico (id 3), crear registro de PRONÓSTICO asociado
			if(in_array(3, $array_air_models_of_sector)){

				$air_model = $this->Air_models_model->get_one(3); // Numérico
				$air_model_name = lang($air_model->name);

				$air_record_forecast = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $id_air_sector,
					"id_air_station" => $id_air_station,
					"id_air_model" => 3, // Numérico
					"id_air_record_type" => 2, // Pronóstico
					"deleted" => 0
				));

				// Si el registro no existe, lo crea
				if(!$air_record_forecast->id){

					// REGISTRO PRONÓSTICO
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $id_air_sector,
						"deleted" => 0
					))->result();

					if(count($air_records_of_sector) >= 1){
						$number = count($air_records_of_sector) + 1;
						$air_record_number = "0".$number;
					} else {
						$air_record_number = "01";
					}

					$air_record_type_pronostic = $this->Air_records_types_model->get_one(2); // Pronóstico
					$air_record_type_pronostic_name = lang($air_record_type_pronostic->name);

					$air_record_code_name = str_replace(" ", "", $name);
					$air_record_code_number = $air_record_number;
					$air_record_code_record_type = 2; // Pronóstico
					$air_record_code_pronostic = strtoupper($air_record_code_name.$air_sector_of_station->name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $id_air_sector,
						"id_air_station" => $save_id,
						"id_air_model" => 3, // Numérico
						"id_air_record_type" => 2, // Pronóstico
						"number" => $air_record_number,
						"name" => $name." | ".$air_model_name." - ".$air_record_type_pronostic_name,
						"description" => $this->input->post('description'),
						"code" => $air_record_code_pronostic,
						"icon" => "steel.png"
					);

					$save_data_air_record = $this->Air_records_model->save($data_air_record);

				}

			}

			echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
	}

	/**
	 * delete
	 * 
	 * Elimina una Estación y sus variables asociadas.
	 * Recibe via POST el id de una Estación y "elimina" el registro, 
	 * haciendo update al campo delete de la Estación, de 0 a 1
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id') id de la Estación
	 * @return JSON Con un mensaje de éxito o error en la operación
	 */
    function delete() {

        $this->access_only_allowed_members();
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

		$id = $this->input->post('id');	
		
		if ($this->input->post('undo')) {
			if ($this->Air_stations_model->delete($id, true)) {
				echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
			} else {
				echo json_encode(array("success" => false, lang('error_occurred')));
			}
		} else {
			if ($this->Air_stations_model->delete($id)) {
				$air_station_rel_variable = $this->Air_stations_rel_variables_model->get_all_where(array(
					"id_air_station" => $id,
					"deleted" => 0
				))->result();
				foreach($air_station_rel_variable as $rel){
					$this->Air_stations_rel_variables_model->delete($rel->id);
				}
				echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			}
		}
		
    }

	/**
	 * list_data
	 * 
	 * Lista las Estaciones asociados a un Cliente / Proyecto
	 * Se utiliza via Ajax en el appTable de la vista principal del módulo de Estaciones
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post("id_client") id del Cliente asociado a la Estación
	 * @uses int $this->input->post("id_project") id del Proyecto asociado a la Estación
	 * @return JSON Con datos asociados a las Estaciones
	 */
    function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"id_client" => $this->input->post("id_client"),
			"id_project" => $this->input->post("id_project")
		);
		
        $list_data = $this->Air_stations_model->get_details($options)->result();
        $result = array();
		
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
		}
		
        echo json_encode(array("data" => $result));
    }
	
	/**
	 * _row_data
	 * 
	 * Devuelve un registro asociado a una Estación.
	 * Se utiliza como método auxiliar dentro de los métodos save y delete para actualizar la vista
	 * al momento de guardar o eliminar un registro.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $id Id de la Estación
	 * @return array Con datos de la Estación para actualizar la vista
	 */
    private function _row_data($id) {
        
        $options = array("id" => $id);
		$data = $this->Air_stations_model->get_details($options)->row();
		
        return $this->_make_row($data);
    }

	/**
	 * _make_row
	 * 
	 * Arma un registro asociado a una Estación
	 * Se utiliza como método auxiliar de _row_data para armar los datos
	 * asociados a los registros que se mostrarán en la vista principal mediante el método list_data
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param array $data datos de la Estación
	 * @return array Cada elemento del array es un registro asociado a una Estación
	 */
    private function _make_row($data) {
		
		$client = $this->Clients_model->get_one($data->id_client);
		$project = $this->Projects_model->get_one($data->id_project);
		$air_sector = $this->Air_sectors_model->get_one($data->id_air_sector);

		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->description.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		
        $row_data = array(
			$data->id,
			modal_anchor(get_uri("air_stations/view/" . $data->id), $data->name, array("title" => lang('view_station'))), 
			$client->company_name, 
			$project->title,
            ($data->description) ? $tooltip_descripcion : "-",
			$air_sector->name
        );
		
        $row_data[] = modal_anchor(get_uri("air_stations/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_station')))
				.modal_anchor(get_uri("air_stations/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_station'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_station'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("air_stations/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

	/**
	 * view
	 * 
	 * Carga datos asociados a una Estación en la vista de modal de Ver
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $id_air_station id de la Estación
	 * @return resource Vista del contenido del modal de Ver de una Estación
	 */
    function view($id_air_station = 0) {
		
        $this->access_only_allowed_members();

		$view_data['label_column'] = "col-md-3";
		$view_data['field_column'] = "col-md-9";

        if ($id_air_station) {

            $options = array("id" => $id_air_station);
            $model_info = $this->Air_stations_model->get_details($options)->row();
			
            if ($model_info) {
				
				$view_data['model_info'] = $model_info;
				$view_data['client'] = $this->Clients_model->get_one($model_info->id_client);
				$view_data['project'] = $this->Projects_model->get_one($model_info->id_project);
				$view_data['air_sector'] = $this->Air_sectors_model->get_one($model_info->id_air_sector);
				
				$options_variables = array("id_air_station" => $id_air_station);
				$air_variables = $this->Air_stations_model->get_variables_of_station($options_variables)->result();
				
				if(count($air_variables)){
					$html_variables = (count($air_variables) > 1) ? "<ul>" : "";
					foreach($air_variables as $variable){
						$html_variables .= (count($air_variables) > 1) ? "<li>" . $variable->name_air_variable . "</li>" : $variable->name_air_variable;
					}
					$html_variables .= (count($air_variables) > 1) ? "</ul>" : "";
					$view_data['html_variables'] = $html_variables;
				}
				
				$this->load->view('air_stations/view', $view_data);

            }  else {
                show_404();
            }
        } else {
            show_404();
        }
    }

	/**
	 * get_projects_of_client
	 * 
	 * Lista de Proyectos asociados a un Cliente
	 * Consulta los Proyectos asociados a un Cliente y luego arma y retorna
	 * un HTML que contiene un dropdown que enlista los Proyectos 
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id_client') id del Cliente
	 * @return HTML dropdown con una lista de Proyectos de un Cliente
	 */
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');
		$label_column = $this->input->post('label_column')?$this->input->post('label_column'):'col-md-3';
		$field_column = $this->input->post('field_column')?$this->input->post('field_column'):'col-md-9';

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$projects_dropdown = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="'.$label_column.'">'.lang('project').'</label>';
		$html .= 	'<div class="'.$field_column.'">';
		$html .= 		form_dropdown("project", array("" => "-") + $projects_dropdown, "", "id='project' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;

	}

	/**
	 * get_air_sectors_of_project
	 * 
	 * Lista de Sectores asociados a un Proyecto
	 * Consulta los Sectores asociados a un Proyecto y luego arma y retorna
	 * un HTML que contiene un dropdown que enlista los Sectores 
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id_project') id del Proyecto
	 * @return HTML dropdown con una lista de Sectores de un Proyecto
	 */
	function get_air_sectors_of_project(){

		$id_project = $this->input->post('id_project');
		$label_column = $this->input->post('label_column') ? $this->input->post('label_column') : 'col-md-3';
		$field_column = $this->input->post('field_column') ? $this->input->post('field_column') : 'col-md-9';

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$air_sectors_dropdown = $this->Air_sectors_model->get_dropdown_list(array("name"), "id", array("id_project" => $id_project));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="air_sector" class="'.$label_column.'">'.lang('sector').'</label>';
		$html .= 	'<div class="'.$field_column.'">';
		$html .= 		form_dropdown("air_sector", array("" => "-") + $air_sectors_dropdown, "", "id='air_sector' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;

	}
	
}
