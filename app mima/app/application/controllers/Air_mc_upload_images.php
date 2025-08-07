<?php
/**
 * Archivo Controlador para Condiciones Meteorológicas / Cargar Imágenes (módulo nivel Cliente / Proyecto)
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
 * Controlador para Condiciones Meteorológicas / Cargar Imágenes (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Condiciones Meteorológicas
 * @property private $id_modulo_cliente id del módulo RCondiciones Meteorológicas (20)
 * @property private $id_submodulo_cliente id del submódulo Cargar Imágenes (34)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_mc_upload_images extends MY_Controller {
	
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
		$this->id_submodulo_cliente = 34;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		

		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
		
    }

	/**
	 * index
	 * 
	 * Miestra vista para cargar las imágenes de monitoreo de 72 horas.
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
		$view_data["puede_editar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");

		$this->template->rander("air_mc_upload_images/index", $view_data);
    }

	/**
	 * upload_file
	 * 
	 * Ejecuta el método helper upload_file_to_temp() para guardar el archivo subido
	 * por el Usuario en el directorio de archivos temporales (/files/temp) del proyecto
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @return void
	 */
    function upload_file() {
        upload_file_to_temp();
    }

	/**
	 * validate_file
	 * 
	 * Valida que archivo a subir por el Usuario al FTP, tenga la extensión png, jpg, jpeg
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @return JSON Con un mensaje de éxito o error según sea el caso.
	 */
    function validate_file() {
		
		$file_name = $this->input->post("file_name");
		
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'png' || $file_ext == 'jpg' || $file_ext == 'jpeg') {
			echo json_encode(array("success" => true));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		
    }

	function validate_zip_file() {

		$file_name = $this->input->post("file_name");
		
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'zip' /*|| $file_ext == 'rar'*/) {
			echo json_encode(array("success" => true));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

	}

	function get_file_field_for_bulk_load(){

		// $html = $this->load->view("includes/multiple_files_uploader_xs_prev", array(
		// 	"upload_url" =>get_uri("air_mc_upload_images/upload_file"),
		// 	"validation_url" =>get_uri("air_mc_upload_images/validate_file"),
		// 	"html_name" => "archivo_importado",
		// 	//"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
		// 	"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required").'"',
		// 	//"obligatorio" => "",
		// 	"id_campo" => "archivo_importado"
			
		// ), true);

		$html = $this->load->view("includes/bulk_file_uploader", array(
			"upload_url" => get_uri("air_mc_upload_images/upload_file"),
			"validation_url" =>get_uri("air_mc_upload_images/validate_zip_file"),
			//"html_name" => 'test',
			//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
		), true);

		echo $html;
	}

	// // Guardar multiples imagenes campo multiarchivo
	// function save(){

	// 	$files = $this->input->post('archivo_importado');

	// 	foreach($files as $file_number){

	// 		$file = $this->input->post("file_name_".$file_number);
	// 		$archivo_subido = move_temp_file($file, "files/meteorological_conditions_images/", "", "", $file);
			
	// 		if(!$archivo_subido){
	// 			echo json_encode(array("success" => false, 'message' => lang('images_failed_load'), 'carga' => true));
	// 		}
	// 	}

	// 	echo json_encode(array("success" => true, 'message' => lang('images_saved'), 'carga' => true));

	// }

	// // Guardar imágenes a partir de un archivo .zip
	// function save(){

	// 	$file = $this->input->post('archivo_importado');
	// 	$file_path = "files/meteorological_conditions_images/";

	// 	// PRIMERO BORRAR LAS IMÁGENES ANTERIORES
	// 	array_map('unlink', glob("$file_path/*.*"));

	// 	$archivo_subido = move_temp_file($file, $file_path, "", "", $file);

	// 	$zip = new ZipArchive();
	// 	$res = $zip->open($file_path.$file);
	// 	if ($res === TRUE) {
	// 		$zip->extractTo($file_path);
	// 		$zip->close();

	// 		unlink($file_path.$file);
	// 		echo json_encode(array("success" => true, 'message' => lang('images_saved'), 'carga' => true));
	// 	} else {
	// 		echo json_encode(array("success" => false, 'message' => lang('images_failed_load'), 'carga' => false));
	// 	}
	// 	exit();

	// }

	// Guardar imágenes a partir de un archivo .zip
	function save(){

		$file = $this->input->post('archivo_importado');
		$file_path_zip = "files/meteorological_conditions_images/zip_file/";

		if($file){
			$archivo_subido = move_temp_file($file, $file_path_zip, "", "", $file);
		}
		
		if($archivo_subido){

			$zip = new ZipArchive();
			$res = $zip->open($file_path_zip.$file);
			if ($res === TRUE) {

				// VALIDA QUE TODOS LOS ARCHIVOS DENTRO DEL ARCHIVO .ZIP SEAN IMÁGENES
				for( $i = 0; $i < $zip->numFiles; $i++ ){ 
					$stat = $zip->statIndex( $i );
					$file_name = $stat['name'];
					$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
					if($file_ext == ''){
						array_map('unlink', array_filter((array) glob("$file_path_zip*"))); // BORRAR TODOS LOS ARCHIVOS DE FILE_PATH_ZIP
						rmdir($file_path_zip); // BORRAR DIRECTORIO ZIP FILE
						echo json_encode(array("success" => false, 'message' => "El archivo comprimido no puede contener carpetas, solo imágenes."));
						exit();
					}
					if ($file_ext != 'png' && $file_ext != 'jpg' && $file_ext != 'jpeg') {
						array_map('unlink', array_filter((array) glob("$file_path_zip*"))); // BORRAR TODOS LOS ARCHIVOS DE FILE_PATH_ZIP
						rmdir($file_path_zip); // BORRAR DIRECTORIO ZIP FILE
						echo json_encode(array("success" => false, 'message' => "El archivo comprimido debe contener solo imágenes"));
						exit();
					} 
				}

				// PRIMERO BORRAR LAS IMÁGENES ANTERIORES
				$file_path = "files/meteorological_conditions_images/";
				array_map('unlink', glob("$file_path/*.*"));

				$zip->extractTo($file_path);
				$zip->close();

				array_map('unlink', array_filter((array) glob("$file_path_zip*"))); // BORRAR TODOS LOS ARCHIVOS DE FILE_PATH_ZIP
				rmdir($file_path_zip); // BORRAR DIRECTORIO ZIP FILE

				echo json_encode(array("success" => true, 'message' => lang('images_saved'), 'carga' => true));
			} else {
				echo json_encode(array("success" => false, 'message' => lang("could_not_unzip_the_file"), 'carga' => false));
			}
		} else {
			echo json_encode(array("success" => false, 'message' => lang("the_file_has_not_been_loaded"), 'carga' => false));
		}
		exit();

	}

}

