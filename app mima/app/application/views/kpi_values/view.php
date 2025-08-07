<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $cliente; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="phase" class="<?php echo $label_column; ?>"><?php echo lang('phase'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $fase; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $proyecto; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="type_of_value" class="<?php echo $label_column; ?>"><?php echo lang('type_of_value'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->tipo_valor); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="value_name" class="<?php echo $label_column; ?>"><?php echo lang('value_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_valor; ?>
        </div>
    </div>
    
    <?php if($model_info->tipo_valor == "simple") { ?>
    
        <div class="form-group">
            <label for="form_type" class="<?php echo $label_column; ?>"><?php echo lang('form_type'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $tipo_formulario; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="form" class="<?php echo $label_column; ?>"><?php echo lang('form'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $formulario->nombre; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="indicator_field" class="<?php echo $label_column; ?>"><?php echo lang('indicator_field'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $campo_unidad; ?>
            </div>
        </div>
        
        <?php 
			
			$html = "";
			
			if($model_info->id_tipo_formulario == "1"){
			
				$html .= '<div class="form-group">';
				$html .= 	'<div class="col-md-12 p0">';
				$html .= 	'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
				
				if($formulario->id_tipo_formulario == "1"){
					if($categoria_valor->id){
						
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		lang("category");
						$html .= 	'</div>';
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		$nombre_categoria;
						$html .= 	'</div>';
						$html .= 	'</div>';
						
					}else{
						
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		lang("category");
						$html .= 	'</div>';
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 	'-';
						$html .= 	'</div>';
						$html .= 	'</div>';
					}
				}
				if($formulario->id_tipo_formulario == "1" && $formulario->flujo == "Residuo"){
					
					//if($kpi_valores_condicion_categoria->id){
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
					//}
					
					$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
					$html .= 		lang("type_of_treatment");
					$html .= 	'</div>';
					$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
					$html .= 		$tipo_tratamiento ? $tipo_tratamiento : "-";
					$html .= 	'</div>';
					
					$html .= 	'</div>';
				}
				
				foreach($array_campos_dinamicos as $nombre_campo => $valor){
					
					$html .= 	'<div class="col-md-12 p0">';
					$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
					$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
					$html .= 		$nombre_campo;
					$html .= 	'</div>';
					$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
					$html .= 		$valor ? $valor : "-";
					$html .= 	'</div>';
					$html .= 	'</div>';
					
				}
				
				$html .= '</div>';
				
			}
			
			if($model_info->id_tipo_formulario == "3"){
				
				if(!$formulario->fijo){
				
					$html .= '<div class="form-group">';
					$html .= 	'<div class="col-md-12 p0">';
					$html .= 	'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
					
					$loop_count = 0;
					foreach($array_campos_dinamicos as $nombre_campo => $valor){
						
						if($loop_count > 0){
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
						}
						
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		$nombre_campo;
						$html .= 	'</div>';
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		$valor ? $valor : "-";
						$html .= 	'</div>';
						$html .= 	'</div>';
						
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
					$html .= 			$valor_campo_tipo_edu_amb;
					$html .= 		'</div>';
					$html .= 	'</div>';
					
					$html .= 	'<div class="col-md-12 p0">';
					$html .= 	'<label for="" class="col-md-3"></label>';
					$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
					$html .= 			$campo_tipo_induccion->nombre;
					$html .= 		'</div>';
					$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
					$html .= 			$valor_campo_tipo_induccion;
					$html .= 		'</div>';
					$html .= 	'</div>';
					
					$html .= '</div>';

				}

			}
			
			$html .= '<div class="form-group">';
			$html .= 	'<label for="operacion" class="'.$label_column.'">'.lang('operation').'</label>';
			$html .= 	'<div class="col-md-4">';
			
				if($model_info->operador == "+"){
					$operador = lang("addition") . " ( " . $model_info->operador . " )";
				}
				if($model_info->operador == "-"){
					$operador = lang("subtraction") . " ( " . $model_info->operador . " )";
				}
				if($model_info->operador == "*"){
					$operador = lang("multiplication") . " ( " . $model_info->operador . " )";
				}
				if($model_info->operador == "/"){
					$operador = lang("division") . " ( " . $model_info->operador . " )";
				}
			
			$html .= 	($model_info->operador) ? $operador : "-";
			$html .= 	'</div>';
			$html .= 	'<div class="col-md-4">';
			$html .= 		$model_info->valor_operador;
			$html .= 	'</div>';
			$html .= '</div>';	
			
			echo $html;
			
		?>
		
    <?php } ?>
    
    <?php if($model_info->tipo_valor == "compound") { ?>
    
    	<div class="form-group">
            <label for="initial_value" class="<?php echo $label_column; ?>"><?php echo lang('initial_value'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $valor_inicial; ?>
            </div>
        </div>
        
        <?php
			
			/*
			$html = '';
        	$html .= '<div class="form-group">';
			$html .= 	'<label for="operacion" class="'.$label_column.'">'.lang('compound_operation').'</label>';
			$html .= 	'<div class="col-md-4">';
			
				if($model_info->operador == "+"){
					$operador = lang("addition") . " ( " . $model_info->operador . " )";
				}
				if($model_info->operador == "-"){
					$operador = lang("subtraction") . " ( " . $model_info->operador . " )";
				}
				if($model_info->operador == "*"){
					$operador = lang("multiplication") . " ( " . $model_info->operador . " )";
				}
				if($model_info->operador == "/"){
					$operador = lang("division") . " ( " . $model_info->operador . " )";
				}
			
			$html .= 	$operador;
			$html .= 	'</div>';
			$html .= 	'<div class="col-md-4">';
			$html .= 		$valor_calculo;
			$html .= 	'</div>';
			$html .= '</div>';
			
			echo $html;
			*/
			
			$html = '';
			$html .= '<div id="grupo_valores_operacion">';
        	$loop_count = 1;
			foreach($array_operacion_compuesta as $index => $operacion_compuesta) { 
            
            	$operador = key($operacion_compuesta);
                $id_valor = $operacion_compuesta[key($operacion_compuesta)]; 

                $label = ($loop_count == 1) ? lang('operation_values') : ""; 
				
				$html .= '        <div class="form-group">';
				$html .= '            <label for="description" class="col-md-3">'.$label.'</label>';
				$html .= '            <div class="col-md-4">';
				
				if($operador == "+"){
					$label_operador = lang("addition") . " ( " . $operador . " )";
				}
				if($operador == "-"){
					$label_operador = lang("subtraction") . " ( " . $operador . " )";
				}
				if($operador == "*"){
					$label_operador = lang("multiplication") . " ( " . $operador . " )";
				}
				if($operador == "/"){
					$label_operador = lang("division") . " ( " . $operador . " )";
				}
				
				$nombre_valor = $this->KPI_Values_model->get_one($id_valor)->nombre_valor;
				
				$html .=                $label_operador;
				$html .= '           </div>';
				$html .= '           <div class="col-md-4">';
				$html .=                $nombre_valor;
				$html .= '            </div>';
				
				$html .= '	</div>';
						$loop_count++;
				}
				
				$html .= '</div>';
				
				echo $html;
		
		?>
    
    <?php } ?>
    
    <div class="form-group">
        <label for="unit_field" class="col-md-3"><?php echo lang('unit_type'); ?></label>
        <div class="col-md-4">
            <?php echo $tipo_unidad ? $tipo_unidad : "-"; ?>
        </div>
        <div class="col-md-4">
            <?php echo $unidad ? $unidad : "-"; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?$model_info->modified:'-';
            ?>
        </div>
    </div>
	
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 