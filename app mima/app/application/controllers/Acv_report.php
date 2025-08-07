<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
	

/*
 * Controlador para Reporte de ACV
 * 
 * Este archivo maneja los metodos utilizados para crear reportes de Analisis de Ciclo de Vida
 * @author Mimasoft
 * @version 1.0
 * @package mimasoft
 */

/*
 * Reporte ACV
 * 
 * @author Mimasoft
 * @version 1.0
 * @package mimasoft
 */
class Acv_report extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		//$this->load->helper("pdf");
    }

	/*
	* Establece una serie de variables vacias para redireccionar a la vista inicial del módulo
	*
	* @access public
	* @return boolean retorna la renderización de la vista de inicio
	*/
    function index() {
		
		$this->access_only_allowed_members();
		$access_info = $this->get_access_info("invoice");
		
		$view_data['label_column'] = "col-md-2";
        $view_data['field_column'] = "col-md-10";
		
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["proyectos"] = array("" => "-");
		$view_data["subproyectos"] = array("" => "-");
		$view_data["unidades_funcionales"] = array("" => "-");
		
        $this->template->rander("acv_report/index", $view_data);
    }
	
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="col-md-2">'.lang('project').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	function get_subprojects_of_projects(){
		
		$id_proyecto = $this->input->post('id_proyecto');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$subproyectos_de_proyecto = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $id_proyecto));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="subproject" class="col-md-2">'.lang('subproject').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("subproject", array("" => "-") + $subproyectos_de_proyecto, "", "id='subproject' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	function get_functional_units_of_subproject(){
		
		$id_subproyecto = $this->input->post("id_subproyecto");
		$unidades_funcionales_subproyecto = $this->Functional_units_model->get_dropdown_list(array("nombre"), "id", array("id_subproyecto" => $id_subproyecto));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="functional_unit" class="col-md-2">'.lang('functional_unit').'</label>';
		
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("functional_unit", array("" => "-") + $unidades_funcionales_subproyecto, "", "id='functional_unit' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;

	}
	
	function get_acv_report(){

		$data_acv = array(
			'id_cliente' => $this->input->post('id_cliente'),
			'id_proyecto' => $this->input->post('id_proyecto'),
			'id_subproyecto' => $this->input->post('id_subproyecto'),
			'id_unidad_funcional' => $this->input->post('id_unidad_funcional'),
			'start_date' => $this->input->post('start_date'),
			'end_date' => $this->input->post('end_date')
		);
		
		$html = '';
		$html .= '<div class="page-title clearfix">';
			$html .= '<h4>'.lang('acv_report').'</h4>';
		$html .= '</div>';
		$html .= '<div class="panel-body">';
			$html .= '<div class="form-group">';	
				$html .= $this->list_unit_types($this->input->post('id_cliente'), $this->input->post('id_proyecto'));
			$html .= '</div>';
			$html .= '<div class="form-group">';	
				$html .= $this->list_company_info($this->input->post('id_cliente'), $this->input->post('id_proyecto'));
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->list_project_info($this->input->post('id_cliente'), $this->input->post('id_proyecto'));
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->list_unit_processes_info($this->input->post('id_cliente'), $this->input->post('id_proyecto'));
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->list_categories_and_materials($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';	
				$html .= $this->list_unit_processes_flows($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';	
				$html .= $this->list_assignment($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->list_impact_categories($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';	
				$html .= $this->list_characterization_model($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->list_calculation_of_category_indicators($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';	
				$html .= $this->list_results_by_impact_category($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';	
				$html .= $this->list_critical_points($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->list_chart_results_of_environmental_impact($data_acv);
			$html .= '</div>';
			$html .= '<div class="form-group">';
				$html .= $this->acv_report_content($data_acv);
			$html .= '</div>';
			
		$html .= '</div>';	

		echo $html;
		
	}
	
	/* funcion para armar el contenido del reporte, para utilizarlo en function get_acv_report()*/
	function acv_report_content($data_acv = array()){
		
		
		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$nombre_uf = $unidad_funcional->nombre;
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		//$valor_uf = $unidad_funcional->valor;
		$valor_uf = get_functional_unit_value($data_acv["id_cliente"], $data_acv["id_proyecto"], $unidad_funcional->id, $data_acv["start_date"], $data_acv["end_date"]);
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($data_acv["id_proyecto"])->result_array();
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		$client_info = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$project_info = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		$id_metodologia = $project_info->id_metodologia;
		
		$html = '';
		$html .= '<div class="row">';
		
			$html .= '<div class="page-title clearfix" style="background-color:#5bc0de; margin-bottom: 15px;">';
				$html .= '<h4>'.lang('impacts_by_category').'</h4>';
			$html .= '</div>';
			
			$html .= '<div id="graficos_procesos">';
				foreach($huellas as $huella){
					//$nombre_unidad_huella = $this->Unity_model->get_one($huella->id_unidad)->nombre;
					$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
						"id_cliente" => $data_acv["id_cliente"], 
						"id_proyecto" => $data_acv["id_proyecto"], 
						"id_tipo_unidad" => $huella->id_tipo_unidad, 
						"deleted" => 0
					))->id_unidad;
					
					$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
					
					$html .= '<div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">';
						$html .= '<div class="panel panel-default">';
							$html .= '<div class="page-title clearfix panel-success">';
								$html .= '<div class="pt10 pb10 text-center">'. $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')</div>';
							$html .= '</div>';
							$html .= '<div class="panel-body">';
								$html .= '<div id="grafico_'.$huella->id.'-uf_'.$data_acv["id_unidad_funcional"].'" style="height: 240px;"></div>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				}	
			$html .= '</div>';		
		
		$html .= '</div>';
		
		$html .= '<script type="text/javascript">';
			
			foreach($huellas as $huella){
				
				$id_huella = $huella->id;
				$total_huella = 0;
				$array_valores_pu = array();
				$array_colores_pu = array();
				
				$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
					"id_cliente" => $data_acv["id_cliente"], 
					"id_proyecto" => $data_acv["id_proyecto"], 
					"id_tipo_unidad" => $huella->id_tipo_unidad, 
					"deleted" => 0
				))->id_unidad;
				
				$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
				
				foreach($procesos_unitarios as $pu){
					
					$id_pu = $pu["id"];
					$nombre_pu = $pu["nombre"];
					$total_pu = 0; 
					
					foreach($criterios_calculos as $criterio_calculo){
						
						$id_criterio = $criterio_calculo->id_criterio;
						$id_formulario = $criterio_calculo->id_formulario;
						$id_material = $criterio_calculo->id_material;
						$id_categoria = $criterio_calculo->id_categoria;
						$id_subcategoria = $criterio_calculo->id_subcategoria;
						
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
						$criterio_fc = $criterio_calculo->criterio_fc;
						$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
						
						// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
						// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
						/*$asignaciones_de_criterio = $this->Assignment_model->get_details(
							array("id_criterio" => $id_criterio, 
							"sp_destino" => $id_subproyecto_uf, 
							"pu_destino" => $id_pu
							)
						)->result();*/
						
						// NUEVA ASIGNACION
						// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
						$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
						
						// GUARDAR FILAS DE ASIGNACIONES EN UN ARREGLO BIDIMENSIONAL
						/*$array_asignaciones = array();
						foreach($asignaciones_de_criterio as $asignacion){
							$array_asignaciones[] = array(
								"criterio_sp" => $asignacion->criterio_sp,
								"criterio_pu" => $asignacion->criterio_pu
							);
						}*/
						
						// CONSULTAR CAMPOS UNIDAD DEL RA
						$array_unidades = array();
						$array_id_unidades = array();
						$array_id_tipo_unidades = array();
						
						foreach($ides_campo_unidad as $id_campo_unidad){
							
							if($id_campo_unidad == 0){
								$id_formulario = $criterio_calculo->id_formulario;
								$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
								$json_unidad_form = json_decode($form_data->unidad,true);
								
								$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
								$id_unidad = $json_unidad_form["unidad_id"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
								$array_unidades[] = $fila_unidad->nombre;
								$array_id_unidades[] = $id_unidad;
								$array_id_tipo_unidades[] = $id_tipo_unidad;
							}else{
								$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
								$info_campo = $fila_campo->opciones;
								$info_campo = json_decode($info_campo, true);
								
								$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
								$id_unidad = $info_campo[0]["id_unidad"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
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
						
						$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($data_acv["id_proyecto"], $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();

						foreach($elementos as $elemento){
							
							$total_elemento = 0;
							$datos_decoded = json_decode($elemento->datos, true);
							
							if($datos_decoded["fecha"]){
							
								if(($datos_decoded["fecha"] >= $data_acv["start_date"]) && ($datos_decoded["fecha"] <= $data_acv["end_date"])){
								
									$mult = 1;
									foreach($ides_campo_unidad as $id_campo_unidad){
										if($id_campo_unidad == 0){
											$mult *= $datos_decoded["unidad_residuo"];
										}else{
											$mult *= $datos_decoded[$id_campo_unidad];
										}
									}
		
									$total_elemento = $mult * $valor_factor;
									
									/*if($id_campo_sp && !$id_campo_pu){
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										
										foreach($array_asignaciones as $array_asignacion){
											if($array_asignacion["criterio_sp"] == $valor_campo_sp){
												$total_pu += $total_elemento;
											}
										}
									}*/
									
									if($id_campo_sp && !$id_campo_pu){
								
										if($id_campo_sp == "tipo_tratamiento"){
											$value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
											$valor_campo_sp = $value->nombre;
										}else{
											$valor_campo_sp = $datos_decoded[$id_campo_sp];
										}
										
										//$valor_campo_sp = $datos_decoded[$id_campo_sp];
										
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
													$total_pu += $total_elemento;
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
												
												$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
												$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
												if($porcentaje_sp != 0){
													$porcentaje_sp = ($porcentaje_sp/100);
												}
												
												if($criterio_sp == $valor_campo_sp){
													$total_pu += ($total_elemento * $porcentaje_sp);
												}
											}
										}
									}
									
									/*if(!$id_campo_sp && $id_campo_pu){
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
		
										foreach($array_asignaciones as $array_asignacion){
											if($array_asignacion["criterio_pu"] == $valor_campo_pu){
												$total_pu += $total_elemento;
											}
										}
									}*/
									
									
									if(!$id_campo_sp && $id_campo_pu){
								
										if($id_campo_pu == "tipo_tratamiento"){
											$value = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
											$valor_campo_pu = $value->nombre;
										}else{
											$valor_campo_pu = $datos_decoded[$id_campo_pu];
										}
										//$valor_campo_pu = $datos_decoded[$id_campo_pu];
										
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
													$total_pu += $total_elemento;
												}
												
											}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
												
												$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
												$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
												if($porcentaje_pu != 0){
													$porcentaje_pu = ($porcentaje_pu/100);
												}
												
												if($criterio_pu == $valor_campo_pu){
													$total_pu += ($total_elemento * $porcentaje_pu);
												}
												
											}
											
											
										}
									}
									
									/*if($id_campo_sp && $id_campo_pu){
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
										
										foreach($array_asignaciones as $array_asignacion){
											if($array_asignacion["criterio_sp"] == $valor_campo_sp && $array_asignacion["criterio_pu"] == $valor_campo_pu){
												$total_pu += $total_elemento;
											}
										}
									}*/
									
									if($id_campo_sp && $id_campo_pu){
										
										if(($id_campo_pu == "tipo_tratamiento")&&($id_campo_sp == "tipo_tratamiento")){
											
											$value_sp = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
											$valor_campo_sp = $value_sp->nombre;
											$value_pu = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
											$valor_campo_pu = $value_pu->nombre;
											
										}else{
											$valor_campo_sp = $datos_decoded[$id_campo_sp];
											$valor_campo_pu = $datos_decoded[$id_campo_pu];
										}
										/*
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
										*/
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
													$total_pu += $total_elemento;
												}
												
											}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
												$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
												$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
												if($porcentaje_pu != 0){
													$porcentaje_pu = ($porcentaje_pu/100);
												}
												
												if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
													$total_pu += ($total_elemento * $porcentaje_pu);
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
												
												$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
												$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
												if($porcentaje_sp != 0){
													$porcentaje_sp = ($porcentaje_sp/100);
												}
												
												if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
													$total_pu += ($total_elemento * $porcentaje_sp);
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){
												
												//echo $porcentajes_sp.'|'.$porcentajes_pu.'<br>';
		
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
													$total_pu += ($total_elemento * $porcentaje_sp * $porcentaje_pu);
												}
											}
										}
									}
									
									if(!$id_campo_sp && !$id_campo_pu){
										//var_dump($asignaciones_de_criterio);
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
												$total_pu += $total_elemento;
											}
										}
									}
									
									
									
								
								
								
								}// END if(($datos_decoded >= $data_acv["start_date"]) && ($datos_decoded <= $data_acv["end_date"]))

							} //END if($datos_decoded["fecha"])


						}

					}

					$total_pu = $total_pu/$valor_uf;
					$total_huella += $total_pu;
					$array_valores_pu[] = array("nombre_pu" => $nombre_pu, "total_pu" => $total_pu);
					
					$proceso_unitario = $this->Unit_processes_model->get_one($id_pu);
					
					array_push($array_colores_pu, ($proceso_unitario->color) ? $proceso_unitario->color : "#00b393");
					//$array_colores_pu[] = $proceso_unitario->color;
				}
				
				$array_data = array();
				foreach($array_valores_pu as $dato_pu){
					if($dato_pu["total_pu"] == 0){
						$porc_pu = 0;
					}else{
						$porc_pu = ($dato_pu["total_pu"]*100)/$total_huella;
					}
					
					$array_data[] = array("name" => $dato_pu["nombre_pu"], "y" => $porc_pu);
				}
				

				$html .= '$("#grafico_'.$huella->id.'-uf_'.$data_acv["id_unidad_funcional"].'").highcharts({';
				$html .=		'chart: {';
				$html .=		   'plotBackgroundColor: null,';
				$html .=		   'plotBorderWidth: null,';
				$html .=		   'plotShadow: false,';
				$html .=		   'type: "pie",';
				$html .=		   'events: {
									   load: function() {
										   if (this.options.chart.forExport) {
											   Highcharts.each(this.series, function (series) {
												   series.update({
													   dataLabels: {
														   enabled: true,
														}
													}, false);
												});
												this.redraw();
											}
										}
									}';
				$html .=		'},';
				$html .=		'title: {';
				$html .=		'   text: "",';
				$html .=		'},';
				
				
				//$name = 'Reporte_'.$datos_info_projecto[0]["sigla_cliente"].'_'.$datos_info_projecto[0]["sigla_projecto"].'_'.date('d-m-Y');
				
				//$nombre_grafico = lang("graphic").'_'.$client_info->sigla.'_'.$project_info->sigla.' '.$huella->nombre.'('.$nombre_unidad_huella.' '.$huella->indicador.') ';
				$nombre_grafico = $client_info->sigla.'_'.$project_info->sigla.'_ACV_'.$huella->abreviatura.'_'.$nombre_unidad_huella.'_'.date("Y-m-d");
				
				$html .= 	'exporting: {';
				$html .=       'filename: "'.$nombre_grafico.'",';
				$html .= 		'buttons: {';
				$html .= 			'contextButton: {';
				$html .= 				'menuItems: [{';
				$html .= 					'text: "'.lang('export_to_png').'",';
				$html .= 					'onclick: function() {';
				$html .= 						'this.exportChart(
													null,{
														plotOptions: {
															pie: {
																dataLabels: {
																	enabled: true,
																},
																showInLegend: false
															},
														}
													}
												);';
				$html .= 					'},';
				$html .= 					'separator: false';
				$html .= 				'}]';
				$html .= 			'}';
				$html .= 		'},';
				
				$html .= 	'chartOptions: {
							   series: [{
								 dataLabels: {
								   style: {
									 fontSize: "15px",
									 fontWeight: "normal",
									 //width: "120px"
								   }
								 }
							   }]
							},
							//sourceWidth: 800,
       					   // sourceHeight: 400,
						   sourceWidth: 800,
       					    sourceHeight: 200,
							scale: 3,';
				
				$html .= 	'},';
				
				
				$html .=		'credits: {';
				$html .=		'   enabled: false';
				$html .=		'},';
				$html .=		'tooltip: {';
				//$html .=		'   pointFormat: "{series.name}: <b>{point.y}%</b>"';
				$html .=		'   pointFormat: "{series.name}: <b>{point.percentage:.0f}%</b>"';
				$html .=		'},';
				$html .=		'plotOptions: {';
				$html .=		'   pie: {';
				//$html .=	'		//size: 80,';
				$html .=	'		allowPointSelect: true,';
				$html .=	'		cursor: "pointer",';
				$html .=	'		dataLabels: {';
				$html .=	'		 enabled: false,';
				$html .=	'		 format: "<b>{point.name}</b>: {point.percentage:.1f} %",';
				$html .=	'		 style: {';
				$html .=	'		  color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",';
				$html .=	'		  fontSize: "9px",';
				$html .=	'		  distance: -30';
				$html .=	'		 },';
				$html .=	'		 crop: false';
				$html .=	'		},';
				$html .=	'		showInLegend: true';
				$html .=	'	   }';
				$html .=	'	},';
				$html .=	'	legend: {';
				$html .=	'	   enabled: true,';
				$html .=	'	   itemStyle:{';
				$html .=	'		fontSize: "9px"';
				$html .=	'	   }';
				$html .=	'	},';
				//$html .=	'	colors: ["#0555FA", "#41B9E6", "#E61400", "#FF0F64", "#008C5A", "#55BE5A", "#FFD700"],';
				$html .=    '   colors:'. json_encode($array_colores_pu).',';
				$html .=	'	series: [{';
				$html .=	'	   name: "Porcentaje",';
				$html .=	'	   colorByPoint: true,';

				
				//$html .= 'data: []';
				$html .=	'	  data:'. json_encode($array_data).'';
				//$html .=	'	  data: [{"name":"test","y":"1"},{"name":"test 2","y":"2"}]';
				$html .=	'	}],';
				$html .=	' });'; 

				$count = 0;
				foreach($array_data as $data){
					if($data["y"] != 0){
						$count++;
					}
				}
				
				if($count == 0){
					$html .= '$("#grafico_'.$huella->id.'-uf_'.$data_acv["id_unidad_funcional"].'").html("<strong>'.lang("no_information_available").'</strong>").css({"text-align":"center", "vertical-align":"middle", "display":"table-cell"});';
				} 
			
			}
		
		$html .= 'adaptarAltura();';
		$html .= 'function adaptarAltura(){';	
		$html .= 	'var maxHeight = Math.max.apply(null, $("div.page-title.clearfix.panel-success").map(function (){';
		$html .= 		'return $(this).height();';
		$html .= 	'}).get());';
		$html .= 	'$("div.page-title.clearfix.panel-success").height(maxHeight);';		
		$html .= '}';
			
		$html .= '</script>';
		
		return $html;
		
	}
	
	function list_unit_types($id_cliente, $id_proyecto){
		
		$array_unidades_huellas = array();
		$array_unidades_reporte = array();
		$unidades_huellas = $this->Module_footprint_units_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
		$unidades_reporte = $this->Reports_units_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		foreach ($unidades_huellas as $uh){
			$tipo_unidad = $this->Unity_type_model->get_one($uh->id_tipo_unidad);
			$unidad = $this->Unity_model->get_one($uh->id_unidad);
			$array_unidades_huellas[$tipo_unidad->nombre] = $unidad->nombre;
		}
		
		foreach ($unidades_reporte as $ur){
			$tipo_unidad = $this->Unity_type_model->get_one($ur->id_tipo_unidad);
			$unidad = $this->Unity_model->get_one($ur->id_unidad);
			$array_unidades_reporte[$tipo_unidad->nombre] = $unidad->nombre;
		}

		/* $contenido_tabla = array();
		foreach($array_unidades_reporte as $index_ur => $ur){
			$contenido_tabla[$index_ur] = $ur;
			foreach($array_unidades_huellas as $index_uh => $uh){
				foreach($array_unidades_huellas as $index_uh => $uh){
							if($index_ur == $index_uh){
								if($ur != $uh){
									$contenido_tabla[$index_ur] = $ur;
								}
							}
				}
			}
		} */
		
		$filename = $client_info->sigla.'_'.$project_info->sigla.'_ACV_'.lang('units_acv_excel').'_'.date("Y-m-d");
		
		$html = '';
		$html .= '<table id="unidades-table" class="table table-bordered">';
		//$html .= '<table id="unidades-table" class="display" cellspacing="0" width="100%">';
			$html .= '<tr class="label-info">';
				$html .= '<th colspan="2" style="text-align:center;"><span>'.lang('units').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_units" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
			$html .= '</tr>'; 
			
			$html .= '<tr>';
				$html .= '<td class="text-center"><b>'.lang('unit_type').'</b></td>';
				$html .= '<td class="text-center"><b>'.lang('unit').'</b></td>';
			$html .= '</tr>';
			foreach($array_unidades_reporte as $index_ur => $ur){
				$html .= '<tr>';
					$html .= '<td>'.$index_ur.'</td>';
					$html .= '<td>'.$ur.'</td>';
						foreach($array_unidades_huellas as $index_uh => $uh){
							if($index_ur == $index_uh){
								if($ur != $uh){
									$html .= '<tr>';
										$html .= '<td>'.$index_uh.'</td>';
										$html .= '<td>'.$uh.'</td>';
									$html .= '</tr>';
								}
							}
						}	
				$html .= '</tr>';
			} 
			
		$html .= '</table>';
		
		/* $html .= '<script type="text/javascript">';
			$html .= '$("#unidades-table").DataTable()';
				$html .= 'source: "'.json_encode(array('data' => $array_unidades_huellas)).'",';
					$html .= 'columns: [';
						$html .= '{title: "Tipo Unidad", "class": "text-center w50"},';
						$html .= '{title: "Unidad", "class": "text-center w50"},';
				$html .= '}]';
			$html .= '});'; 
			
		$html .= '</script>'; */
		

		return $html;
		
	}
	
	//OK
	function list_company_info($id_cliente, $id_proyecto){
		
		$cliente = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		$filename = $cliente->sigla.'_'.$project_info->sigla.'_ACV_'.lang('company_info_acv_excel').'_'.date("Y-m-d");
		
		$html = '';
		$html .= '<table id="info_empresa-table" class="table table-bordered">';
			$html .= '<tr class="label-info">';
				$html .= '<th colspan="2" style="text-align:center;"><span>'.lang('company_info').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_info_empresa" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
			$html .= '</tr>'; 
			
			$html .= '<tr>';
				$html .= '<td><b>'.lang('company').'</b></td>';
				$html .= '<td>'.$cliente->company_name.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('giro').'</b></td>';
				$html .= '<td>'.$cliente->giro.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('country').'</b></td>';
				$html .= '<td>'.$cliente->pais.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('website').'</b></td>';
				$html .= '<td>'.$cliente->website.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('site_logo').'</b></td>';
				$url_logo = "";
				if($cliente->logo){
					$last_modif = filemtime("files/mimasoft_files/client_".$cliente->id."/".$cliente->logo.".png");
					$url_logo = get_file_uri("files/mimasoft_files/client_".$cliente->id."/".$cliente->logo.".png?=".$last_modif);
				} else {
					$url_logo = get_file_uri("files/system/default-site-logo.png");
				}	
				$html .= '<td><img id="site-logo-preview" src="'.$url_logo.'" alt="..." /></td>';
			$html .= '</tr>';
		$html .= '</table>';
		
		return $html;
		
	}
	
	//OK
	function list_project_info($id_cliente, $id_proyecto){
		
		$cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('project_info_acv_excel').'_'.date("Y-m-d");
		
		$rubro = $this->Industries_model->get_one($proyecto->id_industria);
		$subrubro = $this->Subindustries_model->get_one($proyecto->id_tecnologia);
		
		$html = '';
		$html .= '<table id="info_proyecto-table" class="table table-bordered">';
			$html .= '<tr>';
				$html .= '<th class="label-info" colspan="2" style="text-align:center;"><span>'.lang('project_info').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_info_proyecto" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
			$html .= '</tr>'; 
			
			$html .= '<tr>';
				$html .= '<td><b>'.lang('name').'</b></td>';
				$html .= '<td>'.$proyecto->title.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('industry').'</b></td>';
				$html .= '<td>'.$rubro->nombre.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('subindustry').'</b></td>';
				$html .= '<td>'.$subrubro->nombre.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('country').'</b></td>';
				$html .= '<td>'.$proyecto->country.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('city').'</b></td>';
				$html .= '<td>'.$proyecto->city.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('town').'</b></td>';
				$html .= '<td>'.$proyecto->state.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('start_date').'</b></td>';
				//$html .= '<td>'.$proyecto->start_date.'</td>';
				$html .= '<td>'.get_date_format($proyecto->start_date,$id_proyecto).'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('deadline').'</b></td>';
				//$html .= '<td>'.$proyecto->deadline.'</td>';
				$html .= '<td>'.get_date_format($proyecto->deadline,$id_proyecto).'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td><b>'.lang('description').'</b></td>';
				$html .= '<td>'.$proyecto->description.'</td>';
			$html .= '</tr>';
		$html .= '</table>';
		
		return $html;
	}
	
	//OK
	function list_unit_processes_info($id_cliente, $id_proyecto){
		
		$cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('unit_processes_acv_excel').'_'.date("Y-m-d");
		
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($id_proyecto)->result_array();
		
		$html = '';
		$html .= '<table id="procesos_unitarios-table" class="table table-bordered">';
			$html .= '<tr class="label-info">';
				$html .= '<th colspan="2" style="text-align:center;"><span>'.lang('unit_processes').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_procesos_unitarios" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td class="text-center"><b>'.lang('unit_process').'</b></td>';
				$html .= '<td class="text-center"><b>'.lang('definition').'</b></td>';
			$html .= '</tr>';
			
			foreach($procesos_unitarios as $pu){
				$html .= '<tr>';
					$html .= '<td>'.$pu["nombre"].'</td>';
					$descripcion = ($pu["descripcion"] == "")?'-': $pu["descripcion"];
					$html .= '<td>'.$descripcion.'</td>';
				$html .= '</tr>';
			}
			
		$html .= '</table>';
		
		return $html;
		
	}
	
	/*
	* Listado de materiales y categorias asociados al Proyecto y que estén siendo registrados en sus Registros Ambientales durante el rango de fechas ingresado
	*
	* @access public
	* @return string retorna la seccion "Listado de Materiales" del reporte de ACV 
	*/
	function list_categories_and_materials($data_acv = array()){
		
		$elementos = $this->Form_values_model->get_details(
			array(
				"id_proyecto" => $data_acv["id_proyecto"],
				"id_tipo_formulario" => 1
			)
		)->result();
		
		$array_categorias = array();
		foreach($elementos as $elemento){
			$datos_decoded = json_decode($elemento->datos, true);
				
			if($datos_decoded["fecha"]){
				if(($datos_decoded["fecha"] >= $data_acv["start_date"]) && ($datos_decoded["fecha"] <= $data_acv["end_date"])){
					
					if($datos_decoded["id_categoria"]){
						$array_categorias[] = $datos_decoded["id_categoria"];
					}
				}
			}
		}
		
		$array_categorias = array_unique($array_categorias);
		$array_material_categoria = array();
		foreach($array_categorias as $id_categoria){
			$material_info = $this->Materials_model->get_material_of_category($id_categoria)->row();
			$categoria_info = $this->Categories_model->get_one($id_categoria);
			
			if($material_info->id){
				$material = $material_info->nombre;
			}else{
				$material = "-";
			}
			
			if($categoria_info->id){
				$alias_categoria = $this->Categories_alias_model->get_one_where(
					array(
						"id_cliente" => $data_acv["id_cliente"],
						"id_categoria" => $id_categoria, 
						"deleted" => 0
					)
				);
				
				$categoria = ($alias_categoria->alias) ? $alias_categoria->alias." (".$categoria_info->nombre.")" : $categoria_info->nombre;
			}else{
				$categoria = "-";
			}
			
			$array_material_categoria[] = array("material" => $material, "categoria" => $categoria);
		}
		
		array_multisort($array_material_categoria);
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('materials_list_acv_excel').'_'.date("Y-m-d");
		
		$html = '';
		$html .= '<table id="listado_materiales-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="2" style="text-align:center;"><span>'.lang('materials_list').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_listado_materiales" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('material').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('category').'</b></td>';
		$html .= '</tr>';
		
		foreach($array_material_categoria as $material_categoria){
			$html .= '<tr>';
			$html .= '<td>'.$material_categoria["material"].'</td>';
			$html .= '<td>'.$material_categoria["categoria"].'</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		return $html;
	}
	
	/*
	* Listado de Procesos Unitarios a los que se están yendo los valores de materiales y categorias asociados al Proyecto y que estén siendo registrados en sus Registros Ambientales durante el rango de fechas ingresado. Esta información se muestra en base al modulo de Asignación y no contempla mostrarla agrupada por porcentajes.
	*
	* @access public
	* @return string retorna la seccion "Flujos por Proceso Unitario" del reporte de ACV 
	*/
	function list_unit_processes_flows($data_acv = array()){
		
		/*$procesos_unitarios = $this->Unit_processes_model->get_unit_process_details($data_acv["id_proyecto"])->result();
		$subproyecto = $this->Subprojects_model->get_one($data_acv["id_subproyecto"]);
		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		
		$id_subproyecto = $subproyecto->id;
		
		$array_pu_categoria = array();
		foreach($procesos_unitarios as $pu) {// RECORRO LOS PROCESOS UNITARIOS DEL PROYECTO
			
			$id_proceso_unitario = $pu->id;
            
			foreach($criterios_calculos as $criterio_calculo){// RECORRO LOS PROCESOS CRITERIOS Y CALCULOS DEL PROYECTO
				
				$id_criterio = $criterio_calculo->id_criterio;
				$id_formulario = $criterio_calculo->id_formulario;
				$id_material = $criterio_calculo->id_material;
				$id_categoria = $criterio_calculo->id_categoria;
				$id_subcategoria = $criterio_calculo->id_subcategoria;

				// SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC
				if(isset($criterio_calculo->tipo_tratamiento_by_criterio)){
					$j_datos = json_decode($criterio_calculo->tipo_tratamiento_by_criterio, true);

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
				// FIN SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC
				
				// NUEVA ASIGNACION
				// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
				$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
				
				// UNA VEZ QUE YA TENGO FC PARA A NIVEL DE CRITERIO(RA) - CALCULO, RECORRO LOS ELEMENTOS ASOCIADOS
				$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($data_acv["id_proyecto"], $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
				
				foreach($elementos as $elemento){
					
					$datos_decoded = json_decode($elemento->datos, true);
					
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
							
							if($tipo_asignacion_sp == "Total" && $sp_destino == $id_subproyecto && $pu_destino == $id_proceso_unitario){
								
								if($criterio_sp == $valor_campo_sp){
									$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
								}
								
							}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_proceso_unitario){
								
								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto];
								if($porcentaje_sp != 0){
									if($criterio_sp == $valor_campo_sp){
										$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
									}
								}
								
								
							}
							
							
						}
					}
					
					
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
							
							if($tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto && $pu_destino == $id_proceso_unitario){
								
								if($criterio_pu == $valor_campo_pu){
									$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
								}
								
							}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
								
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									if($criterio_pu == $valor_campo_pu){
										$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
									}
									
								}
								
								
							}
							
							
						}
					}
					
					
					if($id_campo_sp && $id_campo_pu){
						
						$valor_campo_sp = $datos_decoded[$id_campo_sp];
						$valor_campo_pu = $datos_decoded[$id_campo_pu];
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							//var_dump($datos_decoded["id_categoria"]);
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto && $pu_destino == $id_proceso_unitario){
								
								if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
									$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
									
								}
								
							}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto){
								$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
								$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
								if($porcentaje_pu != 0){
									if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
									}
								}
								
								
								
							}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_proceso_unitario){
								
								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
									}
								}
								
								
								
							}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){

								$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
								$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
								if($porcentaje_sp != 0){
									
									$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
									$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
									if($porcentaje_pu != 0){
										if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
											$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
										}
									}
									
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
							
							if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto && $pu_destino == $id_proceso_unitario){
								$array_pu_categoria[$id_proceso_unitario] = $datos_decoded["id_categoria"];
							}
						}
					}
					
					
					
				}// FIN ELEMENTO
				
			}// FIN CRITERIO-CALCULO
			
        }// FIN PROCESOS UNITARIOS
		*/
		
		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($data_acv["id_proyecto"])->result_array();
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('unit_processes_flows_acv_excel').'_'.date("Y-m-d");
		
		$array_unit_processes_flows = array();
		
		$html = '';
		$html .= '<table id="flujos_procesos_unitarios-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="3" style="text-align:center;"><span>'.lang('unit_processes_flows').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_flujos_procesos_unitarios" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('unit_process').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('material').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('category').'</b></td>';
		$html .= '</tr>';
		
		foreach($huellas as $huella){
	
			$id_huella = $huella->id;
			$total_huella = 0;
			$array_valores_pu = array();
			
			foreach($procesos_unitarios as $pu){
				
				$id_pu = $pu["id"];
				$nombre_pu = $pu["nombre"];
				$total_pu = 0; 
				
				foreach($criterios_calculos as $index => $criterio_calculo){
					
					// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
					// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
					$id_criterio = $criterio_calculo->id_criterio;
					$asignaciones_de_criterio = $this->Assignment_model->get_details(
						array("id_criterio" => $id_criterio, 
						"sp_destino" => $id_subproyecto_uf, 
						"pu_destino" => $id_pu
						)
					)->result();
					
					if($asignaciones_de_criterio){
						
						$proceso_unitario = $this->Unit_processes_model->get_one($id_pu);
						$material = $this->Materials_model->get_one($criterio_calculo->id_material);
						$categoria = $this->Categories_model->get_one($criterio_calculo->id_categoria);
						
						$alias_categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $data_acv["id_cliente"], "id_categoria" => $criterio_calculo->id_categoria, "deleted" => 0));
						$nombre_categoria = ($alias_categoria->alias) ? $alias_categoria->alias : $categoria->nombre;
						$array_unit_processes_flows[$proceso_unitario->nombre][$index.'-'.$material->nombre] = $nombre_categoria;
						//$array_unit_processes_flows[] = array("pu" => $nombre_pu, "material" => $material->nombre, "categoria" => $nombre_categoria);	
					}

				}
			}
				
		}
		
		foreach($array_unit_processes_flows as $index => $unit_process_flow){		
			foreach(array_unique($unit_process_flow) as $innerIndex => $upf){
				$explode_index = explode('-',$innerIndex, 2);
				$html .= '<tr>';
				$html .= '<td>'.$index.'</td>';
				$html .= '<td>'.$explode_index[1].'</td>';
				$html .= '<td>'.$upf.'</td>';
				$html .= '</tr>'; 
			}
		}

		$html .= '</table>';
		
		return $html;
		
		
	}
	
	
	function list_assignment($data_acv = array()){
 		
		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($data_acv["id_proyecto"])->result_array();
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('assignment_acv_excel').'_'.date("Y-m-d");
		
		$array_list_assignment = array();
		
		$html = '';
		$html .= '<table id="asignacion-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="5" style="text-align:center;"><span>'.lang('assignment').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_asignacion" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('category').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('unit_process_rule_field').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('assignment_rule').'</b></td>';
			$html .= '<td class="text-center"><b>%</b></td>';
			$html .= '<td class="text-center"><b>'.lang('target_unitary_process').'</b></td>';
		$html .= '</tr>';
		
		foreach($huellas as $huella){
	
			$id_huella = $huella->id;
			$total_huella = 0;
			$array_valores_pu = array();
			
			foreach($procesos_unitarios as $pu){
				
				$id_pu = $pu["id"];
				$nombre_pu = $pu["nombre"];
				$total_pu = 0; 
				
				foreach($criterios_calculos as $index => $criterio_calculo){
					
					$id_criterio = $criterio_calculo->id_criterio;
					$id_campo_sp = $criterio_calculo->id_campo_sp;
					$id_campo_pu = $criterio_calculo->id_campo_pu;
					
					$categoria = $this->Categories_model->get_one($criterio_calculo->id_categoria);
					if($id_campo_pu){
						$campo_pu = $this->Fields_model->get_one($id_campo_pu);
					}else{
						$campo_pu = NULL;
					}
					$alias_categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $data_acv["id_cliente"], "id_categoria" => $criterio_calculo->id_categoria, "deleted" => 0));
					$nombre_categoria = ($alias_categoria->alias) ? $alias_categoria->alias : $categoria->nombre;
					
					// NUEVA ASIGNACION
					// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
					$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
					
					if($asignaciones_de_criterio){
						
						foreach($asignaciones_de_criterio as $obj_asignacion){
							
							$criterio_sp = $obj_asignacion->criterio_sp;
							$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
							$sp_destino = $obj_asignacion->sp_destino;
							$porcentajes_sp = $obj_asignacion->porcentajes_sp;
							
							$criterio_pu = $obj_asignacion->criterio_pu;
							$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
							$pu_destino = $obj_asignacion->pu_destino;
							$porcentajes_pu = $obj_asignacion->porcentajes_pu;
							
							// SI EL SP DESTINO ES IGUAL AL DEL LOOP O ES NULL (PORCENTAJE)
							if($sp_destino == $id_subproyecto_uf  || !$sp_destino){
								
								if($tipo_asignacion_sp == "Total"){
									
									if($tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
										$porcentaje = "100%";
										$array_list_assignment[] = array(
											"categoria" => $nombre_categoria,
											"campo_criterio_pu" => ($id_campo_pu)?$campo_pu->nombre:'-',
											"criterio_pu" => ($criterio_pu)?$criterio_pu:'-',
											"porcentaje_pu" => $porcentaje,
											"destino_pu" => $nombre_pu,
										);
									}
									if($tipo_asignacion_pu == "Porcentual"){
										$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
										$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
										if($porcentaje_pu > 0){
											$porcentaje = $porcentaje_pu."%";
											$array_list_assignment[] = array(
												"categoria" => $nombre_categoria,
												"campo_criterio_pu" => ($id_campo_pu)?$campo_pu->nombre:'-',
												"criterio_pu" => ($criterio_pu)?$criterio_pu:'-',
												"porcentaje_pu" => $porcentaje,
												"destino_pu" => $nombre_pu,
											);
										}
									}
									
									
								}
								
								if($tipo_asignacion_sp == "Porcentual"){
									
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
									if($porcentaje_sp > 0){
										
										if($tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
											$porcentaje = "100%";
											$array_list_assignment[] = array(
												"categoria" => $nombre_categoria,
												"campo_criterio_pu" => ($id_campo_pu)?$campo_pu->nombre:'-',
												"criterio_pu" => ($criterio_pu)?$criterio_pu:'-',
												"porcentaje_pu" => $porcentaje,
												"destino_pu" => $nombre_pu,
											);
										}
										if($tipo_asignacion_pu == "Porcentual"){
											$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
											$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
											if($porcentaje_pu > 0){
												$porcentaje = $porcentaje_pu."%";
												$array_list_assignment[] = array(
													"categoria" => $nombre_categoria,
													"campo_criterio_pu" => ($id_campo_pu)?$campo_pu->nombre:'-',
													"criterio_pu" => ($criterio_pu)?$criterio_pu:'-',
													"porcentaje_pu" => $porcentaje,
													"destino_pu" => $nombre_pu,
												);
											}
										}
										
									}
								}
								
								
							}
							
						}
						
					}

				}
			}
				
		}
		
		$array_list_assignment_sorted = array_unique($array_list_assignment, SORT_REGULAR);
		$array_list_assignment_sorted = $this->array_sort($array_list_assignment_sorted, "categoria", SORT_ASC);
		
		foreach($array_list_assignment_sorted as $list_assignment){
			
			$html .= '<tr>';
			$html .= '<td>'.$list_assignment["categoria"].'</td>';
			$html .= '<td>'.$list_assignment["campo_criterio_pu"].'</td>';
			$html .= '<td>'.$list_assignment["criterio_pu"].'</td>';
			$html .= '<td class="text-right">'.$list_assignment["porcentaje_pu"].'</td>';
			$html .= '<td>'.$list_assignment["destino_pu"].'</td>';
			$html .= '</tr>';
			
		}
		$html .= '</table>';
		
		return $html;
		
	}
	
	function list_impact_categories($data_acv = array()){
		//Categorias de impacto proyecto
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('impact_categories_acv_excel').'_'.date("Y-m-d");
		
		$array_list_impact_categories = array();
		
		$html = '';
		$html .= '<table id="categorias_impacto-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="4" style="text-align:center;"><span>'.lang('impact_categories').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_categorias_impacto" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('impact_category').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('unit').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('indicator').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('definition').'</b></td>';
		$html .= '</tr>';
		
		
		foreach($huellas as $huella){
			
			//$unidad = $this->Unity_model->get_one($huella->id_unidad);
			
			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
				"id_cliente" => $data_acv["id_cliente"], 
				"id_proyecto" => $data_acv["id_proyecto"], 
				"id_tipo_unidad" => $huella->id_tipo_unidad, 
				"deleted" => 0
			))->id_unidad;
			
			$unidad = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
			
			
			$descripcion = ($huella->descripcion)?$huella->descripcion:"-";
			$html .= '<tr>';
				$html .= '<td>'.$huella->nombre.'</td>';
				$html .= '<td>'.$unidad.'</td>';
				$html .= '<td>'.$huella->indicador.'</td>';
				$html .= '<td>'.$descripcion.'</td>';
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		return $html;
		
	}
	
	//REVISAR CAMPO DESCRIPCIÓN	
	function list_characterization_model($data_acv = array()){
		
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		$modelo_caracterizacion = $this->Methodology_model->get_one($proyecto->id_metodologia);
		$descripcion = ($modelo_caracterizacion->descripcion)?$modelo_caracterizacion->descripcion:"-";
		
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('characterization_model_acv_excel').'_'.date("Y-m-d");
		
		$html = '';
		$html .= '<table id="modelo_caracterizacion-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="2" style="text-align:center;"><span>'.lang('characterization_model').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_modelo_caracterizacion" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('characterization_model').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('description').'</b></td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td>'.$modelo_caracterizacion->nombre.'</td>';
			$html .= '<td>'.$descripcion.'</td>';
		$html .= '</tr>';
		
		$html .= '</table>';
		
		return $html;
		
	}
	

	function list_calculation_of_category_indicators($data_acv = array()){
		
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		$array_bd = array();
		foreach($criterios_calculos as $criterio_calculo){
			$bd_info = $this->Databases_model->get_one($criterio_calculo->id_bd);
			if($bd_info->id && !in_array($bd_info->id, $array_bd)){
				$array_bd[] = $bd_info->id;
			}
		}
		
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$proyecto = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		
		$filename = $cliente->sigla.'_'.$proyecto->sigla.'_ACV_'.lang('calculation_of_category_indicators_acv_excel').'_'.date("Y-m-d");		
		
		$html = '';
		$html .= '<table id="category_indicators-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="2" style="text-align:center;"><span>'.lang('calculation_of_category_indicators').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_category_indicators" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('database').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('description').'</b></td>';
		$html .= '</tr>';
		
		foreach($array_bd as $id_bd){
			$bd = $this->Databases_model->get_one($id_bd);
			$descripcion = ($bd->descripcion)?$bd->descripcion:"-";
			$html .= '<tr>';
				$html .= '<td>'.$bd->nombre.'</td>';
				$html .= '<td>'.$descripcion.'</td>';
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		return $html;
	}
	
	function list_results_by_impact_category($data_acv = array()){ 
		
		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$nombre_uf = $unidad_funcional->nombre;
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		//$valor_uf = $unidad_funcional->valor;
		$valor_uf = get_functional_unit_value($data_acv["id_cliente"], $data_acv["id_proyecto"], $unidad_funcional->id, $data_acv["start_date"], $data_acv["end_date"]);
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($data_acv["id_proyecto"])->result_array();
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$project_info = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		$id_metodologia = $project_info->id_metodologia;
		
		$filename = $cliente->sigla.'_'.$project_info->sigla.'_ACV_'.lang('results_by_impact_category_acv_excel').'_'.date("Y-m-d");
		
		$html = '';
		$html .= '<table id="resultados_cat_impact-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="'.(count($huellas) + 1).'" style="text-align:center;"><span>'.lang('results_by_impact_category').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_resultados_cat_impact" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('functional_unit').'</b></td>';
			//$html .= '<th>'.lang('description').'</th>';
		//$html .= '</tr>';
		
		
			foreach($huellas as $huella){
				
				$id_huella = $huella->id;
				$total_huella = 0;
				$array_valores_pu = array();
				$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
					"id_cliente" => $data_acv["id_cliente"], 
					"id_proyecto" => $data_acv["id_proyecto"], 
					"id_tipo_unidad" => $huella->id_tipo_unidad, 
					"deleted" => 0
				))->id_unidad;
				
				$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
				
				// VALOR DE CONVERSION
				$id_tipo_unidad_origen = $huella->id_tipo_unidad;
				$id_unidad_origen = $huella->id_unidad;
				$fila_config_huella = $this->Module_footprint_units_model->get_one_where(
					array(
						"id_cliente" => $data_acv["id_cliente"],
						"id_proyecto" => $data_acv["id_proyecto"],
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
				
				foreach($procesos_unitarios as $pu){
					
					$id_pu = $pu["id"];
					$nombre_pu = $pu["nombre"];
					$total_pu = 0;
					
					foreach($criterios_calculos as $criterio_calculo){
				
						$total_criterio = 0;
						
						$id_criterio = $criterio_calculo->id_criterio;
						$id_formulario = $criterio_calculo->id_formulario;
						$id_material = $criterio_calculo->id_material;
						$id_categoria = $criterio_calculo->id_categoria;
						$id_subcategoria = $criterio_calculo->id_subcategoria;
						
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
						$criterio_fc = $criterio_calculo->criterio_fc;
						$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
						
						// NUEVA ASIGNACION
						// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
						$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
						
						// CONSULTAR CAMPOS UNIDAD DEL RA
						$array_unidades = array();
						$array_id_unidades = array();
						$array_id_tipo_unidades = array();
						
						foreach($ides_campo_unidad as $id_campo_unidad){
							
							if($id_campo_unidad == 0){
								$id_formulario = $criterio_calculo->id_formulario;
								$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
								$json_unidad_form = json_decode($form_data->unidad,true);
								
								$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
								$id_unidad = $json_unidad_form["unidad_id"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
								$array_unidades[] = $fila_unidad->nombre;
								$array_id_unidades[] = $id_unidad;
								$array_id_tipo_unidades[] = $id_tipo_unidad;
							}else{
								$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
								$info_campo = $fila_campo->opciones;
								$info_campo = json_decode($info_campo, true);
								
								$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
								$id_unidad = $info_campo[0]["id_unidad"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
								$array_unidades[] = $fila_unidad->nombre;
								$array_id_unidades[] = $id_unidad;
								$array_id_tipo_unidades[] = $id_tipo_unidad;
							}
						}
						
						// OBTENER UNIDAD FINAL
						/*if(count($array_id_unidades) > 1){
							$existe_longitud = in_array(5, $array_id_tipo_unidades);
							$existe_kg = in_array("kg", $array_unidades, true);
							$existe_ton = in_array("t", $array_unidades, true);
							
							if($existe_longitud && ($existe_kg || $existe_ton)){
								$id_unidad = ($existe_kg)?6:($existe_ton)?5:0;
							}
						}else{
							$id_unidad = $array_id_unidades[0];
						}*/
						
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
						$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($data_acv["id_proyecto"], $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
						
						foreach($elementos as $elemento){
							
							$total_elemento = 0;
							$datos_decoded = json_decode($elemento->datos, true);	
							
							if($datos_decoded["fecha"]){
							
								if(($datos_decoded["fecha"] >= $data_acv["start_date"]) && ($datos_decoded["fecha"] <= $data_acv["end_date"])){
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
								
										if($id_campo_sp == "tipo_tratamiento"){
											$value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
											$valor_campo_sp = $value->nombre;
										}else{
											$valor_campo_sp = $datos_decoded[$id_campo_sp];
										}
										
										//$valor_campo_sp = $datos_decoded[$id_campo_sp];
										
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
													//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
												
												$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
												$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
												if($porcentaje_sp != 0){
													$porcentaje_sp = ($porcentaje_sp/100);
												}
												
												if($criterio_sp == $valor_campo_sp){
													$total_elemento += ($total_elemento_interno * $porcentaje_sp);
													//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.'<br>';
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
								
										if($id_campo_pu == "tipo_tratamiento"){
											$value = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
											$valor_campo_pu = $value->nombre;
										}else{
											$valor_campo_pu = $datos_decoded[$id_campo_pu];
										}
										//$valor_campo_pu = $datos_decoded[$id_campo_pu];
										
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
										if(($id_campo_pu == "tipo_tratamiento")&&($id_campo_sp == "tipo_tratamiento")){
											
											$value_sp = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
											$valor_campo_sp = $value_sp->nombre;
											$value_pu = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
											$valor_campo_pu = $value_pu->nombre;
											
										}else{
											$valor_campo_sp = $datos_decoded[$id_campo_sp];
											$valor_campo_pu = $datos_decoded[$id_campo_pu];
										}
										/*
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
										*/
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
													//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.'<br>';
												}
												
											}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
												$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
												$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
												if($porcentaje_pu != 0){
													$porcentaje_pu = ($porcentaje_pu/100);
												}
												
												if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
													$total_elemento += ($total_elemento_interno * $porcentaje_pu);
													//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
												
												$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
												$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
												if($porcentaje_sp != 0){
													$porcentaje_sp = ($porcentaje_sp/100);
												}
												
												if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
													$total_elemento += ($total_elemento_interno * $porcentaje_sp);
													//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.'<br>';
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){
												
												//echo $porcentajes_sp.'|'.$porcentajes_pu.'<br>';
		
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
													//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.' * '.$porcentaje_pu.'<br>';
												}
											}
										}
									}
									
									
									if(!$id_campo_sp && !$id_campo_pu){
										//var_dump($asignaciones_de_criterio);
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
							
							
								} // END if(($datos_decoded >= $data_acv["start_date"]) && ($datos_decoded <= $data_acv["end_date"]))
							
							
							} //END if($datos_decoded["fecha"])
							
	
	
							
						}// FIN ELEMENTO
						
						
						$total_pu += $total_criterio;

					}

					$total_pu = $total_pu/$valor_uf;
					$total_huella += $total_pu;

				}
				
				$total_huella *= $valor_transformacion;
				
				$html .= '<td class="text-center"><b>'.$huella->nombre.' <br>('.$nombre_unidad_huella.' '.$huella->indicador.').</b></td>';
				
				//$tr_total_huella .= '<td class="text-right">'.$total_huella.'</td>';
				$tr_total_huella .= '<td class="text-right">'.number_format($total_huella, strlen($total_huella)).'</td>';
			
			}
			
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>'.$nombre_uf.'</td>';
				$html .= $tr_total_huella;
			$html .= '</tr>';
			$html .= '</table>';
			
			return $html;
	}
	
	function list_critical_points($data_acv = array()){
		
		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$nombre_uf = $unidad_funcional->nombre;
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		//$valor_uf = $unidad_funcional->valor;
		$valor_uf = get_functional_unit_value($data_acv["id_cliente"], $data_acv["id_proyecto"], $unidad_funcional->id, $data_acv["start_date"], $data_acv["end_date"]);
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($data_acv["id_proyecto"])->result_array();
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		$cliente = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$project_info = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		$id_metodologia = $project_info->id_metodologia;
		
		$filename = $cliente->sigla.'_'.$project_info->sigla.'_ACV_'.lang('critical_points_acv_excel').'_'.date("Y-m-d");
		$id_metodologia = $project_info->id_metodologia;
		
		$array_critical_points = array();
		
		$html = '';
		$html .= '<table id="puntos_criticos-table" class="table table-bordered">';
		$html .= '<tr class="label-info">';
			$html .= '<th colspan="9" style="text-align:center;"><span>'.lang('critical_points').'</span><button type="button" class="btn btn-success btn-xs pull-right btn_excel" id="excel_puntos_criticos" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button></th>';
		$html .= '</tr>'; 
		$html .= '<tr>';
			$html .= '<td class="text-center"><b>'.lang('footprint').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('unit_process').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('material').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('category').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('subcategory').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('label').' '.lang('calculation').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('value').' '.lang('total').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('field_of_calculation').'</b></td>';
			$html .= '<td class="text-center"><b>'.lang('transformer').'</b></td>';
		$html .= '</tr>';
		
			foreach($huellas as $huella){
				
				$nombre_huella = $huella->nombre;
				$id_huella = $huella->id;
				$total_huella = 0;
				
				
				foreach($procesos_unitarios as $pu){
					
					$id_pu = $pu["id"];
					$nombre_pu = $pu["nombre"];
					$total_pu = 0; 
					
					foreach($criterios_calculos as $criterio_calculo){
						
						$total_criterio = 0;
					
						$id_criterio = $criterio_calculo->id_criterio;
						$id_formulario = $criterio_calculo->id_formulario;
						$id_material = $criterio_calculo->id_material;
						$id_categoria = $criterio_calculo->id_categoria;
						$id_subcategoria = $criterio_calculo->id_subcategoria;
						
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
						$criterio_fc = $criterio_calculo->criterio_fc;
						$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
						
						$nombres_campo_unidad = "";
						foreach($ides_campo_unidad as $id_campo_unidad){
							$nombres_campo_unidad .= '- '.$this->Fields_model->get_one($id_campo_unidad)->nombre.' <br>';
						}
						
						/*$asignaciones_de_criterio = $this->Assignment_model->get_details(
							array("id_criterio" => $id_criterio, 
								"sp_destino" => $id_subproyecto_uf, 
								"pu_destino" => $id_pu
							)
						)->result();*/
						
						// NUEVA ASIGNACION
						// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
						$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
						
						foreach($asignaciones_de_criterio as $asignacion){
							$criterio_sp = $asignacion->criterio_sp;
							$criterio_pu = $asignacion->criterio_pu;
						}
						
						$array_unidades = array();
						$array_id_unidades = array();
						$array_id_tipo_unidades = array();
						
						foreach($ides_campo_unidad as $id_campo_unidad){
							
							if($id_campo_unidad == 0){
								$id_formulario = $criterio_calculo->id_formulario;
								$form_data = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
								$json_unidad_form = json_decode($form_data->unidad,true);
								
								$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
								$id_unidad = $json_unidad_form["unidad_id"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
								$array_unidades[] = $fila_unidad->nombre;
								$array_id_unidades[] = $id_unidad;
								$array_id_tipo_unidades[] = $id_tipo_unidad;
							}else{
								$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
								$info_campo = $fila_campo->opciones;
								$info_campo = json_decode($info_campo, true);
								
								$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
								$id_unidad = $info_campo[0]["id_unidad"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
								$array_unidades[] = $fila_unidad->nombre;
								$array_id_unidades[] = $id_unidad;
								$array_id_tipo_unidades[] = $id_tipo_unidad;
							}
						}
						
						/*foreach($ides_campo_unidad as $id_campo_unidad){
							$fila_campo = $this->Fields_model->get_one($id_campo_unidad);
							$info_campo = $fila_campo->opciones;
							$info_campo = json_decode($info_campo, true);
							
							$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
							$id_unidad = $info_campo[0]["id_unidad"];
							
							$fila_unidad = $this->Unity_model->get_one($id_unidad);
							$array_unidades[] = $fila_unidad->nombre;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}*/
						
						// OBTENER UNIDAD FINAL
						/*if(count($array_id_unidades) > 1){
							$existe_longitud = in_array(5, $array_id_tipo_unidades);
							$existe_kg = in_array("kg", $array_unidades, true);
							$existe_ton = in_array("t", $array_unidades, true);
							
							if($existe_longitud && ($existe_kg || $existe_ton)){
								$id_unidad = ($existe_kg)?6:($existe_ton)?5:0;
							}
						}else{
							$id_unidad = $array_id_unidades[0];
						}*/
						
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
						
						foreach($asignaciones_de_criterio as $asignacion){
							$criterio_sp = $asignacion->criterio_sp;
							$criterio_pu = $asignacion->criterio_pu;
							
							$elementos_de_asignacion = $this->Calculation_model->get_records_of_forms_for_unit_processes($data_acv["id_proyecto"], $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $id_campo_sp, $criterio_sp, $id_campo_pu, $criterio_pu)->result();

							foreach($elementos_de_asignacion as $elemento){
								
								$datos_decoded = json_decode($elemento->datos, true);
								
								if($datos_decoded["fecha"]){
								
									if(($datos_decoded["fecha"] >= $data_acv["start_date"]) && ($datos_decoded["fecha"] <= $data_acv["end_date"])){
									
									
										$mult = 1;
										foreach($ides_campo_unidad as $id_campo_unidad){
											$mult *= $datos_decoded[$id_campo_unidad];
										}
										
										$total_elemento_interno = $mult * $valor_factor;
										
										$material = $this->Materials_model->get_one($id_material);
										$categoria = $this->Categories_model->get_one($id_categoria);
										$subcategoria = $this->Subcategories_model->get_one($id_subcategoria);
										//$campo_calculo = $this->Fields_model->get_one();
										
										$array_critical_points[] = array(
											"huella" => $nombre_huella,
											"pu" => $nombre_pu,
											"material" => $material->nombre,
											"categoria" => $categoria->nombre,
											"subcategoria" => $subcategoria->nombre,
											"etiqueta" => $criterio_calculo->etiqueta,
											"valor_total" => $total_elemento_interno,
											"campo_calculo" => $nombres_campo_unidad,
											//"transformador" => to_number_project_format($valor_factor, $data_acv["id_proyecto"])
											"transformador" => $mult.' * '.$valor_factor
										);
								
									}//END if(($datos_decoded >= $data_acv["start_date"]) && ($datos_decoded <= $data_acv["end_date"]))
							
							
								}//END if($datos_decoded["fecha"])
							
								//echo 'SUBPROYECTO: '.$id_subproyecto_uf.' | UF: '.$unidad_funcional->nombre.' | HUELLA: '.$nombre_huella.' | PROCESO UNITARIO: '.$nombre_pu.' | CRITERIO: '.$criterio_calculo->etiqueta.' | <strong>'.$criterio_sp.'</strong> Y <strong>'.$criterio_pu.'</strong> | '.$mult.'*'.$valor_factor.' = '.$total_elemento_interno.'<br>';
								//echo 'HUELLA: '.$nombre_huella.' | PROCESO UNITARIO: '.$nombre_pu.' | MATERIAL: '.$material->nombre.' | CATEGORIA: '.$categoria->nombre.' | SUBCATEGORIA: '.$subcategoria->nombre.' | ETIQUETA: '.$criterio_calculo->etiqueta.' | VALOR TOTAL: '.$total_elemento_interno.'<br>';
							}// FIN ELEMENTO
							
							
						}// FIN ASIGNACIONES

					}// FIN CRITERIO-CALCULO

				}// FIN PROCESOS UNITARIOS

			}
			
			$result = array();
			foreach($array_critical_points as $t) {
				$repeat = false;
				for($i=0;$i<count($result);$i++)
				{
					if($result[$i]['etiqueta']==$t['etiqueta'] && $result[$i]['huella']==$t['huella'] && $result[$i]['pu']==$t['pu']
					&& $result[$i]['material']==$t['material'] && $result[$i]['categoria']==$t['categoria'] 
					&& $result[$i]['huella']==$t['huella'] && $result[$i]['subcategoria']==$t['subcategoria'])
					{
						$result[$i]['valor_total']+=$t['valor_total'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false)
					$result[] = array('huella' => $t['huella'], 'pu' => $t['pu'], 'material' => $t['material'], 'categoria' => $t['categoria'],
					'subcategoria' => $t['subcategoria'], 'etiqueta' => $t['etiqueta'], 'valor_total' => $t['valor_total'], 
					'campo_calculo' => $t['campo_calculo'], 'transformador' => $t['transformador']);
			}
			
			//var_dump($result);
			foreach($result as $r){
				$html .= '<tr>';
					$html .= '<td>'.$r["huella"].'</td>';
					$html .= '<td>'.$r["pu"].'</td>';
					$html .= '<td>'.$r["material"].'</td>';
					$html .= '<td>'.$r["categoria"].'</td>';
					$html .= '<td>'.$r["subcategoria"].'</td>';
					$html .= '<td>'.$r["etiqueta"].'</td>';
					$html .= '<td class="text-right">'.to_number_project_format($r["valor_total"], $data_acv["id_proyecto"]).'</td>';
					$html .= '<td>'.$r["campo_calculo"].'</td>';
					$html .= '<td>'.$r["transformador"].'</td>';
				$html .= '</tr>';
			}

			$html .= '</table>';
			
			
			/* foreach($array_critical_points as $acp){
				echo 'HUELLA: '.$acp["huella"].' | PROCESO UNITARIO: '.$acp["pu"].' | MATERIAL: '.$acp["material"].' | CATEGORIA: '.$acp["categoria"].' | SUBCATEGORIA: '.$acp["subcategoria"].' | ETIQUETA: '.$acp["etiqueta"].' | VALOR TOTAL: '.$acp["valor_total"].'<br>';
			} */
			
			
			return $html;
	}
	
	function list_chart_results_of_environmental_impact($data_acv = array()){

		$unidad_funcional = $this->Functional_units_model->get_one($data_acv["id_unidad_funcional"]);
		$nombre_uf = $unidad_funcional->nombre;
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		//$valor_uf = $unidad_funcional->valor;
		$valor_uf = get_functional_unit_value($data_acv["id_cliente"], $data_acv["id_proyecto"], $unidad_funcional->id, $data_acv["start_date"], $data_acv["end_date"]);
		
		$huellas = $this->Project_rel_footprints_model->get_footprints_of_project($data_acv["id_proyecto"])->result();
		$procesos_unitarios = $this->Unit_processes_model->get_pu_of_projects($data_acv["id_proyecto"])->result_array();
		$criterios_calculos = $this->Unit_processes_model->get_rules_calculations_of_project($data_acv["id_cliente"], $data_acv["id_proyecto"])->result();
		$client_info = $this->Clients_model->get_one($data_acv["id_cliente"]);
		$project_info = $this->Projects_model->get_one($data_acv["id_proyecto"]);
		$id_metodologia = $project_info->id_metodologia;
		
		$html = '';
		
		$html .= '<div class="row">';
		$html .= '<div class="page-title clearfix" style="background-color:#5bc0de; margin-bottom: 15px;">';
			$html .= '<h4>'.lang('results_by_impact_category').'</h4>';
		$html .= '</div>';
			$html .= '<div class="panel-body">';
				$html .= '<div id="grafico" style="min-height: 400px;"></div>';
			$html .= '</div>';	
		$html .= '</div>';
			
		$html .= '<script type="text/javascript">';
		
		$array_datos_grafico = array();
			
			foreach($huellas as $huella){
				
				$id_huella = $huella->id;
				$total_huella = 0;
				$array_valores_pu = array();
				$array_colores_pu = array();
				
				foreach($procesos_unitarios as $pu){
					
					$id_pu = $pu["id"];
					$nombre_pu = $pu["nombre"];
					$total_pu = 0; 
					
					foreach($criterios_calculos as $criterio_calculo){
						
						$id_criterio = $criterio_calculo->id_criterio;
						$id_formulario = $criterio_calculo->id_formulario;
						$id_material = $criterio_calculo->id_material;
						$id_categoria = $criterio_calculo->id_categoria;
						$id_subcategoria = $criterio_calculo->id_subcategoria;
						
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
						$criterio_fc = $criterio_calculo->criterio_fc;
						$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
						
						// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
						// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
						/*$asignaciones_de_criterio = $this->Assignment_model->get_details(
							array("id_criterio" => $id_criterio, 
							"sp_destino" => $id_subproyecto_uf, 
							"pu_destino" => $id_pu
							)
						)->result();*/
						
						// NUEVA ASIGNACION
						// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
						$asignaciones_de_criterio = $this->Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
						
						// GUARDAR FILAS DE ASIGNACIONES EN UN ARREGLO BIDIMENSIONAL
						/*$array_asignaciones = array();
						foreach($asignaciones_de_criterio as $asignacion){
							$array_asignaciones[] = array(
								"criterio_sp" => $asignacion->criterio_sp,
								"criterio_pu" => $asignacion->criterio_pu
							);
						}*/
						
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
								$fila_campo = $this->Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
								$info_campo = $fila_campo->opciones;
								$info_campo = json_decode($info_campo, true);
								
								$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
								$id_unidad = $info_campo[0]["id_unidad"];
								
								$fila_unidad = $this->Unity_model->get_one_where(array("id"=>$id_unidad,"deleted"=>0));
								$array_unidades[] = $fila_unidad->nombre;
								$array_id_unidades[] = $id_unidad;
								$array_id_tipo_unidades[] = $id_tipo_unidad;
							}
						}
						
						/*foreach($ides_campo_unidad as $id_campo_unidad){
							$fila_campo = $this->Fields_model->get_one($id_campo_unidad);
							$info_campo = $fila_campo->opciones;
							$info_campo = json_decode($info_campo, true);
							
							$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
							$id_unidad = $info_campo[0]["id_unidad"];
							
							$fila_unidad = $this->Unity_model->get_one($id_unidad);
							$array_unidades[] = $fila_unidad->nombre;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}*/
						
						// OBTENER UNIDAD FINAL
						/*if(count($array_id_unidades) > 1){
							$existe_longitud = in_array(5, $array_id_tipo_unidades);
							$existe_kg = in_array("kg", $array_unidades, true);
							$existe_ton = in_array("t", $array_unidades, true);
							
							if($existe_longitud && ($existe_kg || $existe_ton)){
								$id_unidad = ($existe_kg)?6:($existe_ton)?5:0;
							}
						}else{
							$id_unidad = $array_id_unidades[0];
						}*/
						
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

						//$elementos = $this->Calculation_model->get_records_of_forms_for_calculation_acv_report($data_acv["id_proyecto"], $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $data_acv["start_date"], $data_acv["end_date"])->result();
						$elementos = $this->Calculation_model->get_records_of_forms_for_calculation($data_acv["id_proyecto"], $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();

						foreach($elementos as $elemento){

								$total_elemento = 0;
								$datos_decoded = json_decode($elemento->datos, true);
								
								
							if($datos_decoded["fecha"]){
								
								
								if(($datos_decoded["fecha"] >= $data_acv["start_date"]) && ($datos_decoded["fecha"] <= $data_acv["end_date"])){
									
								
									$mult = 1;
									/*foreach($ides_campo_unidad as $id_campo_unidad){
										$mult *= $datos_decoded[$id_campo_unidad];
									}*/
									
									foreach($ides_campo_unidad as $id_campo_unidad){
										if($id_campo_unidad == 0){
											$mult *= $datos_decoded["unidad_residuo"];
										}else{
											$mult *= $datos_decoded[$id_campo_unidad];
										}
									}
		
									$total_elemento = $mult * $valor_factor;
									//echo $id_campo_sp.'|'.$id_campo_pu.'<br>';
									
									/*if($id_campo_sp && !$id_campo_pu){
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										
										foreach($array_asignaciones as $array_asignacion){
											if($array_asignacion["criterio_sp"] == $valor_campo_sp){
												$total_pu += $total_elemento;
											}
										}
									}*/
									
									if($id_campo_sp && !$id_campo_pu){
								
										if($id_campo_sp == "tipo_tratamiento"){
											$value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
											$valor_campo_sp = $value->nombre;
										}else{
											$valor_campo_sp = $datos_decoded[$id_campo_sp];
										}
										
										//$valor_campo_sp = $datos_decoded[$id_campo_sp];
										
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
													$total_pu += $total_elemento;
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
												
												$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
												$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
												if($porcentaje_sp != 0){
													$porcentaje_sp = ($porcentaje_sp/100);
												}
												
												if($criterio_sp == $valor_campo_sp){
													$total_pu += ($total_elemento * $porcentaje_sp);
												}
											}
										}
									}
									
									/*if(!$id_campo_sp && $id_campo_pu){
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
		
										foreach($array_asignaciones as $array_asignacion){
											if($array_asignacion["criterio_pu"] == $valor_campo_pu){
												$total_pu += $total_elemento;
											}
										}
									}*/
									
									
									if(!$id_campo_sp && $id_campo_pu){
								
										if($id_campo_pu == "tipo_tratamiento"){
											$value = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
											$valor_campo_pu = $value->nombre;
										}else{
											$valor_campo_pu = $datos_decoded[$id_campo_pu];
										}
										//$valor_campo_pu = $datos_decoded[$id_campo_pu];
										
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
													$total_pu += $total_elemento;
												}
												
											}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
												
												$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
												$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
												if($porcentaje_pu != 0){
													$porcentaje_pu = ($porcentaje_pu/100);
												}
												
												if($criterio_pu == $valor_campo_pu){
													$total_pu += ($total_elemento * $porcentaje_pu);
												}
												
											}
											
											
										}
									}
									
									/*if($id_campo_sp && $id_campo_pu){
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
										
										foreach($array_asignaciones as $array_asignacion){
											if($array_asignacion["criterio_sp"] == $valor_campo_sp && $array_asignacion["criterio_pu"] == $valor_campo_pu){
												$total_pu += $total_elemento;
											}
										}
									}*/
									
									if($id_campo_sp && $id_campo_pu){
										
										if(($id_campo_pu == "tipo_tratamiento")&&($id_campo_sp == "tipo_tratamiento")){
											
											$value_sp = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
											$valor_campo_sp = $value_sp->nombre;
											$value_pu = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
											$valor_campo_pu = $value_pu->nombre;
											
										}else{
											$valor_campo_sp = $datos_decoded[$id_campo_sp];
											$valor_campo_pu = $datos_decoded[$id_campo_pu];
										}
										/*
										$valor_campo_sp = $datos_decoded[$id_campo_sp];
										$valor_campo_pu = $datos_decoded[$id_campo_pu];
										*/
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
													$total_pu += $total_elemento;
												}
												
											}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
												$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
												$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
												if($porcentaje_pu != 0){
													$porcentaje_pu = ($porcentaje_pu/100);
												}
												
												if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
													$total_pu += ($total_elemento * $porcentaje_pu);
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
												
												$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
												$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
												if($porcentaje_sp != 0){
													$porcentaje_sp = ($porcentaje_sp/100);
												}
												
												if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
													$total_pu += ($total_elemento * $porcentaje_sp);
												}
												
											}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){
												
												//echo $porcentajes_sp.'|'.$porcentajes_pu.'<br>';
		
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
													$total_pu += ($total_elemento * $porcentaje_sp * $porcentaje_pu);
												}
											}
										}
									}
									
									if(!$id_campo_sp && !$id_campo_pu){
										//var_dump($asignaciones_de_criterio);
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
												$total_pu += $total_elemento;
											}
										}
									}
									
									
								
								
								} // END if(($datos_decoded >= $data_acv["start_date"]) && ($datos_decoded <= $data_acv["end_date"]))

							} //END if($datos_decoded["fecha"])
							
							
						}

					}

					$total_pu = $total_pu/$valor_uf;
					$total_huella += $total_pu;
					$array_valores_pu[] = array("nombre_pu" => $nombre_pu, "total_pu" => $total_pu);
					
					$proceso_unitario = $this->Unit_processes_model->get_one($id_pu);
					
					array_push($array_colores_pu, ($proceso_unitario->color) ? $proceso_unitario->color : "#00b393");
					//$array_colores_pu[] = $proceso_unitario->color;
				}
				
				$array_data = array();
				
				//var_dump($array_colores_pu);
				//var_dump(json_encode($array_colores_pu));
				//exit();
				
				foreach($array_valores_pu as $dato_pu){
					if($dato_pu["total_pu"] == 0){
						$porc_pu = 0;
					}else{
						$porc_pu = ($dato_pu["total_pu"]*100)/$total_huella;
					}
					
					//$array_data[][$huella->abreviatura] = array("name" => $dato_pu["nombre_pu"], "y" => $porc_pu);
					$array_data[] = array("huella" => $huella->abreviatura, "name" => $dato_pu["nombre_pu"], "data" => $porc_pu);
				}

				//$html .=    '   colors:'. json_encode($array_colores_pu).',';
				//$html .=	'	  data:'. json_encode($array_data).'';
				//var_dump($array_data);
				$array_datos_grafico[] = $array_data;
			}

			$array_categories_chart = array();
			$array_data_chart = array();
			
			foreach($array_datos_grafico as $datos_grafico){
				foreach($datos_grafico as $dg){
					$array_categories_chart[] = $dg["huella"];
					$array_data_chart[] = $dg;
				}
			}  
			
			//var_dump($array_data_chart);
			
			$result = array();
			foreach ($array_data_chart as $element) {
				$result[$element['huella']][] = $element;
			}
			
			//var_dump($array_data_chart);
			//var_dump($result);

			$series = array();
			
			foreach($result as $index => $res){
				//var_dump($res);
				$array_valores_procesos = array();
				$array_nombres_procesos = array();
				
				foreach($res as $r){
					
					$array_valores_procesos[] = $r["data"];
					$array_nombres_procesos[] = $r["name"];
					//$array_datos_grafico[] = array("huella" => $r["huella"], "name" => $r["name"], "data" => $r["data"]);
					//var_dump($r["huella"].' - '.$r["name"].' - '.$r["data"]);
				}
				
				$obj_huella = array("name" => $index, "data" => $array_valores_procesos);
				$series[] = $obj_huella;
			}   
			
			
			
			$array = array();
			foreach($array_data_chart as $array_datos){
				$array[$array_datos["name"]][] = $array_datos["data"];
			}

			$series = array();
			foreach($array as $pu => $valores){
				$series[] = array("name" => $pu, "data" => $valores, "dataLabels" => array("enabled" => true,"color" => "black", "borderWidth" => 0, "style" => array("textShadow" => false, "fontSize" => "15")));
			}
			$html .= '$("#grafico").highcharts({';
			$html .= 	'chart: {';
			$html .= 		'type: "bar",';
		    $html .=		'events: {
							   load: function() {
								   if (this.options.chart.forExport) {
									   Highcharts.each(this.series, function (series) {
										   series.update({
											   dataLabels: {
												   enabled: true,
												   color:"black",
												   borderWidth: 0,
												   style: {
														textShadow: false,
														fontSize: "15"
													}
												}
											}, false);
										});
										this.redraw();
									}
								}
							}';
			$html .= 	'},';			
			$html .= 	'title: {';
			//$html .= 		'text: "'.lang('results_by_impact_category').'"';
			$html .= 		'text: "..."';
			$html .= 	'},';
			$html .= 	'credits: {';
			$html .= 		'enabled: false';
			$html .= 	'},';
			$html .= 	'xAxis: {';
			$html .= 		'categories: '.json_encode(array_values(array_unique($array_categories_chart))).',';
			$html .= 		'labels: {style: {fontSize:"16px", color:"black"}}';
			$html .= 	'},';
			$html .= 	'yAxis: {';
			$html .= 		'min: 0,';
			$html .= 		'title: {';
			$html .= 			'text: "'.lang('unit_processes').'"';
			$html .= 		'},';
			$html .= 		'labels: {';
			$html .= 			'formatter: function () {';
			$html .= 				'return this.value + "%";';
			$html .= 			'}';
			$html .= 		'},';
			$html .= 		'stackLabels: {';
			$html .= 			'enabled: true,';
			$html .= 			'style: {';
			$html .= 				'fontWeight: "bold",';
			$html .= 				'color: (Highcharts.theme && Highcharts.theme.textColor) || "gray"';
			$html .= 			'},';
			$html .= 			'formatter: function () {';
			
			/* $datasum = 0;
			foreach($valores as $v){
				$datasum += $v;
			} */
			$html .= 				'return Highcharts.numberFormat(this.total, 0);';
			//$html .= 				'return this.total + "%";';
			/* $html .= 				'var pcnt = (this.y / '.$datasum.') * 100;';
  			$html .= 				'return Highcharts.numberFormat(pcnt) + "%";'; */
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';
			$html .= 	'tooltip: {';
			//$html .= 		'pointFormat: "<span style=\"color:{series.color}\">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>",';
			$html .= 		'pointFormat: "<span style=\"color:{series.color}\">{series.name}</span>: <b>{point.percentage:.0f} %</b><br/>",';
			$html .= 		'shared: true';
			$html .= 	'},';			
			$html .= 	'legend: {';
			$html .= 		'enabled: true,';
			$html .= 		'itemStyle: {fontSize: "15px"}';
			$html .= 	'},';
			
			
			$nombre_grafico = $client_info->sigla.'_'.$project_info->sigla.'_ACV_'.lang('results_by_impact_category_chart').'_'.date("Y-m-d");
			$html .= 	'exporting: {';
			$html .=        'filename: "'.$nombre_grafico.'",';
			$html .=        'sourceWidth: 900,';
			$html .= 		'buttons: {';
			$html .= 			'contextButton: {';
			$html .= 				'menuItems: [{';
			$html .= 					'text: "'.lang('export_to_png').'",';		
			$html .= 					'onclick: function() {';
			$html .= 						'this.exportChart();';
			$html .= 					'},';
			$html .= 					'separator: false';
			$html .= 				'}]';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';

			$html .= 	'plotOptions: {';
		 	$html .= 		'series: {';
			$html .= 			'stacking: "percent"';
			$html .= 		'},';
			$html .= 		'column: {';
			$html .= 			'stacking: "percent",';
			$html .= 			'dataLabels: {';
			$html .= 				'enabled: true,';
			$html .= 				'format:"{y} %",';
			/* $html .= 				'formatter: function() {';
			$html .= 					'return Highcharts.numberFormat(this.y, 2) + "%"';
			$html .= 				'},'; */
			$html .= 				'color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || "white",';
			$html .= 				'style: {';
			$html .= 					'textShadow: "0 0 3px black"';
			$html .= 				'},';
			$html .= 				'filter: {';
			$html .= 					'property: "x",';
			$html .= 					'operator: ">",';
			$html .= 					'value: 4';
			$html .= 				'}';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';
			//$html .=	'colors: ["#0555FA", "#41B9E6", "#E61400", "#FF0F64", "#008C5A", "#55BE5A", "#FFD700"],';
			$html .=    'colors:'. json_encode($array_colores_pu).',';
			//$html .= 	'series:';
			//$html .= json_encode($series);
			
			$html .='series:[';
			foreach($array as $pu => $valores){
			$html .='{';
			$html .='name: "'.$pu.'",';
			$html .='data: [';
					foreach($valores as $v){
					$html .=''.round($v).',';
					}
			$html .='],';
			$html .='dataLabels:{';
			$html .= 'enabled: true,';
			$html .= 'formatter: function(){return (Math.round(this.percentage) + " %");},';
			$html .= 'color: "black",';
			$html .= 'borderWidth: 0,';
			$html .= 'style:{';
			$html .= 'textShadow:false,';
			$html .= 'fontSize:15,';
			$html .= '}';
			$html .='}';

			$html .='},';
			}
			$html .=']';
			
			$html .= '});';
			
			$html .= '$(".highcharts-title > tspan").css("display", "none")';
			
		$html .= '</script>';
		
		return $html;
		
	}
	
	
	//Método para ordenar arreglo multidimensional
	function array_sort($array, $on, $order=SORT_ASC){

		$new_array = array();
		$sortable_array = array();
	
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
	
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
	
			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
	
		return $new_array;
	}
	
}