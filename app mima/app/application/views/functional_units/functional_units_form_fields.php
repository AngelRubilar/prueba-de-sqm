<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="functional_unit_name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "functional_unit_name",
            "name" => "functional_unit_name",
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
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='client_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div id="proyectos_group">
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project", $proyectos, array($model_info->id_proyecto), "id='project' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div id="subproyectos_group">
    <div class="form-group">
        <label for="subproject" class="<?php echo $label_column; ?>"><?php echo lang('subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("subproject", $subproyectos, array($model_info->id_subproyecto), "id='subproject' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="functional_unit_unit" class="<?php echo $label_column; ?>"><?php echo lang('unit'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "functional_unit_unit",
            "name" => "functional_unit_unit",
            "value" => $model_info->unidad,
            "class" => "form-control",
            "placeholder" => lang('unit'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			//"data-rule-number" => true,
			//"data-msg-number" => lang("enter_a_integer"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
		$('#functional_units-form .select2').select2();
		
		$('input[type="text"][maxlength], input[type="number"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#client_id').change(function(){	
			
			var id_client = $(this).val();
			select2LoadingStatusOn($('#project'));
					
			$.ajax({
				url:  '<?php echo_uri("clients/get_projects_of_client"); ?>',
				type:  'post',
				data: {id_client:id_client},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
					select2LoadingStatusOff($('#project'));
					
				}
			});
	
		});	
		
		//$(document).off().on("change", "#project", function() {	
		$(document).on("change", "#project", function(event) {	
		
			var id_proyecto = $(this).val();
			select2LoadingStatusOn($('#subproject'));		
					
			$.ajax({
				url: '<?php echo_uri("subprojects/get_subprojects_of_projects"); ?>',
				type: 'post',
				data: {id_proyecto:id_proyecto},
				//dataType:'json',
				success: function(respuesta){
					
					$('#subproyectos_group').html(respuesta);
					$('#subproject').select2();
					select2LoadingStatusOff($('#subproject'));
				}
			});
			
			event.stopImmediatePropagation();
	
		});	

		
    });
</script>