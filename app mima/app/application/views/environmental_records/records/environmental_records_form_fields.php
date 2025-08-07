<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="tipo_unidad_residuo" value="<?php echo $tipo_unidad_residuo; ?>" />
<input type="hidden" name="unidad_residuo" value="<?php echo $unidad_residuo; ?>" />
<!-- Fecha de registro datepicker -->
<div class="form-group">
  <label for="date_filed" class="col-md-3"><?php echo lang('date_filed'); ?></label>
    <div class=" col-md-9">
        <?php
		$datos = json_decode($model_info->datos, true);
		$fecha_registro = $datos["fecha"];
		//$fecha_registro = get_date_format($datos["fecha"],$this->session->project_context);
        echo form_input(array(
            "id" => "date_filed",
            "name" => "date_filed",
            "value" => $fecha_registro,
            "class" => "form-control datepicker",
            "placeholder" => lang('date_filed'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete" => "off",
        ));
        ?>
    </div>
</div>
<!--Categorías dropdown-->
<?php
  //$datos = json_decode($model_info->datos, true);
  $id_categoria = $datos["id_categoria"];
  $info = ($count_cat == 1) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_category_forms_form_field_info').'"><i class="fa fa-question-circle"></i></span>' : '';
?>
<div class="form-group">
  <label for="category" class=" col-md-3"><?php echo lang('category') . " " . $info; ?></label>
  <div class=" col-md-9">
	
	<?php if ($count_cat == 1) {?>
    	<input type="hidden" name="category" value="<?php echo key($categorias); ?>" />
        <?php
            echo form_dropdown("category", $categorias, $categorias, "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', disabled='disabled', data-msg-required='" . lang('field_required') . "'");
        ?>
        <?php } else { ?>
        <?php
            echo form_dropdown("category", array("" => "-") + $categorias, $id_categoria, "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    <?php } ?>
    
  </div>
</div>

<?php if(($flujo == "Residuo") || ($flujo == "Consumo") || ($flujo == "No Aplica")) { ?>
<div id="waste_unit_group">
	<div class="form-group">
	  <label for="waste_unit" class="col-md-3"><?php echo $nombre_unidad_residuo." (".$tipo_unidad_residuo.")" ?></label>
		<div class=" col-md-9">
			<div class="col-md-10 p0">
			<?php
			$unidad = $datos["unidad_residuo"];
			echo form_input(array(
				"id" => "waste_unit",
				"name" => "waste_unit",
				"value" => $unidad,
				"class" => "form-control",
				"placeholder" => $nombre_unidad_residuo,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_number"),
				"autocomplete" => "off",
			));
			?>
			</div>
			<div class="col-md-2">
				<?php echo $unidad_residuo ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if($flujo == "Residuo") { ?>

	<!--Tipo de tratamiento-->
	<div id="type_of_treatment_group">
		<div class="form-group">
			<label for="type_of_treatment" class="col-md-3"><?php echo lang('type_of_treatment'); ?></label>
			<div class="col-md-9">
				<?php
				$disabled = ($disabled_field)?"disabled='disabled'":"";
				echo form_dropdown("type_of_treatment", array("" => "-") + $tipo_tratamiento, ($model_info->id)?$datos["tipo_tratamiento"]:$tipo_tratamiento_default, "id='type_of_treatment' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".$disabled);
				?>
			</div>
		</div>
	</div>

    <div id="retirement_date_group">
        <div class="form-group">
          <label for="retirement_date" class="col-md-3"><?php echo lang('retirement_date'); ?></label>
            <div class=" col-md-9">
                <?php
                $fecha_retiro = $datos["fecha_retiro"];
                echo form_input(array(
                    "id" => "retirement_date",
                    "name" => "retirement_date",
                    "value" => $fecha_retiro,
                    "class" => "form-control datepicker",
                    "placeholder" => lang('retirement_date'),
                    //"data-rule-required" => true,
                    //"data-msg-required" => lang("field_required"),
                    "autocomplete" => "off",
                ));
                ?>
            </div>
        </div>
    </div>

	<?php if($archivo_retiro) { ?>
		
        <div class="form-group">
          <label for="retirement_evidence" class="col-md-3"><?php echo lang('retirement_evidence'); ?></label>
            <div id="dropzone_retirement_evidence" class="col-md-9">
        		<?php echo $html_archivo_retiro; ?>
        	</div>
        </div>
        
    <?php } else { ?>
    
    	<div class="form-group">
          <label for="retirement_evidence" class="col-md-3"><?php echo lang('retirement_evidence'); ?></label>
            <div id="dropzone_retirement_evidence" class="col-md-9">
                <?php
                    echo $this->load->view("includes/retirement_evidence_uploader", array(
                        "upload_url" => get_uri("fields/upload_file"),
                        "validation_url" =>get_uri("fields/validate_file")
                    ), true);
                ?>
            </div>
        </div>
        
    <?php } ?>
    
    <?php if($archivo_recepcion) { ?>
   		
        <div class="form-group">
          <label for="reception_evidence" class="col-md-3"><?php echo lang('reception_evidence'); ?></label>
            <div id="dropzone_reception_evidence" class="col-md-9">
        		<?php echo $html_archivo_recepcion; ?>
         	</div>
        </div>
        
	<?php } else { ?>
    	
        <div class="form-group">
          <label for="reception_evidence" class="col-md-3"><?php echo lang('reception_evidence'); ?></label>
            <div id="dropzone_reception_evidence" class="col-md-9">
                <?php
                    echo $this->load->view("includes/reception_evidence_uploader", array(
                        "upload_url" => get_uri("fields/upload_file"),
                        "validation_url" =>get_uri("fields/validate_file")
                    ), true);
                ?>
            </div>
        </div>

    <?php } ?>
    
<?php }?>

<?php if($flujo == "Consumo") { ?>
	
	<?php if($type_of_origin == "1"){ // id 1: matter ?>
	
		<div id="matter_group">
			<div class="form-group">
				<label for="matter" class="col-md-3"><?php echo lang('type'); ?></label>
				<div class="col-md-9">
					<input type="hidden" name="type_of_origin" value="<?php echo $type_of_origin; ?>" />
					<?php
						$disabled = ($disabled_field)?"disabled='disabled'":"";
						echo form_dropdown("type_of_origin_matter", $array_tipos_origen_materia, $default_matter, "id='type_of_origin_matter' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".$disabled);
					?>
					<?php if($disabled_field){ ?>
						<input type="hidden" name="type_of_origin_matter" value="<?php echo $default_matter; ?>" />
					<?php } ?>
				</div>
			</div>
		</div>
		
	<?php } ?>
	
	<?php if($type_of_origin == "2"){ // id 2: energy ?>
	
		<div id="matter_group">
			<div class="form-group">
				<label for="matter" class="col-md-3"><?php echo lang('type'); ?></label>
				<div class="col-md-9">
					<?php
					$disabled = ($disabled_field)?"disabled='disabled'":"";
					echo form_dropdown("type_of_origin", $array_tipos_origen, $type_of_origin, "id='type_of_origin' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' ".$disabled);
					?>
					<?php if($disabled){ ?>
					<input type="hidden" name="type_of_origin" value="<?php echo $type_of_origin; ?>" />
					<?php } ?>
				</div>
			</div>
		</div>
		
	<?php } ?>
	
<?php } ?>


<?php if($flujo == "No Aplica") { ?>
	
    <div id="default_type_group">
		<div class="form-group">
			<label for="default_type" class="col-md-3"><?php echo lang('type'); ?></label>
			<div class="col-md-9">
				<?php
				$disabled = ($disabled_default_type)?"disabled='disabled'":"";
				echo form_dropdown("default_type", array("" => "-") + $array_tipos_por_defecto, ($model_info->id)?$datos["default_type"]:$tipo_por_defecto_default, "id='default_type' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' $disabled");
				?>
                <?php if($disabled){ ?>
                <input type="hidden" name="default_type" value="<?php echo $tipo_por_defecto_default; ?>" />
                <?php } ?>
			</div>
		</div>
	</div>
    
<?php } ?>

<?php 
	$html = '';
	foreach($campos as $campo){

		$html .= '<div class="form-group multi-column">';
		if(($campo->id_tipo_campo == 12)||($campo->id_tipo_campo == 11)){// si divisor y texto fijo
			$html .= '<div class="col-md-12">';
			$html .= '<div style="word-wrap: break-word;">';
			$html .= $campo->default_value;
			$html .= '</div>';
			$html .= '</div>';
		}else{
			$html .= '<label for="'.$campo->html_name.'" class="col-md-3">'.$campo->nombre.'</label>';
			$html .= '<div class="col-md-9">';
			$html .= $Environmental_records_controller->get_field($campo->id, $model_info->id);
			$html .= '</div>';
			
		}
		$html .= '</div>';
		
	}

	echo $html;

?>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
		
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
		
		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');
		
    });
</script>