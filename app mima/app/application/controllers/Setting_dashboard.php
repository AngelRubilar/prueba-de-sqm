<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting_dashboard extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	//private $id_modulo_administrador;
	
    function __construct() {
        parent::__construct();
        //$this->access_only_admin();
		
		$this->id_modulo_cliente = 11;
		$this->id_submodulo_cliente = 20;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    function index() {
		
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$waste_settings = $this->Client_waste_settings_model->get_project_waste_settings($id_cliente, $id_proyecto)->result();
		$consumptions_settings = $this->Client_consumptions_settings_model->get_project_consumptions_settings($id_cliente, $id_proyecto)->result();
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_editar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		
		/* Datos configuración Huellas Ambientales */
		$view_data["client_environmental_footprints_settings"] = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
        foreach($view_data["client_environmental_footprints_settings"] as $setting){
			/*
			if($setting->informacion == "Impactos totales" ){
				$view_data["id_impactos_totales"] = $setting->id;
			}
			if($setting->informacion == "Impactos por unidad(es) funcional(es)" ){
				$view_data["id_impactos_por_uf"] = $setting->id;
			}
			if($setting->informacion == "Impactos por categoría" ){
				$view_data["id_impactos_por_categoria"] = $setting->id;
			}*/
			if($setting->informacion == "total_impacts" ){
				$view_data["id_impactos_totales"] = $setting->id;
			}
			if($setting->informacion == "impacts_by_functional_units" ){
				$view_data["id_impactos_por_uf"] = $setting->id;
			}
			
		}
	
		if($waste_settings){
			/* Datos configuración Residuos */
			$view_data["categorias_proyecto_form_residuo"] = $waste_settings;		
		} else {	
			/* Datos configuración Residuos */
			//$view_data["categorias_proyecto_form_residuo"] = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form($id_proyecto, "Residuo")->result();
			
		}
		
		if($consumptions_settings){
			/* Datos configuración Consumos */
			$view_data["categorias_proyecto_form_consumo"] = $consumptions_settings;
		} else {
			/* Datos configuración Consumos */
			//$view_data["categorias_proyecto_form_consumo"] = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form($id_proyecto, "Consumo")->result();
		}
		
		/*Compromisos settings*/
		$view_data["client_compromises_settings"] = $this->Client_compromises_settings_model->get_one_where(array("id_cliente" => $id_cliente,"id_proyecto" => $id_proyecto ,"deleted" => 0));
		$view_data["puede_ver_compromisos"] = $this->profile_access($this->session->user_id, 6, 3, "ver");
		$view_data["disponibilidad_modulo_compromisos"] = $this->Module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 6))->available;
		/*Fin compromisos settings*/
		
		/*Permisos settings*/
		$view_data["client_permitting_settings"] = $this->Client_permitting_settings_model->get_one_where(array("id_cliente" => $id_cliente,"id_proyecto" => $id_proyecto ,"deleted" => 0));
		$view_data["puede_ver_permisos"] = $this->profile_access($this->session->user_id, 7, 5, "ver");
		$view_data["disponibilidad_modulo_permisos"] = $this->Module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 7))->available;
		/*Fin permisos settings*/
		
		$view_data["Categories_alias_model"] = $this->Categories_alias_model;
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		
		$this->template->rander("setting_dashboard/index", $view_data);
    }
	
	
	/* Guardar configuración de huellas ambientales de proyecto de cliente */
	function save_environmental_footprints(){
		
		$id_impactos_totales = $this->input->post("id_impactos_totales");
		$id_impactos_por_uf = $this->input->post("id_impactos_por_uf");
		//$id_impactos_por_categoria = $this->input->post("id_impactos_por_categoria");
		
		$data_impactos_totales = array(
			"id_cliente" => $this->login_user->client_id,
			"id_proyecto" => $this->session->project_context,
			//"informacion" => "Impactos totales",
			"informacion" => "total_impacts",
			"habilitado" => ($this->input->post('total_impacts_enabled')) ? 1 : 0,
			//"tabla" => ($this->input->post('total_impacts_table')) ? 1 : 0,
			//"grafico" => ($this->input->post('total_impacts_graphic')) ? 1 : 0,
		);

		$data_impactos_por_uf = array(
			"id_cliente" => $this->login_user->client_id,
			"id_proyecto" => $this->session->project_context,
			//"informacion" => "Impactos por unidad(es) funcional(es)",
			"informacion" => "impacts_by_functional_units",
			"habilitado" => ($this->input->post('impacts_by_functional_units_enabled')) ? 1 : 0,
			//"tabla" => ($this->input->post('impacts_by_functional_units_table')) ? 1 : 0,
			//"grafico" => ($this->input->post('impacts_by_functional_units_graphic')) ? 1 : 0,
		);
		
		/* $data_impactos_por_categoria = array(
			"id_cliente" => $this->login_user->client_id,
			"id_proyecto" => $this->session->project_context,
			"informacion" => "Impactos por categoría",
			"habilitado" => ($this->input->post('impacts_by_category_enabled')) ? 1 : 0,
			//"tabla" => ($this->input->post('impacts_by_category_table')) ? 1 : 0,
			//"grafico" => ($this->input->post('impacts_by_category_graphic')) ? 1 : 0,
		); */
		
		if($id_impactos_totales){
			$save_id_impactos_totales = $this->Client_environmental_footprints_settings_model->save($data_impactos_totales, $id_impactos_totales);
		} else {
			$save_id_impactos_totales = $this->Client_environmental_footprints_settings_model->save($data_impactos_totales);
		}
		
		if($id_impactos_por_uf){
			$save_id_impactos_por_uf = $this->Client_environmental_footprints_settings_model->save($data_impactos_por_uf, $id_impactos_por_uf);
		} else {
			$save_id_impactos_por_uf = $this->Client_environmental_footprints_settings_model->save($data_impactos_por_uf);
		}
		
		/* if($id_impactos_por_categoria){
			$save_id_impactos_por_categoria = $this->Client_environmental_footprints_settings_model->save($data_impactos_por_categoria, $id_impactos_por_categoria);
		} else {
			$save_id_impactos_por_categoria = $this->Client_environmental_footprints_settings_model->save($data_impactos_por_categoria);
		} */
		
		if($save_id_impactos_totales && $save_id_impactos_por_uf){
			
			// Guardar histórico notificaciones
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;	
			$id_user = $this->session->user_id;
		
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => "edit",
			);
			ayn_save_historical_notification($options);
			
			echo json_encode(array("success" => true, "id_impactos_totales" => $save_id_impactos_totales, "id_impactos_por_uf" => $save_id_impactos_por_uf, "id_impactos_por_categoria" => $save_id_impactos_por_categoria, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}

	
	}
	
	function save_waste(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		//$waste_enabled = $this->input->post("waste_enabled");
		$waste_table = $this->input->post("waste_table");
		$waste_graphic = $this->input->post("waste_graphic");

		$categorias_proyecto_from_residuo = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form($id_proyecto, "Residuo")->result();
		$waste_settings = $this->Client_waste_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
		
		$data_waste_enabled = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto
		);
		
		$data_waste_table = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto
		);
		
		if($waste_settings){ //edit			
			
			$data_waste_enabled = array();
			
			if($waste_enabled){	
				foreach($waste_settings as $ws){
					foreach($waste_enabled as $index => $we){
						if($ws->id_categoria == $index){
							$data_waste_enabled["habilitado"] = $we;
							$save_id = $this->Client_waste_settings_model->save($data_waste_enabled, $ws->id);
						}
					}				
				}
			}
			
			$data_waste_table = array();
			
			if($waste_table){	
				foreach($waste_settings as $ws){
					foreach($waste_table as $index => $wt){
						if($ws->id_categoria == $index){
							$data_waste_table["tabla"] = $wt;
							
							$save_id = $this->Client_waste_settings_model->save($data_waste_table, $ws->id);
						}
					}				
				}
			}
			
			$data_waste_graphic = array();
			
			if($waste_graphic){	
				foreach($waste_settings as $ws){
					foreach($waste_graphic as $index => $wg){
						if($ws->id_categoria == $index){
							$data_waste_graphic["grafico"] = $wg;
							$save_id = $this->Client_waste_settings_model->save($data_waste_graphic, $ws->id);
						}
					}				
				}
			}
			
		} else { //insert
			
			
			if($waste_table){
			
				foreach($waste_table as $index => $we){
					
					$array_we = explode("-", $we);
					$data_waste_table["id_categoria"] = $index;
					$data_waste_table["id_formulario"] = $array_we[1];
					$data_waste_table["tabla"] = $array_we[0];
					$save_id = $this->Client_waste_settings_model->save($data_waste_table);
				}
			
			} else {
				
				foreach($categorias_proyecto_from_consumo as $cat){			
					$data_waste_table["id_categoria"] = $cat->id_categoria;
					$data_waste_table["id_formulario"] = $cat->id_form;
					$data_waste_table["tabla"] = 0;
					$save_id = $this->Client_waste_settings_model->save($data_consumptions_table);
				}
						
			}
			
			
			/* if($waste_enabled){
			
				foreach($waste_enabled as $index => $we){
					
					$array_we = explode("-", $we);
					$data_waste_enabled["id_categoria"] = $index;
					$data_waste_enabled["id_formulario"] = $array_we[1];
					$data_waste_enabled["habilitado"] = $array_we[0];
					$save_id = $this->Client_waste_settings_model->save($data_waste_enabled);
				}
				
			} else {
				
				foreach($categorias_proyecto_from_residuo as $cat){			
					$data_waste_enabled["id_categoria"] = $cat->id_categoria;
					$data_waste_enabled["id_formulario"] = $cat->id_form;
					$data_waste_enabled["habilitado"] = 0;
					$save_id = $this->Client_waste_settings_model->save($data_waste_enabled);
				}
						
			} */
			
			$waste_settings = $this->Client_waste_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
			
			/* $data_waste_table = array();
			
			if($waste_table){
				foreach($waste_settings as $ws){
					foreach($waste_table as $index => $wt){
						$array_wt = explode("-", $wt);
						if($ws->id_categoria == $index){
							$data_waste_table["tabla"] = $array_wt[0];
							$save_id = $this->Client_waste_settings_model->save($data_waste_table, $ws->id);
						}
					}					
				}				
			} */
			
			$data_waste_graphic = array();
			
			if($waste_graphic){
				foreach($waste_settings as $ws){
					foreach($waste_graphic as $index => $wg){
						$array_wg = explode("-", $wg);	
						if($ws->id_categoria == $index){
							$data_waste_graphic["grafico"] = $array_wg[0];
							$save_id = $this->Client_waste_settings_model->save($data_waste_graphic, $ws->id);
						}					
					}				
				}			
			}
			
		} //fin insert
		
		if($save_id){
			
			// Guardar histórico notificaciones
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;	
			$id_user = $this->session->user_id;
		
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => "edit",
			);
			ayn_save_historical_notification($options);
			
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
	
	}
	
	function save_consumptions(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		//$consumptions_enabled = $this->input->post("consumptions_enabled");
		$consumptions_table = $this->input->post("consumptions_table");
		$consumptions_graphic = $this->input->post("consumptions_graphic");

		$categorias_proyecto_from_consumo = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form($id_proyecto, "Consumo")->result();
		$consumptions_settings = $this->Client_consumptions_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
		
		$data_consumptions_enabled = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto
		);
		
		$data_consumptions_table = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto
		);

		if($consumptions_settings){ //edit			
			
			$data_consumptions_enabled = array();
			
			if($consumptions_enabled){	
				foreach($consumptions_settings as $ws){
					foreach($consumptions_enabled as $index => $we){
						if($ws->id_categoria == $index){
							$data_consumptions_enabled["habilitado"] = $we;
							$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_enabled, $ws->id);
						}
					}				
				}
			}
			
			$data_consumptions_table = array();
			
			if($consumptions_table){	
				foreach($consumptions_settings as $ws){
					foreach($consumptions_table as $index => $wt){
						if($ws->id_categoria == $index){
							$data_consumptions_table["tabla"] = $wt;
							$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_table, $ws->id);
						}
					}				
				}
			}
			
			$data_consumptions_graphic = array();
			
			if($consumptions_graphic){
				foreach($consumptions_settings as $ws){
					foreach($consumptions_graphic as $index => $wg){
						if($ws->id_categoria == $index){
							$data_consumptions_graphic["grafico"] = $wg;

							$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_graphic, $ws->id);
						}
					}				
				}
			}
			
		} else { //insert
			
			//$data_consumptions_table = array();
			
			if($consumptions_table){
			
				foreach($consumptions_table as $index => $we){
					
					$array_we = explode("-", $we);
					$data_consumptions_table["id_categoria"] = $index;
					$data_consumptions_table["id_formulario"] = $array_we[1];
					$data_consumptions_table["tabla"] = $array_we[0];
					$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_table);
				}
			
			} else {
				
				foreach($categorias_proyecto_from_consumo as $cat){			
					$data_consumptions_table["id_categoria"] = $cat->id_categoria;
					$data_consumptions_table["id_formulario"] = $cat->id_form;
					$data_consumptions_table["tabla"] = 0;
					$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_table);
				}
						
			}
			
			/* if($consumptions_enabled){
			
				foreach($consumptions_enabled as $index => $we){
					
					$array_we = explode("-", $we);
					$data_consumptions_enabled["id_categoria"] = $index;
					$data_consumptions_enabled["id_formulario"] = $array_we[1];
					$data_consumptions_enabled["habilitado"] = $array_we[0];
					$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_enabled);
				}
				
			} else {
				
				foreach($categorias_proyecto_from_consumo as $cat){			
					$data_consumptions_enabled["id_categoria"] = $cat->id_categoria;
					$data_consumptions_enabled["id_formulario"] = $cat->id_form;
					$data_consumptions_enabled["habilitado"] = 0;
					$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_enabled);
				}
						
			} */
			
			$consumptions_settings = $this->Client_consumptions_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
			
			
			
			/* if($consumptions_table){
				foreach($consumptions_settings as $ws){
					foreach($consumptions_table as $index => $wt){
						$array_wt = explode("-", $wt);
						if($ws->id_categoria == $index){
							$data_consumptions_table["tabla"] = $array_wt[0];
							$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_table, $ws->id);
						}
					}					
				}				
			} */
			
			$data_consumptions_graphic = array();
			
			if($consumptions_graphic){
				foreach($consumptions_settings as $ws){
					foreach($consumptions_graphic as $index => $wg){
						$array_wg = explode("-", $wg);
						if($ws->id_categoria == $index){
							$data_consumptions_graphic["grafico"] = $array_wg[0];
							$save_id = $this->Client_consumptions_settings_model->save($data_consumptions_graphic, $ws->id);
						}					
					}				
				}			
			}
			
		} //fin insert
		
		if($save_id){
			
			// Guardar histórico notificaciones
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;	
			$id_user = $this->session->user_id;
		
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => "edit",
			);
			ayn_save_historical_notification($options);
			
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	
	function save_compromises(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$compromises_table = $this->input->post("table_enabled");
		$compromises_graphic = $this->input->post("graphs_enabled");
		$compromises_setting_id = $this->input->post("compromises_setting_id");
		
		
		if($compromises_setting_id){
			$data = array(
				"tabla" =>$compromises_table,
				"grafico" =>$compromises_graphic
			);
			$save_id = $this->Client_compromises_settings_model->save($data,$compromises_setting_id);			
		}else{
			$data = array(
				"id_cliente" =>$id_cliente,
				"id_proyecto" =>$id_proyecto,
				"tabla" =>$compromises_table,
				"grafico" =>$compromises_graphic,
			);
			$save_id = $this->Client_compromises_settings_model->save($data);
		}

		if($save_id){
			
			// Guardar histórico notificaciones
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;	
			$id_user = $this->session->user_id;
		
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => "edit",
			);
			ayn_save_historical_notification($options);
			
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
	}
	
	function save_permittings(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$permitting_table = $this->input->post("table_enabled");
		$permitting_graphic = $this->input->post("graphs_enabled");
		$permitting_setting_id = $this->input->post("permitting_setting_id");
		
		
		if($permitting_setting_id){
			$data = array(
				"tabla" =>$permitting_table,
				"grafico" =>$permitting_graphic
			);
			$save_id = $this->Client_permitting_settings_model->save($data,$permitting_setting_id);			
		}else{
			$data = array(
				"id_cliente" =>$id_cliente,
				"id_proyecto" =>$id_proyecto,
				"tabla" =>$permitting_table,
				"grafico" =>$permitting_graphic,
			);
			$save_id = $this->Client_permitting_settings_model->save($data);
		}

		if($save_id){
			
			// Guardar histórico notificaciones
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;	
			$id_user = $this->session->user_id;
		
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => "edit",
			);
			ayn_save_historical_notification($options);
			
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
	}	
	
}

