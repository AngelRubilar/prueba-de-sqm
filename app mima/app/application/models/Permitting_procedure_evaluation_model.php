<?php

class Permitting_procedure_evaluation_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evaluaciones_tramitacion_permisos';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){

		$array_detalle = array();
		
		$where = "";
        $id_permiso_proyecto = get_array_value($options, "id_permiso_proyecto");
		$id_evaluado = get_array_value($options, "id_evaluado");
		$id_valor_permiso = get_array_value($options, "id_valor_permiso");
		$id_proyecto = get_array_value($options, "id_proyecto");
		$puede_agregar = get_array_value($options, "puede_agregar");
		$puede_editar = get_array_value($options, "puede_editar");
		
		//SI SE FILTRA SOLO POR EVALUADO
		if($id_evaluado && !$id_valor_permiso){
			//se trae los permisos del evaluado (tienen en común el id_permiso)
			$permisos_evaluado = $this->Values_permitting_model->get_all_where(array("id_permiso" => $id_permiso_proyecto, "deleted" => 0))->result_array();
			$nombre_evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado)->nombre_evaluado;
			$evaluaciones_permiso_evaluado = $this->get_all_where(array("id_evaluado" => $id_evaluado, "deleted" => 0))->result_array();
			
			//var_dump($compromisos_evaluado);
			
			if($evaluaciones_permiso_evaluado){
				
				$array_ids_val_permiso = array();
				
				foreach($evaluaciones_permiso_evaluado as $ece){
					
					$ultima_evaluacion = $this->get_last_evaluation(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $ece["id_valor_permiso"]))->result_array();
					
					$existen_evaluaciones_con_archivos = FALSE;
					$evaluaciones = $this->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $ece["id_valor_permiso"], "deleted" => 0))->result_array();
					foreach($evaluaciones as $evaluacion){
						$evidencias = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $evaluacion["id"], "deleted" => 0))->result_array();
						if($evidencias){
							$existen_evaluaciones_con_archivos = TRUE;
						} 
					}
						
					if($ece["fecha_evaluacion"] == $ultima_evaluacion[0]["fecha_evaluacion"]){
						
						$nombre_permiso = $this->Values_permitting_model->get_one_where(array("id" => $ece["id_valor_permiso"], "deleted" => 0))->nombre_permiso;
						$entidad = $this->Values_permitting_model->get_one_where(array("id" => $ece["id_valor_permiso"], "deleted" => 0))->entidad;
						$numero_permiso = $this->Values_permitting_model->get_one_where(array("id" => $ece["id_valor_permiso"], "deleted" => 0))->numero_permiso;
						$responsable = $this->Users_model->get_one($ece["responsable"]);
						$responsable = $responsable->first_name." ".$responsable->last_name;
						$estado = $this->Permitting_procedure_status_model->get_one($ece["id_estados_tramitacion_permisos"]);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';
						
						//$evidencias = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $ece["id"], "deleted" => 0))->result_array();
						if($existen_evaluaciones_con_archivos){
							$modal_evidencias = modal_anchor(get_uri("permitting_procedure_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ece["id"]));
						} else {
							$modal_evidencias = "-";
						}
						
						$array_ce = array();
						//$array_ce[] = $ece["id_valor_permiso"];
						$array_ce[] = $numero_permiso;
						$array_ce[] = $nombre_permiso;
						$array_ce[] = $entidad;
						$array_ce[] = $nombre_evaluado;
						$array_ce[] = ($nombre_estado) ? $html_estado : "-";
						//$array_ce[] = ($evidencias) ? $modal_evidencias : "-"; 
						$array_ce[] = $modal_evidencias;
						
						$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ece["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						
						$array_ce[] = ((!$ece["observaciones"]) || $ece["observaciones"] == "") ? "-" : $tooltip_observaciones;
						$array_ce[] = $responsable; 
						//$array_ce[] = ($ece["modified"]) ? get_date_format($ece["modified"], $id_proyecto) : get_date_format($ece["created"], $id_proyecto);
						$array_ce[] = ($ece["fecha_evaluacion"]) ? get_date_format($ece["fecha_evaluacion"], $id_proyecto) : "-";
						
						if($puede_agregar == 3 && $puede_editar == 3){
							$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
						} else {
							$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1"));
						}
						 
						$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluacion" => $ece["id"]))
									. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ece["id"], "data-post-select_evaluado" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-id_evaluacion" => $ece["id"], "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
						
						$array_ce[] = $actions;					
						$array_detalle[] = $array_ce;
						$array_ids_val_permiso[] = $ece["id_valor_permiso"];
					
					}
					
				}
				
				foreach($permisos_evaluado as $ce){

					if(in_array($ce["id"], $array_ids_val_permiso)){ continue; }
					
					$array_ce = array();
					//$array_ce[] = $ce["id"];
					$array_ce[] = $ce["numero_permiso"];
					$array_ce[] = $ce["nombre_permiso"];
					$array_ce[] = $ce["entidad"];
					$array_ce[] = $nombre_evaluado;
					$array_ce[] = "-";
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'),  "data-post-id_valor_permiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_valor_permiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					}
					 
					$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_valor_permiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;
					
					$array_detalle[] = $array_ce;
	
				}
							
			} else {
				
				foreach($permisos_evaluado as $ce){
					$array_ce = array();
					//$array_ce[] = $ce["id"];
					$array_ce[] = $ce["numero_permiso"];
					$array_ce[] = $ce["nombre_permiso"];
					$array_ce[] = $ce["entidad"];
					$array_ce[] = $nombre_evaluado;
					$array_ce[] = "-";
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-"; 
					$array_ce[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_valor_permiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_valor_permiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					}
					
					 
					$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_valor_permiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_valor_compromiso" => $ce["id"], "data-post-id_evaluado" => $id_evaluado, "data-post-select_evaluado" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					
					$array_ce[] = $actions;
					
					$array_detalle[] = $array_ce; 
				}
				
			}
			
		} 
		
		//SI SE FILTRA SOLO POR PERMISO
		if(!$id_evaluado && $id_valor_permiso){
			
			$nombre_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->nombre_permiso;
			$entidad = $this->Values_permitting_model->get_one($id_valor_permiso)->entidad;
			//$evaluaciones_cumplimiento_compromiso = $this->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
			$evaluaciones_tramitacion_permiso = $this->get_all_where(array("id_valor_permiso" => $id_valor_permiso, "deleted" => 0))->result_array();
			
			$evaluados_permiso = $this->Evaluated_permitting_model->get_all_where(array("id_permiso" => $id_permiso_proyecto, "deleted" => 0))->result_array();
			
			if($evaluaciones_tramitacion_permiso){
				
				$array_ids_evaluados = array();
				
				foreach($evaluaciones_tramitacion_permiso as $ecc){
					
					$ultima_evaluacion = $this->get_last_evaluation(array("id_evaluado" => $ecc["id_evaluado"], "id_valor_permiso" => $id_valor_permiso))->result_array();
					
					$existen_evaluaciones_con_archivos = FALSE;
					$evaluaciones = $this->get_all_where(array("id_evaluado" => $ecc["id_evaluado"], "id_valor_permiso" => $id_valor_permiso, "deleted" => 0))->result_array();
					foreach($evaluaciones as $evaluacion){
						$evidencias = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $evaluacion["id"], "deleted" => 0))->result_array();
						if($evidencias){
							$existen_evaluaciones_con_archivos = TRUE;
						} 
					}
	
					if($ecc["fecha_evaluacion"] == $ultima_evaluacion[0]["fecha_evaluacion"]){
						
						$nombre_evaluado = $this->Evaluated_permitting_model->get_one($ecc["id_evaluado"])->nombre_evaluado;
						$numero_permiso = $this->Values_permitting_model->get_one_where(array("id" => $ecc["id_valor_permiso"], "deleted" => 0))->numero_permiso;
						$responsable = $this->Users_model->get_one($ecc["responsable"]);
						$responsable = $responsable->first_name." ".$responsable->last_name;
						
						$estado = $this->Permitting_procedure_status_model->get_one($ecc["id_estados_tramitacion_permisos"]);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';
						
						//$evidencias = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $ecc["id"], "deleted" => 0))->result_array();
						
						if($existen_evaluaciones_con_archivos){
							$modal_evidencias = modal_anchor(get_uri("permitting_procedure_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ecc["id"]));						
						} else {
							$modal_evidencias = "-";
						}

						$array_ce = array();
						//$array_ce[] = $ecc["id_valor_permiso"];
						$array_ce[] = $numero_permiso;
						$array_ce[] = $nombre_permiso;
						$array_ce[] = $entidad;
						$array_ce[] = $nombre_evaluado;
						$array_ce[] = ($nombre_estado) ? $html_estado : "-";
						//$array_ce[] = ($evidencias) ? $modal_evidencias : "-"; 
						$array_ce[] = $modal_evidencias; 
						
						$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ecc["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						
						$array_ce[] = ((!$ecc["observaciones"]) || $ecc["observaciones"] == "") ? "-" : $tooltip_observaciones;
						$array_ce[] = $responsable; 
						//$array_ce[] = ($ecc["modified"]) ? get_date_format($ecc["modified"], $id_proyecto) : get_date_format($ecc["created"], $id_proyecto);
						$array_ce[] = ($ecc["fecha_evaluacion"]) ? get_date_format($ecc["fecha_evaluacion"], $id_proyecto) : "-";
						
						if($puede_agregar == 3 && $puede_editar == 3){
							$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_permiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
						} else {
							$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_permiso" => "1"));
						}
						 
						$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluacion" => $ecc["id"]))
									. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ecc["id"], "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
						
						$array_ce[] = $actions;					
						$array_detalle[] = $array_ce;
						
						$array_ids_evaluados[] = $ecc["id_evaluado"];

					}
	
				}
				
				foreach($evaluados_permiso as $ec){
					
					if(in_array($ec["id"], $array_ids_evaluados)){ continue; }
					$numero_permiso = $this->Values_permitting_model->get_one_where(array("id" => $id_valor_permiso, "deleted" => 0))->numero_permiso;
					
					$array_ec = array();
					//$array_ec[] = $id_valor_permiso;
					$array_ec[] = $numero_permiso;
					$array_ec[] = $nombre_permiso;
					$array_ec[] = $entidad;
					$array_ec[] = $ec["nombre_evaluado"];
					$array_ec[] = "-";
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_permiso" => $id_valor_permiso, "data-post-select_valor_permiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_permiso" => $id_valor_permiso, "data-post-select_valor_permiso" => "1"));
					}
					
					$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_permiso" => $id_valor_permiso))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
				}			
				
			} else {
				
				foreach($evaluados_permiso as $ec){
					$array_ec = array();
					$numero_permiso = $this->Values_permitting_model->get_one_where(array("id" => $id_valor_permiso, "deleted" => 0))->numero_permiso;
					
					//$array_ec[] = $id_valor_permiso;
					$array_ec[] = $numero_permiso;
					$array_ec[] = $nombre_permiso;
					$array_ec[] = $entidad;
					$array_ec[] = $ec["nombre_evaluado"];
					$array_ec[] = "-";
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-"; 
					$array_ec[] = "-";
					
					if($puede_agregar == 3 && $puede_editar == 3){
						$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_permiso" => $id_valor_permiso, "data-post-select_valor_permiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
					} else {
						$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_permiso" => $id_valor_permiso, "data-post-select_valor_permiso" => "1"));
					}
					
					$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_permiso" => $id_valor_permiso))
								. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $ec["id"], "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
					$array_ec[] = $actions; 
					
					$array_detalle[] = $array_ec;
				}
				
			}
					
		}
		
		//SI SE FILTRA POR EVALUADO Y PERMISO
		if($id_evaluado && $id_valor_permiso){
			
			$evaluaciones_tramitacion = $this->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $id_valor_permiso, "deleted" => 0))->result_array();
			$nombre_evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado)->nombre_evaluado;
			$nombre_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->nombre_permiso;
			$entidad = $this->Values_permitting_model->get_one($id_valor_permiso)->entidad;
					
			if($evaluaciones_tramitacion){
				
				foreach($evaluaciones_tramitacion as $ev_cump){
					
					$ultima_evaluacion = $this->get_last_evaluation(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $id_valor_permiso))->result_array();

					$existen_evaluaciones_con_archivos = FALSE;
					$evaluaciones = $this->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $id_valor_permiso, "deleted" => 0))->result_array();
					foreach($evaluaciones as $evaluacion){
						$evidencias = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $evaluacion["id"], "deleted" => 0))->result_array();
						if($evidencias){
							$existen_evaluaciones_con_archivos = TRUE;
						}
					}
					
					if($ev_cump["fecha_evaluacion"] == $ultima_evaluacion[0]["fecha_evaluacion"]){

						$numero_permiso = $this->Values_permitting_model->get_one_where(array("id" => $ev_cump["id_valor_permiso"], "deleted" => 0))->numero_permiso;
						$estado = $this->Permitting_procedure_status_model->get_one($ev_cump["id_estados_tramitacion_permisos"]);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';
						
						$responsable = $this->Users_model->get_one($ev_cump["responsable"]);
						$responsable = $responsable->first_name." ".$responsable->last_name;
						
						//$evidencias = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $ev_cump["id"], "deleted" => 0))->result_array();
						if($existen_evaluaciones_con_archivos){
							$modal_evidencias = modal_anchor(get_uri("permitting_procedure_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $ev_cump["id"]));						
						} else {
							$modal_evidencias = "-";
						}
						
						$array_ec = array();
						
						//$array_ec[] = $ev_cump["id"];
						$array_ec[] = $numero_permiso;
						$array_ec[] = $nombre_permiso;
						$array_ec[] = $entidad;
						$array_ec[] = $nombre_evaluado;
						$array_ec[] = ($nombre_estado) ? $html_estado : "-";
						//$array_ec[] = ($evidencias) ? $modal_evidencias : "-";
						$array_ec[] = $modal_evidencias;
						
						$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$ev_cump["observaciones"].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						
						$array_ec[] = ((!$ev_cump["observaciones"]) || $ev_cump["observaciones"] == "") ? "-" : $tooltip_observaciones;
						$array_ec[] = $responsable; 
						//$array_ec[] = ($ev_cump["modified"]) ? get_date_format($ev_cump["modified"], $id_proyecto) : get_date_format($ev_cump["created"], $id_proyecto);
						$array_ec[] = ($ev_cump["fecha_evaluacion"]) ? get_date_format($ev_cump["fecha_evaluacion"], $id_proyecto) : "-";
						
						if($puede_agregar == 3 && $puede_editar == 3){
							$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_permiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
						} else {
							$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_permiso" => "1"));
						}
						
						$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"]))
									. $boton_editar;
						//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $ev_cump["id"], "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
						//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
						 
						$array_ec[] = $actions; 
						
						$array_detalle[] = $array_ec;

					}
	
				}
				
			} else {
				
				$permiso = $this->Values_permitting_model->get_one($id_valor_permiso);
				$id_permiso = $permiso->id;
				$nombre_permiso = $permiso->nombre_permiso;
				$entidad = $permiso->entidad;
				$numero_permiso = $permiso->numero_permiso;
				
				$array_final = array();
				
				//$array_final[] = $id_permiso;
				$array_final[] = $numero_permiso;
				$array_final[] = $nombre_permiso;
				$array_final[] = $entidad;
				$array_final[] = $nombre_evaluado;
				$array_final[] = "-";
				$array_final[] = "-"; 
				$array_final[] = "-"; 
				$array_final[] = "-"; 
				$array_final[] = "-";
				
				if($puede_agregar == 3 && $puede_editar == 3){
					$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_compliance_evaluation/modal_form" */), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_permiso" => $id_valor_permiso, "data-post-select_evaluado" => "1", "data-post-select_valor_permiso" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
				} else {
					$boton_editar = modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_permiso" => $id_valor_permiso, "data-post-select_evaluado" => "1", "data-post-select_valor_permiso" => "1"));
				}
				
				$actions =  modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_permiso" => $id_valor_permiso))
							. $boton_editar;
					//.  modal_anchor(get_uri("compromises_compliance_evaluation/modal_form"), "<i class='fa fa-user-check'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluado" => $id_evaluado, "data-post-id_valor_compromiso" => $id_valor_compromiso, "data-post-select_evaluado" => "1", "data-post-select_valor_compromiso" => "1"));
					//. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_evaluation/delete"), "data-action" => "delete-confirmation"));
				
				$array_final[] = $actions;
				
				$array_detalle[] = $array_final;

			}

		}
		
		return $array_detalle;

	}
	
	function delete_permitting_procedure_evaluation($id){
		
		$evaluaciones_tramitacion_permisos = $this->db->dbprefix('evaluaciones_tramitacion_permisos');

		$sql = "UPDATE $evaluaciones_tramitacion_permisos SET $evaluaciones_tramitacion_permisos.deleted=1 WHERE $evaluaciones_tramitacion_permisos.id=$id; ";
		$this->db->query($sql);
		
	}
	
	function get_last_evaluation($options = array()){
		
		$evaluaciones_tramitacion_permisos_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		 
		$where = "";
        $id_evaluado = get_array_value($options, "id_evaluado");
        if ($id_evaluado) {
            $where .= " AND $evaluaciones_tramitacion_permisos_table.id_evaluado = $id_evaluado";
        }
		
		$id_valor_permiso = get_array_value($options, "id_valor_permiso");
        if ($id_valor_permiso) {
            $where .= " AND $evaluaciones_tramitacion_permisos_table.id_valor_permiso = $id_valor_permiso";
        }
		
		$where .= " AND $evaluaciones_tramitacion_permisos_table.fecha_evaluacion =";
		$where .= " (SELECT MAX($evaluaciones_tramitacion_permisos_table.fecha_evaluacion)";
		$where .= " FROM $evaluaciones_tramitacion_permisos_table";
		$where .= " WHERE $evaluaciones_tramitacion_permisos_table.id_evaluado = $id_evaluado";
		$where .= " AND $evaluaciones_tramitacion_permisos_table.id_valor_permiso = $id_valor_permiso)";
		 
		$sql = "SELECT $evaluaciones_tramitacion_permisos_table.*";
		$sql .= " FROM $evaluaciones_tramitacion_permisos_table";
		$sql .= " WHERE $evaluaciones_tramitacion_permisos_table.deleted = 0";
		$sql .= " $where";
		
		return $this->db->query($sql);
		
	}	
	
	function get_all_where_order_by_date_desc($options = array()){
		
		$evaluaciones_tramitacion_permisos_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		 
		$where = "";
        $id_evaluado = get_array_value($options, "id_evaluado");
        if ($id_evaluado) {
            $where .= " AND $evaluaciones_tramitacion_permisos_table.id_evaluado = $id_evaluado";
        }
		
		$id_valor_permiso = get_array_value($options, "id_valor_permiso");
        if ($id_valor_permiso) {
            $where .= " AND $evaluaciones_tramitacion_permisos_table.id_valor_permiso = $id_valor_permiso";
        }
		
		$sql = "SELECT $evaluaciones_tramitacion_permisos_table.*";
		$sql .= " FROM $evaluaciones_tramitacion_permisos_table";
		$sql .= " WHERE $evaluaciones_tramitacion_permisos_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $evaluaciones_tramitacion_permisos_table.fecha_evaluacion DESC";
		
		return $this->db->query($sql);
		
	}
	
	function get_permittings_with_last_evaluation_no_cumple($id_cliente, $id_proyecto, $until = NULL) {
		
        $compromisos_rca = $this->db->dbprefix('permisos');
        $valores_compromisos_rca = $this->db->dbprefix('valores_permisos');
        $estados_cumplimiento_compromisos = $this->db->dbprefix('estados_tramitacion_permisos');
		$evaluados_rca_compromisos = $this->db->dbprefix('evaluados_permisos');
		$evaluaciones_cumplimiento_compromisos_rca = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		//$criticidades = $this->db->dbprefix('criticidades');
		
		
		/*
		
		PARTE 1:
		
		SELECT 
		valores_compromisos_rca.*, 
		estados_cumplimiento_compromisos.nombre_estado, 
		estados_cumplimiento_compromisos.categoria, 
		evaluados_rca_compromisos.nombre_evaluado, 
		evaluaciones_cumplimiento_compromisos_rca.*,
		criticidades.nombre
		FROM compromisos_rca, valores_compromisos_rca, estados_cumplimiento_compromisos, evaluados_rca_compromisos, evaluaciones_cumplimiento_compromisos_rca, criticidades
		WHERE 
		compromisos_rca.deleted = 0 AND 
		compromisos_rca.id_proyecto = 1 AND 
		valores_compromisos_rca.deleted = 0 AND 
		valores_compromisos_rca.id_compromiso = compromisos_rca.id AND 
		evaluaciones_cumplimiento_compromisos_rca.deleted = 0 AND 
		evaluaciones_cumplimiento_compromisos_rca.id_valor_compromiso = valores_compromisos_rca.id AND 
		evaluados_rca_compromisos.deleted = 0 AND 
		evaluados_rca_compromisos.id_compromiso = compromisos_rca.id AND 
		evaluados_rca_compromisos.id = evaluaciones_cumplimiento_compromisos_rca.id_evaluado AND 
		estados_cumplimiento_compromisos.deleted = 0 AND 
		estados_cumplimiento_compromisos.id_cliente = 1 AND 
		estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_rca.id_estados_cumplimiento_compromiso AND 
		criticidades.id = evaluaciones_cumplimiento_compromisos_rca.id_criticidad
		
		----------------
		PARTE 2:
		
		SELECT * FROM 
		evaluaciones_cumplimiento_compromisos_rca, 
		(SELECT MAX(id) AS id_ultima_evaluacion
		FROM evaluaciones_cumplimiento_compromisos_rca 
		WHERE evaluaciones_cumplimiento_compromisos_rca.deleted = 0 
		GROUP BY evaluaciones_cumplimiento_compromisos_rca.id_valor_compromiso, evaluaciones_cumplimiento_compromisos_rca.id_evaluado) AS tabla_ultimos 
		WHERE evaluaciones_cumplimiento_compromisos_rca.id = tabla_ultimos.id_ultima_evaluacion AND 
		evaluaciones_cumplimiento_compromisos_rca.deleted = 0
		
		PARTE 3: UNIR LAS 2 ANTERIORES
		
		SELECT 
	valores_compromisos_rca.*, 
	estados_cumplimiento_compromisos.nombre_estado, 
	estados_cumplimiento_compromisos.categoria, 
	evaluados_rca_compromisos.nombre_evaluado, 
	evaluaciones_cumplimiento_compromisos_rca_max.*, 
    criticidades.nombre
FROM 
	compromisos_rca, 
	valores_compromisos_rca, 
	estados_cumplimiento_compromisos, 
	evaluados_rca_compromisos, 
	(SELECT * FROM 
		evaluaciones_cumplimiento_compromisos_rca, 
		(SELECT MAX(id) AS id_ultima_evaluacion
		FROM evaluaciones_cumplimiento_compromisos_rca 
		WHERE evaluaciones_cumplimiento_compromisos_rca.deleted = 0 
		GROUP BY evaluaciones_cumplimiento_compromisos_rca.id_valor_compromiso, evaluaciones_cumplimiento_compromisos_rca.id_evaluado) AS tabla_ultimos 
		WHERE evaluaciones_cumplimiento_compromisos_rca.id = tabla_ultimos.id_ultima_evaluacion AND 
		evaluaciones_cumplimiento_compromisos_rca.deleted = 0) AS evaluaciones_cumplimiento_compromisos_rca_max,
        criticidades
WHERE 
	compromisos_rca.deleted = 0 AND 
	compromisos_rca.id_proyecto = 1 AND 
	valores_compromisos_rca.deleted = 0 AND 
	valores_compromisos_rca.id_compromiso = compromisos_rca.id AND 
	evaluaciones_cumplimiento_compromisos_rca_max.deleted = 0 AND 
	evaluaciones_cumplimiento_compromisos_rca_max.id_valor_compromiso = valores_compromisos_rca.id AND 
	evaluados_rca_compromisos.deleted = 0 AND 
	evaluados_rca_compromisos.id_compromiso = compromisos_rca.id AND 
	evaluados_rca_compromisos.id = evaluaciones_cumplimiento_compromisos_rca_max.id_evaluado AND 
	estados_cumplimiento_compromisos.deleted = 0 AND 
	estados_cumplimiento_compromisos.id_cliente = 1 AND 
	estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_rca_max.id_estados_cumplimiento_compromiso AND 
    estados_cumplimiento_compromisos.tipo_evaluacion = 'rca' AND 
    estados_cumplimiento_compromisos.categoria = 'No Cumple' AND 
    criticidades.deleted = 0 AND 
    criticidades.id = evaluaciones_cumplimiento_compromisos_rca_max.id_criticidad
	
	
	PARTE 4: TRAER SOLO LOS CAMPOS NECESARIOS EN EL SELECT
	
	SELECT 
	valores_compromisos_rca.id,
    valores_compromisos_rca.numero_compromiso,
    valores_compromisos_rca.nombre_compromiso,
    evaluados_rca_compromisos.nombre_evaluado, 
	estados_cumplimiento_compromisos.nombre_estado, 
	estados_cumplimiento_compromisos.categoria, 
	criticidades.nombre AS criticidad, 
	evaluaciones_cumplimiento_compromisos_rca_max.responsable_reporte, 
    evaluaciones_cumplimiento_compromisos_rca_max.plazo_cierre 
FROM 
	compromisos_rca, 
	valores_compromisos_rca, 
	estados_cumplimiento_compromisos, 
	evaluados_rca_compromisos, 
	(SELECT * FROM 
		evaluaciones_cumplimiento_compromisos_rca, 
		(SELECT MAX(id) AS id_ultima_evaluacion
		FROM evaluaciones_cumplimiento_compromisos_rca 
		WHERE evaluaciones_cumplimiento_compromisos_rca.deleted = 0 
		GROUP BY evaluaciones_cumplimiento_compromisos_rca.id_valor_compromiso, evaluaciones_cumplimiento_compromisos_rca.id_evaluado) AS tabla_ultimos 
		WHERE evaluaciones_cumplimiento_compromisos_rca.id = tabla_ultimos.id_ultima_evaluacion AND 
		evaluaciones_cumplimiento_compromisos_rca.deleted = 0) AS evaluaciones_cumplimiento_compromisos_rca_max,
        criticidades
WHERE 
	compromisos_rca.deleted = 0 AND 
	compromisos_rca.id_proyecto = 1 AND 
	valores_compromisos_rca.deleted = 0 AND 
	valores_compromisos_rca.id_compromiso = compromisos_rca.id AND 
	evaluaciones_cumplimiento_compromisos_rca_max.deleted = 0 AND 
	evaluaciones_cumplimiento_compromisos_rca_max.id_valor_compromiso = valores_compromisos_rca.id AND 
	evaluados_rca_compromisos.deleted = 0 AND 
	evaluados_rca_compromisos.id_compromiso = compromisos_rca.id AND 
	evaluados_rca_compromisos.id = evaluaciones_cumplimiento_compromisos_rca_max.id_evaluado AND 
	estados_cumplimiento_compromisos.deleted = 0 AND 
	estados_cumplimiento_compromisos.id_cliente = 1 AND 
	estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_rca_max.id_estados_cumplimiento_compromiso AND 
    estados_cumplimiento_compromisos.tipo_evaluacion = 'rca' AND 
    estados_cumplimiento_compromisos.categoria = 'No Cumple' AND 
    criticidades.deleted = 0 AND 
    criticidades.id = evaluaciones_cumplimiento_compromisos_rca_max.id_criticidad
	
	*/
	
		$sql_until = "";
        if($until) {
            $sql_until .= " AND DATE($evaluaciones_cumplimiento_compromisos_rca.fecha_evaluacion) <= '$until' ";
        }
        
        $sql = "SELECT ";
		$sql .= "$valores_compromisos_rca.id, ";
		$sql .= "$valores_compromisos_rca.numero_permiso, ";
		$sql .= "$valores_compromisos_rca.nombre_permiso, ";
		$sql .= "$evaluados_rca_compromisos.nombre_evaluado, ";
		$sql .= "$estados_cumplimiento_compromisos.nombre_estado, ";
		$sql .= "$estados_cumplimiento_compromisos.categoria, ";
		$sql .= "evaluaciones_cumplimiento_compromisos_rca_max.criticidad, ";
		$sql .= "evaluaciones_cumplimiento_compromisos_rca_max.responsable_reporte, ";
		$sql .= "evaluaciones_cumplimiento_compromisos_rca_max.plazo_cierre ";
		$sql .= "FROM ";
		$sql .= "$compromisos_rca, ";
		$sql .= "$valores_compromisos_rca, ";
		$sql .= "$estados_cumplimiento_compromisos, ";
		$sql .= "$evaluados_rca_compromisos, ";
		
		/*$sql .= "(SELECT * FROM ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_rca, ";
		$sql .= "(SELECT $evaluaciones_cumplimiento_compromisos_rca.id AS id_ultima_evaluacion, MAX($evaluaciones_cumplimiento_compromisos_rca.fecha_evaluacion) AS fecha_ultima_evaluacion ";
		$sql .= "FROM $evaluaciones_cumplimiento_compromisos_rca ";
		$sql .= "WHERE $evaluaciones_cumplimiento_compromisos_rca.deleted = 0 ";
		$sql .= $sql_until;
		$sql .= "GROUP BY $evaluaciones_cumplimiento_compromisos_rca.id_valor_permiso, $evaluaciones_cumplimiento_compromisos_rca.id_evaluado) AS tabla_ultimos ";
		$sql .= "WHERE $evaluaciones_cumplimiento_compromisos_rca.id = tabla_ultimos.id_ultima_evaluacion AND ";
		$sql .= "$evaluaciones_cumplimiento_compromisos_rca.deleted = 0) AS evaluaciones_cumplimiento_compromisos_rca_max ";*/
		
		$sql .= "(SELECT * FROM ";
		$sql .= "(SELECT $evaluaciones_cumplimiento_compromisos_rca.* ";
		$sql .= "FROM $evaluaciones_cumplimiento_compromisos_rca ";
		$sql .= "WHERE $evaluaciones_cumplimiento_compromisos_rca.deleted = 0 ";
		$sql .= $sql_until;
		$sql .= "ORDER BY $evaluaciones_cumplimiento_compromisos_rca.fecha_evaluacion DESC) AS tabla_mayor_a_menor ";
		$sql .= "GROUP BY tabla_mayor_a_menor.id_valor_permiso, tabla_mayor_a_menor.id_evaluado) AS evaluaciones_cumplimiento_compromisos_rca_max ";
		
		$sql .= "WHERE ";
		$sql .= "$compromisos_rca.deleted = 0 AND ";
		$sql .= "$compromisos_rca.id_proyecto = $id_proyecto AND ";
		$sql .= "$valores_compromisos_rca.deleted = 0 AND ";
		$sql .= "$valores_compromisos_rca.id_permiso = $compromisos_rca.id AND ";
		$sql .= "evaluaciones_cumplimiento_compromisos_rca_max.deleted = 0 AND ";
		$sql .= "evaluaciones_cumplimiento_compromisos_rca_max.id_valor_permiso = $valores_compromisos_rca.id AND ";
		$sql .= "$evaluados_rca_compromisos.deleted = 0 AND ";
		$sql .= "$evaluados_rca_compromisos.id_permiso = $compromisos_rca.id AND ";
		$sql .= "$evaluados_rca_compromisos.id = evaluaciones_cumplimiento_compromisos_rca_max.id_evaluado AND ";
		$sql .= "$estados_cumplimiento_compromisos.deleted = 0 AND ";
		$sql .= "$estados_cumplimiento_compromisos.id_cliente = $id_cliente AND ";
		$sql .= "$estados_cumplimiento_compromisos.id = evaluaciones_cumplimiento_compromisos_rca_max.id_estados_tramitacion_permisos AND ";
		$sql .= "$estados_cumplimiento_compromisos.categoria = 'No Cumple' ";
		
		//echo $sql;
        return $this->db->query($sql);
    }
	
	
	function get_last_evaluations_of_project($id_proyecto, $until = NULL){
		
		$permisos_table = $this->db->dbprefix('permisos');
		$valores_permisos_table = $this->db->dbprefix('valores_permisos');
		$evaluaciones_tramitacion_permisos_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$estados_tramitacion_permisos_table = $this->db->dbprefix('estados_tramitacion_permisos');
		
		$sql = "SELECT ";
		$sql .= "	$permisos_table.id_proyecto, ";
		$sql .= "	$valores_permisos_table.id_permiso, ";
		$sql .= "	$valores_permisos_table.nombre_permiso, ";
		$sql .= "	mayores.* ";
		$sql .= " FROM";
		$sql .= " $permisos_table,";
		$sql .= " $valores_permisos_table,";
		$sql .= " (SELECT evaluaciones.*, $estados_tramitacion_permisos_table.nombre_estado, $estados_tramitacion_permisos_table.categoria";
		$sql .= " FROM $evaluaciones_tramitacion_permisos_table evaluaciones";
		$sql .= " INNER JOIN (";
		$sql .= " 	SELECT ";
		$sql .= " 		evaluaciones_agrupadas_fecha_max.id_valor_permiso, ";
		$sql .= " 		evaluaciones_agrupadas_fecha_max.id_evaluado, ";
		$sql .= " 		max(evaluaciones_agrupadas_fecha_max.fecha_evaluacion) as fecha_evaluacion_max";
		$sql .= " 	FROM $evaluaciones_tramitacion_permisos_table evaluaciones_agrupadas_fecha_max ";
		$sql .= " 	WHERE ";
		$sql .= " 		evaluaciones_agrupadas_fecha_max.deleted = 0 ";
		if ($until) {
			$sql .= " 		AND DATE(evaluaciones_agrupadas_fecha_max.fecha_evaluacion) <= '$until'  ";
		}
		$sql .= " 	GROUP BY evaluaciones_agrupadas_fecha_max.id_valor_permiso, evaluaciones_agrupadas_fecha_max.id_evaluado";
		$sql .= " 	) evaluaciones_agrupadas";
		$sql .= " ON";
		$sql .= " 	evaluaciones_agrupadas.id_valor_permiso = evaluaciones.id_valor_permiso AND ";
		$sql .= " 	evaluaciones_agrupadas.id_evaluado = evaluaciones.id_evaluado AND ";
		$sql .= " 	evaluaciones.fecha_evaluacion = evaluaciones_agrupadas.fecha_evaluacion_max ";
		$sql .= " LEFT JOIN $estados_tramitacion_permisos_table ";
		$sql .= " ON evaluaciones.id_estados_tramitacion_permisos = $estados_tramitacion_permisos_table.id) AS mayores ";
		$sql .= " WHERE";
		$sql .= " 	mayores.id_valor_permiso = $valores_permisos_table.id AND";
		$sql .= " 	$valores_permisos_table.deleted = 0 AND ";
		$sql .= " 	$valores_permisos_table.id_permiso = $permisos_table.id AND";
		$sql .= " 	$permisos_table.deleted = 0 AND ";
		$sql .= " 	$permisos_table.id_proyecto = $id_proyecto";
		$sql .= " 	ORDER BY mayores.categoria";
		
		return $this->db->query($sql);
		
	}
	
}
