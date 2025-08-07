<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	
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
	
    public function index() {
		
        if ($this->login_user->user_type === "staff") {
            //check which widgets are viewable to current logged in user
			
            $show_timeline = get_setting("module_timeline") ? true : false;
            $show_attendance = get_setting("module_attendance") ? true : false;
            $show_event = get_setting("module_event") ? true : false;
            $show_invoice = get_setting("module_invoice") ? true : false;
            $show_expense = get_setting("module_expense") ? true : false;
            $show_ticket = get_setting("module_ticket") ? true : false;
            $show_project_timesheet = get_setting("module_project_timesheet") ? true : false;

            $view_data["show_timeline"] = $show_timeline;
            $view_data["show_attendance"] = $show_attendance;
            $view_data["show_event"] = $show_event;
            $view_data["show_project_timesheet"] = $show_project_timesheet;

            $access_expense = $this->get_access_info("expense");
            $access_invoice = $this->get_access_info("invoice");

            $access_ticket = $this->get_access_info("ticket");
            $access_timecards = $this->get_access_info("attendance");

            $view_data["show_invoice_statistics"] = false;
            $view_data["show_ticket_status"] = false;
            $view_data["show_income_vs_expenses"] = false;
            $view_data["show_clock_status"] = false;

            
            //check module availability and access permission to show any widget

            if ($show_invoice && $show_expense && $access_expense->access_type === "all" && $access_invoice->access_type === "all") {
                $view_data["show_income_vs_expenses"] = true;
            }

            if ($show_invoice && $access_invoice->access_type === "all") {
                $view_data["show_invoice_statistics"] = true;
            }

            if ($show_ticket && $access_ticket->access_type === "all") {
                $view_data["show_ticket_status"] = true;
            }

            if ($show_attendance && $access_timecards->access_type === "all") {
                $view_data["show_clock_status"] = true;
            }

            $this->template->rander("dashboard/index", $view_data);

        } else {
            //client's dashboard
			if($this->session->project_context){
				redirect('home');
			}else{
				redirect('inicio_projects');
			}
        }
    }

    function view($id_proyecto = 0) {
        // Verificar si el usuario tiene acceso al proyecto
        $this->member_allowed($id_proyecto);

        $view_data = array();

        if($id_proyecto){
            $this->session->set_userdata('project_context', $id_proyecto);
        }

        $id_air_sector = $id_proyecto;

        $id_cliente = $this->login_user->client_id;

        ### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_dashboard');

        // Obtener el sector utilizando el id_proyecto
        $sector = $this->Air_sectors_model->get_one($id_air_sector);
        $view_data["sector"] = $sector;

        // Filtrar estaciones por el sector (id_proyecto)
        $stations = $this->Air_stations_model->get_details(array(
            "id_air_sector" => $id_air_sector  // Usar el id_proyecto directamente para filtrar las estaciones
        ))->result();

        $view_data["stations"] = $stations;

        // Cargar la vista con los datos
        //$this->template->rander("dashboard/client_dashboard", $view_data);
        redirect("report_sqm_variables");
    }


    /*function view($id_proyecto = 0){

        $this->member_allowed($id_proyecto);

        $view_data = array();

        if($id_proyecto){
            $this->session->set_userdata('project_context', $id_proyecto);
        }
        $id_cliente = $this->login_user->client_id;

        ### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_dashboard');

        $sector = $this->Air_sectors_model->get_one(1); // Mina
        $view_data["sector"] = $sector;


        $stations = $this->Air_stations_model->get_details(array(
            "ids" => CONST_ARRAY_NO_EYE3_STATIONS_IDS
            "id_air_sector" => $sector->id
		))->result();

		// ORDENAR EL ARREGLO DE OBJETOS DE ESTACIONES POR ID EN EL ORDEN SOLICITADO: array(2, 1, 3, 4, 13)
		usort($stations, function($objeto1, $objeto2){
			$posicion1 = array_search($objeto1->id, array(1, 2, 3, 4, 5, 6));
			$posicion2 = array_search($objeto2->id, array(1, 2, 3, 4, 5, 6));
			return $posicion1 - $posicion2;
		});

		$view_data["stations"] = $stations;

		$this->template->rander("dashboard/client_dashboard", $view_data);
	}*/

	/*Valida si usuario tiene permiso*/
	function member_allowed($project_id){
		$user_id = $this->login_user->id;
		$project_rel_member = (array)$this->Project_members_model->get_one_where(array("user_id" =>$user_id ,"project_id" => $project_id,"deleted" => 0));
		if(empty(array_filter($project_rel_member))){
			redirect("forbidden");
		}
	}

	function get_widget_by_station(){

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$view_data = array();

		$id_station = $this->input->post("id_station");
		$station = $this->Air_stations_model->get_one($id_station);
		$view_data["station"] = $station;

		$variable = $this->Air_variables_model->get_one(9); // PM10
		$view_data["variable"] = $variable;

		// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
		$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$first_datetime = new DateTime($first_datetime);
		$first_datetime->setTime(0,0,0);
		// $first_datetime = $first_datetime->modify('-24 hours');
		$first_datetime = $first_datetime->format("Y-m-d H:i");

		$last_datetime = new DateTime($first_datetime);
		$last_datetime = $last_datetime->modify('+72 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");

		$view_data["first_datetime"] = $first_datetime;
		

		// ARRAY CON LAS FECHAS Y HORAS ENTRE LA PRIMERA Y ÚLTIMA FECHA DE CONSULTA, PARA EL RANGO DE FECHAS DE CALHEATMAPS
		$period = new DatePeriod(
			new DateTime($first_datetime),
			new DateInterval('PT1H'),
			new DateTime($last_datetime)
		);

		$array_period = array();
		$array_times = array();
		$previous_date = $first_date;

		foreach($period as $datetime){
			$date = $datetime->format("Y-m-d");
			$hour = $datetime->format("H");
			if($previous_date == $date){
				$array_times[] = $hour;
			} else {
				$array_times = array();
				$array_times[] = $hour;
			}
			$array_period[$date] = $array_times;
			$previous_date = $date;
		}


		// CONFIGURACIÓN DE UNIDADES DE REPORTE
		$id_unit = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, 
			"id_proyecto" => $id_proyecto, 
			"id_tipo_unidad" => $variable->id_unit_type,
			"deleted" => 0
		))->id_unidad;
		$unit = $this->Unity_model->get_one($id_unit);
		$view_data["unit"] = $unit;

		// SI HAY AL MENOS UNA ESTACIÓN, BUSCA EL REGISTRO ASOCIADO AL CLIENTE / PROYECTO / SECTOR / ESTACIÓN / MODELO MACHINE LEARNING / TIPO DE REGISTRO: PRONÓSTICO
		
		$ids_records = array();
		$air_records = $this->Air_records_model->get_details(array(
			"id_client"=> $id_cliente,
			"id_project" => $id_proyecto,
			"id_air_sector" => $station->id_air_sector,
			"id_air_station" => $station->id,
			//"id_air_model" => 1, // MACHINE LEARNING
			"id_air_record_type" => 2 // PRONÓSTICO
		))->result();

		foreach($air_records as $air_record){
			$ids_records[] = $air_record->id;
		}
		

		$array_values_p = array();
		$array_ranges_p = array();
		$array_porc_conf_p = array();
		$array_values_models_p = array();

		// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN CALHEATMAP
		$config_options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
			"id_client_submodule" => 0, // SIN SUBMÓDULO
			"alert_config" => array(
				"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
				"id_air_station" => $station->id,
				"id_air_sector" => $station->id_air_sector,
				"id_air_variable" => $variable->id
			),
		);
		$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();

		$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		$array_alerts_colors = array();
		$array_alerts_ranges = array();
		$array_alerts = array();

		if(count($alert_config_forecast)){
			$alert_config = $alert_config_forecast->alert_config;
			if(count($alert_config)){
				foreach($alert_config as $config){

					if($config->nc_active){
						$array_alerts_colors[] = $config->nc_color;
						$array_alerts_ranges[] = $config->min_value;
						$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
					}
					
				}
			}
		}

		$view_data["array_alerts_colors"] = $array_alerts_colors;
		array_shift($array_alerts_ranges);
		$view_data["array_alerts_ranges"] = $array_alerts_ranges;


		// VALOR DE "CONFIABILIDAD PRÓXIMA HORA"
		$today = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$today = new DateTime($today);
		$today_date = $today->format("Y-m-d");
		$next_hour = $today->modify("+1 hours");
		$next_hour = $next_hour->format("H");
		$view_data["next_hour"] = $next_hour.":00";

		$value_p_next_hour = $this->Air_records_values_p_porc_conf_model->get_max_reliability_data_per_hour(array(
			"id_variable" => $variable->id,
			"ids_records" => $ids_records,
			//"id_record" => 5,
			"date" => "$today_date",
			"hour" => "$next_hour"
		))->row();

		$porc_conf_next_hour = $value_p_next_hour->porc_conf ? $value_p_next_hour->porc_conf : 0;
		$view_data["value_p_next_hour"] = $value_p_next_hour;
		$view_data["porc_conf_next_hour"] = to_number_project_format($porc_conf_next_hour, $id_proyecto);
		$view_data["model_next_hour"] = lang($value_p_next_hour->model_name);
		// FIN VALOR DE "CONFIABILIDAD PRÓXIMA HORA"

		foreach($array_period as $date => $hours){

			$array_data_times_values = array();
			$array_data_times_ranges = array();
			$array_data_times_values_porc_conf = array();
			$array_data_times_values_models = array();

			foreach($hours as $hour){

				$value_p = $this->Air_records_values_p_porc_conf_model->get_max_reliability_data_per_hour(array(
					"id_variable" => $variable->id,
					"ids_records" => $ids_records,
					//"id_record" => 5,
					"date" => "$date",
					"hour" => "$hour"
				))->row();

				if($value_p->id){

					$range = "-";
					$prev_min_value = 0;
					foreach($array_alerts as $alert){
						if($value_p->value <= $alert["min_value"]){
							if($prev_min_value){
								$range = lang("between")." ".to_number_project_format($prev_min_value, $id_proyecto)." - ".to_number_project_format($alert["min_value"], $id_proyecto);
							} else {
								$range = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($alert["min_value"], $id_proyecto);
							}
							break;
						}
						$prev_min_value = $alert["min_value"];
					}

					if($value_p->value > end($array_alerts)["min_value"]){
						$range = lang("more_than")." ".to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
					}

					$array_data_times_values[$hour] = $value_p->value;
					$array_data_times_ranges[$hour] = $range;
					$array_data_times_values_porc_conf[$hour] = $value_p->porc_conf;
					$array_data_times_values_models[$hour] = lang($value_p->model_name);

				} else {

					$array_data_times_values[$hour] = 0;
					$array_data_times_values_porc_conf[$hour] = 0;
					$array_data_times_values_models[$hour] = lang("no_information_available");


					if($array_alerts[0]["min_value"] > 0){
						$array_data_times_ranges[$hour] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
					} else {
						$array_data_times_ranges[$hour] = lang("between")." ".to_number_project_format(0, $id_proyecto)." - ".to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
					}

				}

			}

			$array_values_p[$date] = $array_data_times_values;
			$array_ranges_p[$date] = $array_data_times_ranges;
			$array_porc_conf_p[$date] = $array_data_times_values_porc_conf;
			$array_values_models_p[$date] = $array_data_times_values_models;

		}

		$view_data["array_values_p"] = $array_values_p;
		$view_data["array_ranges_p"] = $array_ranges_p;
		$view_data["array_porc_conf_p"] = $array_porc_conf_p;
		$view_data["array_values_models_p"] = $array_values_models_p;

		echo $this->load->view("dashboard/client_dashboard_widget", $view_data, true);
		// echo $this->load->view("dashboard/(calheatmap test)client_dashboard", $view_data, true);

	}

}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */