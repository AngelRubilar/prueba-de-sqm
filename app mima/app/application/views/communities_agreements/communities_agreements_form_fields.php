<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="id_agreements_matrix_config" value="<?php echo $id_agreements_matrix_config; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('code'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "code",
            "name" => "code",
            "value" => $model_info->codigo,
            "class" => "form-control",
            "placeholder" => lang('code'),
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
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->nombre_acuerdo,
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
    <label for="descripcion" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_textarea(array(
            "id" => "description",
            "name" => "description",
            "value" => $model_info->descripcion,
            "class" => "form-control",
            "placeholder" => lang('description'),
            "autofocus" => false,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "2000"
        ));
        ?>
    </div>
</div>

<div class="form-group multi-column">
    <label for="period" class="<?php echo $label_column; ?>"><?php echo lang('period'); ?></label>
    <div class="<?php echo $field_column; ?>">
    	
        <?php 
			
			$periodo = json_decode($model_info->periodo);
			
			$datos_campo1 = array(
				"id" => "period_1",
				"name" => "period_1",
				"value" => $periodo->start_date,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
				"data-rule-required" => true,
            	"data-msg-required" => lang("field_required"),
			);
			
			$datos_campo2 = array(
				"id" => "period_2",
				"name" => "period_2",
				"value" => $periodo->end_date,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"data-rule-greaterThanOrEqual" => "#period_1",
				"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
				"autocomplete" => "off",
				"data-rule-required" => true,
            	"data-msg-required" => lang("field_required"),
			);
			
			/*
			if($obligatorio){
				$datos_campo1['data-rule-required'] = true;
				$datos_campo1['data-msg-required'] = lang("field_required");
				$datos_campo2['data-rule-required'] = true;
				$datos_campo2['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo1['disabled'] = true;
				$datos_campo2['disabled'] = true;
			}
			*/
			$html = '<div class="col-md-6">';
			$html .= form_input($datos_campo1);
			$html .= '</div>';
			$html .= '<div class="col-md-6">';
			$html .= form_input($datos_campo2);
			$html .= '</div>';
			
			echo $html;
				
		?>
        
	</div>
</div>

<div class="form-group">
    <label for="managing" class="<?php echo $label_column; ?>"><?php echo lang('managing'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_dropdown("managing", $managing_dropdown, array($model_info->gestor), "id='managing' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>


<!-- CAMPOS ADICIONALES -->

<?php 

	$html = '';
	foreach($campos_agreements_matrix as $campo){
		
		// 11 = texto fijo | 12 = divisor
		if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
				$html .= $Communities_agreements_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
			
		} else {
		
			$html .= '<div class="form-group multi-column">';
				$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $Communities_agreements_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
		
		}
	}
	
	echo $html;
	
?>

<div class="form-group">
    <?php
	
		$html = '';
        $html .= '<div class="form-group">';
            $html .= '<label for="fields" class="col-md-3">'.lang('stakeholders').' '.$info.'</label>';
            $html .= '<div class="col-md-9">';
			$html .= form_multiselect(
						"stakeholders[]", 
						$multiselect_stakeholders, 
						$multiselect_stakeholders_of_agreements, 
						"id='stakeholders' class='select2 validate-hidden' ",
						NULL,
						$array_stakeholders_usados_seguimiento
					);
			$html .= '</div>';
		$html .= '</div>';

        echo $html;
    ?>
</div>

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

<script type="text/javascript">
    $(document).ready(function () {
		
		$('[data-toggle="tooltip"]').tooltip();
		
		$('#communities_agreements-form .select2').select2();
		
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
		
		setDatePicker(".datepicker");
		setDatePicker("#period_1, #period_2");
		setTimePicker(".timepicker");

    });
</script>