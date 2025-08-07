<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<!--
<div class="form-group">
  <label for="date_filed" class="col-md-3"><?php //echo lang('id'); ?></label>
    <div class=" col-md-9">
        <?php //echo $id_compromiso_proyecto; ?>
    </div>
</div>
-->

<div class="form-group">
  <label for="numero_permiso" class="col-md-3"><?php echo lang('permitting_number'); ?></label>
    <div class=" col-md-9">
        <?php       
			echo form_input(array(
				"id" => "numero_permiso",
				"name" => "numero_permiso",
				"value" => $model_info->numero_permiso,
				"class" => "form-control",
				"placeholder" => lang('permitting_number'),
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-regex" => "^[1-9][0-9]*$",
				"data-msg-regex" => lang("integer_greater_than_zero"),
				"autocomplete"=> "off",
				//"maxlength" => "255"
			));		
		?>
    </div>
</div>


<div class="form-group">
  <label for="nombre_permiso" class="col-md-3"><?php echo lang('name'); ?></label>
    <div class=" col-md-9">
        <?php       
			echo form_input(array(
				"id" => "nombre_permiso",
				"name" => "nombre_permiso",
				"value" => $model_info->nombre_permiso,
				"class" => "form-control",
				"placeholder" => lang('name'),
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete"=> "off",
				"maxlength" => "255"
			));		
		?>
    </div>
</div>

<div id="phases">
    <div class="form-group">
        <label for="phases" class="col-md-3"><?php echo lang('phases'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_multiselect("phases[]", $fases_disponibles, $fases_permiso, "id='phases' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
  <label for="entidad" class="col-md-3"><?php echo lang('entity'); ?></label>
    <div class=" col-md-9">
        <?php       
			echo form_input(array(
				"id" => "entidad",
				"name" => "entidad",
				"value" => $model_info->entidad,
				"class" => "form-control",
				"placeholder" => lang('entidad'),
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete"=> "off",
				"maxlength" => "255"
			));		
		?>
    </div>
</div>


<?php 
/*			
	$html = '';
	$html .= '<div class="form-group">';
	$html .= '<label for="phases" class="col-md-3">'.lang('phases').'</label>';
	$html .= '<div class="col-md-9">';
	$html .= form_multiselect("phases[]", $fases_disponibles, $fases_permiso, "id='phases' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
	$html .= '</div>';
	$html .= '</div>';
	echo $html;		
*/
?>
<!--
<div class="form-group">
  <label for="reportability" class="col-md-3"><?php echo lang('reportability'); ?></label>
    <div class=" col-md-9">
        <?php
        echo form_checkbox("reportability", "1", ($model_info->reportabilidad == 1) ? true : false, "id='reportability'");
        ?>
    </div>
</div>
-->
<?php 
	
	$html = '';
	foreach($campos_permiso as $campo){
		
		// 11 = texto fijo | 12 = divisor
		if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
				$html .= $Upload_permittings_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
		
		
		} else {
		
			$html .= '<div class="form-group multi-column">';
				$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $Upload_permittings_controller->get_field($campo["id_campo"], $model_info->id);
				$html .= '</div>';
			$html .= '</div>';
		
		}

	}
	
	echo $html;

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
		
		$('#individual_upload-form .select2').select2({
			/*sortResults: function(data) {
				return data.sort(function (a, b) {
					a = a.text.toLowerCase();
					b = b.text.toLowerCase();
					if (a > b) {
						return 1;
					} else if (a < b) {
						return -1;
					}
					return 0;
				});
			}*/
		
		});
		setDatePicker("#individual_upload-form .datepicker");
		setTimePicker('#individual_upload-form .timepicker');
		
    });
</script>