<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
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
		$html .= $Feeders_controller->get_field($campo->id, $model_info->id);
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
		
		$('#feeders-form .select2').select2({
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

		setDatePicker("#feeders-form .datepicker");
		setTimePicker('#feeders-form .timepicker');
    });
</script>