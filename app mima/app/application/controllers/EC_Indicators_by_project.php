<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class EC_Indicators_by_project extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");	
		
		$this->id_client_context_module = 4;
		$this->id_client_context_submodule = 8;
		$id_cliente = $this->login_user->client_id;
		//$this->block_url_client_context($id_cliente, $this->id_client_context_module);
		
		// Economía Circular
		$economia_circular_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_modulo" => 4,
			"deleted" => 0
		));
		if(!$economia_circular_disponibilidad_modulo->disponible){
			$this->access_only_allowed_members();
		}
		
    }

    function index() {
		
		//$this->session->set_userdata('project_context', NULL);
		$this->session->set_userdata('menu_ec_active', TRUE);
		$this->session->set_userdata('menu_kpi_active', NULL);
		$this->session->set_userdata('menu_project_active', NULL);
		$this->session->set_userdata('client_area', NULL);
		$this->session->set_userdata('project_context', NULL);
		$this->session->set_userdata('menu_agreements_active', NULL);	
		$this->session->set_userdata('menu_help_and_support_active', NULL);
		$this->session->set_userdata('menu_recordbook_active', NULL);
		
		$id_usuario = $this->session->user_id;
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
				
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
		$paises = array("" => "-") + $this->Countries_model->get_dropdown_list(array("nombre"), "id");
		
		$array_fases = array("" => "-");
		$fases = $this->Phases_model->get_all()->result();
		foreach($fases as $fase){
			if($fase->id == 2 || $fase->id == 3){
				$array_fases[$fase->id] = lang($fase->nombre_lang);
			}
		}
		
		$tecnologias = array("" => "-") + $this->Technologies_model->get_dropdown_list(array("nombre"), "id");
		
		$view_data["paises"] = $paises;
		$view_data["fases"] = $array_fases;
		$view_data["tecnologias"] = $tecnologias;
		$view_data["proyectos"] = array("" => "-");
		
		$this->template->rander("ec_indicators_by_project/index", $view_data);
		
    }
	
	function get_project_filter(){
		
		$opciones_proyectos = array(
			"id_pais" => $this->input->post('id_pais'),
			"id_fase" => $this->input->post('id_fase'),
			"id_tech" => $this->input->post('id_tecnologia'),
			"id_cliente" => $this->login_user->client_id
		);
		
		$array_proyectos = array("" => "-");
		$proyectos = $this->Projects_model->get_project_for_circular_economy_filter($opciones_proyectos)->result();
		foreach($proyectos as $proyecto){
			$array_proyectos[$proyecto->id] = $proyecto->title;
		}
		
		$html = '<div class="form-group col-md-4">';
		$html .= '<label for="proyecto" class="col-md-3">'.lang('project').'</label>';
		$html .= 	'<div class="col-md-9">';
		$html .= 		form_dropdown("proyecto", $array_proyectos, "", "id='proyecto' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
	
	}
	
	function get_ec_report(){
		
		$id_proyecto = $this->input->post('id_proyecto');
		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		$view_data = $this->generate_report($id_proyecto, $fecha_desde, $fecha_hasta);		
		$html_ec_report = $this->load->view('ec_indicators_by_project/ec_indicators_by_project', $view_data, TRUE);

		echo $html_ec_report;
		
	}
	
	function generate_report($id_proyecto, $fecha_desde, $fecha_hasta){

		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$cliente = $this->Clients_model->get_one($proyecto->client_id);
		$id_cliente = $cliente->id;
		
		$view_data["sigla_cliente"] = $cliente->sigla;
		$view_data["sigla_proyecto"] = $proyecto->sigla;
		$view_data["fecha_actual"] = get_current_utc_time("Y-m-d");
		
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["id_cliente"] = $id_cliente;
		$view_data["fecha_desde"] = $fecha_desde;
		$view_data["fecha_hasta"] = $fecha_hasta;
		
		$masa_client_config = $this->Reports_units_settings_clients_model->get_one_where(array(
			"id_cliente" => $id_cliente,
			"id_tipo_unidad" => 1 // MASA
		));
		
		$unidad_masa_client_config = $this->Unity_model->get_one($masa_client_config->id_unidad);
		$view_data["unidad_masa_config"] = $unidad_masa_client_config->nombre;
			
		// ENTRADAS
		
		// Variables V
		$v = 0; 
		$v_consumo_virgen_masa = 0; 
		$v_consumo_virgen_volumen = 0;
		
		// Variables Ti
		$ti = 0;
		$rui = 0;
		$rci = 0;
		$res = 0;
		
		$rui_consumo_reutilizado_masa = 0;
		$rui_consumo_reutilizado_volumen = 0;
		
		$rci_consumo_reciclado_masa = 0;
		$rci_consumo_reciclado_volumen = 0;
		
		$res_consumo_energia_energia = 0;
		$res_na_transporte_transporte = 0;
		$res_na_maquinaria_unidad = 0;
		
		// SALIDAS
		
		// Variables W y To
		$w = 0;
		$to = 0;
		
		$wo = 0;
		$wo_consumo_energia_energia = 0; 
		$wo_residuo_disposicion_masa = 0;
		$wo_residuo_disposicion_volumen = 0;
		$wo_na_transporte_transporte = 0;
		$wo_na_maquinaria_unidad = 0;
		
		$wrci = 0;
		$wrci_consumo_reciclado_masa = 0;
		$wrci_consumo_reciclado_volumen = 0;
		
		$wrco = 0;
		$wrco_residuo_reciclaje_masa = 0;
		$wrco_residuo_reciclaje_volumen = 0;
		
		$rco = 0;
		$rco_residuo_reciclaje_masa = 0;
		$rco_residuo_reciclaje_volumen = 0;
		
		$ruo = 0;
		$ruo_residuo_reutilizacion_masa = 0;
		$ruo_residuo_reutilizacion_volumen = 0;
		
		$o = 0;
		$o_na_pf_energia = 0;
		$o_na_pf_unidad = 0;
		
		
		// V = CONSUMO - VIRGEN - MASA
		$elementos_v_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 1), 'Masa')->result();
		foreach($elementos_v_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$v_consumo_virgen_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			$v_consumo_virgen_masa += $v_consumo_virgen_masa_elemento;
			// FIN VALOR DE CONVERSION
		}
				
		// V = CONSUMO - VIRGEN - VOLUMEN
		$elementos_v_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 1), 'Volumen')->result();
		foreach($elementos_v_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$v_consumo_virgen_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));			
			
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$v_consumo_virgen_volumen_elemento *= $valor_factor_transformacion;
			$v_consumo_virgen_volumen += $v_consumo_virgen_volumen_elemento;
		}
		
		// RUi = CONSUMO - REUTILIZADO - MASA
		$elementos_rui_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 2), 'Masa')->result();
		foreach($elementos_rui_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$rui_consumo_reutilizado_masa += ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
		}
				
		// RUi = CONSUMO - REUTILIZADO - VOLUMEN
		$elementos_rui_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 2), 'Volumen')->result();
		foreach($elementos_rui_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$rui_consumo_reutilizado_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$rui_consumo_reutilizado_volumen_elemento *= $valor_factor_transformacion;
			$rui_consumo_reutilizado_volumen += $rui_consumo_reutilizado_volumen_elemento;
		}
		
		// RCi = CONSUMO - RECICLADO - MASA
		$elementos_rci_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 3), 'Masa')->result();
		foreach($elementos_rci_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			
			$valor_transformacion = $fila_conversion->transformacion;
			$rci_consumo_reciclado_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			$rci_consumo_reciclado_masa += $rci_consumo_reciclado_masa_elemento;
		}
				
		// RCi = CONSUMO - RECICLADO - VOLUMEN
		$elementos_rci_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 3), 'Volumen')->result();
		foreach($elementos_rci_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$rci_consumo_reciclado_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// TRANSFORMACION A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$rci_consumo_reciclado_volumen_elemento *= $valor_factor_transformacion;
			
			// PORCENTAJE DE EFICIENCIA
			/*if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 + (1 - ($tf_config->eficiencia)/100));
			}
			
			$rci_consumo_reciclado_volumen_elemento *= $valor_eff;*/
			$rci_consumo_reciclado_volumen += $rci_consumo_reciclado_volumen_elemento;
		}
				
		// RES = CONSUMO - ENERGIA - ENERGIA
		$elementos_res_energia = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(2), 'Energía')->result();
		foreach($elementos_res_energia as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 21; // MWh
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 4, // Energía
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$res_consumo_energia_energia_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// RENOVABLE
			if(is_null($tf_config->ren)){
				$valor_rem = 0;
			}elseif($tf_config->ren == 100){
				$valor_rem = 0;
			}else{
				$valor_rem = (1 - (($tf_config->ren)/100));
			}
			
			$res_consumo_energia_energia_elemento *= $valor_rem;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (($tf_config->eficiencia)/100);
			}
			
			$res_consumo_energia_energia_elemento *= $valor_eff;
			$res_consumo_energia_energia += $res_consumo_energia_energia_elemento;			
		}
		
		// RES = NO APLICA - TRANSPORTE - TRANSPORTE
		$elementos_res_transporte = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'No Aplica', array(1), 'Transporte')->result();
		foreach($elementos_res_transporte as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 5; // tkm
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 3, // Transporte
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$res_na_transporte_transporte_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$res_na_transporte_transporte_elemento *= $valor_factor_transformacion;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (($tf_config->eficiencia)/100);
			}
			
			$res_na_transporte_transporte_elemento *= $valor_eff;
			$res_na_transporte_transporte += $res_na_transporte_transporte_elemento;
		}
		
		// RES = NO APLICA - MAQUINARIA - UNIDAD
		$elementos_res_unidad = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'No Aplica', array(3), 'Unidad')->result();
		foreach($elementos_res_unidad as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// NO ES NECESARIO CONVERTIR UNIDAD A UNIDAD DE REFERENCIA
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$res_na_maquinaria_unidad_elemento = $elemento->cantidad_total;
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$res_na_maquinaria_unidad_elemento *= $valor_factor_transformacion;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (($tf_config->eficiencia)/100);
			}
			
			$res_na_maquinaria_unidad_elemento *= $valor_eff;
			$res_na_maquinaria_unidad += $res_na_maquinaria_unidad_elemento;			
		}
		
		// CALCULAR V y Ti
		$v = $v_consumo_virgen_masa + $v_consumo_virgen_volumen;
		$rui = $rui_consumo_reutilizado_masa + $rui_consumo_reutilizado_volumen;
		$rci = $rci_consumo_reciclado_masa + $rci_consumo_reciclado_volumen;
		$res = $res_consumo_energia_energia + $res_na_transporte_transporte+$res_na_maquinaria_unidad;
		
		$ti = $v + $rui + $rci + $res;
		
		
		
		// Wo = CONSUMO - ENERGIA - ENERGIA
		$elementos_wo_energia = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(2), 'Energía')->result();
		foreach($elementos_wo_energia as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 21; // MWh
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 4, // Energía
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wo_consumo_energia_energia_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// RENOVABLE
			if(is_null($tf_config->ren)){
				$valor_rem = 0;
			}elseif($tf_config->ren == 0){
				$valor_rem = 0;
			}else{
				$valor_rem = (1 - (($tf_config->ren)/100));
			}
			
			$wo_consumo_energia_energia_elemento *= $valor_rem;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wo_consumo_energia_energia_elemento *= $valor_eff;
			$wo_consumo_energia_energia += $wo_consumo_energia_energia_elemento;
		}
				
		// Wo = RESIDUO - DISPOSICION - MASA
		$elementos_wo_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(1), 'Masa')->result();
		foreach($elementos_wo_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));

			$valor_transformacion = $fila_conversion->transformacion;
			$wo_residuo_disposicion_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			$wo_residuo_disposicion_masa += $wo_residuo_disposicion_masa_elemento;
			// FIN VALOR DE CONVERSION
		}
		
		// Wo = RESIDUO - DISPOSICION - VOLUMEN
		$elementos_wo_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(1), 'Volumen')->result();
		foreach($elementos_wo_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wo_residuo_disposicion_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$wo_residuo_disposicion_volumen_elemento *= $valor_factor_transformacion;
			$wo_residuo_disposicion_volumen += $wo_residuo_disposicion_volumen_elemento;
		}
		
		// Wo = NO APLICA - TRANSPORTE - TRANSPORTE
		$elementos_wo_transporte = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'No Aplica', array(1), 'Transporte')->result();
		foreach($elementos_wo_transporte as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 5; // tkm
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 3, // Transporte
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wo_na_transporte_transporte_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$wo_na_transporte_transporte_elemento *= $valor_factor_transformacion;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wo_na_transporte_transporte_elemento *= $valor_eff;
			$wo_na_transporte_transporte += $wo_na_transporte_transporte_elemento;			
		}
		
		// Wo = NO APLICA - MAQUINARIA - UNIDAD
		$elementos_wo_unidad = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'No Aplica', array(3), 'Unidad')->result();
		foreach($elementos_wo_unidad as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// NO ES NECESARIO CONVERTIR UNIDAD A UNIDAD DE REFERENCIA
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$wo_na_maquinaria_unidad_elemento = $elemento->cantidad_total;
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$wo_na_maquinaria_unidad_elemento *= $valor_factor_transformacion;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wo_na_maquinaria_unidad_elemento *= $valor_eff;
			$wo_na_maquinaria_unidad += $wo_na_maquinaria_unidad_elemento;
		}
		
		// WRCi = CONSUMO - RECICLADO - MASA
		$elementos_wrci_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 3), 'Masa')->result();
		foreach($elementos_wrci_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wrci_consumo_reciclado_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			
			// TRAER EL VALOR DE CONVERSION A MASA Y % EFF PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wrci_consumo_reciclado_masa_elemento *= $valor_eff;
			$wrci_consumo_reciclado_masa += $wrci_consumo_reciclado_masa_elemento;
		}
		
		// WRCi = CONSUMO - RECICLADO - VOLUMEN
		$elementos_wrci_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Consumo', array(1, 3), 'Volumen')->result();
		foreach($elementos_wrci_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wrci_consumo_reciclado_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$wrci_consumo_reciclado_volumen_elemento *= $valor_factor_transformacion;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wrci_consumo_reciclado_volumen_elemento *= $valor_eff;
			$wrci_consumo_reciclado_volumen += $wrci_consumo_reciclado_volumen_elemento;
		}
		
		// WRCo = RESIDUO - RECICLAJE - MASA
		$elementos_wrco_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(3), 'Masa')->result();
		foreach($elementos_wrco_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wrco_residuo_reciclaje_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			
			// TRAER EL VALOR DE CONVERSION A MASA Y % EFF PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wrco_residuo_reciclaje_masa_elemento *= $valor_eff;
			$wrco_residuo_reciclaje_masa += $wrco_residuo_reciclaje_masa_elemento;
		}
				
		// WRCo = RESIDUO - RECICLAJE - VOLUMEN
		$elementos_wrco_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(3), 'Volumen')->result();
		foreach($elementos_wrco_volumen as $elemento){
						
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$wrco_residuo_reciclaje_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$wrco_residuo_reciclaje_volumen_elemento *= $valor_factor_transformacion;
						
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$wrco_residuo_reciclaje_volumen_elemento *= $valor_eff;
			$wrco_residuo_reciclaje_volumen += $wrco_residuo_reciclaje_volumen_elemento;
		}
						
		// RCo = RESIDUO - RECICLAJE - MASA
		$elementos_rco_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(3), 'Masa')->result();
		foreach($elementos_rco_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$rco_residuo_reciclaje_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			
			// TRAER EL VALOR DE CONVERSION A MASA Y % EFF PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (($tf_config->eficiencia)/100);
			}
			
			$rco_residuo_reciclaje_masa_elemento *= $valor_eff;
			$rco_residuo_reciclaje_masa += $rco_residuo_reciclaje_masa_elemento;
		}
		
		// RCo = RESIDUO - RECICLAJE - VOLUMEN
		$elementos_rco_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(3), 'Volumen')->result();
		foreach($elementos_rco_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$rco_residuo_reciclaje_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$rco_residuo_reciclaje_volumen_elemento *= $valor_factor_transformacion;			
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (($tf_config->eficiencia)/100);
			}

			$rco_residuo_reciclaje_volumen_elemento *= $valor_eff;
			$rco_residuo_reciclaje_volumen += $rco_residuo_reciclaje_volumen_elemento;
		}
				
		// RUo = RESIDUO - REUTILIZACION - MASA
		$elementos_ruo_masa = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(2), 'Masa')->result();
		foreach($elementos_ruo_masa as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 1; // Ton
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 1, // Masa
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$ruo_residuo_reutilizacion_masa_elemento = ($elemento->cantidad_total * $valor_transformacion);
			$ruo_residuo_reutilizacion_masa += $ruo_residuo_reutilizacion_masa_elemento;
			// FIN VALOR DE CONVERSION
						
		}
		
		// RUo = RESIDUO - REUTILIZACION - VOLUMEN
		$elementos_ruo_volumen = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'Residuo', array(2), 'Volumen')->result();
		foreach($elementos_ruo_volumen as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 3; // m3
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 2, // Volumen
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$ruo_residuo_reutilizacion_volumen_elemento = ($elemento->cantidad_total * $valor_transformacion);
			
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$ruo_residuo_reutilizacion_volumen_elemento *= $valor_factor_transformacion;
			$ruo_residuo_reutilizacion_volumen += $ruo_residuo_reutilizacion_volumen_elemento;
		}		

		// O = NO APLICA - PRODUCTO FINAL - ENERGIA
		$elementos_o_unidad = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'No Aplica', array(2), 'Energía')->result();
		foreach($elementos_o_unidad as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// VALOR DE CONVERSIÓN
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$id_unidad_origen = $id_unidad_form;
			//$id_unidad_destino = 21; // MWh
			
			$report_unit_setting_client = $this->Reports_units_settings_clients_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => 4, // Energía
				"deleted" => 0
			));
			$id_unidad_destino = $report_unit_setting_client->id_unidad;
			
			$fila_conversion = $this->Conversion_model->get_one_where(array(
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"id_unidad_origen" => $id_unidad_origen,
				"id_unidad_destino" => $id_unidad_destino
			));
			$valor_transformacion = $fila_conversion->transformacion;
			$o_na_pf_energia_elemento = ($elemento->cantidad_total * $valor_transformacion);
			// FIN VALOR DE CONVERSION
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$o_na_pf_energia_elemento *= $valor_factor_transformacion;
			
			// RENOVABLE
			/*if(is_null($tf_config->ren)){
				$valor_rem = 0;
			}elseif($tf_config->ren == 0){
				$valor_rem = 0;
			}else{
				$valor_rem = (1 - (($tf_config->ren)/100));
			}
			
			$o_na_pf_energia_elemento *= $valor_rem;
			
			// PORCENTAJE DE EFICIENCIA
			if(is_null($tf_config->eficiencia)){
				$valor_eff = 0;
			}elseif($tf_config->eficiencia == 0){
				$valor_eff = 1;
			}else{
				$valor_eff = (1 - (($tf_config->eficiencia)/100));
			}
			
			$o_na_pf_energia_elemento *= $valor_eff;*/
			$o_na_pf_energia += $o_na_pf_energia_elemento;
		}
				
		// O = NO APLICA - PRODUCTO FINAL - UNIDAD
		$elementos_o_unidad = $this->EC_Client_transformation_factors_config_model->get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, 'No Aplica', array(2), 'Unidad')->result();
		foreach($elementos_o_unidad as $elemento){
			
			$form = $this->Forms_model->get_one($elemento->id_formulario);
			$campo_unidad_form = json_decode($form->unidad, TRUE);
			$id_tipo_unidad_form = $campo_unidad_form["tipo_unidad_id"];
			$id_unidad_form = $campo_unidad_form["unidad_id"];
			
			// NO ES NECESARIO CONVERTIR UNIDAD A UNIDAD DE REFERENCIA
			$id_tipo_unidad_origen = $id_tipo_unidad_form;
			$o_na_pf_unidad_elemento = $elemento->cantidad_total;
			
			// TRAER EL VALOR DE CONVERSION A MASA PARA CATEGORIA Y TIPO DE UNIDAD DEL LOOP
			$tf_config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $elemento->id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad_origen,
				"deleted" => 0
			));
			
			// A MASA
			$valor_factor_transformacion = ($tf_config->valor_factor_transformacion) ? $tf_config->valor_factor_transformacion : 1;
			$o_na_pf_unidad_elemento *= $valor_factor_transformacion;
			$o_na_pf_unidad += $o_na_pf_unidad_elemento;
			
		}		
		
		// CALCULAR W y To
		$wo = $wo_consumo_energia_energia + $wo_residuo_disposicion_masa + $wo_residuo_disposicion_volumen + $wo_na_transporte_transporte + $wo_na_maquinaria_unidad;
		$wrci = $wrci_consumo_reciclado_masa + $wrci_consumo_reciclado_volumen;
		$wrco = $wrco_residuo_reciclaje_masa + $wrco_residuo_reciclaje_volumen;
		$rco = $rco_residuo_reciclaje_masa + $rco_residuo_reciclaje_volumen;
		$ruo = $ruo_residuo_reutilizacion_masa + $ruo_residuo_reutilizacion_volumen;
		$o = $o_na_pf_energia + $o_na_pf_unidad;
		
		$w = $wo + $wrci + $wrco;
		$to = $wo + $wrci + $wrco + $rco + $ruo + $o;
		
		
		// FORMULA
		$entrada = $v/$ti;
		$salida = $w/$to;
		
		// Si Ti es Cero, valor input debe quedar en Cero
		if($ti == 0){
			$entrada = 0;
		}
		// Si To es Cero, valor input debe quedar en Cero
		if($to == 0){
			$salida = 0;
		}
		
		$Cf = (2 - ($entrada + $salida)) / 2;
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		$view_data["id_proyecto"] = $id_proyecto;

		$view_data["cf"] = $Cf;
		$view_data["input"] = $entrada;
		$view_data["v"] = $v;
		$view_data["ti"] = $ti;
		$view_data["rui"] = $rui;
		$view_data["rci"] = $rci;
		$view_data["res"] = $res;
		$view_data["output"] = $salida;
		$view_data["w"] = $w;
		$view_data["to"] = $to;
		$view_data["wo"] = $wo;
		$view_data["wrci"] = $wrci;
		$view_data["wrco"] = $wrco;
		$view_data["rco"] = $rco;
		$view_data["ruo"] = $ruo;
		$view_data["o"] = $o;
			
		return $view_data;
		
	}
		
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->input->post('id_proyecto');
		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		
		$masa_client_config = $this->Reports_units_settings_clients_model->get_one_where(array(
			"id_cliente" => $id_cliente,
			"id_tipo_unidad" => 1 // MASA
		));
		
		$unidad_masa_client_config = $this->Unity_model->get_one($masa_client_config->id_unidad);
		$view_data["unidad_masa_config"] = $unidad_masa_client_config->nombre;
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		
		// Imágenes de gráficos
		$graficos["image_grafico_circularity_index"] = $this->input->post("image_grafico_circularity_index");
		$graficos["image_grafico_indicadores_input"] = $this->input->post("image_grafico_indicadores_input");
		$graficos["image_grafico_indicadores_output"] = $this->input->post("image_grafico_indicadores_output");
		$graficos["image_grafico_variables_input"] = $this->input->post("image_grafico_variables_input");
		$graficos["image_grafico_variables_output"] = $this->input->post("image_grafico_variables_output");

		$view_data = $this->generate_report($id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["graficos"] = $graficos;
		$view_data["info_cliente"] = $info_cliente;
		$view_data["id_proyecto"] = $id_proyecto;
		
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["info_proyecto"] = $info_proyecto;

		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".lang("circularity_index")."_".lang("indicators_by_project")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".lang("circularity_index")."_".lang("indicators_by_project")."_".date('Y-m-d'));
        $this->pdf->SetKeywords('TCPDF, PDF');
		
		//$this->pdf->SetPrintHeader(false);
		//$this->pdf->SetPrintFooter(false);
		// set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
        $this->pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		// set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE,PDF_MARGIN_BOTTOM);	
		//relación utilizada para ajustar la conversión de los píxeles
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// ---------------------------------------------------------
		// set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$this->SetFont('freemono', '', 14, '', true);        
		$fontawesome = TCPDF_FONTS::addTTFfont('assets/js/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96); 
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$view_data["fontawesome"] = $fontawesome;
		$view_data["pdf"] = $this->pdf;
		$html = $this->load->view('ec_indicators_by_project/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".lang("circularity_index")."_".lang("indicators_by_project")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;

		
	}
	
}