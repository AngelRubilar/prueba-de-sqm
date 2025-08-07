<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->name,
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
		echo form_dropdown("client", $clients, array($model_info->id_client), "id='client' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div id="projects_group">
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project", $projects, array($model_info->id_project), "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
	<label for="air_models" class="<?php echo $label_column; ?>"><?php echo lang('models'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
			echo form_multiselect("air_models[]", $air_models_dropdown, $air_models_selected, "id='air_models' class='select2 multiple validate-hidden' multiple='multiple' data-rule-required='true' data-msg-required='".lang('field_required')."'");
		?>
	</div>
</div>

<div class="form-group multi-column">
    <label for="location" class="<?php echo $label_column; ?>"><?php echo lang('location'); ?></label>

	<div class="col-md-4">
		<?php
			echo form_input(array(
				"id" => "latitude",
				"name" => "latitude",
				"value" => $model_info->latitude,
				"class" => "form-control",
				"placeholder" => lang('latitude'),
				//"autofocus" => true,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                "data-msg-regex" => lang("number_or_decimal_required"),
				"autocomplete"=> "off",
				"maxlength" => "255"
			));
        ?>
	</div>

	<div class="col-md-4">
		<?php
			echo form_input(array(
				"id" => "longitude",
				"name" => "longitude",
				"value" => $model_info->longitude,
				"class" => "form-control",
				"placeholder" => lang('longitude'),
				//"autofocus" => true,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                "data-msg-regex" => lang("number_or_decimal_required"),
				"autocomplete"=> "off",
				"maxlength" => "255"
			));
		?>
	</div>

</div>

<div class="form-group">
	<label for="description" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
		echo form_textarea(array(
			"id" => "description",
			"name" => "description",
			"value" => $model_info->description,
			"class" => "form-control",
			"placeholder" => lang('description'),
			"style" => "height:150px;",
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
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
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#air_sectors-form .select2').select2();
		
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
		
		$('#client').change(function(){	
					
			var id_client = $(this).val();
			select2LoadingStatusOn($('#project'));
					
			$.ajax({
				url:  '<?php echo_uri("air_sectors/get_projects_of_client") ?>',
				type:  'post',
				data: {id_client:id_client},
				//dataType:'json',
				success: function(respuesta){
					
					$('#projects_group').html(respuesta);
					$('#project').select2();
				}
			});
		
		});
		
    });
</script>