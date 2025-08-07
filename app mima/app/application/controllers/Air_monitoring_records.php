<?php
/**
 * Archivo Controlador para Registros Calidad del Aire / Registros de Monitoreo (módulo nivel Cliente / Proyecto)
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
 * Controlador para Registros Calidad del Aire / Registros de Monitoreo (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Monitoreo
 * @property private $id_modulo_cliente id del módulo Registros Calidad del Aire (12)
 * @property private $id_submodulo_cliente id del submódulo Registros de Monitoreo (23)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_monitoring_records extends MY_Controller {
	
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
		$this->id_submodulo_cliente = 23;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		

		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
		
    }

	/**
	 * index
	 * 
	 * Carga los Registros de Aire de tipo Monitoreo de las variables de Calidad del aire y Meteorológicas 
	 * de los Sectores / Estaciones asociadas al Cliente / Proyecto donde está navegando el Usuario
	 * en sesión, para enlistarlos en la vista principal del módulo.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return resource Vista principal del módulo
	 */
    function index() {
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
		$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$this->template->rander("air_monitoring_records/index", $view_data);
    }


}

