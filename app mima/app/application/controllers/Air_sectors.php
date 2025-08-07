<?php
/**
 * Archivo Controlador para Sectores (módulo de Administración)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Sectores
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Sectores (módulo de Administración)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Sectores
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_sectors extends MY_Controller {

	/**
	 * __construct
	 * 
	 * Constructor
	 */
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
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
		$access_info = $this->get_access_info("invoice");
		
		//FILTRO CLIENTE		
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		//FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
        $this->template->rander("air_sectors/index", $view_data);
    }
	
	/**
	 * modal_form
	 * 
	 * Carga datos asociados a un Sector en la vista de modal de ingreso/edición
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id') id del Sector
	 * @return resource Vista del contenido del modal de ingreso/edición de un Sector
	 */
	function modal_form() {
		
        $this->access_only_allowed_members();
        $air_sector_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->Air_sectors_model->get_one($air_sector_id);
		$view_data["clients"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["projects"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_client));
		$air_models_dropdown = array();
		$air_models = $this->Air_models_model->get_all()->result();
		foreach($air_models as $air_model){
			$air_models_dropdown[$air_model->id] = lang($air_model->name);
		}
		$view_data["air_models_dropdown"] = $air_models_dropdown;
		
		if($air_sector_id){
			$view_data["air_models_selected"] = json_decode($view_data['model_info']->air_models);
		}
		
		$this->load->view('air_sectors/modal_form', $view_data);
		
    }
	
	/**
	 * save
	 * 
	 * Guarda el ingreso/edición de un Sector en base de datos.
	 * En caso de recibir via POST el id del Sector, se realiza un update
	 * al registro del Sector asociado a ese id, de lo contrario, crea un nuevo registro.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id') id del Sector
	 * @return JSON Con datos asociados al registro del Sector ingresado/editado para actualizar 
	 * el appTable de la vista principal y mostrar mensaje de éxito o error en la operación
	 * 
	 */
	function save() {

        $air_sector_id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));

		$name = trim($this->input->post('name'));
		$id_client = $this->input->post('client');
		$id_project = $this->input->post('project');
		$air_models = (array)$this->input->post('air_models');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');

		if($this->Air_sectors_model->is_air_sector_name_exists($name, $id_client, $id_project, $air_sector_id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_sector_name')));
			exit(); 
		}
		
		$data_air_sector = array(
			"name" => $name,
			"id_client" => $id_client,
			"id_project" => $id_project,
			"air_models" => json_encode($air_models),
			"latitude" => $latitude,
			"longitude" => $longitude,
			"description" => $this->input->post('description'),
			"deleted" => 0
		);
		
		if($air_sector_id){
			$data_air_sector["modified_by"] = $this->login_user->id;
			$data_air_sector["modified"] = get_current_utc_time();
			$save_id = $this->Air_sectors_model->save($data_air_sector, $air_sector_id);
		} else {
			$data_air_sector["created_by"] = $this->login_user->id;
			$data_air_sector["created"] = get_current_utc_time();
			$save_id = $this->Air_sectors_model->save($data_air_sector);
		}
		
        if ($save_id) {

			// Si viene el modelo Numérico (id 3), crear registros de MONITOREO y PRONÓSTICO asociados
			if(in_array(3, $air_models)){ 

				$air_model = $this->Air_models_model->get_one(3); // Numérico
				$air_model_name = lang($air_model->name);

				/*
				$air_record_monitoring = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $save_id,
					"id_air_model" => 3, // Numérico
					"id_air_record_type" => 1, // Monitoreo
					"deleted" => 0
				));
				
				// Si el registro no existe, lo crea
				if(!$air_record_monitoring->id){

					// REGISTRO MONITOREO
					// Número: Es un correlativo, que se va calculando de acuerdo a la cantidad de registros por sector 
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $save_id,
						"deleted" => 0
					))->result();

					if(count($air_records_of_sector) >= 1){
						$number = count($air_records_of_sector) + 1;
						$air_record_number = "0".$number;
					} else {
						$air_record_number = "01";
					}

					// Nombre: Compuesto por el nombre del Modelo y el Tipo de Registro. Ej: Numérico - Monitoreo
					$air_record_type_monitoring = $this->Air_records_types_model->get_one(1); // Monitoreo
					$air_record_type_monitoring_name = lang($air_record_type_monitoring->name);

					// Código: Formado por el nombre del Sector, el número correlativo generado y el Tipo de Registro
					$air_record_code_name = str_replace(" ", "", $name);
					$air_record_code_number = $air_record_number;
					$air_record_code_record_type = 1; // Monitoreo
					$air_record_code_monitoring = strtoupper($air_record_code_name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record_monitoring = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $save_id,
						"id_air_station" => NULL,
						"id_air_model" => 3, // Numérico
						"id_air_record_type" => 1, // Monitoreo
						"number" => $air_record_number,
						"name" => $air_model_name." - ".$air_record_type_monitoring_name,
						"description" => $this->input->post('description'),
						"code" => $air_record_code_monitoring,
						"icon" => "steel.png"
					);

					$save_data_air_record = $this->Air_records_model->save($data_air_record_monitoring);

				}
				*/

				$air_record_forecast = $this->Air_records_model->get_one_where(array(
					"id_air_sector" => $save_id,
					"id_air_model" => 3, // Numérico
					"id_air_record_type" => 2, // Pronóstico
					"deleted" => 0
				));

				// Si el registro no existe, lo crea
				if(!$air_record_forecast->id){

					// REGISTRO PRONÓSTICO
					$air_records_of_sector = $this->Air_records_model->get_all_where(array(
						"id_air_sector" => $save_id,
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
					$air_record_code_pronostic = strtoupper($air_record_code_name.$air_record_code_number.$air_record_code_record_type);

					$data_air_record = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_air_sector" => $save_id,
						"id_air_station" => NULL,
						"id_air_model" => 3, // Numérico
						"id_air_record_type" => 2, // Pronóstico
						"number" => $air_record_number,
						"name" => $air_model_name." - ".$air_record_type_pronostic_name,
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
	 * Elimina un Sector.
	 * Recibe via POST el id de un Sector y "elimina" el registro, 
	 * haciendo update al campo delete del Sector, de 0 a 1
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id') id del Sector
	 * @return JSON Con un mensaje de éxito o error en la operación
	 */
	function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Air_sectors_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Air_sectors_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

	/**
	 * list_data
	 * 
	 * Lista los Sectores asociados a un Cliente / Proyecto
	 * Se utiliza via Ajax en el appTable de la vista principal del módulo de Sectores
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post("id_client") id del Cliente asociado al Sector
	 * @uses int $this->input->post("id_project") id del Proyecto asociado al Sector
	 * @return JSON Con datos asociados a los Sectores
	 */
	function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"id_client" => $this->input->post("id_client"),
			"id_project" => $this->input->post("id_project")
		);
		
        $list_data = $this->Air_sectors_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }
	
	/**
	 * _row_data
	 * 
	 * Devuelve un registro asociado a un Sector.
	 * Se utiliza como método auxiliar dentro de los métodos save y delete para actualizar la vista
	 * al momento de guardar o eliminar un registro.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $id id del Sector
	 * @return array Con datos del Sector para actualizar la vista
	 */
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->Air_sectors_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	/**
	 * _make_row
	 * 
	 * Arma un registro asociado a un Sector
	 * Se utiliza como método auxiliar de _row_data para armar los datos
	 * asociados a los registros que se mostrarán en la vista principal mediante el método list_data
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param array $data datos del Sector
	 * @return array Cada elemento del array es un registro con datos asociados a un Sector
	 */
	private function _make_row($data) {

		$client = $this->Clients_model->get_one($data->id_client);
		$project = $this->Projects_model->get_one_where(array("id" => $data->id_project, "deleted" => 0));
		$project_name = ($project->title) ?  $project->title : "-";
		
		$tooltip_description = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->description.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$description = ($data->description) ? $tooltip_description : "-";
		
        $row_data = array(
			$data->id, 
			modal_anchor(get_uri("air_sectors/view/" . $data->id), $data->name, array("title" => lang('view_sector'), "data-post-id" => $data->id)), 
			$client->company_name, 
			$project_name,
			$description
		);
		
        $row_data[] =  modal_anchor(get_uri("air_sectors/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_sector'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("air_sectors/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_sector'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_sector'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("air_sectors/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
	
	/**
	 * view
	 * 
	 * Carga datos asociados a un Sector en la vista de modal de Ver
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $air_sector_id id del Sector
	 * @return resource Vista del contenido del modal de Ver de un Sector
	 */
	function view($air_sector_id = 0) {
        $this->access_only_allowed_members();
				
        if ($air_sector_id) {
            $options = array("id" => $air_sector_id);
            $air_sector_info = $this->Air_sectors_model->get_details($options)->row();
            if ($air_sector_info) {

				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";

				$view_data['model_info'] = $air_sector_info;
				$client = $this->Clients_model->get_one($air_sector_info->id_client);
				$view_data["client"] = $client->company_name;
				$project = $this->Projects_model->get_one($air_sector_info->id_project);
				$view_data["project"] = $project->title;

				$air_models = json_decode($air_sector_info->air_models);
				if(count($air_models)){
					$html_models = (count($air_models) > 1) ? "<ul>" : "";
					foreach($air_models as $id_model){
						$model = $this->Air_models_model->get_one($id_model);
						$html_models .= (count($air_models) > 1) ? "<li>" . lang($model->name) . "</li>" : lang($model->name);
					}
					$html_models .= (count($air_models) > 1) ? "</ul>" : "";
					$view_data['html_models'] = $html_models;
				}


				$this->load->view('air_sectors/view', $view_data);
            } else {
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
	
		$id_client = $this->input->post('id_client');
		$col_label = $this->input->post('col_label') ? $this->input->post('col_label') : 'col-md-3';
		$col_projects = $this->input->post('col_projects') ? $this->input->post('col_projects') : 'col-md-9';

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$projects_dropdown = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_client));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="'.$col_label.'">'.lang('project').'</label>';
		$html .= 	'<div class="'.$col_projects.'">';
		$html .= 		form_dropdown("project", array("" => "-") + $projects_dropdown, "", "id='project' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;

	}
	
}
