<?php
/**
 * Archivo Controlador para Forzar envío de alerta de Pronósticos (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Forzar envío de alerta de Pronósticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Forzar envío de alerta de Pronósticos (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Forzar envío de alerta de Pronósticos
 * @property private $id_modulo_cliente id del módulo Administración Cliente Mimaire (15)
 * @property private $id_submodulo_cliente id del submódulo Forzar envío de alerta de Pronósticos (36)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_force_sending_forecast_alert extends MY_Controller {
	
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
		$this->load->library('cron_job');

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 15;
		$this->id_submodulo_cliente = 36;
		
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
	 * @author Gustavo Pinochet Altamirano
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
		$view_data["project_info"] = $proyecto;
		
		//Configuración perfil de usuario
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

        $this->template->rander("air_force_sending_forecast_alert/index", $view_data);

    }

	function modal_form_force_send(){
		$view_data = array();
        $this->load->view('air_force_sending_forecast_alert/modal_form_force_send', $view_data);
	}

	function save_force_send(){

		$id_proyecto = $this->session->project_context;
		$run_air_alerts = $this->cron_job->run_air_alerts($id_proyecto, true);

		// $current_time = strtotime(get_current_utc_time());
        // $this->Settings_model->save_setting("last_cron_job_time", $current_time);

		if($run_air_alerts) {
			
			// // Guardar histórico notificaciones
			// $options = array(
			// 	"id_client" => $client_id,
			// 	"id_project" => $id_proyecto,
			// 	"id_user" => $this->session->user_id,
			// 	"module_level" => "project",
			// 	"id_client_module" => $this->id_modulo_cliente,
			// 	"id_client_submodule" => $this->id_submodulo_cliente,
			// 	"event" => ($elemento_id) ? "edit" : "add",
			// 	"id_element" => $save_id
			// );
			// ayn_save_historical_notification($options);
			
            echo json_encode(array("success" => true, /*"data" => $this->_row_data($save_id, $columnas, $id_other_record),*/ 'id' => $run_air_alerts, 'message' => lang('force_sending_forecast_alert_msj_3')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }

	}

	function tab_email_content(){
		$id_proyecto = $this->session->project_context;
		$view_data = array();
		$view_data["email_content"] = $this->cron_job->run_air_alerts($id_proyecto, true, true);
		$this->load->view('air_force_sending_forecast_alert/tab_email_content', $view_data);
	}

	function get_last_bulletin_pdf(){

		// CONSULTAR ÚLTIMO BOLETÍN SUBIDO Y DISPONIBILIZAR LINK PARA SU DESCARGA
		$id_form = 2; // Formulario dinámico creado para Boletines
		$id_field_file = 1; // Campo Archivo dinámico para PDF del Boletín
		$id_field_text = 2; // Campo Texto dinámico para texto del Boletín
		$last_bulletin = $this->Form_values_model->get_last_value_of_form(array("id_form" => $id_form))->row();
		$bulletin_data = json_decode($last_bulletin->datos, true);
		// $bulletin_text = $bulletin_data[$id_field_text];
		$bulletin_filename = $bulletin_data[$id_field_file];
		$bulletin_filepath = "files/mimasoft_files/client_1/project_1/form_".$id_form."/elemento_".$last_bulletin->id."/";

		//serilize the path
		$file_data = serialize(array(array("file_name" => $bulletin_filename)));
		download_app_files($bulletin_filepath, $file_data);

	}

}
