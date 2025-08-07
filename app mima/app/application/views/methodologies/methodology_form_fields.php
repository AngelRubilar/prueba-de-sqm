<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_name",
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

<div class="form-group">

<?php 

	if($huellas){
		
		$arraySelected = array();
        $arraySelected2 = array();
        $arrayHuellas = array();
        
        foreach($huellas_metodologia as $innerArray){
            $arraySelected[] = $innerArray["id"];
            $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
        }
        foreach($huellas as $innerArray){
			if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
				$arrayHuellas[(string)$innerArray["id"]] = $innerArray["nombre"];
			}
		}

        $array_final = $arraySelected2 + $arrayHuellas;
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="fu" class="col-md-3">'.lang('footprints').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_multiselect("huellas[]", $array_final, $arraySelected, "id='huellas' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}


	

?>

</div>

<div class="form-group">
    <label for="description" class=" col-md-3"><?php echo lang('description'); ?></label>
    <div class=" col-md-9">
        <?php
        echo form_textarea(array(
            "id" => "description",
            "name" => "description",
            "value" => $model_info->descripcion,
            "class" => "form-control",
            "placeholder" => lang('description'),
            "autocomplete"=> "off",
			"maxlength" => "2000"
        ));
        ?>
    </div>
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
        
		$('#huellas').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
			keepOrder: true,
			afterSelect: function(value){
				$('#huellas option[value="'+value+'"]').remove();
				$('#huellas').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
			},
			afterDeselect: function(value){ 
				$('#huellas option[value="'+value+'"]').removeAttr('selected'); 
			}
		});
		
    }); 
</script>