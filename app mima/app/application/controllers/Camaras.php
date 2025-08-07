<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Camaras extends MY_Controller
{

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


	function __construct()
	{
		parent::__construct();
		$this->init_permission_checker("client");

		$this->id_modulo_cliente = 19;
		$this->id_submodulo_cliente = 28;

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		if ($id_proyecto) {
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
	}

	function index()
	{

		ini_set("memory_limit", "-1");

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$view_data["user"] = $this->Users_model->get_one($this->login_user->id);

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		### GENERAR REGISTRO EN LOGS_MODEL ###
		$this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_forecast');

		$project = $this->Projects_model->get_one($air_sector->id_project);
		$view_data["project_info"] = $project;
    $view_data["iframe_src"] = 'https://localhost/external'; // apunta a nuestra pagina que es procesada por nginx
    
		$this->template->rander("camaras/index", $view_data);
	}
}
 