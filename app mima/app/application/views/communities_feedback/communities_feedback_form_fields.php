<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="id_feedback_matrix_config" value="<?php echo $id_feedback_matrix_config; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('date'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "date",
            "name" => "date",
            "value" => $model_info->fecha,
            "class" => "form-control datepicker",
            "placeholder" => lang('date'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
        ));
        ?>
    </div>
</div>

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
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="type_of_stakeholder" class="<?php echo $label_column; ?>"><?php echo lang('type_of_stakeholder'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("type_of_stakeholder", $dropdown_tipos_organizaciones, array($model_info->id_tipo_organizacion), "id='type_of_stakeholder' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
	<?php $info = ($hay_seguimientos) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('feedback_field_disabled').'"><i class="fa fa-question-circle"></i></span>' : ''; ?>
    <label for="descripcion" class="<?php echo $label_column; ?>"><?php echo lang('visit_purpose') . " " . $info; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			$propositos_visita_dropdown = array(
				"" => "-",
				"scheduled_meeting" => lang("scheduled_meeting"),
				"complain" => lang("complain"),
				"comment" => lang("comment"),
				"query" => lang("query"),
				"congratulation" => lang("congratulation"),
			);
			echo form_dropdown("visit_purpose", $propositos_visita_dropdown, array($model_info->proposito_visita), "id='visit_purpose' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
	<?php $info = ($hay_seguimientos) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('feedback_field_disabled').'"><i class="fa fa-question-circle"></i></span>' : ''; ?>
    <label for="responsible" class="<?php echo $label_column; ?>"><?php echo lang('responsible') . " " . $info; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		
			echo form_dropdown("responsible", $responsables_dropdown, array($model_info->responsable), "id='responsible' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<!--
<div class="form-group">
    <label for="answer" class="<?php echo $label_column; ?>"><?php echo lang('answer'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_textarea(array(
            "id" => "answer",
            "name" => "answer",
            "value" => $model_info->respuesta,
            "class" => "form-control",
            "placeholder" => lang('answer'),
            "autofocus" => false,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="answer_status" class="<?php echo $label_column; ?>"><?php echo lang('answer_status'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			$estados_respuesta = array(
				"" => "-",
				"Abierto" => "Abierto",
				"Cerrado" => "Cerrado"
			);
			echo form_dropdown("answer_status", $estados_respuesta, array($model_info->estado_respuesta), "id='answer_status' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
-->
<!-- CAMPOS ADICIONALES -->


<?php 

	$html = '';
	foreach($campos_feedback_matrix as $campo){
		
		// 11 = texto fijo | 12 = divisor
		if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
				$html .= $Communities_feedback_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
			
		} else {
			
			$html .= '<div class="form-group multi-column">';
				$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $Communities_feedback_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
		
		}
		
	}
	
	echo $html;

?>

<?php 
/*
	$html = '';
	foreach($campos_feedback_matrix as $campo){
		$id_campo = $campo['id_campo'];
		$label = '';
		$class = '';
		if ($campo["id_tipo_campo"] != 12) {
			$label = '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
			$class = 'col-md-9';
		} else {
			$class = 'col-md-12';
		}
		$html .= '<div class="form-group">';
			$html .= $label;
			$html .= '<div id="' . $id_campo . '_container" class="' . $class . '">';
			$html .= $Communities_feedback_controller->get_field($campo["id_campo"], $model_info->id, NULL);
			$html .= '</div>'; 
		
		$html .= '</div>';
	}
	
	echo $html;
	*/
?>

<!--CAMPO FIJO "requires_monitoring"-->

<div class="form-group">
	<?php $info = ($hay_seguimientos) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('feedback_field_disabled').'"><i class="fa fa-question-circle"></i></span>' : ''; ?>
	<label for="requires_monitoring" class="<?php echo $label_column; ?>"><?php echo lang('requires_monitoring') . " " . $info; ?></label>
    <div class="<?php echo $field_column; ?>">
		<div class="col-md-6">
			<?php echo lang("yes"); ?>
		</div>
		<div class="col-md-6">
		<?php
		$radio_yes = array(
			"id" => "requires_monitoring",
			"name" => "requires_monitoring",
			"value" => 1,
			"class" => "toggle_specific",
			"checked" => $model_info->requires_monitoring === "1",
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required")
		);
		echo form_radio($radio_yes);
		?>
		</div>
		<div class="col-md-6">
			<?php echo lang("no"); ?>
		</div>
		<div class="col-md-6">
		<?php
		$radio_no = array(
			"id" => "requires_monitoring",
			"name" => "requires_monitoring",
			"value" => 0,
			"class" => "toggle_specific",
			"checked" => $model_info->requires_monitoring === "0",
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required")
		);
		echo form_radio($radio_no);
		?>
		</div>
    </div>
	
</div>

<!-- FIN CAMPO FIJO "requires_monitoring" -->

<?php if($hay_seguimientos){ ?>
    <input type="hidden" name="visit_purpose" value="<?php echo $model_info->proposito_visita; ?>" />
    <input type="hidden" name="responsible" value="<?php echo $model_info->responsable; ?>" />
	<input type="hidden" name="requires_monitoring" value="1">
<?php } ?>


<!--
<div class="form-group">
  <label for="file" class="col-md-3"><?php echo lang('upload_evidence_file'); ?></label>
    <div class="col-md-9">
    	<div id="dropzone_bulk" class="">
			<?php
            
            echo $this->load->view("includes/agreement_evaluation_file_uploader", array(
                "upload_url" => get_uri("communities_agreements/upload_file"),
                "validation_url" =>get_uri("communities_agreements/validate_file"),
                //"html_name" => 'test',
                //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
            ), true);
            ?>
            <?php //$this->load->view("includes/dropzone_preview"); ?>
        </div>
    </div>
</div>
-->

<?php
/* 
	if ($html_archivos_evidencia){ 
		echo $html_archivos_evidencia;
	}
*/
?>

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
		$('.select2').select2();
		
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
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		<?php if($hay_seguimientos) { ?>
			$('#visit_purpose, #responsible, #requires_monitoring').attr('disabled','disabled');
		<?php } ?>
		
	    setDatePicker('.datepicker');
	    setTimePicker('.timepicker');

    });
</script>