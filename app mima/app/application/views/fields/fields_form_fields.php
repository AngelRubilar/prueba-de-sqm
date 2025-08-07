<?php
$this->load->view("includes/summernote");
?>
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<?php 
    
	$mensaje_tooltip = "";
	$info_campo_usado = "";
	
	if($campo_utilizado_en_formulario && !$campo_utilizado_en_matriz && !$campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_form");
	} elseif (!$campo_utilizado_en_formulario && $campo_utilizado_en_matriz && !$campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_matrix");
	} elseif (!$campo_utilizado_en_formulario && !$campo_utilizado_en_matriz && $campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_relationship");
	} elseif ($campo_utilizado_en_formulario && $campo_utilizado_en_matriz && !$campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_form_and_matrix");
	} elseif($campo_utilizado_en_formulario && !$campo_utilizado_en_matriz && $campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_form_and_relationship");
	} elseif(!$campo_utilizado_en_formulario && $campo_utilizado_en_matriz && $campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_matrix_and_relationship");
	} elseif($campo_utilizado_en_formulario && $campo_utilizado_en_matriz && $campo_utilizado_en_relacionamiento){
		$mensaje_tooltip = lang("busy_field_in_form_and_matrix_and_relationship");
	} 
	
	if($campo_utilizado_en_formulario || $campo_utilizado_en_matriz){
		$info_campo_usado = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$mensaje_tooltip.'"><i class="fa fa-question-circle"></i></span>';
	}

?>


