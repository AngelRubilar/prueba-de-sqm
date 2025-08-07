<?php
/**
 * Archivo Controlador para prueba de Tablas de Contingencia (R)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category prueba de Tablas de Contingencia
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para prueba de Tablas de Contingencia (R)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category prueba de Tablas de Contingencia
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_contingency_tables extends MY_Controller {
	
	/**
	 * id_modulo_cliente
	 * @var int $id_modulo_cliente
	 */
	// private $id_modulo_cliente;
	/**
	 * id_submodulo_cliente
	 * @var int $id_submodulo_cliente
	 */
	// private $id_submodulo_cliente;
	
	/**
	 * __construct
	 * 
	 * Constructor
	 */
    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
		
		// $this->id_modulo_cliente = 15;
		// $this->id_submodulo_cliente = 36;
		
		// $id_cliente = $this->login_user->client_id;
		// $id_proyecto = $this->session->project_context;		
		// $this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// // Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		// $this->block_url_client_context($id_cliente, 3);
		
    }

    /**
	 * index
	 * 
	 * Carga aplicación Shiny de Partículas
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @return resource Vista principal del módulo
	 */
    function index() {

		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		
		$view_data["shiny_url"] = "https://particulas.shinyapps.io/tablasDeContingencia/";
        $this->template->rander("air_contingency_tables/index", $view_data);
    }

}
