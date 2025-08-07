<?php
/**
 * Archivo Controlador para Condiciones Meteorológicas / Ver Imágenes (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Condiciones Meteorológicas
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador para Condiciones Meteorológicas / Ver Imágenes (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Condiciones Meteorológicas
 * @property private $id_modulo_cliente id del módulo RCondiciones Meteorológicas (20)
 * @property private $id_submodulo_cliente id del submódulo Ver Imágenes (33)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_mc_display_images extends MY_Controller {
	
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
		
		$this->id_modulo_cliente = 20;
		$this->id_submodulo_cliente = 33;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		

		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
		
    }

	/**
	 * index
	 * 
	 * Muestra las imágenes de monitoreo de 72 horas.
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

		// TRAER LOS GRUPOS 
		$array_groups = array();
		$file_path = "files/meteorological_conditions_images/";
		$files = scandir($file_path);
		$files = array_diff(scandir($file_path), array('.', '..'));

		foreach($files as $file_name){
			$group = explode("_", $file_name)[0];
			$group_name = "";
			switch($group) {
				case "grupoA":
					$group_name = lang("surface");
					break;
				case "grupoB":
					$group_name = lang("950_hpa");
					break;
				case "grupoC":
					$group_name = lang("850_hpa");
					break;
				case "grupoD":
					$group_name = lang("700_hpa");
					break;
				// case "grupoE":
				// 	$group_name = lang("radiosonde");
				// 	break;
				default:
					$group_name = lang("unknown_group");
			}

			$array_groups[$group] = $group_name;
		}
		$view_data["array_groups"] = $array_groups;

		$this->template->rander("air_mc_display_images/index", $view_data);
    }

	function get_images_by_group() {

        $data_slider_group = $this->input->post("data_slider_group");

		// TRAER LAS IMÁGENES 
		$array_images = array();
		$file_path = "files/meteorological_conditions_images/";
		$files = scandir($file_path);
		$files = array_diff(scandir($file_path), array('.', '..'));

		foreach($files as $file_name){
			$group = explode("_", $file_name)[0];
			$date = explode("T", explode("_", $file_name)[1])[0];
			$hour = explode(".", explode("T", explode("_", $file_name)[1])[1])[0].":00 hrs.";
			// echo $hour."<br>";

			if($group == $data_slider_group){
				// $array_images[$group][$file_path.$file_name] = array(
				$array_images[$file_path.$file_name] = array(
					//"file_name" => $file_name,
					"date" => $date,
					"hour" => $hour
				);
			}
		}

		$html = '<div class="slider-'.$data_slider_group.'">';

			foreach($array_images as $file_path => $array_img_data){

				$html .= 	'<div style="height: 60%; width: 60%; margin: auto;">';
				$html .= 		'<img src="'.$file_path."?".time().'" alt="">';
				$html .= 		'<div class="row text-center">';
				$html .= 			'<span class="label label-info large">'.$array_img_data["date"].' - '.$array_img_data["hour"].'</span>';
				$html .=		'</div>';
				$html .=	'</div>';

			}

		$html .= '</div>';

		echo $html;

    }

}

