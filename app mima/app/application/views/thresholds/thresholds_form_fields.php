<?php
$this->load->view("includes/summernote");
?>
<input type="hidden" name="id_thresholds" value="<?php echo $id_thresholds; ?>" />
<input type="hidden" name="id_unit_type" id="id_unit_type" value="<?php echo $id_unit_type; ?>"/>
<input type="hidden" name="id_unit" id="id_unit" value="<?php echo $id_unit; ?>"/>
<!-- listar todos los clientes -->
<div class="form-group">
    <label for="clients" class="col-md-3"><?php echo lang('client'); ?></label>
    <div class="col-md-9">
        <?php
		echo form_dropdown("clients", $clientes, $client, "id='clients' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
<!-- listar proyectos -->
<div id="proyectos_group">
    <div class="form-group">
        <label for="project" class="col-md-3"><?php echo lang('project'); ?></label>
        <div class="col-md-9">
            <?php
				if(isset($available_project)){
					echo form_dropdown("project", $available_project, $id_project, "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				}else{
					echo form_dropdown("project", $projects, "", "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				}
            ?>
        </div>
    </div>
</div>
<!-- listar modulo -->
<div class="form-group">
	<label for="module" class="col-md-3"><?php echo lang('module'); ?></label>
	<div class="col-md-9">
		<?php
			echo form_dropdown("module", $available_module, $id_module, "id='module' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>
<!-- listar fomularios -->
<div class="form-group">
	<label for="forms" class="col-md-3"><?php echo lang('forms'); ?></label>
	<div class="col-md-9">
		<?php
			echo form_dropdown("forms", $available_forms, $id_form, "id='forms' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>
<!-- listar material -->
<div class="form-group">
	<label for="material" class="col-md-3"><?php echo lang('material'); ?></label>
	<div class="col-md-9">
		<?php
			echo form_dropdown("material", $available_material, $id_material, "id='material' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>
<!-- listar categoria -->
<div class="form-group">
	<label for="category" class="col-md-3"><?php echo lang('category'); ?></label>
	<div class="col-md-9">
		<?php
			echo form_dropdown("category", $available_category, $id_category, "id='category' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
</div>
<!--tipo unidad-->
<div class="form-group">
    <label for="unit_type" class="<?php echo $label_column; ?>"><?php echo lang('unit_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "unit_type",
            "name" => "unit_type",
            "value" => $unit_type,
            "class" => "form-control",
            "placeholder" => lang('unit_type'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"readonly" => "readonly"
        ));
        ?>
    </div>
</div>
<!-- unidad -->

<!-- valor riesgo -->
<div class="form-group">
    <label for="risk_value" class="<?php echo $label_column; ?>"><?php echo lang('risk_value'); ?></label>
    <!--<div class="<?php echo $field_column; ?>">-->
	<div class="col-md-9">
		<div class="col-md-10 p0">
			<?php
			echo form_input(array(
				"id" => "risk_value",
				"name" => "risk_value",
				"value" => $risk_value,
				"class" => "form-control",
				"placeholder" => lang('risk_value'),
				"autofocus" => true,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer"),
				"autocomplete"=> "off",
				//"maxlength" => "255"
			));
			?>
		</div>
		<div class="col-md-2">
			<?php
			echo form_input(array(
				"id" => "unit_name_risk_value",
				"name" => "unit_name_risk_value",
				"value" => $unit_name,
				"class" => "form-control",
				"placeholder" => "",
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete"=> "off",
				"readonly" => "readonly",
				"style" => "padding: 0px; text-align: center"
			));
			?>
		</div>
    </div>
</div>
<!-- valor umbral -->
<div class="form-group">
    <label for="threshold_value" class="<?php echo $label_column; ?>"><?php echo lang('threshold_value'); ?></label>
    <!--<div class="<?php echo $field_column; ?>">-->
	<div class="col-md-9">
		<div class="col-md-10 p0">
			<?php
			echo form_input(array(
				"id" => "threshold_value",
				"name" => "threshold_value",
				"value" => $threshold_value,
				"class" => "form-control",
				"placeholder" => lang('threshold_value'),
				"autofocus" => true,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer"),
				"autocomplete"=> "off",
				//"maxlength" => "255"
			));
			?>
		</div>
		<div class="col-md-2">
			<?php
			echo form_input(array(
				"id" => "unit_name_threshold_value",
				"name" => "unit_name_threshold_value",
				"value" => $unit_name,
				"class" => "form-control",
				"placeholder" => "",
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
				"readonly" => "readonly",
				"style" => "padding: 0px; text-align: center"
			));
			?>
		</div>
    </div>
</div>
<!-- etiqueta-->
<div class="form-group">
    <label for="label" class="<?php echo $label_column; ?>"><?php echo lang('label'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "label",
            "name" => "label",
            "value" => $label,
            "class" => "form-control",
            "placeholder" => lang('label'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
    });
</script> 