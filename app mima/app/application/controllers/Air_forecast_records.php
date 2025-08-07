<?php
/**
 * Archivo Controlador para Registros Calidad del Aire / Registros de Pronóstico (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Pronosticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Registros Calidad del Aire / Registros de Pronóstico (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Pronosticos
 * @property private $id_modulo_cliente id del módulo Registros Calidad del Aire (12)
 * @property private $id_submodulo_cliente id del submódulo Registros de Pronóstico (24)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_forecast_records extends MY_Controller {
	
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
		
		$this->id_modulo_cliente = 12;
		$this->id_submodulo_cliente = 24;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}

    }

	/**
	 * index
	 * 
	 * Carga los Registros de Aire de tipo Pronóstico de las variables de Calidad del aire y Meteorológicas 
	 * de los Sectores / Estaciones asociadas al Cliente / Proyecto donde está navegando el Usuario
	 * en sesión, para enlistarlos en la vista principal del módulo.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return resource Vista principal del módulo
	 */
    function index() {

		$id_proyecto = $this->session->project_context;
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		$id_cliente = $proyecto->client_id;

		$air_forecast_records = $this->Air_records_model->get_details(array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_air_record_type" => 2 // Pronóstico
		))->result();

		$view_data["air_forecast_records"] = $air_forecast_records;

        $this->template->rander("air_forecast_records/index", $view_data);
	}
	
	/**
	 * view
	 * 
	 * Carga datos asociados a un Registro de Pronóstico en la vista de modal de Ver
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $id_air_record id del Registro de Pronóstico
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return resource Vista del contenido del modal de Ver de un Registro de Pronóstico
	 */
	function view($id_air_record) {
		
		$id_proyecto = $this->session->project_context;
		$id_cliente =  $this->login_user->client_id;

        if ($id_air_record) {

			// Filtro Variables
			$array_variables[] = array("id" => "", "text" => "- ".lang("variable")." -");
			$variables = $this->Air_variables_model->get_all()->result();
			foreach($variables as $variable){
				$array_variables[] = array("id" => $variable->id, "text" => $variable->name);
			}
			$view_data['array_variables'] = json_encode($array_variables);

			// VALIDAR QUE EL REGISTRO QUE SE ESTA VIENDO PERTENECE AL MISMO CLIENTE DEL USUARIO EN SESIÓN
			// Y QUE SEA DE TIPO PRONÓSTICO

			$air_record = $this->Air_records_model->get_details(array(
				"id" => $id_air_record,
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_air_record_type" => 2, // Pronóstico
				"deleted" => 0
			))->row();
			

			if($air_record->id){

				$view_data["air_record_info"] = $air_record;

				// VALIDAR QUE EL USUARIO SEA MIEMBRO DEL PROYECTO DEL REGISTRO
				$miembro_proyecto = $this->Project_members_model->get_one_where(array(
					"user_id" => $this->login_user->id,
					"project_id" => $id_proyecto, 
					"deleted" => 0
				));
				
				if(!$miembro_proyecto->id){
					redirect("forbidden");
				}

				$air_records_values_p = $this->Air_records_values_p_model->get_all_where(array(
					"id_record" => $id_air_record,
					"deleted" => 0
				))->result();
				$num_registros = count($air_records_values_p);
				$view_data['num_registros'] = $num_registros;
				
				$proyecto = $this->Projects_model->get_one($this->session->project_context);
				$view_data["project_info"] = $proyecto;
				
				$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
				$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
				$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");

				$this->template->rander("air_forecast_records/records/index", $view_data);

			} else {
				redirect("forbidden");
			}	

        } else {
            show_404();
        }
    }

   /**
	 * list_data
	 * 
	 * Lista los Registros de Pronósticos de las variables de Calidad del aire y Meteorológicas 
	 * de los Sectores / Estaciones asociadas al Cliente / Proyecto donde está navegando el Usuario
	 * Se utiliza via Ajax en el appTable de la vista principal del módulo de Registros de Pronóstico
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $id_air_record id del Registro de Pronóstico
	 * @uses int $this->input->post("id_client") Id del Cliente asociado al Registro de Pronóstico
	 * @uses int $this->input->post("id_project") Id del Proyecto asociado al Registro de Pronóstico
	 * @uses int $this->session->user_id id del Usuario en sesión
	 * @return JSON Con datos asociados a los Registros de Pronósticos
	 */
    function list_data($id_air_record = 0) {

		$air_record = $this->Air_records_model->get_one($id_air_record);

		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$id_variable = $this->input->post("id_variable");
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");

		$options = array(
			"id_record" => $id_air_record,
			"id_variable" => $id_variable,
			"start_date" => $start_date,
			"end_date" => $end_date,
			"id_model" => $air_record->id_air_model
		);

		$list_data = $this->Air_records_values_p_model->get_details2($options)->result_array();

		echo json_encode(array("data" => $list_data));
		
    }

}
