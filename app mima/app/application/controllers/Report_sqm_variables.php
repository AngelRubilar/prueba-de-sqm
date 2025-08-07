<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report_sqm_variables extends MY_Controller
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

        $this->id_modulo_cliente = 17;
        $this->id_submodulo_cliente = 31;

        $id_cliente = $this->login_user->client_id;
        $id_proyecto = $this->session->project_context;
        // Obtener el id_client_group del usuario logueado
   		$id_client_group = isset($this->login_user->id_client_group) ? $this->login_user->id_client_group: null;
		

        if ($id_proyecto) {
            $this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
        }
    }

    function index()
    {

        ini_set("memory_limit", "-1");

        $id_cliente = $this->login_user->client_id;
        $id_proyecto = $this->session->project_context;

        //obtnemos el id_client_group del usuario logueado
		$id_client_group = isset($this->login_user->id_client_group) ? $this->login_user->id_client_group: null;
		

        $view_data["user"] = $this->Users_model->get_one($this->login_user->id);
        $view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        $view_data["collapsed_left_menu"] = true;
        ### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_forecast');

        $project = $this->Projects_model->get_one($air_sector->id_project);
        $view_data["project_info"] = $project;
        
        log_message("error", "id_client_group: " . $id_client_group);
        if($id_client_group==6){
            $view_data["iframe_src"] = "http://localhost:3000/react/estaciones";
            $this->template->rander("report_sqm_variables/index", $view_data);
        } else if($id_client_group==7){
            $view_data["iframe_src"] = "http://localhost:3000/react/sqm_grup1y";
            $this->template->rander("report_sqm_variables/index", $view_data);
        } else {
            $view_data["iframe_src"] = "http://localhost:3000/react/sqm_grup2";
            $this->template->rander("report_sqm_variables/index", $view_data);
        }
    }
}

