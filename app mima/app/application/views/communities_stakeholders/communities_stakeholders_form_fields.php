<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="id_stakeholder_matrix_config" value="<?php echo $id_stakeholder_matrix_config; ?>" />
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
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<!--
<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('last_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "last_name",
            "name" => "last_name",
            "value" => $model_info->apellidos,
            "class" => "form-control",
            "placeholder" => lang('last_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
-->

<div class="form-group">
    <label for="rut" class="<?php echo $label_column; ?>"><?php echo lang('rut'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "rut",
            "name" => "rut",
            "value" => $model_info->rut,
            "class" => "form-control",
            "placeholder" => lang('rut'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			//"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="type_of_organization" class="<?php echo $label_column; ?>"><?php echo lang('type_of_organization'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("type_of_organization", $dropdown_tipos_organizaciones, array($model_info->id_tipo_organizacion), "id='type_of_organization' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="locality" class="<?php echo $label_column; ?>"><?php echo lang('locality'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "locality",
            "name" => "locality",
            "value" => $model_info->localidad,
            "class" => "form-control",
            "placeholder" => lang('locality'),
            "autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<!-- CAMPOS ADICIONALES -->

<?php 
	$html = '';
	foreach($campos_stakeholder_matrix as $campo){
		
		// 11 = texto fijo | 12 = divisor
		if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
				$html .= $Communities_stakeholders_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
			
		} else {
			
			$html .= '<div class="form-group multi-column">';
				$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $Communities_stakeholders_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
	}
	
	echo $html;

?>

<hr>
    
<div class="pb10" style="text-align: center;">
  <h4><?php echo lang("contact_data"); ?></h4>
</div>

<div class="form-group">
    <label for="contact_name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "contact_name",
            "name" => "contact_name",
            "value" => $model_info->nombres_contacto,
            "class" => "form-control",
            "placeholder" => lang('name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="contact_last_name" class="<?php echo $label_column; ?>"><?php echo lang('last_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "contact_last_name",
            "name" => "contact_last_name",
            "value" => $model_info->apellidos_contacto,
            "class" => "form-control",
            "placeholder" => lang('last_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="contact_phone" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "contact_phone",
            "name" => "contact_phone",
            "value" => $model_info->telefono_contacto,
            "class" => "form-control",
            "placeholder" => lang('phone'),
            "autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			//"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="contact_email" class="<?php echo $label_column; ?>"><?php echo lang('email'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "contact_email",
            "name" => "contact_email",
            "value" => $model_info->correo_contacto,
            "class" => "form-control",
            "placeholder" => lang('email'),
            "autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"data-rule-email" => true,
			"data-msg-email" => lang("enter_valid_email"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="contact_address" class="<?php echo $label_column; ?>"><?php echo lang('address'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "contact_address",
            "name" => "contact_address",
            "value" => $model_info->direccion_contacto,
            "class" => "form-control",
            "placeholder" => lang('address'),
            "autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
		
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

		//$('#type_of_organization').select2();
		$('#communities_stakeholders-form .select2').select2();
		setDatePicker("#communities_stakeholders-form .datepicker");
		setTimePicker('#communities_stakeholders-form .timepicker');
		
    });
</script>