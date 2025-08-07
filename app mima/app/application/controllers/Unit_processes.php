<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Unit_processes extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	//private $id_modulo_administrador;
	
    function __construct() {
		
        parent::__construct();
		
        //check permission to access this module
        $this->init_permission_checker("client");
		$this->load->helper('directory');
		
		$this->id_modulo_cliente = 1;
		$this->id_submodulo_cliente = 2;
		//$this->id_modulo_administrador = 2;
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    /* load clients list view */

    function index() {
        //$this->access_only_allowed_members();
		
		if ($this->login_user->user_type === "staff") {
			
			// FILTRO FASES
			$array_fases[] = array("id" => "", "text" => "- ".lang("phase")." -");			
			$fases = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			foreach($fases as $fase){
				$array_fases[] = array("id" => $fase["id"], "text" => lang($fase["nombre_lang"]));
			}
			$view_data['fases_dropdown'] = json_encode($array_fases);
			
			$this->template->rander("unit_processes/index", $view_data);
			
        } else {
            //client's dashboard    
			
			$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			
            $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
			$id_proyecto = $this->session->project_context;
			$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
			$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
			
			$view_data['huellas'] = $huellas;
			$view_data['unidades_funcionales'] = $unidades_funcionales;	
			$view_data['page_type'] = "dashboard";
			$view_data['client_info'] = $client_info;
			$view_data['project_info'] = $this->Projects_model->get_one($id_proyecto);
			$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
			$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $id_proyecto)->result();
			
			//$view_data['General_settings_model'] = $this->General_settings_model;
			//$view_data['Projects_model'] = $this->Projects_model;
			//$view_data['Project_rel_footprints_model'] = $this->Project_rel_footprints_model;
			$view_data['Calculation_model'] = $this->Calculation_model;
			$view_data['Fields_model'] = $this->Fields_model;
			$view_data['Unity_model'] = $this->Unity_model;
			$view_data["Forms_model"] = $this->Forms_model;
			$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
			$view_data['Assignment_model'] = $this->Assignment_model;
			$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
			
			$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
			$view_data['Conversion_model'] = $this->Conversion_model;
			$view_data['Unit_processes_model'] = $this->Unit_processes_model;
			//$view_data['Form_rel_materiales_rel_categorias_model'] = $this->Form_rel_materiales_rel_categorias_model;
			
			$view_data["columnas"] = $this->Project_rel_footprints_model->get_footprints_of_project_json($id_proyecto);
			//var_dump($view_data["columnas"]);
			
			$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
			
			if($client_info->habilitado){
				$this->template->rander("unit_processes/client/index", $view_data);
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }

    }

    /* load client add/edit modal */

    function modal_form() {
	
        $this->access_only_allowed_members();
        $unit_process_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        $view_data['model_info'] = $this->Unit_processes_model->get_one($unit_process_id);
        $view_data["iconos"] = directory_map('./assets/images/unit-processes/');
		$view_data["fases_disponibles"] = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
		
		if($unit_process_id){
           $view_data["fases_pu"] = $this->Unit_processes_model->get_phases_of_pu($unit_process_id)->result_array();
        }
		
        $this->load->view('unit_processes/modal_form', $view_data);
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }


    function save() {
		
        $unit_process_id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
        $data_pu = array(
            "nombre" => $this->input->post('unit_process_name'),
			"icono" => $this->input->post('icono'),
			"color" => $this->input->post('color'),
			"descripcion" => $this->input->post('description')
        );
		
		$multiselect_fases = $this->input->post('fases');
		
		if($unit_process_id){
			
			// VALIDACION DE NOMBRE REPETIDO
			$titulo_pu = $this->input->post('unit_process_name');
			$pu_same_name = $this->Unit_processes_model->get_all_where(array("nombre" => $titulo_pu, "deleted" => 0));
			if($pu_same_name->num_rows() && $pu_same_name->row()->id != $unit_process_id){
				echo json_encode(array("success" => false, 'message' => lang('pu_title_warning')));
				exit();
			}
		
			$data_pu["modified_by"] = $this->login_user->id;
			$data_pu["modified"] = get_current_utc_time();
			
			//Edit fases_rel_pu
            $delete_fases_rel_pu = $this->Phase_rel_pu_model->delete_phases_rel_pu($unit_process_id);
			if($delete_fases_rel_pu){
                if($multiselect_fases){
                    foreach($multiselect_fases as $id_fase){
						$data_fase_rel_pu["id_fase"] = (int)$id_fase;
						$data_fase_rel_pu["id_proceso_unitario"] = $unit_process_id;
						$data_fase_rel_pu["created_by"] = $this->login_user->id;
						$data_fase_rel_pu["created"] = get_current_utc_time();
						$data_fase_rel_pu["modified_by"] = $this->login_user->id;
						$data_fase_rel_pu["modified"] = get_current_utc_time();
						$save_data_fase_rel_pu = $this->Phase_rel_pu_model->save($data_fase_rel_pu);
					}            
                }
            }
            $save_id = $this->Unit_processes_model->save($data_pu, $unit_process_id);	
			
		} else {
			
			// VALIDACION DE NOMBRE REPETIDO
			$titulo_pu = $this->input->post('unit_process_name');
			$pu_same_name = $this->Unit_processes_model->get_all_where(array("nombre" => $titulo_pu, "deleted" => 0))->result();
			if($pu_same_name){
				echo json_encode(array("success" => false, 'message' => lang('pu_title_warning')));
				exit();
			}
			
			$data_pu["created_by"] = $this->login_user->id;
			$data_pu["created"] = get_current_utc_time();
			
            $save_id = $this->Unit_processes_model->save($data_pu);
            //save relation
            if($multiselect_fases){
				foreach($multiselect_fases as $id_fase){
					$data_fase_rel_pu["id_fase"] =(int)$id_fase;
					$data_fase_rel_pu["id_proceso_unitario"] = $save_id;
					$data_fase_rel_pu["created_by"] = $this->login_user->id;
					$data_fase_rel_pu["created"] = get_current_utc_time();
					$save_data_fase_rel_pu = $this->Phase_rel_pu_model->save($data_fase_rel_pu);
				}			
			}
				
		}
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_admin_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Unit_processes_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_admin_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Unit_processes_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function admin_list_data() {

        $this->access_only_allowed_members();
        
        //$list_data = $this->Unit_processes_model->admin_list_data()->result();
		
		$options = array(
			"id_fase" => $this->input->post("id_fase")
		);
		
        $list_data = $this->Unit_processes_model->get_details($options)->result();
		
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_admin_make_row($data);
        }
        echo json_encode(array("data" => $result));
		
    }
	
	private function _admin_row_data($id) {
        $options = array(
            "id" => $id
        );
        $data = $this->Unit_processes_model->get_details($options)->row();
        return $this->_admin_make_row($data);
    }
	
	private function _admin_make_row($data){
		
		$fases_pu = $this->Unit_processes_model->get_phases_of_pu($data->id)->result_array();
		$html_fases = "";
		foreach($fases_pu as $fase){
			$html_fases .= $fase["nombre"]."<br>";
		}
		
		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		
		$row_data = array($data->id,
            modal_anchor(get_uri("unit_processes/admin_view/" . $data->id), $data->nombre, array("title" => lang('view_unit_process'))),
			"<img heigth='20' width='20' src='/assets/images/unit-processes/$data->icono' />"."&nbsp;".$data->icono, 
			$html_fases, $data->descripcion ? $tooltip_descripcion : "-"
        );

        $row_data[] = modal_anchor(get_uri("unit_processes/admin_view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_unit_process')))
					. modal_anchor(get_uri("unit_processes/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_unit_process'), "data-post-id" => $data->id))
                	. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_unit_process'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("unit_processes/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
		
	}
	
	function admin_view($id = 0) {

        if ($id) {
			
            $options = array("id" => $id);
            $model_info = $this->Unit_processes_model->get_details($options)->row();

            if ($model_info) { 
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $model_info;
				$view_data["fases"] = $this->Unit_processes_model->get_phases_of_pu($id)->result_array();
				
                $this->load->view('unit_processes/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function list_data($id_subproyecto, $id_uf, $start_date = NULL, $end_date = NULL) {
		
		/* $options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();
		$id_proyecto = $this->session->project_context;
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result(); */
		
        //$this->access_only_allowed_members();

		$id_proyecto = $this->session->project_context;
		$list_data = $this->Unit_processes_model->get_unit_process_details($id_proyecto)->result();
		$uf_data = $this->Functional_units_model->get_one($id_uf);
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $uf_data, $start_date, $end_date);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of client list  table */
    private function _row_data($id) {
        $options = array(
            "id" => $id
        );
        $data = $this->Functional_units_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data, $uf_data, $start_date = NULL, $end_date = NULL) {
		
		$id_subproyecto_uf = $uf_data->id_subproyecto;
		$id_uf = $uf_data->id;
		//$valor_uf = $uf_data->valor;
		
		$icono_pu = base_url("assets/images/unit-processes/".$data->icono);
		
		$html_pu = '<div class="milestone pull-left p0">';
			$html_pu .= '<h1><img src="'.$icono_pu.'" alt="..." height="37" width="37" class="mCS_img_loaded"></h1>';
			$html_pu .= '<div class="pt10 pb10 b-t label-success proceso_unitario"> '.$data->nombre.' </div>';
		$html_pu .= '</div>';
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_metodologia = $this->Projects_model->get_one($id_proyecto)->id_metodologia;
		//$columnas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		$valor_uf = get_functional_unit_value($id_cliente, $id_proyecto, $id_uf, $start_date, $end_date);
		
		$row_data[] = '<a href="#" class="details-control"><i class="fa fa-plus-circle font-16"></i></a>';
		//$row_data[] = $data->id;
		$row_data[] = $data->id_rel;
		$row_data[] = $html_pu;
		
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($id_cliente, $id_proyecto)->result();
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		
		//$id_uf = $unidad_funcional->id;
		//$nombre_uf = $unidad_funcional->nombre;
		//$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		
		$array_categorias = array();
		
		foreach($huellas as $huella){
			
			$nombre_huella = $huella->nombre;
			$id_huella = $huella->id;
			$total_huella = 0;
			
			// VALOR DE CONVERSION
			$id_tipo_unidad_origen = $huella->id_tipo_unidad;
			$id_unidad_origen = $huella->id_unidad;
			$fila_config_huella = $this->Module_footprint_units_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $id_tipo_unidad_origen,
					"deleted" => 0
				)
			);
			$id_unidad_destino = $fila_config_huella->id_unidad;
			//print_r($Conversion_model);
			$fila_conversion = $this->Conversion_model->get_one_where(
				array(
					"id_tipo_unidad" => $id_tipo_unidad_origen,
					"id_unidad_origen" => $id_unidad_origen,
					"id_unidad_destino" => $id_unidad_destino
				)
			);
			$valor_transformacion = $fila_conversion->transformacion;
			// FIN VALOR DE CONVERSION
			
			$id_pu = $data->id;
			$nombre_pu = $data->nombre;
			$total_pu = 0;
			
			foreach($criterios_calculos as $criterio_calculo){
				
				$total_criterio = 0;
				
				$id_criterio = $criterio_calculo->id_criterio;
				$id_formulario = $criterio_calculo->id_formulario;
				$id_material = $criterio_calculo->id_material;
				$id_categoria = $criterio_calculo->id_categoria;
				$id_subcategoria = $criterio_calculo->id_subcategoria;
				
				if($this->Categories_model->get_one($id_categoria)){
					$nombre_categoria = $this->Categories_model->get_one($id_categoria)->nombre;
					$alias_categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_categoria" => $id_categoria, "deleted" => 0));
					if($alias_categoria->alias){
						$nombre_categoria = $alias_categoria->alias;
					}
				} else {
					$nombre_categoria = "";
				}
				
				//$nombre_categoria = $this->Categories_model->get_one($id_categoria)?$this->Categories_model->get_one($id_categoria)->nombre:'';
				
				$id_subcategoria = $criterio_calculo->id_subcategoria;
				
				/*$id_campo_sp = $criterio_calculo->id_campo_sp;
				$id_campo_pu = $criterio_calculo->id_campo_pu;
				$id_campo_fc = $criterio_calculo->id_campo_fc;
				$criterio_fc = $criterio_calculo->criterio_fc;*/
				
				/*SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC */
				if(isset($criterio_calculo->tipo_by_criterio)){
					$j_datos = json_decode($criterio_calculo->tipo_by_criterio, true);

					if($j_datos["id_campo_sp"] == "1"){
						$id_campo_sp ="tipo_tratamiento";
					}else{
						$id_campo_sp = $criterio_calculo->id_campo_sp;
					}

					if($j_datos["id_campo_pu"] == "1"){
						$id_campo_pu ="tipo_tratamiento";
					}else{
						$id_campo_pu = $criterio_calculo->id_campo_pu;
					}

					if($j_datos["id_campo_fc"] == "1"){
						$id_campo_fc ="tipo_tratamiento";
					}else{
						$id_campo_fc = $criterio_calculo->id_campo_fc;
					}

				}else{
					$id_campo_sp = $criterio_calculo->id_campo_sp;
					$id_campo_pu = $criterio_calculo->id_campo_pu;
					$id_campo_fc = $criterio_calculo->id_campo_fc;
				}

				if($criterio_calculo->criterio_fc == "Disposición"){
					$value = $this->Tipo_tratamiento_model->get_one_where(array("nombre" => "Disposición", "deleted" => 0));
					$criterio_fc = $value->id;
				}else if($criterio_calculo->criterio_fc == "Reutilización"){
					$value = $this->Tipo_tratamiento_model->get_one_where(array("nombre" => "Reutilización", "deleted" => 0));
					$criterio_fc = $value->id;

				}else if($criterio_calculo->criterio_fc == "Reciclaje"){
					$value = $this->Tipo_tratamiento_model->get_one_where(array("nombre" => "Reciclaje", "deleted" => 0));
					$criterio_fc = $value->id;

				}else{
					$criterio_fc = $criterio_calculo->criterio_fc;
				}
				/*FIN SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC */
				
				$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
				
				/*
				// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
				// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
				$asignaciones_de_criterio = $this->Assignment_model->get_details(
					array("id_criterio" => $id_criterio, 
						"sp_destino" => $id_subproyecto_uf, 
						"pu_destino" => $id_pu
					)
				)->result();
				*/
				
				// NUEVA ASIGNACION
				// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
				$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
				/*
				// GUARDAR FILAS DE ASIGNACIONES EN UN ARREGLO BIDIMENSIONAL
				$array_asignaciones = array();
				foreach($asignaciones_de_criterio as $asignacion){
					$array_asignaciones[] = array(
						"criterio_sp" => $asignacion->criterio_sp,
						"criterio_pu" => $asignacion->criterio_pu
					);
				}
				*/
				
				// CONSULTAR CAMPOS UNIDAD DEL RA
				$array_unidades = array();
				$array_id_unidades = array();
				$array_id_tipo_unidades = array();
				
				foreach($ides_campo_unidad as $id_campo_unidad){
					
					if($id_campo_unidad == 0){
						$id_formulario = $criterio_calculo->id_formulario;
						$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
						$json_unidad_form = json_decode($form_data->unidad, true);

						$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
						$id_unidad = $json_unidad_form["unidad_id"];

						$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
						$array_unidades[] = $fila_unidad->nombre;
						$array_id_unidades[] = $id_unidad;
						$array_id_tipo_unidades[] = $id_tipo_unidad;
					}else{
						$fila_campo = $this->Fields_model->get_one($id_campo_unidad);
						$info_campo = $fila_campo->opciones;
						$info_campo = json_decode($info_campo, true);
						
						$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
						$id_unidad = $info_campo[0]["id_unidad"];
						
						$fila_unidad = $this->Unity_model->get_one($id_unidad);
						$array_unidades[] = $fila_unidad->nombre;
						$array_id_unidades[] = $id_unidad;
						$array_id_tipo_unidades[] = $id_tipo_unidad;
					}
					
				}
				
				// OBTENER UNIDAD FINAL
				// Se ampliaron unidades de cálculo 
				if(count($array_id_unidades) == 1){
					$id_unidad = $array_id_unidades[0];
				}elseif(count($array_id_unidades) == 2){
					
					if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
						$id_unidad = $array_id_unidades[1];
					}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
						$id_unidad = $array_id_unidades[0];
					}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
						$id_unidad = 5;
					}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
						$id_unidad = 6;
					}
					
				}elseif(count($array_id_unidades) == 3){
					
					if(
						in_array(18, $array_id_unidades) && 
						in_array(9, $array_id_unidades) && 
						in_array(1, $array_id_unidades)
					){
						$id_unidad = 5;
					}elseif(
						in_array(18, $array_id_unidades) && 
						in_array(9, $array_id_unidades) && 
						in_array(2, $array_id_unidades)
					){
						$id_unidad = 6;
					}else{
						
					}
					
				}else{
					
				}
				
				// CONSULTAR FC
				$fila_factor = $this->Characterization_factors_model->get_one_where(
					array(
						"id_metodologia" => $id_metodologia,
						"id_huella" => $id_huella,
						"id_material" => $id_material,
						"id_categoria" => $id_categoria,
						"id_subcategoria" => $id_subcategoria,
						"id_unidad" => $id_unidad,
						"deleted" => 0
					)
				);
				
				$valor_factor = 0;
				if($fila_factor->id){
					$valor_factor = $fila_factor->factor;
				}
				
				
				// UNA VEZ QUE YA TENGO FC PARA A NIVEL DE CRITERIO(RA) - CALCULO, RECORRO LOS ELEMENTOS ASOCIADOS
				$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
				foreach($elementos as $elemento){
					$total_elemento = 0;
					
					$datos_decoded = json_decode($elemento->datos, true);
					
					$mult = 1;
					foreach($ides_campo_unidad as $id_campo_unidad){
						if($id_campo_unidad == 0){
							$mult *= $datos_decoded["unidad_residuo"];
						}else{
							$mult *= $datos_decoded[$id_campo_unidad];
						}
					}
					
					// AL CALCULAR A NIVEL DE ELEMENTO, EL RESULTADO MULTIPLICARLO POR EL FC
					
					$total_elemento_interno = $mult * $valor_factor;
					
					// IF VALOR DE CAMPO DE CRITERIO SP EN CRITERIO = VALOR DE CRITERIO SP DE ARRAY DE ASIGNACIONES Y
					// VALOR DE CAMPO DE CITERIO PU EN CRITERIO = VALOR DE CRITERIO UF DE ARRAY DE ASIGNACIONES
					
					/*if($id_campo_sp && !$id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						
						foreach($array_asignaciones as $array_asignacion){
							if($array_asignacion["criterio_sp"] == $valor_campo_sp){
								$total_elemento += $total_elemento_interno;
							}
						}
					}*/
					
					if($id_campo_sp && !$id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								if($criterio_sp == $valor_campo_sp){
									$total_elemento += $total_elemento_interno;
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
								
								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}
								
								if($criterio_sp == $valor_campo_sp){
									$total_elemento += ($total_elemento_interno * $porcentaje_sp);
								}
								
							}
							
							
						}
					}
					
					/*if(!$id_campo_sp && $id_campo_pu){
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($array_asignaciones as $array_asignacion){
							if($array_asignacion["criterio_pu"] == $valor_campo_pu){
								$total_elemento += $total_elemento_interno;
							}
						}
					}*/
					
					if(!$id_campo_sp && $id_campo_pu){
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								if($criterio_pu == $valor_campo_pu){
									$total_elemento += $total_elemento_interno;
									//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
								}
								
							}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
								
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}
								
								if($criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_pu);
									//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
								}
								
							}
							
							
						}
					}
					
					/*if($id_campo_sp && $id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($array_asignaciones as $array_asignacion){
							if($array_asignacion["criterio_sp"] == $valor_campo_sp && $array_asignacion["criterio_pu"] == $valor_campo_pu){
								$total_elemento += $total_elemento_interno;
							}
						}
					}*/
					
					
					if($id_campo_sp && $id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += $total_elemento_interno;
								}
								
							}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_pu);
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
								
								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_sp);
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){

								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}

								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_sp * $porcentaje_pu);
								}
								
							}
							
						}
					}
					
					
					if(!$id_campo_sp && !$id_campo_pu){
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								//if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += $total_elemento_interno;
								//}
								
							}
						}
					}
					
					$total_criterio += $total_elemento;
					
				}// FIN ELEMENTO
				
				// CALCULO DE DOBLE CLICK - CATEGORIAS
				//if((($total_criterio/$valor_uf) * $valor_transformacion) > 0){
					$array_categorias[$nombre_categoria][$nombre_huella] += (($total_criterio/$valor_uf) * $valor_transformacion);
				//}
				$total_pu += $total_criterio;
				
			}// FIN CRITERIO-CALCULO

			$total_pu = $total_pu/$valor_uf;
			$total_pu *= $valor_transformacion;
			//$row_data[] = $total_pu;//.' f:'.$valor_f.' sp:'.$id_subproyecto_uf;
			
			$total_pu_final = to_number_project_format($total_pu,$id_proyecto);
			$row_data[] = $total_pu_final;
			
			
		}// FIN FOREACH HUELLA
		
		//var_dump("------------- PU:  ".$nombre_pu);
		//var_dump($data);
		//var_dump($array_categorias);
		// PROCESO LOS VALORES DEL DOBLE CLICK
		$array_categorias_formatted = array();
		foreach($array_categorias as $nombre_categoria => $array_huellas){
			if(array_sum($array_huellas) == 0){continue;}

			foreach($array_huellas as $nombre_huella => $valor){
				
				$valor_final = to_number_project_format($valor, $id_proyecto);
				$array_categorias_formatted[$nombre_categoria][$nombre_huella] = $valor_final;
			}
		}
		
		$row_data['num_huellas'] = count($huellas);
		$row_data['categorias'] = $array_categorias_formatted;

        //$row_data[] =  modal_anchor(get_uri("unit_processes/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_functional_unit'), "data-post-id" => $data->id));
        return $row_data;
    }

    function view($functional_unit_id = 0) {
        $this->access_only_allowed_members();

        if ($functional_unit_id) {
            $options = array("id" => $functional_unit_id);
            $functional_unit_info = $this->Functional_units_model->get_details($options)->row();
            if ($functional_unit_info) {
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $functional_unit_info;
				
				$cliente = $this->Clients_model->get_one($view_data['model_info']->id_cliente);
				$proyecto = $this->Projects_model->get_one($view_data['model_info']->id_proyecto);
				$subproyecto = $this->Subprojects_model->get_one($view_data['model_info']->id_subproyecto);
				
				$view_data["cliente"] = $cliente->company_name;
				$view_data["proyecto"] = $proyecto->title;
				$view_data["subproyecto"] = $subproyecto->nombre;
				
				$this->load->view('functional_units/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	
	

    /* only visible to client  */

    function users() {
        if ($this->login_user->user_type === "client") {
            $view_data['client_id'] = $this->login_user->client_id;
            $this->template->rander("clients/contacts/users", $view_data);
        }
    }
	
	function get_pdf(){
		
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");
		
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
		
		$imagenes_graficos = $this->input->post("imagenes_graficos");
		$view_data['imagenes_graficos'] = $imagenes_graficos;
			
		$view_data['huellas'] = $huellas;
		$view_data['unidades_funcionales'] = $unidades_funcionales;	
		$view_data['page_type'] = "dashboard";
		$view_data['client_info'] = $client_info;
		$view_data['project_info'] = $this->Projects_model->get_one($id_proyecto);
		$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
		$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $id_proyecto)->result();
		$view_data['Calculation_model'] = $this->Calculation_model;
		$view_data['Fields_model'] = $this->Fields_model;
		$view_data['Unity_model'] = $this->Unity_model;
		$view_data["Forms_model"] = $this->Forms_model;
		$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
		$view_data['Assignment_model'] = $this->Assignment_model;
		$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;	
		$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
		$view_data['Conversion_model'] = $this->Conversion_model;
		$view_data['Unit_processes_model'] = $this->Unit_processes_model;
		
		$array_columnas = array();
		$columnas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result_array();
		foreach($columnas as $columna){
			
			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
				"id_cliente" => $id_cliente, 
				"id_proyecto" => $id_proyecto, 
				"id_tipo_unidad" => $columna["id_tipo_unidad"], 
				"deleted" => 0
			))->id_unidad;

			$nombre = $columna["nombre"];
			$indicador = $columna["indicador"];
			$unidad = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
			$nombre_columna = $nombre . "<br>" . "(" . $unidad . " " . $indicador . ")";
			$array_columnas[] = $nombre_columna;
		}
		
		$array_unidades_funcionales = array();
		foreach($unidades_funcionales as $key => $unidad_funcional){
			$id_uf = $unidad_funcional->id;
			$list_data = $this->Unit_processes_model->get_unit_process_details($id_proyecto)->result();
			$uf_data = $this->Functional_units_model->get_one($id_uf);
			$result = array();
			foreach ($list_data as $data) {
				$result[] = $this->_make_row_pdf($data, $uf_data, $start_date, $end_date); 
			}
			$array_unidades_funcionales[$id_uf] = $result;
		}
		
		$view_data["columnas"] = $array_columnas;
		$view_data["array_unidades_funcionales"] = $array_unidades_funcionales;
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($client_info->sigla."_".$project_info->sigla."_".lang("unit_processes_pdf")."_".date('Y-m-d'));
        $this->pdf->SetSubject($client_info->sigla."_".$project_info->sigla."_".lang("unit_processes_pdf")."_".date('Y-m-d'));
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
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$html = $this->load->view('unit_processes/client/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $client_info->sigla."_".$project_info->sigla."_".lang("unit_processes_pdf")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");
		
		echo $pdf_file_name;
		
	}
	
	private function _make_row_pdf($data, $uf_data, $start_date, $end_date){
		
		$id_subproyecto_uf = $uf_data->id_subproyecto;
		$id_uf = $uf_data->id;
		//$valor_uf = $uf_data->valor;
		
		//$icono_pu = base_url("assets/images/unit-processes/".$data->icono);
		$icono_pu = "assets/images/unit-processes/".$data->icono;
		
		$html_pu = '<div style="text-align: center; float: left !important; padding: 0px; margin: 0px;">';
		$html_pu .= '<img src="'.$icono_pu.'" height="30" width="30" />';
		$html_pu .= '<br><span style="text-align: center;"> '.$data->nombre.' </span>';
		$html_pu .= '</div>';
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$valor_uf = get_functional_unit_value($id_cliente, $id_proyecto, $id_uf, $start_date, $end_date);
		
		$id_metodologia = $this->Projects_model->get_one($id_proyecto)->id_metodologia;
		//$columnas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		
		//$row_data[] = '<a href="#" class="details-control"><i class="fa fa-plus-circle font-16"></i></a>';
		//$row_data[] = $data->id;
		//$row_data[] = $data->id_rel;
		//$row_data[] = $data->nombre;
		$row_data[] = $html_pu;
		
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($id_cliente, $id_proyecto)->result();
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		
		//$id_uf = $unidad_funcional->id;
		//$nombre_uf = $unidad_funcional->nombre;
		//$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		
		$array_categorias = array();
		
		foreach($huellas as $huella){
			
			$nombre_huella = $huella->nombre;
			$id_huella = $huella->id;
			$total_huella = 0;
			
			// VALOR DE CONVERSION
			$id_tipo_unidad_origen = $huella->id_tipo_unidad;
			$id_unidad_origen = $huella->id_unidad;
			$fila_config_huella = $this->Module_footprint_units_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $id_tipo_unidad_origen,
					"deleted" => 0
				)
			);
			$id_unidad_destino = $fila_config_huella->id_unidad;
			//print_r($Conversion_model);
			$fila_conversion = $this->Conversion_model->get_one_where(
				array(
					"id_tipo_unidad" => $id_tipo_unidad_origen,
					"id_unidad_origen" => $id_unidad_origen,
					"id_unidad_destino" => $id_unidad_destino
				)
			);
			$valor_transformacion = $fila_conversion->transformacion;
			// FIN VALOR DE CONVERSION
			
			$id_pu = $data->id;
			$nombre_pu = $data->nombre;
			$total_pu = 0;
			
			foreach($criterios_calculos as $criterio_calculo){
				
				$total_criterio = 0;
				
				$id_criterio = $criterio_calculo->id_criterio;
				$id_formulario = $criterio_calculo->id_formulario;
				$id_material = $criterio_calculo->id_material;

				$id_categoria = $criterio_calculo->id_categoria;
				$id_subcategoria = $criterio_calculo->id_subcategoria;
				
				if($this->Categories_model->get_one($id_categoria)){
					$nombre_categoria = $this->Categories_model->get_one($id_categoria)->nombre;
					$alias_categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_categoria" => $id_categoria, "deleted" => 0));
					if($alias_categoria->alias){
						$nombre_categoria = $alias_categoria->alias;
					}
				} else {
					$nombre_categoria = "";
				}
				
				//$nombre_categoria = $this->Categories_model->get_one($id_categoria)?$this->Categories_model->get_one($id_categoria)->nombre:'';
				
				$id_subcategoria = $criterio_calculo->id_subcategoria;
				
				/*$id_campo_sp = $criterio_calculo->id_campo_sp;
				$id_campo_pu = $criterio_calculo->id_campo_pu;
				$id_campo_fc = $criterio_calculo->id_campo_fc;
				$criterio_fc = $criterio_calculo->criterio_fc;*/
				
				/*SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC */
				if(isset($criterio_calculo->tipo_by_criterio)){
					$j_datos = json_decode($criterio_calculo->tipo_by_criterio, true);

					if($j_datos["id_campo_sp"] == "1"){
						$id_campo_sp ="tipo_tratamiento";
					}else{
						$id_campo_sp = $criterio_calculo->id_campo_sp;
					}

					if($j_datos["id_campo_pu"] == "1"){
						$id_campo_pu ="tipo_tratamiento";
					}else{
						$id_campo_pu = $criterio_calculo->id_campo_pu;
					}

					if($j_datos["id_campo_fc"] == "1"){
						$id_campo_fc ="tipo_tratamiento";
					}else{
						$id_campo_fc = $criterio_calculo->id_campo_fc;
					}

				}else{
					$id_campo_sp = $criterio_calculo->id_campo_sp;
					$id_campo_pu = $criterio_calculo->id_campo_pu;
					$id_campo_fc = $criterio_calculo->id_campo_fc;
				}

				if($criterio_calculo->criterio_fc == "Disposición"){
					$value = $this->Tipo_tratamiento_model->get_one_where(array("nombre" => "Disposición", "deleted" => 0));
					$criterio_fc = $value->id;
				}else if($criterio_calculo->criterio_fc == "Reutilización"){
					$value = $this->Tipo_tratamiento_model->get_one_where(array("nombre" => "Reutilización", "deleted" => 0));
					$criterio_fc = $value->id;

				}else if($criterio_calculo->criterio_fc == "Reciclaje"){
					$value = $this->Tipo_tratamiento_model->get_one_where(array("nombre" => "Reciclaje", "deleted" => 0));
					$criterio_fc = $value->id;

				}else{
					$criterio_fc = $criterio_calculo->criterio_fc;
				}
				/*FIN SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC */
				
				$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
				
				/*
				// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
				// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
				$asignaciones_de_criterio = $this->Assignment_model->get_details(
					array("id_criterio" => $id_criterio, 
						"sp_destino" => $id_subproyecto_uf, 
						"pu_destino" => $id_pu
					)
				)->result();
				*/
				
				// NUEVA ASIGNACION
				// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
				$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
				/*
				// GUARDAR FILAS DE ASIGNACIONES EN UN ARREGLO BIDIMENSIONAL
				$array_asignaciones = array();
				foreach($asignaciones_de_criterio as $asignacion){
					$array_asignaciones[] = array(
						"criterio_sp" => $asignacion->criterio_sp,
						"criterio_pu" => $asignacion->criterio_pu
					);
				}
				*/
				
				// CONSULTAR CAMPOS UNIDAD DEL RA
				$array_unidades = array();
				$array_id_unidades = array();
				$array_id_tipo_unidades = array();
				
				foreach($ides_campo_unidad as $id_campo_unidad){
					
					if($id_campo_unidad == 0){
						$id_formulario = $criterio_calculo->id_formulario;
						$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
						$json_unidad_form = json_decode($form_data->unidad, true);

						$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
						$id_unidad = $json_unidad_form["unidad_id"];

						$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
						$array_unidades[] = $fila_unidad->nombre;
						$array_id_unidades[] = $id_unidad;
						$array_id_tipo_unidades[] = $id_tipo_unidad;
					}else{
						$fila_campo = $this->Fields_model->get_one($id_campo_unidad);
						$info_campo = $fila_campo->opciones;
						$info_campo = json_decode($info_campo, true);
						
						$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
						$id_unidad = $info_campo[0]["id_unidad"];
						
						$fila_unidad = $this->Unity_model->get_one($id_unidad);
						$array_unidades[] = $fila_unidad->nombre;
						$array_id_unidades[] = $id_unidad;
						$array_id_tipo_unidades[] = $id_tipo_unidad;
					}
					
				}
				
				// OBTENER UNIDAD FINAL
				// Se ampliaron unidades de cálculo 
				if(count($array_id_unidades) == 1){
					$id_unidad = $array_id_unidades[0];
				}elseif(count($array_id_unidades) == 2){
					
					if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
						$id_unidad = $array_id_unidades[1];
					}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
						$id_unidad = $array_id_unidades[0];
					}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
						$id_unidad = 5;
					}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
						$id_unidad = 6;
					}
					
				}elseif(count($array_id_unidades) == 3){
					
					if(
						in_array(18, $array_id_unidades) && 
						in_array(9, $array_id_unidades) && 
						in_array(1, $array_id_unidades)
					){
						$id_unidad = 5;
					}elseif(
						in_array(18, $array_id_unidades) && 
						in_array(9, $array_id_unidades) && 
						in_array(2, $array_id_unidades)
					){
						$id_unidad = 6;
					}else{
						
					}
					
				}else{
					
				}
				
				// CONSULTAR FC
				$fila_factor = $this->Characterization_factors_model->get_one_where(
					array(
						"id_metodologia" => $id_metodologia,
						"id_huella" => $id_huella,
						"id_material" => $id_material,
						"id_categoria" => $id_categoria,
						"id_subcategoria" => $id_subcategoria,
						"id_unidad" => $id_unidad,
						"deleted" => 0
					)
				);
				
				$valor_factor = 0;
				if($fila_factor->id){
					$valor_factor = $fila_factor->factor;
				}
				
				
				// UNA VEZ QUE YA TENGO FC PARA A NIVEL DE CRITERIO(RA) - CALCULO, RECORRO LOS ELEMENTOS ASOCIADOS
				$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $start_date, $end_date)->result();
				
				foreach($elementos as $elemento){
					$total_elemento = 0;
					
					$datos_decoded = json_decode($elemento->datos, true);
					
					$mult = 1;
					foreach($ides_campo_unidad as $id_campo_unidad){
						if($id_campo_unidad == 0){
							$mult *= $datos_decoded["unidad_residuo"];
						}else{
							$mult *= $datos_decoded[$id_campo_unidad];
						}
					}
					
					// AL CALCULAR A NIVEL DE ELEMENTO, EL RESULTADO MULTIPLICARLO POR EL FC
					
					$total_elemento_interno = $mult * $valor_factor;
					
					// IF VALOR DE CAMPO DE CRITERIO SP EN CRITERIO = VALOR DE CRITERIO SP DE ARRAY DE ASIGNACIONES Y
					// VALOR DE CAMPO DE CITERIO PU EN CRITERIO = VALOR DE CRITERIO UF DE ARRAY DE ASIGNACIONES
					
					/*if($id_campo_sp && !$id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						
						foreach($array_asignaciones as $array_asignacion){
							if($array_asignacion["criterio_sp"] == $valor_campo_sp){
								$total_elemento += $total_elemento_interno;
							}
						}
					}*/
					
					if($id_campo_sp && !$id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								if($criterio_sp == $valor_campo_sp){
									$total_elemento += $total_elemento_interno;
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
								
								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}
								
								if($criterio_sp == $valor_campo_sp){
									$total_elemento += ($total_elemento_interno * $porcentaje_sp);
								}
								
							}
							
							
						}
					}
					
					/*if(!$id_campo_sp && $id_campo_pu){
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($array_asignaciones as $array_asignacion){
							if($array_asignacion["criterio_pu"] == $valor_campo_pu){
								$total_elemento += $total_elemento_interno;
							}
						}
					}*/
					
					if(!$id_campo_sp && $id_campo_pu){
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								if($criterio_pu == $valor_campo_pu){
									$total_elemento += $total_elemento_interno;
									//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
								}
								
							}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
								
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}
								
								if($criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_pu);
									//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
								}
								
							}
							
							
						}
					}
					
					/*if($id_campo_sp && $id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($array_asignaciones as $array_asignacion){
							if($array_asignacion["criterio_sp"] == $valor_campo_sp && $array_asignacion["criterio_pu"] == $valor_campo_pu){
								$total_elemento += $total_elemento_interno;
							}
						}
					}*/
					
					
					if($id_campo_sp && $id_campo_pu){
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += $total_elemento_interno;
								}
								
							}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_pu);
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
								
								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_sp);
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){

								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									$porcentaje_sp = ($porcentaje_sp/100);
								}

								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									$porcentaje_pu = ($porcentaje_pu/100);
								}
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += ($total_elemento_interno * $porcentaje_sp * $porcentaje_pu);
								}
								
							}
							
						}
					}
					
					
					if(!$id_campo_sp && !$id_campo_pu){
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
								
								//if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$total_elemento += $total_elemento_interno;
								//}
								
							}
						}
					}
					
					$total_criterio += $total_elemento;
					
				}// FIN ELEMENTO
				
				// CALCULO DE DOBLE CLICK - CATEGORIAS
				//if((($total_criterio/$valor_uf) * $valor_transformacion) > 0){
					$array_categorias[$nombre_categoria][$nombre_huella] += (($total_criterio/$valor_uf) * $valor_transformacion);
				//}
				$total_pu += $total_criterio;
				
			}// FIN CRITERIO-CALCULO

			$total_pu = $total_pu/$valor_uf;
			$total_pu *= $valor_transformacion;
			//$row_data[] = $total_pu;//.' f:'.$valor_f.' sp:'.$id_subproyecto_uf;
			
			$total_pu_final = to_number_project_format($total_pu,$id_proyecto);
			$row_data[] = $total_pu_final;
			
			
		}// FIN FOREACH HUELLA
		
		// PROCESO LOS VALORES DEL DOBLE CLICK
		$array_categorias_formatted = array();
		foreach($array_categorias as $nombre_categoria => $array_huellas){
			if(array_sum($array_huellas) == 0){continue;}

			foreach($array_huellas as $nombre_huella => $valor){
				
				$valor_final = to_number_project_format($valor, $id_proyecto);
				$array_categorias_formatted[$nombre_categoria][$nombre_huella] = $valor_final;
			}
		}
		
		//$row_data['num_huellas'] = count($huellas);
		$row_data['categorias'] = $array_categorias_formatted;

        //$row_data[] =  modal_anchor(get_uri("unit_processes/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_functional_unit'), "data-post-id" => $data->id));
        return $row_data;
		
	}
	
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}
	
	// Muestra el mismo contenido que al entrar al módulo pero con los datos filtrados por rango de fechas.
	function get_unit_processes(){
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;
		
		$options = array("id" => $this->login_user->client_id);
		$client_info = $this->Clients_model->get_details($options)->row();
		$id_proyecto = $this->session->project_context;
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($id_proyecto)->result();
		$unidades_funcionales = $this->Functional_units_model->get_details(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto))->result();
		
		$view_data['huellas'] = $huellas;
		$view_data['unidades_funcionales'] = $unidades_funcionales;	
		$view_data['page_type'] = "dashboard";
		$view_data['client_info'] = $client_info;
		$view_data['project_info'] = $this->Projects_model->get_one($id_proyecto);
		$view_data['procesos_unitarios'] = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
		$view_data['criterios_calculos'] = $this->Unit_processes_model->get_rules_calculations_of_project($client_info->id, $id_proyecto)->result();
		
		$view_data['Calculation_model'] = $this->Calculation_model;
		$view_data['Fields_model'] = $this->Fields_model;
		$view_data['Unity_model'] = $this->Unity_model;
		$view_data["Forms_model"] = $this->Forms_model;
		$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
		$view_data['Assignment_model'] = $this->Assignment_model;
		$view_data['Assignment_combinations_model'] = $this->Assignment_combinations_model;
		
		$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
		$view_data['Conversion_model'] = $this->Conversion_model;
		$view_data['Unit_processes_model'] = $this->Unit_processes_model;
		
		$view_data["columnas"] = $this->Project_rel_footprints_model->get_footprints_of_project_json($id_proyecto);
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		
		if($client_info->habilitado){
			echo $this->load->view("unit_processes/client/unit_processes_by_date", $view_data, TRUE);
		}else{
			$this->session->sess_destroy();
			redirect('signin/index/disabled');
		}
		
	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */