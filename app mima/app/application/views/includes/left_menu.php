<?php //if($this->login_user->user_type == "client" && !$this->session->project_context){ ?>

<?php //}else{ ?>
<div id="sidebar" class="box-content ani-width <?php echo ($collapsed_left_menu) ? "collapsed" : ""; ?>">
    <div id="sidebar-scroll">
        <ul class="" id="sidebar-menu">
            <?php
            if ($this->login_user->user_type == "staff") {
                
                $sidebar_menu = array(
                    array("name" => "dashboard", "url" => "dashboard", "class" => "fa-desktop")
                );

                $permissions = $this->login_user->permissions;

                $access_expense = get_array_value($permissions, "expense");
                $access_invoice = get_array_value($permissions, "invoice");
                $access_ticket = get_array_value($permissions, "ticket");
                $access_client = get_array_value($permissions, "client");
                $access_timecard = get_array_value($permissions, "attendance");
                $access_leave = get_array_value($permissions, "leave");
                $access_estimate = get_array_value($permissions, "estimate");
                $access_items = ($this->login_user->is_admin || $access_invoice || $access_estimate);

                $manage_help_and_knowledge_base = ($this->login_user->is_admin || get_array_value($permissions, "help_and_knowledge_base"));
                $manage_timesheets = ($this->login_user->is_admin || get_array_value($permissions, "timesheet_manage_permission"));
                
                
				// AYUDA:
				$help_and_support_submenu = array(
                    array("name" => "faq", "url" => "faq", "controller" => "faq"),
                    array("name" => "wiki", "url" => "wiki", "controller" => "wiki"),
                    array("name" => "what_is_mimasoft", "url" => "what_is_mimasoft", "controller" => "what_is_mimasoft"),
                    //array("name" => "contact", "url" => "contact", "controller" => "contact")
				);
				$sidebar_menu[] = array("name" => "help_and_support", "url" => "help_and_support", "class" => "fa fa-question-circle","submenu" => $help_and_support_submenu);
				
				// PERFILES:
				$profiles_submenu = array(
                    array("name" => "generals", "url" => "generals", "controller" => "generals"),	
					array("name" => "projects", "url" => "profiles", "controller" => "profiles"),
                );
				$sidebar_menu[] = array("name" => "profiles", "url" => "#", "class" => "fa-unlock-alt", "submenu" => $profiles_submenu);
                
                // PROYECTOS:
				$projects_submenu = array(
                    array("name" => "platform_configuration", "url" => "general_settings", "controller" => "general_settings"),	
					array("name" => "clients", "url" => "clients", "controller" => "clients"),
					array("name" => "users", "url" => "users", "controller" => "users"),	
					array("name" => "projects", "url" => "projects", "controller" => "projects"),
					array("name" => "subprojects", "url" => "subprojects", "controller" => "subprojects"),
					array("name" => "sectors", "url" => "Air_sectors", "controller" => "Air_sectors"),
                );
				$sidebar_menu[] = array("name" => "projects", "url" => "administration", "class" => "fa-industry", "submenu" => $projects_submenu);
				
				// REGISTROS:
				$records_submenu = array(
					//array("name" => "bulk_creation", "url" => "admin_bulk_load", "controller" => "admin_bulk_load"),
                    array("name" => "fields", "url" => "fields", "controller" => "fields"),
                    array("name" => "forms", "url" => "forms", "controller" => "forms"),
					array("name" => "categories_alias", "url" => "categories_alias", "controller" => "categories_alias"),
					array("name" => "stations", "url" => "Air_stations", "controller" => "Air_stations"),
                );
                $sidebar_menu[] = array("name" => "records", "url" => "records", "class" => "fa-table", "submenu" => $records_submenu);
				
				// MODELO:
				$model_submenu = array(
					array("name" => "footprint_format", "url" => "footprint_format", "controller" => "footprint_format"),
                    array("name" => "databases", "url" => "databases", "controller" => "databases"),
                    array("name" => "methodologies", "url" => "methodologies", "controller" => "methodologies"),
					array("name" => "characterization_factors", "url" => "characterization_factors", "controller" => "characterization_factors"),
					array("name" => "footprints", "url" => "footprints", "controller" => "footprints"),
                    array("name" => "materials", "url" => "materials", "controller" => "materials"),
					array("name" => "categories", "url" => "categories", "controller" => "categories"),
					array("name" => "subcategories", "url" => "subcategories", "controller" => "subcategories"),
					array("name" => "relationship", "url" => "relationship", "controller" => "relationship"),
					//array("name" => "thresholds", "url" => "thresholds", "controller" => "thresholds"),
					
                );
                $sidebar_menu[] = array("name" => "model", "url" => "model", "class" => "fa-database", "submenu" => $model_submenu);
				
				// INDICADORES:
				$indicators_submenu = array(
                    array("name" => "functional_units", "url" => "functional_units", "controller" => "functional_units"),
					array("name" => "unit_processes", "url" => "unit_processes", "controller" => "unit_processes"),
                );
                $sidebar_menu[] = array("name" => "indicators_menu", "url" => "#", "class" => "fa-puzzle-piece", "submenu" => $indicators_submenu);
				
				// COMPROMISOS:
				$compromises_submenu = array(
					array("name" => "compliance_status", "url" => "compromises_compliance_status", "controller" => "compromises_compliance_status"),
					array("name" => "rca_matrix_config", "url" => "compromises_rca_matrix_config", "controller" => "compromises_rca_matrix_config"),
					array("name" => "reportable_matrix_config", "url" => "compromises_reportables_matrix_config", "controller" => "compromises_reportables_matrix_config"),
					array("name" => "upload_compromises", "url" => "upload_compromises", "controller" => "upload_compromises"),
				);

				$sidebar_menu[] = array("name" => "compromises", "url" => "#", "class" => "fa fa-handshake-o", "submenu" => $compromises_submenu);
				
				// PERMISOS
				$permitting_submenu = array(
					array("name" => "matrix_config", "url" => "permitting_matrix_config", "controller" => "permitting_matrix_config"),
					array("name" => "status_procedure", "url" => "permitting_procedure_status", "controller" => "permitting_procedure_status"),
					array("name" => "upload_permittings", "url" => "upload_permittings", "controller" => "upload_permittings"),
				);

				$sidebar_menu[] = array("name" => "permittings", "url" => "#", "class" => "fa fa-file-signature", "submenu" => $permitting_submenu);
				
				// RESIDUOS:
				$waste_submenu = array(
					array("name" => "indicators", "url" => "indicators", "controller" => "indicators"),
				);
				
				$sidebar_menu[] = array("name" => "waste", "url" => "#", "class" => "fa fa-exclamation-triangle", "submenu" => $waste_submenu);
				
				// COMUNIDADES
				$communities_submenu = array(
					array("name" => "stakeholders_config", "url" => "stakeholders_matrix_config", "controller" => "stakeholders_matrix_config"),
					array("name" => "agreement_config", "url" => "agreements_matrix_config", "controller" => "agreements_matrix_config"),
					array("name" => "feedback_config", "url" => "feedback_matrix_config", "controller" => "feedback_matrix_config"),
					array("name" => "agreements_status_config", "url" => "communities_evaluation_status", "controller" => "communities_evaluation_status"),
				);
				
				$sidebar_menu[] = array("name" => "communities", "url" => "#", "class" => "fa fa-users", "submenu" => $communities_submenu);
				
				// REPORTE ACV
				$acv_submenu = array(
					array("name" => "acv_report", "url" => "acv_report", "controller" => "acv_report"),
				);
				
				$sidebar_menu[] = array("name" => "acv", "url" => "#", "class" => "fa fa-file-text-o", "submenu" => $acv_submenu);
				
				// KPI
				$kpi_submenu = array(
					array("name" => "values", "url" => "KPI_Values", "controller" => "KPI_Values"),
					array("name" => "kpi_report", "url" => "KPI_Report", "controller" => "KPI_Report"),
					array("name" => "kpi_charts", "url" => "KPI_Charts", "controller" => "KPI_Charts"),
				);
				
				$sidebar_menu[] = array("name" => "kpi", "url" => "#", "class" => "fa fa-line-chart", "submenu" => $kpi_submenu);
				
				/*if($this->login_user->is_admin) {
                    $sidebar_menu[] = array("name" => "settings", "url" => "settings/general", "class" => "fa-wrench");
                }*/   
                
            } else {
                //client menu
				
				// DISPONIBILIDAD DE MÓDULOS A NIVEL DE CLIENTE
				$modulos = $this->Client_module_availability_model->get_client_setting($this->login_user->client_id)->result();		
				$array_modulos = array();
				foreach ($modulos as $modulo){
					$array_modulos[$modulo->id_modulo] = $modulo->disponible;
				}
				
				//Verifico si el contenido del módulo es visible para el usuario, si no lo es, se esconde el menú.
				$user = $this->Users_model->get_one($this->session->user_id);
				$client_context_profile_id = $user->id_client_context_profile;
				$client_context_modules_rel_profiles = $this->Client_context_modules_rel_profiles_model->get_all_where(array("id_client_context_profile" => $client_context_profile_id))->result();
				
				foreach($client_context_modules_rel_profiles as $rel){
				
					if($rel->id_client_context_submodule){
						
						if($rel->id_client_context_submodule == 1){ // FAQ - SUBMÓDULO AYUDA Y SOPORTE
							$faq_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 2){ // GLOSARIO - SUBMÓDULO AYUDA Y SOPORTE
							$glo_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 3){ // ¿QUÉ ES MIMASOFT? - SUBMÓDULO AYUDA Y SOPORTE
							$qem_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 4){ // CONTACTO - SUBMÓDULO AYUDA Y SOPORTE
							$con_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 5){ // KPI - REPORTE KPI
							$kpi_rep_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 6){ // KPI - GRAFICOS POR PROYECTO
							$kpi_gpp_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 7){ // KPI - GRAFICOS ENTRE PROYECTOS
							$kpi_gep_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 8){ // ECONOMÍA CIRCULAR - INDICADORES POR PROYECTOS
							$ec_ipp_option = $rel->ver;
						}
						if($rel->id_client_context_submodule == 9){ // ECONOMÍA CIRCULAR - INDICADORES ENTRE PROYECTOS
							$ec_iep_option = $rel->ver;
						}
					}
				}
				
				$sidebar_menu = array(
					array("name" => "home", "url" => "home", "controller" => "home", "class" => "fa fa-home")
				);
				
				if($this->session->menu_project_active){
					$sidebar_menu[] = array("name" => "projects", "url" => "inicio_projects", "controller" => "inicio_projects", "class" => "fa fa-th-large");	
				}
				
				if($this->session->menu_help_and_support_active){
					
					$help_knowledge_base_menues = array();
					// MÓDULO AYUDA Y SOPORTE
					if ($faq_option != 3) {
						$help_knowledge_base_menues[] = array("name" => "faq", "url" => "faq", "controller" => "faq"); // SUBMÓDULO FAQ
					}
					if ($glo_option != 3) {
						$help_knowledge_base_menues[] = array("name" => "wiki", "url" => "wiki", "controller" => "wiki"); //SUBMÓDULO GLOSARIO
					}
					if ($qem_option != 3) {
						$help_knowledge_base_menues[] = array("name" => "what_is_mimasoft", "url" => "what_is_mimasoft", "controller" => "what_is_mimasoft"); // SUBMÓDULO ¿QUE ES 	MIMASOFT?
					}
					if ($con_option != 3) {
						$help_knowledge_base_menues[] = array("name" => "contact", "url" => "contact", "controller" => "contact"); //SUBMÓDULO CONTACTO
					}
					$sidebar_menu[] = array("name" => "help_and_support", "url" => "help", "class" => "fa-question-circle",
						"submenu" => $help_knowledge_base_menues
					);
				}
				
				if($this->session->menu_kpi_active){
					if($kpi_rep_option != 3){
						$kpi_submenu[] = array("name" => "kpi_report", "url" => "KPI_Report", "controller" => "KPI_Report");
					}
					if($kpi_gpp_option != 3){
						$kpi_submenu[] = array("name" => "charts_by_project", "url" => "KPI_Charts_by_project", "controller" => "KPI_Charts_by_project");
					}
					if($kpi_gep_option != 3){
						$kpi_submenu[] = array("name" => "charts_between_projects", "url" => "KPI_Charts_between_projects", "controller" => "KPI_Charts_between_projects");
					}

					if(!empty($kpi_submenu)){
						$sidebar_menu[] = array("name" => "kpi", "url" => "#", "class" => "fa fa-line-chart", "submenu" => $kpi_submenu);
					}
				}
				
				if($this->session->menu_ec_active){
					if($ec_ipp_option != 3){
						$ec_submenu[] = array("name" => "ec_indicators_by_project", "url" => "EC_Indicators_by_project", "controller" => "EC_Indicators_by_project");
					}
					if($ec_iep_option != 3){
						$ec_submenu[] = array("name" => "ec_indicators_between_projects", "url" => "EC_Indicators_between_projects", "controller" => "EC_Indicators_between_projects");
					}

					if(!empty($ec_submenu)){
						$sidebar_menu[] = array("name" => "circular_economy", "url" => "#", "class" => "fa fa fa-line-chart", "submenu" => $ec_submenu);
					}
				}
				
				if($this->session->project_context){

					//Verifico si el contenido del módulo es visible para el usuario, si no lo es, se esconde el menú.
					$user = $this->Users_model->get_one($this->session->user_id);
					$profile_id = $user->id_profile;
					$clients_modules_rel_profiles = $this->Clients_modules_rel_profiles_model->get_all_where(array("id_profile" => $profile_id))->result();
					
					foreach($clients_modules_rel_profiles as $rel){
						
						if($rel->id_client_submodule){
							
							if($rel->id_client_submodule == 1){ // SUBMÓDULO UNIDADES FUNCIONALES (HUELLAS AMBIENTALES)
								$uf_option = $rel->ver;
							}
							if($rel->id_client_submodule == 2){ // SUBMÓDULO PROCESOS UNITARIOS (HUELLAS AMBIENTALES)
								$pu_option = $rel->ver;
							}
							if($rel->id_client_submodule == 3){ // SUBMÓDULO CUMPLIMIENTO DE COMPROMISOS (COMPROMISOS)
								$cump_comp_option = $rel->ver;
							}
							if($rel->id_client_submodule == 4){ // SUBMÓDULO EVALUACIÓN DE CUMPLIMIENTO (COMPROMISOS)
								$eval_cump_option = $rel->ver;
							}
							if($rel->id_client_submodule == 5){ // SUBMÓDULO TRAMITACIÓN DE PERMISOS (PERMISOS)
								$tramit_perm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 6){ // SUBMÓDULO EVALUACIÓN DE PERMISOS (PERMISOS)
								$eval_perm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 7){ // SUBMÓDULO RESUMEN (RESIDUOS)
								$waste_summary_option = $rel->ver;
							}
							if($rel->id_client_submodule == 8){ // SUBMÓDULO DETALLE (RESIDUOS)
								$waste_details_option = $rel->ver;
							}
							if($rel->id_client_submodule == 9){ // SUBMÓDULO INDICADORES (RESIDUOS)
								$waste_indicators_option = $rel->ver;
							}
							if($rel->id_client_submodule == 10){ // SUBMÓDULO RESUMEN (COMUNIDADES)
								$communities_summary_option = $rel->ver;
							}
							if($rel->id_client_submodule == 11){ // SUBMÓDULO STAKEHOLDERS (COMUNIDADES)
								$communities_stakeholders_option = $rel->ver;
							}
							if($rel->id_client_submodule == 12){ // SUBMÓDULO ACUERDOS (COMUNIDADES)
								$communities_agreements_option = $rel->ver;
							}
							if($rel->id_client_submodule == 13){ // SUBMÓDULO SEGUIMIENTO DE ACUERDOS (COMUNIDADES)
								$communities_agreements_monitoring_option = $rel->ver;
							}
							if($rel->id_client_submodule == 14){ // SUBMÓDULO FEEDBACK (COMUNIDADES)
								$communities_feedback_option = $rel->ver;
							}
							if($rel->id_client_submodule == 15){ // SUBMÓDULO SEGUIMIENTO DE FEEDBACK (COMUNIDADES)
								$communities_feedback_monitoring_option = $rel->ver;
							}
							if($rel->id_client_submodule == 16){ // SUBMÓDULO FAQ (AYUDA Y SOPORTE)
								$faq_option = $rel->ver;
							}
							if($rel->id_client_submodule == 17){ // SUBMÓDULO GLOSARIO (AYUDA Y SOPORTE)
								$wiki_option = $rel->ver;
							}
							if($rel->id_client_submodule == 18){ // SUBMÓDULO ¿QUÉ ES MIMASOFT? (AYUDA Y SOPORTE)
								$what_mima_option = $rel->ver;
							}
							if($rel->id_client_submodule == 19){ // SUBMÓDULO CONTACTO (AYUDA Y SOPORTE)
								$contact_option = $rel->ver;
							}
							if($rel->id_client_submodule == 20){ // SUBMÓDULO CONFIGURACIÓN PANEL PRINCIPAL (ADMINISTRACIÓN CLIENTE)
								$config_pp_option = $rel->ver;
							}
							if($rel->id_client_submodule == 21){ // SUBMÓDULO CARGA MASIVA (ADMINISTRACIÓN CLIENTE)
								$cm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 27){ // SUBMÓDULO CARGA MASIVA (AIRE)
								$cma_option = $rel->ver;
							}
							if($rel->id_client_submodule == 22){ // SUBMÓDULO COMPROMISOS REPORTABLES
								$rep_comp_option = $rel->ver;
							}
							if($rel->id_client_submodule == 23){ // SUBMÓDULO REGISTROS DE MONITOREO
								$air_rm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 24){ // SUBMÓDULO REGISTROS DE PRONÓSTICO
								$air_rp_option = $rel->ver;
							}
							if($rel->id_client_submodule == 28){ // SUBMÓDULOS DINÁMICOS SECTORES
								$air_sec_option = $rel->ver;
							}
							if($rel->id_client_submodule == 29){ // SUBMÓDULO DESCARGA MASIVA (AIRE)
								$dma_option = $rel->ver;
							}
							if($rel->id_client_submodule == 30){ // SUBMÓDULO CARGA DE DATOS SINÓPTICOS (AIRE)
								$cdsa_option = $rel->ver;
							}
							if($rel->id_client_submodule == 31){ // SUBMÓDULO GRÁFICOS DE MONITOREO (AIRE)
								$air_monitoring_charts_option = $rel->ver;
							}
							if($rel->id_client_submodule == 32){ // SUBMÓDULO REGISTROS DE MONITOREO (AIRE)
								$air_monitoring_records_option = $rel->ver;
							}
							if($rel->id_client_submodule == 33){ // SUBMÓDULO VER IMÁGENES (AIRE)
								$air_upload_images_option = $rel->ver;
							}
							if($rel->id_client_submodule == 34){ // SUBMÓDULO CARGAR IMÁGENES (AIRE)
								$air_display_images_option = $rel->ver;
							}
							if($rel->id_client_submodule == 35){ // SUBMÓDULO EFICIENCIA DE MONITOREO (AIRE)
								$air_monitoring_efficiency_option = $rel->ver;
							}
							if($rel->id_client_submodule == 36){ // SUBMÓDULO FORZAR CORREO DE PRONÓSTICOS (AIRE)
								$air_fcp_option = $rel->ver;
							}

						} else {
							
							if($rel->id_client_module == 2){ // REGISTROS AMBIENTALES
								$ra_option = $rel->ver;
							}
							if($rel->id_client_module == 3){ // MANTENEDORAS
								$fe_option = $rel->ver;
							}
							if($rel->id_client_module == 4){ // OTROS REGISTROS
								$or_option = $rel->ver;
							}
							if($rel->id_client_module == 5){ // REPORTES
								$rep_option = $rel->ver;
							}
							if($rel->id_client_module == 16){ // RESUMEN DE PRONÓSTICOS (AIRE)
								$forecast_summary_option = $rel->ver;
							}
							if($rel->id_client_module == 18){ // DESEMPEÑO DE PRONÓSTICOS (AIRE)
								$forecast_performance_option = $rel->ver;
							}
							if($rel->id_client_module == 19){ // COMPARACIÓN DE PRONÓSTICOS (AIRE)
								$forecast_comparison_option = $rel->ver;
							}
						}

					}
					
					// DISPONIBILIDAD DE MÓDULOS
					$modulos = $this->Module_availability_model->get_project_setting($this->login_user->client_id, $this->session->project_context)->result();		
					$array_modulos = array();
					foreach ($modulos as $modulo){
						$array_modulos[$modulo->id_modulo_cliente] = $modulo->available;
 					}
					
                    //$sidebar_menu[] = array("name" => "dashboard", "url" => "dashboard/view/".$this->session->project_context, "class" => "fa-desktop");
                    // $sidebar_menu[] = array("name" => "project_info", "url" => "project_info", "class" => "fa-info-circle");
					
					// MÓDULO HUELLAS AMBIENTALES
                    $environmental_footprints_submenu = array();
					
					if($array_modulos[1] == 1){ 
						if($uf_option != 3){ // SUBMÓDULO UNIDADES FUNCIONALES
							$environmental_footprints_submenu[] = array("name" => "functional_units", "url" => "functional_units", "controller" => "functional_units"); 
						}
						if($pu_option != 3){ // SUBMÓDULO PROCESOS UNITARIOS
							$environmental_footprints_submenu[] = array("name" => "unit_processes", "url" => "unit_processes", "controller" => "unit_processes");
						}
					}
					
					if(!($array_modulos[1] == 0)){
						if(!empty($environmental_footprints_submenu))
						$sidebar_menu[] = array("name" => "environmental_footprints", "url" => "environmental_footprints", "class" => "fa-shoe-prints","submenu" => $environmental_footprints_submenu);
					}
					
					$array_registros_submenu = array();
					if($array_modulos[2] == 1 || $array_modulos[3] == 1 || $array_modulos[4] == 1){
						if($array_modulos[2] == 1 && $ra_option != 3){
							$array_registros_submenu[] = array("name" => "environmental_records", "url" => "environmental_records", "class" => "fa-leaf", "controller" => "environmental_records");
						}
						if($array_modulos[3] == 1 && $fe_option != 3){
							$array_registros_submenu[] = array("name" => "feeders", "url" => "feeders", "class" => "fa-table", "controller" => "feeders");
						}
						if($array_modulos[4] == 1 && $or_option != 3){
							$array_registros_submenu[] = array("name" => "other_records", "url" => "other_records", "class" => "fa-th-list", "controller" => "other_records");
						}
					}
					
					if( !($array_modulos[2] == 0) || !($array_modulos[3] == 0) || !($array_modulos[4] == 0) ){
						if(!empty($array_registros_submenu))
						$sidebar_menu[] = array("name" => "records", "url" => "#", "class" => "fa-th-list", "submenu" => $array_registros_submenu);			
					}


					
					// MÓDULO PRONÓSTICOS
					$air_sectors = $this->Air_sectors_model->get_all_where(array(
						"id_project" => $this->session->project_context,
						"deleted" => 0
					))->result();

					// $array_forecast_submenu = array();
					// foreach($air_sectors as $sector){
					// 	// Encriptado de $sector->id
					// 	$id_sector_encrypt = urlencode($this->encrypt->encode($sector->id));
					// 	//$array_forecast_submenu[] = array("name" => $sector->name, "url" => "air_forecast_sectors/index/?p=".$id_sector_encrypt, "controller" => "air_forecast_sectors", "param" => $id_sector_encrypt);
					// 	$array_forecast_submenu[] = array("name" => $sector->name, "url" => "air_forecast_sectors/index/".$id_sector_encrypt, "controller" => "air_forecast_sectors", "param" => $id_sector_encrypt);
					// }

					// if($array_modulos[14] == 1){ 
					// 	if($air_sectors && $air_sec_option != 3){
					// 		$sidebar_menu[] = array("name" => "forecasts", "url" => "#", "class" => "fa-sun", "submenu" => $array_forecast_submenu);
					// 	}
					// }
					if($array_modulos[14] == 1){ 
						foreach($air_sectors as $sector){
							// Encriptado de $sector->id
							$id_sector_encrypt = urlencode($this->encrypt->encode($sector->id));
							$sidebar_menu[] = array("name" => "forecasts", "url" => "air_forecast_sectors/index/".$id_sector_encrypt, "class" => "fa fa-sun", "param" => $id_sector_encrypt);
						}
					}

					if($array_modulos[16] == 1){ 
						if($air_sectors && $forecast_summary_option != 3){
							$sidebar_menu[] = array("name" => "air_forecast_summary", "url" => "air_forecast_summary", "controller" => "air_forecast_summary", "class" => "fa fa-sun");
						}
					}

					if($array_modulos[18] == 1){ 
						if($air_sectors && $forecast_performance_option != 3){
							$sidebar_menu[] = array("name" => "air_forecast_performance", "url" => "air_forecast_performance", "controller" => "air_forecast_performance", "class" => "fa fa-bar-chart");
						}
					}

					if($array_modulos[19] == 1){ 
						if($air_sectors && $forecast_comparison_option != 3){
							$sidebar_menu[] = array("name" => "air_forecast_comparison", "url" => "air_forecast_comparison", "controller" => "air_forecast_comparison", "class" => "fa fa-bar-chart");
                        }
					}

                    // Modulo Reportes centinela
                    $air_sectors = $this->Air_sectors_model->get_all_where(array(
                        "id_project" => $this->session->project_context,
                        "deleted" => 0
                    ))->result();

					
                   // $sidebar_menu[] = array("name" => "camera_forecasts","url"=> "camaras", "controller" => "camaras", "class" => "fa fa-camera");
				    $sidebar_menu[] = array("name" => "report_sqm_variables", "url" => "report_sqm_variables", "controller" => "report_sqm_variables", "class" => "fa fa-dashboard");
                    $sidebar_menu[] = array("name" => "report_sqm_pm10", "url" => "report_sqm_pm10", "controller" => "report_sqm_pm10", "class" => "fa fa-area-chart");
                    $sidebar_menu[] = array("name" => "report_sqm_so2", "url" => "report_sqm_so2", "controller" => "report_sqm_so2", "class" => "fa fa-check-square-o");
                    //$sidebar_menu[] = array("name" => "report_sqm_viento", "url" => "report_sqm_viento", "controller" => "report_sqm_viento", "class" => "fa fa-check-square-o");
                    $sidebar_menu[] = array("name" => "report_auto", "url" => "report_auto", "controller" => "report_auto", "class" => "fa fa-check-square-o");
                    //$sidebar_menu[] = array("name" => "report_sqm", "url" => "report_sqm", "controller" => "report_sqm", "class" => "fa fa-check-square-o");

					// Validación para grupo 7 y 8
					if (isset($this->login_user->id_client_group) && in_array($this->login_user->id_client_group, [6, 7, 8])) {
						$sidebar_menu[] = array(
							"name" => "report_sqm_hospital",
							"url" => "report_sqm_hospital",
							"controller" => "report_sqm_hospital",
							"class" => "fa fa-star"
						);
					}

					// MÓDULO CONDICIONES METEOROLÓGICAS
					$array_air_meteorological_conditions_submenu = array();
					if($array_modulos[20] == 1){ // MÓDULO CONDICIONES METEOROLÓGICAS
						if($air_upload_images_option != 3){ // SUBMÓDULO VER IMAGENES
							$array_air_meteorological_conditions_submenu[] = array("name" => "display_images", "url" => "air_mc_display_images", "controller" => "air_mc_display_images");
						}
						if($air_display_images_option != 3){ // SUBMÓDULO CARGAR IMAGENES
							$array_air_meteorological_conditions_submenu[] = array("name" => "upload_images", "url" => "air_mc_upload_images", "controller" => "air_mc_upload_images");
						}
					}

					if( !($array_modulos[20] == 0)){
						if(!empty($array_air_meteorological_conditions_submenu))
						$sidebar_menu[] = array("name" => "meteorological_conditions", "url" => "#", "class" => "fa-image", "submenu" => $array_air_meteorological_conditions_submenu);			
					}

					// MÓDULO MONITOREO
					/*$array_air_monitoring_submenu = array();
					
					if($array_modulos[17] == 1){ 
						log_message('error',"que es esto : ".json_encode($array_modules));
						if($air_monitoring_charts_option != 3){ // GRÁFICOS
							$array_air_monitoring_submenu[] = array("name" => "air_charts", "url" => "air_monitoring_charts", "controller" => "air_monitoring_charts");
						}
						if($air_monitoring_records_option != 3){ // REGISTROS
							$array_air_monitoring_submenu[] = array("name" => "air_records", "url" => "air_monitoring_data", "controller" => "air_monitoring_data");
						}
						if($air_monitoring_efficiency_option != 3){ // EFICIENCIA
							$array_air_monitoring_submenu[] = array("name" => "air_efficiency", "url" => "air_monitoring_efficiency", "controller" => "air_monitoring_efficiency");
						}
					}

					if( !($array_modulos[17] == 0)){
						if(!empty($array_air_monitoring_submenu))
							$sidebar_menu[] = array("name" => "air_monitoring", "url" => "#", "class" => "fa-th-list", "submenu" => $array_air_monitoring_submenu);			
					}*/
					
					// MÓDULO REGISTROS CALIDAD DEL AIRE
					$array_air_quality_records_submenu = array();
					if($array_modulos[12] == 1){ // MÓDULO REGISTROS CALIDAD DEL AIRE
						if($air_rm_option != 3){ // SUBMÓDULO REGISTROS DE MONITOREO
							$array_air_quality_records_submenu[] = array("name" => "monitoring_records", "url" => "air_monitoring_records", "controller" => "air_monitoring_records");
						}
						if($air_rp_option != 3){ // SUBMÓDULO REGISTROS DE PRONÓSTICO
							$array_air_quality_records_submenu[] = array("name" => "forecast_records", "url" => "air_forecast_records", "controller" => "air_forecast_records");
						}
					}

					if( !($array_modulos[12] == 0)){
						if(!empty($array_air_quality_records_submenu))
						$sidebar_menu[] = array("name" => "air_quality_records", "url" => "#", "class" => "fa-th-list", "submenu" => $array_air_quality_records_submenu);			
					}

					if($array_modulos[5] == 1){ // MÓDULO REPORTES
						if($rep_option != 3)
						$sidebar_menu[] = array("name" => "reports", "url" => "reports", "class" => "fa-line-chart");
					}

                    // MÓDULO COMPROMISOS
					$compromises_submenu = array();
					
					if($array_modulos[6] == 1){
						
						if($cump_comp_option != 3){ // SUBMÓDULO CUMPLIMIENTO DE COMPROMISOS
							$compromises_submenu[] = array("name" => "compromises_compliance", "url" => "compromises_compliance_client", "controller" => "compromises_compliance_client");
						}
						/*if($eval_cump_option != 3){ // SUBMÓDULO EVALUACIÓN DE CUMPLIMIENTO
							$compromises_submenu[] = array("name" => "compliance_evaluation", "url" => "compromises_compliance_evaluation", "controller" => "compromises_compliance_evaluation");
						}*/
						if($eval_cump_option != 3){
							$compromises_submenu[] = array("name" => "compromises_rca_evaluation", "url" => "compromises_rca_evaluation", "controller" => "compromises_rca_evaluation");
						}
						if($rep_comp_option != 3){
							$compromises_submenu[] = array("name" => "compromises_reportables_evaluation", "url" => "compromises_reportables_evaluation", "controller" => "compromises_reportables_evaluation");
						}
						
					}
					
					if(!($array_modulos[6] == 0)){
						if(!empty($compromises_submenu))
						$sidebar_menu[] = array("name" => "compromises", "url" => "#", "class" => "fa fa-handshake-o", "submenu" => $compromises_submenu);
					}
					
					// MÓDULO PERMISOS	
					$permitting_submenu = array();
					
					if($array_modulos[7] == 1){ 
						if($tramit_perm_option != 3){ // SUBMÓDULO TRAMITACION DE PERMISOS
							$permitting_submenu[] = array("name" => "permittings_procedure", "url" => "permitting_procedure_client", "controller" => "permitting_procedure_client");
						}
						if($eval_perm_option != 3){ // SUBMÓDULO EVALUACIÓN DE PERMISOS
							$permitting_submenu[] = array("name" => "permittings_evaluation", "url" => "permitting_procedure_evaluation", "controller" => "permitting_procedure_evaluation");
						}
					}
					
					if(!($array_modulos[7] == 0)){
						if(!empty($permitting_submenu))
						$sidebar_menu[] = array("name" => "permittings", "url" => "#", "class" => "fa fa-file-signature", "submenu" => $permitting_submenu);
					}
					
					// MÓDULO RESIDUOS
					$waste_submenu = array();
					
					if($array_modulos[8] == 1){
						if($waste_summary_option != 3){ // SUBMÓDULO RESUMEN
							$waste_submenu[] = array("name" => "summary", "url" => "waste_summary", "controller" => "waste_summary");
						}
						if($waste_details_option != 3){ // SUBMÓDULO DETALLE
							$waste_submenu[] = array("name" => "detail", "url" => "client_waste_detail", "controller" => "client_waste_detail");
						}
						if($waste_indicators_option != 3){ // SUBMÓDULO INDICADORES
							$waste_submenu[] = array("name" => "indicators", "url" => "client_indicators", "controller" => "client_indicators");
						}
					}
					
					if(!($array_modulos[8] == 0)){
						if(!empty($waste_submenu))
						$sidebar_menu[] = array("name" => "waste", "url" => "#", "class" => "fa fa-exclamation-triangle", "submenu" => $waste_submenu);
					}
					
					// MÓDULO COMUNIDADES
					$communities_submenu = array();
	
					if($array_modulos[9] == 1){
						if($communities_summary_option != 3){ // SUBMÓDULO RESUMEN 
							$communities_submenu[] = array("name" => "summary", "url" => "communities_agreements_summary", "controller" => "communities_agreements_summary");
						}
						if($communities_stakeholders_option != 3){ // SUBMÓDULO STAKEHOLDERS 
							$communities_submenu[] = array("name" => "stakeholders", "url" => "communities_stakeholders", "controller" => "communities_stakeholders");
						}
						if($communities_agreements_option != 3){ // SUBMÓDULO ACUERDOS 
							$communities_submenu[] = array("name" => "agreements", "url" => "communities_agreements", "controller" => "communities_agreements");
						}
						if($communities_agreements_monitoring_option != 3){ // SUBMÓDULO SEGUIMIENTO ACUERDOS 
							$communities_submenu[] = array("name" => "agreements_monitoring", "url" => "agreements_monitoring", "controller" => "agreements_monitoring");
						}
						if($communities_feedback_option != 3){ // SUBMÓDULO FEEDBACK 
							$communities_submenu[] = array("name" => "feedback", "url" => "communities_feedback", "controller" => "communities_feedback");
						}
						if($communities_feedback_monitoring_option != 3){ // SUBMÓDULO SEGUIMIENTO FEEDBACK 
							$communities_submenu[] = array("name" => "feedback_monitoring", "url" => "feedback_monitoring", "controller" => "feedback_monitoring");
						}
					}
					
					if(!($array_modulos[9] == 0)){
						if(!empty($communities_submenu))
						$sidebar_menu[] = array("name" => "communities", "url" => "#", "class" => "fa fa-users", "submenu" => $communities_submenu);
					}
					

					// ADMINISTRACIÓN CLIENTE SGE
					$customer_administrator_submenu = array();
					
					if($array_modulos[11] == 1){ 
						if($config_pp_option != 3){ //CONFIGURACION PANEL PRINCIPAL
							$customer_administrator_submenu[] = array("name" => "setting_dashboard", "url" => "setting_dashboard", "controller" => "setting_dashboard");
						}
						if($cm_option != 3){ // SUBMÓDULO CARGA MASIVA
							$customer_administrator_submenu[] = array("name" => "setting_bulk_load", "url" => "setting_bulk_load", "controller" => "setting_bulk_load");
						}
					}

                    if(!($array_modulos[11] == 0)){ 
						if (!empty($customer_administrator_submenu))
						$sidebar_menu[] = array("name" => "customer_administrator", "url" => "customer_administrator", "class" => "fa fa-cogs","submenu" => $customer_administrator_submenu);	
					}


					// ADMINISTRACIÓN CLIENTE MIMAIRE
 					if($array_modulos[15] == 1){ 
						if($cma_option != 3){ // SUBMÓDULO CARGA MASIVA AIRE
							$customer_administrator_air_submenu[] = array("name" => "setting_bulk_load_air", "url" => "air_setting_bulk_load", "controller" => "air_setting_bulk_load");
						}
						if($cdsa_option != 3){ // SUBMÓDULO CARGA DE DATOS SINÓPTICOS
							$customer_administrator_air_submenu[] = array("name" => "synoptic_data_upload", "url" => "air_synoptic_data_upload", "controller" => "air_synoptic_data_upload");
						}
						if($dma_option != 3){ // SUBMÓDULO DESCARGA MASIVA AIRE
							$customer_administrator_air_submenu[] = array("name" => "setting_bulk_download_air", "url" => "air_setting_bulk_download", "controller" => "air_setting_bulk_download");
						}
					}
						if($air_fcp_option != 3){ // SUBMÓDULO FORZAR CORREO DE PRONÓSTICOS
							$customer_administrator_air_submenu[] = array("name" => "force_sending_forecast_alert", "url" => "air_force_sending_forecast_alert", "controller" => "air_force_sending_forecast_alert");
						}
					}
					

                    if(!($array_modulos[15] == 0)){ 
						if (!empty($customer_administrator_air_submenu)){
							$sidebar_menu[] = array("name" => "customer_administrator_air", "url" => "customer_administrator_air", "class" => "fa fa-cogs","submenu" => $customer_administrator_air_submenu);	
						}
					}

					//$sidebar_menu[] = array("name" => "tutorials", "url" => "tutorials", "controller" => "tutorials", "class" => "fa-video");

                }
				
                /*if(!$this->session->project_context){
                    	
						$modulos = $this->Client_module_availability_model->get_client_setting($this->login_user->client_id)->result();
						$array_modulos = array();
						foreach ($modulos as $modulo){
							$array_modulos[$modulo->id_modulo] = $modulo->disponible;
						}
						
						$help_knowledge_base_menues = array();
						
						if($array_modulos[1] == 1){ // MÓDULO AYUDA Y SOPORTE
							$help_knowledge_base_menues[] = array("name" => "faq", "url" => "faq", "controller" => "faq"); // SUBMÓDULO FAQ
							$help_knowledge_base_menues[] = array("name" => "wiki", "url" => "wiki", "controller" => "wiki"); //SUBMÓDULO GLOSARIO
							$help_knowledge_base_menues[] = array("name" => "what_is_mimasoft", "url" => "what_is_mimasoft", "controller" => "what_is_mimasoft"); // SUBMÓDULO ¿QUE ES MIMASOFT?
							$help_knowledge_base_menues[] = array("name" => "contact", "url" => "contact", "controller" => "contact"); //SUBMÓDULO CONTACTO
						}
						
                        $sidebar_menu = array(
                            array("name" => "projects", "url" => "inicio_projects", "controller" => "inicio_projects", "class" => "fa fa-th-large")
                        );
						
						if(!($array_modulos[1] == 0)){
							$sidebar_menu[] = array("name" => "help_and_support", "url" => "help", "class" => "fa-question-circle",
								"submenu" => $help_knowledge_base_menues
							);
						}

                }else{

					//Verifico si el contenido del módulo es visible para el usuario, si no lo es, se esconde el menú.
					$user = $this->Users_model->get_one($this->session->user_id);
					$profile_id = $user->id_profile;
					$clients_modules_rel_profiles = $this->Clients_modules_rel_profiles_model->get_all_where(array("id_profile" => $profile_id))->result();
					
					foreach($clients_modules_rel_profiles as $rel){
						
						if($rel->id_client_submodule){
							
							if($rel->id_client_submodule == 1){ // SUBMÓDULO UNIDADES FUNCIONALES (HUELLAS AMBIENTALES)
								$uf_option = $rel->ver;
							}
							if($rel->id_client_submodule == 2){ // SUBMÓDULO PROCESOS UNITARIOS (HUELLAS AMBIENTALES)
								$pu_option = $rel->ver;
							}
							if($rel->id_client_submodule == 3){ // SUBMÓDULO CUMPLIMIENTO DE COMPROMISOS (COMPROMISOS)
								$cump_comp_option = $rel->ver;
							}
							if($rel->id_client_submodule == 4){ // SUBMÓDULO EVALUACIÓN DE CUMPLIMIENTO (COMPROMISOS)
								$eval_cump_option = $rel->ver;
							}
							if($rel->id_client_submodule == 5){ // SUBMÓDULO TRAMITACIÓN DE PERMISOS (PERMISOS)
								$tramit_perm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 6){ // SUBMÓDULO EVALUACIÓN DE PERMISOS (PERMISOS)
								$eval_perm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 7){ // SUBMÓDULO RESUMEN (RESIDUOS)
								$waste_summary_option = $rel->ver;
							}
							if($rel->id_client_submodule == 8){ // SUBMÓDULO DETALLE (RESIDUOS)
								$waste_details_option = $rel->ver;
							}
							if($rel->id_client_submodule == 9){ // SUBMÓDULO INDICADORES (RESIDUOS)
								$waste_indicators_option = $rel->ver;
							}
							if($rel->id_client_submodule == 10){ // SUBMÓDULO RESUMEN (COMUNIDADES)
								$communities_summary_option = $rel->ver;
							}
							if($rel->id_client_submodule == 11){ // SUBMÓDULO STAKEHOLDERS (COMUNIDADES)
								$communities_stakeholders_option = $rel->ver;
							}
							if($rel->id_client_submodule == 12){ // SUBMÓDULO ACUERDOS (COMUNIDADES)
								$communities_agreements_option = $rel->ver;
							}
							if($rel->id_client_submodule == 13){ // SUBMÓDULO SEGUIMIENTO DE ACUERDOS (COMUNIDADES)
								$communities_agreements_monitoring_option = $rel->ver;
							}
							if($rel->id_client_submodule == 14){ // SUBMÓDULO FEEDBACK (COMUNIDADES)
								$communities_feedback_option = $rel->ver;
							}
							if($rel->id_client_submodule == 15){ // SUBMÓDULO SEGUIMIENTO DE FEEDBACK (COMUNIDADES)
								$communities_feedback_monitoring_option = $rel->ver;
							}
							if($rel->id_client_submodule == 16){ // SUBMÓDULO FAQ (AYUDA Y SOPORTE)
								$faq_option = $rel->ver;
							}
							if($rel->id_client_submodule == 17){ // SUBMÓDULO GLOSARIO (AYUDA Y SOPORTE)
								$wiki_option = $rel->ver;
							}
							if($rel->id_client_submodule == 18){ // SUBMÓDULO ¿QUÉ ES MIMASOFT? (AYUDA Y SOPORTE)
								$what_mima_option = $rel->ver;
							}
							if($rel->id_client_submodule == 19){ // SUBMÓDULO CONTACTO (AYUDA Y SOPORTE)
								$contact_option = $rel->ver;
							}
							if($rel->id_client_submodule == 20){ // SUBMÓDULO CONFIGURACIÓN PANEL PRINCIPAL (ADMINISTRACIÓN CLIENTE)
								$config_pp_option = $rel->ver;
							}
							if($rel->id_client_submodule == 21){ // SUBMÓDULO CARGA MASIVA (ADMINISTRACIÓN CLIENTE)
								$cm_option = $rel->ver;
							}
							if($rel->id_client_submodule == 22){ // SUBMÓDULO COMPROMISOS REPORTABLES
								$rep_comp_option = $rel->ver;
							}
							
						} else {
							
							if($rel->id_client_module == 2){ // REGISTROS AMBIENTALES
								$ra_option = $rel->ver;
							}
							if($rel->id_client_module == 3){ // MANTENEDORAS
								$fe_option = $rel->ver;
							}
							if($rel->id_client_module == 4){ // OTROS REGISTROS
								$or_option = $rel->ver;
							}
							if($rel->id_client_module == 5){ // REPORTES
								$rep_option = $rel->ver;
							}
							
						}

					}
					
					// DISPONIBILIDAD DE MÓDULOS
					$modulos = $this->Module_availability_model->get_project_setting($this->login_user->client_id, $this->session->project_context)->result();		
					$array_modulos = array();
					foreach ($modulos as $modulo){
						$array_modulos[$modulo->id_modulo_cliente] = $modulo->available;
 					}
					
                    $sidebar_menu = array(
                        array("name" => "projects", "url" => "inicio_projects", "url" =>"inicio_projects",  "class" => "fa fa fa-th-large"),
                    );
					
                    $sidebar_menu[] = array("name" => "dashboard", "url" => "dashboard/view/".$this->session->project_context, "class" => "fa-desktop");
                    $sidebar_menu[] = array("name" => "project_info", "url" => "project_info", "class" => "fa-info-circle");
					
					// MÓDULO HUELLAS AMBIENTALES
                    $environmental_footprints_submenu = array();
					
					if($array_modulos[1] == 1){ 
						if($uf_option != 3){ // SUBMÓDULO UNIDADES FUNCIONALES
							$environmental_footprints_submenu[] = array("name" => "functional_units", "url" => "functional_units", "controller" => "functional_units"); 
						}
						if($pu_option != 3){ // SUBMÓDULO PROCESOS UNITARIOS
							$environmental_footprints_submenu[] = array("name" => "unit_processes", "url" => "unit_processes", "controller" => "unit_processes");
						}
					}
					
					if(!($array_modulos[1] == 0)){
						if(!empty($environmental_footprints_submenu))
						$sidebar_menu[] = array("name" => "environmental_footprints", "url" => "environmental_footprints", "class" => "fa-shoe-prints","submenu" => $environmental_footprints_submenu);
					}

					if($array_modulos[2] == 1){ // MÓDULO REGISTROS AMBIENTALES
						if($ra_option != 3)
						$sidebar_menu[] = array("name" => "environmental_records", "url" => "environmental_records", "class" => "fa-leaf");
					}
					
					if($array_modulos[3] == 1){ // MÓDULO MANTENEDORAS
						if($fe_option != 3)
						$sidebar_menu[] = array("name" => "feeders", "url" => "feeders", "class" => "fa-table");
					}
					
					if($array_modulos[4] == 1){ // MÓDULO OTROS REGISTROS
						if($or_option != 3)
						 $sidebar_menu[] = array("name" => "other_records", "url" => "other_records", "class" => "fa-th-list");
					}

					if($array_modulos[5] == 1){ // MÓDULO REPORTES
						if($rep_option != 3)
						$sidebar_menu[] = array("name" => "reports", "url" => "reports", "class" => "fa-line-chart");
					}

                    // MÓDULO COMPROMISOS
					$compromises_submenu = array();
					
					if($array_modulos[6] == 1){ 
						if($cump_comp_option != 3){ // SUBMÓDULO CUMPLIMIENTO DE COMPROMISOS
							$compromises_submenu[] = array("name" => "compromises_compliance", "url" => "compromises_compliance_client", "controller" => "compromises_compliance_client");
						}
						if($eval_cump_option != 3){
							$compromises_submenu[] = array("name" => "compromises_rca_evaluation", "url" => "compromises_rca_evaluation", "controller" => "compromises_rca_evaluation");
						}
						
						if($rep_comp_option != 3){
							$compromises_submenu[] = array("name" => "compromises_reportables_evaluation", "url" => "compromises_reportables_evaluation", "controller" => "compromises_reportables_evaluation");
						}
					}
					
					if(!($array_modulos[6] == 0)){
						if(!empty($compromises_submenu))
						$sidebar_menu[] = array("name" => "compromises", "url" => "#", "class" => "fa fa-handshake-o", "submenu" => $compromises_submenu);
					}
					
					// MÓDULO PERMISOS	
					$permitting_submenu = array();
					
					if($array_modulos[7] == 1){ 
						if($tramit_perm_option != 3){ // SUBMÓDULO TRAMITACION DE PERMISOS
							$permitting_submenu[] = array("name" => "permittings_procedure", "url" => "permitting_procedure_client", "controller" => "permitting_procedure_client");
						}
						if($eval_perm_option != 3){ // SUBMÓDULO EVALUACIÓN DE PERMISOS
							$permitting_submenu[] = array("name" => "permittings_evaluation", "url" => "permitting_procedure_evaluation", "controller" => "permitting_procedure_evaluation");
						}
					}
					
					if(!($array_modulos[7] == 0)){
						if(!empty($permitting_submenu))
						$sidebar_menu[] = array("name" => "permittings", "url" => "#", "class" => "fa fa-file-signature", "submenu" => $permitting_submenu);
					}
					
					// MÓDULO RESIDUOS
					$waste_submenu = array();
					
					if($array_modulos[8] == 1){
						if($waste_summary_option != 3){ // SUBMÓDULO RESUMEN
							$waste_submenu[] = array("name" => "summary", "url" => "waste_summary", "controller" => "waste_summary");
						}
						if($waste_details_option != 3){ // SUBMÓDULO DETALLE
							$waste_submenu[] = array("name" => "detail", "url" => "client_waste_detail", "controller" => "client_waste_detail");
						}
						if($waste_indicators_option != 3){ // SUBMÓDULO INDICADORES
							$waste_submenu[] = array("name" => "indicators", "url" => "client_indicators", "controller" => "client_indicators");
						}
					}
					
					if(!($array_modulos[8] == 0)){
						if(!empty($waste_submenu))
						$sidebar_menu[] = array("name" => "waste", "url" => "#", "class" => "fa fa-exclamation-triangle", "submenu" => $waste_submenu);
					}
					
					// MÓDULO COMUNIDADES
					$communities_submenu = array();
	
					if($array_modulos[9] == 1){
						if($communities_summary_option != 3){ // SUBMÓDULO RESUMEN 
							$communities_submenu[] = array("name" => "summary", "url" => "communities_agreements_summary", "controller" => "communities_agreements_summary");
						}
						if($communities_stakeholders_option != 3){ // SUBMÓDULO STAKEHOLDERS 
							$communities_submenu[] = array("name" => "stakeholders", "url" => "communities_stakeholders", "controller" => "communities_stakeholders");
						}
						if($communities_agreements_option != 3){ // SUBMÓDULO ACUERDOS 
							$communities_submenu[] = array("name" => "agreements", "url" => "communities_agreements", "controller" => "communities_agreements");
						}
						if($communities_agreements_monitoring_option != 3){ // SUBMÓDULO SEGUIMIENTO ACUERDOS 
							$communities_submenu[] = array("name" => "agreements_monitoring", "url" => "agreements_monitoring", "controller" => "agreements_monitoring");
						}
						if($communities_feedback_option != 3){ // SUBMÓDULO FEEDBACK 
							$communities_submenu[] = array("name" => "feedback", "url" => "communities_feedback", "controller" => "communities_feedback");
						}
						if($communities_feedback_monitoring_option != 3){ // SUBMÓDULO SEGUIMIENTO FEEDBACK 
							$communities_submenu[] = array("name" => "feedback_monitoring", "url" => "feedback_monitoring", "controller" => "feedback_monitoring");
						}
					}
					
					if(!($array_modulos[9] == 0)){
						if(!empty($communities_submenu))
						$sidebar_menu[] = array("name" => "communities", "url" => "#", "class" => "fa fa-users", "submenu" => $communities_submenu);
					}
					
					// MÓDULO AYUDA Y SOPORTE
					$help_and_support_submenu = array();
					
					if($array_modulos[10] == 1){
						if($faq_option != 3){ // SUBMÓDULO FAQ
							$help_and_support_submenu[] = array("name" => "faq", "url" => "faq", "controller" => "faq");
						}
						if($wiki_option != 3){ // SUBMÓDULO GLOSARIO
							$help_and_support_submenu[] = array("name" => "wiki", "url" => "wiki", "controller" => "wiki");
						}
						if($what_mima_option != 3){ // SUBMÓDULO ¿QUE ES MIMASOFT?
							$help_and_support_submenu[] = array("name" => "what_is_mimasoft", "url" => "what_is_mimasoft", "controller" => "what_is_mimasoft");
						}
						if($contact_option != 3){ // SUBMÓDULO CONTACTO
							$help_and_support_submenu[] = array("name" => "contact", "url" => "contact", "controller" => "contact");
						}
					}
 					
					if(!($array_modulos[10] == 0)){
						if (!empty($help_and_support_submenu))
						$sidebar_menu[] = array("name" => "help_and_support", "url" => "help_and_support", "class" => "fa fa-question-circle","submenu" => $help_and_support_submenu);
					}
					
					//ADMINISTRACION CLIENTE
					$customer_administrator_submenu = array();
					
					if($array_modulos[11] == 1){ 
						if($config_pp_option != 3){ //CONFIGURACION PANEL PRINCIPAL
							$customer_administrator_submenu[] = array("name" => "setting_dashboard", "url" => "setting_dashboard", "controller" => "setting_dashboard");
						}
						if($cm_option != 3){ // SUBMÓDULO CARGA MASIVA
							$customer_administrator_submenu[] = array("name" => "setting_bulk_load", "url" => "setting_bulk_load", "controller" => "setting_bulk_load");
						}
					}

                    if(!($array_modulos[11] == 0)){ 
						if (!empty($customer_administrator_submenu))
						$sidebar_menu[] = array("name" => "customer_administrator", "url" => "customer_administrator", "class" => "fa fa-cogs","submenu" => $customer_administrator_submenu);	
					}
 
                }*/
                
            
            
            //$ci = & get_instance();
            //$controller_name = strtolower(get_class($ci));
            //var_dump($sidebar_menu);
            foreach ($sidebar_menu as $main_menu) {
                $submenu = get_array_value($main_menu, "submenu");
                $expend_class = $submenu ? " expand " : "";
				
				if($this->session->client_area){
					$active_class = active_menu($main_menu['controller'], $submenu);
				} else {
					if($s_menu['controller'] == 'air_forecast_sectors'){
						$active_class = active_menu($main_menu['name'], $submenu, $main_menu['param']);
					} else {
						$active_class = active_menu($main_menu['name'], $submenu, $main_menu['param']);
					}
				}
				
				if(!$this->session->project_context && !$this->session->client_area){
					$active_class = active_menu($main_menu['controller'], $submenu);
				}

                //echo $active_class;
                //echo $controller_name;
                $submenu_open_class = "";
                if ($expend_class && $active_class) {
                    $submenu_open_class = " open ";
                }

                $devider_class = get_array_value($main_menu, "devider") ? "devider" : "";
                $badge = get_array_value($main_menu, "badge");
                $badge_class = get_array_value($main_menu, "badge_class");
                ?>
                <li class="<?php echo $active_class . " " . $expend_class . " " . $submenu_open_class . " $devider_class"; ?> main">
                    <a href="<?php if(!isset($submenu)){echo_uri($main_menu['url']);}else{echo_uri($submenu["0"]["url"]);};?>">
                        <i class="fa <?php echo ($main_menu['class']); ?>"></i>
                        <span><?php echo lang($main_menu['name']); ?></span>
                        <?php
                        if ($badge) {
                            echo "<span class='badge $badge_class'>$badge</span>";
                        }
                        ?>
                    </a>
                    <?php
                    if ($submenu) {
                        echo "<ul>";
                        foreach ($submenu as $s_menu) {

							if($s_menu['controller'] == 'air_forecast_sectors'){
								$active_submenu_class = active_submenu($s_menu['controller'], false, $s_menu["param"]);
							} else {
								$active_submenu_class = active_submenu($s_menu['controller'], true);
							}

                            
                            ?>
                        <li class="<?php echo $active_submenu_class; ?>">
                            <a href="<?php echo_uri($s_menu['url']); ?>">
                                <i class="dot fa fa-circle"></i>
								<span><?php echo (lang($s_menu['name'])) ? lang($s_menu['name']) : $s_menu['name']; ?></span>
                            </a>
                        </li>
                        <?php
                    }
                    echo "</ul>";
                }
                ?>
                </li>
            <?php } ?>
        </ul>
    </div>
	<div class="p5">
		<img style="width: 100%; height: 100%" src="<?php echo get_file_uri(get_setting("system_file_path") . 'logo_particulas_left_menu.png'); ?>" />
	</div>
</div><!-- sidebar menu end -->
<?php //} ?>
<script>
	$(document).ready(function() {
		// Posicionar scroll cuando la página recarga
		$('#sidebar-scroll').mCustomScrollbar('scrollTo', $('#sidebar-scroll').find('.mCSB_container').find('.active'));
		// Posicionar scroll cuando hacen clic
		$('#sidebar-scroll li').on('click', function() {
			$('#sidebar-scroll').mCustomScrollbar('scrollTo', $('#sidebar-scroll').find('.mCSB_container').find('.active'));
		})
	})
</script>