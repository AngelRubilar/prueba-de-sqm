<?php
$this->load->view("includes/summernote");
?>
<input type="hidden" name="id_indicator" value="<?php echo $id_indicator ?>" />
<!-- listar todos los clientes -->
<div class="form-group">
    <label for="clients" class="col-md-3"><?php echo lang('client'); ?></label>
    <div class="col-md-9">
        <?php
		echo form_dropdown("clients", $clientes, $cliente, "id='clients' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
					echo form_dropdown("project", $proyectos, "", "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				}
            ?>
        </div>
    </div>
</div>
<!-- Nombre indicador -->
<div class="form-group">
    <label for="indicator_name" class="<?php echo $label_column; ?>"><?php echo lang('indicator_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "indicator_name",
            "name" => "indicator_name",
            "value" => $indicator_name,
            "class" => "form-control",
            "placeholder" => lang('indicator_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
<!-- Unidad -->
<div class="form-group">
    <label for="unit" class="<?php echo $label_column; ?>"><?php echo lang('unit'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "unit",
            "name" => "unit",
            "value" => $unit,
            "class" => "form-control",
            "placeholder" => lang('unit'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
<!-- color -->
<div class="form-group">
    <label for="color" class="<?php echo $label_column; ?>"><?php echo lang('color'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <div id="cp11" class="input-group colorpicker-component colorpicker-default">
        <?php
        echo form_input(array(
            "id" => "color",
            "name" => "color",
            "value" => $color,
            "class" => "form-control",
            "placeholder" => lang('color'),
            "autocomplete"=> "off",
            "readonly"=> true
        ));
        ?>
        <span class="input-group-addon"><i id="coloricon" style="border: solid black 1px;"></i></span>
        </div>
    </div>
</div>
<!-- iconos -->
<div class="form-group">
<label for="icon" class="col-md-3"><?php echo lang("icon"); ?></label>
	<div class="col-md-9">
		<select name="icon" id="icon" class="select2 validate-hidden">
			<option value="">-</option>
			<?php foreach($icons as $key=> $icons) { ?>
				<option value="<?php echo $key ?>" ><?php echo $icons ?></option>
			<?php } ?>
		</select>
	</div>
</div>

<div id="fields_category">
	<?php  
		$arraySelected = array();
		$arraySelected2 = array();
		$arrayIndicators = array();

		foreach($categories_selected as $key => $innerArray){
			$arraySelected[] = $key;
			$arraySelected2[(string)$key] = $innerArray;
		}

		foreach($categories_available as $key => $innerArray){
			if(array_search($innerArray, $arraySelected2) === FALSE){
				$arrayIndicators[(string)$key] = $innerArray;
			}

		}

		$array_final = $arraySelected2+$arrayIndicators;

		$html = '';
		$html .= '<div class="form-group">';
			$html .= '<label for="category" class="col-md-3">'.lang('category').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_multiselect("category[]", $array_final, $arraySelected, "id='category' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';

		echo $html;
	?>
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