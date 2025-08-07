<input type="hidden" name="id" id="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<?php if($form_rel_project->id){ ?>
	<input type="hidden" name="id_form_rel_project" value="<?php echo $form_rel_project->id; ?>" />
<?php } ?>
<!-- Form name label -->
<div class="form-group">
    <label for="nombre" class="<?php echo $label_column; ?>"><?php echo lang('form_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "nombre",
            "name" => "nombre",
            "value" => $model_info->nombre,
            "class" => "form-control",
            "placeholder" => lang('form_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<!-- Form description label -->
<div class="form-group">
    <label for="descripcion" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_textarea(array(
            "id" => "descripcion",
            "name" => "descripcion",
            "value" => htmlspecialchars_decode($model_info->descripcion),
            "class" => "form-control",
            "placeholder" => lang('description'),
            "autofocus" => false,
			"autocomplete"=> "off",
			"maxlength" => "2000"
        ));
        ?>
    </div>
</div>

<!-- listar todos los clientes -->
<?php
	$disabled = '';
	$info = '';
	if($campos_disabled){
		$disabled = 'disabled="disabled"';
		$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
	}
?>
<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client') . ' ' . $info; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='client_id' class='select2 validate-hidden' data-sigla='$sigla_cliente' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled");
		?>
    </div>
    <?php if($campos_disabled){ ?>
    	<input type="hidden" name="client" value="<?php echo $model_info->id_cliente; ?>" />
    <?php } ?>
</div>
 <!-- listar todos los clientes -->

<!-- listar todos los proyectos -->
<?php
	$disabled = '';
	$info = '';
	if($campos_disabled){
		$disabled = 'disabled="disabled"';
		$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
	}
?>
<div id="proyectos_group">
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project') . ' ' . $info; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project", $proyectos, array($proyecto->id), "id='project' class='select2 validate-hidden' data-sigla='$sigla_proyecto' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled");
            ?>
        </div>
        <?php if($campos_disabled){ ?>
    		<input type="hidden" name="project" value="<?php echo $proyecto->id; ?>" />
    	<?php } ?>
    </div>
</div>
 <!-- listar todos los proyectos -->

<?php
	$array_input_form_number = array(
		"id" => "form_number",
		"name" => "form_number",
		"value" => $model_info->numero,
		"class" => "form-control",
		"placeholder" => lang('form_number'),
		"autofocus" => true,
		"data-rule-required" => true,
		"data-msg-required" => lang("field_required"),
		"autocomplete"=> "off",
		//"maxlength" => "255"
	);
	$disabled = '';
	$info = '';
	if($campos_disabled){
		$array_input_form_number["disabled"] = "disabled";
		$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
	}
?>
<div class="form-group">
	<label for="sge" class="<?php echo $label_column; ?>"><?php echo lang('form_number').' '.$info; ?></label>
	<div class="<?php echo $field_column; ?>">
    	<?php 
			echo form_input($array_input_form_number);
		?>
        <?php if($campos_disabled){ ?>
    		<input type="hidden" name="form_number" value="<?php echo $model_info->numero; ?>" />
    	<?php } ?>
	</div>
</div>

<div class="form-group">
	<label for="codigo" class="<?php echo $label_column; ?>"><?php echo lang("code"); ?></label>
	<div class="<?php echo $field_column; ?>">
    	<?php 
			echo form_input(array(
				"id" => "codigo",
				"name" => "codigo",
				"value" => $model_info->codigo,
				"class" => "form-control",
				"placeholder" => lang('code'),
				"autofocus" => true,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
				"readonly" => "readonly"
        	));
		?>
	</div>
</div>

<div class="form-group">
	<label for="icono" class="<?php echo $label_column; ?>"><?php echo lang("icon"); ?></label>
	<div class="<?php echo $field_column; ?>">
        <select name="icono" id="icono" class="select2 validate-hidden" data-rule-required="true" data-msg-required="Este campo es requerido." tabindex="-1" title="iconos" aria-required="true">
            <option value="">-</option>
			<?php foreach($iconos as $icono) { ?>
            	<?php if($icono == 'empty.png'){
					continue;
				}?>
                <option value="<?php echo $icono ?>" ><?php echo $icono ?></option>
            <?php } ?>
        </select>
	</div>
</div>


<!--form type dropdown-->
<?php
	$disabled = '';
	$info = '';
	if($campos_disabled){
		$disabled = 'disabled="disabled"';
		$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
	}
?>
<div class="form-group">
    <label for="tipo_formulario" class="<?php echo $label_column; ?>"><?php echo lang('category') . ' ' . $info; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("tipo_formulario", $tipo_formulario, array($model_info->id_tipo_formulario), "id='tipo_formulario' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled");
		?> 
    </div>
    <?php if($campos_disabled){ ?>
    	<input type="hidden" name="tipo_formulario" value="<?php echo $model_info->id_tipo_formulario; ?>" />
    <?php } ?>
</div>

<div id="flow_group">

<?php if($model_info->flujo) { ?>
 	
	<?php 	
		$checked_consumo = ($model_info->flujo == "Consumo") ? "checked" : "";
		$checked_residuo = ($model_info->flujo == "Residuo") ? "checked" : "";
		$checked_no_aplica = ($model_info->flujo == "No Aplica") ? "checked" : "";
	?>
    
    <?php
	
		$array_input_radio_consumo = array(
			"id" => "consumo",
			"name" => "flow",
			"value" => "Consumo",
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			$checked_consumo => $checked_consumo
		);
		
		$array_input_radio_residuo = array(
			"id" => "residuo",
			"name" => "flow",
			"value" => "Residuo",
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			$checked_residuo => $checked_residuo
		);
		
		$array_input_radio_no_aplica = array(
			"id" => "no_aplica",
			"name" => "flow",
			"value" => "No Aplica",
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			$checked_no_aplica => $checked_no_aplica
		);
		
		$disabled = '';
		$info = '';
		if($campos_disabled){
			$array_input_radio_consumo["disabled"] = "disabled";
			$array_input_radio_residuo["disabled"] = "disabled";
			$array_input_radio_no_aplica["disabled"] = "disabled";
			$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
		}
		
	?>
    
    <div class="form-group">
        <label for="flow" class="<?php echo $label_column; ?>"><?php echo lang('flow').' '.$info; ?></label>
        <div class="<?php echo $field_column; ?>">
        
            <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
                <?php echo lang("consumption");?>
            </div>
            <div class="col-md-9 col-sm-9 col-xs-9">
                <?php echo form_radio($array_input_radio_consumo); ?>	 
            </div>
        
            <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
                <?php echo lang("one_waste");?>
            </div>
            <div class="col-md-9 col-sm-9 col-xs-9">
                <?php echo form_radio($array_input_radio_residuo); ?>	 
            </div>
            
            <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
                <?php echo lang("does_not_apply");?>
            </div>
            <div class="col-md-9 col-sm-9 col-xs-9">
                <?php echo form_radio($array_input_radio_no_aplica); ?>	 
            </div>
            
            <?php if($campos_disabled){ ?>
                <input type="hidden" name="flow" value="<?php echo $model_info->flujo; ?>" />
            <?php } ?>
            
        </div>
    </div>

<?php } ?>
</div>

<div id="unidad_residuos_group">

	<?php if(($model_info->flujo == "Residuo") || ($model_info->flujo == "Consumo") || ($model_info->flujo == "No Aplica")) { ?>
    
		<?php if($model_info->unidad) { ?>
        
			<?php 
			
				$array_input_unit_field_name = array(
					"id" => "waste_unit",
					"name" => "waste_unit",
					"value" => $unidad_residuo,
					"class" => "form-control",
					"placeholder" => lang('unit_field_name'),
					"autofocus" => true,
					"data-rule-required" => true,
					"data-msg-required" => lang("field_required"),
					"autocomplete"=> "off",
					"maxlength" => "255"
				);
				
				$info = '';
                if($unidad_disabled || $campos_disabled){
					$array_input_unit_field_name["disabled"] = "disabled";
					$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
                } 
				
            ?> 
            
            <div class="form-group">
                <label for="waste_unit" class="<?php echo $label_column; ?>"><?php echo lang('unit_field_name').' '.$info; ?></label>
                <div class="<?php echo $field_column; ?>">
                    <?php
                    echo form_input($array_input_unit_field_name);
                    ?>
                    <?php if($unidad_disabled || $campos_disabled){ ?>
                        <input type="hidden" name="waste_unit" value="<?php echo $unidad_residuo; ?>" />
                    <?php } ?>
                </div>
            </div>
                
            <?php 
				$disabled_unidad = '';
				$info = '';
				if($unidad_disabled || $campos_disabled){
					$disabled_unidad = 'disabled="disabled"';
					$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
				}
			?>
            
            <div class="form-group multi-column">
                <label for="unit_field" class="<?php echo $label_column; ?>"><?php echo lang('unit_type').' '.$info; ?></label>
                <div class="col-md-4">
                 <?php echo form_dropdown("unit_field", $tipos_unidades_disponibles, $tipo_unidad_seleccionado, "id='unit_field' class='select2' $disabled_unidad"); ?>
                </div>
        
                <div id="symbol_group">
                    <div class="col-md-4">
                        <?php echo form_dropdown("unit_symbol",$unidades_disponibles, $unidad_seleccionado, "id='unit_symbol' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled_unidad"); ?>
                    </div>
                </div>
                
                <?php if($unidad_disabled || $campos_disabled){ ?>
                    <input type="hidden" name="unit_field" value="<?php echo $tipo_unidad_seleccionado; ?>" />
                    <input type="hidden" name="unit_symbol" value="<?php echo $unidad_seleccionado; ?>" />
                <?php } ?>
                
            </div>
            
		<?php }?>
        
	<?php } ?>
    
</div>


<div id="tipo_tratamiento_group">

	<?php if($model_info->flujo == "Residuo") { ?>

        <?php 
			$disabled_tipo_tratamiento = '';
			$info = '';
			if($tipo_tratamiento_disabled || $campos_disabled){
				$disabled_tipo_tratamiento = 'disabled="disabled"';
				$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
			}
		?>
        
        <div class="form-group">
            <label for="type_of_treatment" class="col-md-3"><?php echo lang('default_type_of_treatment').' '.$info; ?></label>
            <div class="col-md-4">
                <?php
                echo form_dropdown("type_of_treatment", array("" => "-") + $tipos_tratamiento, $tipo_tratamiento, "id='type_of_treatment' class='select2 validate-hidden' $disabled_tipo_tratamiento");
                ?>
            </div>
        
            <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?>
                <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('disabled_field_description') ?>"><i class="fa fa-question-circle"></i></span>
            </label>
            <div class="col-md-2">
                <?php
                echo form_checkbox("disabled_field", "1", $disabled_field ? true : false, "id='disabled_field' $disabled_tipo_tratamiento");
                ?>                       
            </div>
        </div>
	
	<?php } ?>
    
</div>


<div id="consumo_group">

	<?php if($model_info->flujo == "Consumo") { ?>
    	
		<?php 
			$disabled_tipo_origen = '';
			$info = '';
			if($type_of_origin_disabled || $campos_disabled){
				$disabled_tipo_origen = 'disabled="disabled"';
				$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
			}
		?>
                
        <div class="form-group">
            <label for="type_of_origin" class="col-md-3"><?php echo lang('type_of_origin').' '.$info; ?></label>
            <div class="col-md-4">
                <?php
                echo form_dropdown("type_of_origin", array("" => "-") + $array_tipos_origen, $type_of_origin, "id='type_of_origin' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' $disabled_tipo_origen");
                ?>
                <?php if($type_of_origin_disabled || $campos_disabled){ ?>
                    <input type="hidden" name="type_of_origin" value="<?php echo $type_of_origin; ?>" />
                <?php } ?>
            </div>
            
            <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?>
                <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('disabled_field_description') ?>"><i class="fa fa-question-circle"></i></span>
            </label>
            <div class="col-md-2">
                <?php
                	echo form_checkbox("disabled_field", "1", ($disabled_type_of_origin || $campos_disabled) ? true : false, "id='disabled_field' $disabled_tipo_origen");
                ?>
                <?php if($type_of_origin_disabled || $campos_disabled){ ?>
                    <input type="hidden" name="disabled_field" value="<?php echo ($disabled_type_of_origin || $campos_disabled) ? true : false; ?>" />
                <?php } ?>                     
            </div>
        </div>
        
    <?php } ?>

</div>

<div id="consumo_default_matter_group">

	<?php if($model_info->flujo == "Consumo" && $type_of_origin_name == "matter"){ ?>
    
        <?php 
			$disabled_default_matter = '';
			$info = '';
			if($type_of_origin_disabled || $campos_disabled){
				$disabled_default_matter = 'disabled="disabled"';
				$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
			}
		?>
        
        <div class="form-group">
            <label for="type_of_origin" class="col-md-3"><?php echo lang('default_matter').' '.$info; ?></label>
            <div class="col-md-4">
                <?php echo form_dropdown("default_matter", $array_materias_por_defecto, $default_matter, "id='default_matter' class='select2 validate-hidden' $disabled_default_matter"); ?>
            	<?php if($type_of_origin_disabled || $campos_disabled){ ?>
                    <input type="hidden" name="default_matter" value="<?php echo $default_matter; ?>" />
                <?php } ?>    
            </div>
        </div>
        
    <?php } ?>
    
</div>

<div id="no_aplica_group">
<?php if($model_info->flujo == "No Aplica") { ?>
	<?php $disabled_no_aplica = ($disabled_default_type)?'disabled="disabled"':'';?>
    <div class="form-group">
        <label for="default_type" class="col-md-3"><?php echo lang('default_type') ?></label>
        <div class="col-md-4">
            <?php
            echo form_dropdown("default_type", array("" => "-") + $array_tipos_por_defecto, $tipo_por_defecto, "id='default_type' class='select2 validate-hidden' $disabled_no_aplica");
            ?>
        </div>
        
        <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?>
            <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('disabled_field_description') ?>"><i class="fa fa-question-circle"></i></span>
        </label>
        <div class="col-md-2">
            <?php
            echo form_checkbox("disabled_field", "1", $default_type_disabled ? true : false, "id='disabled_field' $disabled_no_aplica");
            ?>                       
        </div>
    </div>    
<?php } ?>
</div>

<div id="materiales_group">
	<?php
        
		if($materiales_disponibles && $model_info->id_tipo_formulario == 1){
			
			$arraySelected = array();
			$arraySelected2 = array();
			$arrayMaterialesFormulario = array();

			foreach($materiales_de_formulario as $innerArray){
				$arraySelected[] = $innerArray["id"];
				$arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
			}

			foreach($materiales_disponibles as $innerArray){
				if(array_search($innerArray->nombre, $arraySelected2) === FALSE){
					$arrayMaterialesFormulario[(string)$innerArray->id] = $innerArray->nombre;
				}
				
			}
			$array_final = $arraySelected2 + $arrayMaterialesFormulario;
			
			$disabled = '';
			$info = '';
			if($campos_disabled){
				$disabled = 'disabled="disabled"';
				$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
			}
			
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="campos" class="col-md-3">'.lang('materials').' '.$info.'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_multiselect("materiales[]", $array_final, $arraySelected, "id='materiales' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled");
				$html .= '</div>';
			$html .= '</div>';
			
			echo $html;
		}

    ?>
</div>

<div id="categorias_group">
	<?php
        
		if($categorias_disponibles && $model_info->id_tipo_formulario == 1){
							
			$arraySelected = array();
			foreach($categorias_de_formulario as $cf){
				$arraySelected[] = $cf->id;
			}
			
			$disabled = '';
			$info = '';
			if($campos_disabled){
				$disabled = 'disabled="disabled"';
				$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_form_values_msj').'"><i class="fa fa-question-circle"></i></span>';
			}
			
			$html = '';
			$html .= '<div class="form-group">';
	
				$html .= '<label for="categorias" class="col-md-3">'.lang('categories').' '.$info.'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_multiselect("categorias[]", $categorias_disponibles, $arraySelected, "id='categorias' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled");
				$html .= '</div>';
			$html .= '</div>';
			
			echo $html;
			
		}

    ?>
</div>

<!-- multiselect campos del proyecto -->
<div id="fields_group">
<?php  
		
	$arraySelected = array();
	$arraySelected2 = array();
	$arrayCamposProyecto = array();

	foreach($campos_de_formulario as $innerArray){
		$arraySelected[] = $innerArray["id"];
		$arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
	}		
	foreach($campos_de_proyecto as $innerArray){
		if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
			$arrayCamposProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
		}
		
	}
	natcasesort($arrayCamposProyecto);
	
	$array_final = $arraySelected2 + $arrayCamposProyecto;
	
	
	
	$disabled = ($campos_disabled)?'disabled="disabled"':'';
	$info = ($campos_disabled)?'<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_fields_info').'"><i class="fa fa-question-circle"></i></span>':'';
	

	
	$html = '';
	$html .= '<div class="form-group">';
		$html .= '<label for="campos" class="col-md-3">'.lang('fields').' '.$info.'</label>';
		
		$html .= '<div class="col-md-9">';
		$html .= form_multiselect("campos[]", $array_final, $arraySelected, "id='campos' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "' $disabled");
		$html .= '</div>';
	$html .= '</div>';
	
	echo $html;
	
?>
</div>
<!-- <input type="hidden" name="campos_multiple_value" id="campos_multiple_value"  /> -->
<!-- multiselect campos del proyecto -->
<style>
.seleccionado{
	background-color: #08c;
	text-color: #fff !important;
}
.multiselect-header{
  text-align: center;
  padding: 3px;
  background: #7988a2;
  color: #fff;
}
</style>

<!--Script here-->
<script type="text/javascript">

$(document).ready(function () {		
	
	$('[data-toggle="tooltip"]').tooltip();
	$('#forms-form .select2').select2();
	
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
	
	//$('#select-from').css('');

	$('#campos').multiSelect({
		selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
		selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
		//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-6'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-6'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
		keepOrder: true,
		afterSelect: function(value){
			$('#campos option[value="'+value+'"]').remove();
			$('#campos').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
			
		},
		afterDeselect: function(value){ 
			$('#campos option[value="'+value+'"]').removeAttr('selected');
			
		},
	});
			
	$('#materiales').multiSelect({
		selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_materials"); ?>" + "</div>",
		selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_materials"); ?>" + "</div>",
		keepOrder: true,
		afterSelect: function(value){
			$('#materiales option[value="'+value+'"]').remove();
			$('#materiales').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
		},
		afterDeselect: function(value){ 
			$('#materiales option[value="'+value+'"]').removeAttr('selected'); 
		}
	 });
	 
	 $('#categorias').multiSelect({
		selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_categories"); ?>" + "</div>",
		selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_categories"); ?>" + "</div>",
		keepOrder: true,
		afterSelect: function(value){
			$('#categorias option[value="'+value+'"]').remove();
			$('#categorias').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
		},
		afterDeselect: function(value){ 
			$('#categorias option[value="'+value+'"]').removeAttr('selected'); 
		}
	 });
	
	
	
	
	
	$('#client_id').change(function(){
		
		nombre_registro = $("#nombre").val().replace(/ /g,"");
		numero_formulario = $("#form_number").val().replace(/ /g,"");	
		
		$('#fields_group').html("");		
		var id_client = $(this).val();	
		select2LoadingStatusOn($('#project'));
				
		$.ajax({
			url: '<?php echo_uri("clients/get_projects_of_client"); ?>',
			type: 'post',
			data: {id_client:id_client},
			success: function(respuesta){
				$('#proyectos_group').html(respuesta);
				$('#project').select2();
			}
		});
		
		$("#form_number").trigger('focus').trigger('focusout');

	});	
	
	//$(document).off().on("change","#project", function(e){
	$(document).on("change","#project", function(event){
	
		$('#fields_group').html("");
		
		var id_project = $(this).val();	
		var id_form = $('#id').val();
		
		$.ajax({
			url:  '<?php echo_uri("projects/get_fields_of_project2"); ?>',
			type:  'post',
			data: {id_project:id_project, id_form:id_form},
			success: function(respuesta){
				$('#fields_group').html(respuesta);
				$('#campos').multiSelect({
					selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
					selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
					//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-4'><a id='deseleccionar' class='btn btn-xs btn-default'><i class='fa fa-arrow-left' aria-hidden='true'></i></a></div><div class='col-md-4'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-4'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
					keepOrder: true,
					afterSelect: function(value){
						$('#campos option[value="'+value+'"]').remove();
						$('#campos').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
					},
					afterDeselect: function(value){ 
						$('#campos option[value="'+value+'"]').removeAttr('selected');
					},
				});
			}
		});
		
		var tipo_formulario = $('#tipo_formulario').val();
		if(tipo_formulario == 1){
			
			$.ajax({
				url:  '<?php echo_uri("forms/get_materials_of_project"); ?>',
				type:  'post',
				data: {project:id_project},
				success: function(respuesta){			
	
					$('#materiales_group').html(respuesta);
					
					$('#materiales').multiSelect({
							selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_materials"); ?>" + "</div>",
							selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_materials"); ?>" + "</div>",
							keepOrder: true,
							afterSelect: function(value){
								$('#materiales option[value="'+value+'"]').remove();
								$('#materiales').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
							},
							afterDeselect: function(value){ 
								$('#materiales option[value="'+value+'"]').removeAttr('selected'); 
							}
					 }); 
					
				}
			});
			
		} else {
			$('#materiales_group').html("");
		}
		
		$("#form_number").trigger('focus').trigger('focusout');
		set_codigo_formulario();
		
		event.stopImmediatePropagation();
		
	});
	
	
	$('#tipo_formulario').change(function(){
		
		var tipo_formulario = $(this).val();
		var project = $('#project').val();
		
		if(tipo_formulario == 1){
			
			$.ajax({
				url: '<?php echo_uri("forms/get_materials_of_project"); ?>',
				type: 'post',
				data: {project:project},
				success: function(respuesta){			
	
					$('#materiales_group').html(respuesta);
					$('#materiales').multiSelect({
						selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_materials"); ?>" + "</div>",
						selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_materials"); ?>" + "</div>",
						keepOrder: true,
						afterSelect: function(value){
							$('#materiales option[value="'+value+'"]').remove();
							$('#materiales').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
						},
						afterDeselect: function(value){ 
							$('#materiales option[value="'+value+'"]').removeAttr('selected'); 
						}
					 }); 
				}
			});
			
			$('#materiales_group').html("");
			$('#fields_group').html("");				
			//Cargar todos los campos menos tipo mantenedora 
			
			$.ajax({
				url:  '<?php echo_uri("projects/get_fields_of_project2"); ?>',
				type:  'post',
				data: {id_project:project, tipo_formulario:tipo_formulario},
				success: function(respuesta){
					$('#fields_group').html(respuesta);
					$('#campos').multiSelect({
						selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
						selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
						keepOrder: true,
						afterSelect: function(value){
							$('#campos option[value="'+value+'"]').remove();
							$('#campos').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
						},
						afterDeselect: function(value){ 
							$('#campos option[value="'+value+'"]').removeAttr('selected'); 
						},
					});
				}
			});
			
			$.ajax({
				url: '<?php echo_uri("forms/get_flow_radio"); ?>',
				type: 'post',
				success: function(respuesta){
					$('#flow_group').html(respuesta);
				}
			});
			
			$.ajax({
				url:  '<?php echo_uri("forms/get_unit_fields"); ?>',
				type:  'post',
				//data: {flow:flow},
				success: function(respuesta){
					$('#unidad_residuos_group').html(respuesta);
					$('#unit_field').select2();
					$('#unit_symbol').select2();
					
					$('#unidad_residuos_group input[type="text"][maxlength]').maxlength({
						//alwaysShow: true,
						threshold: 245,
						warningClass: "label label-success",
						limitReachedClass: "label label-danger",
						appendToParent:true
					});
					
				}
			});
			
			
			
			
		} else if(tipo_formulario == 2){
			
			$('#materiales_group').html("");
			$('#fields_group').html("");
			$('#flow_group').html("");	
			$('#categorias_group').html("");	
			$('#unidad_residuos_group').html("");
			//Cargar todos los campos menos tipo mantenedora 
			
			$.ajax({
				url:  '<?php echo_uri("projects/get_fields_of_project2") ?>',
				type:  'post',
				data: {id_project:project, tipo_formulario:tipo_formulario},
				success: function(respuesta){
					$('#fields_group').html(respuesta);
					$('#campos').multiSelect({
						selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
						selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
						//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-6'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-6'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
						keepOrder: true,
						afterSelect: function(value){
							$('#campos option[value="'+value+'"]').remove();
							$('#campos').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
						},
						afterDeselect: function(value){ 
							$('#campos option[value="'+value+'"]').removeAttr('selected'); 
						},
					});
				}
			});
		} else {
			
			$('#materiales_group').html("");
			$('#flow_group').html("");
			$('#categorias_group').html("");
			$('#unidad_residuos_group').html("");
			
			$.ajax({
				url:  '<?php echo_uri("projects/get_fields_of_project2") ?>',
				type:  'post',
				data: {id_project:project},
				success: function(respuesta){
					$('#fields_group').html(respuesta);
					$('#campos').multiSelect({
						selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
						selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
						//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-6'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-6'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
						keepOrder: true,
						afterSelect: function(value){
							$('#campos option[value="'+value+'"]').remove();
							$('#campos').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
						},
						afterDeselect: function(value){ 
							$('#campos option[value="'+value+'"]').removeAttr('selected'); 
						},
					});
				}
			});
		}
		
		$('#tipo_tratamiento_group').html("");
		$('#consumo_group').html("");
		$('#consumo_default_matter_group').html("");
		$('#no_aplica_group').html("");
		// Remover el handler para este elemento y asi evitar que se duplique.
		$(document).off("change","#type_of_origin");
		
	});

	
	$(document).on('change', 'input[name=flow]', function(event){
		
		var flow = $('input[name=flow]:checked').val();
		select2LoadingStatusOn($('#type_of_treatment, #type_of_origin'));
		
		if(flow == "Residuo"){
			$.ajax({
				url: '<?php echo_uri("forms/get_type_of_treatment_definition"); ?>',
				type: 'post',
				data: {flow:flow},
				success: function(respuesta){
					

					$('#consumo_group').html("");
					$('#no_aplica_group').html("");
					$('#tipo_tratamiento_group').html(respuesta);
					$('[data-toggle="tooltip"]').tooltip();
					$('#type_of_treatment').select2();
					
					$('#tipo_tratamiento_group input[type="text"][maxlength]').maxlength({
						//alwaysShow: true,
						threshold: 245,
						warningClass: "label label-success",
						limitReachedClass: "label label-danger",
						appendToParent:true
					});
					
				}
			});
			
			$('#consumo_default_matter_group').html("");
			
			// Remover el handler para este elemento y asi evitar que se duplique.
			$(document).off("change","#type_of_origin");
			
		}else if(flow == "Consumo"){
			
			$.ajax({
				url: '<?php echo_uri("forms/get_consumption_fields"); ?>',
				type: 'post',
				data: {flow:flow},
				success: function(respuesta){
					
					$('#tipo_tratamiento_group').html("");
					$('#no_aplica_group').html("");
					$('#consumo_group').html(respuesta);
					$('[data-toggle="tooltip"]').tooltip();
					$('#type_of_origin').select2();
				}
			});
			
			$(document).on("change","#type_of_origin", function(e){
				
				var id_type_of_origin = $(this).val();
				
				$.ajax({
					url: '<?php echo_uri("forms/get_consumption_fields_type_of_origin"); ?>',
					type: 'post',
					data: {id_type_of_origin:id_type_of_origin},
					success: function(respuesta){
						$('#consumo_default_matter_group').html(respuesta);
						$('#default_matter').select2();
					}
				});
				
				if(!id_type_of_origin || id_type_of_origin == "1"){ // id 1 = matter (Materia)
					$("#forms-form #disabled_field").prop('checked', false);
					$("#forms-form #disabled_field").prop("disabled", true);
					
					$(document).on("change","#default_matter", function(e){
						
						var id_default_matter = $(this).val();
						if(!id_default_matter){
							$("#forms-form #disabled_field").prop('checked', false);
							$("#forms-form #disabled_field").prop("disabled", true);
						} else {
							$("#forms-form #disabled_field").prop("disabled", false);
						}
						
					});
					
				}else{
					$("#forms-form #disabled_field").prop('checked', true);
					$("#forms-form #disabled_field").prop("disabled", true);
				}
				
				
							
			});			
			
		}else if(flow == "No Aplica"){
						
			$.ajax({
				url: '<?php echo_uri("forms/get_no_apply_fields"); ?>',
				type: 'post',
				data: {flow:flow},
				success: function(respuesta){
					
					$('#tipo_tratamiento_group').html("");
					$('#consumo_group').html("");
					$('#consumo_default_matter_group').html("");
					$('#no_aplica_group').html(respuesta);
					$('[data-toggle="tooltip"]').tooltip();
					$('#default_type').select2();
					
				}
			});
			
			// Remover el handler para este elemento y asi evitar que se duplique.
			$(document).off("change","#type_of_origin");
		}
		
		event.stopImmediatePropagation();
		
	});
	
	<?php if($model_info->flujo == "Consumo") { ?>
		
		var type_of_origin = '<?php echo $type_of_origin; ?>';
		var default_matter = '<?php echo $default_matter; ?>';
		
		if(type_of_origin == "1"){
			if(default_matter){
				$("#forms-form #disabled_field").prop("disabled", false);
			} else {
				$("#forms-form #disabled_field").prop("disabled", true);
			}
		}
		
		if(type_of_origin == "2"){
			$("#forms-form #disabled_field").prop('checked', true);
			$("#forms-form #disabled_field").prop("disabled", true);
		}
		
		if(!type_of_origin){
			$("#forms-form #disabled_field").prop("disabled", true);
		}
		
		var type_of_origin_disabled = '<?php echo $type_of_origin_disabled; ?>';
		
		if(type_of_origin_disabled){
			$("#forms-form #disabled_field").prop("disabled", true);	
		}
		
		$(document).on("change","#type_of_origin", function(e){
				
			var id_type_of_origin = $(this).val();
			
			$.ajax({
				url: '<?php echo_uri("forms/get_consumption_fields_type_of_origin"); ?>',
				type: 'post',
				data: {id_type_of_origin:id_type_of_origin},
				success: function(respuesta){
					$('#consumo_default_matter_group').html(respuesta);
					$('#default_matter').select2();
				}
			});
			
			if(!id_type_of_origin || id_type_of_origin == "1"){ // id 1 = matter (Materia)
				$("#forms-form #disabled_field").prop('checked', false);
				$("#forms-form #disabled_field").prop("disabled", true);
			}else if(!id_type_of_origin || id_type_of_origin == "2"){ // id 2 = energy
				$("#forms-form #disabled_field").prop('checked', true);
				$("#forms-form #disabled_field").prop("disabled", true);
			}else{
				$("#forms-form #disabled_field").prop("disabled", false);
			}
			
		});	
		
		
		$(document).on("change","#default_matter", function(e){
				
			var id_default_matter = $(this).val();
			if(!id_default_matter){
				$("#forms-form #disabled_field").prop('checked', false);
				$("#forms-form #disabled_field").prop("disabled", true);
			} else {
				$("#forms-form #disabled_field").prop("disabled", false);
			}
			
		});
		
			
		
		
	<?php } else { ?>
		// Remover el handler para este elemento y asi evitar que se duplique.
		$(document).off("change","#type_of_origin");
	<?php } ?>
	
	$(document).on('change', '#unit_field', function(event){
	//$(document).one('change', '#unit_field', function(){
	//$('#unit_field').off("change").on('change', function(){
			
		var unit_type_id = $(this).val();
		select2LoadingStatusOn($('#unit_symbol'));
		
		if (unit_type_id !== "-"){
	
			$.ajax({
				url: '<?php echo_uri("forms/get_unit_symbol") ?>',
				type: 'post',
				data: {unit_type_id:unit_type_id},
				success: function(result){
					
					select2LoadingStatusOff($('#unit_symbol'));
					
					var obj = jQuery.parseJSON(result);
					var option = '';
					var option = '<option> - </option>';
					$.each(obj, function(index, value) {
						option += '<option value="'+index+'">' + value + '</option>';
					});
					$('#unit_symbol').html(option);
					$('#unit_symbol').select2();
				}
			});
		}else if(unit_type_id == "-"){
			var option = '';
			var option = '<option> - </option>';
			$('#unit_symbol').html(option);
			$('#unit_symbol').select2();
		}
		
		event.stopImmediatePropagation();
		
	});
	
	$(document).on('change', '#type_of_treatment', function(event){
			
		var type_of_treatment = $(this).val();
		if(!type_of_treatment){
			$("#forms-form #disabled_field").prop('checked', false);
			$("#forms-form #disabled_field").prop("disabled", true);
		}else{
			//$("#forms-form #disabled_field").prop('checked', false);
			$("#forms-form #disabled_field").prop("disabled", false);
		}
		
	});
	
	$(document).on('change', '#default_type', function(event){
			
		var default_type = $(this).val();
		if(!default_type){
			$("#forms-form #disabled_field").prop('checked', false);
			$("#forms-form #disabled_field").prop("disabled", true);
		}else{
			$("#forms-form #disabled_field").prop("disabled", false);
		}
		
	});
	
	$(document).on('change', '#materiales', function(event){
		
		var array_materiales = $(this).val()?$(this).val():[]; //obtiene todos los elementos seleccionados.
		var array_categorias = $('#categorias').val();
		var array_materiales_form = '<?php echo json_encode($array_materiales_formulario); ?>';
		
		$.ajax({
			url:  '<?php echo_uri("forms/get_categorias_of_material") ?>',
			type:  'post',
			data: {array_materiales:array_materiales, array_categorias:array_categorias},
			//dataType:'json',
			success: function(respuesta){
				
				$('#categorias_group').html(respuesta);
				$('#categorias').multiSelect({
					selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_categories"); ?>" + "</div>",
					selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_categories"); ?>" + "</div>",
					keepOrder: true,
					afterSelect: function(value){
						$('#categorias').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
						//$('#campos option[value="'+value+'"]').remove();
						//$('#campos').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
					},
					afterDeselect: function(value){ 
						$('#categorias option[value="'+value+'"]').removeAttr('selected');
						//$('#campos option[value="'+value+'"]').removeAttr('selected'); 
					}
				});	
			}
		})
		
		event.stopImmediatePropagation();
		
	});
	
	
	$("#icono").select2().select2("val", '<?php echo $model_info->icono; ?>');
	
	function format(state) {
		if(state.text != '-'){
			return "<img class='' heigth='20' width='20' src='/assets/images/icons/" + state.text + "'/>" + "&nbsp;&nbsp;" + state.text;
		}else{
			return state.text;
		}
		
	}
	
	$("#icono").select2({
		formatResult: format,
		formatSelection: format,
		escapeMarkup: function(m) { return m; }
	});
	
	// GENERACION DE CODIGO DE FORMULARIO
	$('#nombre, #form_number').keyup(function(e){
		set_codigo_formulario();
	});
	
	function set_codigo_formulario(){	
	
		var nombre_registro = $("#nombre").val().replace(/ /g,"");
		var id_project = $("#project").val();	
		var numero_formulario = $("#form_number").val().replace(/ /g,"");
		
		if(id_project){
			appLoader.show();
			$.ajax({
				url: '<?php echo_uri("projects/get_sigla_of_project"); ?>',
				type: 'post',
				data: {id_project:id_project},
				success: function(respuesta){
					var sigla_proyecto = respuesta;
					var codigo = sigla_proyecto + numero_formulario + nombre_registro;
					$('#codigo').val(codigo);
					appLoader.hide();
				}
			});
		}else{
			var codigo = numero_formulario + nombre_registro;
			$('#codigo').val(codigo);
		}
			
		
				
	}
	
	
});
	
</script>