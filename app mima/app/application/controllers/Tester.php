<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tester extends MY_Controller {
	
	function __construct() {
        
		parent::__construct();
		$this->load->helper('currency');
		//$this->access_only_allowed_members();
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$id_cliente = $this->login_user->client_id;

		/*if($this->login_user->user_type === "client") {
			$this->block_url_client_context($id_cliente, 14);
		}*/
    }
	
  function index(){
    $this->template->rander("tester/index");
  }

}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */