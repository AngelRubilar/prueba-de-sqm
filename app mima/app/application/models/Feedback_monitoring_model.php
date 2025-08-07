<?php

class Feedback_monitoring_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evaluaciones_feedback';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
		
		$array_detalle = array();		
		$where = "";

		$id_feedback_matrix_config = get_array_value($options, "id_feedback_matrix_config");
		$proposito_visita = get_array_value($options, "proposito_visita");
		$id_responsable = get_array_value($options, "id_responsable");
		$id_proyecto = get_array_value($options, "id_proyecto");
		$puede_editar = get_array_value($options, "puede_editar");
		
		
		//var_dump("id_responsable: " . $id_feedback_matrix_config);
		//var_dump("proposito_visita: " . $proposito_visita);
		//var_dump("id_responsable: " . $id_responsable);
		//exit();
		
		//SI SE FILTRA SOLO POR PROPOSITO VISITA
		if($proposito_visita && !$id_responsable){

			$valores_feedback_proposito_visita = $this->Values_feedback_model->get_all_where(array(
				"id_feedback_matrix_config" => $id_feedback_matrix_config, 
				"proposito_visita" => $proposito_visita,
				"requires_monitoring" => 1,
				"deleted" => 0
			))->result_array();
			
			//buscar evaluaciones que pertenezcan a los feedback que tengan proposito_visita = $proposito_visita
			$options_evaluation = array();
			if($valores_feedback_proposito_visita){
				foreach($valores_feedback_proposito_visita as $valor_feedback){
					$options_evaluation[] = $valor_feedback["id"];
				}
				$evaluaciones_feedback_proposito_visita = $this->get_evaluations_of_feedback_array($options_evaluation)->result_array();
			}

			if($evaluaciones_feedback_proposito_visita){
				
				$array_ids_val_comp = array();
				
				foreach($evaluaciones_feedback_proposito_visita as $ece){
					
					$valor_feedback = $this->Values_feedback_model->get_one($ece["id_valor_feedback"]);
					$responsable = $this->Users_model->get_one($valor_feedback->responsable);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ece["id"], "deleted" => 0))->result_array();					
					//$modal_evidencias = modal_anchor(get_uri("compromises_compliance_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ece["id"]));
					
					$evidencias = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $ece["id"], "deleted" => 0))->result_array();
					$modal_evidencias = modal_anchor(get_uri("feedback_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ece["id"]));

					$array_ce = array();
					$array_ce[] = $valor_feedback->id;
					$array_ce[] = get_date_format($valor_feedback->fecha, $id_proyecto);
					$array_ce[] = $valor_feedback->nombre;
					$array_ce[] = lang($valor_feedback->proposito_visita);
					$array_ce[] = $nombre_responsable;
					
					$tooltip_respuesta = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ece["respuesta"].'"><i class="fas fa-info-circle fa-lg"></i></span>';	
					$array_ce[] = ((!$ece["respuesta"]) || $ece["respuesta"] == "") ? "-" : $tooltip_respuesta;
					$array_ce[] = ((!$ece["estado_respuesta"]) || $ece["estado_respuesta"] == "") ? "-" : $ece["estado_respuesta"];
					$array_ce[] = ($evidencias) ? $modal_evidencias : "-";
					$array_ce[] = ($ece["modified"]) ? get_date_format($ece["modified"], $id_proyecto) : get_date_format($ece["created"], $id_proyecto);
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_proposito_visita" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "feedback_monitoring/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_proposito_visita" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					 
					$actions =  modal_anchor(get_uri("feedback_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-id_evaluacion" => $ece["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-id_evaluacion" => $ece["id"], "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;					
					$array_detalle[] = $array_ce;
					$array_ids_val_comp[] = $ece["id_valor_feedback"];
					
				}
				
				foreach($valores_feedback_proposito_visita as $ce){
					if(in_array($ce["id"], $array_ids_val_comp)){ continue; }
					
					$array_ce = array();
					$array_ce[] = $ce["id"];
					$array_ce[] = get_date_format($ce["fecha"], $id_proyecto);
					$array_ce[] = $ce["nombre"];
					$array_ce[] = lang($ce["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($ce["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_ce[] = $nombre_responsable; 
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-";
					$array_ce[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_responsable" => $ce["responsable"], "data-post-proposito_visita" => $ce["proposito_visita"], "data-post-id_valor_feedback" => $ce["id"], "data-post-select_proposito_visita" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_responsable" => $ce["responsable"], "data-post-proposito_visita" => $ce["proposito_visita"], "data-post-id_valor_feedback" => $ce["id"], "data-post-select_proposito_visita" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					 
					$actions =  modal_anchor(get_uri("feedback_monitoring/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-id_responsable" => $ce["responsable"], "data-post-proposito_visita" => $ce["proposito_visita"], "data-post-id_valor_feedback" => $ce["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_responsable" => $ce["id"], "data-post-proposito_visita" => $proposito_visita, "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;
					
					$array_detalle[] = $array_ce;
	
				}
						
			} else {

				foreach($valores_feedback_proposito_visita as $ce){
					$array_ce = array();
					
					//$array_ce[] = $ce["id"];
					$array_ce[] = $ce["id"];
					$array_ce[] = get_date_format($ce["fecha"], $id_proyecto);
					$array_ce[] = $ce["nombre"];
					$array_ce[] = lang($ce["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($ce["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_ce[] = $nombre_responsable; 
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-";
					$array_ce[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_responsable" => $ce["responsable"], "data-post-proposito_visita" => $ce["proposito_visita"], "data-post-id_valor_feedback" => $ce["id"], "data-post-select_proposito_visita" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_responsable" => $ce["responsable"], "data-post-proposito_visita" => $ce["proposito_visita"], "data-post-id_valor_feedback" => $ce["id"], "data-post-select_proposito_visita" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					 
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-id_responsable" => $ce["responsable"], "data-post-proposito_visita" => $ce["proposito_visita"], "data-post-id_valor_feedback" => $ce["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_responsable" => $ce["id"], "data-post-proposito_visita" => $proposito_visita, "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;
					
					$array_detalle[] = $array_ce; 
				}
				
			}
			
		} 
		
		//SI SE FILTRA SOLO POR RESPONSABLE
		if(!$proposito_visita && $id_responsable){	
			
			/*
			$nombre_compromiso = $this->Values_compromises_model->get_one($id_responsable)->nombre_compromiso;
			$evaluaciones_cumplimiento_compromiso = $this->get_all_where(array("id_responsable" => $id_responsable, "deleted" => 0))->result_array();
			$evaluados_compromiso = $this->Evaluated_compromises_model->get_all_where(array("id_compromiso" => $id_compromiso_proyecto, "deleted" => 0))->result_array();
			*/

			$valores_feedback_responsable = $this->Values_feedback_model->get_all_where(array(
				"id_feedback_matrix_config" => $id_feedback_matrix_config, 
				"responsable" => $id_responsable, 
				"requires_monitoring" => 1,
				"deleted" => 0
			))->result_array();
			
			//buscar evaluaciones que pertenezcan a los feedback que tengan id_responsable = $id_responsable
			$options_evaluation = array();
			if($valores_feedback_responsable){
				foreach($valores_feedback_responsable as $valor_feedback){
					$options_evaluation[] = $valor_feedback["id"];
				}
				$evaluaciones_feedback_responsable = $this->get_evaluations_of_feedback_array($options_evaluation)->result_array();
			}

			if($evaluaciones_feedback_responsable){
				
				$array_ids_evaluados = array();
				
				foreach($evaluaciones_feedback_responsable as $ecc){
					
					$valor_feedback = $this->Values_feedback_model->get_one($ecc["id_valor_feedback"]);
					$responsable = $this->Users_model->get_one($valor_feedback->responsable);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ecc["id"], "deleted" => 0))->result_array();				
					//$modal_evidencias = modal_anchor(get_uri("feedback_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ecc["id"]));
					
					$evidencias = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $ecc["id"], "deleted" => 0))->result_array();
					$modal_evidencias = modal_anchor(get_uri("feedback_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ecc["id"]));
					
					$array_ce = array();
					$array_ce[] = $valor_feedback->id;
					$array_ce[] = get_date_format($valor_feedback->fecha, $id_proyecto);
					$array_ce[] = $valor_feedback->nombre;
					$array_ce[] = lang($valor_feedback->proposito_visita);
					$array_ce[] = $nombre_responsable;
					
					$tooltip_respuesta = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ecc["respuesta"].'"><i class="fas fa-info-circle fa-lg"></i></span>';	
					$array_ce[] = ((!$ecc["respuesta"]) || $ecc["respuesta"] == "") ? "-" : $tooltip_respuesta;
					$array_ce[] = ((!$ecc["estado_respuesta"]) || $ecc["estado_respuesta"] == "") ? "-" : $ecc["estado_respuesta"];
					$array_ce[] = ($evidencias) ? $modal_evidencias : "-";
					$array_ce[] = ($ecc["modified"]) ? get_date_format($ecc["modified"], $id_proyecto) : get_date_format($ecc["created"], $id_proyecto);
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					 
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-id_evaluacion" => $ecc["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;					
					$array_detalle[] = $array_ce;
					
					$array_ids_evaluados[] = $ecc["id_valor_feedback"];
					
				}
				
				foreach($valores_feedback_responsable as $ec){
					
					if(in_array($ec["id"], $array_ids_evaluados)){ continue; }
					
					$array_ec = array();
					//$array_ec[] = $id_valor_compromiso;
					$array_ec[] = $ec["id"];
					$array_ec[] = get_date_format($ec["fecha"], $id_proyecto);
					$array_ec[] = $ec["nombre"];
					$array_ec[] = lang($ec["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($ec["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_ec[] = $nombre_responsable;
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $ec["proposito_visita"], "data-post-id_responsable" => $ec["responsable"], "data-post-id_valor_feedback" => $ec["id"], "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $ec["proposito_visita"], "data-post-id_responsable" => $ec["responsable"], "data-post-id_valor_feedback" => $ec["id"], "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-proposito_visita" => $ec["proposito_visita"], "data-post-id_responsable" => $ec["responsable"], "data-post-id_valor_feedback" => $ec["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-proposito_visita" => $ec["id"], "data-post-id_responsable" => $id_responsable, "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
				}			
				
			} else {
				
				foreach($valores_feedback_responsable as $ec){
					
					$array_ec = array();
					
					//$array_ec[] = $id_valor_compromiso;
					$array_ec[] = $ec["id"];
					$array_ec[] = get_date_format($ec["fecha"], $id_proyecto);
					$array_ec[] = $ec["nombre"];
					$array_ec[] = lang($ec["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($ec["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_ec[] = $nombre_responsable;
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
		
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $ec["proposito_visita"], "data-post-id_responsable" => $ec["responsable"], "data-post-id_valor_feedback" => $ec["id"], "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $ec["proposito_visita"], "data-post-id_responsable" => $ec["responsable"], "data-post-id_valor_feedback" => $ec["id"], "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-proposito_visita" => $ec["proposito_visita"], "data-post-id_responsable" => $ec["responsable"], "data-post-id_valor_feedback" => $ec["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-proposito_visita" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
				}
				
			}
					
		}

		//SI SE FILTRA POR PROPOSITO VISITA Y POR RESPONSABLE
		//if($id_evaluado && $id_valor_compromiso){
		if($proposito_visita && $id_responsable){
			
			$valores_feedback = $this->Values_feedback_model->get_all_where(array(
				"proposito_visita" => $proposito_visita, 
				"responsable" => $id_responsable,
				"requires_monitoring" => 1,
				"deleted" => 0
			))->result_array();
			
			//buscar evaluaciones que pertenezcan a los feedback que tengan proposito_visita = $proposito_visita y responsable = $id_responsable
			$options_evaluation = array();
			if($valores_feedback){
				foreach($valores_feedback as $valor_feedback){
					$options_evaluation[] = $valor_feedback["id"];
				}
				$evaluaciones_feedback = $this->get_evaluations_of_feedback_array($options_evaluation)->result_array();
			}
			
			if($evaluaciones_feedback){
				
				foreach($evaluaciones_feedback as $ev_cump){
					
					$valor_feedback = $this->Values_feedback_model->get_one($ev_cump["id_valor_feedback"]);
					$responsable = $this->Users_model->get_one($valor_feedback->responsable);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;

					//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ev_cump["id"], "deleted" => 0))->result_array();
					//$modal_evidencias = modal_anchor(get_uri("compromises_compliance_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ev_cump["id"]));
					$evidencias = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $ev_cump["id"], "deleted" => 0))->result_array();
					$modal_evidencias = modal_anchor(get_uri("feedback_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ev_cump["id"]));
	
					$array_ec = array();
					$array_ec[] = $valor_feedback->id;
					$array_ec[] = get_date_format($valor_feedback->fecha, $id_proyecto);
					$array_ec[] = $valor_feedback->nombre;
					$array_ec[] = lang($valor_feedback->proposito_visita);
					$array_ec[] = $nombre_responsable;
					
					$tooltip_respuesta = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ev_cump["respuesta"].'"><i class="fas fa-info-circle fa-lg"></i></span>';	
					$array_ec[] = ((!$ev_cump["respuesta"]) || $ev_cump["respuesta"] == "") ? "-" : $tooltip_respuesta;				 
					$array_ec[] = ((!$ev_cump["estado_respuesta"]) || $ev_cump["estado_respuesta"] == "") ? "-" : $ev_cump["estado_respuesta"];
					$array_ec[] = ($evidencias) ? $modal_evidencias : "-";
					$array_ec[] = ($ev_cump["modified"]) ? get_date_format($ev_cump["modified"], $id_proyecto) : get_date_format($ev_cump["created"], $id_proyecto);
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
					
					$array_ids_feedback[] = $valor_feedback->id;
				}
				
				
				foreach($valores_feedback as $vf){
					
					if(in_array($vf["id"], $array_ids_feedback)){ continue; }
					
					$array_ec = array();
					$array_ec[] = $vf["id"];
					$array_ec[] = get_date_format($vf["fecha"], $id_proyecto);
					$array_ec[] = $vf["nombre"];
					$array_ec[] = lang($vf["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($vf["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_ec[] = $nombre_responsable; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					$array_ec[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"]))
								. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-proposito_visita" => $proposito_visita, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ec[] = $actions;
					
					$array_detalle[] = $array_ec;
					
				}

				
			} else {

				foreach($valores_feedback as $vf){

					$array_final = array();
				
					//$array_final[] = $id_compromiso;
					$array_final[] = $vf["id"];
					$array_final[] = get_date_format($vf["fecha"], $id_proyecto);
					$array_final[] = $vf["nombre"];
					$array_final[] = lang($vf["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($vf["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_final[] = $nombre_responsable; 
					$array_final[] = "-"; 
					$array_final[] = "-"; 
					$array_final[] = "-";
					$array_final[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"]))
								. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-proposito_visita" => $proposito_visita, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_final[] = $actions;
					
					$array_detalle[] = $array_final;

				}
	
			}

		}
		

		if(!$proposito_visita && !$id_responsable){
			
			$valores_feedback = $this->Values_feedback_model->get_all_where(array(
				"requires_monitoring" => 1,
				"deleted" => 0
			))->result_array();
			
			//buscar evaluaciones que pertenezcan a los feedback que tengan proposito_visita = $proposito_visita y responsable = $id_responsable
			$options_evaluation = array();
			if($valores_feedback){
				foreach($valores_feedback as $valor_feedback){
					$options_evaluation[] = $valor_feedback["id"];
				}
				$evaluaciones_feedback = $this->get_evaluations_of_feedback_array($options_evaluation)->result_array();
			}
			
			if($evaluaciones_feedback){
				
				foreach($evaluaciones_feedback as $ev_cump){
					
					$valor_feedback = $this->Values_feedback_model->get_one($ev_cump["id_valor_feedback"]);
					$responsable = $this->Users_model->get_one($valor_feedback->responsable);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;

					//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ev_cump["id"], "deleted" => 0))->result_array();
					//$modal_evidencias = modal_anchor(get_uri("compromises_compliance_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ev_cump["id"]));
					$evidencias = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $ev_cump["id"], "deleted" => 0))->result_array();
					$modal_evidencias = modal_anchor(get_uri("feedback_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ev_cump["id"]));
	
					$array_ec = array();
					$array_ec[] = $valor_feedback->id;
					$array_ec[] = get_date_format($valor_feedback->fecha, $id_proyecto);
					$array_ec[] = $valor_feedback->nombre;
					$array_ec[] = lang($valor_feedback->proposito_visita);
					$array_ec[] = $nombre_responsable;
					
					$tooltip_respuesta = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ev_cump["respuesta"].'"><i class="fas fa-info-circle fa-lg"></i></span>';	
					$array_ec[] = ((!$ev_cump["respuesta"]) || $ev_cump["respuesta"] == "") ? "-" : $tooltip_respuesta;				 
					$array_ec[] = ((!$ev_cump["estado_respuesta"]) || $ev_cump["estado_respuesta"] == "") ? "-" : $ev_cump["estado_respuesta"];
					$array_ec[] = ($evidencias) ? $modal_evidencias : "-";
					$array_ec[] = ($ev_cump["modified"]) ? get_date_format($ev_cump["modified"], $id_proyecto) : get_date_format($ev_cump["created"], $id_proyecto);
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"]))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
					
					$array_ids_feedback[] = $valor_feedback->id;
				}
				
				
				foreach($valores_feedback as $vf){
					
					if(in_array($vf["id"], $array_ids_feedback)){ continue; }
					
					$array_ec = array();
					$array_ec[] = $vf["id"];
					$array_ec[] = get_date_format($vf["fecha"], $id_proyecto);
					$array_ec[] = $vf["nombre"];
					$array_ec[] = lang($vf["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($vf["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_ec[] = $nombre_responsable; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					$array_ec[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"]))
								. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-proposito_visita" => $proposito_visita, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ec[] = $actions;
					
					$array_detalle[] = $array_ec;
					
				}

				
			} else {

				foreach($valores_feedback as $vf){

					$array_final = array();
				
					//$array_final[] = $id_compromiso;
					$array_final[] = $vf["id"];
					$array_final[] = get_date_format($vf["fecha"], $id_proyecto);
					$array_final[] = $vf["nombre"];
					$array_final[] = lang($vf["proposito_visita"]);
					
					$responsable = $this->Users_model->get_one($vf["responsable"]);
					$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
					
					$array_final[] = $nombre_responsable; 
					$array_final[] = "-"; 
					$array_final[] = "-"; 
					$array_final[] = "-";
					$array_final[] = "-";
					
					if($puede_editar != 3){
						$boton_editar = modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1"));
					} else {
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"], "data-post-select_proposito_visita" => "1", "data-post-select_responsable" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					}
					
					$actions =  modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback_evaluation'), "data-post-proposito_visita" => $vf["proposito_visita"], "data-post-id_responsable" => $vf["responsable"], "data-post-id_valor_feedback" => $vf["id"]))
								. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-proposito_visita" => $proposito_visita, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_final[] = $actions;
					
					$array_detalle[] = $array_final;

				}
	
			}

		}
		
		
		return $array_detalle;

	}
	
	function get_evaluations_of_feedback_array($options_evaluation){
		
		$evaluaciones_feedback_table = $this->db->dbprefix('evaluaciones_feedback');
		
		$options_evaluation = implode(', ', $options_evaluation);

		$sql = "SELECT $evaluaciones_feedback_table.* ";
		$sql .= " FROM $evaluaciones_feedback_table";
		$sql .= " WHERE $evaluaciones_feedback_table.id_valor_feedback IN ($options_evaluation)";

		return $this->db->query($sql);

	}
		
}
