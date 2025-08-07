<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->nombre,
            "class" => "form-control",
            "placeholder" => lang('name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
        ));
        ?>
    </div>
</div>

<!-- listar todos los clientes -->
<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='clientes' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
 <!-- listar todos los clientes -->

<!-- listar todos los proyectos -->
<div id="proyectos_group">
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project", $proyectos, array($model_info->id_proyecto), "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>
 <!-- listar todos los proyectos -->


<div class="form-group">

<?php 

	if($unidades_funcionales_disponibles){
		
		$arraySelected = array();
        $arraySelected2 = array();
        $arrayUFuncionalesSubproyecto = array();
        
        foreach($unidades_funcionales_subproyecto as $innerArray){
            $arraySelected[] = $innerArray["id"];
            $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
        }
        foreach($unidades_funcionales_disponibles as $innerArray){
			if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
				$arrayUFuncionalesSubproyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
			}
		}

        $array_final = $arraySelected2 + $arrayUFuncionalesSubproyecto;
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="fu" class="col-md-3">'.lang('functional_units').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_multiselect("functional_units[]", $array_final, $arraySelected, "id='functional_units' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;	
	}

?>

</div>


<div class="form-group">
    <label for="infrastructure_type" class="<?php echo $label_column; ?>"><?php echo lang("infrastructure_type") ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		
		$infrastructure_types = array(
			"" => "-",
			"Generación" => "Generación",
			"Transmisión" => "Transmisión"
		);
		
		echo form_dropdown("infrastructure_type", $infrastructure_types, array($model_info->tipo_infraestructura), "id='infrastructure_type' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div> 



<div id="generacion_group">

<?php 

	if($model_info->id){
		
		if($model_info->tipo_infraestructura == "Generación"){

			$html = '';
			$html = '<div style="text-align: center;"><h5>'.lang("generation").'</h5></div>';
			$html .= '<br />';
			/*
			$html .= '<div class="form-group">';
			$html .= '<label for="technologies" class="col-md-3">'.lang('technology').'</label>';
			$html .= '<div class="col-md-9">';
			
			$tecnologias = array(
				"Eólica" => "Eólica",
				"Sola fotovoltaica" => "Sola fotovoltaica",
				"Termosolar" => "Termosolar",
				"Mini hibrido pasada" => "Mini hibrido pasada",
				"Mini hibrido embalse" => "Mini hibrido embalse",
				"Geotérmica" => "Geotérmica"
			);
			
			$html .= form_dropdown("technologies", array("" => "-") + $tecnologias, $model_info->tecnologia, "id='technologies' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			$html .= '</div>';*/
			
			$html .= '<div class="form-group">';
			$html .= '<label for="no_of_generation_equipment" class="col-md-3">'.lang('no_of_generation_equipment').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "no_of_generation_equipment",
						"name" => "no_of_generation_equipment",
						"type" => "number",
						"value" => $model_info->num_equipos_generacion,
						"class" => "form-control",
						"placeholder" => lang('no_of_generation_equipment'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="unit_power_of_equipment" class="col-md-3">'.lang('unit_power_of_equipment_mw').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "unit_power_of_equipment",
						"name" => "unit_power_of_equipment",
						"type" => "number",
						"value" => $model_info->potencia_unitaria_equipos,
						"class" => "form-control",
						"placeholder" => lang('unit_power_of_equipment_mw'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="surface" class="col-md-3">'.lang('surface_km2').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "surface",
						"name" => "surface",
						"type" => "number",
						"value" => $model_info->superficie,
						"class" => "form-control",
						"placeholder" => lang('surface'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';
			
			echo $html;
		}
		
	}

?>


</div>

<div id="transmision_group">

<?php 

	if($model_info->id){

		if($model_info->tipo_infraestructura == "Transmisión"){
		
			$html = '';
			$html = '<div style="text-align: center;"><h5>'.lang("transmission").'</h5></div>';
			$html .= '<br />';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="type_of_substation" class="col-md-3">'.lang('type_of_substation').'</label>';
			$html .= '<div class="col-md-9">';
			
			$types_of_substation = array(
				"Elevación" => "Elevación",
				"Interconexión" => "Interconexión"
			);
			
			$html .= form_dropdown("type_of_substation", array("" => "-") + $types_of_substation, $model_info->tipo_subestacion, "id='type_of_substation' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="transformation_capacity" class="col-md-3">'.lang('transformation_capacity_kv').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "transformation_capacity",
						"name" => "transformation_capacity",
						"type" => "number",
						"value" => $model_info->capacidad_transformacion,
						"class" => "form-control",
						"placeholder" => lang('transformation_capacity_kv'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="number_of_high_voltage_towers" class="col-md-3">'.lang('number_of_high_voltage_towers').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "number_of_high_voltage_towers",
						"name" => "number_of_high_voltage_towers",
						"type" => "number",
						"value" => $model_info->num_torres_alta_tension,
						"class" => "form-control",
						"placeholder" => lang('number_of_high_voltage_towers'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';
						
			$html .= '<div class="form-group">';
			$html .= '<label for="line_length" class="col-md-3">'.lang('line_length').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "line_length",
						"name" => "line_length",
						"type" => "number",
						"value" => $model_info->longitud_linea,
						"class" => "form-control",
						"placeholder" => lang('line_length'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="form-group">';
			$html .= '<label for="surface" class="col-md-3">'.lang('surface_km2').'</label>';
			$html .= '<div class="col-md-9">';
			
			$html .= form_input(array(
						"id" => "surface",
						"name" => "surface",
						"type" => "number",
						"value" => $model_info->superficie,
						"class" => "form-control",
						"placeholder" => lang('surface'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off"
					));
		
			$html .= '</div>';
			$html .= '</div>';
			
			echo $html;
		
		}

	}

?>

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
		$('#clientes').select2();
		$('#project').select2();
		$('#infrastructure_type').select2();
		$('#technologies').select2();
		$('#type_of_substation').select2();
		
		$('#clientes').change(function(){	
					
			var id_client = $(this).val();	
					
			$.ajax({
				url:  '<?php echo_uri("clients/get_projects_of_client") ?>',
				type:  'post',
				data: {id_client:id_client},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
				}
			});
		
		});	
		

		$('#infrastructure_type').change(function(){	
					
			var tipo_infraestructura = $(this).val();	
					
			$.ajax({
				url:  '<?php echo_uri("subprojects/get_infrastructure_type_fields") ?>',
				type:  'post',
				data: {tipo_infraestructura: tipo_infraestructura},
				//dataType:'json',
				success: function(respuesta){				
					
					if(tipo_infraestructura == "Generación"){
						$('#transmision_group').html("");
						$('#generacion_group').html(respuesta);
					} else if(tipo_infraestructura == "Transmisión"){
						$('#generacion_group').html("");
						$('#transmision_group').html(respuesta);
					} else {
						$('#generacion_group').html("");
						$('#transmision_group').html("");
					}

					$('#technologies').select2();
					$('#type_of_substation').select2();
					
				}
			});
		
		});

		$('#functional_units').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
			keepOrder: true,
			afterSelect: function(value){
				$('#functional_units option[value="'+value+'"]').remove();
				$('#functional_units').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
			},
			afterDeselect: function(value){ 
				$('#functional_units option[value="'+value+'"]').removeAttr('selected'); 
			}
		});
			
    });
</script>