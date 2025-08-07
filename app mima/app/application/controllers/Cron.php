<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('cron_job');
    }

	function test_r_lautaro(){

		phpinfo();

		/*
			ACCESO LAUTARO
			Usuario: particulas02@leftraru.nlhpc.cl
			Clave: Chagres2022
		*/

		$server = 'particulas02@leftraru.nlhpc.cl'; // Reemplaza 'hostname' con el nombre o la dirección IP del servidor SSH externo
		$port = 22; // Puerto SSH predeterminado es 22
		$username = 'particulas02@leftraru2'; // Reemplaza 'username' con tu nombre de usuario de SSH externo
		$password = 'Chagres2022'; // Reemplaza 'password' con tu contraseña de SSH externo
		$scriptPath = '/R/print.R'; // Ruta al script R en el servidor externo

		// Establecer la conexión SSH
		$connection = ssh2_connect($server, $port);
		if (!$connection) {
			die('No se pudo establecer la conexión SSH.');
		}

		// Autenticación con nombre de usuario y contraseña
		if (!ssh2_auth_password($connection, $username, $password)) {
			die('No se pudo autenticar con el nombre de usuario y la contraseña proporcionados.');
		}

		// Ejecutar el script R en el servidor externo y capturar el resultado
		$command = 'Rscript ' . $scriptPath; // Comando para ejecutar el script R
		$stream = ssh2_exec($connection, $command);
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
		fclose($stream);

		// Imprimir el resultado
		echo $output;

		// Cerrar la conexión SSH
		ssh2_disconnect($connection);

	}

	function test_r(){
		
		// $scriptR = 'x <- c(1, 2, 3, 4, 5); mean(x)';
		// $resultadoR = R($scriptR);
		// echo "El resultado de R es: " . $resultadoR;

		// $output = system("Rscript test_r/test.R 1 4");
		// var_dump($output);

		require_once('vendor/kachkaev/php-r/src/Kachkaev/PHPR/RCore.php');
		require_once('vendor/kachkaev/php-r/src/Kachkaev/PHPR/Engine/CommandLineREngine.php');

		// use Kachkaev\PHPR\RCore;
		// use Kachkaev\PHPR\Engine\CommandLineREngine;

		// Crear una instancia de RCore
		$r = new RCore(new CommandLineREngine('/usr/bin/R'));

		// Ejecutar código R
		$result = $r->run('x = 1 + 2; x');

		// Imprimir el resultado
		echo $result;
	}

    function index() {

        $this->cron_job->run();
		$current_time = strtotime(get_current_utc_time());
        $this->Settings_model->save_setting("last_cron_job_time", $current_time);
        
        /*
        ini_set('max_execution_time', 300); //execute maximum 300 seconds 
        //wait at least 5 minute befor starting new cron job
        $last_cron_job_time = get_setting('last_cron_job_time');

        $current_time = strtotime(get_current_utc_time());

        if ($last_cron_job_time == "" || ($current_time > (strtotime($last_cron_job_time) + 300))) {
            $this->cron_job->run();
            $this->Settings_model->save_setting("last_cron_job_time", $current_time);
        }
        */
    }

    function run_air_alerts($id_proyecto){
        $this->cron_job->run_air_alerts($id_proyecto);
		$current_time = strtotime(get_current_utc_time());
        $this->Settings_model->save_setting("last_cron_job_time", $current_time);
    }

    /**
	 * clean_forecast_data
	 * 
	 * Elimina la data de pronósticos antigua de la base de datos para agilizar las consultas, dejando solamente 
     * los últimos datos registrados necesarios para que en la vista de Pronósticos se vea información.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @return JSON con mensaje de respuesta
	 */
    function clean_forecast_data() {

		ini_set('memory_limit', '-1'); 

		// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
		$last_upload_date = $this->Air_records_values_uploads_model->get_first_id_to_delete()->row()->created;

		// $today_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$first_datetime = new DateTime($last_upload_date);
		$first_datetime->setTime(0,0,0);
		$first_datetime = $first_datetime->modify('-96 hours');
		$first_date = $first_datetime->format("Y-m-d");

		$air_records_values_uploads_rows = $this->Air_records_values_uploads_model->get_all()->num_rows();
		$air_records_values_p_rows = $this->Air_records_values_p_model->get_all()->num_rows();

		// SI HAY REGISTROS
		if($air_records_values_uploads_rows || $air_records_values_p_rows){

			// BUSCAR EL ÚLTIMO REGISTRO DE LA TABLA air_records_values_uploads EN DONDE EL CAMPO created SEA MENOR A $first_datetime Y OBTENER EL id
			$first_id_to_delete = $this->Air_records_values_uploads_model->get_first_id_to_delete(array("created" => $first_date))->row()->id;
			$delete_values_p_min =  $this->Air_records_values_p_min_model->delete_old_values_from_an_id_upload($first_id_to_delete);
			$delete_values_p_max =  $this->Air_records_values_p_max_model->delete_old_values_from_an_id_upload($first_id_to_delete);
			$delete_values_p_porc_conf =  $this->Air_records_values_p_porc_conf_model->delete_old_values_from_an_id_upload($first_id_to_delete);
			$delete_values_p = $this->Air_records_values_p_model->delete_old_values_from_an_id_upload($first_id_to_delete);
			$delete_values_uploads = $this->Air_records_values_uploads_model->delete_old_values_from_an_id($first_id_to_delete);

			// SI AMBAS TABLAS SON LIMPIADAS
			if($delete_values_p && $delete_values_uploads){

				// REINICIAR IDS
				$reset_ids_values_uploads = $this->Air_records_values_uploads_model->reset_ids();
				// $reset_ids_values_p = $this->Air_records_values_p_model->reset_ids();
				// $reset_ids_values_p_min = $this->Air_records_values_p_min_model->reset_ids();
				// $reset_ids_values_p_max = $this->Air_records_values_p_max_model->reset_ids();

				echo json_encode(array("success" => true, "message" => lang("forecast_data_clean_msj")));
			} else {
				echo json_encode(array("message" => lang("no_forecast_data_clean_msj")));
			}

		} else {
			echo json_encode(array("message" => lang("no_forecast_data_clean_msj")));
		}

	}
    
	function enviar_correo_verificacion(){

		$this->cron_job->enviar_correo_verificacion();
		$current_time = strtotime(get_current_utc_time());
        $this->Settings_model->save_setting("last_cron_job_time", $current_time);
	}
}

/* End of file Cron.php */
/* Location: ./application/controllers/Cron.php */