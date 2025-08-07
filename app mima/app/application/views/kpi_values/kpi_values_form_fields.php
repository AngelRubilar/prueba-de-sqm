<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="id_cliente" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("id_cliente", $clientes, array($model_info->id_cliente), "id='id_cliente' class='select2 validate-hidden' data-sigla='' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="id_fase" class="<?php echo $label_column; ?>"><?php echo lang('phase'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("id_fase", $fases, array($model_info->id_fase), "id='id_fase' class='select2 validate-hidden' data-sigla='' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div id="proyectos_group">
    <div class="form-group">
        <label for="id_proyecto" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("id_proyecto", $proyectos, array($model_info->id_proyecto), "id='id_proyecto' class='select2 validate-hidden' data-sigla='' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="tipo_valor" class="<?php echo $label_column; ?>"><?php echo lang('type_of_value'); ?></label>
    <div class="<?php echo $field_column; ?>">
        
        <?php 	
            $checked_simple = ($model_info->tipo_valor == "simple") ? "checked" : "";
            $checked_compound = ($model_info->tipo_valor == "compound") ? "checked" : "";
        ?>
        
        <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
            <?php echo lang("simple");?>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9">
            <?php
            
            $datos_campo = array(
                "id" => "simple",
                "name" => "tipo_valor",
                "value" => "simple",
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                $checked_simple => $checked_simple
            );
            
            echo form_radio($datos_campo);
            
            ?>	 
        </div>
    
        <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
            <?php echo lang("compound");?>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9">
            <?php
            
            $datos_campo = array(
                "id" => "compound",
                "name" => "tipo_valor",
                "value" => "compound",
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                $checked_compound => $checked_compound
            );
            
            echo form_radio($datos_campo);
            
            ?>	 
        </div>
        
    </div>
</div>

<div class="form-group">
    <label for="nombre_valor" class="<?php echo $label_column; ?>"><?php echo lang('value_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "nombre_valor",
            "name" => "nombre_valor",
            "value" => $model_info->nombre_valor,
            "class" => "form-control",
            "placeholder" => lang('value_name'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div id="tipo_valor_group">

	<?php if($model_info->id){ ?>
    	
        <?php 
			
			if($model_info->tipo_valor == "simple") { 
			
				$html = '';
					
				$html .= '<div class="form-group">';
				$html .= 	'<label for="tipo_formulario" class="'.$label_column.'">'.lang('form_type').'</label>';
				$html .= 	'<div class="'.$field_column.'">';
				$html .= 		form_dropdown("id_tipo_formulario", array("" => "-") + $tipos_formulario, $model_info->id_tipo_formulario, "id='id_tipo_formulario' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
				$html .= 	'</div>';
				$html .= '</div>';
				
				$html .= '<div id="tipo_valor_formulario_group">';
				$html .= 	'<div class="form-group">';
				$html .= 		'<label for="formulario" class="'.$label_column.'">'.lang('form').'</label>';
				$html .= 		'<div class="'.$field_column.'">';
				$html .= 			form_dropdown("id_formulario", array("" => "-") + $array_formularios , $model_info->id_formulario, "id='id_formulario' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
				$html .= 		'</div>';
				$html .= 	'</div>';
				$html .= '</div>';
						
				$html .= '<div id="campos_formulario_group">';
				$html .= 	'<div class="form-group">';
				$html .= 		'<label for="id_campo_unidad" class="'.$label_column.'">'.lang('indicator_field').'</label>';
				$html .= 		'<div class="'.$field_column.'">';
				$html .= 			form_dropdown("id_campo_unidad", array("" => "-") + $array_campos_unidad, $model_info->id_campo_unidad, "id='id_campo_unidad' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
				$html .= 		'</div>';
				$html .= 	'</div>';

				if($model_info->id_tipo_formulario == "1"){
				
					$html .= '<div class="form-group">';
					$html .= 	'<div class="col-md-12 p0">';
					$html .= 	'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
					
					if($formulario->id_tipo_formulario == "1"){
						if(count($array_categorias)){
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		lang("category");
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		form_dropdown("categoria", array("" => "-") + $array_categorias, $kpi_valor_condicion_categoria, "id='categoria' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
						}
					}
					if($formulario->id_tipo_formulario == "1" && $formulario->flujo == "Residuo"){
						
						if(count($array_categorias)){
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
						}
						
						$array_tipo_tratamiento = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"), 'id');
			
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		lang("type_of_treatment");
						$html .= 	'</div>';
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("tipo_tratamiento", array("" => "-") + $array_tipo_tratamiento, $kpi_valor_condicion_tipo_tratamiento, "id='tipo_tratamiento' class='select2 condicion'");
						$html .= 	'</div>';
						
						$html .= 	'</div>';
					}
					
					foreach($array_campos_dinamicos as $id_campo => $nombre_campo){
			
						$campo = $this->Fields_model->get_one($id_campo);
						
						if($campo->id_tipo_campo == 6){
							
							$opciones_campo = json_decode($campo->opciones, TRUE);
							$array_opciones_campo = array();
				
							
							foreach($opciones_campo as $index => $opcion){
								$array_opciones_campo[$opcion["value"]] = $opcion["text"];
							}
							
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		$nombre_campo;
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", $array_opciones_campo, $kpi_valores_condicion_campos_dinamicos[$id_campo], "id='campos_dinamicos' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
							
						}
						
						if($campo->id_tipo_campo == 9){
							
							$opciones_campo = json_decode($campo->opciones, TRUE);
							$array_opciones_campo = array();
							foreach($opciones_campo as $opcion){
								$array_opciones_campo[$opcion["value"]] = $opcion["text"];
							}
							
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		$nombre_campo;
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, $kpi_valores_condicion_campos_dinamicos[$id_campo], "id='campos_dinamicos' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
							
						} 
						
						if($campo->id_tipo_campo == 16){
							
							// Mantenedoras
							$default_value = json_decode($campo->default_value, TRUE);
							$id_mantenedora = $default_value["mantenedora"];
							$id_campo_mantenedora = $default_value["field_value"];
			
							// Buscar los valores de la mantenedora para dejarlos como opciones del select
							$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
								"id_formulario" => $id_mantenedora,
								"deleted" => 0
							));
							
							$valores_formulario = $this->Form_values_model->get_all_where(array(
								"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
								"deleted" => 0
							))->result();
			
							$array_opciones_campo = array();
							foreach($valores_formulario as $valor_formulario){
								
								$datos = json_decode($valor_formulario->datos, TRUE);
								$array_opciones_campo[$datos[$id_campo_mantenedora]] = $datos[$id_campo_mantenedora];
								
							}
							
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		$nombre_campo;
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, $kpi_valores_condicion_campos_dinamicos[$id_campo], "id='campos_dinamicos' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
						}
			
					}
					
					$html .= '</div>';

				}

				if($model_info->id_tipo_formulario == "3"){
					
					if(!$formulario->fijo){
						
						$html .= '<div class="form-group">';
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
						
						
						$loop_count = 0;
						foreach($array_campos_dinamicos as $id_campo => $nombre_campo){
				
							$campo = $this->Fields_model->get_one($id_campo);
							
							if($loop_count > 0){
								$html .= 	'<div class="col-md-12 p0">';
								$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							}
							
							
							if($campo->id_tipo_campo == 6){
								
								$opciones_campo = json_decode($campo->opciones, TRUE);
								$array_opciones_campo = array();
					
								
								foreach($opciones_campo as $index => $opcion){
									$array_opciones_campo[$opcion["value"]] = $opcion["text"];
								}
			
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		$nombre_campo;
								$html .= 	'</div>';
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", $array_opciones_campo, $kpi_valores_condicion_campos_dinamicos[$id_campo], "id='campos_dinamicos' class='select2 condicion'");
								$html .= 	'</div>';
								
								$html .= 	'</div>';
								
							}
							
							if($campo->id_tipo_campo == 9){
								
								$opciones_campo = json_decode($campo->opciones, TRUE);
								$array_opciones_campo = array();
								foreach($opciones_campo as $opcion){
									$array_opciones_campo[$opcion["value"]] = $opcion["text"];
								}
				
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		$nombre_campo;
								$html .= 	'</div>';
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, $kpi_valores_condicion_campos_dinamicos[$id_campo], "id='campos_dinamicos' class='select2 condicion'");
								$html .= 	'</div>';
								
								$html .= 	'</div>';
								
							} 
							
							if($campo->id_tipo_campo == 16){
								
								// Mantenedoras
								$default_value = json_decode($campo->default_value, TRUE);
								$id_mantenedora = $default_value["mantenedora"];
								$id_campo_mantenedora = $default_value["field_value"];
				
								// Buscar los valores de la mantenedora para dejarlos como opciones del select
								$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
									"id_formulario" => $id_mantenedora,
									"deleted" => 0
								));
								
								$valores_formulario = $this->Form_values_model->get_all_where(array(
									"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
									"deleted" => 0
								))->result();
				
								$array_opciones_campo = array();
								foreach($valores_formulario as $valor_formulario){
									
									$datos = json_decode($valor_formulario->datos, TRUE);
									$array_opciones_campo[$datos[$id_campo_mantenedora]] = $datos[$id_campo_mantenedora];
									
								}
			
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		$nombre_campo;
								$html .= 	'</div>';
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, $kpi_valores_condicion_campos_dinamicos[$id_campo], "id='campos_dinamicos' class='select2 condicion'");
								$html .= 	'</div>';
								
								$html .= 	'</div>';
							
							}
							
							$loop_count++;
							
						}
						
						$html .= '</div>';
						
					} else {
						
						$html .= '<div class="form-group">';
						
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="" class="col-md-3">'.lang('condition').'</label>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 			$campo_tipo_edu_amb->nombre;
						$html .= 		'</div>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("campos_or_fijos[".$campo_tipo_edu_amb->id."]", array("" => "-") + $array_valores_tipo_edu_amb, $array_kpi_valores_condicion_campos_fijos[12], "id='' class='select2 condicion'");
						$html .= 		'</div>';
						$html .= 	'</div>';
						
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="" class="col-md-3"></label>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 			$campo_tipo_induccion->nombre;
						$html .= 		'</div>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("campos_or_fijos[".$campo_tipo_induccion->id."]", array("" => "-") + $array_valores_tipo_induccion, $array_kpi_valores_condicion_campos_fijos[13], "id='' class='select2 condicion'");
						$html .= 		'</div>';
						$html .= 	'</div>';
						
						$html .= '</div>';
						
					}
				
				}

				$html .= '</div>'; // Fin <div id="campos_formularios_group">

				$html .= '<div class="form-group">';
				$html .= 	'<label for="operacion" class="'.$label_column.'">'.lang('operation').'</label>';
				$html .= 	'<div class="col-md-4">';
				$html .= 		form_dropdown("operador", $array_operadores, $model_info->operador, "id='operador' class='select2'");
				$html .= 	'</div>';
				$html .= 	'<div class="col-md-4">';
				$html .= 		form_input(array(
									"id" => "valor_operador",
									"name" => "valor_operador",
									"value" => $model_info->valor_operador,
									"class" => "form-control",
									"placeholder" => lang('value'),
									//"autofocus" => true,
									//"data-rule-required" => true,
									//"data-msg-required" => lang("field_required"),
									"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
									"data-msg-regex" => lang("number_or_decimal_required"),
									"autocomplete"=> "off",
									"maxlength" => "255"
								));
				$html .= 	'</div>';
				$html .= '</div>';		
				
				echo $html;
			
			} 
			
			/*
			if($model_info->tipo_valor == "compound") { 
				
				$html = '';
					
				$html .= '<div class="form-group">';
				$html .= 	'<label for="valor_inicial" class="'.$label_column.'">'.lang('initial_value').'</label>';
				$html .= 	'<div class="'.$field_column.'">';
				$html .= 		form_dropdown("valor_inicial", array("" => "-") + $array_valores, $model_info->valor_inicial, "id='valor_inicial' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
				$html .= 	'</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= 	'<label for="operacion" class="'.$label_column.'">'.lang('compound_operation').'</label>';
				$html .= 	'<div class="col-md-4">';
				$html .= 		form_dropdown("operador", $array_operadores, $model_info->operador, "id='operador' class='select2'");
				$html .= 	'</div>';
				$html .= 	'<div class="col-md-4">';
				$html .= 		form_dropdown("valor_calculo", array("" => "-") + $array_valores, $model_info->valor_calculo, "id='valor_calculo' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
				$html .= 	'</div>';
				$html .= '</div>';
				
			}
			*/
			
			
		
		?>
        
    <?php } ?>

	<?php if($model_info->id && $model_info->tipo_valor == "compound") { ?>
    	
        
        <div class="form-group">
        <label for="valor_inicial" class="col-md-3"><?php echo lang('initial_value'); ?></label>
        	<div class="col-md-9">
        		<?php echo form_dropdown("valor_inicial", array("" => "-") + $array_valores, $model_info->valor_inicial, "id='valor_inicial' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'"); ?>
        	</div>
        </div>
        
        <div class="form-group" id="modelo" style="display:none;">
            <label for="description" class="col-md-3 control-label"></label>
            <div class="col-md-4">
                <?php //echo form_dropdown("operador[]", $array_operadores, "", "id='' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'"); ?>
                <?php echo form_dropdown("operador[]", $array_operadores, "", "id='' class='select2 modelo'");?>
            </div>
            <div class="col-md-4">
                <?php //echo form_dropdown("valor_calculo[]", array("" => "-") + $array_valores, "", "id='' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'"); ?>
            	<?php echo form_dropdown("valor_calculo[]", array("" => "-") + $array_valores, "", "id='' class='select2 modelo'"); ?>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remover_opcion"><i class="fa fa-trash-o"></i></button>
            </div>
        </div>
        
        <div class="form-group">
            <label for="valor_operacion" class="col-md-3"><?php echo lang('operation_values'); ?></label>
            <div class="col-md-9">
                
                <button type="button" id="agregar_valor_operacion" class="btn btn-xs btn-success col-sm-1"><i class="fa fa-plus"></i></button>
                <button type="button" id="eliminar_valor_operacion" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        
        <div id="grupo_valores_operacion">
        	<?php $loop_count = 1; ?>
			<?php foreach($array_operacion_compuesta as $index => $operacion_compuesta) { ?>
            
            	<?php $operador = key($operacion_compuesta); ?>
                <?php $id_valor = $operacion_compuesta[key($operacion_compuesta)]; ?>

                    <?php $clase_valor_operacion = ($loop_count != 1) ? "valor_operacion" : ""; ?>
                    <?php $row_number = $loop_count; ?>
                    <?php //$id_row_number = ($loop_count != 1) ? "row_".$row_number : ""; ?>
                    <div id="row_<?php echo $row_number; ?>" class="form-group <?php echo $clase_valor_operacion; ?>">
                        <label for="description" class="col-md-3"></label>
                        <div class="col-md-4">
                            <?php echo form_dropdown("operador[".$row_number."]", $array_operadores, $operador, "id='operador' class='select2 validate-hidden operacion_compuesta' data-rule-required='true' data-msg-required='" . lang('field_required') . "'"); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo form_dropdown("valor_calculo[".$row_number."]", array("" => "-") + $array_valores, $id_valor, "id='valor_calculo' class='select2 validate-hidden operacion_compuesta' data-rule-required='true' data-msg-required='" . lang('field_required') . "'"); ?>
                        </div>
                        <?php if($loop_count != 1) {?>
            			<div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remover_opcion"><i class="fa fa-trash-o"></i></button>
                        </div>
                        <?php } ?>
                    </div>
                    <?php $loop_count++; ?>
            <?php } ?>
 		</div>
        
    
    <?php } ?>

</div>

<div class="form-group multi-column">
	<?php 
		$info = "";
		$disabled = "";
		if($valor_ocupado_reporte){
			$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_units').'"><i class="fa fa-question-circle"></i></span>'; 
			$disabled = "disabled";
		}
		if($valor_ocupado_graficos){
			$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_units_2').'"><i class="fa fa-question-circle"></i></span>'; 
			$disabled = "disabled";
		}	
	?>
    <label for="unit_field" class="col-md-3"><?php echo lang('unit_type'). " " . $info; ?></label>
    <div class="col-md-4">
        <?php echo form_dropdown("tipo_unidad", $tipo_unidad, $model_info->id_tipo_unidad, "id='tipo_unidad' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled"); ?>
    </div>
    <div id="unidades_group">
        <div class="col-md-4">
            <?php echo form_dropdown("unidad", $array_unidades, $model_info->id_unidad, "id='unidad' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled"); ?>
        </div>
    </div>
</div>

<style>
	.multiselect-header{
	  text-align: center;
	  padding: 3px;
	  background: #7988a2;
	  color: #fff;
	}
</style>
<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		//$('#kpi_values-form .select2').select2();
		
		<?php if($model_info->id) { ?>
			<?php if($model_info->tipo_valor == "compound"){?>
				$('#id_cliente').select2();
				$('#id_fase').select2();
				$('#id_proyecto').select2();
				$('#valor_inicial').select2();
				$('.operacion_compuesta').select2();
				$('#tipo_unidad').select2();
				$('#unidad').select2();
				//$('#valor_calculo').select2();
				
			<?php } else { ?>
				$('#kpi_values-form .select2').select2();
			<?php } ?>
		<?php } else { ?>
			$('#kpi_values-form .select2').select2();
		<?php } ?>
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#id_cliente').change(function(){
			$('#id_fase').val("").trigger('change');
		});
		
		$('#id_fase').change(function(){	
					
			var id_cliente = $('#id_cliente').val();
			var id_fase = $(this).val();
			
			select2LoadingStatusOn($('#id_proyecto'));
			
			$.ajax({
				url:  '<?php echo_uri("KPI_Values/get_projects_of_client_phase") ?>',
				type:  'post',
				data: {id_cliente:id_cliente, id_fase:id_fase},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#id_proyecto').select2();
				}
			});
			
			$('#mensaje_form').html('');
		
		});
		
		//$('input[name=tipo_valor]').change(function(){	
		$(document).on('change', 'input[name=tipo_valor]', function(event){
			
			var tipo_valor = $('input[name=tipo_valor]:checked').val();
			var id_proyecto = $('#id_proyecto').val();
			
			$.ajax({
				url: '<?php echo_uri("KPI_Values/get_campos_tipo_valor"); ?>',
				type: 'post',
				data: {tipo_valor:tipo_valor, id_proyecto: id_proyecto},
				success: function(respuesta){
					
					initScrollbar(".modal-body", {setHeight: 400});
					
					$('#tipo_valor_group').html(respuesta);
					
					if(tipo_valor == "simple"){
						
						$('#id_tipo_formulario').select2();
						$('#id_formulario').select2();
						$('#id_campo_unidad').select2();
						$('#operador').select2();
						
					}
					
					if(tipo_valor == "compound"){
						
						$('#valor_inicial').select2();
						$('#operador').select2();
						$('#valor_calculo').select2();
						
						$('#operador').attr('data-rule-required', true);
						$('#operador').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
						$('#operador').addClass('validate-hidden');
						
						$('#valor_calculo').attr('data-rule-required', true);
						$('#valor_calculo').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
						$('#valor_calculo').addClass('validate-hidden');
						
					}

				}
			});
			
			$('#mensaje_form').html('');
			
			event.stopImmediatePropagation();
		
		});
		
		$(document).off('change', '#id_tipo_formulario').on('change', '#id_tipo_formulario', function(){
			
			var id_cliente = $("#id_cliente").val();
			var id_proyecto = $("#id_proyecto").val();
			var id_tipo_formulario = $(this).val();
			
			select2LoadingStatusOn($('#id_formulario'));

			$.ajax({
				url: '<?php echo_uri("KPI_Values/get_tipo_valor_formulario_group"); ?>',
				type: 'post',
				data: {id_cliente:id_cliente, id_proyecto:id_proyecto, id_tipo_formulario:id_tipo_formulario},
				success: function(respuesta){
					$('#tipo_valor_formulario_group').html(respuesta);
					$('#id_formulario').select2();		
				}
			});
			
			$('#mensaje_form').html('');
			
		});
		
		$(document).on('change', '#id_formulario', function(){
			
			var id_formulario = $(this).val();
			
			select2LoadingStatusOn($('#id_campo_unidad'));

			$.ajax({
				url: '<?php echo_uri("KPI_Values/get_fields_of_form"); ?>',
				type: 'post',
				data: {id_formulario:id_formulario},
				success: function(respuesta){
					$('#campos_formulario_group').html(respuesta);
					$('#id_campo_unidad').select2();
					$('.condicion').select2();
				}
			});

		});
	
		$(document).on('change', '#id_proyecto', function(){
			$('#id_tipo_formulario').val("").trigger('change');
			$('#id_campo_unidad').val("").trigger('change');
			
			
			var tipo_valor = $('input[name=tipo_valor]:checked').val();
			var id_proyecto = $('#id_proyecto').val();
			
			$.ajax({
				url: '<?php echo_uri("KPI_Values/get_campos_tipo_valor"); ?>',
				type: 'post',
				data: {tipo_valor:tipo_valor, id_proyecto: id_proyecto},
				success: function(respuesta){
					
					//initScrollbar(".modal-body", {setHeight: 400});
					
					$('#tipo_valor_group').html(respuesta);
					
					if(tipo_valor == "simple"){
						
						$('#id_tipo_formulario').select2();
						$('#id_formulario').select2();
						$('#id_campo_unidad').select2();
						$('#operador').select2();
						
					}
					
					if(tipo_valor == "compound"){
						
						$('#valor_inicial').select2();
						$('#operador').select2();
						$('#valor_calculo').select2();
						
						$('#operador').attr('data-rule-required', true);
						$('#operador').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
						$('#operador').addClass('validate-hidden');
						
						$('#valor_calculo').attr('data-rule-required', true);
						$('#valor_calculo').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
						$('#valor_calculo').addClass('validate-hidden');
						
					}

				}
			});
			
			$('#mensaje_form').html('');
			
			
		});
		
		$(document).on('click', '.delete', function(){
			initScrollbar(".modal-body", {setHeight: 50});
		});
		
		
		$(document).on('click', '#agregar_valor_operacion', function(event){
		//$('#agregar_valor_operacion').on('click', function(){
			//var row_number = $tbody.attr('id').replace('row_', '');
			if($('#kpi_values-form .valor_operacion').last().length){
				var row_number = parseInt($('#kpi_values-form .valor_operacion').last().attr('id').replace('row_', ''))+1;
			}else{
				var row_number = 2;
			}

			$('#kpi_values-form #grupo_valores_operacion').append($('<div id="row_'+row_number+'">').addClass('form-group valor_operacion').html($('#kpi_values-form #modelo').html()));
			$('#kpi_values-form .valor_operacion').last().find('select[name="operador[]"]').attr('name', 'operador['+row_number+']');
			
			$('#kpi_values-form .valor_operacion').last().find('select[name="operador['+row_number+']"]').attr('data-rule-required', true);
			$('#kpi_values-form .valor_operacion').last().find('select[name="operador['+row_number+']"]').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
			$('#kpi_values-form .valor_operacion').last().find('select[name="operador['+row_number+']"]').addClass('validate-hidden');
			
			$('#kpi_values-form .valor_operacion').last().find('select[name="valor_calculo[]"]').attr('name', 'valor_calculo['+row_number+']');
			$('#kpi_values-form .valor_operacion').last().find('select[name="valor_calculo['+row_number+']"]').attr('data-rule-required', true);
			$('#kpi_values-form .valor_operacion').last().find('select[name="valor_calculo['+row_number+']"]').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
			$('#kpi_values-form .valor_operacion').last().find('select[name="valor_calculo['+row_number+']"]').addClass('validate-hidden');
			
			$('#kpi_values-form .valor_operacion').last().find('select[name="operador['+row_number+']"]').select2();
			$('#kpi_values-form .valor_operacion').last().find('select[name="valor_calculo['+row_number+']"]').select2();
			
			event.stopImmediatePropagation();
			
		});
		
		<?php //if(count($array_informaciones_en_configuracion)){ ?>
		$(document).on('click', '#eliminar_valor_operacion', function(event){
		//$('#eliminar_valor_operacion').on('click', function(){
			$('#kpi_values-form .valor_operacion').last().remove();
			event.stopImmediatePropagation();
		});
		<?php //} ?>
		
		$(document).on('click', '.remover_opcion', function(){
		  $(this).closest('#kpi_values-form .valor_operacion').remove();
		});
		
		$('#tipo_unidad').change(function(){	
					
			var id_tipo_unidad = $(this).val();
			select2LoadingStatusOn($('#unidad'));
			
			$.ajax({
				url:  '<?php echo_uri("KPI_Values/get_unidades_of_tipo_unidad") ?>',
				type:  'post',
				data: {id_tipo_unidad:id_tipo_unidad},
				//dataType:'json',
				success: function(respuesta){
					
					$('#unidades_group').html(respuesta);
					$('#unidad').select2();
				}
			});
					
		});
		
    });
</script>