<?php

class Compromises_compliance_evaluation_reportables_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evaluaciones_cumplimiento_compromisos_reportables';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){

		$array_detalle = array();
		
		$where = "";
        $id_compromiso_proyecto = get_array_value($options, "id_compromiso_proyecto");
		$id_evaluado = get_array_value($options, "id_evaluado");
		$id_valor_compromiso = get_array_value($options, "id_valor_compromiso");
		$id_proyecto = get_array_value($options, "id_proyecto");
		$puede_agregar = get_array_value($options, "puede_agregar");
		$puede_editar = get_array_value($options, "puede_editar");
		
		//SI SE FILTRA SOLO POR EVALUADO
		if($id_evaluado && !$id_valor_compromiso){
			//se trae los compromisos del evaluado (tienen en común el id_compromiso)
			$compromisos_evaluado = $this->Values_compromises_reportables_model->get_all_where(array("id_compromiso" => $id_compromiso_proyecto, "deleted" => 0))->result_array();
			$nombre_evaluado = $this->Plans_reportables_compromises_model->get_one($id_evaluado)->nombre_evaluado;
			$evaluaciones_cumplimiento_evaluado = $this->get_all_where(array("id_evaluado" => $id_evaluado, "deleted" => 0))->result_array();

			if($evaluaciones_cumplimiento_evaluado){
				
				$array_ids_val_comp = array();

				foreach($evaluaciones_cumplimiento_evaluado as $ece){

					$ultima_evaluacion = $this->get_last_evaluation(array("id_evaluado" => $id_evaluado, "id_valor_compromiso" => $ece["id_valor_compromiso"]))->result_array();

					$existen_evaluaciones_con_archivos = FALSE;
					$evaluaciones = $this->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_compromiso" => $ece["id_valor_compromiso"], "deleted" => 0))->result_array();
					foreach($evaluaciones as $evaluacion){
						$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(
							array(
								"id_evaluacion_cumplimiento_compromiso" => $evaluacion["id"], 
								"tipo_evaluacion" => "reportable", 
								"deleted" => 0
							)
						)->result_array();
						if($evidencias){
							$existen_evaluaciones_con_archivos = TRUE;
						} 
					}
					
					if($ece["fecha_evaluacion"] == $ultima_evaluacion[0]["fecha_evaluacion"]){

						$numero_compromiso = $this->Values_compromises_reportables_model->get_one_where(array("id" => $ece["id_valor_compromiso"], "deleted" => 0))->numero_compromiso;
						$nombre_compromiso = $this->Values_compromises_reportables_model->get_one_where(array("id" => $ece["id_valor_compromiso"], "deleted" => 0))->nombre_compromiso;
						
						$accion_cumplimiento_control = $this->Values_compromises_reportables_model->get_one_where(
							array(
								"id" => $ece["id_valor_compromiso"], 
								"deleted" => 0
							)
						)->accion_cumplimiento_control;
						$frecuencia_ejecucion = $this->Values_compromises_reportables_model->get_one_where(
							array(
								"id" => $ece["id_valor_compromiso"], 
								"deleted" => 0
							)
						)->frecuencia_ejecucion;
						
						$responsable = $this->Users_model->get_one($ece["responsable"]);
						$responsable = $responsable->first_name." ".$responsable->last_name;
						$estado = $this->Compromises_compliance_status_model->get_one($ece["id_estados_cumplimiento_compromiso"]);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';
						
						//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ece["id"], "deleted" => 0))->result_array();
						if($existen_evaluaciones_con_archivos){
							$modal_evidencias = modal_anchor(get_uri("compromises_rca_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ece["id"]));					
						} else {
							$modal_evidencias = "-";
						}
						
						$array_ce = array();
						//$array_ce[] = $ece["id_valor_compromiso"];
						$array_ce[] = $numero_compromiso;
						$array_ce[] = $nombre_compromiso;
						$array_ce[] = $nombre_evaluado;
						$array_ce[] = $accion_cumplimiento_control;
						$array_ce[] = $frecuencia_ejecucion;
						$array_ce[] = ($nombre_estado) ? $html_estado : "-";
						//$array_ce[] = ($evidencias) ? $modal_evidencias : "-"; 
						$array_ce[] = $modal_evidencias; 
						
						$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ece["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						
						$array_ce[] = ((!$ece["observaciones"]) || $ece["observaciones"] == "") ? "-" : $tooltip_observaciones; 
						$array_ce[] = $responsable; 
						//$array_ce[] = ($ece["modified"]) ? get_date_format($ece["modified"], $id_proyecto) : get_date_format($ece["created"], $id_proyecto);
						$array_ce[] = ($ece["fecha_evaluacion"]) ? get_date_format($ece["fecha_evaluacion"], $id_proyecto) : "-";
						
						
						if($puede_agregar == 3 && $puede_editar == 3){
							$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
						} else {
							$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1"));	
						}
						 
						$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluacion" => $ece["id"]))
									. $boton_editar;
						//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-id_evaluacion" => $ece["id"], "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
						
						$array_ce[] = $actions;					
						$array_detalle[] = $array_ce;
						$array_ids_val_comp[] = $ece["id_valor_compromiso"];

					} 

				}

				foreach($compromisos_evaluado as $ce){
					if(in_array($ce["id"], $array_ids_val_comp)){ continue; }
					
					$array_ce = array();
					//$array_ce[] = $ce["id"];
					$array_ce[] = $ce["numero_compromiso"];
					$array_ce[] = $ce["nombre_compromiso"];
					$array_ce[] = $nombre_evaluado;
					$array_ce[] = $ce["accion_cumplimiento_control"];
					$array_ce[] = $ce["frecuencia_ejecucion"];
					$array_ce[] = "-";
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'),  "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					}
					 
					$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;
					
					$array_detalle[] = $array_ce;
	
				}
						
			} else {
				
				foreach($compromisos_evaluado as $ce){
					$array_ce = array();
					
					//$array_ce[] = $ce["id"];
					$array_ce[] = $ce["numero_compromiso"];
					$array_ce[] = $ce["nombre_compromiso"];
					$array_ce[] = $nombre_evaluado;
					$array_ce[] = $ce["accion_cumplimiento_control"];
					$array_ce[] = $ce["frecuencia_ejecucion"];
					$array_ce[] = "-";
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					}
 
					$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;
					
					$array_detalle[] = $array_ce; 
				}
				
			}
			
		} 
		
		//SI SE FILTRA SOLO POR COMPROMISO
		if(!$id_evaluado && $id_valor_compromiso){
			
			$nombre_compromiso = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso)->nombre_compromiso;
			$evaluaciones_cumplimiento_compromiso = $this->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
			//$evaluaciones_cumplimiento_compromiso = $this->get_last_evaluation(array("id_valor_compromiso" => $id_valor_compromiso))->result_array();
			$evaluados_compromiso = $this->Plans_reportables_compromises_model->get_all_where(array("id_compromiso" => $id_compromiso_proyecto, "deleted" => 0))->result_array();

			if($evaluaciones_cumplimiento_compromiso){
				
				$array_ids_evaluados = array();
				
				foreach($evaluaciones_cumplimiento_compromiso as $ecc){
					
					$ultima_evaluacion = $this->get_last_evaluation(array("id_evaluado" => $ecc["id_evaluado"], "id_valor_compromiso" => $id_valor_compromiso))->result_array();
					
					$existen_evaluaciones_con_archivos = FALSE;
					$evaluaciones = $this->get_all_where(array("id_evaluado" => $ecc["id_evaluado"], "id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
					foreach($evaluaciones as $evaluacion){
						$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(
							array(
								"id_evaluacion_cumplimiento_compromiso" => $evaluacion["id"], 
								"tipo_evaluacion" => "reportable", 
								"deleted" => 0
							)
						)->result_array();
						if($evidencias){
							$existen_evaluaciones_con_archivos = TRUE;
						} 
					}
					
					if($ecc["fecha_evaluacion"] == $ultima_evaluacion[0]["fecha_evaluacion"]){
												
						$numero_compromiso = $this->Values_compromises_reportables_model->get_one_where(array("id" => $ecc["id_valor_compromiso"], "deleted" => 0))->numero_compromiso;
						$nombre_evaluado = $this->Plans_reportables_compromises_model->get_one($ecc["id_evaluado"])->nombre_evaluado;
						
						$accion_cumplimiento_control = $this->Values_compromises_reportables_model->get_one_where(
							array(
								"id" => $ecc["id_valor_compromiso"], 
								"deleted" => 0
							)
						)->accion_cumplimiento_control;
						$frecuencia_ejecucion = $this->Values_compromises_reportables_model->get_one_where(
							array(
								"id" => $ecc["id_valor_compromiso"], 
								"deleted" => 0
							)
						)->frecuencia_ejecucion;
						
						$responsable = $this->Users_model->get_one($ecc["responsable"]);
						$responsable = $responsable->first_name." ".$responsable->last_name;
						
						$estado = $this->Compromises_compliance_status_model->get_one($ecc["id_estados_cumplimiento_compromiso"]);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';
						
						//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ecc["id"], "deleted" => 0))->result_array();
						if($existen_evaluaciones_con_archivos){
							$modal_evidencias = modal_anchor(get_uri("compromises_rca_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ecc["id"]));					
						} else {
							$modal_evidencias = "-";
						}

						$array_ce = array();
						//$array_ce[] = $ecc["id_valor_compromiso"];
						$array_ce[] = $numero_compromiso;
						$array_ce[] = $nombre_compromiso;
						$array_ce[] = $nombre_evaluado;
						$array_ce[] = $accion_cumplimiento_control;
						$array_ce[] = $frecuencia_ejecucion;
						$array_ce[] = ($nombre_estado) ? $html_estado : "-";
						//$array_ce[] = ($evidencias) ? $modal_evidencias : "-"; 
						$array_ce[] = $modal_evidencias; 
						
						$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ecc["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						
						$array_ce[] = ((!$ecc["observaciones"]) || $ecc["observaciones"] == "") ? "-" : $tooltip_observaciones;
						$array_ce[] = $responsable; 
						//$array_ce[] = ($ecc["modified"]) ? get_date_format($ecc["modified"], $id_proyecto) : get_date_format($ecc["created"], $id_proyecto);
						$array_ce[] = ($ecc["fecha_evaluacion"]) ? get_date_format($ecc["fecha_evaluacion"], $id_proyecto) : "-";
						
						if($puede_agregar == 3 && $puede_editar == 3){
							$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_compromiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
						} else {
							$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_compromiso" => "1"));
						}
						 
						$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluacion" => $ecc["id"]))
									. $boton_editar;
						//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
						
						$array_ce[] = $actions;					
						$array_detalle[] = $array_ce;
						
						$array_ids_evaluados[] = $ecc["id_evaluado"];
						
					}

				}
				
				foreach($evaluados_compromiso as $ec){
					
					if(in_array($ec["id"], $array_ids_evaluados)){ continue; }
					$numero_compromiso = $this->Values_compromises_reportables_model->get_one_where(array("id" => $id_valor_compromiso, "deleted" => 0))->numero_compromiso;
					
					$array_ec = array();
					//$array_ec[] = $id_valor_compromiso;
					$array_ec[] = $numero_compromiso;
					$array_ec[] = $nombre_compromiso;
					$array_ec[] = $ec["nombre_evaluado"];
					$array_ec[] = $ec["accion_cumplimiento_control"];
					$array_ec[] = $ec["frecuencia_ejecucion"];
					$array_ec[] = "-";
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));	
					}
					
					$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
				}			
				
			} else {
				
				foreach($evaluados_compromiso as $ec){
					$array_ec = array();
					$numero_compromiso = $this->Values_compromises_reportables_model->get_one_where(array("id" => $id_valor_compromiso, "deleted" => 0))->numero_compromiso;
					
					//$array_ec[] = $id_valor_compromiso;
					$array_ec[] = $numero_compromiso;
					$array_ec[] = $nombre_compromiso;
					$array_ec[] = $ec["nombre_evaluado"];
					$array_ec[] = $ec["accion_cumplimiento_control"];
					$array_ec[] = $ec["frecuencia_ejecucion"];
					$array_ec[] = "-";
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));
					}
					
					$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
				}
				
			}
					
		}
		
		//SI SE FILTRA POR EVALUADO Y COMPROMISO
		if($id_evaluado && $id_valor_compromiso){
			
			$evaluaciones_cumplimiento = $this->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
			//$evaluaciones_cumplimiento = $this->get_last_evaluation(array("id_evaluado" => $id_evaluado, "id_valor_compromiso" => $id_valor_compromiso))->result_array();
			$nombre_evaluado = $this->Plans_reportables_compromises_model->get_one($id_evaluado)->nombre_evaluado;
			$nombre_compromiso = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso)->nombre_compromiso;
					
			if($evaluaciones_cumplimiento){
				
				foreach($evaluaciones_cumplimiento as $ev_cump){
					
					$ultima_evaluacion = $this->get_last_evaluation(array("id_evaluado" => $id_evaluado, "id_valor_compromiso" => $id_valor_compromiso))->result_array();
					
					$existen_evaluaciones_con_archivos = FALSE;
					$evaluaciones = $this->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
					foreach($evaluaciones as $evaluacion){
						$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(
							array(
								"id_evaluacion_cumplimiento_compromiso" => $evaluacion["id"], 
								"tipo_evaluacion" => "reportable", 
								"deleted" => 0
							)
						)->result_array();
						if($evidencias){
							$existen_evaluaciones_con_archivos = TRUE;
						}
					}
					
					if($ev_cump["fecha_evaluacion"] == $ultima_evaluacion[0]["fecha_evaluacion"]){
					
						$numero_compromiso = $this->Values_compromises_reportables_model->get_one_where(array("id" => $ev_cump["id_valor_compromiso"], "deleted" => 0))->numero_compromiso;
						$estado = $this->Compromises_compliance_status_model->get_one($ev_cump["id_estados_cumplimiento_compromiso"]);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';
						
						$accion_cumplimiento_control = $this->Values_compromises_reportables_model->get_one_where(
							array(
								"id" => $ev_cump["id_valor_compromiso"], 
								"deleted" => 0
							)
						)->accion_cumplimiento_control;
						$frecuencia_ejecucion = $this->Values_compromises_reportables_model->get_one_where(
							array(
								"id" => $ev_cump["id_valor_compromiso"], 
								"deleted" => 0
							)
						)->frecuencia_ejecucion;
						
						$responsable = $this->Users_model->get_one($ev_cump["responsable"]);
						$responsable = $responsable->first_name." ".$responsable->last_name;
						
						//$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $ev_cump["id"], "deleted" => 0))->result_array();
						if($existen_evaluaciones_con_archivos){
							$modal_evidencias = modal_anchor(get_uri("compromises_rca_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ev_cump["id"]));
						} else {
							$modal_evidencias = "-";
						}

						$array_ec = array();
						
						//$array_ec[] = $ev_cump["id"];
						$array_ec[] = $numero_compromiso;
						$array_ec[] = $nombre_compromiso;
						$array_ec[] = $nombre_evaluado;
						$array_ec[] = $accion_cumplimiento_control;
						$array_ec[] = $frecuencia_ejecucion;
						$array_ec[] = ($nombre_estado) ? $html_estado : "-";
						//$array_ec[] = ($evidencias) ? $modal_evidencias : "-";  
						$array_ec[] = $modal_evidencias;
						
						$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ev_cump["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						
						$array_ec[] = ((!$ev_cump["observaciones"]) || $ev_cump["observaciones"] == "") ? "-" : $tooltip_observaciones;
						$array_ec[] = $responsable; 
						//$array_ec[] = ($ev_cump["modified"]) ? get_date_format($ev_cump["modified"], $id_proyecto) : get_date_format($ev_cump["created"], $id_proyecto);
						$array_ec[] = ($ev_cump["fecha_evaluacion"]) ? get_date_format($ev_cump["fecha_evaluacion"], $id_proyecto) : "-";
						
						if($puede_agregar == 3 && $puede_editar == 3){
							$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
						} else {
							$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));	
						}
						
						$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"]))
									. $boton_editar;
						//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
						 
						$array_ec[] = $actions; 
						
						$array_detalle[] = $array_ec;

					}
					
				}
				
			} else {
				
				$compromiso = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso);
				$id_compromiso = $compromiso->id;
				$numero_compromiso = $compromiso->numero_compromiso;
				$nombre_compromiso = $compromiso->nombre_compromiso;
				$accion_cumplimiento_control = $compromiso->accion_cumplimiento_control;
				$frecuencia_ejecucion = $compromiso->frecuencia_ejecucion;
				
				$array_final = array();
				
				//$array_final[] = $id_compromiso;
				$array_final[] = $numero_compromiso;
				$array_final[] = $nombre_compromiso;
				$array_final[] = $nombre_evaluado;
				$array_final[] = $accion_cumplimiento_control;
				$array_final[] = $frecuencia_ejecucion;
				$array_final[] = "-";
				$array_final[] = "-"; 
				$array_final[] = "-"; 
				$array_final[] = "-"; 
				$array_final[] = "-";
				
				if($puede_agregar == 3 && $puede_editar == 3){
					$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_rca_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
				} else {
					$boton_editar = modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));	
				}
				
				$actions =  modal_anchor(get_uri("compromises_rca_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_compromiso" => $id_valor_compromiso))
							. $boton_editar;
					//.  modal_anchor(get_uri("compromises_rca_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_rca_evaluation/delete"), "data-action" => "delete-confirmation"));
				
				$array_final[] = $actions;
				
				$array_detalle[] = $array_final;

			}

		}

		return $array_detalle;

	}
	
	
	function delete_compromises_compliance_evaluation($id){
		
		$evaluaciones_cumplimiento_compromisos_reportables = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_reportables');
		
        $sql = "UPDATE $evaluaciones_cumplimiento_compromisos_reportables SET $evaluaciones_cumplimiento_compromisos_reportables.deleted=1 WHERE $evaluaciones_cumplimiento_compromisos_reportables.id=$id; ";
        $this->db->query($sql);
		
	}
	
	function get_last_evaluation($options = array()){
		
		$evaluaciones_cumplimiento_compromisos_reportables_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_reportables');
		 
		$where = "";
        $id_evaluado = get_array_value($options, "id_evaluado");
        if ($id_evaluado) {
            $where .= " AND $evaluaciones_cumplimiento_compromisos_reportables_table.id_evaluado = $id_evaluado";
        }
		
		$id_valor_compromiso = get_array_value($options, "id_valor_compromiso");
        if ($id_valor_compromiso) {
            $where .= " AND $evaluaciones_cumplimiento_compromisos_reportables_table.id_valor_compromiso = $id_valor_compromiso";
        }
		
		$where .= " AND $evaluaciones_cumplimiento_compromisos_reportables_table.fecha_evaluacion =";
		$where .= " (SELECT MAX($evaluaciones_cumplimiento_compromisos_reportables_table.fecha_evaluacion)";
		$where .= " FROM $evaluaciones_cumplimiento_compromisos_reportables_table";
		//$where .= " WHERE $evaluaciones_cumplimiento_compromisos_reportables_table.id_evaluado = $id_evaluado";
		$where .= " WHERE $evaluaciones_cumplimiento_compromisos_reportables_table.id_valor_compromiso = $id_valor_compromiso)";
		 
		$sql = "SELECT $evaluaciones_cumplimiento_compromisos_reportables_table.*";
		$sql .= " FROM $evaluaciones_cumplimiento_compromisos_reportables_table";
		$sql .= " WHERE $evaluaciones_cumplimiento_compromisos_reportables_table.deleted = 0";
		$sql .= " $where";
		//$sql .= " ORDER BY $evaluaciones_cumplimiento_compromisos_reportables_table.id DESC LIMIT 1";
		
		return $this->db->query($sql);
		
	}
	
	function get_all_where_order_by_date_desc($options = array()){
		
		$evaluaciones_cumplimiento_compromisos_reportables_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_reportables');
		 
		$where = "";
        $id_evaluado = get_array_value($options, "id_evaluado");
        if ($id_evaluado) {
            $where .= " AND $evaluaciones_cumplimiento_compromisos_reportables_table.id_evaluado = $id_evaluado";
        }
		
		$id_valor_compromiso = get_array_value($options, "id_valor_compromiso");
        if ($id_valor_compromiso) {
            $where .= " AND $evaluaciones_cumplimiento_compromisos_reportables_table.id_valor_compromiso = $id_valor_compromiso";
        }
		
		$sql = "SELECT $evaluaciones_cumplimiento_compromisos_reportables_table.*";
		$sql .= " FROM $evaluaciones_cumplimiento_compromisos_reportables_table";
		$sql .= " WHERE $evaluaciones_cumplimiento_compromisos_reportables_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $evaluaciones_cumplimiento_compromisos_reportables_table.fecha_evaluacion DESC";
		
		return $this->db->query($sql);
		
	}
	
	
	function get_compromises_reportables_with_last_evaluation_no_cumple($id_cliente, $id_proyecto, $until = NULL) {
		
        $compromisos_reportables = $this->db->dbprefix('compromisos_reportables');
        $valores_compromisos_reportables = $this->db->dbprefix('valores_compromisos_reportables');
        $estados_cumplimiento_compromisos = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$planificaciones_reportables_compromisos = $this->db->dbprefix('planificaciones_reportables_compromisos');
		$evaluaciones_cumplimiento_compromisos_reportables = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_reportables');
		$criticidades = $this->db->dbprefix('criticidades');
		
		
		/*
		
		PARTE 1:
		
SELECT 
	valores_compromisos_reportables.*, 
    estados_cumplimiento_compromisos.nombre_estado, 
    estados_cumplimiento_compromisos.categoria, 
    planificaciones_reportables_compromisos.planificacion, 
    evaluaciones_cumplimiento_compromisos_reportables.*, 
    criticidades.nombre 
FROM 
	compromisos_reportables, 
	valores_compromisos_reportables, 
	planificaciones_reportables_compromisos, 
	evaluaciones_cumplimiento_compromisos_reportables, 
	estados_cumplimiento_compromisos, 
	criticidades 
WHERE 
compromisos_reportables.deleted = 0 AND 
compromisos_reportables.id_proyecto = 1 AND 
valores_compromisos_reportables.deleted = 0 AND 
valores_compromisos_reportables.id_compromiso = compromisos_reportables.id AND 
planificaciones_reportables_compromisos.deleted = 0 AND 
planificaciones_reportables_compromisos.id_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND 
evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables.id_planificacion = planificaciones_reportables_compromisos.id AND 
estados_cumplimiento_compromisos.id_cliente = 1 AND 
estados_cumplimiento_compromisos.deleted = 0 AND 
estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND 
estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND 
estados_cumplimiento_compromisos.categoria = 'No Cumple' AND 
criticidades.id = evaluaciones_cumplimiento_compromisos_reportables.id_criticidad
		
		----------------
		PARTE 2:
		
		SELECT 
	evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso, estados_cumplimiento_compromisos.nombre_estado, MAX(planificaciones_reportables_compromisos.planificacion) AS maxima_planificacion 
FROM 
	evaluaciones_cumplimiento_compromisos_reportables, 
    planificaciones_reportables_compromisos, 
    estados_cumplimiento_compromisos 
WHERE 
	evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND 
    planificaciones_reportables_compromisos.deleted = 0 AND 
    planificaciones_reportables_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_planificacion AND 
    estados_cumplimiento_compromisos.id_cliente = 1 AND 
    estados_cumplimiento_compromisos.deleted = 0 AND 
    estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND 
    estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND 
    estados_cumplimiento_compromisos.categoria = 'No Cumple' 
GROUP BY evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso
		
		PARTE 3: UNIR LAS 2 ANTERIORES
		
		SELECT 
	valores_compromisos_reportables.*, 
    estados_cumplimiento_compromisos.nombre_estado, 
    estados_cumplimiento_compromisos.categoria, 
    planificaciones_reportables_compromisos.planificacion, 
    evaluaciones_cumplimiento_compromisos_reportables.*, 
    criticidades.nombre 
FROM 
	compromisos_reportables, 
	valores_compromisos_reportables, 
	planificaciones_reportables_compromisos, 
	evaluaciones_cumplimiento_compromisos_reportables, 
	estados_cumplimiento_compromisos, 
    (SELECT 
	evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso, estados_cumplimiento_compromisos.nombre_estado, MAX(planificaciones_reportables_compromisos.planificacion) AS maxima_planificacion 
FROM 
	evaluaciones_cumplimiento_compromisos_reportables, 
    planificaciones_reportables_compromisos, 
    estados_cumplimiento_compromisos 
WHERE 
	evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND 
    planificaciones_reportables_compromisos.deleted = 0 AND 
    planificaciones_reportables_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_planificacion AND 
    estados_cumplimiento_compromisos.id_cliente = 1 AND 
    estados_cumplimiento_compromisos.deleted = 0 AND 
    estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND 
    estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND 
    estados_cumplimiento_compromisos.categoria = 'No Cumple' 
GROUP BY evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso) AS evaluaciones_cumplimiento_compromisos_reportables_max, 
	criticidades 
WHERE 
compromisos_reportables.deleted = 0 AND 
compromisos_reportables.id_proyecto = 1 AND 
valores_compromisos_reportables.deleted = 0 AND 
valores_compromisos_reportables.id_compromiso = compromisos_reportables.id AND 
planificaciones_reportables_compromisos.deleted = 0 AND 
planificaciones_reportables_compromisos.id_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND 
evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables.id_planificacion = planificaciones_reportables_compromisos.id AND 
estados_cumplimiento_compromisos.id_cliente = 1 AND 
estados_cumplimiento_compromisos.deleted = 0 AND 
estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND 
estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND 
estados_cumplimiento_compromisos.categoria = 'No Cumple' AND 
criticidades.id = evaluaciones_cumplimiento_compromisos_reportables.id_criticidad AND 
evaluaciones_cumplimiento_compromisos_reportables_max.id_valor_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables_max.maxima_planificacion = planificaciones_reportables_compromisos.planificacion 
	
	
	PARTE 4: TRAER SOLO LOS CAMPOS NECESARIOS EN EL SELECT
	
	SELECT 
	valores_compromisos_reportables.id,
    valores_compromisos_reportables.numero_compromiso,
    valores_compromisos_reportables.nombre_compromiso,
    planificaciones_reportables_compromisos.planificacion, 
	estados_cumplimiento_compromisos.nombre_estado, 
	estados_cumplimiento_compromisos.categoria, 
	criticidades.nombre AS criticidad, 
	evaluaciones_cumplimiento_compromisos_reportables.responsable_reporte, 
    evaluaciones_cumplimiento_compromisos_reportables.plazo_cierre 
FROM 
	compromisos_reportables, 
	valores_compromisos_reportables, 
	planificaciones_reportables_compromisos, 
	evaluaciones_cumplimiento_compromisos_reportables, 
	estados_cumplimiento_compromisos, 
    (SELECT 
	evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso, estados_cumplimiento_compromisos.nombre_estado, MAX(planificaciones_reportables_compromisos.planificacion) AS maxima_planificacion 
FROM 
	evaluaciones_cumplimiento_compromisos_reportables, 
    planificaciones_reportables_compromisos, 
    estados_cumplimiento_compromisos 
WHERE 
	evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND 
    planificaciones_reportables_compromisos.deleted = 0 AND 
    planificaciones_reportables_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_planificacion AND 
    estados_cumplimiento_compromisos.id_cliente = 1 AND 
    estados_cumplimiento_compromisos.deleted = 0 AND 
    estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND 
    estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND 
    estados_cumplimiento_compromisos.categoria = 'No Cumple' 
GROUP BY evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso) AS evaluaciones_cumplimiento_compromisos_reportables_max, 
	criticidades 
WHERE 
compromisos_reportables.deleted = 0 AND 
compromisos_reportables.id_proyecto = 1 AND 
valores_compromisos_reportables.deleted = 0 AND 
valores_compromisos_reportables.id_compromiso = compromisos_reportables.id AND 
planificaciones_reportables_compromisos.deleted = 0 AND 
planificaciones_reportables_compromisos.id_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND 
evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables.id_planificacion = planificaciones_reportables_compromisos.id AND 
estados_cumplimiento_compromisos.id_cliente = 1 AND 
estados_cumplimiento_compromisos.deleted = 0 AND 
estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND 
estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND 
estados_cumplimiento_compromisos.categoria = 'No Cumple' AND 
criticidades.id = evaluaciones_cumplimiento_compromisos_reportables.id_criticidad AND 
evaluaciones_cumplimiento_compromisos_reportables_max.id_valor_compromiso = valores_compromisos_reportables.id AND 
evaluaciones_cumplimiento_compromisos_reportables_max.maxima_planificacion = planificaciones_reportables_compromisos.planificacion 
	
	*/
	
		$sql_until = "";
        if($until) {
            $sql_until .= "DATE($evaluaciones_cumplimiento_compromisos_reportables.fecha_evaluacion) <= '$until' AND ";
        }
        
        $sql = "SELECT ";
		$sql .= "$valores_compromisos_reportables.id, ";
		$sql .= "$valores_compromisos_reportables.numero_compromiso, ";
		$sql .= "$valores_compromisos_reportables.nombre_compromiso,";
		$sql .= "$planificaciones_reportables_compromisos.planificacion, ";
		$sql .= "$estados_cumplimiento_compromisos.nombre_estado, ";
		$sql .= "$estados_cumplimiento_compromisos.categoria,";
		$sql .= "$criticidades.nombre AS criticidad, ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables.responsable_reporte, ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables.plazo_cierre ";
		$sql .= "FROM ";
		$sql .= "$compromisos_reportables, ";
		$sql .= "$valores_compromisos_reportables, ";
		$sql .= "$planificaciones_reportables_compromisos, ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables, ";
		$sql .= "$estados_cumplimiento_compromisos, ";
		$sql .= "(SELECT 
	$evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso, $estados_cumplimiento_compromisos.nombre_estado, MAX($planificaciones_reportables_compromisos.planificacion) AS maxima_planificacion ";
		$sql .= "FROM ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables, ";
		$sql .= "$planificaciones_reportables_compromisos, ";
		$sql .= "$estados_cumplimiento_compromisos ";
		$sql .= "WHERE ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND ";
		$sql .= $sql_until;
		$sql .= "$planificaciones_reportables_compromisos.deleted = 0 AND ";
		$sql .= "$planificaciones_reportables_compromisos.id = $evaluaciones_cumplimiento_compromisos_reportables.id_planificacion AND ";
		$sql .= "$estados_cumplimiento_compromisos.id_cliente = $id_cliente AND ";
		$sql .= "$estados_cumplimiento_compromisos.deleted = 0 AND ";
		$sql .= "$estados_cumplimiento_compromisos.id = $evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND ";
		$sql .= "$estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND ";
		$sql .= "$estados_cumplimiento_compromisos.categoria = 'No Cumple' ";
		$sql .= "GROUP BY $evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso) AS evaluaciones_cumplimiento_compromisos_reportables_max, 
	criticidades ";
		$sql .= "WHERE ";
		$sql .= "$compromisos_reportables.deleted = 0 AND ";
		$sql .= "$compromisos_reportables.id_proyecto = $id_proyecto AND ";
		$sql .= "$valores_compromisos_reportables.deleted = 0 AND ";
		$sql .= "$valores_compromisos_reportables.id_compromiso = $compromisos_reportables.id AND ";
		$sql .= "$planificaciones_reportables_compromisos.deleted = 0 AND ";
		$sql .= "$planificaciones_reportables_compromisos.id_compromiso = $valores_compromisos_reportables.id AND ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables.deleted = 0 AND ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables.id_valor_compromiso = $valores_compromisos_reportables.id AND ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_reportables.id_planificacion = $planificaciones_reportables_compromisos.id AND ";
		$sql .= "$estados_cumplimiento_compromisos.id_cliente = $id_cliente AND ";
		$sql .= "$estados_cumplimiento_compromisos.deleted = 0 AND ";
		$sql .= "$estados_cumplimiento_compromisos.id = $evaluaciones_cumplimiento_compromisos_reportables.id_estados_cumplimiento_compromiso AND ";
		$sql .= "$estados_cumplimiento_compromisos.tipo_evaluacion = 'reportable' AND ";
		$sql .= "$estados_cumplimiento_compromisos.categoria = 'No Cumple' AND ";
		$sql .= "$criticidades.id = $evaluaciones_cumplimiento_compromisos_reportables.id_criticidad AND ";
		$sql .= "evaluaciones_cumplimiento_compromisos_reportables_max.id_valor_compromiso = $valores_compromisos_reportables.id AND ";
		$sql .= "evaluaciones_cumplimiento_compromisos_reportables_max.maxima_planificacion = $planificaciones_reportables_compromisos.planificacion";

        return $this->db->query($sql);
	}
	
	function get_last_evaluations_of_project($id_proyecto, $until = NULL){
		
		$compromisos_reportables_table = $this->db->dbprefix('compromisos_reportables');
		$valores_compromisos_reportables_table = $this->db->dbprefix('valores_compromisos_reportables');
		$planificaciones_reportables_compromisos_table = $this->db->dbprefix('planificaciones_reportables_compromisos');
		$evaluaciones_cumplimiento_compromisos_reportables_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_reportables');
		$estados_cumplimiento_compromisos_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		
		$sql = "SELECT ";
		$sql .= "	$compromisos_reportables_table.id_proyecto, ";
		$sql .= "	$valores_compromisos_reportables_table.id_compromiso, ";
		$sql .= "	$valores_compromisos_reportables_table.nombre_compromiso, ";
		$sql .= "	$planificaciones_reportables_compromisos_table.planificacion, ";
		$sql .= "	$evaluaciones_cumplimiento_compromisos_reportables_table.* ";
		$sql .= " FROM";
		$sql .= " 	$compromisos_reportables_table ";
		$sql .= " LEFT JOIN $valores_compromisos_reportables_table ";
		$sql .= " ON ";
		$sql .= " 	$compromisos_reportables_table.id = $valores_compromisos_reportables_table.id_compromiso ";
		$sql .= " LEFT JOIN $planificaciones_reportables_compromisos_table ";
		$sql .= " ON ";
		$sql .= " 	$valores_compromisos_reportables_table.id = $planificaciones_reportables_compromisos_table.id_compromiso ";
		$sql .= " LEFT JOIN $evaluaciones_cumplimiento_compromisos_reportables_table ";
		$sql .= " ON ";
		$sql .= " 	$planificaciones_reportables_compromisos_table.id = $evaluaciones_cumplimiento_compromisos_reportables_table.id_planificacion AND ";
		$sql .= " $valores_compromisos_reportables_table.id = $evaluaciones_cumplimiento_compromisos_reportables_table.id_valor_compromiso ";
		$sql .= " LEFT JOIN $estados_cumplimiento_compromisos_table ";
		$sql .= " ON ";
		$sql .= " 	$evaluaciones_cumplimiento_compromisos_reportables_table.id_estados_cumplimiento_compromiso = $estados_cumplimiento_compromisos_table.id";
		$sql .= " WHERE ";
		$sql .= " 	$compromisos_reportables_table.id_proyecto = $id_proyecto AND ";
		$sql .= " 	$compromisos_reportables_table.deleted = 0 AND ";
		$sql .= " 	$valores_compromisos_reportables_table.deleted = 0 AND ";
		$sql .= " 	$planificaciones_reportables_compromisos_table.deleted = 0 AND ";
		if ($until) {
			$sql .= " DATE($planificaciones_reportables_compromisos_table.planificacion) <= '$until' AND ";
		}
		$sql .= " 	$evaluaciones_cumplimiento_compromisos_reportables_table.deleted = 0 AND ";
		$sql .= " 	$estados_cumplimiento_compromisos_table.deleted = 0 AND ";
		$sql .= " 	$estados_cumplimiento_compromisos_table.tipo_evaluacion = 'reportable' ";
		
		return $this->db->query($sql);
		
	}
		
}
