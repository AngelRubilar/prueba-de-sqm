<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Relationship extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();
        $this->template->rander("relationship/index");
    }
    
    /* load views of tabs*/
    
    function criterio() {
        //$this->access_only_team_members();
        //$this->init_project_permission_checker($project_id);
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO MATERIALES
		$array_materiales[] = array("id" => "", "text" => "- ".lang("material")." -");
		$materiales = $this->Materials_model->get_dropdown_list(array("nombre"), 'id');
		foreach($materiales as $id => $nombre){
			$array_materiales[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['materiales_dropdown'] = json_encode($array_materiales);
		
        $this->load->view('relationship/criterio/index', $view_data);
        //$this->template->rander("relationship/criterio");
    }
    
    function asignacion() {
        //$this->access_only_team_members();
        //$this->init_project_permission_checker($project_id);
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO CRITERIO
		$array_criterios[] = array("id" => "", "text" => "- ".lang("rule")." -");
		$criterios = $this->Rule_model->get_dropdown_list(array("etiqueta"), 'id');
		foreach($criterios as $id => $etiqueta){
			$array_criterios[] = array("id" => $id, "text" => $etiqueta);
		}
		$view_data['criterios_dropdown'] = json_encode($array_criterios);
		
        $this->load->view('relationship/asignacion/index', $view_data);
    }
    
    function calculo() {
        //$this->access_only_team_members();
        //$this->init_project_permission_checker($project_id);
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO CRITERIO
		$array_criterios[] = array("id" => "", "text" => "- ".lang("rule")." -");
		$criterios = $this->Rule_model->get_dropdown_list(array("etiqueta"), 'id');
		foreach($criterios as $id => $etiqueta){
			$array_criterios[] = array("id" => $id, "text" => $etiqueta);
		}
		$view_data['criterios_dropdown'] = json_encode($array_criterios);
		
		// FILTRO BASE DE DATOS
		$array_bases_de_datos[] = array("id" => "", "text" => "- ".lang("database")." -");
		$bases_de_datos = $this->Databases_model->get_dropdown_list(array("nombre"), 'id');
		foreach($bases_de_datos as $id => $nombre){
			$array_bases_de_datos[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['bases_de_datos_dropdown'] = json_encode($array_bases_de_datos);
		
        $this->load->view('relationship/calculo/index', $view_data);
    }
	
	// CRITERIOS
    
    /* list of criterios */

    function criterio_list_data() {

        $this->access_only_allowed_members();
        
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"id_material" => $this->input->post("id_material")
		);
		
        $list_data = $this->Rule_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_criterio_row($data);
        }
        echo json_encode(array("data" => $result));
    }
    
    /* prepare a row of rule list table */

    private function _make_criterio_row($data) {
		
		$formulario_criterio = $this->Forms_model->get_one($data->id_formulario);
		$flujo_formulario_criterio = $formulario_criterio->flujo;
        
		if(isset($data->tipo_by_criterio)){
			$jsno_data = json_decode($data->tipo_by_criterio,true);

			if($jsno_data["id_campo_sp"] == "1" ){
				$campo_sp = $flujo_formulario_criterio == "Residuo" ? lang('type_of_treatment') : lang('type');
			}else{
				$campo_sp = ($data->campo_sp)?$data->campo_sp:"-";
			}
			
			if($jsno_data["id_campo_pu"] == "1"){
				$campo_pu = $flujo_formulario_criterio == "Residuo" ? lang('type_of_treatment') : lang('type');
			}else{
				$campo_pu = ($data->campo_pu)?$data->campo_pu:"-";
			}
			
			if($jsno_data["id_campo_fc"] == "1"){
				$campo_fc = $flujo_formulario_criterio == "Residuo" ? lang('type_of_treatment') : lang('type');
			}else{
				$campo_fc = ($data->campo_fc)?$data->campo_fc:"-";
			}
		}else{
			$campo_sp = ($data->campo_sp)?$data->campo_sp:"-";
			$campo_pu = ($data->campo_pu)?$data->campo_pu:"-";
			$campo_fc = ($data->campo_fc)?$data->campo_fc:"-";
		}
		/*
        $row_data = array($data->id,
            modal_anchor(get_uri("relationship/view_criterio/" . $data->id), $data->company_name, array("title" => lang('view_rule'), "data-post-id" => $data->id)),
            $data->title,
            $data->formulario,
            $data->material,
            ($data->campo_sp)?$data->campo_sp:"-",
            ($data->campo_pu)?$data->campo_pu:"-",
            ($data->campo_fc)?$data->campo_fc:"-",
            $data->etiqueta,
        );
		*/
        $row_data = array($data->id,
            modal_anchor(get_uri("relationship/view_criterio/" . $data->id), $data->company_name, array("title" => lang('view_rule'), "data-post-id" => $data->id)),
            $data->title,
            $data->formulario,
            $data->material,
            $campo_sp,
            $campo_pu,
            $campo_fc,
            $data->etiqueta,
        );
				  
        $row_data[] = modal_anchor(get_uri("relationship/view_criterio/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_rule'), "data-post-id" => $data->id)) 
				. modal_anchor(get_uri("relationship/add_rule"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_rule'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_rule'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("relationship/delete_rule"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
    
    /* return a row of rule list table */

    private function _row_criterio_data($id) {
        $options = array(
            "id" => $id,
        );
        $data = $this->Rule_model->get_details($options)->row();
        return $this->_make_criterio_row($data);
    }

    function add_rule() {
        $this->access_only_allowed_members();
        $rule_id = $this->input->post('id');

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
        
        $view_data['model_info'] = $this->Rule_model->get_one($rule_id);
        $view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");   
        $view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_cliente));
		
		$formulario_criterio = $this->Forms_model->get_one($view_data['model_info']->id_formulario);
		$flujo_formulario_criterio = $formulario_criterio->flujo;
		
		if($rule_id){
			
			if(isset($view_data['model_info']->tipo_by_criterio)){
				$j_datos = json_decode($view_data['model_info']->tipo_by_criterio,true);
				
				if($j_datos["id_campo_sp"] == "1"){
					$view_data["id_campo_sp"] = $flujo_formulario_criterio == "Residuo" ? "tipo_tratamiento" : "tipo";
				}else{
					$view_data["id_campo_sp"] = $view_data['model_info']->id_campo_sp;
				}
				
				if($j_datos["id_campo_pu"] == "1"){
					$view_data["id_campo_pu"] = $flujo_formulario_criterio == "Residuo" ? "tipo_tratamiento" : "tipo";
				}else{
					$view_data["id_campo_pu"] = $view_data['model_info']->id_campo_pu;
				}
				
				if($j_datos["id_campo_fc"] == "1"){
					$view_data["id_campo_fc"] = $flujo_formulario_criterio == "Residuo" ? "tipo_tratamiento" : "tipo";
				}else{
					$view_data["id_campo_fc"] = $view_data['model_info']->id_campo_fc;
				}
				
			}else{
				$view_data["id_campo_sp"] = $view_data['model_info']->id_campo_sp;
				$view_data["id_campo_pu"] = $view_data['model_info']->id_campo_pu;
				$view_data["id_campo_fc"] = $view_data['model_info']->id_campo_fc;
			}

			// formularios
			$formularios = $this->Forms_model->get_forms_of_project(array("id_proyecto" => $view_data['model_info']->id_proyecto, "id_tipo_formulario" => 1))->result();
			$forms = array();
			$forms[""] = "-";
			foreach($formularios as $form){
				$forms[$form->id] = $form->nombre;
			}
			$view_data["formularios"] = $forms;

			// materiales
			$materiales = $this->Materials_model->get_materials_of_form($view_data['model_info']->id_formulario);
			$mats = array();
			$mats[""] = "-";
			foreach($materiales as $mat){
				$mats[$mat['id']] = $mat['nombre'];
			}
			$view_data["materiales"] = $mats;

			// campos
			/* $options = array("nombre" => "Selección", "id_formulario" => $view_data['model_info']->id_formulario);
			$campos_select = $this->Field_rel_form_model->get_fields_related_to_form_with_options($options)->result(); */
			$form = $this->Forms_model->get_one($view_data['model_info']->id_formulario);
			$campos_select = $this->Field_rel_form_model->get_fields_for_environmental_record_rule($view_data['model_info']->id_formulario)->result();
			$campos = array();
			//$campos[""] = "-";
			
			if($form->flujo == "Residuo"){

				$campos[""] = "-";
				$campos["tipo_tratamiento"] = lang('type_of_treatment');

			}else{
				$campos[""] = "-";
				$campos["tipo"] = lang('type');
			}

			foreach($campos_select as $cs){
				$campos[$cs->id] = $cs->nombre;
			}
			$view_data["campos"] = $campos;

		} else {
			
			$view_data["formularios"] = array("" => "-");
			$view_data["materiales"] = array("" => "-");
			$view_data["campos"] = array("" => "-");
			
		}

        $this->load->view('relationship/criterio/modal_form', $view_data);
    }

    //on change function to rule
    function get_er_of_project_json(){
    
        $id_proyecto = $this->input->post('id_project');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_registros_ambientales = array();
        $array_registros_ambientales[] = array("id" => "", "text" => "-");
		
        if($id_proyecto){
			$er_de_proyecto = $this->Forms_model->get_forms_of_project(array("id_proyecto" => $id_proyecto, "id_tipo_formulario" => 1))->result();
            foreach($er_de_proyecto as $registro_ambiental){
                $array_registros_ambientales[] = array("id" => $registro_ambiental->id, "text" => $registro_ambiental->nombre);
            }
        }
        
        echo json_encode($array_registros_ambientales);
        
    }
    
    //on change function to rule
    function get_materials_of_form(){
    
        $id_formulario = $this->input->post('id_form');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_materiales = array();
        $array_materiales[] = array("id" => "", "text" => "-");
		if($id_formulario){
			 $materiales_de_proyecto = $this->Materials_model->get_materials_of_form($id_formulario);
			foreach($materiales_de_proyecto as $material){
				$array_materiales[] = array("id" => $material['id'], "text" => $material['nombre']);
			}
		}
		
        echo json_encode($array_materiales);
    }
	
    //on change function to rule
    function get_fields_of_form(){
    
        $id_formulario = $this->input->post('id_form');
		
		if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_campos_select = array();
		$array_campos_select[] = array("id" => "", "text" => "-");
		
		if($id_formulario){
			$form = $this->Forms_model->get_one($id_formulario);
			$campos_select = $this->Field_rel_form_model->get_fields_for_environmental_record_rule($id_formulario)->result();
			
			if($form->flujo == "Residuo"){
				$array_campos_select[] = array("id" => "tipo_tratamiento", "text" => ''.lang('type_of_treatment').'');
				
			}
			if($form->flujo == "Consumo" || $form->flujo == "No Aplica"){
				$array_campos_select[] = array("id" => "tipo", "text" => ''.lang('type').'');
	
			}
			foreach($campos_select as $campo_select){
				$array_campos_select[] = array("id" => $campo_select->id, "text" => $campo_select->nombre);
			}
		}
		
        echo json_encode($array_campos_select);
    }
    
    //* insert or update a rule 

    function save_criterio() {
        $rule_id = $this->input->post('id');

        //$this->access_only_allowed_members_or_client_contact($client_id);

        validate_submitted_data(array(
            "id" => "numeric",
            "client" => "required",
            "project" => "required",
            "form" => "required",
            "material" => "required",
            //"fc_rule" => "required",
            "label" => "required",
        ));

		$id_campo_sp_js = ($this->input->post('subproject_rule'))=="tipo_tratamiento" || ($this->input->post('subproject_rule'))=="tipo" ? '1' : NULL;
		$id_campo_pu_js = ($this->input->post('unit_processes_rule'))=="tipo_tratamiento" || ($this->input->post('unit_processes_rule'))=="tipo" ? '1' : NULL;
		$id_campo_fc_js = ($this->input->post('fc_rule'))=="tipo_tratamiento" || ($this->input->post('fc_rule'))=="tipo" ? '1' : NULL;

		
		if(($id_campo_fc_js == NULL)&&($id_campo_pu_js == NULL)&&($id_campo_sp_js == NULL)){
			$j_data = NULL;
		}else{
			
			$json_data = array(
				"id_campo_sp" => $id_campo_sp_js,
				"id_campo_pu" => $id_campo_pu_js,
				"id_campo_fc" => $id_campo_fc_js
			);
			$j_data = json_encode($json_data);
			
		}
		/*
		$json_data = array(
			"id_campo_sp" => ($this->input->post('subproject_rule'))=="tipo_tratamiento" ? '1' : 'NULL',
			"id_campo_pu" => ($this->input->post('unit_processes_rule'))=="tipo_tratamiento" ? '1' : 'NULL',
			"id_campo_fc" => ($this->input->post('fc_rule'))=="tipo_tratamiento" ? '1' : 'NULL',
		);
		*/
		
		//$j_data = json_encode($json_data);
		
		if($this->input->post('subproject_rule') == "tipo_tratamiento" || $this->input->post('subproject_rule') == "tipo"){
			$id_campo_sp = NULL; 
		}else if($this->input->post('subproject_rule') !== "tipo_tratamiento" && $this->input->post('subproject_rule') !== "tipo"){ 
			//$id_campo_sp = $this->input->post('subproject_rule');
			$id_campo_sp = ($this->input->post('subproject_rule'))?$this->input->post('subproject_rule') : NULL;
		}
		
		if($this->input->post('unit_processes_rule') == "tipo_tratamiento" || $this->input->post('unit_processes_rule') == "tipo"){ 
			$id_campo_pu = NULL; 
		}else if($this->input->post('unit_processes_rule') !== "tipo_tratamiento" && $this->input->post('unit_processes_rule') !== "tipo"){ 
			//$id_campo_pu = $this->input->post('unit_processes_rule'); 
			$id_campo_pu = ($this->input->post('unit_processes_rule'))?$this->input->post('unit_processes_rule') : NULL; 
		}
		
		if($this->input->post('fc_rule') == "tipo_tratamiento" ||$this->input->post('fc_rule') == "tipo"){ 
			$id_campo_fc = NULL; 
		}else if($this->input->post('fc_rule') !== "tipo_tratamiento" && $this->input->post('fc_rule') !== "tipo"){ 
			//$id_campo_fc = $this->input->post('fc_rule'); 
			$id_campo_fc = ($this->input->post('fc_rule'))?$this->input->post('fc_rule') : NULL; 
		}
		
		/*
        $data = array(
            "id_cliente" => $this->input->post('client'),
            "id_proyecto" => $this->input->post('project'),
            "id_formulario" => $this->input->post('form'),
            "id_material" => $this->input->post('material'),
            "id_campo_sp" => ($this->input->post('subproject_rule'))?$this->input->post('subproject_rule') : NULL,
            "id_campo_pu" => ($this->input->post('unit_processes_rule'))?$this->input->post('unit_processes_rule') : NULL,
            "id_campo_fc" => ($this->input->post('fc_rule'))?$this->input->post('fc_rule') : NULL,
            "etiqueta" => $this->input->post('label'),
        );
		*/
		
		$data = array(
			"id_cliente" => $this->input->post('client'),
			"id_proyecto" => $this->input->post('project'),
			"id_formulario" => $this->input->post('form'),
			"id_material" => $this->input->post('material'),
			"id_campo_sp" => $id_campo_sp,
			"id_campo_pu" => $id_campo_pu,
			"id_campo_fc" => $id_campo_fc,
			"tipo_by_criterio" => $j_data,
			"etiqueta" => $this->input->post('label'),
		);
		
		$criterio = $this->Rule_model->get_one_where(array(
			"id_cliente" => $this->input->post('client'),
			"id_proyecto" => $this->input->post('project'),
			"id_formulario" => $this->input->post('form'),
			"id_material" => $this->input->post('material'),
			"deleted" => 0
		));
		
		//EDITAR
		if($rule_id){
			
			//Validación de una única etiqueta para cliente/proyecto
			$etiqueta = $data["etiqueta"];
			$client = $data["id_cliente"];
			$project = $data["id_proyecto"];
			$rule = $this->Rule_model->get_one($rule_id);
			if($data["etiqueta"] !== $rule->etiqueta){
				$rules = $this->Rule_model->get_all_where(array("id_cliente" => $client, "id_proyecto" => $project, "deleted" => 0))->result();
				foreach($rules as $dato){
					if($client == $dato->id_cliente && $project == $dato->id_proyecto){
						if($etiqueta == $dato->etiqueta){
							echo json_encode(array("success" => false, 'message' => lang('labels_warning')));
							exit();
						}
					}
				}
			} 	
			
			if($rule_id != $criterio->id){
				if($criterio->id_cliente == $this->input->post('client')
					&& $criterio->id_proyecto == $this->input->post('project')
					&& $criterio->id_formulario == $this->input->post('form')){
					echo json_encode(array("success" => false, 'message' => lang('duplicate_rule')));
					exit(); 
				} 
			}
			
			// VALIDACIÓN: NO SE PUEDEN EDITAR CAMPOS CRITERIO SP O CRITERIO PU, SI HAY UNA ASIGNACIÓN ASOCIADA AL CRITERIO.
			$asignacion = $this->Assignment_model->get_one_where(array(
				"id_cliente" => $this->input->post('client'),
				"id_proyecto" => $this->input->post('project'),
				"id_criterio" => $rule_id,
				"deleted" => 0
			));
			
			if($asignacion->id){
				
				$criterio_sp_before_edit = $rule->id_campo_sp; // CRITERIO SP ANTES DE EDITAR
				$criterio_pu_before_edit = $rule->id_campo_pu; // CRITERIO PU ANTES DE EDITAR
				$criterio_sp_before_edit_json = json_decode($rule->tipo_by_criterio)->id_campo_sp; // CRITERIO SP JSON ANTES DE EDITAR
				$criterio_pu_before_edit_json = json_decode($rule->tipo_by_criterio)->id_campo_pu; // CRITERIO PU JSON ANTES DE EDITAR
				
				// SI NO ESTABA SETEADO EL CRITERIO SP Y ESTÁ SIENDO SETEADO 
				if( (!$criterio_sp_before_edit && !$criterio_sp_before_edit_json) && ($id_campo_sp || $id_campo_sp_js) ){
					$message = sprintf($this->lang->line('cant_edit_rule_field'), lang("subproject_rule"));
					echo json_encode(array("success" => false, 'message' => $message));
					exit();
				}
				
				// SI NO ESTABA SETEADO EL CRITERIO PU Y ESTÁ SIENDO SETEADO 
				if( (!$criterio_pu_before_edit && !$criterio_pu_before_edit_json) && ($id_campo_pu || $id_campo_pu_js) ){
					$message = sprintf($this->lang->line('cant_edit_rule_field'), lang("unit_processes_rule"));
					echo json_encode(array("success" => false, 'message' => $message));
					exit();
				}
				
				// SI ESTABA SETEADO EL CRITERIO SP Y ESTÁ SIENDO MODIFICADO
				if( ($criterio_sp_before_edit || $criterio_sp_before_edit_json) && ($id_campo_sp != $criterio_sp_before_edit || $id_campo_sp_js != $criterio_sp_before_edit_json) ){
					$message = sprintf($this->lang->line('cant_edit_rule_field'), lang("subproject_rule"));
					echo json_encode(array("success" => false, 'message' => $message));
					exit();
				}
				
				// SI ESTABA SETEADO EL CRITERIO PU Y ESTÁ SIENDO MODIFICADO
				if( ($criterio_pu_before_edit || $criterio_pu_before_edit_json) && ($id_campo_pu != $criterio_pu_before_edit || $id_campo_pu_js != $criterio_pu_before_edit_json) ){
					$message = sprintf($this->lang->line('cant_edit_rule_field'), lang("unit_processes_rule"));
					echo json_encode(array("success" => false, 'message' => $message));
					exit();
				}
				
			} 
			
			// VALIDACIÓN: NO SE PUEDE EDITAR CAMPO CRITERIO FC, SI HAY UN CÁLCULO ASOCIADO AL CRITERIO.
			$calculo = $this->Calculation_model->get_one_where(array(
				"id_cliente" => $this->input->post('client'),
				"id_proyecto" => $this->input->post('project'),
				"id_criterio" => $rule_id,
				"deleted" => 0
			));
			
			if($calculo->id){
				
				$criterio_fc_before_edit = $rule->id_campo_fc; // CRITERIO PU ANTES DE EDITAR
				$criterio_fc_before_edit_json = json_decode($rule->tipo_by_criterio)->id_campo_fc; // CRITERIO PU JSON ANTES DE EDITAR
				
				// SI NO ESTABA SETEADO EL CRITERIO FC Y ESTÁ SIENDO SETEADO 
				if( (!$criterio_fc_before_edit && !$criterio_fc_before_edit_json) && ($id_campo_fc || $id_campo_fc_js) ){
					$message = sprintf($this->lang->line('cant_edit_rule_field'), lang("fc_rule"));
					echo json_encode(array("success" => false, 'message' => $message));
					exit();
				}
				
				// SI ESTABA SETEADO EL CRITERIO PU Y ESTÁ SIENDO MODIFICADO
				if( ($criterio_fc_before_edit || $criterio_fc_before_edit_json) && ($id_campo_fc != $criterio_fc_before_edit || $id_campo_fc_js != $criterio_fc_before_edit_json) ){
					$message = sprintf($this->lang->line('cant_edit_rule_field'), lang("fc_rule"));
					echo json_encode(array("success" => false, 'message' => $message));
					exit();
				}
				
			}
			

		//AGREGAR	
		} else {
			
			//Validación de una única etiqueta para cliente/proyecto
			$etiqueta = $data["etiqueta"];
			$client = $data["id_cliente"];
			$project = $data["id_proyecto"];
			
		    $criterios = $this->Rule_model->get_all_where(array("id_cliente" => $client, "id_proyecto" => $project, "deleted" => 0))->result();
			
			foreach($criterios as $dato){
				if($client == $dato->id_cliente && $project == $dato->id_proyecto){
					if($etiqueta == $dato->etiqueta){
						echo json_encode(array("success" => false, 'message' => lang('labels_warning')));
						exit();
					}
				}
			}
			
			if($criterio->id){
				echo json_encode(array("success" => false, 'message' => lang('duplicate_rule')));
				exit(); 
			} 
		}

        if($rule_id){
            $data["modified_by"] = $this->login_user->id;
            $data["modified"] = get_current_utc_time();
        }else{
            $data["created_by"] = $this->login_user->id;
            $data["created"] = get_current_utc_time();

        }

        $save_id = $this->Rule_model->save($data, $rule_id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_criterio_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
    
    
    //* delete or undo a rule 

    function delete_rule() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

		$asignaciones_asociadas = $this->Assignment_model->get_all_where(array("id_criterio" => $id, "deleted" => 0))->result();
		$calculos_asociados = $this->Calculation_model->get_all_where(array("id_criterio" => $id, "deleted" => 0))->result();
		
		// VALIDACIÓN: NO ELIMINAR CRITERIO SI TIENE UNA ASIGNACIÓN O UN CÁLCULO ASOCIADO
		if(count($calculos_asociados)){
			echo json_encode(array("success" => false, 'message' => lang("cant_delete_rule_calculation")));
			exit();
		}
		if(count($asignaciones_asociadas)){
			echo json_encode(array("success" => false, 'message' => lang("cant_delete_rule_assignment")));
			exit();
		}

		foreach($asignaciones_asociadas as $aa){
			$this->Assignment_model->delete($aa->id);
		}

		foreach($calculos_asociados as $ca){
			$this->Calculation_model->delete($ca->id);
		}

        if ($this->input->post('undo')) {
            if ($this->Rule_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_criterio_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Rule_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	//On change function 
    function get_criterio_of_project_json(){
    
        $id_proyecto = $this->input->post('id_proyecto');

        $criterios_de_proyecto = $this->Rule_model->get_details(array("id_proyecto" => $id_proyecto))->result();
        $crits = array();
        $crits[] = array("id" => "", "text" => "-");

            
            foreach($criterios_de_proyecto as $cri){
                $crits[] = array("id" => $cri->id, "text" => $cri->etiqueta);

            }

        echo json_encode($crits);
    }
	
	function get_rule_options(){
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		$id_criterio = $this->input->post("id_criterio");
		
		// BUSCAR SI YA EXISTIA UNA ASIGNACION ANTERIORMENTE PARA ESTA COMBINACION
		$assignment = $this->Assignment_model->get_one_where(array(
			"id_cliente" => $id_cliente,
        	"id_proyecto" => $id_proyecto,
			"id_criterio" => $id_criterio,
			"deleted" => 0,
		));
		
		// TAB SUBPROYECTOS
		// lista de subproyectos del proyecto
		$subproyectos = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $id_proyecto));
		// info del criterio
		$criterio = $this->Rule_model->get_one($id_criterio);
		
		$formulario_criterio = $this->Forms_model->get_one($criterio->id_formulario);
		$flujo_formulario_criterio = $formulario_criterio->flujo;
		
		// info del campo criterio sp seleccionado en criterio
		$campo_sp = $this->Fields_model->get_one($criterio->id_campo_sp);
		
		if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_sp){
			
			$j_data = json_decode($criterio->tipo_by_criterio);
			if($j_data->id_campo_sp == "1"){
				
				if($flujo_formulario_criterio == "Residuo"){
					$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
					$opciones_campo_sp = array();
					foreach($campos_fijos as $campo){
						$value = $campo->nombre;
						$text = $campo->nombre;
						$opciones_campo_sp[] = array("value" => $value, "text" => $text);
					}
				} 
				
				if($flujo_formulario_criterio == "Consumo"){
					$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
					$opciones_campo_sp = array();
					if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
						$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $data_tipo_origen->type_of_origin,
							"deleted" => 0
						))->result();
						foreach($tipos_origen_materia as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
						$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
						$value = lang($tipo_origen->nombre);
						$text = $tipo_origen->nombre;
						$opciones_campo_sp[] = array("value" => $value, "text" => $text);
					}
				}
				
				if($flujo_formulario_criterio == "No Aplica"){
					$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
					$opciones_campo_sp = array();
					foreach($campos_fijos as $campo){
						$value = lang($campo->nombre);
						$text = $campo->nombre;
						$opciones_campo_sp[] = array("value" => $value, "text" => $text);
					}
				}
	
			}
			
		}elseif($campo_sp->id_tipo_campo == 16){
			
			$opciones_campo_sp = array();
			$datos_campo_sp = json_decode($campo_sp->default_value);
			$id_mantenedora = $datos_campo_sp->mantenedora;
			
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			
			foreach($datos as $row){
				$fila = json_decode($row->datos, true);
				$value = $fila[$datos_campo_sp->field_value];
				$text = $fila[$datos_campo_sp->field_label];
				$opciones_campo_sp[] = array("value" => $value, "text" => $text);
			}
		}else{
			$opciones_campo_sp = json_decode($campo_sp->opciones, true);
		}
		
		$varios_sp = (count($subproyectos) > 1);
		if(!$varios_sp){
			reset($subproyectos);
			$first_key = key($subproyectos);
		}
		
		$html_sp = '';
		$html_sp .= '<div id="tabla_asignacion_sp" class="form-group">';
			$html_sp .= '<table class="table">';
			$html_sp .= '<thead>';
			$html_sp .= '<tr>';
				$html_sp .= '<th>'. lang("subproject_rule").' </th>';
				$html_sp .= '<th>'. lang("assignment_type").' </th>';
				$html_sp .= '<th>'. lang("target_subproject").' </th>';
				$html_sp .= '<th class="w10p"> % </th>';
			$html_sp .= '</tr>';
			$html_sp .= '<thead>';
			
			$index = 1;
			
			if($opciones_campo_sp){
				foreach($opciones_campo_sp as $sp){
					
					if($sp["value"]){
						
						$html_sp .= '<tbody id="row_'.$index.'">';
						$html_sp .= '<tr>';
							$html_sp .= '<td>'.$sp["value"].'</td>';
							$html_sp .= '<input type="hidden" name="criterio_sp['.$index.']" value="'.$sp["value"].'" />';
							$html_sp .= '<td>';
							$html_sp .= form_dropdown("assignment_type_sp[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_sp?"":"Total", "id='' class='tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
							if(!$varios_sp){
								$html_sp .= '<input type="hidden" name="assignment_type_sp['.$index.']" value="Total"/>';
							}
							$html_sp .= '</td>';
							$html_sp .= '<td>';
								$html_sp .= form_dropdown("subproject[".$index."]", array("" => "-") + $subproyectos, ($varios_sp?"":$first_key), "id='' class='subproject select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
								if(!$varios_sp){
									$html_sp .= '<input type="hidden" name="subproject['.$index.']" value="'.$first_key.'"/>';
								}
							$html_sp .= '</td>';
							$html_sp .= '<td>';
								$html_sp .= '-';
							$html_sp .= '</td>';
						
						$html_sp .= '</tr>';
						$html_sp .= '</tbody>';
						$index++;
						
					}
					
					
				}
			}else{
				$html_sp .= '<tbody id="">';
				$html_sp .= '<tr>';
					$html_sp .= '<td>-</td>';
					$html_sp .= '<input type="hidden" name="criterio_sp" value="" />';
					$html_sp .= '<td>';
					$html_sp .= form_dropdown("assignment_type_sp", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), "Total", "id='' class='tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' disabled");
					if(!$varios_sp){
						$html_sp .= '<input type="hidden" name="assignment_type_sp" value="Total"/>';
					}
					$html_sp .= '</td>';
					$html_sp .= '<td>';
						$html_sp .= form_dropdown("subproject", array("" => "-") + $subproyectos, ($varios_sp?"":$first_key), "id='' class='subproject select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
						if(!$varios_sp){
							$html_sp .= '<input type="hidden" name="subproject" value="'.$first_key.'"/>';
						}
					$html_sp .= '</td>';
					$html_sp .= '<td>';
						$html_sp .= '-';
					$html_sp .= '</td>';
				
				$html_sp .= '</tr>';
				$html_sp .= '</tbody>';
				
			}
			
			$html_sp .= '</table>';
		$html_sp .= '</div>';
		
		
		
		// TAB PROCESOS UNITARIOS
		
		// selects de procesos unitarios
		$unit_processes = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
		$unit_processes_select = array();
		foreach($unit_processes as $up){
			$unit_processes_select[(string)$up["id"]] = $up["nombre"];
		}
		
		// info del campo criterio pu seleccionado en criterio
		$campo_pu = $this->Fields_model->get_one($criterio->id_campo_pu);

		if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_pu){
			$j_data = json_decode($criterio->tipo_by_criterio);
			
			if($flujo_formulario_criterio == "Residuo"){
				if($j_data->id_campo_pu == "1"){
					$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
					$opciones_campo_pu = array();
					foreach($campos_fijos as $campo){
						$value = $campo->nombre;
						$text = $campo->nombre;
						$opciones_campo_pu[] = array("value" => $value, "text" => $text);
					}
				}
			}
			
			if($flujo_formulario_criterio == "Consumo"){
				$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
				$opciones_campo_pu = array();
				if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
					$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
						"id_tipo_origen" => $data_tipo_origen->type_of_origin,
						"deleted" => 0
					))->result();
					foreach($tipos_origen_materia as $campo){
						$value = lang($campo->nombre);
						$text = $campo->nombre;
						$opciones_campo_pu[] = array("value" => $value, "text" => $text);
					}
				}
				if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
					$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
					$value = lang($tipo_origen->nombre);
					$text = $tipo_origen->nombre;
					$opciones_campo_pu[] = array("value" => $value, "text" => $text);
				}
			}
			
			if($flujo_formulario_criterio == "No Aplica"){
				$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
				$opciones_campo_pu = array();
				foreach($campos_fijos as $campo){
					$value = lang($campo->nombre);
					$text = $campo->nombre;
					$opciones_campo_pu[] = array("value" => $value, "text" => $text);
				}

			}
			
		}elseif($campo_pu->id_tipo_campo == 16){// select desde mantenedora
			$opciones_campo_pu = array();
			$datos_campo_pu = json_decode($campo_pu->default_value);
			$id_mantenedora = $datos_campo_pu->mantenedora;
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			foreach($datos as $row){
				$fila = json_decode($row->datos, true);
				$value = $fila[$datos_campo_pu->field_value];
				$text = $fila[$datos_campo_pu->field_label];
				$opciones_campo_pu[] = array("value" => $value, "text" => $text);
			}
		} else {// seleccion
			$opciones_campo_pu = json_decode($campo_pu->opciones, true);
		}
		
		
		$varios_pu = (count($unit_processes_select) > 1);
		if(!$varios_pu){
			reset($unit_processes_select);
			$first_key = key($unit_processes_select);
		}
		
		$html_pu = '';
		$html_pu .= '<div id="tabla_asignacion_pu" class="form-group">';
			$html_pu .= '<table class="table">';
			$html_pu .= '<thead>';
			$html_pu .= '<tr>';
				$html_pu .= '<th>'. lang("unit_processes_rule").' </th>';
				$html_pu .= '<th>'. lang("assignment_type").' </th>';
				$html_pu .= '<th>'. lang("target_unitary_process").' </th>';
				$html_pu .= '<th class="w10p"> % </th>';
			$html_pu .= '</tr>';	
			$html_pu .= '<thead>';	
			
			$index = 1;
		
		if($opciones_campo_pu){
				
			foreach($opciones_campo_pu as $pu){
				
				if($pu["value"]){
					
					$html_pu .= '<tbody id="row_'.$index.'">';
						$html_pu .= '<tr>';
							$html_pu .= '<td>'.$pu["value"].'</td>';
							$html_pu .= '<input type="hidden" name="criterio_pu['.$index.']" value="'.$pu["value"].'" />';
							$html_pu .= '<td>';
							$html_pu .= form_dropdown("assignment_type_pu[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_pu?"":"Total", "id='' class='tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
							if(!$varios_pu){
								$html_pu .= '<input type="hidden" name="assignment_type_pu['.$index.']" value="Total"/>';
							}
							$html_pu .= '</td>';
							$html_pu .= '<td>';
								$html_pu .= form_dropdown("unit_process[".$index."]", array("" => "-") + $unit_processes_select, ($varios_pu?"":$first_key), "id='' class='unit_process select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
								if(!$varios_pu){
									$html_pu .= '<input type="hidden" name="unit_process['.$index.']" value="'.$first_key.'"/>';
								}
							$html_pu .= '</td>';
							$html_pu .= '<td>';
								$html_pu .= '-';
							$html_pu .= '</td>';
						
						$html_pu .= '</tr>';
						$html_pu .= '</tbody>';
					$index++;
					
				}
				
			}
		}else{
			$html_pu .= '<tbody id="">';
			$html_pu .= '<tr>';
				$html_pu .= '<td>-</td>';
				$html_pu .= '<input type="hidden" name="criterio_pu" value="" />';
				$html_pu .= '<td>';
				$html_pu .= form_dropdown("assignment_type_pu", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), "Total", "id='' class='tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' disabled");
				if(!$varios_pu){
					$html_pu .= '<input type="hidden" name="assignment_type_pu" value="Total"/>';
				}
				$html_pu .= '</td>';
				$html_pu .= '<td>';
					$html_pu .= form_dropdown("unit_process", array("" => "-") + $unit_processes_select, ($varios_pu?"":$first_key), "id='' class='unit_process select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
					if(!$varios_pu){
						$html_pu .= '<input type="hidden" name="unit_process" value="'.$first_key.'"/>';
					}
				$html_pu .= '</td>';
				$html_pu .= '<td>';
					$html_pu .= '-';
				$html_pu .= '</td>';
			
			$html_pu .= '</tr>';
			$html_pu .= '</tbody>';
		}
		
		$html_pu .= '</table>';
		$html_pu .= '</div>';
		
		echo json_encode(array($html_sp, $html_pu));
	}
	
	// ASIGNACIONES
    
    function asignacion_list_data() {

        $this->access_only_allowed_members();
        
		$options = array(			
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"id_criterio" => $this->input->post("id_criterio"),			
		);
		
        $list_data = $this->Assignment_model->get_details($options)->result();
        
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_asignacion_row($data);
        }
        echo json_encode(array("data" => $result));
    }
    
    //prepare a row of assignment list table

    private function _make_asignacion_row($data) {
        
        $row_data = array($data->id,
           	modal_anchor(get_uri("relationship/view_asignacion/" . $data->id), $data->company_name, array("class" => "edit", "title" => lang('view_assignment'), "data-modal-lg" => 1, "data-post-id" => $data->id)),
            $data->title,
            $data->etiqueta,
			//$data->subprojects ? $data->subprojects : "-",
            //$data->pu ? $data->pu : "-",
            
        );

        $row_data[] = modal_anchor(get_uri("relationship/view_asignacion/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_assignment'), "data-modal-lg" => 1, "data-post-id" => $data->id)) 
				. modal_anchor(get_uri("relationship/edit_assignment"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_assignment'), "data-modal-lg" => 1, "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_assignment'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("relationship/delete_assignment"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
    

	//return a row of assignment list table

    private function _row_asignacion_data($id) {
        $options = array(
            "id" => $id,
        );
        $data = $this->Assignment_model->get_details($options)->row();
        return $this->_make_asignacion_row($data);
    }

    function add_assignment() {
		
		$this->access_only_allowed_members();

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
        $view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
       
        $this->load->view('relationship/asignacion/modal_form_add', $view_data);
		
    }
	
	function edit_assignment() {
		
		$this->access_only_allowed_members();
		$assignment_id = $this->input->post('id');

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
        $view_data['model_info'] = $this->Assignment_model->get_one($assignment_id);
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_cliente));
		$view_data["criterios"] = array("" => "-") + $this->Rule_model->get_dropdown_list(array("etiqueta"), "id", array("id_proyecto" => $view_data['model_info']->id_proyecto));
		
		$view_data["subproyectos"] = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $view_data['model_info']->id_proyecto));
		$unit_processes = $this->Unit_processes_model->get_pu_of_projects($view_data['model_info']->id_proyecto)->result_array();
		$view_data["unit_processes_select"] = array();
		
		foreach($unit_processes as $up){
			$view_data["unit_processes_select"][(string)$up["id"]] = $up["nombre"];
		}
		
		//
		$id_cliente = $view_data['model_info']->id_cliente;
		$id_proyecto = $view_data['model_info']->id_proyecto;
		$id_criterio = $view_data['model_info']->id_criterio;
		
		// TAB SUBPROYECTOS
		// lista de subproyectos del proyecto
		$subproyectos = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $id_proyecto));
		// info del criterio
		$criterio = $this->Rule_model->get_one($id_criterio);
		
		$formulario_criterio = $this->Forms_model->get_one($criterio->id_formulario);
		$flujo_formulario_criterio = $formulario_criterio->flujo;
		
		// info del campo criterio sp seleccionado en criterio
		$campo_sp = $this->Fields_model->get_one($criterio->id_campo_sp);
		
		if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_sp){
			
			$j_data = json_decode($criterio->tipo_by_criterio);
			if($j_data->id_campo_sp == "1"){
				
				if($flujo_formulario_criterio == "Residuo"){
					$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
					$opciones_campo_sp = array();
					foreach($campos_fijos as $campo){
						$value = $campo->nombre;
						$text = $campo->nombre;
						$opciones_campo_sp[] = array("value" => $value, "text" => $text);
					}
				}
				
				if($flujo_formulario_criterio == "Consumo"){
					$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
					$opciones_campo_sp = array();
					if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
						$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $data_tipo_origen->type_of_origin,
							"deleted" => 0
						))->result();
						foreach($tipos_origen_materia as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
						$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
						$value = lang($tipo_origen->nombre);
						$text = $tipo_origen->nombre;
						$opciones_campo_sp[] = array("value" => $value, "text" => $text);
					}
				}
				
				if($flujo_formulario_criterio == "No Aplica"){
					$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
					$opciones_campo_sp = array();
					foreach($campos_fijos as $campo){
						$value = lang($campo->nombre);
						$text = $campo->nombre;
						$opciones_campo_sp[] = array("value" => $value, "text" => $text);
					}
				}
				
			}
			
		}elseif($campo_sp->id_tipo_campo == 16){
			
			$opciones_campo_sp = array();
			$datos_campo_sp = json_decode($campo_sp->default_value);
			$id_mantenedora = $datos_campo_sp->mantenedora;
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			
			foreach($datos as $row){
				$fila = json_decode($row->datos, true);
				$value = $fila[$datos_campo_sp->field_value];
				$text = $fila[$datos_campo_sp->field_label];
				$opciones_campo_sp[] = array("value" => $value, "text" => $text);
			}
			
		}else{
			$opciones_campo_sp = json_decode($campo_sp->opciones, true);
		}
		
		$varios_sp = (count($subproyectos) > 1);
		if(!$varios_sp){
			reset($subproyectos);
			$first_key = key($subproyectos);
		}
		
		$html_sp = '';
		$html_sp .= '<div id="tabla_asignacion_sp" class="form-group">';
			$html_sp .= '<table class="table">';
			$html_sp .= '<thead>';
			$html_sp .= '<tr>';
				$html_sp .= '<th>'. lang("subproject_rule").' </th>';
				$html_sp .= '<th>'. lang("assignment_type").' </th>';
				$html_sp .= '<th>'. lang("target_subproject").' </th>';
				$html_sp .= '<th class="w10p"> % </th>';
			$html_sp .= '</tr>';	
			$html_sp .= '<thead>';	
			
			$index = 1;
			
			if($opciones_campo_sp){
				foreach($opciones_campo_sp as $sp){
					
					if($sp["value"]){
						
						$combinacion = $this->Assignment_combinations_model->get_one_where(
							array(
								"id_asignacion" => $assignment_id,
								"criterio_sp" => $sp["value"],
								"deleted" => 0,
							)
						);
						
						// VERIFICO QUE LA PRIMERA PATA DE LA COMBINACION EXISTE, SI NO, ENTONCES HAY QUE AGREGAR UN TR NUEVO
						if($combinacion->id){
						
							$tipo_asignacion_sp = $combinacion->tipo_asignacion_sp;
							$sp_destino = $combinacion->sp_destino;
							$trs = '';
							
							$html_sp .= '<tbody id="row_'.$index.'">';
							$html_sp .= '<tr>';
								
								// SEPARACION ENTRE TOTAL Y PORCENTAJE
								if($tipo_asignacion_sp == "Total"){
									
									$html_sp .= '<td>'.$sp["value"].'</td>';
									$html_sp .= '<input type="hidden" name="criterio_sp['.$index.']" value="'.$sp["value"].'" />';
									$html_sp .= '<td>';
									$html_sp .= form_dropdown("assignment_type_sp[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_sp?$tipo_asignacion_sp:"Total", "id='' class='tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
									if(!$varios_sp){
										$html_sp .= '<input type="hidden" name="assignment_type_sp['.$index.']" value="Total"/>';
									}
									$html_sp .= '</td>';
									
									$html_sp .= '<td>';
										$html_sp .= form_dropdown("subproject[".$index."]", array("" => "-") + $subproyectos, ($varios_sp?$sp_destino:$first_key), "id='' class='subproject select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
										if(!$varios_sp){
											$html_sp .= '<input type="hidden" name="subproject['.$index.']" value="'.$first_key.'"/>';
										}
									$html_sp .= '</td>';
									$html_sp .= '<td>';
										$html_sp .= '-';
									$html_sp .= '</td>';
									
								}elseif($tipo_asignacion_sp == "Porcentual"){
									
									$html_sp .= '<td rowspan="'.count($subproyectos).'">'.$sp["value"].'</td>';
									$html_sp .= '<input type="hidden" name="criterio_sp['.$index.']" value="'.$sp["value"].'" />';
									$html_sp .= '<td rowspan="'.count($subproyectos).'">';
									$html_sp .= form_dropdown("assignment_type_sp[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_sp?$tipo_asignacion_sp:"Total", "id='' class='tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
									if(!$varios_sp){
										$html_sp .= '<input type="hidden" name="assignment_type_sp['.$index.']" value="Total"/>';
									}
									$html_sp .= '</td>';
									
									$porcentajes_sp = $combinacion->porcentajes_sp;
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									
									$row_cont = 1;
									foreach($porcentajes_sp_decoded as $id_sp => $porcentaje_sp){
										$info_sp = $this->Subprojects_model->get_one($id_sp);
										if($row_cont == 1){
											$html_sp .= '<td>';
											$html_sp .= $info_sp->nombre;
											$html_sp .= '</td>';
											
											$html_sp .= '<td>';
											$html_sp .= '<div class="slider"></div><span class="value">'.$porcentaje_sp.'%</span><input type="hidden" name="porc_sp['.$index.']['.$id_sp.']" class="porc" value="'.$porcentaje_sp.'"/>';
											$html_sp .= '</td>';
											
										}elseif($row_cont > 1 && $row_cont < count($subproyectos)){
	
											$trs .= '<tr>';
											$trs .= '<td>';
											$trs .= $info_sp->nombre;
											$trs .= '</td>';
											
											$trs .= '<td>';
											$trs .= '<div class="slider"></div><span class="value">'.$porcentaje_sp.'%</span><input type="hidden" name="porc_sp['.$index.']['.$id_sp.']" class="porc" value="'.$porcentaje_sp.'"/>';
											$trs .= '</td>';
											$trs .= '</tr>';
											
										}else{
											
											$trs .= '<tr>';
											$trs .= '<td>';
											$trs .= $info_sp->nombre;
											$trs .= '</td>';
											
											$trs .= '<td>';
											$trs .= '<div class="slider"></div><span class="value">'.$porcentaje_sp.'%</span><input type="hidden" name="porc_sp['.$index.']['.$id_sp.']" class="porc" value="'.$porcentaje_sp.'"/>';
											$trs .= '<input type="hidden" name="porc_total_sp['.$index.']" value="100" class="campo_porc_total" data-rule-required="true" data-msg-required="' . lang('field_required') . '" data-rule-equals="100" data-msg-equals="'.lang('field_must_be_equals_to').'">';
											$trs .= '</td>';
											$trs .= '</tr>';
											
										}
										$row_cont++;
									}
									
									
								}else{
									
								}
								
							
							$html_sp .= '</tr>';
							$html_sp .= $trs;
							$html_sp .= '</tbody>';
							
						}else{// LA OPCION QUE SE ESTA INGRESANDO ES NUEVA
							
							$html_sp .= '<tbody id="row_'.$index.'">';
							$html_sp .= '<tr>';
								$html_sp .= '<td>'.$sp["value"].'</td>';
								$html_sp .= '<input type="hidden" name="criterio_sp['.$index.']" value="'.$sp["value"].'" />';
								$html_sp .= '<td>';
								$html_sp .= form_dropdown("assignment_type_sp[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_sp?"":"Total", "id='' class='tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
								if(!$varios_sp){
									$html_sp .= '<input type="hidden" name="assignment_type_sp['.$index.']" value="Total"/>';
								}
								$html_sp .= '</td>';
								$html_sp .= '<td>';
									$html_sp .= form_dropdown("subproject[".$index."]", array("" => "-") + $subproyectos, ($varios_sp?"":$first_key), "id='' class='subproject select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
									if(!$varios_sp){
										$html_sp .= '<input type="hidden" name="subproject['.$index.']" value="'.$first_key.'"/>';
									}
								$html_sp .= '</td>';
								$html_sp .= '<td>';
									$html_sp .= '-';
								$html_sp .= '</td>';
							
							$html_sp .= '</tr>';
							$html_sp .= '</tbody>';
							
						}
						
						$index++;
						
					}
					
					
				}
			}else{

				$combinacion = $this->Assignment_combinations_model->get_one_where(
					array(
						"id_asignacion" => $assignment_id,
						"criterio_sp" => NULL,
						"deleted" => 0,
					)
				);
				
				if($combinacion->id){
					//$tipo_asignacion_sp = $combinacion->tipo_asignacion_sp;
					$sp_destino = $combinacion->sp_destino;
				}else{
					$sp_destino = "";
				}

				$html_sp .= '<tbody id="">';
				$html_sp .= '<tr>';
					$html_sp .= '<td>-</td>';
					$html_sp .= '<input type="hidden" name="criterio_sp" value="" />';
					$html_sp .= '<td>';
					$html_sp .= form_dropdown("assignment_type_sp", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), "Total", "id='' class='tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' disabled");
					if(!$varios_sp){
						$html_sp .= '<input type="hidden" name="assignment_type_sp" value="Total"/>';
					}
					$html_sp .= '</td>';
					$html_sp .= '<td>';
						$html_sp .= form_dropdown("subproject", array("" => "-") + $subproyectos, $sp_destino, "id='' class='subproject select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_sp?"":"disabled"));
						if(!$varios_sp){
							$html_sp .= '<input type="hidden" name="subproject" value="'.$first_key.'"/>';
						}
					$html_sp .= '</td>';
					$html_sp .= '<td>';
						$html_sp .= '-';
					$html_sp .= '</td>';
				
				$html_sp .= '</tr>';
				$html_sp .= '</tbody>';
				
			}
			
			$html_sp .= '</table>';
		$html_sp .= '</div>';
		
		$view_data["html_sp"] = $html_sp;
		$view_data["modelo_sp"] = form_dropdown("subproject[1]", array("" => "-") + $subproyectos, "", "id='' class='subproject select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ");
		
		
		
		// TAB PROCESOS UNITARIOS
		// selects de procesos unitarios
		$unit_processes = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
		$unit_processes_select = array();
		foreach($unit_processes as $up){
			$unit_processes_select[(string)$up["id"]] = $up["nombre"];
		}
		
		// info del campo criterio pu seleccionado en criterio
		$campo_pu = $this->Fields_model->get_one($criterio->id_campo_pu);

		if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_pu){

			$j_data = json_decode($criterio->tipo_by_criterio);
			if($j_data->id_campo_pu == "1"){
				
				if($flujo_formulario_criterio == "Residuo"){
					$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
					$opciones_campo_pu = array();
					foreach($campos_fijos as $campo){
						$value = $campo->nombre;
						$text = $campo->nombre;
						$opciones_campo_pu[] = array("value" => $value, "text" => $text);
					}
				}
				
				if($flujo_formulario_criterio == "Consumo"){
					$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
					$opciones_campo_pu = array();
					if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
						$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $data_tipo_origen->type_of_origin,
							"deleted" => 0
						))->result();
						foreach($tipos_origen_materia as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
						$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
						$value = lang($tipo_origen->nombre);
						$text = $tipo_origen->nombre;
						$opciones_campo_pu[] = array("value" => $value, "text" => $text);
					}
				}
				
				if($flujo_formulario_criterio == "No Aplica"){
					$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
					$opciones_campo_pu = array();
					foreach($campos_fijos as $campo){
						$value = lang($campo->nombre);
						$text = $campo->nombre;
						$opciones_campo_pu[] = array("value" => $value, "text" => $text);
					}
				}
				
			}	
		}elseif($campo_pu->id_tipo_campo == 16){// select desde mantenedora
			$opciones_campo_pu = array();
			$datos_campo_pu = json_decode($campo_pu->default_value);
			$id_mantenedora = $datos_campo_pu->mantenedora;
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			foreach($datos as $row){
				$fila = json_decode($row->datos, true);
				$value = $fila[$datos_campo_pu->field_value];
				$text = $fila[$datos_campo_pu->field_label];
				$opciones_campo_pu[] = array("value" => $value, "text" => $text);
			}
		} else {// seleccion
			$opciones_campo_pu = json_decode($campo_pu->opciones, true);
		}
		
		$varios_pu = (count($unit_processes_select) > 1);
		if(!$varios_pu){
			reset($unit_processes_select);
			$first_key = key($unit_processes_select);
		}
		
		$html_pu = '';
		$html_pu .= '<div id="tabla_asignacion_pu" class="form-group">';
			$html_pu .= '<table class="table">';
			$html_pu .= '<thead>';
			$html_pu .= '<tr>';
				$html_pu .= '<th>'. lang("unit_processes_rule").' </th>';
				$html_pu .= '<th>'. lang("assignment_type").' </th>';
				$html_pu .= '<th>'. lang("target_unitary_process").' </th>';
				$html_pu .= '<th class="w10p"> % </th>';
			$html_pu .= '</tr>';	
			$html_pu .= '<thead>';	
			
			$index = 1;
		
		if($opciones_campo_pu){
			foreach($opciones_campo_pu as $pu){
				
				if($pu["value"]){
					
					$combinacion = $this->Assignment_combinations_model->get_one_where(
						array(
							"id_asignacion" => $assignment_id,
							"criterio_pu" => $pu["value"],
							"deleted" => 0,
						)
					);
					
					// VERIFICO QUE LA PRIMERA PATA DE LA COMBINACION EXISTE, SI NO, ENTONCES HAY QUE AGREGAR UN TR NUEVO
					if($combinacion->id){
						
						$tipo_asignacion_pu = $combinacion->tipo_asignacion_pu;
						$pu_destino = $combinacion->pu_destino;
						$trs = '';
						
						$html_pu .= '<tbody id="row_'.$index.'">';
						$html_pu .= '<tr>';
						
							// SEPARACION ENTRE TOTAL Y PORCENTAJE
							if($tipo_asignacion_pu == "Total"){
								
								$html_pu .= '<td>'.$pu["value"].'</td>';
								$html_pu .= '<input type="hidden" name="criterio_pu['.$index.']" value="'.$pu["value"].'" />';
								$html_pu .= '<td>';
								$html_pu .= form_dropdown("assignment_type_pu[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_pu?$tipo_asignacion_pu:"Total", "id='' class='tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
								if(!$varios_pu){
									$html_pu .= '<input type="hidden" name="assignment_type_pu['.$index.']" value="Total"/>';
								}
								$html_pu .= '</td>';
								$html_pu .= '<td>';
									$html_pu .= form_dropdown("unit_process[".$index."]", array("" => "-") + $unit_processes_select, ($varios_pu?$pu_destino:$first_key), "id='' class='unit_process select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
									if(!$varios_pu){
										$html_pu .= '<input type="hidden" name="unit_process['.$index.']" value="'.$first_key.'"/>';
									}
								$html_pu .= '</td>';
								$html_pu .= '<td>';
									$html_pu .= '-';
								$html_pu .= '</td>';
								
							}elseif($tipo_asignacion_pu == "Porcentual"){
								
								$html_pu .= '<td rowspan="'.count($unit_processes).'">'.$pu["value"].'</td>';
								$html_pu .= '<input type="hidden" name="criterio_pu['.$index.']" value="'.$pu["value"].'" />';
								$html_pu .= '<td rowspan="'.count($unit_processes).'">';
								$html_pu .= form_dropdown("assignment_type_pu[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_pu?$tipo_asignacion_pu:"Total", "id='' class='tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
								if(!$varios_pu){
									$html_pu .= '<input type="hidden" name="assignment_type_pu['.$index.']" value="Total"/>';
								}
								$html_pu .= '</td>';
								
								$porcentajes_pu = $combinacion->porcentajes_pu;
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								
								$row_cont = 1;
								foreach($porcentajes_pu_decoded as $id_pu => $porcentaje_pu){
									$info_pu = $this->Unit_processes_model->get_one($id_pu);
									if($row_cont == 1){
										$html_pu .= '<td>';
										$html_pu .= $info_pu->nombre;
										$html_pu .= '</td>';
										
										$html_pu .= '<td>';
										$html_pu .= '<div class="slider"></div><span class="value">'.$porcentaje_pu.'%</span><input type="hidden" name="porc_pu['.$index.']['.$id_pu.']" class="porc" value="'.$porcentaje_pu.'"/>';
										$html_sp .= '</td>';
										
									}elseif($row_cont > 1 && $row_cont < count($unit_processes)){
	
										$trs .= '<tr>';
										$trs .= '<td>';
										$trs .= $info_pu->nombre;
										$trs .= '</td>';
										
										$trs .= '<td>';
										$trs .= '<div class="slider"></div><span class="value">'.$porcentaje_pu.'%</span><input type="hidden" name="porc_pu['.$index.']['.$id_pu.']" class="porc" value="'.$porcentaje_pu.'"/>';
										$trs .= '</td>';
										$trs .= '</tr>';
										
									}else{
										
										$trs .= '<tr>';
										$trs .= '<td>';
										$trs .= $info_pu->nombre;
										$trs .= '</td>';
										
										$trs .= '<td>';
										$trs .= '<div class="slider"></div><span class="value">'.$porcentaje_pu.'%</span><input type="hidden" name="porc_pu['.$index.']['.$id_pu.']" class="porc" value="'.$porcentaje_pu.'"/>';
										$trs .= '<input type="hidden" name="porc_total_pu['.$index.']" value="100" class="campo_porc_total" data-rule-required="true" data-msg-required="' . lang('field_required') . '" data-rule-equals="100" data-msg-equals="'.lang('field_must_be_equals_to').'">';
										$trs .= '</td>';
										$trs .= '</tr>';
										
									}
									$row_cont++;
								}
									
							}else{
								
							}
							
						$html_pu .= '</tr>';
						$html_pu .= $trs;
						$html_pu .= '</tbody>';
						
					}else{// LA OPCION QUE SE ESTA INGRESANDO ES NUEVA
					
						$html_pu .= '<tbody id="row_'.$index.'">';
						$html_pu .= '<tr>';
							$html_pu .= '<td>'.$pu["value"].'</td>';
							$html_pu .= '<input type="hidden" name="criterio_pu['.$index.']" value="'.$pu["value"].'" />';
							$html_pu .= '<td>';
							$html_pu .= form_dropdown("assignment_type_pu[".$index."]", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $varios_pu?"":"Total", "id='' class='tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
							if(!$varios_pu){
								$html_pu .= '<input type="hidden" name="assignment_type_pu['.$index.']" value="Total"/>';
							}
							$html_pu .= '</td>';
							$html_pu .= '<td>';
								$html_pu .= form_dropdown("unit_process[".$index."]", array("" => "-") + $unit_processes_select, ($varios_pu?"":$first_key), "id='' class='unit_process select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
								if(!$varios_pu){
									$html_pu .= '<input type="hidden" name="unit_process['.$index.']" value="'.$first_key.'"/>';
								}
							$html_pu .= '</td>';
							$html_pu .= '<td>';
								$html_pu .= '-';
							$html_pu .= '</td>';
						
						$html_pu .= '</tr>';
						$html_pu .= '</tbody>';
					
					}
					
					$index++;
					
				}
				
			}
		}else{

			$combinacion = $this->Assignment_combinations_model->get_one_where(
				array(
					"id_asignacion" => $assignment_id,
					"criterio_pu" => NULL,
					"deleted" => 0,
				)
			);
			
			if($combinacion->id){
				//$tipo_asignacion_pu = $combinacion->tipo_asignacion_pu;
				$pu_destino = $combinacion->pu_destino;
			}else{
				$pu_destino = "";
			}
			
			$html_pu .= '<tbody id="">';
			$html_pu .= '<tr>';
				$html_pu .= '<td>-</td>';
				$html_pu .= '<input type="hidden" name="criterio_pu" value="" />';
				$html_pu .= '<td>';
				$html_pu .= form_dropdown("assignment_type_pu", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), "Total", "id='' class='tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' disabled");
				if(!$varios_pu){
					$html_pu .= '<input type="hidden" name="assignment_type_pu" value="Total"/>';
				}
				$html_pu .= '</td>';
				$html_pu .= '<td>';
					$html_pu .= form_dropdown("unit_process", array("" => "-") + $unit_processes_select, $pu_destino, "id='' class='unit_process select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".($varios_pu?"":"disabled"));
					if(!$varios_pu){
						$html_pu .= '<input type="hidden" name="unit_process" value="'.$first_key.'"/>';
					}
				$html_pu .= '</td>';
				$html_pu .= '<td>';
					$html_pu .= '-';
				$html_pu .= '</td>';
			
			$html_pu .= '</tr>';
			$html_pu .= '</tbody>';
		}
		
		$html_pu .= '</table>';
		$html_pu .= '</div>';
		
		$view_data["html_pu"] = $html_pu;
		$view_data["modelo_pu"] = form_dropdown("unit_process[1]", array("" => "-") + $unit_processes_select, "", "id='' class='unit_process select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ");
		
        $this->load->view('relationship/asignacion/modal_form_edit', $view_data);
		
    }

	
	function get_target_subprojects_of_projects(){
		
		$id_proyecto = $this->input->post('id_proyecto');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$subproyectos_de_proyecto = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $id_proyecto));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="target_subproject" class="col-md-3">'.lang('target_subproject').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_dropdown("target_subproject", array("" => "-") + $subproyectos_de_proyecto, "", "id='target_subproject' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	function get_pu_destino_of_projects(){
		
		$id_proyecto = $this->input->post('id_proyecto');
		$unit_processes = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
		
		$unit_processes_select = array();
		foreach($unit_processes as $up){
			$unit_processes_select[(string)$up["id"]] = $up["nombre"];
		}
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="unit_process" class="col-md-3">'.lang('unit_process').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_dropdown("unit_process", array("" => "-") + $unit_processes_select, "", "id='unit_process' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	function save_asignacion_add() {
		
		$array_save_id = array();
		
		// DATOS TABLA ASIGNACIONES
		$data_asignaciones = array(
			"id_cliente" => $this->input->post('client_id2'),
        	"id_proyecto" => $this->input->post('project2'),
			"id_criterio" => $this->input->post('criterio2'),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time(),
		);
		$id_criterio = $this->input->post('criterio2');
		
		// VALIDACION DE ASIGNACION PRE-EXISTENTE
		$assignment = $this->Assignment_model->get_one_where(array(
			"id_cliente" => $this->input->post('client_id2'),
        	"id_proyecto" => $this->input->post('project2'),
			"id_criterio" => $this->input->post('criterio2'),
			"deleted" => 0,
		));
		
		if($assignment->id){
			echo json_encode(array("success" => false, 'message' => lang('duplicate_assignment')));
			exit(); 
		}
		
		$save_asignacion_id = $this->Assignment_model->save($data_asignaciones);
		if($save_asignacion_id){
		
			// PROCESO LAS COMBINACIONES
			$filas_finales = array();
			
			$criterio = $this->Rule_model->get_one($id_criterio);
			$campo_sp = $this->Fields_model->get_one($criterio->id_campo_sp);
			$campo_pu = $this->Fields_model->get_one($criterio->id_campo_pu);
			
			$formulario_criterio = $this->Forms_model->get_one($criterio->id_formulario);
			$flujo_formulario_criterio = $formulario_criterio->flujo;
			
			if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_sp){

				$j_data = json_decode($criterio->tipo_by_criterio);
				if($j_data->id_campo_sp == "1"){
					
					if($flujo_formulario_criterio == "Residuo"){
						$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
						$opciones_campo_sp = array();
						$opciones_campo_sp[] = array("value" => "", "text" => "-");
						foreach($campos_fijos as $campo){
							$value = $campo->nombre;
							$text = $campo->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "Consumo"){
						$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
						$opciones_campo_sp = array();
						if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
							$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
								"id_tipo_origen" => $data_tipo_origen->type_of_origin,
								"deleted" => 0
							))->result();
							foreach($tipos_origen_materia as $campo){
								$value = lang($campo->nombre);
								$text = $campo->nombre;
								$opciones_campo_sp[] = array("value" => $value, "text" => $text);
							}
						}
						if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
							$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
							$value = lang($tipo_origen->nombre);
							$text = $tipo_origen->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
													
						}
					}
					
					if($flujo_formulario_criterio == "No Aplica"){
						$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
						$opciones_campo_sp = array();
						foreach($campos_fijos as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					
				}	
			}else if($campo_sp->id_tipo_campo == 16){// select desde mantenedora
				//$opciones_campo_sp = json_decode($campo_sp->default_value);
				$opciones_campo_sp = array();
				$opciones_campo_sp[] = array("value" => "", "text" => "-");
				$datos_campo_sp = json_decode($campo_sp->default_value);
				$id_mantenedora = $datos_campo_sp->mantenedora;
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				foreach($datos as $row){
					$fila = json_decode($row->datos, true);
					$value = $fila[$datos_campo_sp->field_value];
					$text = $fila[$datos_campo_sp->field_label];
					$opciones_campo_sp[] = array("value" => $value, "text" => $text);
				}
			} else {// seleccion
				$opciones_campo_sp = json_decode($campo_sp->opciones, true);
			}
			
			if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_pu){

				$j_data = json_decode($criterio->tipo_by_criterio);
				if($j_data->id_campo_pu == "1"){
					
					if($flujo_formulario_criterio == "Residuo"){
						$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
						$opciones_campo_pu = array();
						$opciones_campo_pu[] = array("value" => "", "text" => "-");
						foreach($campos_fijos as $campo){
							$value = $campo->nombre;
							$text = $campo->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "Consumo"){
						$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
						$opciones_campo_pu = array();
						if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
							$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
								"id_tipo_origen" => $data_tipo_origen->type_of_origin,
								"deleted" => 0
							))->result();
							foreach($tipos_origen_materia as $campo){
								$value = lang($campo->nombre);
								$text = $campo->nombre;
								$opciones_campo_pu[] = array("value" => $value, "text" => $text);
							}
						}
						if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
							$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
							$value = lang($tipo_origen->nombre);
							$text = $tipo_origen->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "No Aplica"){
						$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
						$opciones_campo_pu = array();
						foreach($campos_fijos as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					
				}	
			}else if($campo_pu->id_tipo_campo == 16){// select desde mantenedora
				//$opciones_campo_pu = json_decode($campo_pu->default_value);
				$opciones_campo_pu = array();
				$opciones_campo_pu[] = array("value" => "", "text" => "-");
				$datos_campo_pu = json_decode($campo_pu->default_value);
				$id_mantenedora = $datos_campo_pu->mantenedora;
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				foreach($datos as $row){
					$fila = json_decode($row->datos, true);
					$value = $fila[$datos_campo_pu->field_value];
					$text = $fila[$datos_campo_pu->field_label];
					$opciones_campo_pu[] = array("value" => $value, "text" => $text);
				}
			} else {// seleccion
				$opciones_campo_pu = json_decode($campo_pu->opciones, true);
			}
			
			$criterio_sp = array();
			$criterio_pu = array();
			$tipos_asignaciones_sp = array();
			$tipos_asignaciones_pu = array();
			$destinos_asignaciones_sp = array();
			$destinos_asignaciones_pu = array();
			$porcentajes_sp = array();
			$porcentajes_pu = array();
			
			if($this->input->post('assignment_type_sp')){
				$sp_tipo_asignacion = array_values($this->input->post('assignment_type_sp'));
			}
			if($this->input->post('assignment_type_pu')){
				$pu_tipo_asignacion = array_values($this->input->post('assignment_type_pu'));
			}
			$sp_destino = $this->input->post('subproject');
			$pu_destino = $this->input->post('unit_process');
			$porc_sp = $this->input->post('porc_sp');
			$porc_pu = $this->input->post('porc_pu');
			
			$cant_registros_por_guardar = 0;
			
			// QUITO LAS OPCIONES POR DEFECTO "-"
			// Para tipo Selección, Radiobuttons (id:9) no ocupan "-"
			if($campo_sp->id_tipo_campo == 6){
				array_shift($opciones_campo_sp);
			}
			if($campo_pu->id_tipo_campo == 6){
				array_shift($opciones_campo_pu);
			}
			
			// SI LOS 2 CAMPOS TRAEN OPCIONES
			if($opciones_campo_sp && $opciones_campo_pu){
				
				$index_asignacion_sp = 0;
				$index_destino_sp = 1;
				$index_porcentajes_sp = 1;
				foreach($opciones_campo_sp as $array_sp){
					
					if($array_sp["value"] == ""){continue;}
					
					$index_asignacion_pu = 0;
					$index_destino_pu = 1;
					$index_porcentajes_pu = 1;
					foreach($opciones_campo_pu as $array_pu){
						
						if($array_pu["value"] == ""){continue;}
						
						$criterio_sp[] = $array_sp["value"];
						$criterio_pu[] = $array_pu["value"];
						$tipos_asignaciones_sp[] = $sp_tipo_asignacion[$index_asignacion_sp];
						$tipos_asignaciones_pu[] = $pu_tipo_asignacion[$index_asignacion_pu];
						$destinos_asignaciones_sp[] = ($sp_destino[$index_destino_sp]=="")?NULL:$sp_destino[$index_destino_sp];
						$destinos_asignaciones_pu[] = ($pu_destino[$index_destino_pu]=="")?NULL:$pu_destino[$index_destino_pu];
						$porcentajes_sp[] = ($porc_sp[$index_porcentajes_sp]=="")?NULL:json_encode($porc_sp[$index_porcentajes_sp]);
						$porcentajes_pu[] = ($porc_pu[$index_porcentajes_pu]=="")?NULL:json_encode($porc_pu[$index_porcentajes_pu]);
						
						$index_asignacion_pu++;
						$index_destino_pu++;
						$index_porcentajes_pu++;
						
						$cant_registros_por_guardar++;
						
						
					}
					
					$index_asignacion_sp++;
					$index_destino_sp++;
					$index_porcentajes_sp++;
					
				}
				
			}

			// SI SOLO EL CAMPO SP TRAE OPCIONES
			if($opciones_campo_sp && !$opciones_campo_pu){
				
				$index_asignacion_sp = 0;
				$index_destino_sp = 1;
				$index_porcentajes_sp = 1;
				foreach($opciones_campo_sp as $array_sp){
					
					if($array_sp["value"] == ""){continue;}
					$criterio_sp[] = $array_sp["value"];
					$criterio_pu[] = NULL;
					$tipos_asignaciones_sp[] = $sp_tipo_asignacion[$index_asignacion_sp];
					$tipos_asignaciones_pu[] = "Total";
					$destinos_asignaciones_sp[] = ($sp_destino[$index_destino_sp]=="")?NULL:$sp_destino[$index_destino_sp];
					$destinos_asignaciones_pu[] = $pu_destino;
					$porcentajes_sp[] = ($porc_sp[$index_porcentajes_sp]=="")?NULL:json_encode($porc_sp[$index_porcentajes_sp]);
					$porcentajes_pu[] = NULL;
					
					$index_asignacion_sp++;
					$index_destino_sp++;
					$index_porcentajes_sp++;
					
					$cant_registros_por_guardar++;
					
				}
				
			}
			
			
			// SI SOLO EL CAMPO PU TRAE OPCIONES
			if(!$opciones_campo_sp && $opciones_campo_pu){
				
				$index_asignacion_pu = 0;
				$index_destino_pu = 1;
				$index_porcentajes_pu = 1;
				foreach($opciones_campo_pu as $array_pu){
					
					if($array_pu["value"] == ""){continue;}
					$criterio_sp[] = NULL;
					$criterio_pu[] = $array_pu["value"];
					$tipos_asignaciones_sp[] = "Total";
					$tipos_asignaciones_pu[] = $pu_tipo_asignacion[$index_asignacion_pu];
					$destinos_asignaciones_sp[] = $sp_destino;
					$destinos_asignaciones_pu[] = ($pu_destino[$index_destino_pu]=="")?NULL:$pu_destino[$index_destino_pu];
					$porcentajes_sp[] = NULL;
					$porcentajes_pu[] = ($porc_pu[$index_porcentajes_pu]=="")?NULL:json_encode($porc_pu[$index_porcentajes_pu]);
					
					$index_asignacion_pu++;
					$index_destino_pu++;
					$index_porcentajes_pu++;
					
					$cant_registros_por_guardar++;
					
				}
				
			}
			
			// SI NINGUNO DE LOS 2 CAMPOS TRAE OPCIONES
			if(!$opciones_campo_sp && !$opciones_campo_pu){
				
				// VALIDAR SI SE ESTA INGRESANDO UN SP UNICO CON CAMPO DESHABILITADO
				$subproyectos = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $this->input->post('project2')));
				if(count($subproyectos) == 1){
					reset($subproyectos);
					$sp_destino = key($subproyectos);
				}
				
				// VALIDAR SI SE ESTA INGRESANDO UN PU UNICO CON CAMPO DESHABILITADO
				$unit_processes = $this->Unit_processes_model->get_unit_process_details($this->input->post('project2'))->result();
				$unit_processes_select = array();
				foreach($unit_processes as $up){
					$unit_processes_select[(string)$up->id] = $up->nombre;
				}
				if(count($unit_processes_select) == 1){
					reset($unit_processes_select);
					$pu_destino = key($unit_processes_select);
				}
				
				$criterio_sp[] = NULL;
				$criterio_pu[] = NULL;
				$tipos_asignaciones_sp[] = "Total";
				$tipos_asignaciones_pu[] = "Total";
				$destinos_asignaciones_sp[] = $sp_destino;
				$destinos_asignaciones_pu[] = $pu_destino;
				$porcentajes_sp[] = NULL;
				$porcentajes_pu[] = NULL;

				$cant_registros_por_guardar++;

			}
			
			
			// LOS TIPOS DE ASIGNACION
			if($this->input->post('assignment_type_sp')){
				$sp_tipo_asignacion = array_values($this->input->post('assignment_type_sp'));
			}
			if($this->input->post('assignment_type_pu')){
				$pu_tipo_asignacion = array_values($this->input->post('assignment_type_pu'));
			}
			
			// LAS ID'S DE LOS DESTINOS (SI ES QUE SON TOTALES)
			$sp_destino = $this->input->post('subproject');//trae solo los nuevos
			$pu_destino = $this->input->post('unit_process');//trae solo los nuevos
			
			// LAS ID'S DE LOS DESTINOS Y SUS PORCENTAJES (SI ES QUE SON PORECENTUALES)
			$sp_porcentajes = $this->input->post('porc_sp');//trae solo los nuevos
			$pu_porcentajes = $this->input->post('porc_pu');//trae solo los nuevos
			
			$cantidad_registros = count($criterio_sp);
			
			$data_combinaciones = array(
				"created_by" => $this->login_user->id,
				"created" => get_current_utc_time(),
			);
			for($i = 1; $i <= $cantidad_registros; $i++){
				
				$data_combinaciones["id_asignacion"] = $save_asignacion_id;
				
				$data_combinaciones["criterio_sp"] = $criterio_sp[($i-1)];
				$data_combinaciones["tipo_asignacion_sp"] = $tipos_asignaciones_sp[($i-1)];
				$data_combinaciones["sp_destino"] = $destinos_asignaciones_sp[($i-1)];
				$data_combinaciones["porcentajes_sp"] = $porcentajes_sp[($i-1)];
				$data_combinaciones["criterio_pu"] = $criterio_pu[($i-1)];
				$data_combinaciones["tipo_asignacion_pu"] = $tipos_asignaciones_pu[($i-1)];
				$data_combinaciones["pu_destino"] = $destinos_asignaciones_pu[($i-1)];
				$data_combinaciones["porcentajes_pu"] = $porcentajes_pu[($i-1)];
				$save_id = $this->Assignment_combinations_model->save($data_combinaciones);
				$array_save_id[] = $save_id;
				//var_dump($tipos_asignaciones_sp[($i-1)].' - '.$tipos_asignaciones_pu[($i-1)]);
			}
	
			if(count($array_save_id) == $cant_registros_por_guardar) {	
				//echo json_encode(array("success" => true, "datos" => $filas_finales, 'ingreso' => true, 'message' => lang('record_saved')));
				echo json_encode(array("success" => true, "data" => $this->_row_asignacion_data($save_asignacion_id), 'message' => lang('record_saved')));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}
		}
    }
	
	function save_asignacion_edit() {
		
		$assignment_id = $this->input->post('id');
		$array_save_id = array();
		$save_id;
		
		$data_asignacion = array(
			"id_cliente" => $this->input->post('client_id2'),
        	"id_proyecto" => $this->input->post('project2'),
			"id_criterio" => $this->input->post('criterio2'),
			"modified_by" => $this->login_user->id,
			"modified" => get_current_utc_time(),
		);
		$id_criterio = $this->input->post('criterio2');
		
		// VALIDACION DE ASIGNACION PRE-EXISTENTE
		$assignment = $this->Assignment_model->get_one_where(array(
			"id_cliente" => $this->input->post('client_id2'),
        	"id_proyecto" => $this->input->post('project2'),
			"id_criterio" => $this->input->post('criterio2'),
			"deleted" => 0,
		));
		
		if($assignment_id != $assignment->id){
			echo json_encode(array("success" => false, 'message' => lang('duplicate_assignment')));
			exit(); 
		}
		
		$save_asignacion_id = $this->Assignment_model->save($data_asignacion, $assignment_id);
		if($save_asignacion_id){
		
			// PROCESO LAS COMBINACIONES
			$filas_finales = array();
			
			$criterio = $this->Rule_model->get_one($id_criterio);
			$campo_sp = $this->Fields_model->get_one($criterio->id_campo_sp);
			$campo_pu = $this->Fields_model->get_one($criterio->id_campo_pu);
			
			$formulario_criterio = $this->Forms_model->get_one($criterio->id_formulario);
			$flujo_formulario_criterio = $formulario_criterio->flujo;
			
			if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_sp){

				$j_data = json_decode($criterio->tipo_by_criterio);
				if($j_data->id_campo_sp == "1"){
					
					if($flujo_formulario_criterio == "Residuo"){
						$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result();
						$opciones_campo_sp = array();
						$opciones_campo_sp[] = array("value" => "", "text" => "-");
						foreach($campos_fijos as $campo){
							$value = $campo->nombre;
							$text = $campo->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "Consumo"){
						$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
						$opciones_campo_sp = array();
						if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
							$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
								"id_tipo_origen" => $data_tipo_origen->type_of_origin,
								"deleted" => 0
							))->result();
							foreach($tipos_origen_materia as $campo){
								$value = lang($campo->nombre);
								$text = $campo->nombre;
								$opciones_campo_sp[] = array("value" => $value, "text" => $text);
							}
						}
						if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
							$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
							$value = lang($tipo_origen->nombre);
							$text = $tipo_origen->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "No Aplica"){
						$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
						$opciones_campo_sp = array();
						foreach($campos_fijos as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_sp[] = array("value" => $value, "text" => $text);
						}
					}
					
				}	
				
			}else if($campo_sp->id_tipo_campo == 16){// select desde mantenedora
				$opciones_campo_sp = array();
				$opciones_campo_sp[] = array("value" => "", "text" => "-");
				$datos_campo_sp = json_decode($campo_sp->default_value);
				$id_mantenedora = $datos_campo_sp->mantenedora;
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				foreach($datos as $row){
					$fila = json_decode($row->datos, true);
					$value = $fila[$datos_campo_sp->field_value];
					$text = $fila[$datos_campo_sp->field_label];
					$opciones_campo_sp[] = array("value" => $value, "text" => $text);
				}
			} else {// seleccion
				$opciones_campo_sp = json_decode($campo_sp->opciones, true);
			}
			
			if(isset($criterio->tipo_by_criterio) && json_decode($criterio->tipo_by_criterio)->id_campo_pu){

				$j_data = json_decode($criterio->tipo_by_criterio);
				if($j_data->id_campo_pu == "1"){
					
					if($flujo_formulario_criterio == "Residuo"){
						$campos_fijos = $this->Tipo_tratamiento_model->get_all_where(array("deleted"=>0))->result();
						$opciones_campo_pu = array();
						$opciones_campo_pu[] = array("value" => "", "text" => "-");
						foreach($campos_fijos as $campo){
							$value = $campo->nombre;
							$text = $campo->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "Consumo"){
						$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
						$opciones_campo_pu = array();
						if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
							$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
								"id_tipo_origen" => $data_tipo_origen->type_of_origin,
								"deleted" => 0
							))->result();
							foreach($tipos_origen_materia as $campo){
								$value = lang($campo->nombre);
								$text = $campo->nombre;
								$opciones_campo_pu[] = array("value" => $value, "text" => $text);
							}
						}
						if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
							$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
							$value = lang($tipo_origen->nombre);
							$text = $tipo_origen->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					
					if($flujo_formulario_criterio == "No Aplica"){
						$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
						$opciones_campo_pu = array();
						foreach($campos_fijos as $campo){
							$value = lang($campo->nombre);
							$text = $campo->nombre;
							$opciones_campo_pu[] = array("value" => $value, "text" => $text);
						}
					}
					
				}	
			}else if($campo_pu->id_tipo_campo == 16){// select desde mantenedora
				$opciones_campo_pu = array();
				$opciones_campo_pu[] = array("value" => "", "text" => "-");
				$datos_campo_pu = json_decode($campo_pu->default_value);
				$id_mantenedora = $datos_campo_pu->mantenedora;
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				foreach($datos as $row){
					$fila = json_decode($row->datos, true);
					$value = $fila[$datos_campo_pu->field_value];
					$text = $fila[$datos_campo_pu->field_label];
					$opciones_campo_pu[] = array("value" => $value, "text" => $text);
				}
			} else {// seleccion
				$opciones_campo_pu = json_decode($campo_pu->opciones, true);
			}
			
			$criterio_sp = array();
			$criterio_pu = array();
			$tipos_asignaciones_sp = array();
			$tipos_asignaciones_pu = array();
			$destinos_asignaciones_sp = array();
			$destinos_asignaciones_pu = array();
			$porcentajes_sp = array();
			$porcentajes_pu = array();
			
			if($this->input->post('assignment_type_sp')){
				$sp_tipo_asignacion = array_values($this->input->post('assignment_type_sp'));
			}
			if($this->input->post('assignment_type_pu')){
				$pu_tipo_asignacion = array_values($this->input->post('assignment_type_pu'));
			}
			
			$sp_destino = $this->input->post('subproject');
			$pu_destino = $this->input->post('unit_process');
			$porc_sp = $this->input->post('porc_sp');
			$porc_pu = $this->input->post('porc_pu');
			
			$cant_registros_por_guardar = 0;
			
			// QUITO LAS OPCIONES POR DEFECTO "-"
			// Para tipo Selección, Radiobuttons (id:9) no ocupan "-"
			if($campo_sp->id_tipo_campo == 6){
				array_shift($opciones_campo_sp);
			}
			if($campo_pu->id_tipo_campo == 6){
				array_shift($opciones_campo_pu);
			}
			
			// SI LOS 2 CAMPOS TRAEN OPCIONES
			if($opciones_campo_sp && $opciones_campo_pu){
				
				$index_asignacion_sp = 0;
				$index_destino_sp = 1;
				$index_porcentajes_sp = 1;
				
				foreach($opciones_campo_sp as $array_sp){
					
					if($array_sp["value"] == ""){continue;}
					
					$index_asignacion_pu = 0;
					$index_destino_pu = 1;
					$index_porcentajes_pu = 1;
					
					foreach($opciones_campo_pu as $array_pu){
						
						if($array_pu["value"] == ""){continue;}
						
						$criterio_sp[] = $array_sp["value"];
						$criterio_pu[] = $array_pu["value"];
						$tipos_asignaciones_sp[] = $sp_tipo_asignacion[$index_asignacion_sp];
						$tipos_asignaciones_pu[] = $pu_tipo_asignacion[$index_asignacion_pu];
						$destinos_asignaciones_sp[] = ($sp_destino[$index_destino_sp] == "")?NULL:$sp_destino[$index_destino_sp];
						$destinos_asignaciones_pu[] = ($pu_destino[$index_destino_pu] == "")?NULL:$pu_destino[$index_destino_pu];
						
						$porcentajes_sp[] = ($porc_sp[$index_porcentajes_sp] == "")?NULL:json_encode($porc_sp[$index_porcentajes_sp]);
						$porcentajes_pu[] = ($porc_pu[$index_porcentajes_pu] == "")?NULL:json_encode($porc_pu[$index_porcentajes_pu]);
						
						$index_asignacion_pu++;
						$index_destino_pu++;
						$index_porcentajes_pu++;
						
						$cant_registros_por_guardar++;
						
					}
					$index_asignacion_sp++;
					$index_destino_sp++;
					$index_porcentajes_sp++;
					
				}
				
			}

			// SI SOLO EL CAMPO SP TRAE OPCIONES
			if($opciones_campo_sp && !$opciones_campo_pu){
				
				$index_asignacion_sp = 0;
				$index_destino_sp = 1;
				$index_porcentajes_sp = 1;
				foreach($opciones_campo_sp as $array_sp){
					
					if($array_sp["value"] == ""){continue;}
					
					$criterio_sp[] = $array_sp["value"];
					$criterio_pu[] = NULL;
					$tipos_asignaciones_sp[] = $sp_tipo_asignacion[$index_asignacion_sp];
					$tipos_asignaciones_pu[] = "Total";
					$destinos_asignaciones_sp[] = ($sp_destino[$index_destino_sp] == "")?NULL:$sp_destino[$index_destino_sp];
					$destinos_asignaciones_pu[] = $pu_destino;
					$porcentajes_sp[] = ($porc_sp[$index_porcentajes_sp] == "")?NULL:json_encode($porc_sp[$index_porcentajes_sp]);
					$porcentajes_pu[] = NULL;
					
					$index_asignacion_sp++;
					$index_destino_sp++;
					$index_porcentajes_sp++;
					
					$cant_registros_por_guardar++;
					
				}
				
			}
	
			
			// SI SOLO EL CAMPO PU TRAE OPCIONES
			if(!$opciones_campo_sp && $opciones_campo_pu){
				
				$index_asignacion_pu = 0;
				$index_destino_pu = 1;
				$index_porcentajes_pu = 1;
				foreach($opciones_campo_pu as $array_pu){
					
					if($array_pu["value"] == ""){continue;}
					
					$criterio_sp[] = NULL;
					$criterio_pu[] = $array_pu["value"];
					$tipos_asignaciones_sp[] = "Total";
					$tipos_asignaciones_pu[] = $pu_tipo_asignacion[$index_asignacion_pu];
					$destinos_asignaciones_sp[] = $sp_destino;
					$destinos_asignaciones_pu[] = ($pu_destino[$index_destino_pu] == "")?NULL:$pu_destino[$index_destino_pu];
					$porcentajes_sp[] = NULL;
					$porcentajes_pu[] = ($porc_pu[$index_porcentajes_pu] == "")?NULL:json_encode($porc_pu[$index_porcentajes_pu]);
					
					$index_asignacion_pu++;
					$index_destino_pu++;
					$index_porcentajes_pu++;
					
					$cant_registros_por_guardar++;
					
				}
				
			}
			
			// SI NINGUNO DE LOS 2 CAMPOS TRAE OPCIONES
			if(!$opciones_campo_sp && !$opciones_campo_pu){
				
				// VALIDAR SI SE ESTA INGRESANDO UN SP UNICO CON CAMPO DESHABILITADO
				$subproyectos = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $this->input->post('project2')));
				if(count($subproyectos) == 1){
					reset($subproyectos);
					$sp_destino = key($subproyectos);
				}
				
				// VALIDAR SI SE ESTA INGRESANDO UN PU UNICO CON CAMPO DESHABILITADO
				$unit_processes = $this->Unit_processes_model->get_unit_process_details($this->input->post('project2'))->result();
				$unit_processes_select = array();
				foreach($unit_processes as $up){
					$unit_processes_select[(string)$up->id] = $up->nombre;
				}
				if(count($unit_processes_select) == 1){
					reset($unit_processes_select);
					$pu_destino = key($unit_processes_select);
				}
				$criterio_sp[] = NULL;
				$criterio_pu[] = NULL;
				$tipos_asignaciones_sp[] = "Total";
				$tipos_asignaciones_pu[] = "Total";
				$destinos_asignaciones_sp[] = $sp_destino;
				$destinos_asignaciones_pu[] = $pu_destino;
				$porcentajes_sp[] = NULL;
				$porcentajes_pu[] = NULL;
				
				$cant_registros_por_guardar++;

			}
			
			// CONSULTO COMBINACIONES ANTES DEL CAMBIO
			$combinaciones_antes = $this->Assignment_combinations_model->get_all_where(
				array(
					"id_asignacion" => $save_asignacion_id,
					"deleted" => 0,
			))->result();
			
			/*if($this->input->post('assignment_type_sp')){
				$sp_tipo_asignacion = array_values($this->input->post('assignment_type_sp'));
			}
			if($this->input->post('assignment_type_pu')){
				$pu_tipo_asignacion = array_values($this->input->post('assignment_type_pu'));
			}
			
			// LAS ID'S DE LOS DESTINOS (SI ES QUE SON TOTALES)
			$sp_destino = $this->input->post('subproject');//trae solo los nuevos
			$pu_destino = $this->input->post('unit_process');//trae solo los nuevos
			
			// LAS ID'S DE LOS DESTINOS Y SUS PORCENTAJES (SI ES QUE SON PORCENTUALES)
			$sp_porcentajes = $this->input->post('porc_sp');//trae solo los nuevos
			$pu_porcentajes = $this->input->post('porc_pu');//trae solo los nuevos
			*/
			
			$cantidad_registros = count($criterio_sp);
			
			$data_combinaciones = array(
				"modified_by" => $this->login_user->id,
				"modified" => get_current_utc_time(),
			);
			
			for($i = 1; $i <= $cantidad_registros; $i++){
				
				$data_combinaciones["id_asignacion"] = $save_asignacion_id;
				
				$data_combinaciones["criterio_sp"] = $criterio_sp[($i-1)];
				$data_combinaciones["tipo_asignacion_sp"] = $tipos_asignaciones_sp[($i-1)];
				
				$data_combinaciones["sp_destino"] = $destinos_asignaciones_sp[($i-1)];
				$data_combinaciones["porcentajes_sp"] = $porcentajes_sp[($i-1)];
				
				$data_combinaciones["criterio_pu"] = $criterio_pu[($i-1)];
				$data_combinaciones["tipo_asignacion_pu"] = $tipos_asignaciones_pu[($i-1)];
				
				$data_combinaciones["pu_destino"] = $destinos_asignaciones_pu[($i-1)];
				$data_combinaciones["porcentajes_pu"] = $porcentajes_pu[($i-1)];
				
				// VALIDAR SI ESTA COMBINACIÓN YA EXISTIA PREVIAMENTE
				$combinacion_existente = $this->Assignment_combinations_model->get_one_where(
					array(
						"id_asignacion" => $save_asignacion_id,
						"criterio_sp" => $criterio_sp[($i-1)],
						"criterio_pu" => $criterio_pu[($i-1)],
						"deleted" => 0,
				));
				
				if($combinacion_existente->id){
					$save_combinacion_id = $this->Assignment_combinations_model->save($data_combinaciones, $combinacion_existente->id);
					$array_save_id[] = $save_combinacion_id;
				}else{
					$save_combinacion_id = $this->Assignment_combinations_model->save($data_combinaciones);
					$array_save_id[] = $save_combinacion_id;
				}
				
			}
			
			// AHORA DEBEMOS BORRAR ESAS COMBINACIONES QUE QUEDAN FUERA
			foreach($combinaciones_antes as $ca){
				if(!in_array($ca->id, $array_save_id)){
					$this->Assignment_combinations_model->delete($ca->id);
				}
			}
			
			if(count($array_save_id) == $cant_registros_por_guardar) {
				echo json_encode(array("success" => true, "data" => $this->_row_asignacion_data($save_asignacion_id), 'id' => $save_asignacion_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}
		}
	
		/*if($save_id) {
			echo json_encode(array("success" => true, "data" => $this->_row_asignacion_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}*/
        
    }
	
	
    function delete_assignment() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Assignment_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_asignacion_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if($this->Assignment_model->delete($id)) {
				
				$combinaciones = $this->Assignment_combinations_model->get_all_where(
					array(
						"id_asignacion" => $id,
						"deleted" => 0,
				))->result();
				foreach($combinaciones as $ca){
					$this->Assignment_combinations_model->delete($ca->id);
				}
				
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	
	// CALCULOS
	
	function calculo_list_data() {

        //$this->access_only_allowed_members();
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"id_criterio" => $this->input->post("id_criterio"),
			"id_bd" => $this->input->post("id_bd")
		);
		
        $list_data = $this->Calculation_model->get_details($options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_calculo_row($data);
        }
        echo json_encode(array("data" => $result));
    }
	
	/* prepare a row of rule list table */

    private function _make_calculo_row($data) {
        
		$unidades = json_decode($data->id_campo_unidad);
		$array_nombres_unidades = array();

		foreach($unidades as $key => $id_unidad){		
			if($id_unidad == 0){
				$criterio = $this->Rule_model->get_one($data->id_criterio);
				$form = $this->Forms_model->get_one($criterio->id_formulario);
				if(($form->flujo == "Residuo")||($form->flujo == "Consumo")||($form->flujo == "No Aplica")){
					$unidad_form = json_decode($form->unidad, true);
					$array_nombres_unidades[] = $unidad_form["nombre_unidad"];
				}
			}else{
				$unidad = $this->Fields_model->get_one($id_unidad);
				$array_nombres_unidades[$key] = $unidad->nombre;
			}
		}
		
		/*
		foreach($unidades as $key => $id_unidad){
			$unidad = $this->Fields_model->get_one($id_unidad);
			$array_nombres_unidades[$key] = $unidad->nombre;
		}
		*/
        $row_data = array($data->id,
            modal_anchor(get_uri("relationship/view_calculo/" . $data->id), $data->company_name,  array("title" => lang('view_calculo'), "data-post-id" => $data->id)),
            $data->title,
            $data->etiqueta,
            ($data->criterio_fc)?$data->criterio_fc:"-",
            //$data->nombre_unidad,
			//$data->id_campo_unidad,
			implode(', ', $array_nombres_unidades),
			$data->nombre_bd,
            $data->nombre_categoria,
			$data->nombre_subcategoria,
			($data->etiqueta_calculo)?$data->etiqueta_calculo:"-"
        );
					  
        $row_data[] = modal_anchor(get_uri("relationship/view_calculo/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_calculation'), "data-post-id" => $data->id)) 
				. modal_anchor(get_uri("relationship/add_calculation"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_calculation'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_calculation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("relationship/delete_calculation"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
    
    /* return a row of rule list table */

    private function _row_calculo_data($id) {
        $options = array(
            "id" => $id,
        );
        $data = $this->Calculation_model->get_details($options)->row();
        return $this->_make_calculo_row($data);
    }

    function add_calculation() {
		
        $this->access_only_allowed_members();
        $calculation_id = $this->input->post('id');

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		
		if($calculation_id){
			
			// TRAIGO LA INFO DEL CALCULO EDITADO
			$view_data['model_info'] = $this->Calculation_model->get_one($calculation_id);
			// EL CLIENTE DEL CALCULO EDITADO
			$id_cliente = $view_data['model_info']->id_cliente;
			// EL PROYECTO DEL CALCULO EDITADO
			$id_proyecto = $view_data['model_info']->id_proyecto;
			// EL ID CRITERIO DEL CALCULO EDITADO
			$id_criterio = $view_data['model_info']->id_criterio;
			// EL ID DE BASE DE DATO DEL CALCULO EDITADO
			$id_bd = $view_data['model_info']->id_bd;
			// TRAIGO LA INFO DEL CRITERIO DEL CALCULO EDITADO
			$criterio = $this->Rule_model->get_one($id_criterio);
			$opciones_criterio = $this->Fields_model->get_one($criterio->id_campo_fc);
			
			$formulario_criterio = $this->Forms_model->get_one($criterio->id_formulario);
			$flujo_formulario_criterio = $formulario_criterio->flujo;
			
			// PROYECTOS
			$view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
			
			// CRITERIOS
			$view_data["criterios"] = array("" => "-") + $this->Rule_model->get_dropdown_list(array("etiqueta"), "id", array("id_proyecto" => $id_proyecto));
			
			// OPCIONES CRITERIO FC
			$opciones = array();
			$es_mantenedora = json_decode($opciones_criterio->default_value, true);
			if($es_mantenedora["mantenedora"]){
				$id_mantenedora = $es_mantenedora["mantenedora"];
				$id_campo_label = $es_mantenedora["field_label"];
				$id_campo_value = $es_mantenedora["field_value"];
				
				$filas_mantenedora = $this->Feeders_model->get_values_of_record($id_mantenedora)->result();
				foreach($filas_mantenedora as $fila){
					$datos_criterio = json_decode($fila->datos, true);
					$valor_label = $datos_criterio[$id_campo_label];
					$valor_value = $datos_criterio[$id_campo_value];
					$opciones[$valor_value] = $valor_label;
				}
				
			}else{
				if(!$criterio->id_campo_fc){
					$criterio_data = json_decode($criterio->tipo_by_criterio, true);
					if($criterio_data["id_campo_fc"] = 1){
						
						if($flujo_formulario_criterio == "Residuo"){
							$tipos_tratamientos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result();
							foreach($tipos_tratamientos as $tp){
								$opciones[$tp->nombre] = $tp->nombre;
							}
						}
						
						if($flujo_formulario_criterio == "Consumo"){
							$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
							if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
								$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
									"id_tipo_origen" => $data_tipo_origen->type_of_origin,
									"deleted" => 0
								))->result();
								foreach($tipos_origen_materia as $campo){
									$opciones[lang($campo->nombre)] = lang($campo->nombre);
								}
							}
							if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
								$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
								$opciones[lang($tipo_origen->nombre)] = lang($tipo_origen->nombre);
							}
						}
						
						if($flujo_formulario_criterio == "No Aplica"){
							$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
							foreach($campos_fijos as $campo){
								$opciones[lang($campo->nombre)] = lang($campo->nombre);
							}
						}
						
					}
				}else{
					$opciones_criterio = json_decode($opciones_criterio->opciones);
					foreach($opciones_criterio as $index => $opcion){
						$opciones[$opcion->value] = $opcion->text;
					}
				}
			}

			/*$opciones_criterio = json_decode($opciones_criterio->opciones);
			$opciones = array();
			foreach($opciones_criterio as $index => $opcion){
				$opciones[$opcion->value] = $opcion->text;
			}*/
			
			$view_data["opciones_criterio"] = $opciones;
			
			
			// CALCULOS (CAMPOS TIPO UNDIAD)
			$campos = array();
			$form_data = $this->Forms_model->get_one($criterio->id_formulario);
	
			if(($form_data->flujo == "Residuo") || ($form_data->flujo == "Consumo") || ($form_data->flujo == "No Aplica")){
				$data_unidad = json_decode($form_data->unidad,true);
				$campos[0] = $data_unidad["nombre_unidad"];
				$form_fields = $this->Field_rel_form_model->get_fields_related_to_form($criterio->id_formulario)->result_array();
				foreach($form_fields as $field){
				$campo = $this->Fields_model->get_one($field["id_campo"]);
					if($campo->id_tipo_campo == 15){ //si es Unidad
						$campos[$field["id_campo"]] = $campo->nombre;
					}
				}
				$view_data["calculo"] = $campos;
			}else{
				$form_fields = $this->Field_rel_form_model->get_fields_related_to_form($criterio->id_formulario)->result_array();
				foreach($form_fields as $field){
				$campo = $this->Fields_model->get_one($field["id_campo"]);
					if($campo->id_tipo_campo == 15){ //si es Unidad
						$campos[$field["id_campo"]] = $campo->nombre;
					}
				}
				$view_data["calculo"] = $campos;
			}
			
			/*
			$form_fields = $this->Field_rel_form_model->get_fields_related_to_form($criterio->id_formulario)->result_array();
			foreach($form_fields as $field){
			$campo = $this->Fields_model->get_one($field["id_campo"]);
				if($campo->id_tipo_campo == 15){ //si es Unidad
					$campos[$field["id_campo"]] = $campo->nombre;
				}
			}
			$view_data["calculo"] = array("" => "-") + $campos;
			*/
			
			// BASES DE DATOS
			$criterio = $this->Rule_model->get_one($id_criterio);
			$material = $this->Materials_model->get_one($criterio->id_material);
			$id_material = $material->id;
			$proyecto = $this->Projects_model->get_one($id_proyecto);
			$id_formato_huella = $proyecto->id_formato_huella;
			$id_metodologia = $proyecto->id_metodologia;
			
			$base_de_datos = $this->Characterization_factors_model->get_databases_of_fc(array(
				"id_formato_huella" => $id_formato_huella, 
				"id_metodologia" => $id_metodologia,
				"id_material" => $id_material,
			))->result_array();
		
			$databases_dropdown = array("" => "-");
			foreach($base_de_datos as $bd){
				$databases_dropdown[$bd['id']] = $bd['nombre']; 
			}
			$view_data["bases_de_datos"] = $databases_dropdown;
		
			// CATEGORIAS
			//$category_materials = $this->Materials_rel_category_model->get_categories_related_to_material($criterio->id_material)->result_array();
			$categorias = $this->Characterization_factors_model->get_categories_of_fc(array(
				"id_bd" => $id_bd, 
				"id_formato_huella" => $id_formato_huella, 
				"id_metodologia" => $id_metodologia,
				"id_material" => $id_material,
			))->result_array();
			
			$ra_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array(
				"id_formulario" => $criterio->id_formulario,
				"id_material" => $id_material,
				"deleted" => 0
			))->result_array();
			
			$categories_dropdown = array("" => "-");
			foreach($categorias as $cat){
				foreach($ra_categorias as $ra_cat){
					if($cat['id'] == $ra_cat['id_categoria']){ 
						$id_categoria = $cat['id'];
						break;
					}
				}
				if($id_categoria == $cat['id']){
					$categories_dropdown[$cat['id']] = $cat['nombre'];
				}
			}
			$view_data["categorias"] = $categories_dropdown;
			
			// SUBCATEGORIAS
			//$subcategorias = $this->Subcategories_model->get_dropdown_list(array("nombre"), "id", array("id_categoria" => $view_data['model_info']->id_categoria));
			$subcategorias = $this->Categories_model->get_subcategories_of_category($view_data['model_info']->id_categoria)->result();
			$select_subcategorias = array("" => "-");
			foreach($subcategorias as $sub){
				$select_subcategorias[$sub->id] = $sub->nombre;
			}
			$view_data["subcategorias"] = $select_subcategorias;
			
		}else{
			$view_data["proyectos"] = array("" => "-");
			$view_data["criterios"] = array("" => "-");
			$view_data["opciones_criterio"] = array("" => "-");
			$view_data['bases_de_datos'] = array("" => "-");
			$view_data["categorias"] = array("" => "-");
			$view_data["subcategorias"] = array("" => "-");	
		}
		
        $this->load->view('relationship/calculo/modal_form', $view_data);
    }
	
	function save_calculo(){
		
		$calculo_id = $this->input->post('id');
		$criterio_fc = $this->input->post('criterio_fc_id');

		$data = array(
            "id_cliente" => $this->input->post('client_id'),
            "id_proyecto" => $this->input->post('project_id'),
            "id_criterio" => $this->input->post('criterio_id'),
            "criterio_fc" => ($criterio_fc == "" || $criterio_fc == NULL) ? NULL : $criterio_fc,
            //"id_campo_unidad" => $this->input->post('calculo'),
			"id_campo_unidad" => json_encode($this->input->post('calculo')),
			"id_bd" => $this->input->post('database'),
            "id_categoria" => $this->input->post('categoria'),
            "id_subcategoria" => $this->input->post('subcategoria'),
			"etiqueta" => $this->input->post('label')
        );

		$where_calculation = array(
            "id_cliente" => $this->input->post('client_id'),
            "id_proyecto" => $this->input->post('project_id'),
            "id_criterio" => $this->input->post('criterio_id'),
            "criterio_fc" => ($criterio_fc == "" || $criterio_fc == NULL) ? NULL : $criterio_fc,
			"id_campo_unidad" => json_encode($this->input->post('calculo')),
			//"id_bd" => $this->input->post('database'),
            "id_categoria" => $this->input->post('categoria'),
            "id_subcategoria" => $this->input->post('subcategoria'),
			"deleted" => 0
        );
		
		$calculation_same_data = $this->Calculation_model->get_one_where($where_calculation);
		
		//EDITAR
		if($calculo_id){
			
			// VALIDACION HASTA CATEGORIA
			if($calculation_same_data->id && $calculation_same_data->id != $calculo_id){
				//NO SE PUEDE REPETIR LA BASE DE DATOS EN DOS CÁLCULOS CON LAS MISMAS COMBINACIONES (DE VALORES DE SUS CAMPOS)			
				if($calculation_same_data->id_bd == $this->input->post('database')){
					echo json_encode(array("success" => false, 'message' => lang('duplicate_calculation')));
					exit();
				}
			}
			
			// VALIDACION DE UNICA ETIQUETA POR CLIENTE/PROYECTO
			$where_calculation = array(
				"id_cliente" => $this->input->post('client_id'),
				"id_proyecto" => $this->input->post('project_id'),
				"etiqueta" => $this->input->post('label'),
				"deleted" => 0
			);
			$calculation_same_client_project = $this->Calculation_model->get_one_where($where_calculation);
			if($calculation_same_client_project->id && $calculation_same_client_project->id != $calculo_id){
				echo json_encode(array("success" => false, 'message' => lang('duplicate_calculation_label')));
				exit();
			}
			
		
		} else {// AGREGAR	
			
			// VALIDACION HASTA CATEGORIA
			if($calculation_same_data->id){
				//NO SE PUEDE REPETIR LA BASE DE DATOS EN DOS CÁLCULOS CON LAS MISMAS COMBINACIONES (DE VALORES DE SUS CAMPOS)			
				if($calculation_same_data->id_bd == $this->input->post('database')){
					echo json_encode(array("success" => false, 'message' => lang('duplicate_calculation')));
					exit();
				}
			}
			
			// VALIDACION DE UNICA ETIQUETA POR CLIENTE/PROYECTO
			$where_calculation = array(
				"id_cliente" => $this->input->post('client_id'),
				"id_proyecto" => $this->input->post('project_id'),
				"etiqueta" => $this->input->post('label'),
				"deleted" => 0
			);
			$calculation_same_client_project = $this->Calculation_model->get_one_where($where_calculation);
			if($calculation_same_client_project->id){
				echo json_encode(array("success" => false, 'message' => lang('duplicate_calculation_label')));
				exit();
			}
			
		}	
		
		if($calculo_id){
            $data["modified_by"] = $this->login_user->id;
            $data["modified"] = get_current_utc_time();
        }else{
            $data["created_by"] = $this->login_user->id;
            $data["created"] = get_current_utc_time();
        }
		
		$save_id = $this->Calculation_model->save($data, $calculo_id);
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_calculo_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	function delete_calculation() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Calculation_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_calculo_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Calculation_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function get_db_of_criterio_fc(){
    
        $id_criterio = $this->input->post('id_criterio');
		
		if($id_criterio){
			$criterio = $this->Rule_model->get_one($id_criterio);
			$material = $this->Materials_model->get_one($criterio->id_material);
			$id_material = $material->id;
			$id_proyecto = $criterio->id_proyecto;
			$proyectos = $this->Projects_model->get_one($id_proyecto);
			$id_formato_huella = $proyectos->id_formato_huella;
			$id_metodologia = $proyectos->id_metodologia;
			
			$base_de_datos = $this->Characterization_factors_model->get_databases_of_fc(array(
				"id_formato_huella" => $id_formato_huella, 
				"id_metodologia" => $id_metodologia,
				"id_material" => $id_material,
			))->result_array();
		}
		
		$databases_dropdown = array("" => "-");
		foreach($base_de_datos as $bd){
			$databases_dropdown[$bd['id']] = $bd['nombre']; 
		}
		
		$html = '<div class="form-group" id="">';
			$html .= '<label for="database" class="col-md-3">'.lang('database').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("database", $databases_dropdown, "", "id='db_id' class='select2 validate-hidden' data-sigla=''");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
    }
	
	function get_categories_of_criterio_fc(){
    
        $id_criterio = $this->input->post('id_criterio');
		$id_bd = $this->input->post('id_bd');
		
		if($id_criterio && $id_bd){
			$criterio = $this->Rule_model->get_one($id_criterio);
			$material = $this->Materials_model->get_one($criterio->id_material);
			$id_material = $material->id;
			$id_proyecto = $criterio->id_proyecto;
			$proyectos = $this->Projects_model->get_one($id_proyecto);
			$id_formato_huella = $proyectos->id_formato_huella;
			$id_metodologia = $proyectos->id_metodologia;
			
			$categorias = $this->Characterization_factors_model->get_categories_of_fc(array(
				"id_bd" => $id_bd, 
				"id_formato_huella" => $id_formato_huella, 
				"id_metodologia" => $id_metodologia,
				"id_material" => $id_material,
			))->result_array();
			
			//consultar las categorias del material del registro ambiental del criterio que se seleccionó y el resto de categorías sacarla
			//$ra_criterio = $this->Forms_model->get_one($criterio->id_formulario);
			$ra_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array(
				"id_formulario" => $criterio->id_formulario,
				"id_material" => $id_material,
				"deleted" => 0
			))->result_array();
			
		}
		
		$categories_dropdown = array("" => "-");
		foreach($categorias as $cat){
			foreach($ra_categorias as $ra_cat){
				if($cat['id'] == $ra_cat['id_categoria']){ 
					$id_categoria = $cat['id'];
					break;
				}
			}
			if($id_categoria == $cat['id']){
				$categories_dropdown[$cat['id']] = $cat['nombre'];
			}
		}
		
		$html = '<div class="form-group">';
			$html .= '<label for="categoria" class="col-md-3">'.lang('category').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("categoria", $categories_dropdown, "", "id='categoria' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
    }
	
	function get_options_of_criterio_fc(){
    
        $id_criterio = $this->input->post('id_criterio');
		$criterio = $this->Rule_model->get_one($id_criterio);
		$formulario_criterio = $this->Forms_model->get_one($criterio->id_formulario);
		$flujo_formulario_criterio = $formulario_criterio->flujo;
		
		$opciones = array();
		
		if(isset($criterio->tipo_by_criterio)){
			$json_data = json_decode($criterio->tipo_by_criterio, true);
			$id_campo_fc_js = $json_data["id_campo_fc"];
		}
		
		if((isset($id_campo_fc_js)) || ($id_campo_fc_js == 1)){
			
			if($flujo_formulario_criterio == "Residuo"){
				$tipos_tratamientos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result();
				foreach($tipos_tratamientos as $tipo_tratamiento){
					$valor_label = $tipo_tratamiento->nombre;
					$valor_value = $tipo_tratamiento->nombre;
					$opciones[$valor_value] = $valor_label;
				}
			}
			
			if($flujo_formulario_criterio == "Consumo"){
				$data_tipo_origen = json_decode($formulario_criterio->tipo_origen);
				if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
					$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
						"id_tipo_origen" => $data_tipo_origen->type_of_origin,
						"deleted" => 0
					))->result();
					foreach($tipos_origen_materia as $campo){
						$valor_label = lang($campo->nombre);
						$valor_value = lang($campo->nombre);
						$opciones[$valor_value] = $valor_label;
					}
				}
				if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
					$tipo_origen = $this->EC_Types_of_origin_model->get_one(2);  // id 2: energy
					$valor_label = lang($tipo_origen->nombre);
					$valor_value = lang($tipo_origen->nombre);
					$opciones[$valor_value] = $valor_label;
				}
			}
			
			if($flujo_formulario_criterio == "No Aplica"){
				$campos_fijos = $this->EC_Types_no_apply_model->get_all()->result();
				foreach($campos_fijos as $campo){
					$valor_label = lang($campo->nombre);
					$valor_value = lang($campo->nombre);
					$opciones[$valor_value] = $valor_label;
				}
			}
			
		}else{

			$opciones_criterio = $this->Fields_model->get_one($criterio->id_campo_fc);
			$es_mantenedora = json_decode($opciones_criterio->default_value, true);
			if($es_mantenedora["mantenedora"]){
				$id_mantenedora = $es_mantenedora["mantenedora"];
				$id_campo_label = $es_mantenedora["field_label"];
				$id_campo_value = $es_mantenedora["field_value"];

				$filas_mantenedora = $this->Feeders_model->get_values_of_record($id_mantenedora)->result();
				foreach($filas_mantenedora as $fila){
					$datos_criterio = json_decode($fila->datos, true);
					$valor_label = $datos_criterio[$id_campo_label];
					$valor_value = $datos_criterio[$id_campo_value];
					$opciones[$valor_value] = $valor_label;
				}

			}else{
				$opciones_criterio = json_decode($opciones_criterio->opciones);

				foreach($opciones_criterio as $index => $opcion){
					$opciones[$opcion->value] = $opcion->text;
				} 
			}
		}

		/*
		$opciones_criterio = $this->Fields_model->get_one($criterio->id_campo_fc);
		$es_mantenedora = json_decode($opciones_criterio->default_value, true);
		if($es_mantenedora["mantenedora"]){
			$id_mantenedora = $es_mantenedora["mantenedora"];
			$id_campo_label = $es_mantenedora["field_label"];
			$id_campo_value = $es_mantenedora["field_value"];
			
			$filas_mantenedora = $this->Feeders_model->get_values_of_record($id_mantenedora)->result();
			foreach($filas_mantenedora as $fila){
				$datos_criterio = json_decode($fila->datos, true);
				$valor_label = $datos_criterio[$id_campo_label];
				$valor_value = $datos_criterio[$id_campo_value];
				$opciones[$valor_value] = $valor_label;
			}
			
		}else{
			$opciones_criterio = json_decode($opciones_criterio->opciones);
			
			foreach($opciones_criterio as $index => $opcion){
				$opciones[$opcion->value] = $opcion->text;
			} 
		}
		*/
		$html .= '<div class="form-group">';
			$html .= '<label for="criterio_fc_id" class="col-md-3">'.lang('fc_rule').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("criterio_fc_id", array("" => "-") + $opciones, "", "id='criterio_fc_id' class='select2 validate-hidden' data-sigla='' ");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
    }
	
	function get_unidades_of_criterio_fc(){
	
		$id_criterio = $this->input->post('id_criterio');
		$campos = array();
		
		if($id_criterio){
			$criterio = $this->Rule_model->get_one($id_criterio);
			$form = $this->Forms_model->get_one($criterio->id_formulario);
			$form_fields = $this->Field_rel_form_model->get_fields_related_to_form($criterio->id_formulario)->result_array();
			
			if(($form->flujo == "Residuo") || ($form->flujo == "Consumo") || ($form->flujo == "No Aplica")){
				$unidad_form = json_decode($form->unidad,"true");
				$campos[0] = $unidad_form["nombre_unidad"];
				
				foreach($form_fields as $field){
				$campo = $this->Fields_model->get_one($field["id_campo"]);
					if($campo->id_tipo_campo == 15){ //si es Unidad
						$campos[$field["id_campo"]] = $campo->nombre;
					}
				}
			}else{
				foreach($form_fields as $field){
				$campo = $this->Fields_model->get_one($field["id_campo"]);
					if($campo->id_tipo_campo == 15){ //si es Unidad
						$campos[$field["id_campo"]] = $campo->nombre;
					}
				}
			}
		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="calculo" class="col-md-3">'.lang('calculation').'</label>';
			$html .= '<div class="col-md-9">';
			//$html .= form_dropdown("calculo", array("" => "-") + $campos, "", "id='calculo' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= form_multiselect("calculo[]", $campos, "", "id='calculo' class='select2 validate-hidden multiple' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "' multiple='multiple'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	/*function get_categories_of_criterio_fc(){
		
		$id_criterio = $this->input->post('id_criterio');
		$criterio = $this->Rule_model->get_one($id_criterio);
		//$material = $this->Materials_model->get_one_where(array("id" => $criterio->id_material));
		//$category_materials = $this->Materials_rel_category_model->get_categories_related_to_material($criterio->id_material)->result_array();
		$category_materials = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form_and_material($criterio->id_formulario, $criterio->id_material)->result_array();
		
		
		$categorias = array();
		foreach($category_materials as $cm){
			$categoria = $this->Categories_model->get_one($cm["id_categoria"]);
			$categorias[$cm["id_categoria"]] = $categoria->nombre;			
 		} 
		
		$html .= '<div class="form-group">';
			$html .= '<label for="categoria" class="col-md-3">'.lang('category').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("categoria", array("" => "-") + $categorias, "", "id='categoria' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}*/
	
	function get_subcategories_of_category(){
		
		$id_criterio = $this->input->post('id_criterio');
		$id_bd = $this->input->post('id_bd');
		$id_categoria = $this->input->post("categoria");
		
		$criterio = $this->Rule_model->get_one($id_criterio);
		$id_material = $criterio->id_material;
		
		$select_subcategorias = array();
		if($id_categoria){
			$subcategorias = $this->Categories_model->get_subcategories_of_category($id_categoria)->result();
			foreach($subcategorias as $sub){
				$existen_combinaciones = $this->Characterization_factors_model->get_all_where(array("id_bd" => $id_bd, "id_material" => $id_material, "id_categoria" => $id_categoria, "id_subcategoria" => $sub->id))->result();
				if($existen_combinaciones){
					$select_subcategorias[$sub->id] = $sub->nombre;
				}
			}
		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="subcategoria" class="col-md-3">'.lang('subcategory').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("subcategoria", array("" => "-") + $select_subcategorias, "", "id='subcategoria' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
    
    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* delete or undo a client */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Clients_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Clients_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
        
        $list_data = $this->Clients_model->get_details()->result();
        
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of client list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );
        $data = $this->Clients_model->get_details($options)->row();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
        
        $row_data = array($data->id,
            //anchor(get_uri("clients/view/" . $data->id), $data->company_name),
            $data->company_name,
            $data->sigla,
            to_decimal_format($data->total_projects)
        );

        $row_data[] = modal_anchor(get_uri("clients/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_client'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete"), "data-action" => "delete"));

        return $row_data;
    }

    function view_criterio($criterio_id = 0){
		
		$this->access_only_allowed_members();

        if ($criterio_id) {
            $options = array("id" => $criterio_id);
            $criterio_info = $this->Rule_model->get_details($options)->row();
            if ($criterio_info) {
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $criterio_info;
				$formulario_criterio = $this->Forms_model->get_one($criterio_info->id_formulario);
				$view_data['flujo_formulario_criterio'] = $formulario_criterio->flujo;
				$this->load->view('relationship/criterio/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
		
	}
	
	function view_asignacion($asignacion_id = 0){
        if ($asignacion_id) {
            $asignacion_info = $this->Assignment_model->get_one($asignacion_id);
            if ($asignacion_info) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data["asignacion_info"] = $asignacion_info;
				$view_data["cliente"] = $this->Clients_model->get_one($asignacion_info->id_cliente);
				$view_data["proyecto"] = $this->Projects_model->get_one($asignacion_info->id_proyecto);
				$view_data["criterio"] = $this->Rule_model->get_one($asignacion_info->id_criterio);
				
				// TABLA DE SUBPROYECTO
				$combinaciones_sp = $this->Assignment_combinations_model->get_sp_rules_options_combinations_based($asignacion_id)->result();
				
				$html_sp = '';
				$html_sp .= '<div id="tabla_asignacion_sp" class="form-group">';
					$html_sp .= '<table class="table">';
					$html_sp .= '<thead>';
					$html_sp .= '<tr>';
						$html_sp .= '<th>'.lang("subproject_rule").'</th>';
						$html_sp .= '<th>'.lang("assignment_type").'</th>';
						$html_sp .= '<th>'.lang("target_subproject").'</th>';
						$html_sp .= '<th class="w10p">%</th>';
					$html_sp .= '</tr>';	
					$html_sp .= '</thead>';	
					
					$index = 1;
					foreach($combinaciones_sp as $row){
						$tr = '';
						
						$html_sp .= '<tbody id="row_'.$index.'">';
						$html_sp .= '<tr>';
						
							if($row->tipo_asignacion_sp == 'Total'){
								$criterio_sp = $row->criterio_sp?$row->criterio_sp:'-';
								$html_sp .= '<td>'.$criterio_sp.'</td>';
								$html_sp .= '<td>'.$row->tipo_asignacion_sp.'</td>';
								
								$id_subproyecto = $row->sp_destino;
								$subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
								$html_sp .= '<td>'.$subproyecto->nombre.'</td>';
								$html_sp .= '<td>100%</td>';
								
							}elseif($row->tipo_asignacion_sp == 'Porcentual'){
								$sp_decoded = json_decode($row->porcentajes_sp, true);
								$num_sp = count($sp_decoded);
								$array_porcentajes = array();
								foreach($sp_decoded as $id_subproyecto => $porc){
									$subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
									$array_porcentajes[] = array("sp" => $subproyecto->nombre, "porc" => $porc);
								}
								
								$html_sp .= '<td rowspan="'.$num_sp.'">'.$row->criterio_sp.'</td>';
								$html_sp .= '<td rowspan="'.$num_sp.'">'.$row->tipo_asignacion_sp.'</td>';
								
								$html_sp .= '<td>'.$array_porcentajes[0]["sp"].'</td>';
								$html_sp .= '<td>'.$array_porcentajes[0]["porc"].'%</td>';
								
								foreach($array_porcentajes as $key => $sp){
									if($key == 0){continue;}
									$tr .= '<tr>';
									$tr .= '<td>'.$sp["sp"].'</td>';
									$tr .= '<td>'.$sp["porc"].'%</td>';
									$tr .= '</tr>';
								}
							}
						
						$html_sp .= '</tr>';
						$html_sp .= $tr;
						$html_sp .= '</tbody>';
						$index++;
					}
					
					$html_sp .= '</table>';
				$html_sp .= '</div>';
				
				
				// TABLA DE PROCESO UNITARIO
				$combinaciones_pu = $this->Assignment_combinations_model->get_pu_rules_options_combinations_based($asignacion_id)->result();
				
				$html_pu = '';
				$html_pu .= '<div id="tabla_asignacion_pu" class="form-group">';
					$html_pu .= '<table class="table">';
					$html_pu .= '<thead>';
					$html_pu .= '<tr>';
						$html_pu .= '<th>'.lang("unit_processes_rule").'</th>';
						$html_pu .= '<th>'.lang("assignment_type").'</th>';
						$html_pu .= '<th>'.lang("target_unitary_process").'</th>';
						$html_pu .= '<th class="w10p">%</th>';
					$html_pu .= '</tr>';	
					$html_pu .= '</thead>';	
					
					$index = 1;
					foreach($combinaciones_pu as $row){
						$tr = '';
						
						$html_pu .= '<tbody id="row_'.$index.'">';
						$html_pu .= '<tr>';
						
							if($row->tipo_asignacion_pu == 'Total'){
								
								$criterio_pu = $row->criterio_pu?$row->criterio_pu:'-';
								$html_pu .= '<td>'.$criterio_pu.'</td>';
								$html_pu .= '<td>'.$row->tipo_asignacion_pu.'</td>';
								
								$id_proceso_unitario = $row->pu_destino;
								$proceso_unitario = $this->Unit_processes_model->get_one($id_proceso_unitario);
								$html_pu .= '<td>'.$proceso_unitario->nombre.'</td>';
								$html_pu .= '<td>100%</td>';
								
							}elseif($row->tipo_asignacion_pu == 'Porcentual'){
								$pu_decoded = json_decode($row->porcentajes_pu, true);
								$num_pu = count($pu_decoded);
								$array_porcentajes = array();
								foreach($pu_decoded as $id_proceso_unitario => $porc){
									$proceso_unitario = $this->Unit_processes_model->get_one($id_proceso_unitario);
									$array_porcentajes[] = array("pu" => $proceso_unitario->nombre, "porc" => $porc);
								}
								
								$html_pu .= '<td rowspan="'.$num_pu.'">'.$row->criterio_pu.'</td>';
								$html_pu .= '<td rowspan="'.$num_pu.'">'.$row->tipo_asignacion_pu.'</td>';
								
								$html_pu .= '<td>'.$array_porcentajes[0]["pu"].'</td>';
								$html_pu .= '<td>'.$array_porcentajes[0]["porc"].'%</td>';
								
								foreach($array_porcentajes as $key => $pu){
									if($key == 0){continue;}
									$tr .= '<tr>';
									$tr .= '<td>'.$pu["pu"].'</td>';
									$tr .= '<td>'.$pu["porc"].'%</td>';
									$tr .= '</tr>';
								}
							}
						
						$html_pu .= '</tr>';
						$html_pu .= $tr;
						$html_pu .= '</tbody>';
						$index++;
					}
					
					$html_pu .= '</table>';
				$html_pu .= '</div>';
				
				
				$view_data["html"] = $html_sp.$html_pu;
				$this->load->view('relationship/asignacion/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
	}
	
	function view_calculo($calculo_id = 0){		
        if ($calculo_id) {
			$calculo_info = $this->Calculation_model->get_details(array("id" => $calculo_id))->row();
            if ($calculo_info) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data["calculo"] = $calculo_info;
				
				$criterio = $this->Rule_model->get_one($calculo_info->id_criterio);
				$form_data = $this->Forms_model->get_one($criterio->id_formulario);
				
				if(($form_data->flujo == "Residuo") || ($form_data->flujo == "Consumo") || ($form_data->flujo == "No Aplica")){
					$unidades = json_decode($calculo_info->id_campo_unidad);
					$array_nombres_unidades = array();
					foreach($unidades as $key => $id_unidad){
						if($key == 0){
							$unidad_fija = json_decode($form_data->unidad);
							$array_nombres_unidades[$key] = $unidad_fija->nombre_unidad;
						}else{
							$unidad = $this->Fields_model->get_one($id_unidad);
							$array_nombres_unidades[$key] = $unidad->nombre;
						}
					}
					$view_data["campos_unidad"] = implode(', ', $array_nombres_unidades);
				}else{
					
					$unidades = json_decode($calculo_info->id_campo_unidad);
					$array_nombres_unidades = array();
					foreach($unidades as $key => $id_unidad){
						$unidad = $this->Fields_model->get_one($id_unidad);
						$array_nombres_unidades[$key] = $unidad->nombre;
					}
					$view_data["campos_unidad"] = implode(', ', $array_nombres_unidades);
				}
				$this->load->view('relationship/calculo/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
	}
	

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */