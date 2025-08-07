<?php
	
	/*
	 * Helper creado para la generación y guardado de alertas y notificaciones
	 */
	
	/*
	 * Guarda histórico de notificaciones
	 */
	if (!function_exists('ayn_save_historical_notification')) {
	
		function ayn_save_historical_notification($data = array()) {
		
			$ci = & get_instance();
			$id_client = get_array_value($data, "id_client");
			$module_level = get_array_value($data, "module_level");
			$event = get_array_value($data, "event");
			$id_user = get_array_value($data, "id_user");
			$id_element = get_array_value($data, "id_element");
			
			$is_email_sended = get_array_value($data, "is_email_sended");
			$massive = get_array_value($data, "massive");
			
			$user = $ci->Users_model->get_one($id_user);
			$data["is_admin"] = $user->is_admin;
			$data["notified_date"] = get_current_utc_time();
			
			if($module_level == "general"){
				
				$id_client_context_module = get_array_value($data, "id_client_context_module");
				$id_client_context_submodule = get_array_value($data, "id_client_context_submodule");
				
				$notif_config = $ci->AYN_Notif_general_model->get_one_where(array(
					"id_client" => $id_client,
					"id_client_context_module" => $id_client_context_module,
					"id_client_context_submodule" => $id_client_context_submodule,
					"event" => $event,
					"deleted" => 0
				));
				
				if($notif_config->id){
					$notif_config_users = $ci->AYN_Notif_general_users_model->get_all_where(array(
						"id_notif_general" => $notif_config->id,
						"deleted" => 0
					))->result_array();
				}
				
				if($notif_config->id && count($notif_config_users)){
					
					// SI ESTÁ CHECKEADO WEB O EMAIL, ESTÁ CONFIGURADO, POR LO QUE EL HISTÓRICO SE GUARDA.
					if($notif_config->web_notification || $notif_config->email_notification){
						
						// GUARDA HISTÓRICO Y SE ASOCIA A LA CONFIGURACIÓN
						$data["id_notif_general"] = $notif_config->id;
						
						if(!$notif_config->email_notification){
							$data["is_email_sended"] = 1;
						} else {
							if($massive && $is_email_sended == 1){
								$data["is_email_sended"] = 1;
							} else {
								$data["is_email_sended"] = 0;
							}
						}
						
						$data["web_only"] = ($notif_config->web_notification) ? 1 : 0;
						
						$save_id = $ci->AYN_Notif_historical_model->save($data);
						foreach($notif_config_users as $notif_config_user){
							$general_profile_access = general_profile_access(
								$notif_config_user["id_user"], 
								$notif_config->id_client_context_module, 
								$notif_config->id_client_context_submodule, 
								"ver"
							);
							if($general_profile_access != 3){
								$data_users = array(
									"id_notif_historical" => $save_id,
									"id_user" => $notif_config_user["id_user"]
								);
								$save_users = $ci->AYN_Notif_historical_users_model->save($data_users);
							}
						}
						
					}
					
				}

			}
			
			if($module_level == "project"){
				
				$id_project = get_array_value($data, "id_project");
				$id_client_module = get_array_value($data, "id_client_module");
				$id_client_submodule = get_array_value($data, "id_client_submodule");
				
				$event_config = $event;
				
				if($id_client_module == "4"){ // Otros Registros
					if($event == "add" || $event == "add_fixed_or"){
						$event_config = "add";
					}
					if($event == "edit" || $event == "edit_fixed_or"){
						$event_config = "edit";
					}
					if($event == "delete" || $event == "delete_fixed_or"){
						$event_config = "delete";
					}
				}
				
				$notif_config = $ci->AYN_Notif_projects_clients_model->get_one_where(array(
					"id_client" => $id_client,
					"id_project" => $id_project,
					"id_client_module" => $id_client_module,
					"id_client_submodule" => $id_client_submodule,
					"event" => $event_config,
					"deleted" => 0
				));
				
				if($notif_config->id){
					$notif_config_users = $ci->AYN_Notif_projects_clients_users_model->get_all_where(array(
						"id_notif_projects_clients" => $notif_config->id,
						"deleted" => 0
					))->result_array();
				}
				
				if($notif_config->id && count($notif_config_users)){
					
					// SI ESTÁ CHECKEADO WEB O EMAIL, ESTÁ CONFIGURADO, POR LO QUE EL HISTÓRICO SE GUARDA.
					if($notif_config->web_notification || $notif_config->email_notification){
						
						// GUARDA HISTÓRICO Y SE ASOCIA A LA CONFIGURACIÓN
						$data["id_notif_projects_clients"] = $notif_config->id;
												
						if(!$notif_config->email_notification){
							$data["is_email_sended"] = 1;
						} else {
							if($massive && $is_email_sended == 1){
								$data["is_email_sended"] = 1;
							} else {
								$data["is_email_sended"] = 0;
							}
						}
						
						$data["web_only"] = ($notif_config->web_notification) ? 1 : 0;
						
						$save_id = $ci->AYN_Notif_historical_model->save($data);
						foreach($notif_config_users as $notif_config_user){
							$profile_access = profile_access(
								$notif_config_user["id_user"], 
								$notif_config->id_client_module, 
								$notif_config->id_client_submodule, 
								"ver"
							);
							if($profile_access != 3){
								$data_users = array(
									"id_notif_historical" => $save_id,
									"id_user" => $notif_config_user["id_user"]
								);
								$save_users = $ci->AYN_Notif_historical_users_model->save($data_users);
							}
						}
						
					}
	
				}
				
			}
			
			if($module_level == "admin"){
				
				$id_project = get_array_value($data, "id_project");
				$id_admin_module = get_array_value($data, "id_admin_module");
				$id_admin_submodule = get_array_value($data, "id_admin_submodule");
				
				$notif_config = $ci->AYN_Notif_projects_admin_model->get_one_where(array(
					"id_client" => $id_client,
					"id_project" => $id_project,
					"id_admin_module" => $id_admin_module,
					"id_admin_submodule" => $id_admin_submodule,
					"event" => $event,
					"deleted" => 0
				));
				
				if($notif_config->id){
					$notif_config_users = $ci->AYN_Notif_projects_admin_users_model->get_all_where(array(
						"id_notif_projects_admin" => $notif_config->id,
						"deleted" => 0
					))->result_array();
				}
				
				if($notif_config->id && count($notif_config_users)){
					
					// SI ESTÁ CHECKEADO WEB O EMAIL, ESTÁ CONFIGURADO, POR LO QUE EL HISTÓRICO SE GUARDA.
					if($notif_config->web_notification || $notif_config->email_notification){
						
						// GUARDA HISTÓRICO Y SE ASOCIA A LA CONFIGURACIÓN
						$data["id_notif_projects_admin"] = $notif_config->id;
						
						//$data["is_email_sended"] = (!$notif_config->email_notification) ? 1 : 0;
						
						if(!$notif_config->email_notification){
							$data["is_email_sended"] = 1;
						} else {
							if($massive && $is_email_sended == 1){
								$data["is_email_sended"] = 1;
							} else {
								$data["is_email_sended"] = 0;
							}
						}
						
						$data["web_only"] = ($notif_config->web_notification) ? 1 : 0;
						
						$save_id = $ci->AYN_Notif_historical_model->save($data);
						foreach($notif_config_users as $notif_config_user){
							$data_users = array(
								"id_notif_historical" => $save_id,
								"id_user" => $notif_config_user["id_user"]
							);
							$save_users = $ci->AYN_Notif_historical_users_model->save($data_users);
						}
						
					}
					
				}
	
			}
			
		}
	
	}
	
	
	/*
	 * Retorna un array con la configuración de una notificación que contiene
	 * las URL de los módulos en donde se realiza una acción o evento de uno de sus elementos y su id
	 */
	 
	if (!function_exists('get_notification_config')) {
	
		function get_notification_config($info_options) {
		
			if($info_options->web_only){
						
				$ci = & get_instance();
				
				$array_result = array();
				$url = "";
				$ajax_url = "";
				
				$module_name = "";
				$submodule_name = "";
				$message = "";
				
				if($info_options->id_client_module){
					$module = $ci->Clients_modules_model->get_one($info_options->id_client_module);
					$module_name = $module->name;
					if($info_options->id_client_submodule){
						$submodule_name = $ci->Clients_submodules_model->get_one($info_options->id_client_submodule)->name;
					}
				}
				
				if($info_options->id_client_context_module){
					$module = $ci->Client_context_modules_model->get_one($info_options->id_client_context_module);
					$module_name = $module->name;
					if($info_options->id_client_context_submodule){
						$submodule_name = $ci->Client_context_submodules_model->get_one($info_options->id_client_context_submodule)->name;
					}
				}
				
				if($info_options->id_admin_module){
					$module = $ci->AYN_Admin_modules_model->get_one($info_options->id_admin_module);
					$module_name = $module->name;
					if($info_options->id_admin_submodule){
						$submodule_name = $ci->AYN_Admin_submodules_model->get_one($info_options->id_admin_submodule)->name;
					}
				}
				
				$id_element = (isset($info_options->id_element)) ? $info_options->id_element : "";
				$project_name = $ci->Projects_model->get_one($info_options->id_project)->title;
				
				// Notificaciones módulos nivel cliente
				if($info_options->module_level == "general"){
					
					$message_in_module = " ".lang("in")." ".$module_name;
					if($submodule_name){
						$message_in_module .= " | ".$submodule_name;
					}
				
					// Acuerdos Territorio - Beneficiarios: 2 | Acuerdos Distribución - Beneficiarios: 6
					if(($info_options->id_client_context_module == "2" || $info_options->id_client_context_module == "6") && $info_options->id_client_context_submodule == "0"){
	
						$element = $ci->AC_Beneficiaries_model->get_one($id_element);
						$ajax_url = ($element->id) ? get_uri("AC_Beneficiaries/view/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
						
						if($info_options->event == "add"){
							$message = lang("added_an_item").$message_in_module;	
						} elseif($info_options->event == "edit"){
							$message = lang("edited_an_item").$message_in_module;	
						} elseif($info_options->event == "delete"){
							if($info_options->massive){
								$message = lang("deleted_items_massively").$message_in_module;
							} else {
								$message = lang("deleted_an_item").$message_in_module;
							}
						}
						
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
						
						$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					
					}
					
					// Acuerdos Territorio - Actividades: 3 | Acuerdos Distribución - Actividades: 7
					if(($info_options->id_client_context_module == "3" || $info_options->id_client_context_module == "7")){
						
						$element = $ci->AC_Activities_model->get_one($id_element);
						$ajax_url = ($element->id) ? get_uri("AC_Activities/view/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
						
						$message_in_module .= " | ".lang("activities_record");
						if($info_options->event == "add"){
							$message = lang("added_an_item").$message_in_module;
						} elseif($info_options->event == "edit"){
							$message = lang("edited_an_item").$message_in_module;
						} elseif($info_options->event == "delete"){
							if($info_options->massive){
								$message = lang("deleted_items_massively").$message_in_module;
							} else {
								$message = lang("deleted_an_item").$message_in_module;
							}
						}
						
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
						
						$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					// Acuerdos Territorio - Convenios y Donaciones: 4 | Acuerdos Distribución - Convenios y Donaciones: 8
					if($info_options->id_client_context_module == "4" || $info_options->id_client_context_module == "8"){
										
						// Pestaña Información
						if($info_options->event == "information_add" || $info_options->event == "information_edit" || $info_options->event == "information_delete" || $info_options->event == "information_audit"){ 
							
							$element = $ci->AC_Information_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Agreements_record/view_information/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
	
							$message_in_module .= " | ".lang("agreements_record")." | ".lang("information");
							if($info_options->event == "information_add"){
								$message = lang("added_an_item").$message_in_module;
							} elseif($info_options->event == "information_edit"){
								$message = lang("edited_an_item").$message_in_module;
							} elseif($info_options->event == "information_delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively").$message_in_module;
								} else {
									$message = lang("deleted_an_item").$message_in_module;
								}
							} elseif($info_options->event == "information_audit"){
								$message = lang("audited_an_item").$message_in_module;
							}
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
							
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
						// Pestaña Configuración
						if($info_options->event == "configuration_add" || $info_options->event == "configuration_edit" || $info_options->event == "configuration_delete" || $info_options->event == "configuration_close"){
													
							$element = $ci->AC_Configuration_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Agreements_record/view_configuration/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
							
							if($info_options->id_client_context_module == "4"){
								$message_in_module .= " | ".lang("agreements_record")." | ".lang("configuration");
							} elseif($info_options->id_client_context_module == "8"){
								$message_in_module .= " | ".lang("configuration");
							}
		
							if($info_options->event == "configuration_add"){
								$message = lang("added_an_item").$message_in_module;
							} elseif($info_options->event == "configuration_edit"){
								$message = lang("edited_an_item").$message_in_module;
							} elseif($info_options->event == "configuration_delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively").$message_in_module;
								} else {
									$message = lang("deleted_an_item").$message_in_module;
								}
							} elseif($info_options->event == "configuration_close"){
								$message = lang("closed_an_item").$message_in_module;
							}
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
							
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
		
						
						// Pestaña Registro de Ejecución
						if($info_options->event == "execution_record_add" || $info_options->event == "execution_record_edit" || $info_options->event == "execution_record_delete"){
		
							$element = $ci->AC_Execution_records_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Agreements_record/view_execution_record/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
							
							$message_in_module .= " | ".lang("execution_record");
							if($info_options->event == "execution_record_add"){
								$message = lang("added_an_item").$message_in_module;
							} elseif($info_options->event == "execution_record_edit"){
								$message = lang("edited_an_item").$message_in_module;
							} elseif($info_options->event == "execution_record_delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively").$message_in_module;
								} else {
									$message = lang("deleted_an_item").$message_in_module;
								}
							}
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
							
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
						// Pestaña Registro de Pago
						if($info_options->event == "payment_record_edit"){
							
							$element = $ci->AC_Configuration_associated_payments_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Agreements_record/view_payment_record/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element' data-post-id_informacion='$element->id_informacion' data-post-client_area='$element->client_area'";
							
							$message = lang("edited_an_item").$message_in_module." | ".lang("payment_record");
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
		
					}
					
					// Acuerdos Territorio - Mantenedoras: 5 |  Acuerdos Distribución - Mantenedoras: 9
					if($info_options->id_client_context_module == "5" || $info_options->id_client_context_module == "9"){
										
						// Mantenedoras Sociedades
						if($info_options->event == "society_add" || $info_options->event == "society_edit" || $info_options->event == "society_delete"){
							
							$element = $ci->AC_Feeders_societies_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Feeders/view_societies/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
							
							$message_in_module .= " | ".lang("societies");
							if($info_options->event == "society_add"){
								$message = lang("added_an_item").$message_in_module;
							} elseif($info_options->event == "society_edit"){
								$message = lang("edited_an_item").$message_in_module;
							} elseif($info_options->event == "society_delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively").$message_in_module;
								} else {
									$message = lang("deleted_an_item").$message_in_module;
								}
							}
							
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
						// Mantenedoras Centrales
						if($info_options->event == "central_add" || $info_options->event == "central_edit" || $info_options->event == "central_delete"){ 
							
							$element = $ci->AC_Feeders_centrals_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Feeders/view_centrals/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
													
							$message_in_module .= " | ".lang("centrals");
							if($info_options->event == "central_add"){
								$message = lang("added_an_item").$message_in_module;
							} elseif($info_options->event == "central_edit"){
								$message = lang("edited_an_item").$message_in_module;
							} elseif($info_options->event == "central_delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively").$message_in_module;
								} else {
									$message = lang("deleted_an_item").$message_in_module;
								}
							}
							
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
						// Mantenedoras Tipos de Acuerdo
						if($info_options->event == "type_of_agreement_add" || $info_options->event == "type_of_agreement_edit" || $info_options->event == "type_of_agreement_delete"){ 
							
							$element = $ci->AC_Feeders_types_agreements_model->get_one($id_element);
							$ajax_url = ($element->id) ? get_uri("AC_Feeders/view_types_of_agreement/".$id_element) : get_uri("AYN_Notif_historical/view_element_details");
							
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$id_element'";
	
							$message_in_module .= " | ".lang("types_of_agreement");
							if($info_options->event == "type_of_agreement_add"){
								$message = lang("added_an_item").$message_in_module;
							} elseif($info_options->event == "type_of_agreement_edit"){
								$message = lang("edited_an_item").$message_in_module;
							} elseif($info_options->event == "type_of_agreement_delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively").$message_in_module;
								} else {
									$message = lang("deleted_an_item").$message_in_module;
								}
							}
							
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
										
					}
					
					// Ayuda y Soporte
					if($info_options->id_client_context_module == "1"){
						if($info_options->id_client_context_submodule == "4" && $info_options->event == "send_email"){ 
													
							$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-module_level='$info_options->module_level' data-post-id_modulo='$info_options->id_client_context_module' data-post-id_submodulo='$info_options->id_client_context_submodule' data-post-id_element='$id_element' data-post-event='$info_options->event'";
							
							$message = lang("sended_contact_form");
							$message .= "<div>"."<strong>".lang("module").   ": "."</strong>".lang($module->contexto)."</div>";				
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
					}
					
				}
				
				// Notificaciones módulos nivel cliente - proyecto
				if($info_options->module_level == "project"){
					
					// Registros Ambientales || Mantenedoras || Otros Registros
					if($info_options->id_client_module == "2" || $info_options->id_client_module == "3" || $info_options->id_client_module == "4"){
						
						if($info_options->id_client_module == "4" && ($info_options->event == "add_fixed_or" || $info_options->event == "edit_fixed_or" || $info_options->event == "delete_fixed_or")){
							
							$element = $ci->Fixed_form_values_model->get_one_where(array("id" => $id_element));
							$id_form = $element->id_formulario;
							$form_name = $ci->Forms_model->get_one($id_form)->nombre;
							
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("other_records/preview_fixed_form/".$id_form);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
							
							if($info_options->event == "add_fixed_or"){
								$info_options->event = "add";
							}
							
							if($info_options->event == "edit_fixed_or"){
								$info_options->event = "edit";
							}
							
							if($info_options->event == "delete_fixed_or"){
								$info_options->event = "delete";
							}
							
						} else {
							$element = $ci->Form_values_model->get_one_where(array("id" => $id_element));
							$form_rel_project = $ci->Form_rel_project_model->get_one($element->id_formulario_rel_proyecto);
							$id_form = $form_rel_project->id_formulario;
							$form_name = $ci->Forms_model->get_one($id_form)->nombre;
							
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("other_records/preview/".$id_form);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
						}
						
						if($info_options->id_client_module == "2"){
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("environmental_records/preview/".$id_form);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
							$form = $ci->Forms_model->get_one($id_form);
							$flujo = ($form->flujo != "No Aplica") ? ' <label class="label label-success large">'.$form->flujo.'</label></p>' : "";
						}
						
						if($info_options->id_client_module == "3"){
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("feeders/preview/".$id_form);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
						}
	
						if($info_options->event == "add"){
							$message = lang("added_an_item")." ".lang("in")." <strong>".$form_name."</strong>".$flujo;
						} elseif($info_options->event == "edit"){
							$message = lang("edited_an_item")." ".lang("in")." <strong>".$form_name."</strong>".$flujo;
						} elseif($info_options->event == "delete"){
							if($info_options->massive){
								$message = lang("deleted_items_massively")." ".lang("in")." <strong>".$form_name."</strong>".$flujo;
							} else {
								$message = lang("deleted_an_item")." ".lang("in")." <strong>".$form_name."</strong>".$flujo;
							}
						}
						
						$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					
					// Compromisos
					if($info_options->id_client_module == "6"){
						
						// Evaluación de Compromisos RCA
						if($info_options->id_client_submodule == "4"){
							
							$element = $ci->Compromises_compliance_evaluation_rca_model->get_one_where(array("id" => $id_element));
							$valor_compromiso = $ci->Values_compromises_rca_model->get_one($element->id_valor_compromiso);
							
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("compromises_rca_evaluation/view/".$element->id);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
							
							if($info_options->event == "add"){
								$message = lang("added_an_evaluation")." ".lang("in")." ".$valor_compromiso->nombre_compromiso;
							} elseif($info_options->event == "edit"){
								$message = lang("edited_an_evaluation")." ".lang("in")." ".$valor_compromiso->nombre_compromiso;
							}
							
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
						// Evaluación de Compromisos Reportables
						if($info_options->id_client_submodule == "22"){
							
							$element = $ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array("id" => $id_element));
							$valor_compromiso = $ci->Values_compromises_reportables_model->get_one($element->id_valor_compromiso);
							
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("compromises_reportables_evaluation/view/".$element->id);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_evaluacion='$element->id' data-post-id_compromiso='$element->id_valor_compromiso' data-post-id_proyecto='$info_options->id_project'";
							
							$message = lang("edited_an_evaluation")." ".lang("in")." ".$valor_compromiso->nombre_compromiso;
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
							
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
					}
					
					// Permisos
					if($info_options->id_client_module == "7"){
										
						$element = $ci->Permitting_procedure_evaluation_model->get_one_where(array("id" => $id_element));
						$valor_permiso = $ci->Values_permitting_model->get_one($element->id_valor_permiso);
	
						$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("permitting_procedure_evaluation/view/".$element->id);
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_evaluacion='$element->id' data-post-id_proyecto='$info_options->id_project'";
							
						if($info_options->event == "add"){
							$message = lang("added_an_evaluation")." ".lang("in")." ".$valor_permiso->nombre_permiso;
						} elseif($info_options->event == "edit"){
							$message = lang("edited_an_evaluation")." ".lang("in")." ".$valor_permiso->nombre_permiso;
						}
						
						$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					// Administración Cliente
					if($info_options->id_client_module == "11"){
	
	
						// Configuración Panel Principal
						if($info_options->id_client_submodule == "20"){
													
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("setting_dashboard");
							$url_attributes = "href='$ajax_url'";
							
							$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-module_level='$info_options->module_level' data-post-id_modulo='$info_options->id_client_module' data-post-id_submodulo='$info_options->id_client_submodule' data-post-event='$info_options->event' data-post-id_cliente='$info_options->id_client' data-post-id_proyecto='$info_options->id_project'";
							
							$message = lang("edited_an_item")." ".lang("in")." ".$submodule_name;
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
		
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
						// Carga Masiva
						if($info_options->id_client_submodule == "21"){
							
							$element = $ci->Forms_model->get_one($id_element);
							$form_name = $element->nombre;
							
							if($element->id_tipo_formulario == 1){
								$ajax_url = get_uri("environmental_records/view/".$element->id);
							} elseif($element->id_tipo_formulario == 2){
								$ajax_url = get_uri("feeders/view/".$element->id);
							} elseif($element->id_tipo_formulario == 3){
								$ajax_url = get_uri("other_records/view/".$element->id);
							} else {
								$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
							}
							
							$url_attributes = "href='$ajax_url'";
							
							$message = lang("added_elements_massively")." ".lang("in")." ".$form_name;
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
							
							$ci->session->set_userdata('project_context', $info_options->id_project);
							$ci->session->set_userdata('menu_project_active', NULL);
							$ci->session->set_userdata('client_area', NULL);
							$ci->session->set_userdata('menu_agreements_active', NULL);
							$ci->session->set_userdata('menu_kpi_active', NULL);
							$ci->session->set_userdata('menu_help_and_support_active', NULL);
							$ci->session->set_userdata('menu_recordbook_active', NULL);
							$ci->session->set_userdata('menu_ec_active', NULL);
							
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
						}
						
					}
					
					// Recordbook
					if($info_options->id_client_module == "12"){
						
						// Registros Recordbook
						if($info_options->id_client_submodule == "23"){
							
							$element = $ci->Recordbook_values_model->get_one_where(array("id" => $id_element));
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("recordbook/view/".$element->id);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_feedback_matrix_config='$element->id_recordbook_matrix_config' data-post-id_proyecto='$info_options->id_project'";
							
							if($info_options->event == "add"){
								$message = lang("added_an_item")." ".lang("in")." ".$submodule_name;
							} elseif($info_options->event == "edit"){
								$message = lang("edited_an_item")." ".lang("in")." ".$submodule_name;
							} elseif($info_options->event == "delete"){
								if($info_options->massive){
									$message = lang("deleted_items_massively")." ".lang("in")." ".$submodule_name;
								} else {
									$message = lang("deleted_an_item")." ".lang("in")." ".$submodule_name;
								}
							}
							
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
		
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
		
						}
						
						// Seguimiento del recordbook
						if($info_options->id_client_submodule == "24"){
							
							$element = $ci->Recordbook_monitoring_model->get_one_where(array("id" => $id_element));						
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("recordbook_monitoring/view/".$element->id);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_evaluacion='$element->id' data-post-id_proyecto='$info_options->id_project'";
							
							$message = lang("edited_an_item")." ".lang("in")." ".$submodule_name;
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
							
							$array_result = array("url_attributes" => $url_attributes, "message" => $message);
							
						}
						
					}
					
				}
				
				// Notificaciones módulos nivel admin
				if($info_options->module_level == "admin"){
					
					// Proyectos
					if($info_options->id_admin_module == "4"){
	
						$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-module_level='$info_options->module_level' data-post-id_modulo='$info_options->id_admin_module' data-post-id_element='$id_element'";
	
						$message = lang("edited_the_item");
						if($info_options->event == "project_edit_name"){
							$message .= " ".lang("project_name");
						} elseif($info_options->event == "project_edit_auth_amb"){
							$message .= " ".lang("environmental_authorization");
						} elseif($info_options->event == "project_edit_start_date"){
							$message .= " ".lang("start_date");
						} elseif($info_options->event == "project_edit_end_date"){
							$message .= " ".lang("term_date");
						} elseif($info_options->event == "project_edit_members"){
							$message .= " ".lang("members");
						} elseif($info_options->event == "project_edit_desc"){
							$message .= " ".lang("description");
						} elseif($info_options->event == "project_edit_status"){
							$message .= " ".lang("status");
						} elseif($info_options->event == "project_edit_pu"){
							$message .= " ".lang("unit_processes");
						} elseif($info_options->event == "project_edit_cat_impact"){
							$message .= " ".lang("footprints");
						}
		
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					// Registros
					if($info_options->id_admin_module == "5"){
						
						$element = $ci->Forms_model->get_one($id_element);
						$form_name = $element->nombre;
						
						if($element->id_tipo_formulario == 1){
							$ajax_url = get_uri("environmental_records/view/".$element->id);
						} elseif($element->id_tipo_formulario == 2){
							$ajax_url = get_uri("feeders/view/".$element->id);
						} elseif($element->id_tipo_formulario == 3){
							$ajax_url = get_uri("other_records/view/".$element->id);
						} else {
							$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
						}
						
						$url_attributes = "href='$ajax_url'";
						
						if($info_options->event == "form_add"){
							$message = lang("added_the_form")." ".$form_name;
						} elseif($info_options->event == "form_edit_name"){
							$message = lang("edited_the_item")." ".lang("name")." ".lang("in")." ".lang("form")." ".$form_name;
						} elseif($info_options->event == "form_edit_cat"){
							$message = lang("edited_the_item")." ".lang("category")." ".lang("in")." ".lang("form")." ".$form_name;
						} elseif($info_options->event == "form_delete"){
							if($info_options->massive){
								$message = lang("deleted_items_massively")." ".lang("in")." ".$submodule_name;
							} else {
								$message = lang("deleted_an_item")." ".lang("in")." ".$submodule_name;
							}
						}
						
						$ci->session->set_userdata('project_context', $info_options->id_project);
						$ci->session->set_userdata('menu_project_active', NULL);
						$ci->session->set_userdata('client_area', NULL);
						$ci->session->set_userdata('menu_agreements_active', NULL);
						$ci->session->set_userdata('menu_kpi_active', NULL);
						$ci->session->set_userdata('menu_help_and_support_active', NULL);
						$ci->session->set_userdata('menu_recordbook_active', NULL);
						$ci->session->set_userdata('menu_ec_active', NULL);
		
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					// Indicadores
					if($info_options->id_admin_module == "7"){
						
						$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-module_level='$info_options->module_level' data-post-id_modulo='$info_options->id_admin_module' data-post-id='$id_element' data-post-id_element='$id_element'";
												
						if($info_options->event == "uf_add_element"){
							$message = lang("added_an_item")." ".lang("in")." ".$submodule_name;
						} elseif($info_options->event == "uf_edit_element"){
							$message = lang("edited_an_item")." ".lang("in")." ".$submodule_name;
						} elseif($info_options->event == "uf_delete_element"){
							if($info_options->massive){
								$message = lang("deleted_items_massively")." ".lang("in")." ".$submodule_name;
							} else {
								$message = lang("deleted_an_item")." ".lang("in")." ".$submodule_name;
							}
						}
						
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					// Compromisos
					if($info_options->id_admin_module == "8"){
						
						$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-module_level='$info_options->module_level' data-post-id_modulo='$info_options->id_admin_module' data-post-id='$id_element' data-post-id_element='$id_element' data-post-event='$info_options->event'";
						
						if($info_options->event == "comp_rca_add"){
							$message = lang("added_an_item")." ".lang("in")." ".lang("compromises_rca");
						}
						
						if($info_options->event == "comp_rep_add"){
							$message = lang("added_an_item")." ".lang("in")." ".lang("compromises_rep");
						}
						
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
					// Permisos
					if($info_options->id_admin_module == "9"){
						
						$ajax_url = get_uri("AYN_Notif_historical/view_element_details");
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-module_level='$info_options->module_level' data-post-id_modulo='$info_options->id_admin_module' data-post-id='$id_element' data-post-id_element='$id_element' data-post-event='$info_options->event'";
						
						if($info_options->event == "permitting_add"){
							
							//$element = $ci->Values_permitting_model->get_one($id_element);
							//$url = ($ci->session->project_context == $info_options->id_project) ? get_uri("permitting_procedure_evaluation") : "";
							$message = lang("added_an_item")." ".lang("in")." ".lang("permittings");
						}
						
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						$array_result = array("url_attributes" => $url_attributes, "message" => $message);
					}
					
				}
		
				return $array_result;
				
			}
		}
	
	}
	
	if (!function_exists('profile_access')) {
		
		function profile_access($user_id, $module_id, $submodule_id = 0, $permission){
			
			$ci = & get_instance();
			$user = $ci->Users_model->get_one($user_id);
			$profile_id = $user->id_profile;
			$clients_modules_rel_profiles = $ci->Clients_modules_rel_profiles_model->get_all_where(array("id_profile" => $profile_id))->result();
			
			$option = "";
			
			if($submodule_id == 0){ // SI SUBMÓDULO ES == 0 ES PORQUE EL MÓDULO NO POSEE SUBMÓDULOS
				foreach($clients_modules_rel_profiles as $rel){
					if($rel->id_client_module == $module_id){
						$option = $rel->$permission;
					}
				}	
			} else {
				foreach($clients_modules_rel_profiles as $rel){
					if($rel->id_client_submodule == $submodule_id){
						$option = $rel->$permission;
					}
				}
			}
	
			return $option;
	
		}
		
	}
		
	if (!function_exists('general_profile_access')) {
		
		function general_profile_access($user_id, $module_id, $submodule_id = 0, $permission){
			
			$ci = & get_instance();
			$user = $ci->Users_model->get_one($user_id);
			$profile_id = $user->id_client_context_profile;
			$client_context_modules_rel_profiles = $ci->Client_context_modules_rel_profiles_model->get_all_where(array("id_client_context_profile" => $profile_id))->result();
			
			$option = "";
			
			if($submodule_id == 0){ // SI SUBMÓDULO ES == 0 ES PORQUE EL MÓDULO NO POSEE SUBMÓDULOS
				foreach($client_context_modules_rel_profiles as $rel){
					if($rel->id_client_context_module == $module_id){
						$option = $rel->$permission;
					}
				}	
			} else {
				foreach($client_context_modules_rel_profiles as $rel){
					if($rel->id_client_context_submodule == $submodule_id){
						$option = $rel->$permission;
					}
				}
			}
	
			return $option;
	
		}
		
	}
	
	/*
	 * Guarda histórico de notificaciones
	 */
	if (!function_exists('ayn_save_historical_alert')) {
	
		function ayn_save_historical_alert($data = array()) {
			
			$ci = & get_instance();
			$id_client = get_array_value($data, "id_client");
			$id_user = get_array_value($data, "id_user");
			$id_element = get_array_value($data, "id_element");
			$data["alert_date"] = get_current_utc_time();
			
			$id_project = get_array_value($data, "id_project");
			$id_client_module = get_array_value($data, "id_client_module");
			$id_client_submodule = get_array_value($data, "id_client_submodule");
						
			// Consultar la configuración de alertas que pertenezca al cliente - proyecto, al módulo, y a la categoría y unidad del formulario.
			$config_options = array(
				"id_client" => $id_client,
				"id_project" => $id_project,
				"id_client_module" => $id_client_module,
				"id_client_submodule" => $id_client_submodule,
				"alert_config" => ($id_client_module == "7") ? array() : $data["alert_config"], // Si el módulo es Permisos, se debe enviar un array vacío para consultar la configuración.
			);
						
			$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();	
						
			// if configuración de alertas existe
			if($alert_config_project->id){
				$alert_config_project_users = $ci->AYN_Alert_projects_users_model->get_all_where(array(
					"id_alert_project" => $alert_config_project->id,
					"deleted" => 0
				))->result_array();
			}
			
			if(
				!$alert_config_project->risk_email_alert && 
				!$alert_config_project->risk_web_alert && 
				!$alert_config_project->threshold_email_alert && 
				!$alert_config_project->threshold_web_alert
			){
				return;
			}
						
			if($alert_config_project->id && count($alert_config_project_users)){
				
				$alert_config = json_decode($alert_config_project->alert_config, TRUE);
				
				if($id_client_module == "2"){  // Registros Ambientales
					
					$form_value = $ci->Form_values_model->get_one($id_element);
					$datos = json_decode($form_value->datos, TRUE);
					
					$id_categoria = $data["alert_config"]["id_categoria"];
					$id_tipo_unidad = $data["alert_config"]["id_tipo_unidad"];
					$id_unidad = $data["alert_config"]["id_unidad"];
										
					$suma_elementos = $ci->AYN_Alert_projects_model->get_sum_unit_field_of_ra_forms(array(
						"id_proyecto" => $id_project,
						"id_categoria" => $id_categoria,
						"id_unidad" => $id_unidad
					))->row()->suma_elementos;
					
					$data["alert_config"]["suma_elementos"] = $suma_elementos;
					$data["alert_config"] = json_encode($data["alert_config"], TRUE);
					
					/*
						VALOR RIESGO: Si la suma de los elementos de los formularios que tengan la categoría y tipo de unidad de la 
						configuración, iguala o supera el valor riesgo de la configuración y a su vez es menor que el valor umbral
						de la configuración, se genera la alerta y se lanza el mensaje de precaución "el umbral está por ser sobrepasado".
					*/

					$data["is_email_sended"] = (!$alert_config_project->risk_email_alert && !$alert_config_project->threshold_email_alert) ? 1 : 0;
					$data["web_only"] = ($alert_config_project->risk_web_alert || $alert_config_project->threshold_web_alert) ? 1 : 0;
					
					if( ($suma_elementos >= $alert_config["risk_value"]) && ($suma_elementos < $alert_config["threshold_value"]) ){

						$data["id_alert_projects"] = $alert_config_project->id;
						$save_id = $ci->AYN_Alert_historical_model->save($data);
						foreach($alert_config_project_users as $alert_config_project_user){
							$profile_access = profile_access(
								$alert_config_project_user["id_user"], 
								$alert_config_project->id_client_module, 
								$alert_config_project->id_client_submodule, 
								"ver"
							);
							if($profile_access != 3){
								$data_users = array(
									"id_alert_historical" => $save_id,
									"id_user" => $alert_config_project_user["id_user"]
								);
								$save_users = $ci->AYN_Alert_historical_users_model->save($data_users);
							}
						}
						
					}
					
					/*
						VALOR UMBRAL: Si la suma de los elementos de los formularios que tengan la categoría y tipo de unidad de la 
						configuración, iguala o supera el valor umbral de la configuración, se genera la alerta y se lanza el mensaje 
						de alerta "el umbral ha sido sobrepasado"
					*/
					if($suma_elementos >= $alert_config["threshold_value"]){
						
						$data["id_alert_projects"] = $alert_config_project->id;
						$save_id = $ci->AYN_Alert_historical_model->save($data);
						foreach($alert_config_project_users as $alert_config_project_user){
							$profile_access = profile_access(
								$alert_config_project_user["id_user"], 
								$alert_config_project->id_client_module, 
								$alert_config_project->id_client_submodule, 
								"ver"
							);
							if($profile_access != 3){
								$data_users = array(
									"id_alert_historical" => $save_id,
									"id_user" => $alert_config_project_user["id_user"]
								);
								$save_users = $ci->AYN_Alert_historical_users_model->save($data_users);
							}
						}
						
					}

				}
				
				if($id_client_module == "6"){ // Compromisos
				
					$id_planificacion = $data["alert_config"]["id_planificacion"];
					$id_valor_compromiso = $data["alert_config"]["id_valor_compromiso"];
					$tipo_evaluacion = $data["alert_config"]["tipo_evaluacion"];
					
					if($id_client_submodule == "4"){ // Evaluación de Compromisos RCA
						$id_estado_evaluacion = $ci->Compromises_compliance_evaluation_rca_model->get_one($data["id_element"])->id_estados_cumplimiento_compromiso;
					}
					
					if($id_client_submodule == "22"){ // Evaluación de Compromisos Reportables
						$id_estado_evaluacion = $ci->Compromises_compliance_evaluation_reportables_model->get_one($data["id_element"])->id_estados_cumplimiento_compromiso;
					}
					
					if(!$id_planificacion){
						
						if($id_estado_evaluacion == $alert_config["risk_value"] || $id_estado_evaluacion == $alert_config["threshold_value"]){
							
							$data["id_alert_projects"] = $alert_config_project->id;
							$data["alert_config"]["id_estado_evaluacion"] = $id_estado_evaluacion;
							$data["alert_config"] = json_encode($data["alert_config"], TRUE);
							
							$data["is_email_sended"] = (!$alert_config_project->risk_email_alert && !$alert_config_project->threshold_email_alert) ? 1 : 0;
							$data["web_only"] = ($alert_config_project->risk_web_alert || $alert_config_project->threshold_web_alert) ? 1 : 0;
	
							$save_id = $ci->AYN_Alert_historical_model->save($data);
							foreach($alert_config_project_users as $alert_config_project_user){
								$profile_access = profile_access(
									$alert_config_project_user["id_user"], 
									$alert_config_project->id_client_module, 
									$alert_config_project->id_client_submodule, 
									"ver"
								);
								if($profile_access != 3){
									$data_users = array(
										"id_alert_historical" => $save_id,
										"id_user" => $alert_config_project_user["id_user"]
									);
									$save_users = $ci->AYN_Alert_historical_users_model->save($data_users);
								}
							}
							
						}

					}
					
					if($id_client_submodule == "22" && $id_planificacion){
						
						// Caso Especial: El registro de históricos de alertas para las planificaciones de compromisos reportables, se crean al
						// momento de que el usuario Admin crea un compromiso reportable. 
						
					}
					
				}
				
				if($id_client_module == "7"){ // Permisos
					
					$id_estado_evaluacion = $ci->Permitting_procedure_evaluation_model->get_one($data["id_element"])->id_estados_tramitacion_permisos;
					
					$data["id_alert_projects"] = $alert_config_project->id;
					$data["alert_config"]["id_estado_evaluacion"] = $id_estado_evaluacion;
					$data["alert_config"] = json_encode($data["alert_config"], TRUE);
					
					$data["is_email_sended"] = (!$alert_config_project->risk_email_alert && !$alert_config_project->threshold_email_alert) ? 1 : 0;
					$data["web_only"] = ($alert_config_project->risk_web_alert || $alert_config_project->threshold_web_alert) ? 1 : 0;
					
					$save_id = $ci->AYN_Alert_historical_model->save($data);
					foreach($alert_config_project_users as $alert_config_project_user){
						$profile_access = profile_access(
							$alert_config_project_user["id_user"], 
							$alert_config_project->id_client_module, 
							$alert_config_project->id_client_submodule, 
							"ver"
						);
						if($profile_access != 3){
							$data_users = array(
								"id_alert_historical" => $save_id,
								"id_user" => $alert_config_project_user["id_user"]
							);
							$save_users = $ci->AYN_Alert_historical_users_model->save($data_users);
						}
					}
					
				}
				
				if($id_client_module == "12"){ // Recordbook
				
					$recordbook = $ci->Recordbook_values_model->get_one($data["id_element"]);
					
					if($recordbook->proposito_visita == $alert_config["risk_value"] || $recordbook->proposito_visita == $alert_config["threshold_value"]) {
						
						$data["id_alert_projects"] = $alert_config_project->id;

						$data["is_email_sended"] = (!$alert_config_project->risk_email_alert && !$alert_config_project->threshold_email_alert) ? 1 : 0;
						$data["web_only"] = ($alert_config_project->risk_web_alert || $alert_config_project->threshold_web_alert) ? 1 : 0;
					
						$save_id = $ci->AYN_Alert_historical_model->save($data);
						foreach($alert_config_project_users as $alert_config_project_user){
							$profile_access = profile_access(
								$alert_config_project_user["id_user"], 
								$alert_config_project->id_client_module, 
								$alert_config_project->id_client_submodule, 
								"ver"
							);
							if($profile_access != 3){
								$data_users = array(
									"id_alert_historical" => $save_id,
									"id_user" => $alert_config_project_user["id_user"]
								);
								$save_users = $ci->AYN_Alert_historical_users_model->save($data_users);
							}
						}
						
					}
				
				}
				
			}

		}
		
	}
	
	if( !function_exists("get_alert_config") ) {
		
		function get_alert_config($info_options) {
			
			if($info_options->web_only){
						
				$ci = & get_instance();
				
				$array_result = array();
				$url = "";
				$ajax_url = "";
				
				$module_name = "";
				$submodule_name = "";
				$message = "";
				
				if($info_options->id_client_module){
					$module = $ci->Clients_modules_model->get_one($info_options->id_client_module);
					$module_name = $module->name;
					if($info_options->id_client_submodule){
						$submodule_name = $ci->Clients_submodules_model->get_one($info_options->id_client_submodule)->name;
					}
				}
				
				$id_element = (isset($info_options->id_element)) ? $info_options->id_element : "";
				$project_name = $ci->Projects_model->get_one($info_options->id_project)->title;
				
				// Registros Ambientales
				if($info_options->id_client_module == "2"){
					
					$element = $ci->Form_values_model->get_one_where(array("id" => $id_element));
					$form_rel_project = $ci->Form_rel_project_model->get_one($element->id_formulario_rel_proyecto);
					$id_form = $form_rel_project->id_formulario;
					$form_name = $ci->Forms_model->get_one($id_form)->nombre;
					
					$ajax_url = ($element->deleted) ? get_uri("AYN_Alert_historical/view_element_details") : get_uri("environmental_records/preview/".$id_form);
					$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
	
					$alert_config = json_decode($info_options->alert_config, TRUE);
					// ALÍAS
					$row_alias = $ci->Categories_alias_model->get_one_where(
						array(
							'id_categoria' => $alert_config["id_categoria"], 
							'id_cliente' => $ci->login_user->client_id, 
							'deleted' => 0
						)
					);
					if($row_alias->alias){
						$categoria = $row_alias->alias;
					}else{
						$categoria = $ci->Categories_model->get_one($alert_config["id_categoria"])->nombre;
					}
					//
					$unidad = $ci->Unity_model->get_one($alert_config["id_unidad"])->nombre;
					
					// Consultar la configuración de alertas que pertenezca al cliente - proyecto, al módulo, y a la categoría y unidad del formulario.
					$config_options = array(
						"id_client" => $info_options->id_client,
						"id_project" => $info_options->id_project,
						"id_client_module" => $info_options->id_client_module,
						"id_client_submodule" => $info_options->id_client_submodule,
						"alert_config" => $alert_config,
					);
					
					$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
					$alert_config_project_field = json_decode($alert_config_project->alert_config, TRUE);
					$valor_riesgo = $alert_config_project_field["risk_value"];
					$valor_umbral = $alert_config_project_field["threshold_value"];
					$suma_elementos = $alert_config["suma_elementos"];
					
					if( ($suma_elementos >= $valor_riesgo) && ($suma_elementos < $valor_umbral) ){
						
						$message = "<div>".lang("the_umbral_of")." ".$categoria." - ".$valor_umbral." ".$unidad." ".lang("in")." ".lang("record")." ".$form_name." ".lang("is_close_to_being_exceeded")."</div>";
						$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						
						$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => "caution");
					}
					
					if($suma_elementos >= $valor_umbral){
						
						$message = "<div>".lang("the_umbral_of")." ".$categoria." - ".$valor_umbral." ".$unidad." ".lang("in")." ".lang("record")." ".$form_name." ".lang("has_been_exceeded")."</div>";
						$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name."</div>";
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						
						$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => "alert");
					}
						
				}
				
				if($info_options->id_client_module == "6"){ // Compromisos
					
					if($info_options->id_client_submodule == "4"){ // Evaluación de Compromisos RCA
						
						$element = $ci->Compromises_compliance_evaluation_rca_model->get_one_where(array("id" => $id_element));
						$ajax_url = ($element->deleted) ? get_uri("AYN_Alert_historical/view_element_details") : get_uri("compromises_rca_evaluation/view/".$element->id);
						$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_proyecto='$info_options->id_project'";
	
						$alert_config = json_decode($info_options->alert_config, TRUE);
						$nombre_compromiso = $ci->Values_compromises_rca_model->get_one($alert_config["id_valor_compromiso"])->nombre_compromiso;
						
						// Consultar la configuración
						$config_options = array(
							"id_client" => $info_options->id_client,
							"id_project" => $info_options->id_project,
							"id_client_module" => $info_options->id_client_module,
							"id_client_submodule" => $info_options->id_client_submodule,
							"alert_config" => $alert_config,
						);
						
						$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
						$alert_config_project_field = json_decode($alert_config_project->alert_config, TRUE);
						$valor_riesgo = $alert_config_project_field["risk_value"];
						$valor_umbral = $alert_config_project_field["threshold_value"];
						$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
						$estado_evaluacion = $ci->Compromises_compliance_status_model->get_one($id_estado_evaluacion)->nombre_estado;
						
						$message = "<div>".lang("an_evaluation_has_been_entered")." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_compromiso."</div>";
						$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
						$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
						
						$message_type = "caution";
						if($id_estado_evaluacion == $valor_riesgo){
							$message_type = "caution";
						} elseif($id_estado_evaluacion == $valor_umbral) {
							$message_type = "alert";
						}
						
						$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
										
					}
					
					if($info_options->id_client_submodule == "22"){ // Evaluación de Compromisos Reportables
				
						$alert_config = json_decode($info_options->alert_config, TRUE);
						
						/*
							Se debe generar una alerta X días antes de la planificación (riesgo) si no se ha ingresado fecha de ejecución.
							Se debe generar una alerta X días después de la planificación (umbral) si no se ha ingresado fecha de ejecución.
						*/
						if($alert_config["id_planificacion"]){ // Planificación
							
							$planificacion = $ci->Plans_reportables_compromises_model->get_one($alert_config["id_planificacion"]);
							$valor_compromiso_reportable = $ci->Values_compromises_reportables_model->get_one($planificacion->id_compromiso)->nombre_compromiso;
							$fecha_actual = get_current_utc_time("Y-m-d");
							$fecha_planificacion = $planificacion->planificacion;
							$evaluacion = $ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array(
								"id_planificacion" => $planificacion->id,
								"deleted" => 0
							));
							
							if(!$evaluacion->modified_by){ // Si la evaluación no se ha editado, se genera la alerta
								
								// Consultar la configuración
								$config_options = array(
									"id_client" => $info_options->id_client,
									"id_project" => $info_options->id_project,
									"id_client_module" => $info_options->id_client_module,
									"id_client_submodule" => $info_options->id_client_submodule,
									"alert_config" => array("id_planificacion" => $alert_config["id_planificacion"]),
								);
								
								$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
								$alert_config_project_field = json_decode($alert_config_project->alert_config, TRUE);
								$valor_riesgo = $alert_config_project_field["risk_value"];
								$valor_umbral = $alert_config_project_field["threshold_value"];
								
								$dif_dias = strtotime($fecha_planificacion) - strtotime($fecha_actual);
								$dif_dias = round($dif_dias / (60 * 60 * 24));
								
								//echo "fecha_planificacion: ".$fecha_planificacion."<br>";
								//echo "fecha_actual: ".$fecha_actual."<br>";
								//echo "valor_riesgo: ".$valor_riesgo."<br>";
								//echo "valor_umbral: ".$valor_umbral."<br>";		
								
								if($dif_dias >= 0){ // Si fecha actual es menor o igual a fecha de planificacion (dentro del plazo)
									
									if($valor_riesgo >= $dif_dias){
										
										$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("compromises_reportables_evaluation/view/".$alert_config["id_planificacion"]);
										$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$evaluacion->id' data-post-id_evaluacion='$evaluacion->id' data-post-id_compromiso='$planificacion->id_compromiso' data-post-id_proyecto='$info_options->id_project'";
											
										$message_type = "reminder_caution";
										$cantidad_dias = $valor_riesgo;
										$message = "<div>".lang("according_to_the_planning")." ".$planificacion->descripcion.", ".lang("in")." ".$cantidad_dias." ".lang("days")." ".lang("it_must_be_reported")." ".$valor_compromiso_reportable." (".format_to_date($fecha_planificacion).")</div>";
										$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
										$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
										$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
										
									}
									
								} else { // fuera del plazo
									
									$dif_dias = $dif_dias * -1;
									//echo "dif_dias: ".$dif_dias."<br><br>";
									
									if($valor_umbral <= $dif_dias){
										
										$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("compromises_reportables_evaluation/view/".$alert_config["id_planificacion"]);
										$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$evaluacion->id' data-post-id_evaluacion='$evaluacion->id' data-post-id_compromiso='$planificacion->id_compromiso' data-post-id_proyecto='$info_options->id_project'";
										
										$message_type = "reminder_alert";
										$cantidad_dias = $valor_umbral;
										$message = "<div>".lang("according_to_the_planning")." ".$planificacion->descripcion.", hace ".$cantidad_dias." ".lang("days")." ".lang("it_should_have_been_reported")." ".$valor_compromiso_reportable." (".format_to_date($fecha_planificacion).")</div>";
										$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
										$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
										$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
										
									}
								
								}
	
							}
							
						} else {
							
							$element = $ci->Compromises_compliance_evaluation_reportables_model->get_one_where(array("id" => $id_element));
							
							$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("compromises_reportables_evaluation/view/".$element->id);
							$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_evaluacion='$element->id' data-post-id_compromiso='$element->id_valor_compromiso' data-post-id_proyecto='$info_options->id_project'";
							$nombre_compromiso = $ci->Values_compromises_reportables_model->get_one($alert_config["id_valor_compromiso"])->nombre_compromiso;
							
							// Consultar la configuración
							$config_options = array(
								"id_client" => $info_options->id_client,
								"id_project" => $info_options->id_project,
								"id_client_module" => $info_options->id_client_module,
								"id_client_submodule" => $info_options->id_client_submodule,
								"alert_config" => $alert_config,
							);
							
							$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
							$alert_config_project_field = json_decode($alert_config_project->alert_config, TRUE);
							$valor_riesgo = $alert_config_project_field["risk_value"];
							$valor_umbral = $alert_config_project_field["threshold_value"];
							$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
							$estado_evaluacion = $ci->Compromises_compliance_status_model->get_one($id_estado_evaluacion)->nombre_estado;
							
							$message = "<div>".lang("an_evaluation_has_been_entered")." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_compromiso."</div>";
							$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
							$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
							
							$message_type = "";
							if($id_estado_evaluacion == $valor_riesgo){
								
								// Se actualiza la fecha de histórico para que aparezca al principio del dropdown de alertas
								//$update_historical = $this->AYN_Alert_historical_model->save(array("alert_date" => get_current_utc_time()), $alert_config_project->id);
								$message_type = "caution";
								$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
							
							} elseif($id_estado_evaluacion == $valor_umbral) {
								
								// Se actualiza la fecha de histórico para que aparezca al principio del dropdown de alertas
								//$update_historical = $this->AYN_Alert_historical_model->save(array("alert_date" => get_current_utc_time()), $alert_config_project->id);
								$message_type = "alert";
								$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
							
							}
							
						}
						
					}
					
				}
				
				if($info_options->id_client_module == "7"){ // Permisos
				
					$element = $ci->Permitting_procedure_evaluation_model->get_one_where(array("id" => $id_element));
					$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("permitting_procedure_evaluation/view/".$element->id);
					$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_evaluacion='$element->id' data-post-id_proyecto='$info_options->id_project'";
					
					$alert_config = json_decode($info_options->alert_config, TRUE);
					$nombre_permiso = $ci->Values_permitting_model->get_one($alert_config["id_valor_permiso"])->nombre_permiso;
					
					// Consultar la configuración
					$config_options = array(
						"id_client" => $info_options->id_client,
						"id_project" => $info_options->id_project,
						"id_client_module" => $info_options->id_client_module,
						"id_client_submodule" => $info_options->id_client_submodule,
						//"alert_config" => $alert_config,
						"alert_config" => array(),
					);
					
					$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
					$alert_config_project_field = json_decode($alert_config_project->alert_config, TRUE);
					$valor_riesgo = $alert_config_project_field["risk_value"];
					$valor_umbral = $alert_config_project_field["threshold_value"];
					$id_estado_evaluacion = $alert_config["id_estado_evaluacion"];
					$estado_evaluacion = $ci->Permitting_procedure_status_model->get_one($id_estado_evaluacion)->nombre_estado;
						
					$message = "<div>".lang("an_evaluation_has_been_entered")." - ".$estado_evaluacion." - ".lang("in")." ".$nombre_permiso."</div>";
					$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
					$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
					
					$message_type = "";
					if($id_estado_evaluacion == $valor_riesgo){
						$message_type = "caution";
					} elseif($id_estado_evaluacion == $valor_umbral) {
						$message_type = "alert";
					}
					
					$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
						
					
				}
				
				
				if($info_options->id_client_module == "12"){ // Recordbook
					
					$element = $ci->Recordbook_values_model->get_one_where(array("id" => $id_element));
					$ajax_url = ($element->deleted) ? get_uri("AYN_Notif_historical/view_element_details") : get_uri("recordbook/view/".$element->id);
					$url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_url' data-post-id='$element->id' data-post-id_feedback_matrix_config='$element->id_recordbook_matrix_config' data-post-id_proyecto='$info_options->id_project'";
	
					$valor_recordbook = $ci->Recordbook_values_model->get_one($id_element);
					
					// Consultar la configuración
					$config_options = array(
						"id_client" => $info_options->id_client,
						"id_project" => $info_options->id_project,
						"id_client_module" => $info_options->id_client_module,
						"id_client_submodule" => $info_options->id_client_submodule,
						//"alert_config" => $alert_config,
					);
					
					$alert_config_project = $ci->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
					$alert_config_project_field = json_decode($alert_config_project->alert_config, TRUE);
					$valor_riesgo = $alert_config_project_field["risk_value"];
					$valor_umbral = $alert_config_project_field["threshold_value"];
							
					$message = "<div>".lang("a_recordbook_record_has_been_entered")." - ".$valor_recordbook->nombre." - ".lang("with")." ".lang($valor_recordbook->proposito_visita)." ".lang("as_visit_purpose")."</div>";
					$message .= "<div>"."<strong>".lang("module").": "."</strong>".$module_name." | ".$submodule_name."</div>";
					$message .= "<div>"."<strong>".lang("project").": "."</strong>".$project_name."</div>";
					
					$message_type = "";
					if($valor_recordbook->proposito_visita == $valor_riesgo){
						$message_type = "caution";
					} elseif($valor_recordbook->proposito_visita == $valor_umbral) {
						$message_type = "alert";
					}
					
					$array_result = array("url_attributes" => $url_attributes, "message" => $message, "message_type" => $message_type);	
					
				}
				
				return $array_result;
			}
			
		}
		
	}