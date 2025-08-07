<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KPI_Charts extends MY_Controller {
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");		
    }

    function index() {
		
		// Filtro Cliente
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// Filtro Fase
		$array_fases[] = array("id" => "", "text" => "- ".lang("phase")." -");
		$fases = $this->Phases_model->get_dropdown_list(array("nombre"), 'id');
		foreach($fases as $id => $nombre_fase){
			if($id == 2 || $id == 3){
				$array_fases[] = array("id" => $id, "text" => $nombre_fase);
			}
		}
		$view_data['fases_dropdown'] = json_encode($array_fases);
		
		// Filtro Proyecto
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// Filtro Item
		$array_items[] = array("id" => "", "text" => "- ".lang("kpi_item")." -");
		$array_items[] = array("id" => "materials_and_waste", "text" => lang("materials_and_waste"));
		$array_items[] = array("id" => "emissions", "text" => lang("emissions"));
		$array_items[] = array("id" => "energy", "text" => lang("energy"));
		$array_items[] = array("id" => "water", "text" => lang("water"));
		$array_items[] = array("id" => "social", "text" => lang("social"));
		$view_data['items_dropdown'] = json_encode($array_items);
		
        $this->template->rander("kpi_charts/index", $view_data);
    }
	
	function modal_form() {

        $id_kpi_estructura_grafico = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));
		
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
		
		if($id_kpi_estructura_grafico){
			
			$opciones_kpi_estructura_grafico = array("id" => $id_kpi_estructura_grafico);
			$kpi_estructura_grafico = $this->KPI_Charts_structure_model->get_details($opciones_kpi_estructura_grafico)->row();
			$view_data["model_info"] = $kpi_estructura_grafico;
			
			// Series
			$series = json_decode($kpi_estructura_grafico->series, TRUE);
			$view_data["series"] = $series;
			
			$id_tipo_unidad = 0;
			if($kpi_estructura_grafico->item == "materials_and_waste" || $kpi_estructura_grafico->item == "emissions"){
				$id_tipo_unidad = 1; // Masa
			} elseif($kpi_estructura_grafico->item == "energy"){
				$id_tipo_unidad = 4; // Energía
			} elseif($kpi_estructura_grafico->item == "water"){
				$id_tipo_unidad = 2; // Volumen
			} elseif($kpi_estructura_grafico->item == "social"){
				$id_tipo_unidad = 9; // Unidad
			}

			//$unidad = $this->Unity_model->get_one($id_unidad)->nombre;
			$tipo_unidad = $this->Unity_type_model->get_one($id_tipo_unidad)->nombre;
			$view_data["id_tipo_unidad"] = $id_tipo_unidad;
			//$view_data["unidad"] = $unidad;
			$view_data["tipo_unidad"] = $tipo_unidad;
			
			$view_data["KPI_Charts_controller"] = $this;

		} 
		
        $this->load->view('kpi_charts/modal_form', $view_data);
    }
	
	
	function save() {
		
		$id_kpi_estructura_grafico = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));

		// $id_kpi_estructura_grafico debería venir siempre
		if($id_kpi_estructura_grafico){

			$kpi_estructura_grafico = $this->KPI_Charts_structure_model->get_one($id_kpi_estructura_grafico);
			
			if($kpi_estructura_grafico->series){

				$series = json_decode($kpi_estructura_grafico->series, TRUE);
				$data_series = array();
				foreach($series as $nombre_serie => $valor_serie){
					$data_series[$nombre_serie] = $this->input->post($nombre_serie);
				}
				$data = array(
					"series" => json_encode($data_series)
				);
				$save_id = $this->KPI_Charts_structure_model->save($data, $kpi_estructura_grafico->id);
			
			}

		} else {
			
			$data_kpi_report["created_by"] = $this->login_user->id;
			$data_kpi_report["created"] = get_current_utc_time();
			$save_id = $this->KPI_Charts_structure_model->save($data_kpi_report);
			
		}
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function list_data() {
		
		$options = array(
			"id_cliente" => $this->input->post('id_cliente'),
			"id_fase" => $this->input->post('id_fase'),
			"id_proyecto" => $this->input->post('id_proyecto'),
			"item" => $this->input->post('item'),
		);
		
        $list_data = $this->KPI_Charts_structure_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
			if($data->submodulo_grafico == "charts_by_project"){
				$result[] = $this->_make_row($data);
			}
        }
		
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->KPI_Charts_structure_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$row_data[] = $data->id;
		$row_data[] = $data->nombre_cliente;
		$row_data[] = $data->nombre_fase;
		$row_data[] = $data->nombre_proyecto;
		$row_data[] = lang($data->item);
		$row_data[] = lang($data->subitem);
		$row_data[] = lang($data->tipo_grafico);
		
		$row_data[] = modal_anchor(get_uri("KPI_Charts/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_kpi_chart'), "data-post-id" => $data->id))
					. modal_anchor(get_uri("KPI_Charts/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_kpi_chart'), "data-post-id" => $data->id));
		
        return $row_data;
    }
	
	function view($id_kpi_estructura_grafico = 0) {

        if ($id_kpi_estructura_grafico) {
            
			$options = array("id" => $id_kpi_estructura_grafico);
            $info_kpi_estructura_grafico = $this->KPI_Charts_structure_model->get_details($options)->row();
            
			if ($info_kpi_estructura_grafico) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_kpi_estructura_grafico;
				
				// Series
				$array_series = array();
				$series = json_decode($info_kpi_estructura_grafico->series, TRUE);
				$view_data["series"] = $series;
				
				$id_tipo_unidad = 0;
				if($info_kpi_estructura_grafico->item == "materials_and_waste" || $info_kpi_estructura_grafico->item == "emissions"){
					$id_tipo_unidad = 1; // Masa
				} elseif($info_kpi_estructura_grafico->item == "energy"){
					$id_tipo_unidad = 4; // Energía
				} elseif($info_kpi_estructura_grafico->item == "water"){
					$id_tipo_unidad = 2; // Volumen
				} elseif($info_kpi_estructura_grafico->item == "social"){
					$id_tipo_unidad = 9; // Unidad
				}
					
				//$unidad = $this->Unity_model->get_one($id_unidad)->nombre;
				$tipo_unidad = $this->Unity_type_model->get_one($id_tipo_unidad)->nombre;
				
				//var_dump($series);
				
				foreach($series as $nombre_serie => $valor_serie){
					$nombre_valor = $this->KPI_Values_model->get_one($valor_serie)->nombre_valor;
					
					$array_series[$nombre_serie] = array("valor" => ($valor_serie) ? $nombre_valor : "-", "tipo_unidad" => $tipo_unidad);
					
					//$array_series[$nombre_serie] = $valor_serie ? $nombre_valor : "-";
				}
				$view_data["series"] = $array_series;

				$this->load->view('kpi_charts/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function get_array_values_of_series($opciones_valores = array()){

		$opciones_valores["deleted"] = 0;
		$valores = $this->KPI_Values_model->get_all_where($opciones_valores)->result();
		
		$array_valores = array("" => "-");
		foreach($valores as $valor){
			$array_valores[$valor->id] = $valor->nombre_valor;
		}
		
		return $array_valores;
		
	}
	
}