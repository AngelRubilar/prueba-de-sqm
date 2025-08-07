<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron_job {

    private $today = null;
    private $ci = null;
	
    function run() {
		
		$this->today = get_today_date();
    	$this->ci = get_instance();
		
		// LLAMADA AL HISTÓRICO DE NOTIFICACIONES PARA ENVIAR CORREOS
		$ayn_notif_historical = $this->ci->AYN_Notif_historical_model->get_all_where(array(
			//"module_level" => "general",
			"is_email_sended" => 0,
			"deleted" => 0
		))->result();
		
		foreach($ayn_notif_historical as $notif_historical){
			
			$data = array("is_email_sended" => 1);
			$this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
			
			if($notif_historical->module_level == "general"){
				
				$notif_general = $this->ci->AYN_Notif_general_model->get_one($notif_historical->id_notif_general);
				if($notif_general->email_notification){ // Si la configuración está seteada para enviar correo de notificación, se envía el correo a cada usuario
									
					$notif_historical_users = $this->ci->AYN_Notif_historical_users_model->get_all_where(array(
						"id_notif_historical" => $notif_historical->id,
						"deleted" => 0
					))->result();
					
					foreach($notif_historical_users as $historical_user){
						$id_user_action = $notif_historical->id_user;
						$id_user_to_notify = $historical_user->id_user;
						if($id_user_action != $id_user_to_notify){
							$send_email = $this->_send_email_notifications($notif_historical, $historical_user->id_user);
						}
					}
					/*
					if($send_email){
						$data = array("is_email_sended" => 1);
						$notif_historical = $this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
					}
					*/
				}
				
			}
			
			if($notif_historical->module_level == "project"){
				
				$notif_general = $this->ci->AYN_Notif_projects_clients_model->get_one($notif_historical->id_notif_projects_clients);
				if($notif_general->email_notification){ // Si la configuración está seteada para enviar correo de notificación, se envía el correo a cada usuario
									
					$notif_historical_users = $this->ci->AYN_Notif_historical_users_model->get_all_where(array(
						"id_notif_historical" => $notif_historical->id,
						"deleted" => 0
					))->result();

					foreach($notif_historical_users as $historical_user){
						$id_user_action = $notif_historical->id_user;
						$id_user_to_notify = $historical_user->id_user;
						if($id_user_action != $id_user_to_notify){
							$send_email = $this->_send_email_notifications($notif_historical, $historical_user->id_user);
						}
					}
					/*
					if($send_email){
						$data = array("is_email_sended" => 1);
						$notif_historical = $this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
					}
					*/
				}
			
			}
			
			if($notif_historical->module_level == "admin"){
				
				$notif_general = $this->ci->AYN_Notif_projects_admin_model->get_one($notif_historical->id_notif_projects_admin);
				if($notif_general->email_notification){ // Si la configuración está seteada para enviar correo de notificación, se envía el correo a cada usuario
									
					$notif_historical_users = $this->ci->AYN_Notif_historical_users_model->get_all_where(array(
						"id_notif_historical" => $notif_historical->id,
						"deleted" => 0
					))->result();

					foreach($notif_historical_users as $historical_user){
						$id_user_action = $notif_historical->id_user;
						$id_user_to_notify = $historical_user->id_user;
						if($id_user_action != $id_user_to_notify){
							$send_email = $this->_send_email_notifications($notif_historical, $historical_user->id_user);
						}
					}
					/*
					if($send_email){
						$data = array("is_email_sended" => 1);
						$notif_historical = $this->ci->AYN_Notif_historical_model->save($data, $notif_historical->id);
					}
					*/
				}
			
			}

		}
		
		// LLAMADA AL HISTÓRICO DE ALERTAS PARA ENVIAR CORREOS
		$ayn_alert_historical = $this->ci->AYN_Alert_historical_model->get_all_where(array(
			//"module_level" => "general",
			"is_email_sended" => 0,
			"deleted" => 0
		))->result();
		
		foreach($ayn_alert_historical as $alert_historical){
						
			$alert_config_historical = json_decode($alert_historical->alert_config, TRUE);
			$suma_elementos = $alert_config_historical["suma_elementos"];

			$alert_projects_config = $this->ci->AYN_Alert_projects_model->get_one($alert_historical->id_alert_projects);
			$alert_config_config = json_decode($alert_projects_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_config["risk_value"];
			$valor_umbral = $alert_config_config["threshold_value"];
			
			//if($alert_projects_config->risk_email_alert && ($suma_elementos >= $valor_riesgo) && ($suma_elementos < $valor_umbral) )
			if($alert_projects_config->risk_email_alert || $alert_projects_config->threshold_email_alert){
					
				$alert_historical_users = $this->ci->AYN_Alert_historical_users_model->get_all_where(array(
					"id_alert_historical" => $alert_historical->id,
					"deleted" => 0
				))->result();
				
				if(count($alert_historical_users)){
					
					$data = array("is_email_sended" => 1);
					$this->ci->AYN_Alert_historical_model->save($data, $alert_historical->id);
					
					foreach($alert_historical_users as $alert_user){
						$send_email = $this->_send_email_alerts($alert_historical, $alert_projects_config, $alert_user->id_user);
					}
					
				}
				/*
				if($send_email){ // Si se envía el correo, se setea is_email_sended en 1
					$data = array("is_email_sended" => 1);
					$save_alert_historical = $this->ci->AYN_Alert_historical_model->save($data, $alert_historical->id);
				}
				*/
			}
			
		}

	}
	
	// Alertas MIMAire
	function run_air_alerts($id_proyecto){

		$this->ci = get_instance();

		$actual_date = date("Y-m-d H:i:s");
		$actual_date = convert_date_utc_to_local($actual_date, "Y-m-d");

		$alert_historical_air = $this->ci->AYN_Alert_historical_air_model->get_one_where(array(
			"alert_date" => $actual_date,
			"deleted" => 0
		));
		
		// Si en la fecha actual no se ha enviado los correos de alerta
		if(!$alert_historical_air->id){

			//$id_proyecto = $this->ci->session->project_context;
			$id_cliente = $this->ci->Projects_model->get_one($id_proyecto)->client_id;
			
			// Llamar a la configuración de Alertas de Pronóstico
			$array_alerts_forecast = array();
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => 14, // Módulo de Pronóstico
				"id_client_submodule" => 0, // Sin submódulo
				"alert_config" => array(
					"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
				),
			);

			$alerts_config_air_forecast_alerts = $this->ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->result();

			// ORDENAR EL ARREGLO DE OBJETOS DE ESTACIONES POR ID EN EL ORDEN SOLICITADO: array(2, 3, 4, 1, 13)
			usort($alerts_config_air_forecast_alerts, function($objeto1, $objeto2){
				$posicion1 = array_search($objeto1->id_station, array(2, 3, 4, 1, 13));
				$posicion2 = array_search($objeto2->id_station, array(2, 3, 4, 1, 13));
				return $posicion1 - $posicion2;
			});

			foreach($alerts_config_air_forecast_alerts as $alert_config_air_forecast_alerts){

				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);

				if(count($alert_config_forecast)){
					$alert_config = $alert_config_forecast->alert_config;
					if(count($alert_config)){
						foreach($alert_config as $config){
							$array_alerts_forecast[$alert_config_forecast->id_air_sector][$alert_config_forecast->id_air_station][$alert_config_forecast->id_air_variable][] = array(
								"nc_active" => $config->nc_active, 
								"nc_name" => $config->nc_name, 
								"nc_color" => $config->nc_color, 
								"min_value" => $config->min_value
							);
						}
					}
				}

			}

			// Llamar a la configuración de Plan de Acción
			$array_alerts_ap = array();
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => 14, // Módulo de Pronóstico
				"id_client_submodule" => 0, // Sin submódulo
				"alert_config" => array(
					"air_config" => "action_plan", // Acordeón Plan de Acción
				),
			);

			$alerts_config_air_action_plan = $this->ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->result();
			
			foreach($alerts_config_air_action_plan as $alert_config_air_action_plan){

				$alert_config_action_plan = json_decode($alert_config_air_action_plan->alert_config);

				if(count($alert_config_action_plan)){
					$alert_config = $alert_config_action_plan->alert_config;
					if(count($alert_config)){
						foreach($alert_config as $config){

							if($config->ap_active && $config->ap_email){
								$alert_users = $this->ci->AYN_Alert_projects_users_model->get_all_where(array(
									"id_alert_project" => $alert_config_air_action_plan->id,
									"deleted" => 0
								))->result();
								$array_alerts_ap_users = array(); // Arreglo para guardar los usuarios a notificar
								foreach($alert_users as $alert_user){
									$array_alerts_ap_users[] = $alert_user->id_user;
								}
							}
							
							$array_alerts_ap[$alert_config_action_plan->id_air_sector][$alert_config_action_plan->id_air_station][$alert_config_action_plan->id_air_variable][] = array(
								"ap_active" => $config->ap_active, 
								"ap_action_plan" => $config->ap_action_plan, 
								"ap_email" => $config->ap_email, 
								"ap_web" => $config->ap_web,
							);

							$array_alerts_ap[$alert_config_action_plan->id_air_sector][$alert_config_action_plan->id_air_station][$alert_config_action_plan->id_air_variable]["users"] = count($array_alerts_ap_users) ? $array_alerts_ap_users : array();

						}
					}
				}

			}
			
			// Array con datos de configuración
			$array_alerts_final = array();
			foreach($array_alerts_forecast as $id_sector => $alerts_forecast){

				foreach($alerts_forecast as $id_estacion => $alert_forecast){

					$air_station = $this->ci->Air_stations_model->get_one($id_estacion);

					//if($air_station->is_receptor == 0){ // Si la estación no es receptora

						foreach($alert_forecast as $id_variable => $alerts_f){

							$alerts_ap = $array_alerts_ap[$id_sector][$id_estacion][$id_variable];

							foreach($alerts_ap as $index_alert => $alert_ap){

								$alert_f = $alerts_f[$index_alert];

								//if($alert_f["nc_active"] && $alert_ap["ap_active"] && $alert_ap["ap_email"]){
									$array_alerts_final[$id_sector][$id_estacion][$id_variable][] = array(
										"nc_active" => $alert_f["nc_active"], "nc_name" => $alert_f["nc_name"], "nc_color" => $alert_f["nc_color"], "min_value" => $alert_f["min_value"],
										"ap_active" => $alert_ap["ap_active"], "ap_action_plan" => $alert_ap["ap_action_plan"], "ap_email" => $alert_ap["ap_email"], "ap_web" => $alert_ap["ap_web"],
									);

									$array_alerts_final[$id_sector][$id_estacion][$id_variable]["users"] = $alerts_ap["users"];

								//}

							}	
		
						} // Fin foreach $alert_forecast

					//} // Fin if $air_station->is_receptor == 0

				} // Fin foreach $alerts_forecast
				
			} // Fin foreach $array_alerts_forecast
			

			$array_forecast_email_data = array();
			foreach($array_alerts_final as $id_sector => $sectors_alerts){
				
				foreach($sectors_alerts as $id_estacion => $station_alerts){

					// Modelo MACHINE LEARNING
					$air_record = $this->ci->Air_records_model->get_details(array(
						"id_client"=> $id_cliente,
						"id_project" => $id_proyecto,
						"id_air_sector" => $id_sector,
						"id_air_station" => $id_estacion,
						"id_air_model" => 1, // Modelo MACHINE LEARNING
						"id_air_record_type" => 2 // Pronóstico
					))->row();

					if($air_record->id){

						foreach($station_alerts as $id_variable => $variable_alerts){

							$values_p = $this->ci->Air_records_values_p_model->get_last_upload_data_1D_by_date(array(
								"id_variable" => $id_variable,
								"id_record" => $air_record->id,
							))->result();
		
							if(count($values_p)){

								$actual_date = date("Y-m-d H:i:s");
								$actual_date = convert_date_utc_to_local($actual_date, "Y-m-d");
								$hay_data = false;
								foreach($values_p as $value_p){ // últimos valores de la variable (72 hrs)
								
									$earlier = new DateTime($value_p->date);
									$later = new DateTime($actual_date);
									$date_diff = $later->diff($earlier)->format("%a");
						
									if($value_p->date > $actual_date && $date_diff == 1){ // si hay Pronósticos del día siguiente al actual (próximas 24 horas)
										$hay_data = true;
									}

								}

								if(!$hay_data){
									$array_forecast_email_data = array();
								} else {
									// [1] es el id del modelo Machine Learning
									$array_forecast_email_data[$id_sector][$id_estacion][$id_variable][1] = $this->_set_array_forecast_email_data($id_sector, $id_estacion, $id_variable, $values_p, $variable_alerts);
								}

								
							} // Fin if count($values_p)

						} // Fin foreach $station_alerts
						
					} // Fin if $air_records->id


					// // Modelo Estadístico
						// $air_record = $this->ci->Air_records_model->get_details(array(
						// 	"id_client"=> $id_cliente,
						// 	"id_project" => $id_proyecto,
						// 	"id_air_sector" => $id_sector,
						// 	"id_air_station" => $id_estacion,
						// 	"id_air_model" => 2, // Modelo Estadístico
						// 	"id_air_record_type" => 2 // Pronóstico
						// ))->row();

						// if($air_record->id){

						// 	foreach($station_alerts as $id_variable => $variable_alerts){

						// 		$values_p = $this->ci->Air_records_values_p_model->get_last_upload_data_1D_by_date(array(
						// 			"id_variable" => $id_variable,
						// 			"id_record" => $air_record->id,
						// 		))->result();
			
						// 		if(count($values_p)){
						// 			$array_forecast_email_data[$id_sector][$id_estacion][$id_variable][2] = $this->_set_array_forecast_email_data($id_sector, $id_estacion, $id_variable, $values_p, $variable_alerts);
						// 		} // Fin if count($values_p)

						// 	} // Fin foreach $station_alerts
							
						// } // Fin if $air_records->id


						// // Modelo Numérico
						// $air_record = $this->ci->Air_records_model->get_details(array(
						// 	"id_client"=> $id_cliente,
						// 	"id_project" => $id_proyecto,
						// 	"id_air_sector" => $id_sector,
						// 	"id_air_station" => $id_estacion,
						// 	"id_air_model" => 3, // Modelo Numérico
						// 	"id_air_record_type" => 2 // Pronóstico
						// ))->row();

						// if($air_record->id){

						// 	foreach($station_alerts as $id_variable => $variable_alerts){

						// 		$values_p = $this->ci->Air_records_values_p_model->get_last_upload_data_1D_by_date(array(
						// 			"id_variable" => $id_variable,
						// 			"id_record" => $air_record->id,
						// 		))->result();
			
						// 		if(count($values_p)){
						// 			$array_forecast_email_data[$id_sector][$id_estacion][$id_variable][3] = $this->_set_array_forecast_email_data($id_sector, $id_estacion, $id_variable, $values_p, $variable_alerts);
						// 		} // Fin if count($values_p)

						// 	} // Fin foreach $station_alerts
							
					// } // Fin if $air_records->id


					

				} // Fin foreach $sectors_alerts

			} // Fin foreach $array_alerts_final

			if(count($array_forecast_email_data)){

				// $send_email_air_forecast_alert = $this->_send_email_air_forecast_alert($id_proyecto, 1, $array_forecast_email_data);
				$send_email_air_forecast_alert = $this->_send_email_air_forecast_alert_cc($id_proyecto, 1, $array_forecast_email_data);

			} else {

				$array_forecast_email_data = array("no_data" => true);
				foreach($array_alerts_final as $id_sector => $sectors_alerts){
					foreach($station_alerts as $id_variable => $variable_alerts){
						foreach($variable_alerts["users"] as $id_user){
							$array_forecast_email_data[$id_user] = $id_user;
						}
					}
				}
				// $send_email_air_forecast_alert = $this->_send_email_air_forecast_alert($id_proyecto, 1, $array_forecast_email_data);
				$send_email_air_forecast_alert = $this->_send_email_air_forecast_alert_cc($id_proyecto, 1, $array_forecast_email_data);
			}
			
		} // Fin if $alert_historical_air->id

	}

	private function _send_email_notifications($notif_historical, $id_user_to_notify){
		
		// Se envía correo a los usuarios del histórico menos al usuario que realizó la acción
		$send_app_mail = FALSE;
		$module_level = $notif_historical->module_level;
		$id_user_action = $notif_historical->id_user;
		$event = $notif_historical->event;
		$id_element = $notif_historical->id_element;
		$user_action = $this->ci->Users_model->get_one($id_user_action);
		$user_to_notify = $this->ci->Users_model->get_one($id_user_to_notify);
		$notified_date = $notif_historical->notified_date;
		$massive = $notif_historical->massive;
			
		if($module_level == "general" && $id_user_action != $id_user_to_notify){
			
			$email_template = $this->ci->Email_templates_model->get_final_template("ayn_notification_general");
			$id_module = $notif_historical->id_client_context_module;
			$id_submodule = $notif_historical->id_client_context_submodule;
			$module_name = lang($this->ci->Client_context_modules_model->get_one($id_module)->contexto);
			$submodule_name = $this->ci->Client_context_modules_model->get_one($id_module)->name;
			
			$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
			$parser_data["USER_ACTION_NAME"] = $user_action->first_name." ".$user_action->last_name;
			$parser_data["MODULE_NAME"] = $module_name;
			$datetime_format = get_setting_client_mimasoft($notif_historical->id_client, 'date_format')." ".set_time_format_client($notif_historical->id_client);
			$parser_data["NOTIFIED_DATE"] = convert_date_utc_to_local_client_mimasoft($notified_date, $datetime_format, $notif_historical->id_client);
			$parser_data["SITE_URL"] = get_uri();
			$parser_data["CONTACT_URL"] = get_uri("contact");
			$parser_data_signature["SITE_URL"] = get_uri();
			$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
			$parser_data["SIGNATURE"] = $signature_message;
				
			// Acuerdos Territorio - Beneficiarios: 2 | Acuerdos Distribución - Beneficiarios: 6
			if(($id_module == "2" || $id_module == "6") && $id_submodule == "0"){
				
				if($event == "add"){
					$event_message = lang("added_an_item");	
				} elseif($event == "edit"){
					$event_message = lang("edited_an_item");
				} elseif($event == "delete"){
					$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
				}
				
				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["SUBMODULE_NAME"] = $submodule_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Acuerdos Territorio - Actividades: 3 | Acuerdos Distribución - Actividades: 7
			if(($id_module == "3" || $id_module == "7")){
				
				if($event == "add"){
					$event_message = lang("added_an_item");
				} elseif($event == "edit"){
					$event_message = lang("edited_an_item");
				} elseif($event == "delete"){
					$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
				}

				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("activities_record");
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);

				//echo $message;

			}
			
			// Acuerdos Territorio - Convenios y Donaciones: 4 | Acuerdos Distribución - Convenios y Donaciones: 8
			if($id_module == "4" || $id_module == "8"){
								
				// Pestaña Información
				if($event == "information_add" || $event == "information_edit" || $event == "information_delete" || $event == "information_audit"){ 
					
					if($event == "information_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "information_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "information_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					} elseif($event == "information_audit"){
						$event_message = lang("audited_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("agreements_record")." | ".lang("information");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Pestaña Configuración
				if($event == "configuration_add" || $event == "configuration_edit" || $event == "configuration_delete" || $event == "configuration_close"){
					
					if($event == "configuration_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "configuration_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "configuration_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					} elseif($event == "configuration_close"){
						$event_message = lang("closed_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					if($id_module == "4"){
						$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("agreements_record")." | ".lang("configuration");
					} elseif($id_module == "8"){
						$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("configuration");
					}
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
				
				}
				
				// Pestaña Registro de Ejecución
				if($event == "execution_record_add" || $event == "execution_record_edit" || $event == "execution_record_delete"){

					if($event == "execution_record_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "execution_record_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "execution_record_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("execution_record");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Pestaña Registro de Pago
				if($event == "payment_record_edit"){
					
					$event_message = lang("edited_an_item");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("payment_record");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}

			}			
			
			// Acuerdos Territorio - Mantenedoras: 5 |  Acuerdos Distribución - Mantenedoras: 9
			if($id_module == "5" || $id_module == "9"){
								
				// Mantenedoras Sociedades
				if($event == "society_add" || $event == "society_edit" || $event == "society_delete"){
					
					if($event == "society_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "society_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "society_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("societies");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Mantenedoras Centrales
				if($event == "central_add" || $event == "central_edit" || $event == "central_delete"){ 
					
					if($event == "central_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "central_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "central_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("centrals");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Mantenedoras Tipos de Acuerdo
				if($event == "type_of_agreement_add" || $event == "type_of_agreement_edit" || $event == "type_of_agreement_delete"){ 
					
					if($event == "type_of_agreement_add"){
						$event_message = lang("added_an_item");
					} elseif($event == "type_of_agreement_edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "type_of_agreement_delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = $submodule_name." | ".lang("types_of_agreement");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
								
			}
			
			// Ayuda y Soporte
			if($id_module == "1"){
				if($id_submodule == "4" && $event == "send_email"){ 
					
					$event_message = lang("sent_form");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["SUBMODULE_NAME"] = lang("contact");
					
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
			}
						
		}
		
		if($module_level == "project" && $id_user_action != $id_user_to_notify){
			
			$email_template = $this->ci->Email_templates_model->get_final_template("ayn_notification_projects_clients");
			$id_module = $notif_historical->id_client_module;
			$id_submodule = $notif_historical->id_client_submodule;
			$module_name = $this->ci->Clients_modules_model->get_one($id_module)->name;
			$submodule_name = $this->ci->Clients_submodules_model->get_one($id_submodule)->name;
			$project = $this->ci->Projects_model->get_one($notif_historical->id_project);
			
			$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
			$parser_data["USER_ACTION_NAME"] = $user_action->first_name." ".$user_action->last_name;
			
			if($id_submodule){
				$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
			} else {
				$parser_data["MODULE_NAME"] = $module_name;
			}
			
			$datetime_format = get_setting_mimasoft($notif_historical->id_project, "date_format")." ".set_time_format($notif_historical->id_project);
			$parser_data["NOTIFIED_DATE"] = convert_date_utc_to_local_mimasoft($notified_date, $datetime_format, $notif_historical->id_project);
			$parser_data["SITE_URL"] = get_uri();
			$parser_data["CONTACT_URL"] = get_uri("contact");
			$parser_data_signature["SITE_URL"] = get_uri();
			$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
			$parser_data["SIGNATURE"] = $signature_message;
			
			// Registros Ambientales || Mantenedoras || Otros Registros
			if($id_module == "2" || $id_module == "3" || $id_module == "4"){
				
				
				if($id_module == "4" && ($event == "add_fixed_or" || $event == "edit_fixed_or" || $event == "delete_fixed_or")){
					$element = $this->ci->Fixed_form_values_model->get_one_where(array("id" => $id_element));
					$id_form = $element->id_formulario;
					$form_name = $this->ci->Forms_model->get_one($id_form)->nombre;
					if($event == "add_fixed_or"){
						$event = "add";
					}
					if($event == "edit_fixed_or"){
						$event = "edit";
					}
					if($event == "delete_fixed_or"){
						$event = "delete";
					}
				} else {
					$element = $this->ci->Form_values_model->get_one_where(array("id" => $id_element));
					$form_rel_project = $this->ci->Form_rel_project_model->get_one($element->id_formulario_rel_proyecto);
					$id_form = $form_rel_project->id_formulario;
					$form = $this->ci->Forms_model->get_one($id_form);
					$flujo = ($form->id_tipo_formulario == "1" && $form->flujo != "No Aplica") ? ' ('.$form->flujo.')' : "";
					$form_name = $this->ci->Forms_model->get_one($id_form)->nombre;
				}
				
				if($event == "add"){
					$event_message = lang("added_an_item");
				} elseif($event == "edit"){
					$event_message = lang("edited_an_item");
				} elseif($event == "delete"){
					$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
				}
				
				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["ELEMENT"] = " <strong>".$form_name."</strong>".$flujo;
				$parser_data["PROJECT_NAME"] = $project->title;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Compromisos
			if($id_module == "6"){
				
				// Evaluación de Compromisos RCA
				if($id_submodule == "4"){
					
					$element = $this->ci->Compromises_compliance_evaluation_rca_model->get_one_where(array("id" => $id_element));
					$valor_compromiso = $this->ci->Values_compromises_rca_model->get_one($element->id_valor_compromiso);
					
					if($event == "add"){
						$event_message = lang("added_an_evaluation");
					} elseif($event == "edit"){
						$event_message = lang("edited_an_evaluation");

					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $valor_compromiso->nombre_compromiso;
					$parser_data["PROJECT_NAME"] = $project->title;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
				// Evaluación de Compromisos Reportables
				if($id_submodule == "22"){
					
					$element = $this->ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array("id" => $id_element));
					$valor_compromiso = $this->ci->Values_compromises_reportables_model->get_one($element->id_valor_compromiso);
					$event_message = lang("edited_an_evaluation");
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $valor_compromiso->nombre_compromiso;
					$parser_data["PROJECT_NAME"] = $project->title;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
			}
			
			// Permisos
			if($id_module == "7"){
								
				$element = $this->ci->Permitting_procedure_evaluation_model->get_one_where(array("id" => $id_element));
				$valor_permiso = $this->ci->Values_permitting_model->get_one($element->id_valor_permiso);

				if($event == "add"){
					$event_message = lang("added_an_evaluation");
				} elseif($event == "edit"){
					$event_message = lang("edited_an_evaluation");
				}
				
				$parser_data["EVENT"] = strtolower($event_message);
				$parser_data["ELEMENT"] = $valor_permiso->nombre_permiso;
				$parser_data["PROJECT_NAME"] = $project->title;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
					
			}
			
			// Administración Cliente
			if($id_module == "11"){
				
				// Configuración Panel Principal
				if($id_submodule == "20"){
					
					$event_message = lang("edited_an_item");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $submodule_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
				
				}
				
				// Carga Masiva
				if($id_submodule == "21"){
					
					$element = $this->ci->Forms_model->get_one($id_element);
					$form_name = $element->nombre;
					
					$event_message = lang("added_elements_massively");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $form_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
				}
				
			}
			
			// Recordbook
			if($id_module == "12"){
				
				// Registros Recordbook
				if($id_submodule == "23"){
					
					$element = $this->ci->Recordbook_values_model->get_one_where(array("id" => $id_element));

					if($event == "add"){
						$event_message = lang("added_an_item");
					} elseif($event == "edit"){
						$event_message = lang("edited_an_item");
					} elseif($event == "delete"){
						$event_message = ($massive) ? lang("deleted_items_massively") : lang("deleted_an_item");
					}
					
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $submodule_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);

					//echo $message;

				}
				
				// Seguimiento del recordbook
				if($id_submodule == "24"){
					
					$element = $this->ci->Recordbook_monitoring_model->get_one_where(array("id" => $id_element));
					$event_message = lang("edited_an_item");
					$parser_data["EVENT"] = strtolower($event_message);
					$parser_data["ELEMENT"] = $submodule_name;
					$parser_data["PROJECT_NAME"] = $project->title;
					$parser_data["MODULE_NAME"] = $module_name;
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
					//echo $message;
					
				}
				
			}

		}
		
		// Notificaciones módulos nivel admin
		if($module_level == "admin"){
			
			$email_template = $this->ci->Email_templates_model->get_final_template("ayn_notification_projects_admin");
			$id_module = $notif_historical->id_admin_module;
			$id_submodule = $notif_historical->id_admin_submodule;
			$module_name = $this->ci->AYN_Admin_modules_model->get_one($id_module)->name;
			$submodule_name = $this->ci->AYN_Admin_submodules_model->get_one($id_submodule)->name;
			$project = $this->ci->Projects_model->get_one($notif_historical->id_project);
			
			$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
			$parser_data["USER_ACTION_NAME"] = $user_action->first_name." ".$user_action->last_name;
			$parser_data["MODULE_NAME"] = $module_name;
						
			$parser_data["NOTIFIED_DATE"] = format_to_datetime($notified_date);
			$parser_data["SITE_URL"] = get_uri();
			$parser_data["CONTACT_URL"] = get_uri("contact");
			$parser_data_signature["SITE_URL"] = get_uri();
			$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
			$parser_data["SIGNATURE"] = $signature_message;
			
			// Proyectos
			if($id_module == "4"){
				
				$element = $this->ci->Projects_model->get_one($id_element);
				
				$event_message = strtolower(lang("edited_the_item"));
				if($event == "project_edit_name"){
					$event_message .= ' '.lang("project_name");
				} elseif($event == "project_edit_auth_amb"){
					$event_message .= ' '.lang("environmental_authorization");
				} elseif($event == "project_edit_start_date"){
					$event_message .= ' '.lang("start_date");
				} elseif($event == "project_edit_end_date"){
					$event_message .= ' '.lang("term_date");
				} elseif($event == "project_edit_members"){
					$event_message .= ' '.lang("members");
				} elseif($event == "project_edit_desc"){
					$event_message .= ' '.lang("description");
				} elseif($event == "project_edit_status"){
					$event_message .= ' '.lang("status");
				} elseif($event == "project_edit_pu"){
					$event_message .= ' '.lang("unit_processes");
				} elseif($event == "project_edit_cat_impact"){
					$event_message .= ' '.lang("footprints");
				}

				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $element->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;

			}
			
			// Registros
			if($id_module == "5"){
				
				$element = $this->ci->Forms_model->get_one_where(array("id" => $id_element));
				$form_name = $element->nombre;
								
				if($event == "form_add"){
					$event_message = strtolower(lang("added_the_form"))." ".$form_name;
				} elseif($event == "form_edit_name"){
					$event_message = strtolower(lang("edited_the_item"))." ".lang("name")." ".lang("in")." ".lang("form")." ".$form_name;
				} elseif($event == "form_edit_cat"){
					$event_message = strtolower(lang("edited_the_item"))." ".lang("category")." ".lang("in")." ".lang("form")." ".$form_name;
				} elseif($event == "form_delete"){
					$event_message = strtolower(lang("deleted_an_item"))." ".lang("in")." ".$submodule_name;
					$event_message = ($massive) ? strtolower(lang("deleted_items_massively"))." ".lang("in")." ".$submodule_name : strtolower(lang("deleted_an_item"))." ".lang("in")." ".$submodule_name;
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Indicadores
			if($id_module == "7"){
				
				$element = $this->ci->Functional_units_model->get_one_where(array("id" => $id_element));
								
				if($event == "uf_add_element"){
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".$submodule_name;
				} elseif($event == "uf_edit_element"){
					$event_message = strtolower(lang("edited_an_item"))." ".lang("in")." ".$submodule_name;
				} elseif($event == "uf_delete_element"){
					$event_message = ($massive) ? strtolower(lang("deleted_items_massively"))." ".lang("in")." ".$submodule_name : strtolower(lang("deleted_an_item"))." ".lang("in")." ".$submodule_name;
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;
				
			}
			
			// Compromisos
			if($id_module == "8"){

				if($event == "comp_rca_add"){
					$element = $this->ci->Values_compromises_rca_model->get_one($id_element);
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".lang("compromises_rca");
				}
				
				if($event == "comp_rep_add"){
					$element = $this->ci->Values_compromises_reportables_model->get_one($id_element);
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".lang("compromises_rep");
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;

			}
			
			// Permisos
			if($id_module == "9"){
				
				if($event == "permitting_add"){
					$element = $this->ci->Values_permitting_model->get_one($id_element);
					$event_message = strtolower(lang("added_an_item"))." ".lang("in")." ".lang("permittings");
				}
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["PROJECT_NAME"] = $project->title;
				$parser_data["MODULE_NAME"] = $module_name;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message);
				//echo $message;

			}
			
		}
		
		return $send_app_mail;
		
	}
	
	private function _send_email_alerts($alert_historical, $alert_project_config, $id_user_to_alert){
		
		// Se envía correo a los usuarios del histórico menos al usuario que realizó la acción
		$send_app_mail = FALSE;
		$id_user_action = $alert_historical->id_user;
		$event = $alert_historical->event;
		$id_element = $alert_historical->id_element;
		$user_to_alert = $this->ci->Users_model->get_one($id_user_to_alert);
		$alert_date = format_to_datetime($alert_historical->alert_date);
		
		$email_template = $this->ci->Email_templates_model->get_final_template("ayn_alerts_admin");
		
		$id_module = $alert_historical->id_client_module;
		$id_submodule = $alert_historical->id_client_submodule;
		$module_name = $this->ci->Clients_modules_model->get_one($id_module)->name;
		$submodule_name = $this->ci->Clients_submodules_model->get_one($id_submodule)->name;
		$project = $this->ci->Projects_model->get_one($alert_historical->id_project);
		
		$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_alert->first_name." ".$user_to_alert->last_name;
		$parser_data["MODULE_NAME"] = $module_name;
		$parser_data["ALERT_DATE"] = $alert_date;
		$parser_data["PROJECT_NAME"] = $project->title;
		$parser_data["SITE_URL"] = get_uri();
		$parser_data["CONTACT_URL"] = get_uri("contact");
		$parser_data_signature["SITE_URL"] = get_uri();
		$signature_message = $this->ci->parser->parse_string($email_template->signature, $parser_data_signature, TRUE);
		$parser_data["SIGNATURE"] = $signature_message;
		
		$alerted_users = $this->ci->AYN_Alert_historical_users_model->get_all_where(array(
			"id_alert_historical" => $alert_historical->id,
			"deleted" => 0
		))->result();
		$html_alerted_users = "";
		foreach($alerted_users as $alerted_user){
			$user = $this->ci->Users_model->get_one($alerted_user->id_user);
			$user_name = $user->first_name." ".$user->last_name;
			$image_url = get_avatar($user->image);
			$avatar = anchor(get_uri("project_info/view_user_profile/".$user->id."/".$project->id), "<span style='width: 20px; height: 20px; display: inline-block; white-space: nowrap; margin-right: 10px;'><img width='20' height='20' src='$image_url' alt='...' style='height: auto; max-width: 100%; border-radius: 50%; -webkit-border-radius: 10px; -moz-border-radius: 10px;'></span>$user_name", array("title" => ""));
			$html_alerted_users .= $avatar;
			if(next($alerted_users)){
				$html_alerted_users .= "<br><br>";
			}
		}
		
		$parser_data["ALERTED_USERS"] = $html_alerted_users;

		
		// Registros Ambientales
		if($id_module == "2"){
			
			$element = $this->ci->Form_values_model->get_one_where(array("id" => $id_element));
			$form_rel_project = $this->ci->Form_rel_project_model->get_one($element->id_formulario_rel_proyecto);
			$id_form = $form_rel_project->id_formulario;
			$form_name = $this->ci->Forms_model->get_one($id_form)->nombre;
						
			$alert_config = json_decode($alert_historical->alert_config, TRUE);
			$categoria = $this->ci->Categories_model->get_one($alert_config["id_categoria"]);
			$alias_categoria = $this->ci->Categories_alias_model->get_one_where(array(
				"id_cliente" => $alert_historical->id_client,
				"id_categoria" => $categoria->id,
				"deleted" => 0
			));
			$nombre_categoria = ($alias_categoria->id) ? $alias_categoria->alias : $categoria->nombre;
			
			$unidad = $this->ci->Unity_model->get_one($alert_config["id_unidad"])->nombre;
			
			$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_project_field["risk_value"];
			$valor_umbral = $alert_config_project_field["threshold_value"];
			$suma_elementos = $alert_config["suma_elementos"];
									
			if( ($suma_elementos >= $valor_riesgo) && ($suma_elementos < $valor_umbral) ){
				
				$event_message = strtolower(lang("the_umbral_of"))." ".$nombre_categoria." - ".$valor_umbral." ".$unidad." ".lang("in")." ".lang("record")." ".$form_name." ".lang("is_close_to_being_exceeded");				
				$parser_data["MESSAGE_TYPE"] = lang("caution");
				$parser_data["EVENT"] = $event_message;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;
	
			}
			
			if($suma_elementos >= $valor_umbral){
				
				$event_message = strtolower(lang("the_umbral_of"))." ".$nombre_categoria." - ".$valor_umbral." ".$unidad." ".lang("in")." ".lang("record")." ".$form_name." ".lang("has_been_exceeded");				
				$parser_data["MESSAGE_TYPE"] = lang("alert");
				$parser_data["EVENT"] = $event_message;
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;

			}
			
		}
		
		if($id_module == "6"){ // Compromisos
			
			if($id_submodule == "4"){ // Evaluación de Compromisos RCA

				$alert_config = json_decode($alert_historical->alert_config, TRUE);
				$nombre_compromiso = $this->ci->Values_compromises_rca_model->get_one($alert_config["id_valor_compromiso"])->nombre_compromiso;
				
				$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
				$valor_riesgo = $alert_config_project_field["risk_value"];
				$valor_umbral = $alert_config_project_field["threshold_value"];
				$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
				$estado_evaluacion = $this->ci->Compromises_compliance_status_model->get_one($id_estado_evaluacion)->nombre_estado;
								
				$event_message = strtolower(lang("an_evaluation_has_been_entered"))." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_compromiso;
				
				$parser_data["EVENT"] = $event_message;
				$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
				if($id_estado_evaluacion == $valor_riesgo){
					$parser_data["MESSAGE_TYPE"] = lang("caution");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
					//echo $message;
				} elseif($id_estado_evaluacion == $valor_umbral) {
					$parser_data["MESSAGE_TYPE"] = lang("alert");
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
					$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
					//echo $message;
				}
				
			}
			
			if($id_submodule == "22"){ // Evaluación de Compromisos Reportables
								
				$alert_config = json_decode($alert_historical->alert_config, TRUE);
				
				/*
					Se debe generar una alerta X días antes de la planificación (riesgo) si no se ha ingresado fecha de ejecución.
					Se debe generar una alerta X días después de la planificación (umbral) si no se ha ingresado fecha de ejecución.
				*/
				if($alert_config["id_planificacion"]){ // Planificación
					
					$planificacion = $this->ci->Plans_reportables_compromises_model->get_one($alert_config["id_planificacion"]);
					$valor_compromiso_reportable = $this->ci->Values_compromises_reportables_model->get_one($planificacion->id_compromiso)->nombre_compromiso;
					$actual_date = date("Y-m-d H:i:s");
					$actual_date = convert_date_utc_to_local($actual_date, "Y-m-d");
					$fecha_planificacion = $planificacion->planificacion;
					$evaluacion = $this->ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array(
						"id_planificacion" => $planificacion->id,
						"deleted" => 0
					));
					
					if(!$evaluacion->modified_by){ // Si la evaluación no se ha editado, se genera la alerta
						
						$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
						$valor_riesgo = $alert_config_project_field["risk_value"];
						$valor_umbral = $alert_config_project_field["threshold_value"];
						
						$dif_dias = strtotime($fecha_planificacion) - strtotime($actual_date);
						$dif_dias = round($dif_dias / (60 * 60 * 24));
													
						if($dif_dias >= 0){ // Si fecha actual es menor o igual a fecha de planificacion (dentro del plazo)
							
							if($valor_riesgo >= $dif_dias){

								$cantidad_dias = $valor_riesgo;
								$event_message = strtolower(lang("according_to_the_planning"))." ".$planificacion->descripcion.", ".lang("in")." ".$cantidad_dias." ".lang("days")." ".lang("it_must_be_reported")." ".$valor_compromiso_reportable." (".format_to_date($fecha_planificacion, false).")";
								$parser_data["MESSAGE_TYPE"] = lang("reminder_caution");
								$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
								$parser_data["EVENT"] = $event_message;
								$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
								$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
								//echo $message;
								
							}
							
						} else { // fuera del plazo
							
							$dif_dias = $dif_dias * -1;
							if($valor_umbral <= $dif_dias){
								
								$cantidad_dias = $valor_umbral;
								$event_message = strtolower(lang("according_to_the_planning"))." ".$planificacion->descripcion.", ".lang("in")." ".$cantidad_dias." ".lang("days")." ".lang("it_should_have_been_reported")." ".$valor_compromiso_reportable." (".format_to_date($fecha_planificacion, false).")";
								$parser_data["MESSAGE_TYPE"] = lang("reminder_alert");
								$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
								$parser_data["EVENT"] = $event_message;
								$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
								$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
								//echo $message;
								
							}
						
						}

					}
					
				} else {
										
					$nombre_compromiso = $this->ci->Values_compromises_reportables_model->get_one($alert_config["id_valor_compromiso"])->nombre_compromiso;
					$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
					$valor_riesgo = $alert_config_project_field["risk_value"];
					$valor_umbral = $alert_config_project_field["threshold_value"];
					$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
					$estado_evaluacion = $this->ci->Compromises_compliance_status_model->get_one($id_estado_evaluacion)->nombre_estado;
					
					$event_message = strtolower(lang("an_evaluation_has_been_entered"))." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_compromiso;
					
					$parser_data["EVENT"] = $event_message;
					$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
					if($id_estado_evaluacion == $valor_riesgo){
						$parser_data["MESSAGE_TYPE"] = lang("caution");
						$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
						$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
						//echo $message;
					} elseif($id_estado_evaluacion == $valor_umbral) {
						$parser_data["MESSAGE_TYPE"] = lang("alert");
						$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
						$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
						//echo $message;
					}
					
				}
				
			}
			
		}
		
		if($id_module == "7"){ // Permisos
		
			$alert_config = json_decode($alert_historical->alert_config, TRUE);
			$nombre_permiso = $this->ci->Values_permitting_model->get_one($alert_config["id_valor_permiso"])->nombre_permiso;

			$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_project_field["risk_value"];
			$valor_umbral = $alert_config_project_field["threshold_value"];
			$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
			$estado_evaluacion = $this->ci->Permitting_procedure_status_model->get_one($id_estado_evaluacion)->nombre_estado;
			
			$event_message = strtolower(lang("an_evaluation_has_been_entered"))." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_permiso;
			
			$parser_data["EVENT"] = $event_message;
			$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
			if($id_estado_evaluacion == $valor_riesgo){
				$parser_data["MESSAGE_TYPE"] = lang("caution");
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;
			} elseif($id_estado_evaluacion == $valor_umbral) {
				$parser_data["MESSAGE_TYPE"] = lang("alert");
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
				//echo $message;
			}
			
		}
		
		if($id_module == "12"){ // Recordbook
			
			$valor_recordbook = $this->ci->Recordbook_values_model->get_one($id_element);
			$alert_config_project_field = json_decode($alert_project_config->alert_config, TRUE);
			$valor_riesgo = $alert_config_project_field["risk_value"];
			$valor_umbral = $alert_config_project_field["threshold_value"];

			$event_message = strtolower(lang("a_recordbook_record_has_been_entered"))." - ".$valor_recordbook->nombre." - ".lang("with")." ".lang($valor_recordbook->proposito_visita)." ".lang("as_visit_purpose");
			
			$parser_data["EVENT"] = $event_message;
			$parser_data["MODULE_NAME"] = $module_name." | ".$submodule_name;
			if($valor_recordbook->proposito_visita == $valor_riesgo){
				$parser_data["MESSAGE_TYPE"] = lang("caution");
			} elseif($valor_recordbook->proposito_visita == $valor_umbral) {
				$parser_data["MESSAGE_TYPE"] = lang("alert");
			}
			$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
			$send_app_mail = send_app_mail($user_to_alert->email, $email_template->subject, $message);
			//echo $message;
										
		}
		
		return $send_app_mail;
		
	}

	/* Métodos auxiliares para notificaciones de correo MIMAire */

	function _set_array_forecast_email_data($id_sector, $id_estacion, $id_variable, $values_p, $variable_alerts){

		$array_forecast_email_data = array();
		$actual_date = date("Y-m-d H:i:s");
		$actual_date = convert_date_utc_to_local($actual_date, "Y-m-d");

		foreach($values_p as $value_p){ // últimos valores de la variable (72 hrs)
								
			$earlier = new DateTime($value_p->date);
			$later = new DateTime($actual_date);
			$date_diff = $later->diff($earlier)->format("%a");

			if($value_p->date > $actual_date && $date_diff == 1){ // si hay Pronósticos del día siguiente al actual (próximas 24 horas)
				
				$array_porc_conf_by_time = array();
				foreach($value_p as $field => $value){

					if(strpos($field, 'time_porc_conf') !== false) {
						$array_porc_conf_by_time[substr($field, 15, 2)] = $value;	
					}

				}

				foreach($value_p as $field => $value){

					if (strpos($field, 'time_') !== false && strpos($field, 'time_min_') === false && strpos($field, 'time_max_') === false && strpos($field, 'time_porc_conf_') === false) { // time_ fields

						$array_forecast_email_data["users"] = $variable_alerts["users"];

						foreach($variable_alerts as $index => $alert){

							if( ($value > $alert["min_value"] && $value <= $variable_alerts[$index + 1]["min_value"]) && $alert["ap_active"] && $alert["ap_email"] && $alert["nc_active"]){

								$array_forecast_email_data[$field] = array(
									"nombre_alerta" => $alert["nc_name"],
									"rango" => lang("between") . " " . $alert["min_value"] . " - " . $variable_alerts[$index + 1]["min_value"],
									"porc_conf" => $array_porc_conf_by_time[substr($field, 5, 2)]
									//"valor" => "1 | " . $value
								);

								break;

							} elseif($value > end($variable_alerts)["min_value"] && count($variable_alerts) && end($variable_alerts)["ap_active"] && end($variable_alerts)["ap_email"] && end($variable_alerts)["nc_active"]){
								
								$array_forecast_email_data[$field] = array(
									"nombre_alerta" => end($variable_alerts)["nc_name"],
									"rango" => lang("more_than"). " " . end($variable_alerts)["min_value"],
									"porc_conf" => $array_porc_conf_by_time[substr($field, 5, 2)]
									//"valor" => "2 | " . $value
								);

								break;

							} else {

								if($value == $alert["min_value"] && $alert["ap_active"] && $alert["ap_email"] && $alert["nc_active"]){

									$array_forecast_email_data[$field] = array(
										"nombre_alerta" => $alert["nc_name"],
										"rango" => lang("between") . " " . $alert["min_value"] . " - " . $variable_alerts[$index + 1]["min_value"],
										"porc_conf" => $array_porc_conf_by_time[substr($field, 5, 2)]
										//"valor" => "3 | " . $value
									);

									break;

								} else {

									$index++;

									/*$array_forecast_email_data[$field] = array(
										"nombre_alerta" => lang("no_upcoming_events"),
										//"valor" => "4 | " . $value
									);*/

								}


							}

						}
						
					}

				} // Fin foreach $value_p
				
				break; // Para que no continue con el pronóstico (posterior a próximas 24 horas).
			}

			
			
		} // Fin foreach $values_p

		return $array_forecast_email_data;

	}

	function _send_email_air_forecast_alert($id_proyecto, $id_modelo, $array_forecast_email_data){

		$actual_date = date("Y-m-d H:i:s");
		$actual_date = convert_date_utc_to_local($actual_date, "Y-m-d");
		$tomorrow = date('Y-m-d H:i:s', strtotime($actual_date . ' +1 day'));

		$email_template = $this->ci->Email_templates_model->get_final_template("ayn_alerts_projects_air");

		$parser_data = array();
		$parser_data["ALERT_DATE"] = get_date_format($tomorrow, $id_proyecto);

		$proyecto = $this->ci->Projects_model->get_one($id_proyecto);
		$parser_data["PROJECT_NAME"] = $proyecto->title;
		
		$modelo = $this->ci->Air_models_model->get_one($id_modelo);
		$parser_data["MODEL_NAME"] = lang($modelo->name);

		$forecast_module = $this->ci->Clients_modules_model->get_one(14)->name;
		$parser_data["MODULE_NAME"] = $forecast_module;

		$parser_data["SITE_URL"] = get_uri();
		$parser_data["CONTACT_URL"] = get_uri("contact");
		$parser_data_signature["SITE_URL"] = get_uri();

		$signature_template = $this->ci->Email_templates_model->get_one_where(array("template_name" => "signature_air", "deleted" => 0));
		$signature = ($signature_template->custom_message) ? $signature_template->custom_message : $signature_template->default_message;
		$signature_message = $this->ci->parser->parse_string($signature, $parser_data_signature, TRUE);
		$parser_data["SIGNATURE"] = $signature_message;

		// CONSULTAR ÚLTIMO BOLETÍN SUBIDO Y DISPONIBILIZAR LINK PARA SU DESCARGA
		$id_form = 2; // Formulario dinámico creado para Boletines
		$id_field_file = 1; // Campo Archivo dinámico para PDF del Boletín
		$id_field_text = 2; // Campo Texto dinámico para texto del Boletín
		$last_bulletin = $this->ci->Form_values_model->get_last_value_of_form(array("id_form" => $id_form))->row();
		$bulletin_data = json_decode($last_bulletin->datos, true);
		$bulletin_text = $bulletin_data[$id_field_text];
		$parser_data["BULLETIN_TEXT"] = $bulletin_text;

		$bulletin_filename = $bulletin_data[$id_field_file];
		$bulletin_filepath = getcwd()."/files/mimasoft_files/client_1/project_1/form_".$id_form."/elemento_".$last_bulletin->id."/".$bulletin_filename;

		$email_options = array("attachments" => array(array("file_path" => $bulletin_filepath, "file_name" => remove_file_prefix($bulletin_filename))));

		// $link_last_bulletin = get_uri("other_records/download_last_bulletin");
		// $parser_data["LINK_LAST_BULLETIN"] = $link_last_bulletin;

		if($array_forecast_email_data["no_data"] == true){

			foreach($array_forecast_email_data as $index => $id_user){
				if($index != "no_data"){

					$user_to_notify = $this->ci->Users_model->get_one($id_user);
					$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
					$parser_data["HTML_FORECAST_TABLE"] = lang("no_upcoming_events");
		
					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);

					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message, $email_options);
					$email_options = array();

					$alert_historical_air_data = array(
						"id_alert_projects" => NULL,
						"id_user" => $id_user,
						"alert_date" => $actual_date
					);
	
					$save_alert_historical_air = $this->ci->AYN_Alert_historical_air_model->save($alert_historical_air_data);

				}
			}

		} else {

			$array_forecast_email_data_by_user = array();
			foreach($array_forecast_email_data as $id_sector => $sectors_forecast_email_data){
				foreach($sectors_forecast_email_data as $id_estacion => $stations_forecast_email_data){
					foreach($stations_forecast_email_data as $id_variable => $variables_forecast_email_data){
						foreach($variables_forecast_email_data as $id_modelo => $models_forecast_email_data){
							foreach($models_forecast_email_data as $index => $forecast_email_data){
								if($index == "users"){
									foreach($forecast_email_data as $i => $id_user){
										$array_forecast_email_data_by_user[$id_user][$id_sector][$id_estacion][$id_variable][$id_modelo] = $models_forecast_email_data;
									}
								}
							}
						}
					}
				}
			}
	
			$alert_options_final = array();
			foreach($array_forecast_email_data_by_user as $id_user => $array_forecast_email_data){
	
				$user_to_notify = $this->ci->Users_model->get_one($id_user);
	
				$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
		
				$html_forecast_table = "<table border='1'>";
				$html_forecast_table .= 	"<tr style='font-size: 13px;'>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("station")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("variable")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("model")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("alert_name")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("range")." [ug/m3]"."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("hour")."</th>";
				$html_forecast_table .= 	"</tr>";
	
				$hay_datos = false;
				foreach($array_forecast_email_data as $id_sector => $sectors_forecast_email_data){
	
					$sector = $this->ci->Air_sectors_model->get_one($id_sector);
	
					foreach($sectors_forecast_email_data as $id_estacion => $stations_forecast_email_data){
						
						$station = $this->ci->Air_stations_model->get_one($id_estacion);
	
						foreach($stations_forecast_email_data as $id_variable => $variables_forecast_email_data){
							
							$variable = $this->ci->Air_variables_model->get_one($id_variable);
	
							$alert_options = array(
								"id_client" => $proyecto->client_id,
								"id_project" => $proyecto->id,
								"id_client_module" => 14, // Pronóstico
								"id_client_submodule" => 0,
								"alert_config" => array(
									"air_config" => "action_plan",
									"id_air_sector" => $id_sector,
									"id_air_station" => $id_estacion,
									"id_air_variable" => $id_variable
								),
								
							);
	
							$alert_options_final[$id_user][] = $alert_options;

							//foreach($variables_forecast_email_data as $index => $forecast_email_data){
							foreach($variables_forecast_email_data as $id_modelo => $models_forecast_email_data){

								$modelo = $this->ci->Air_models_model->get_one($id_modelo);
								
								foreach($models_forecast_email_data as $index => $forecast_email_data){

									if(strpos($index, 'time_') !== false && strpos($index, 'time_min_') === false && strpos($index, 'time_max_') === false && strpos($index, 'time_porc_conf_') === false) { // time_ fields
										
										$hay_datos = true;
										$hora = substr($index, 5, 6).":00";
										
										$html_forecast_table .= 	"<tr style='font-size: 13px;'>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$station->name."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$variable->name."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".lang($modelo->name)."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$forecast_email_data["nombre_alerta"]."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$forecast_email_data["rango"]."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$hora."</td>";
										$html_forecast_table .= 	"</tr>";
		
									}

								}
	
							}
	
						}
	
					}
	
				}
	
				$html_forecast_table .= 	"</table>";
	
				$parser_data["HTML_FORECAST_TABLE"] = ($hay_datos) ? $html_forecast_table : lang("no_upcoming_events");
				
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message, $email_options);
				$email_options = array();
				
			}

			// Guardo registro de envío de alerta por correo
			foreach($alert_options_final as $id_user => $alert_options){
	
				foreach($alert_options as $options){
	
					$alert_projects_config = $this->ci->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
	
					$alert_historical_air_data = array(
						"id_alert_projects" => $alert_projects_config->id,
						"id_user" => $id_user,
						"alert_date" => $actual_date
					);
	
					$save_alert_historical_air = $this->ci->AYN_Alert_historical_air_model->save($alert_historical_air_data);
	
				}
	
			}

		}

	}

	/* Version de la función que envía en cada correo una lista con los demás usuarios que recibieron el correo.
		NOTA: esta función asume que todos los usuarios notificados tienen las mismas alertas asociadas.
	*/
	function _send_email_air_forecast_alert_cc($id_proyecto, $id_modelo, $array_forecast_email_data){

		$actual_date = date("Y-m-d H:i:s");
		$actual_date = convert_date_utc_to_local($actual_date, "Y-m-d");
		$tomorrow = date('Y-m-d H:i:s', strtotime($actual_date . ' +1 day'));

		$email_template = $this->ci->Email_templates_model->get_final_template("ayn_alerts_projects_air");

		$parser_data = array();
		$parser_data["ALERT_DATE"] = get_date_format($tomorrow, $id_proyecto);

		$proyecto = $this->ci->Projects_model->get_one($id_proyecto);
		$parser_data["PROJECT_NAME"] = $proyecto->title;
		
		$modelo = $this->ci->Air_models_model->get_one($id_modelo);
		$parser_data["MODEL_NAME"] = lang($modelo->name);

		$forecast_module = $this->ci->Clients_modules_model->get_one(14)->name;
		$parser_data["MODULE_NAME"] = $forecast_module;

		$parser_data["SITE_URL"] = get_uri();
		$parser_data["CONTACT_URL"] = get_uri("contact");
		$parser_data_signature["SITE_URL"] = get_uri();

		$signature_template = $this->ci->Email_templates_model->get_one_where(array("template_name" => "signature_air", "deleted" => 0));
		$signature = ($signature_template->custom_message) ? $signature_template->custom_message : $signature_template->default_message;
		$signature_message = $this->ci->parser->parse_string($signature, $parser_data_signature, TRUE);
		$parser_data["SIGNATURE"] = $signature_message;

		// IMÁGENES DE GRÁFICOS (COMPARACIÓN DE PRONÓSTICOS)
		$html_chart_images = "";
		$array_stations_charts_images = air_forecast_comparison_get_chart_img();
		foreach($array_stations_charts_images as $array_image){
			// $html_chart_images .= "<img src='".$array_image["img_src"]."' alt='".$array_image["name_station"]."' width='500' /> <br>";
			$html_chart_images .= "<img src='data:image/png;base64,".base64_encode($array_image["binary_data"])."' alt='".$array_image["name_station"]."' height='350' width='500' /> <br>";
		}
		$parser_data["CHART_IMAGES"] = $html_chart_images;
		// $parser_data["CHART_IMAGES"] = "";

		// CONSULTAR ÚLTIMO BOLETÍN SUBIDO Y DISPONIBILIZAR LINK PARA SU DESCARGA
		$id_form = 2; // Formulario dinámico creado para Boletines
		$id_field_file = 1; // Campo Archivo dinámico para PDF del Boletín
		$id_field_text = 2; // Campo Texto dinámico para texto del Boletín
		$last_bulletin = $this->ci->Form_values_model->get_last_value_of_form(array("id_form" => $id_form))->row();
		$bulletin_data = json_decode($last_bulletin->datos, true);
		$bulletin_text = $bulletin_data[$id_field_text];
		$parser_data["BULLETIN_TEXT"] = $bulletin_text;

		$bulletin_filename = $bulletin_data[$id_field_file];
		$bulletin_filepath = getcwd()."/files/mimasoft_files/client_1/project_1/form_".$id_form."/elemento_".$last_bulletin->id."/".$bulletin_filename;

		$email_options = array("attachments" => array(array("file_path" => $bulletin_filepath, "file_name" => remove_file_prefix($bulletin_filename))));

		// $link_last_bulletin = get_uri("other_records/download_last_bulletin");
		// $parser_data["LINK_LAST_BULLETIN"] = $link_last_bulletin;
		
		if($array_forecast_email_data["no_data"] == true){

			// Se crea una lista con los usuarios que se debe alertar por correo para agregarlos a una lista de usuarios notificados (tipo CC)
			$html_cc_users = "<ul>";
			foreach($array_forecast_email_data as $index => $id_user){
				if($index != "no_data"){
					$email = $this->ci->Users_model->get_one($id_user)->email;
					$html_cc_users .= "<li>".$email."</li>";
				}
			}
			$html_cc_users .= "</ul>";
			$parser_data["HTML_CC_USERS"] = $html_cc_users;
			// var_dump($html_cc_users);exit;

			foreach($array_forecast_email_data as $index => $id_user){
				if($index != "no_data"){

					$user_to_notify = $this->ci->Users_model->get_one($id_user);
					$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
					$parser_data["HTML_FORECAST_TABLE"] = lang("no_upcoming_events");

					$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);

					$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message, $email_options);
					$email_options = array();

					$alert_historical_air_data = array(
						"id_alert_projects" => NULL,
						"id_user" => $id_user,
						"alert_date" => $actual_date
					);
	
					$save_alert_historical_air = $this->ci->AYN_Alert_historical_air_model->save($alert_historical_air_data);

				}
			}

		} else {

			$users_for_email_cc = array();

			$array_forecast_email_data_by_user = array();
			foreach($array_forecast_email_data as $id_sector => $sectors_forecast_email_data){
				foreach($sectors_forecast_email_data as $id_estacion => $stations_forecast_email_data){
					foreach($stations_forecast_email_data as $id_variable => $variables_forecast_email_data){
						foreach($variables_forecast_email_data as $id_modelo => $models_forecast_email_data){
							foreach($models_forecast_email_data as $index => $forecast_email_data){
								if($index == "users"){
									foreach($forecast_email_data as $i => $id_user){
										$array_forecast_email_data_by_user[$id_user][$id_sector][$id_estacion][$id_variable][$id_modelo] = $models_forecast_email_data;

										$users_for_email_cc[] = $id_user;
									}
								}
							}
						}
					}
				}
			}

			// Se crea una lista con los usuarios que se debe alertar por correo para agregarlos a una lista de usuarios notificados (tipo CC)
			$users_for_email_cc = array_unique($users_for_email_cc);

			$html_cc_users = "<ul>";
			foreach($users_for_email_cc as $id_user){
				$email = $this->ci->Users_model->get_one($id_user)->email;
				$html_cc_users .= "<li>".$email."</li>";
			}
			$html_cc_users .= "</ul>";
			$parser_data["HTML_CC_USERS"] = $html_cc_users;
			// var_dump($html_cc_users);exit;
			
			$alert_options_final = array();

			foreach($array_forecast_email_data_by_user as $id_user => $array_forecast_email_data){
	
				$user_to_notify = $this->ci->Users_model->get_one($id_user);
	
				$parser_data["USER_TO_NOTIFY_NAME"] = $user_to_notify->first_name." ".$user_to_notify->last_name;
		
				$html_forecast_table = "<table border='1'>";
				$html_forecast_table .= 	"<tr style='font-size: 13px;'>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("station")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("variable")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("model")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("alert_name")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("range")." [ug/m3]"."</th>";
				// $html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>"."% ".lang("reliability")."</th>";
				$html_forecast_table .= 		"<th style='text-align: center; padding: 5px;'>".lang("hour")."</th>";
				$html_forecast_table .= 	"</tr>";
	
				$hay_datos = false;
				foreach($array_forecast_email_data as $id_sector => $sectors_forecast_email_data){
	
					$sector = $this->ci->Air_sectors_model->get_one($id_sector);
	
					foreach($sectors_forecast_email_data as $id_estacion => $stations_forecast_email_data){
						
						$station = $this->ci->Air_stations_model->get_one($id_estacion);
	
						foreach($stations_forecast_email_data as $id_variable => $variables_forecast_email_data){
							
							$variable = $this->ci->Air_variables_model->get_one($id_variable);
	
							$alert_options = array(
								"id_client" => $proyecto->client_id,
								"id_project" => $proyecto->id,
								"id_client_module" => 14, // Pronóstico
								"id_client_submodule" => 0,
								"alert_config" => array(
									"air_config" => "action_plan",
									"id_air_sector" => $id_sector,
									"id_air_station" => $id_estacion,
									"id_air_variable" => $id_variable
								),
								
							);
	
							$alert_options_final[$id_user][] = $alert_options;

							//foreach($variables_forecast_email_data as $index => $forecast_email_data){
							foreach($variables_forecast_email_data as $id_modelo => $models_forecast_email_data){

								$modelo = $this->ci->Air_models_model->get_one($id_modelo);
								
								foreach($models_forecast_email_data as $index => $forecast_email_data){

									if(strpos($index, 'time_') !== false && strpos($index, 'time_min_') === false && strpos($index, 'time_max_') === false && strpos($index, 'time_porc_conf_') === false) { // time_ fields
										
										$hay_datos = true;
										$hora = substr($index, 5, 6).":00";
										
										$html_forecast_table .= 	"<tr style='font-size: 13px;'>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$station->name."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$variable->name."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".lang($modelo->name)."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$forecast_email_data["nombre_alerta"]."</td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$forecast_email_data["rango"]."</td>";
										// $html_forecast_table .= 	"<td style='padding: 5px;'>".to_number_project_format($forecast_email_data["porc_conf"], $id_proyecto)."% </td>";
										$html_forecast_table .= 	"<td style='padding: 5px;'>".$hora."</td>";
										$html_forecast_table .= 	"</tr>";
		
									}

								}
	
							}
	
						}
	
					}
	
				}
	
				$html_forecast_table .= 	"</table>";
	
				$parser_data["HTML_FORECAST_TABLE"] = ($hay_datos) ? $html_forecast_table : lang("no_upcoming_events");
				
				$message = $this->ci->parser->parse_string($email_template->message, $parser_data, TRUE);
				
				$send_app_mail = send_app_mail($user_to_notify->email, $email_template->subject, $message, $email_options);
				$email_options = array();
				
			}

			// Guardo registro de envío de alerta por correo
			foreach($alert_options_final as $id_user => $alert_options){
	
				foreach($alert_options as $options){
	
					$alert_projects_config = $this->ci->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
	
					$alert_historical_air_data = array(
						"id_alert_projects" => $alert_projects_config->id,
						"id_user" => $id_user,
						"alert_date" => $actual_date
					);
	
					$save_alert_historical_air = $this->ci->AYN_Alert_historical_air_model->save($alert_historical_air_data);
	
				}
	
			}

		}

	}

}
