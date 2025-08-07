<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

<?php
	$html = '';
	foreach($campos as $campo){
		
		$html .= '<div class="form-group multi-column">';
		if(($campo->id_tipo_campo == 12)||($campo->id_tipo_campo == 11)){// si divisor o texto fijo
			$html .= '<div class="col-md-12">';
			$html .= '<div style="word-wrap: break-word;">';
			$html .= $campo->default_value;
			$html .= '</div>';
			$html .= '</div>';
		}else{
			$html .= '<label for="'.$campo->html_name.'" class="col-md-3">'.$campo->nombre.'</label>';
			$html .= '<div class="col-md-9">';
			$html .= $Other_records_controller->get_field_fixed_form($campo->id, $model_info->id, NULL, $record_info->id);
			$html .= '</div>';
		}
		$html .= '</div>';
		
		
	}
	
	echo $html;

?>		

<script type="text/javascript">
    $(document).ready(function () {
		
		$('#other_records-form .select2').select2();
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
		
		setDatePicker("#other_records-form .datepicker");
		setTimePicker('#other_records-form .timepicker');
    });
</script>