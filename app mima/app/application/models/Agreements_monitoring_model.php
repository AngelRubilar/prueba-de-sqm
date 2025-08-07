<?php

class Agreements_monitoring_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evaluaciones_acuerdos';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
		
		$array_detalle = array();
		
		$where = "";
        $id_agreements_matrix = get_array_value($options, "id_agreements_matrix");
		$id_valor_acuerdo = get_array_value($options, "id_valor_acuerdo");
		$id_stakeholder = get_array_value($options, "id_stakeholder");
		$id_proyecto = get_array_value($options, "id_proyecto");
		$puede_editar = get_array_value($options, "puede_editar");
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		//SI SE FILTRA SOLO POR VALOR ACUERDO
		if($id_valor_acuerdo && !$id_stakeholder){
			
			//Devolver una fila por cada stakeholder del acuerdo
			$valor_acuerdo = $this->Values_agreements_model->get_one($id_valor_acuerdo);
			$array_sh_acuerdo = json_decode($valor_acuerdo->stakeholders);

			$evaluaciones_acuerdo = $this->Agreements_monitoring_model->get_all_where(array("id_valor_acuerdo" => $id_valor_acuerdo, "deleted" => 0))->result_array();
		
			if($evaluaciones_acuerdo){
				
				$array_ids_sh = array();
				
				foreach($evaluaciones_acuerdo as $ea){

					$array_ea = array();
					
					$array_ea[] = $valor_acuerdo->id;
					$array_ea[] = $valor_acuerdo->codigo;
					$array_ea[] = $valor_acuerdo->nombre_acuerdo;

					//GESTOR
					$gestor = $this->Users_model->get_one($valor_acuerdo->gestor);
					$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
					
					//STAKEHOLDER
					$stakeholder = $this->Values_stakeholders_model->get_one($ea["id_stakeholder"]);
					$array_ea[] = $stakeholder->nombre;

					$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($ea["estado_tramitacion"]);
					$estado_actividades = $this->Communities_evaluation_status_model->get_one($ea["estado_actividades"]);
					$estado_financiero = $this->Communities_evaluation_status_model->get_one($ea["estado_financiero"]);
					
					$html_estado_tramitacion = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_tramitacion .= '<div style="background-color:'.$estado_tramitacion->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_tramitacion .= $estado_tramitacion->nombre_estado;
					$html_estado_tramitacion .= '</div>';
					
					$html_estado_actividades = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_actividades .= '<div style="background-color:'.$estado_actividades->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_actividades .= $estado_actividades->nombre_estado;
					$html_estado_actividades .= '</div>';
					
					$html_estado_financiero = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_financiero .= '<div style="background-color:'.$estado_financiero->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_financiero .= $estado_financiero->nombre_estado;
					$html_estado_financiero .= '</div>';
					
					//$array_ea[] = $this->Communities_evaluation_status_model->get_one($ea["estado_tramitacion"])->nombre_estado;
					//$array_ea[] = $this->Communities_evaluation_status_model->get_one($ea["estado_actividades"])->nombre_estado;
					//$array_ea[] = $this->Communities_evaluation_status_model->get_one($ea["estado_financiero"])->nombre_estado;
					
					$array_ea[] = $html_estado_tramitacion;
					$array_ea[] = $html_estado_actividades;
					$array_ea[] = $html_estado_financiero;
					
					$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ea["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
					$array_ea[] = ((!$ea["observaciones"]) || $ea["observaciones"] == "") ? "-" : $tooltip_observaciones;
	
					//EVIDENCIAS
					$evidencias = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" => $ea["id"], "deleted" => 0))->result_array();
					$modal_evidencias = modal_anchor(get_uri("agreements_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion_acuerdo" => $ea["id"]));
					$evidencias_acuerdo = ($evidencias) ? $modal_evidencias : "-";
					$array_ea[] = $evidencias_acuerdo;
					
					$array_ea[] = ($ea["modified"]) ? get_date_format($ea["modified"], $id_proyecto) : get_date_format($ea["created"], $id_proyecto);
	
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $ea["id"], "data-post-value_agreement_id" => $ea["id_valor_acuerdo"], "data-post-id_stakeholder" => $ea["id_stakeholder"]));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $ea["id"], "data-post-value_agreement_id" => $ea["id_valor_acuerdo"], "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-id" => $ea["id"]))
								. $boton_editar;
					
					$array_ea[] = $actions;
					$array_detalle[] = $array_ea;
					$array_ids_sh[] = $ea["id_stakeholder"];

				}
				
				foreach($array_sh_acuerdo as $sh){
					
					if(in_array($sh, $array_ids_sh)){ continue; }
					
					$array_ea = array();
					$array_ea[] = $valor_acuerdo->id;
					$array_ea[] = $valor_acuerdo->codigo;
					$array_ea[] = $valor_acuerdo->nombre_acuerdo;

					//GESTOR
					$gestor = $this->Users_model->get_one($valor_acuerdo->gestor);
					$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
					
					//STAKEHOLDER
					$stakeholder = $this->Values_stakeholders_model->get_one($sh);
					$array_ea[] = $stakeholder->nombre;
					
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					
					//
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $ea["id_valor_acuerdo"], "data-post-id_stakeholder" => $sh));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $ea["id_valor_acuerdo"], "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "data-post-id_stakeholder" => $sh))
								. $boton_editar;
					
					$array_ea[] = $actions;
					//
					
					//$array_ea[] = "botones (sin evaluacion)";
					
					$array_detalle[] = $array_ea;
				}
				
			} else {
				
				//var_dump("sin evaluaciones");
				foreach($array_sh_acuerdo as $sh){
					
					$array_ea = array();
					$array_ea[] = $valor_acuerdo->id;
					$array_ea[] = $valor_acuerdo->codigo;
					$array_ea[] = $valor_acuerdo->nombre_acuerdo;

					//GESTOR
					$gestor = $this->Users_model->get_one($valor_acuerdo->gestor);
					$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
					
					//STAKEHOLDER
					$stakeholder = $this->Values_stakeholders_model->get_one($sh);
					$array_ea[] = $stakeholder->nombre;
					
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					
					//
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "data-post-id_stakeholder" => $sh));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "data-post-id_stakeholder" => $sh))
								. $boton_editar;
					
					$array_ea[] = $actions;
					//
					
					//$array_ea[] = "botones (sin evaluacion)";
					
					$array_detalle[] = $array_ea;
					
				}
				
				
			}
			
		}
		
		//SI SE FILTRA SOLO POR STAKEHOLDER
		if(!$id_valor_acuerdo && $id_stakeholder){
			
			$valores_acuerdos = $this->Values_agreements_model->get_agreements_that_have_stakeholder($id_stakeholder, $id_agreements_matrix)->result_array();
			
			foreach($valores_acuerdos as $va){
				
				$evaluacion_acuerdo = $this->Agreements_monitoring_model->get_one_where(array("id_valor_acuerdo" => $va["id"], "id_stakeholder" => $id_stakeholder, "deleted" => 0));
				
				if($evaluacion_acuerdo->id){
				
					$array_ea = array();
					
					$array_ea[] = $va["id"];
					$array_ea[] = $va["codigo"];
					$array_ea[] = $va["nombre_acuerdo"];
					
					//GESTOR
					$gestor = $this->Users_model->get_one($va["gestor"]);
					$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
					
					//STAKEHOLDER
					$stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
					$array_ea[] = $stakeholder->nombre;
					
					$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($evaluacion_acuerdo->estado_tramitacion);
					$estado_actividades = $this->Communities_evaluation_status_model->get_one($evaluacion_acuerdo->estado_actividades);
					$estado_financiero = $this->Communities_evaluation_status_model->get_one($evaluacion_acuerdo->estado_financiero);
					
					$html_estado_tramitacion = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_tramitacion .= '<div style="background-color:'.$estado_tramitacion->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_tramitacion .= $estado_tramitacion->nombre_estado;
					$html_estado_tramitacion .= '</div>';
					
					$html_estado_actividades = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_actividades .= '<div style="background-color:'.$estado_actividades->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_actividades .= $estado_actividades->nombre_estado;
					$html_estado_actividades .= '</div>';
					
					$html_estado_financiero = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_financiero .= '<div style="background-color:'.$estado_financiero->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_financiero .= $estado_financiero->nombre_estado;
					$html_estado_financiero .= '</div>';
					
					$array_ea[] = $html_estado_tramitacion;
					$array_ea[] = $html_estado_actividades;
					$array_ea[] = $html_estado_financiero;
					
					$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$evaluacion_acuerdo->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
					$array_ea[] = ((!$evaluacion_acuerdo->observaciones) || $evaluacion_acuerdo->observaciones == "") ? "-" : $tooltip_observaciones;
	
					//EVIDENCIAS
					$evidencias = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" => $evaluacion_acuerdo->id, "deleted" => 0))->result_array();
					$modal_evidencias = modal_anchor(get_uri("agreements_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion_acuerdo" => $evaluacion_acuerdo->id));
					$evidencias_acuerdo = ($evidencias) ? $modal_evidencias : "-";
					$array_ea[] = $evidencias_acuerdo;
					
					$array_ea[] = ($evaluacion_acuerdo->modified) ?get_date_format($evaluacion_acuerdo->modified, $id_proyecto) : get_date_format($evaluacion_acuerdo->created, $id_proyecto);
	
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $evaluacion_acuerdo->id, "data-post-value_agreement_id" => $evaluacion_acuerdo->id_valor_acuerdo, "data-post-id_stakeholder" => $evaluacion_acuerdo->id_stakeholder));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $evaluacion_acuerdo->id, "data-post-value_agreement_id" => $evaluacion_acuerdo->id_valor_acuerdo, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-id" => $evaluacion_acuerdo->id))
								. $boton_editar;
					
					$array_ea[] = $actions;
					$array_detalle[] = $array_ea;
					$array_ids_sh[] = $ea["id_stakeholder"];
					//var_dump($array_ea);
					
				
				} else {
				
					//var_dump("no evaluation yet");
					
					$array_ea = array();
					
					$array_ea[] = $va["id"];
					$array_ea[] = $va["codigo"];
					$array_ea[] = $va["nombre_acuerdo"];
					
					//GESTOR
					$gestor = $this->Users_model->get_one($va["gestor"]);
					$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
					
					//STAKEHOLDER
					$stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
					$array_ea[] = $stakeholder->nombre;
					
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					
					//
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $va["id"], "data-post-id_stakeholder" => $id_stakeholder));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $va["id"], "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-value_agreement_id" => $va["id"], "data-post-id_stakeholder" => $id_stakeholder))
								. $boton_editar;
					
					$array_ea[] = $actions;
					//
					
					//$array_ea[] = "botones (sin evaluacion)";
					
					$array_detalle[] = $array_ea;
					
				}
				
				
				
			}
			
		}
		
		//SI SE FILTRA POR ACUERDO Y STAKEHOLDER
		if($id_valor_acuerdo && $id_stakeholder){
			
			//PENDIENTE
			//Listar UNA fila del stakeholder seleccionado dentro del acuerdo seleccionado
			$valor_acuerdo = $this->Values_agreements_model->get_one($id_valor_acuerdo);
			$valor_stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
			
			$evaluacion = $this->Agreements_monitoring_model->get_one_where(array("id_valor_acuerdo" => $id_valor_acuerdo, "id_stakeholder" => $id_stakeholder, "deleted" => 0));

			if($evaluacion->id){
				
				$array_ea = array();
					
				$array_ea[] = $valor_acuerdo->id;
				$array_ea[] = $valor_acuerdo->codigo;
				$array_ea[] = $valor_acuerdo->nombre_acuerdo;
				
				//GESTOR
				$gestor = $this->Users_model->get_one($valor_acuerdo->gestor);
				$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
				
				//STAKEHOLDER
				$array_ea[] = $valor_stakeholder->nombre;

				$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($evaluacion->estado_tramitacion);
				$estado_actividades = $this->Communities_evaluation_status_model->get_one($evaluacion->estado_actividades);
				$estado_financiero = $this->Communities_evaluation_status_model->get_one($evaluacion->estado_financiero);
				
				$html_estado_tramitacion = '<div class="text-center" style="text-align: -webkit-center;">';
				$html_estado_tramitacion .= '<div style="background-color:'.$estado_tramitacion->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
				$html_estado_tramitacion .= $estado_tramitacion->nombre_estado;
				$html_estado_tramitacion .= '</div>';
				
				$html_estado_actividades = '<div class="text-center" style="text-align: -webkit-center;">';
				$html_estado_actividades .= '<div style="background-color:'.$estado_actividades->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
				$html_estado_actividades .= $estado_actividades->nombre_estado;
				$html_estado_actividades .= '</div>';
				
				$html_estado_financiero = '<div class="text-center" style="text-align: -webkit-center;">';
				$html_estado_financiero .= '<div style="background-color:'.$estado_financiero->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
				$html_estado_financiero .= $estado_financiero->nombre_estado;
				$html_estado_financiero .= '</div>';
				
				$array_ea[] = $html_estado_tramitacion;
				$array_ea[] = $html_estado_actividades;
				$array_ea[] = $html_estado_financiero;
				
				$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$evaluacion->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
				$array_ea[] = ((!$evaluacion->observaciones) || $evaluacion->observaciones == "") ? "-" : $tooltip_observaciones;
				
				//EVIDENCIAS
				$evidencias = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" => $evaluacion->id, "deleted" => 0))->result_array();
				$modal_evidencias = modal_anchor(get_uri("agreements_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion_acuerdo" => $evaluacion->id));
				$evidencias_acuerdo = ($evidencias) ? $modal_evidencias : "-";
				$array_ea[] = $evidencias_acuerdo;
				
				$array_ea[] = ($evaluacion->modified) ?get_date_format($evaluacion->modified, $id_proyecto) : get_date_format($evaluacion->created, $id_proyecto);
				
				if($puede_editar != 3){
					$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $evaluacion->id, "data-post-value_agreement_id" => $evaluacion->id_valor_acuerdo, "data-post-id_stakeholder" => $evaluacion->id_stakeholder));
				} else {
					$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $evaluacion->id, "data-post-value_agreement_id" => $evaluacion->id_valor_acuerdo, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
				}
				
				$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-id" => $evaluacion->id))
							. $boton_editar;
				
				$array_ea[] = $actions;
				$array_detalle[] = $array_ea;
					
			} else {
				
				$stakeholders_de_acuerdo = json_decode($valor_acuerdo->stakeholders);
				//Si no existe una evaluacion pero el acuerdo tiene al stakeholder que no tiene evaluación:
				if(in_array($id_stakeholder, $stakeholders_de_acuerdo)){
				
					$array_ea = array();
					
					$array_ea[] = $valor_acuerdo->id;
					$array_ea[] = $valor_acuerdo->codigo;
					$array_ea[] = $valor_acuerdo->nombre_acuerdo;
					
					//GESTOR
					$gestor = $this->Users_model->get_one($valor_acuerdo->gestor);
					$array_ea[] = $gestor->first_name . " " . $gestor->last_name;
					
					//STAKEHOLDER
					$array_ea[] = $valor_stakeholder->nombre;
					
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					$array_ea[] = "-";
					
					//
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "data-post-id_stakeholder" => $id_stakeholder));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("agreements_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-value_agreement_id" => $id_valor_acuerdo, "data-post-id_stakeholder" => $id_stakeholder))
								. $boton_editar;
					
					$array_ea[] = $actions;
					//
					
					//$array_ea[] = "botones (sin evaluacion)";
					
					$array_detalle[] = $array_ea;
					

				}
				
			}
			
		}
		
		
		return $array_detalle;
		
	}
	
	/*
	function get_details($options = array()){
		
		$agreements_evaluations_table = $this->db->dbprefix('evaluaciones_acuerdos');
		
		$id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $agreements_evaluations_table.id = $id";
        }

        $value_agreement_id = get_array_value($options, "id_valor_acuerdo");
        if ($value_agreement_id) {
            $where .= " AND $agreements_evaluations_table.id_valor_acuerdo = $value_agreement_id";
        }
		
		$id_stakeholder = get_array_value($options, "id_stakeholder");
        if ($id_stakeholder) {
            $where .= " AND $agreements_evaluations_table.id_stakeholder = $id_stakeholder";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $agreements_evaluations_table.*";
		$sql .= " FROM $agreements_evaluations_table";
		$sql .= " WHERE $agreements_evaluations_table.deleted = 0";
		$sql .= "$where";
		
		return $this->db->query($sql);
		
	}
	*/
	function get_details_no_data($options = array()){
		
		$value_agreement_id = get_array_value($options, "id_valor_acuerdo");
        if ($value_agreement_id) {
            //$where .= " AND $agreements_evaluations_table.id_valor_acuerdo = $value_agreement_id";
			$value_agreement = $this->Values_agreements_model->get_one($value_agreement_id);
        }
		
		$id_stakeholder = get_array_value($options, "id_stakeholder");
        if ($id_stakeholder) {
            //$where .= " AND $agreements_evaluations_table.id_stakeholder = $id_stakeholder";
			$stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
        }
		
		//GESTOR
		$gestor = $this->Users_model->get_one($value_agreement->gestor);
		$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
		
		$result = array(
			$value_agreement->id,
			$value_agreement->codigo, 
			$value_agreement->nombre_acuerdo,
			$nombre_gestor,
			$stakeholder->nombre,
			"-",
			"-",
			"-",
			"-",
			"-",
			"-",
		);
		
		$result[] = modal_anchor(get_uri("agreements_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-value_agreement_id" => $value_agreement_id, "data-post-id_stakeholder" => $id_stakeholder))
				    . modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-value_agreement_id" => $value_agreement_id, "data-post-id_stakeholder" => $id_stakeholder));
		
		return $result;
		
	}
	
	function get_total_agreements_by_evaluation_status_for_graphic($options = array()){
		
		$categoria_estado = get_array_value($options, "categoria_estado");
		$id_proyecto = get_array_value($options, "id_proyecto");
		
		$values_agreements_table = $this->db->dbprefix('valores_acuerdos');
		$agreements_evaluations_table = $this->db->dbprefix('evaluaciones_acuerdos');
		$communities_evaluation_status_table = $this->db->dbprefix('estados_evaluacion_comunidades');
		$agreements_matrix_config_table = $this->db->dbprefix('agreements_matrix_config');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $values_agreements_table.id, $values_agreements_table.nombre_acuerdo, $agreements_evaluations_table.id as id_evaluacion,"; 
		$sql .= " $agreements_evaluations_table.$categoria_estado, $communities_evaluation_status_table.nombre_estado,";
		$sql .= " $communities_evaluation_status_table.categoria, COUNT(*) as cantidad_estados";
		$sql .= " FROM $values_agreements_table, $agreements_evaluations_table, $communities_evaluation_status_table, $agreements_matrix_config_table";
		$sql .= " WHERE $agreements_evaluations_table.id_valor_acuerdo = $values_agreements_table.id";
		$sql .= " AND $agreements_evaluations_table.$categoria_estado = $communities_evaluation_status_table.id";
		$sql .= " AND $agreements_matrix_config_table.id = $values_agreements_table.id_agreement_matrix_config";
		$sql .= " AND $agreements_matrix_config_table.id_proyecto = $id_proyecto";
		$sql .= " AND $values_agreements_table.deleted = 0";
		$sql .= " AND $agreements_evaluations_table.deleted = 0";
		$sql .= " AND $communities_evaluation_status_table.deleted = 0";
		$sql .= " AND $agreements_matrix_config_table.deleted = 0";
		$sql .= " GROUP BY $values_agreements_table.id";
		//$sql .= " GROUP BY $categoria_estado";
		
		//var_dump($sql);
		
		return $this->db->query($sql);
		
	}
	
	function get_total_agreements_by_evaluation_status_for_table($options = array()){
		
		$categoria_estado = get_array_value($options, "categoria_estado");
		$id_proyecto = get_array_value($options, "id_proyecto");

		$values_agreements_table = $this->db->dbprefix('valores_acuerdos');
		$agreements_evaluations_table = $this->db->dbprefix('evaluaciones_acuerdos');
		$communities_evaluation_status_table = $this->db->dbprefix('estados_evaluacion_comunidades');
		$agreements_matrix_config_table = $this->db->dbprefix('agreements_matrix_config');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $values_agreements_table.id, $values_agreements_table.nombre_acuerdo, $agreements_evaluations_table.id as id_evaluacion,"; 
		$sql .= " $agreements_evaluations_table.$categoria_estado, $communities_evaluation_status_table.nombre_estado,";
		$sql .= " $communities_evaluation_status_table.categoria, COUNT(*) as cantidad_estados";
		$sql .= " FROM $values_agreements_table, $agreements_evaluations_table, $communities_evaluation_status_table, $agreements_matrix_config_table";
		$sql .= " WHERE $agreements_evaluations_table.id_valor_acuerdo = $values_agreements_table.id";
		$sql .= " AND $agreements_evaluations_table.$categoria_estado = $communities_evaluation_status_table.id";
		
		$sql .= " AND $agreements_matrix_config_table.id = $values_agreements_table.id_agreement_matrix_config";
		$sql .= " AND $agreements_matrix_config_table.id_proyecto = $id_proyecto";
		
		$sql .= " AND $values_agreements_table.deleted = 0";
		$sql .= " AND $agreements_evaluations_table.deleted = 0";
		$sql .= " AND $communities_evaluation_status_table.deleted = 0";
		
		$sql .= " AND $agreements_matrix_config_table.deleted = 0";
		
		$sql .= " GROUP BY $values_agreements_table.id, $categoria_estado";
		//$sql .= " GROUP BY id_evaluacion";
		
		//var_dump($sql);
		
		return $this->db->query($sql);
		
	}
	
	//function get_consolidated_agreements_evaluations($id_agreement_matrix){
	function get_consolidated_agreements_evaluations($options = array()){
		
		$agreements_evaluations_table = $this->db->dbprefix('evaluaciones_acuerdos');
		$values_agreements_table = $this->db->dbprefix('valores_acuerdos');
		$values_stakeholders_table = $this->db->dbprefix('valores_stakeholders');
		$communities_evaluation_status_table = $this->db->dbprefix('estados_evaluacion_comunidades');
		$agreements_matrix_config_table = $this->db->dbprefix('agreements_matrix_config');
		
		$where = "";
		
		$id_agreement_matrix_config = get_array_value($options, "id_agreement_matrix_config");
        if ($id_agreement_matrix_config) {
            $where .= " AND $agreements_matrix_config_table.id = $id_agreement_matrix_config";
        }
		
		$estado_tramitacion = get_array_value($options, "estado_tramitacion");
        if ($estado_tramitacion) {
            $where .= " AND $agreements_evaluations_table.estado_tramitacion = $estado_tramitacion";
        }
		
		$estado_actividades = get_array_value($options, "estado_actividades");
        if ($estado_actividades) {
            $where .= " AND $agreements_evaluations_table.estado_actividades = $estado_actividades";
        }
		
		$estado_financiero = get_array_value($options, "estado_financiero");
        if ($estado_financiero) {
            $where .= " AND $agreements_evaluations_table.estado_financiero = $estado_financiero";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = " SELECT $values_agreements_table.nombre_acuerdo, $values_stakeholders_table.nombre AS nombre_stakeholder,";
		$sql .= " $values_agreements_table.id AS id_acuerdo, $values_stakeholders_table.id AS id_stakeholder,";
		$sql .= " $agreements_evaluations_table.estado_tramitacion, $agreements_evaluations_table.estado_actividades, $agreements_evaluations_table.observaciones,";
		$sql .= " $agreements_evaluations_table.estado_financiero";
		$sql .= " FROM $agreements_evaluations_table, $values_agreements_table, $values_stakeholders_table,";
		$sql .= " $communities_evaluation_status_table, $agreements_matrix_config_table";
		$sql .= " WHERE $agreements_evaluations_table.id_valor_acuerdo = $values_agreements_table.id";
		$sql .= " AND $values_agreements_table.id_agreement_matrix_config = $agreements_matrix_config_table.id";
		$sql .= " AND $agreements_evaluations_table.id_stakeholder = $values_stakeholders_table.id";
		$sql .= " AND $agreements_evaluations_table.deleted = 0";
		$sql .= " AND $values_agreements_table.deleted = 0";
		$sql .= " AND $values_stakeholders_table.deleted = 0";
		$sql .= " AND $communities_evaluation_status_table.deleted = 0";
		$sql .= " AND $agreements_matrix_config_table.deleted = 0";
		$sql .= " $where";
		//$sql .= " AND $agreements_matrix_config_table.id = $id_agreement_matrix";
		$sql .= " GROUP BY $agreements_evaluations_table.id";
		$sql .= " ORDER BY $agreements_evaluations_table.id, $values_agreements_table.id, $values_stakeholders_table.id";
		
		return $this->db->query($sql);
		
	}
		
}