<div class="form-group">
    <label for="field_name" class="col-md-2"><?php echo lang('field_name'); ?></label>
    <div class="col-md-10">
        <?php
        echo form_input(array(
            "id" => "field_name",
            "name" => "field_name",
            "value" => $model_info->nombre,
            "class" => "form-control",
            "placeholder" => lang('field_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<!-- listar todos los clientes -->
<div class="form-group">
    <label for="project" class="col-md-2"><?php echo lang('client'). " " . $info_campo_usado; ?></label>
    <div class="col-md-10">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='client_id' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
 <!-- listar todos los clientes -->

<!-- listar todos los proyectos -->
<div id="proyectos_group">
    <div class="form-group">
        <label for="project" class="col-md-2"><?php echo lang('project'). " " . $info_campo_usado; ?></label>
        <div class="col-md-10">
            <?php
			if($model_info){
				$proyectos = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $model_info->id_cliente));
				echo form_dropdown("project", $proyectos, array($model_info->id_proyecto), "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			}else{
				echo form_dropdown("project", array("" => "-"), array($model_info->id_proyecto), "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			}
            
            ?>
        </div>
    </div>
</div>
 
<div class="form-group">
	
    <label for="field_type" class="col-md-2"><?php echo lang('field_type'). " " . $info_campo_usado; ?></label>
    <div class="col-md-10">
        <?php
		
		if($tipo_campo){
			$tipo_campo = $tipo_campo[0]->tipo_campo;
		}else{
			$tipo_campo = "";
		}
		
		echo form_dropdown("field_type", $field_types_dropdown, array($tipo_campo), "id='field_type' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
        
        <?php if($campo_utilizado_en_formulario || $campo_utilizado_en_matriz || $campo_utilizado_en_relacionamiento){ ?>
        <input type="hidden" name="field_type" value="<?php echo $tipo_campo; ?>">
        <?php } ?>
        
    </div>
</div>

<!--
<div class="form-group" id="modelo" style="display:none;">
    <label for="field-ta" class="col-sm-2 control-label"></label>
    <div class="col-md-4">
        <input type="text" class="form-control" name="labels[]" maxlength="255" placeholder="Label" autocomplete="off">
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" name="values[]" maxlength="255" placeholder="Value" autocomplete="off">
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
    </div>
</div>
-->
<div class="form-group" id="modelo" style="display:none;">
    <label for="field-ta" class="col-sm-2 control-label"></label>
    <div class="col-md-8">
        <input type="text" class="form-control" name="values[]" maxlength="255" placeholder="<?php echo lang('value'); ?>" autocomplete="off">
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
    </div>
</div>

<div class="form-group" id="modelo_radio" style="display:none">
    <label for="field-ta" class="col-sm-2 control-label"></label>
    <div class="col-md-8">
        <input type="text" class="form-control" name="values_radio[]" maxlength="255" placeholder="<?php echo lang('option'); ?>" autocomplete="off">
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
    </div>
</div>

<div id="default_value_field_group">
    <?php
	$html = "";
    if($tipo_campo == "Input text"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => lang('default_value_field'),
				"autofocus" => true,
				//"data-rule-required" => true,
				//"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
			    "maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	if($tipo_campo == "Texto Largo"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_textarea(array(
				"id" => "default_value_field",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => lang('default_value_field'),
				"style" => "height:150px;",
				"autocomplete" => "off",
				"maxlength" => "2000"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	if($tipo_campo == "Número"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => lang('default_value_field'),
				"autofocus" => true,
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	if($tipo_campo == "Fecha"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_date_field" class="col-md-2">'.lang('default_date_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_date_field",
				"name" => "default_date_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	if($tipo_campo == "Periodo"){
		$date_default1 = "";
		$date_default2 = "";
		if($model_info->default_value){
			$date_default1 = json_decode($model_info->default_value);
			$date_default2 = json_decode($model_info->default_value);
		}
		$html .= '<div class="form-group">';
			$html .= '<label for="default_date_field" class="col-md-2">'.lang('default_date_field').'</label>';
			$html .= '<div class="col-md-5">';
			$html .= form_input(array(
				"id" => "default_date_field1",
				"name" => "default_date_field1",
				"value" => $date_default1->start_date,
				"class" => "form-control",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';
			
			$html .= '<div class="col-md-5">';
			$html .= form_input(array(
				"id" => "default_date_field2",
				"name" => "default_date_field2",
				"value" => $date_default1->end_date,
				"class" => "form-control",
				"placeholder" => "YYYY-MM-DD",
				"data-rule-greaterThanOrEqual" => "#default_date_field1",
				"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	
	if($tipo_campo == "Selección"){
		
		$array_opciones = array();
		if($model_info->opciones){
			$opciones = json_decode($model_info->opciones);
			foreach($opciones as $index => $opcion){
				if($index == 0){
					$array_opciones[''] = $opcion->text;
				}else{
					$array_opciones[$opcion->value] = $opcion->value;
				}
				//$array_opciones[] = array($opcion->value => $opcion->value);
				
			}
		}
		
		// FILA POR DEFECTO
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("default_value_field", $array_opciones, $model_info->default_value, "id='default_value_field' class='select2'");
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA AGREGAR - QUITAR
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= '<div class="col-md-9">&nbsp;</div>';
			$html .= '<button type="button" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>';
			$html .= '<button type="button" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>';
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA OPCIONES
		$html .= '<div class="form-group default">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('select_field_options').'</label>';
		
			// CAMPO LABEL
			/*$html .= '<div class="col-md-4">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "label",
				"value" => $opciones[0]->text,
				"class" => "form-control",
				"placeholder" => "",
				//"autofocus" => true,
				//"disabled" => true,
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';*/
		
			// CAMPO VALUE
			$html .= '<div class="col-md-8">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "value",
				"value" => $opciones[0]->text,
				"class" => "form-control",
				//"placeholder" => lang('empty'),
				//"autofocus" => true,
				//"readonly" => true,
				"autocomplete" => "off",
				"maxlength" => "255",
				"data-rule-required" => "true",
				"data-msg-required" => lang("field_required")
			));
			$html .= '</div>';
			
		$html .= '</div>';
		
		
		foreach($opciones as $index => $opcion){
			
			if($index == 0){continue;}
			
			$html .= '<div id="row_' . $index . '" class="form-group opcion">';
				$html .= '<label for="default_value_field" class="col-md-2 control-label">'.lang('').'</label>';
			
				// CAMPO LABEL
				/*$html .= '<div class="col-md-4">';
				$html .= form_input(array(
					//"id" => "default_value_field",
					"name" => "labels[]",
					"value" => $opcion->text,
					"class" => "form-control",
					"placeholder" => "Label",
					//"autofocus" => true,
					//"disabled" => true,
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';*/
			
				// CAMPO VALUE
				$html .= '<div class="col-md-8">';
				$html .= form_input(array(
					//"id" => "default_value_field",
					"name" => "values[$index]",
					"value" => $opcion->value,
					"class" => "form-control",
					"placeholder" => lang('value'),
					//"autofocus" => true,
					//"readonly" => true,
					"autocomplete" => "off",
					"maxlength" => "255",
					"data-rule-required" => "true",
					"data-msg-required" => lang("field_required")
				));
				$html .= '</div>';
				
				// BOTON BORRAR
				$html .= '<div class="col-md-2">';
				$html .= form_button(array(
					//"id" => "default_value_field",
					//"name" => "values[]",
					//"value" => $opcion->value,
					"class" => "btn btn-danger",
					"onclick" => "removeOption($(this));",
				), "<i class='fa fa-trash-o'></i>");
				$html .= '</div>';
				
			$html .= '</div>';
			
		}
	}
	
	if($tipo_campo == "Selección Múltiple"){
		
		$array_opciones = array();
		if($model_info->opciones){
			$opciones = json_decode($model_info->opciones);
			foreach($opciones as $index => $opcion){
				/*if($index == 0){
					//$array_opciones[''] = $opcion->text;
				}else{
					$array_opciones[$opcion->value] = $opcion->value;
				}*/
				$array_opciones[$opcion->value] = $opcion->value;
				
				
			}
		}
		
		$array_defaults = json_decode($model_info->default_value);
		
		// FILA POR DEFECTO
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_multiselect("default_value_field[]", $array_opciones, $array_defaults, "id='default_value_field' class='multiple' multiple='multiple'");
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA AGREGAR - QUITAR
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= '<div class="col-md-9">&nbsp;</div>';
			$html .= '<button type="button" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>';
			$html .= '<button type="button" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>';
			$html .= '</div>';
		$html .= '</div>';
		
		foreach($opciones as $index => $opcion){
			
			//if($index == 0){continue;}
			
			$html .= '<div class="form-group opcion">';
				$html .= '<label for="default_value_field" class="col-md-2 control-label">'.lang('').'</label>';
			
				// CAMPO LABEL
				$html .= '<div class="col-md-4">';
				$html .= form_input(array(
					//"id" => "default_value_field",
					"name" => "labels[]",
					"value" => $opcion->text,
					"class" => "form-control",
					"placeholder" => "Label",
					//"autofocus" => true,
					//"disabled" => true,
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';
			
				// CAMPO VALUE
				$html .= '<div class="col-md-4">';
				$html .= form_input(array(
					//"id" => "default_value_field",
					"name" => "values[]",
					"value" => $opcion->value,
					"class" => "form-control",
					"placeholder" => "Value",
					//"autofocus" => true,
					//"readonly" => true,
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';
				
				// BOTON BORRAR
				$html .= '<div class="col-md-2">';
				$html .= form_button(array(
					//"id" => "default_value_field",
					//"name" => "values[]",
					//"value" => $opcion->value,
					"class" => "btn btn-danger",
					"onclick" => "removeOption($(this));",
				), "<i class='fa fa-trash-o'></i>");
				$html .= '</div>';
				
			$html .= '</div>';
			
		}
	}
	
	if($tipo_campo == "Rut"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"class" => "form-control rut",
				"placeholder" => lang('default_value_field'),
				"autofocus" => true,
				//"data-rule-required" => true,
				//"data-msg-required" => lang("field_required"),
				"data-rule-minlength" => 6,
				"data-msg-minlength" => lang("enter_minimum_6_characters"),
				"data-rule-maxlength" => 13,
				"data-msg-maxlength" => lang("enter_maximum_13_characters"),
				"autocomplete" => "off",
					"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	
	if($tipo_campo == "Radio Buttons"){
		
		$array_opciones = array();
		if($model_info->opciones){
			$opciones = json_decode($model_info->opciones);
			foreach($opciones as $opcion){
				$array_opciones[] = array($opcion->value => $opcion->value);
			}
		}
		
		// FILA POR DEFECTO
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("default_value_field", $array_opciones, $model_info->default_value, "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA AGREGAR - QUITAR
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= '<div class="col-md-9">&nbsp;</div>';
			$html .= '<button type="button" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>';
			$html .= '<button type="button" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>';
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA OPCIONES
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('radio_field_options').'</label>';
		
			// CAMPO VALUE
			$html .= '<div class="col-md-8">';
			$html .= form_input(array(
				"id" => "default_value_field1",
				"name" => "values_radio[1]",
				"value" => $opciones[0]->value,
				"class" => "form-control",
				"placeholder" => lang('option_1'),
				//"autofocus" => true,
				"autocomplete" => "off",
				"maxlength" => "255",
				"data-rule-required" => "true",
				"data-msg-required" => lang("field_required")
			));
			$html .= '</div>';
			
		$html .= '</div>';
		
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2"></label>';
		
			// CAMPO VALUE
			$html .= '<div class="col-md-8">';
			$html .= form_input(array(
				"id" => "default_value_field2",
				"name" => "values_radio[2]",
				"value" => $opciones[1]->value,
				"class" => "form-control",
				"placeholder" => lang('option_2'),
				//"autofocus" => true,
				"autocomplete" => "off",
				"maxlength" => "255",
				"data-rule-required" => "true",
				"data-msg-required" => lang("field_required")
			));
			$html .= '</div>';
			
		$html .= '</div>';
		
		
		foreach($opciones as $index => $opcion){
			
			$input_index = $index + 1;
			
			if($index < 2){
				continue;
			}
			
			$html .= '<div id="row_' . $input_index . '" class="form-group opcion">';
				$html .= '<label for="" class="col-md-2 control-label">'.lang('').'</label>';
			
				// CAMPO LABEL
				$html .= '<div class="col-md-8">';
				$html .= form_input(array(
					"id" => "default_value_field1",
					"name" => "values_radio[$input_index]",
					"value" => $opcion->value,
					"class" => "form-control",
					"placeholder" => lang('option_1'),
					//"autofocus" => true,
					"autocomplete" => "off",
					"maxlength" => "255",
					"data-rule-required" => "true",
					"data-msg-required" => lang("field_required")
				));
				$html .= '</div>';
				
				
				// BOTON BORRAR
				$html .= '<div class="col-md-2">';
				$html .= form_button(array(
					//"id" => "default_value_field",
					//"name" => "values[]",
					//"value" => $opcion->value,
					"class" => "btn btn-danger",
					"onclick" => "removeOption($(this));",
				), "<i class='fa fa-trash-o'></i>");
				$html .= '</div>';
				
			$html .= '</div>';
			
		}
		
	}
	
	if($tipo_campo == "Archivo"){
		
	}
	
	if($tipo_campo == "Texto Fijo"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_textarea(array(
				"id" => "default_value_field_rich",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"placeholder" => lang('default_value_field'),
				"class" => "form-control",
				"autocomplete" => "off",
				//"maxlength" => "2000"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	
	if($tipo_campo == "Divisor"){
		
	}
	
	if($tipo_campo == "Correo"){
		$html = '';
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => lang('email'),
				"autofocus" => true,
				"data-rule-email" => true,
				"data-msg-email" => lang("enter_valid_email"),
				//"data-rule-required" => true,
				//"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	
	if($tipo_campo == "Hora"){
		$html .= '<div class="form-group">';
			$html .= '<label for="default_time_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_time_field",
				"name" => "default_time_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => lang('default_value_field'),
				"autofocus" => true,
				//"data-rule-required" => true,
				//"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
				"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';
	}
	
	
	
	if($tipo_campo == "Unidad"){
		
		$seleccionada = "";
		$symbol_seleccionado = "";
		if($model_info->opciones){
			$opciones = json_decode($model_info->opciones);
			$seleccionada = $opciones[0]->id_tipo_unidad;
			$symbol_seleccionado = $opciones[0]->id_unidad;
		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="default_time_field" class="col-md-2">'.lang('default_value_field').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_input(array(
				"id" => "default_value_field",
				"name" => "default_value_field",
				"value" => $model_info->default_value,
				"class" => "form-control",
				"placeholder" => lang('default_value_field'),
				"autofocus" => true,
				//"data-rule-required" => true,
				//"data-msg-required" => lang("field_required"),
				"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
				"data-msg-regex" => lang("number_or_decimal_required"),
				"autocomplete" => "off",
				//"maxlength" => "255"
			));
			$html .= '</div>';
		$html .= '</div>';

		// FILA POR DEFECTO
		$html .= '<div class="form-group multi-column">';
			$html .= '<label for="unit_field" class="col-md-2">'.lang('unit').'</label>';
			$html .= '<div class="col-md-5">';
			$html .= form_dropdown("unit_field", $tipos_de_unidad, $seleccionada, "id='unit_field' class='select2'");
			$html .= '</div>';
			
			
			$html .= '<div id="symbol_group">';
			$html .= '<div class="col-md-5">';
			$html .= form_dropdown("unit_symbol",$symbol_select_values, $symbol_seleccionado, "id='unit_symbol' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			$html .= '</div>';
						
		$html .= '</div>';
		
	}
	
	
	if($tipo_campo == "Selección desde Mantenedora"){
		
		if($model_info->default_value){
			$valores = json_decode($model_info->default_value);
			$id_mantenedora = $valores->mantenedora;
			//$id_campo_label = $valores->field_label;
			$id_campo_value = $valores->field_value;
			
			$array_campos = array();
			$campos = $this->Field_rel_form_model->get_fields_details_related_to_form($id_mantenedora)->result_array();
			foreach ($campos as $row){
				$array_campos[$row['id']] = $row['nombre'];
			}
		}else{
			$array_campos = array();
		}
		
		$html = '';
		
		// FILA SELECT MANTENEDORA
		$mantenedoras = array("" => "-") + $this->Forms_model->get_dropdown_list(array("nombre"), "id", array("id_tipo_formulario" => 2, "id_cliente" => $model_info->id_cliente));
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('feeder_table').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("feeder_table", $mantenedoras, $id_mantenedora, "id='feeder_table' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA SELECT LABEL
		$html .= '<div id="mantenedora_group">';
		/*$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('label').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("labels_field", array("" => "-") + $array_campos, $id_campo_label, "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';*/
		
		// FILA SELECT VALUE
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('value').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("values_field", array("" => "-") + $array_campos, $id_campo_value, "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		
	}
	
	echo $html;
	
	
?>
</div>

<div class="form-group">
    <label for="obligatory_field" class="col-md-3"><?php echo lang('obligatory_field'); ?>
        <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('obligatory_field_description') ?>"><i class="fa fa-question-circle"></i></span>
    </label>
    <div class="col-md-3">
        <?php
        echo form_checkbox("obligatory_field", "1", $model_info->obligatorio ? true : false, "id='obligatory_field'");
        ?>                       
    </div>
    
    <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?>
        <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('disabled_field_description') ?>"><i class="fa fa-question-circle"></i></span>
    </label>
    <div class="col-md-3">
        <?php
        echo form_checkbox("disabled_field", "1", $model_info->habilitado ? true : false, "id='disabled_field'");
        ?>                       
    </div>
    
</div>



<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
		$('#fields-form .select2').select2();
		$('#fields-form .select2_multiple').select2({
			multiple: true
		});
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#fields-form .rut').rut({
			formatOn: 'keyup',
			minimumLength: 8,
			validateOn: 'change'
		});
		
		setDatePicker("#default_date_field");
		setDatePicker("#default_date_field1, #default_date_field2");
		setTimePicker("#default_time_field");
		
		if($.trim($("#fields-form #field_type option:selected").text()) == 'Texto Fijo' || 
		$.trim($("#fields-form #field_type option:selected").text()) == 'Divisor'){
			initWYSIWYGEditor("#default_value_field_rich", {
				height: 200,
				toolbar: [
					['style', ['style']],
					['font', ['bold', 'italic', 'underline', 'clear']],
					['fontname', ['fontname']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['height', ['height']],
					//['table', ['table']],
					//['insert', ['hr', 'picture', 'video']],
					['view', [/*'fullscreen'*/, 'codeview']]
				],
				onImageUpload: function (files, editor, welEditable) {
					//insert image url
				},
				lang: "<?php echo lang('language_locale_long'); ?>"
			});
			$("#fields-form #obligatory_field").prop('checked', false);
			$("#fields-form #obligatory_field").prop("disabled", true);
			$("#fields-form #disabled_field").prop('checked', false);
			$("#fields-form #disabled_field").prop("disabled", true);
		}else{
			if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección desde Mantenedora'){
				$("#fields-form #obligatory_field").prop("disabled", false);
				$("#fields-form #disabled_field").prop('checked', false);
				$("#fields-form #disabled_field").prop("disabled", true);
			}else{
				$("#fields-form #obligatory_field").prop("disabled", false);
				$("#fields-form #disabled_field").prop("disabled", false);
			}
			
		}
		
		$('#fields-form .multiple').multiSelect();
		
		// ONCHANGE CLIENTE -> TRAE PROYECTOS
		$('#fields-form #client_id').change(function(){
				
			var id_client = $(this).val();	
			select2LoadingStatusOn($('#project'));
			
			$.ajax({
				url:  '<?php echo_uri("clients/get_projects_of_client") ?>',
				type:  'post',
				data: {id_client:id_client, col_label:'col-md-2', col_projects:'col-md-10'},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
				}
			});
			
		});	
		
		
		$('#fields-form #field_type').change(function(){
			var id_field = $(this).val();
			var client_id = $('#client_id').val();
			var project_id = $('#project').val();
			
			$.ajax({
				url:  '<?php echo_uri("fields/get_field_type_options"); ?>',
				type:  'post',
				data: {id_field:id_field, client_id:client_id, project_id:project_id},
				//dataType:'json',
				success: function(respuesta){
					
					$('#default_value_field_group').html(respuesta);
					
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
					
					setDatePicker("#default_date_field");
					setDatePicker("#default_date_field1, #default_date_field2");
					setTimePicker("#default_time_field");
					$('#fields-form select.select2').select2();
					
					$('#fields-form .rut').rut({
						formatOn: 'keyup',
						minimumLength: 8,
						validateOn: 'change'
					});
					
					if($.trim($("#fields-form #field_type option:selected").text()) == 'Texto Fijo' || 
					$.trim($("#fields-form #field_type option:selected").text()) == 'Divisor'){
						initWYSIWYGEditor("#default_value_field_rich", {
							height: 200,
							toolbar: [
								['style', ['style']],
								['font', ['bold', 'italic', 'underline', 'clear']],
								['fontname', ['fontname']],
								['color', ['color']],
								['para', ['ul', 'ol', 'paragraph']],
								['height', ['height']],
								//['table', ['table']],
								//['insert', ['hr', 'picture', 'video']],
								['view', [/*'fullscreen'*/, 'codeview']]
							],
							onImageUpload: function (files, editor, welEditable) {
								//insert image url
							},
							lang: "<?php echo lang('language_locale_long'); ?>"
						});
						$("#fields-form #obligatory_field").prop('checked', false);
						$("#fields-form #obligatory_field").prop("disabled", true);
						$("#fields-form #disabled_field").prop('checked', false);
						$("#fields-form #disabled_field").prop("disabled", true);
					}else{
						if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección desde Mantenedora'){
							$("#fields-form #obligatory_field").prop("disabled", false);
							$("#fields-form #disabled_field").prop('checked', false);
							$("#fields-form #disabled_field").prop("disabled", true);
						}else{
							$("#fields-form #obligatory_field").prop("disabled", false);
							$("#fields-form #disabled_field").prop("disabled", false);
						}
						
					}

					$('#fields-form .multiple').multiSelect();
					
					
				}
			});
			
			
		});
		
		$('#fields-form').on('change', '#unit_field', function(){
			
			var unidad = $(this).val();
			select2LoadingStatusOn($('#unit_symbol'));
			
			$.ajax({
				url:  '<?php echo_uri("fields/get_symbol_of_unit") ?>', //CREAR EN CONTROLADOR
				type:  'post',
				data: {unidad:unidad},
				//dataType:'json',
				success: function(respuesta){;
					$('#symbol_group').html(respuesta);
					$('#unit_symbol').select2();
				}
			});
			
		});
		
		
		$("#fields-form").on("select2-opening", '#default_value_field', function() {
		
			if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección'){
		
				$(this).empty();
				//$("#fields-form #default_value_field").append("<option value=''>-</option>");
				
				var default_label = $("#fields-form .default input[name='value']").val();
				$("#fields-form #default_value_field").append("<option value=''>"+default_label+"</option>");
				
				var index = 1;
				
				$("#fields-form .opcion").each(function(){
					var value = $(this).find("input[name='values[" + index++ + "]']").val();
					$("#fields-form #default_value_field").append("<option value='"+value+"'>"+value+"</option>");
				});
			}
			
			if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección Múltiple'){
				
			}
			
			if($.trim($("#fields-form #field_type option:selected").text()) == 'Radio Buttons'){
				
				$(this).empty();
				var val1 = $("#fields-form #default_value_field1").val();
				var val2 = $("#fields-form #default_value_field2").val();
				$("#fields-form #default_value_field").append("<option value='"+val1+"'>"+val1+"</option>");
				$("#fields-form #default_value_field").append("<option value='"+val2+"'>"+val2+"</option>");
				
				var index = 3;
				
				$("#fields-form .opcion").each(function(){
					var value = $(this).find("input[name='values_radio[" + index++ + "]']").val();
					$("#fields-form #default_value_field").append("<option value='"+value+"'>"+value+"</option>");
				});
				
			}
			
			//$.trim($("#fields-form #field_type option:selected").text()) == 'Selección Múltiple'
			
		});
		
		//para el multiselect
		//$("#fields-form").on("focus", '#default_value_field_group #ms-default_value_field ul', function() {
		$("#fields-form").on("focusout", '#default_value_field_group input', function() {
			if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección Múltiple'){
				multiselectRefresh();
			}
		});
		
		
		// ONCHANGE MANTENEDORA -> TRAE CAMPOS DE ESA MANTENEDORA
		//$('#fields-form #feeder_table').change(function(){
		$("#fields-form").on("change", '#feeder_table', function() {
				
			var id_feeder = $(this).val();	
			select2LoadingStatusOn($('#labels_field'));
			select2LoadingStatusOn($('#values_field'));
			
			$.ajax({
				url:  '<?php echo_uri("fields/get_fields_of_feeder") ?>',
				type:  'post',
				data: {id_feeder:id_feeder},
				//dataType:'json',
				success: function(respuesta){
					
					$('#mantenedora_group').html(respuesta);
					$('#labels_field, #values_field').select2();
				}
			});
			
		});	
		

    });
	
function addOptions(){
	if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección' || 
	$.trim($("#fields-form #field_type option:selected").text()) == 'Selección Múltiple'){
		
		if($('#fields-form .opcion').last().length){
			var row_number = parseInt($('#fields-form .opcion').last().attr('id').replace('row_', ''))+1;
		}else{
			var row_number = 1;
		}

		$('#fields-form #default_value_field_group').append($('<div id="row_'+row_number+'">').addClass('form-group opcion').html($('#fields-form #modelo').html()));
		$('#fields-form .opcion').last().find('input').attr('name', 'values['+row_number+']');
		
		$('#fields-form .opcion').last().find('input').attr('data-rule-required', true);
		$('#fields-form .opcion').last().find('input').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
		
		//$('#fields-form #default_value_field_group').append($("<div />").addClass('form-group opcion').html($('#fields-form #modelo').html()));
		//$('#fields-form .opcion').last().find('input').attr('required', true);
		
		
		$('#fields-form .opcion').last().find('input').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
	}
	
	if($.trim($("#fields-form #field_type option:selected").text()) == 'Radio Buttons'){
		
		if($('#fields-form .opcion').last().length){
			var row_number = parseInt($('#fields-form .opcion').last().attr('id').replace('row_', ''))+1;
		}else{
			var row_number = 3;
		}

		$('#fields-form #default_value_field_group').append($('<div id="row_'+row_number+'">').addClass('form-group opcion').html($('#fields-form #modelo_radio').html()));
		$('#fields-form .opcion').last().find('input').attr('name', 'values_radio['+row_number+']');
		
		$('#fields-form .opcion').last().find('input').attr('data-rule-required', true);
		$('#fields-form .opcion').last().find('input').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
		
		//$('#fields-form #default_value_field_group').append($("<div />").addClass('form-group opcion').html($('#fields-form #modelo_radio').html()));
		//$('#fields-form .opcion').last().find('input').attr('required', true);
		$('#fields-form .opcion').last().find('input').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
	}
	
}
function removeOptions(){
	$('#fields-form .opcion').last().remove();
	
	if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección Múltiple'){
		multiselectRefresh();
	}
	
}
function removeOption(element){
	element.closest('#fields-form .opcion').remove();
	
	if($.trim($("#fields-form #field_type option:selected").text()) == 'Selección Múltiple'){
		multiselectRefresh();
	}
}

function multiselectRefresh(){
	
	var array_seleccionadas = $('#fields-form .multiple').val();
	
	$('#fields-form .multiple').empty();
	
	var default_label = $("#fields-form .default input[name='value']").val();
	//$("#fields-form #default_value_field").append("<option value=''>"+default_label+"</option>");
	$("#fields-form .opcion").each(function(){
		var value = $(this).find("input[name='values[]']").val();
		if($.inArray(value, array_seleccionadas) != -1){
			$("#fields-form .multiple").append("<option value='"+value+"' selected>"+value+"</option>");
		}else{
			$("#fields-form .multiple").append("<option value='"+value+"'>"+value+"</option>");
		}
	});
	$('#fields-form .multiple').multiSelect('refresh');
}

/*function updateDefaults(){
	$("#fields-form #default_value_field").select2("destroy");
			
	$("#fields-form #default_value_field").html("");
	$("#fields-form #default_value_field").append("<option value=''>-</option>");
	$("#fields-form .opcion").each(function(){
		var value = $(this).find("input[name=values]").val();
		$("#fields-form #default_value_field").append("<option value='"+value+"'>"+value+"</option>");
	});
	$("#fields-form select#default_value_field").select2();
}*/


	<?php  if($campo_utilizado_en_formulario || $campo_utilizado_en_matriz || $campo_utilizado_en_relacionamiento){ ?>
		$('#client_id').attr('disabled','disabled');
		$('#project').attr('disabled','disabled');
		$('#field_type').attr('disabled','disabled');
	<?php } ?>


</script>