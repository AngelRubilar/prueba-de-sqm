<input type="hidden" name="id" id="id" value="<?php echo $model_info->id; ?>" />

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
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_dropdown("client", $clients_dropdown, array($model_info->id_client), "id='client' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'");
		?>
    </div>
</div>

<div id="projects_group">
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            	echo form_dropdown("project", $projects_dropdown, array($model_info->id_project), "id='project' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'");
            ?>
        </div>
    </div>
</div>

<div id="air_sectors_group">
    <div class="form-group">
        <label for="air_sector" class="<?php echo $label_column; ?>"><?php echo lang('sector'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            	echo form_dropdown("air_sector", $air_sectors_dropdown, array($model_info->id_air_sector), "id='air_sector' class='select2 validate-hidden' data-rule-required='true' data-msg-required='".lang('field_required')."'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="is_receptor" class="<?php echo $label_column; ?>"><?php echo lang('is_receptor?'); ?></label>
    <div class="<?php echo $field_column; ?>">
		<?php 

			// EDICION
			if($model_info->id){
				$checked_station = ($model_info->is_receptor == 0) ? "checked" : "";
            	$checked_receptor = ($model_info->is_receptor == 1) ? "checked" : "";
			}else{// INGRESO
				$checked_station = "checked";
            	$checked_receptor = "";
			}
        ?>
        
        <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
            <?php echo lang("yes");?>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9">
            <?php
            
            $datos_campo = array(
                "id" => "is_receptor",
                "name" => "tipo_estacion",
                "value" => "1",
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                $checked_receptor => $checked_receptor
			);
            
            echo form_radio($datos_campo);
            
            ?>	 
        </div>
    
        <div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">
            <?php echo lang("no");?>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9">
            <?php
            
			$datos_campo = array(
                "id" => "is_station",
                "name" => "tipo_estacion",
                "value" => "0",
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                $checked_station => $checked_station
            );
            
            echo form_radio($datos_campo);
            
            ?>	 
        </div>
        
    </div>
</div>

<div class="form-group">
    <label for="description" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_textarea(array(
				"id" => "description",
				"name" => "description",
				//"value" => htmlspecialchars_decode($model_info->description),
				"value" =>$model_info->description,
				"class" => "form-control",
				"placeholder" => lang('description'),
				"autofocus" => false,
				"autocomplete"=> "off",
				"maxlength" => "2000"
			));
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

<div class="form-group multi-column">
    <label for="load_code" class="<?php echo $label_column; ?>"><?php echo lang('load_code'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
			echo form_input(array(
				"id" => "load_code",
				"name" => "load_code",
				"value" => $model_info->load_code,
				"class" => "form-control",
				"placeholder" => lang('load_code'),
				//"autofocus" => true,
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete"=> "off",
				"maxlength" => "255"
			));
        ?>
	</div>
</div>

<div class="form-group multi-column">
    <label for="load_code_api" class="<?php echo $label_column; ?>"><?php echo lang('load_code_api'); ?></label>
	<div class="<?php echo $field_column; ?>">
		<?php
			echo form_input(array(
				"id" => "load_code_api",
				"name" => "load_code_api",
				"value" => $model_info->load_code_api,
				"class" => "form-control",
				"placeholder" => lang('load_code_api'),
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
    <label for="air_variables" class="<?php echo $label_column; ?>"><?php echo lang('variables'); ?></label>
    <div class="<?php echo $field_column; ?>">
		<?php
			echo form_multiselect(
				"air_variables[]", 
				$air_variables_multiselect_availables, 
				$air_variables_multiselect_selected, 
				"id='air_variables' class='multiple validate-hidden' multiple='multiple' data-rule-required='true' data-msg-required='".lang('field_required')."'"
			);
		?>
    </div>
</div>

<style>
	.seleccionado{
		background-color: #08c;
		text-color: #fff !important;
	}

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
		$('#air_stations-form .select2').select2();
		
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
		
		$('#air_variables').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
			//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-6'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-6'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
			keepOrder: true,
			afterSelect: function(value){
				$('#air_variables option[value="'+value+'"]').remove();
				$('#air_variables').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
				
			},
			afterDeselect: function(value){ 
				$('#air_variables option[value="'+value+'"]').removeAttr('selected');
			}
		});

		$('#client').change(function(){

			var id_client = $(this).val();
			select2LoadingStatusOn($('#project'));
					
			$.ajax({
				url: '<?php echo_uri("air_stations/get_projects_of_client"); ?>',
				type: 'post',
				data: {
					id_client: id_client,
					field_column: "<?php echo $field_column; ?>",
					label_column: "<?php echo $label_column; ?>"
				},
				success: function(respuesta){
					$('#projects_group').html(respuesta);
					$('#project').select2();
				}
			});

		});	
		
		$(document).on("change","#project", function(event){
		
			var id_project = $(this).val();	
			select2LoadingStatusOn($('#air_sector'));
			
			$.ajax({
				url:  '<?php echo_uri("air_stations/get_air_sectors_of_project"); ?>',
				type:  'post',
				data: {
					id_project: id_project, 
					field_column: "<?php echo $field_column; ?>",
					label_column: "<?php echo $label_column; ?>"
				},
				success: function(respuesta){
					$('#air_sectors_group').html(respuesta);
					$('#air_sector').select2();
				}
			});
			
			event.stopImmediatePropagation();
			
		});

	});

</script>