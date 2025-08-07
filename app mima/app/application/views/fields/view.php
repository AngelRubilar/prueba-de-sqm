<?php echo form_open("", array("id" => "fields-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php
	$this->load->view("includes/summernote");
	?>
	<input type="hidden" name="id" value="<?php echo $field_info->id; ?>" />
	<div class="form-group">
		<label for="field_name" class="col-md-3"><?php echo lang('field_name'); ?></label>
		<div class="col-md-9">
			<?php
			echo $field_info->nombre;
			?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="field_type" class="col-md-3"><?php echo lang('field_type'); ?></label>
		<div class="col-md-9">
			<?php
			echo $field_info->tipo_campo;
			?>
		</div>
	</div>
	
	<div class="form-group" id="default_value_field_group">
    	<label for="preview" class="col-md-3"><?php echo lang('preview'); ?></label>
        <!--<div class="col-md-10">-->
        	<?php
			if($preview){
				echo $preview;
			}
			?>
		<!--</div>-->
    </div>
	
	
	<div class="form-group">
		<label for="obligatory_field" class="col-md-3"><?php echo lang('obligatory_field'); ?>
			<!--<span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('obligatory_field_description') ?>"><i class="fa fa-question-circle"></i></span>-->
		</label>
		<div class="col-md-3">
			<?php
			echo form_checkbox("obligatory_field", "", $field_info->obligatorio ? true : false, "id='obligatory_field' disabled");
			?>                       
		</div>
		
		<label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?>
			<!--<span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('disabled_field_description') ?>"><i class="fa fa-question-circle"></i></span>-->
		</label>
		<div class="col-md-3">
			<?php
			echo form_checkbox("disabled_field", "", $field_info->habilitado ? true : false, "id='disabled_field' disabled");
			?>                       
		</div>
		
	</div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $field_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($field_info->modified)?$field_info->modified:'-';
            ?>
        </div>
    </div>
	
	
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
			$('#fields-form .select2').select2();
			$('#fields-form .select2_multiple').select2({
				multiple: true
			});
			$('#fields-form .rut').rut({
				formatOn: 'keyup',
				minimumLength: 8,
				validateOn: 'change'
			});
			setDatePicker("#default_date_field");
			setDatePicker("#default_date_field1, #default_date_field2");
			setTimePicker('#time_preview');
			
			initWYSIWYGEditor("#default_value_field_rich", {
				height: 100,
				toolbar: [
					['style', ['style']],
					['font', ['bold', 'italic', 'underline', 'clear']],
					['fontname', ['fontname']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['height', ['height']],
					//['table', ['table']],
					//['insert', ['hr', 'picture', 'video']],
					['view', [/*'fullscreen'*/, 'codeview']]
				],
				onImageUpload: function (files, editor, welEditable) {
					//insert image url
				},
				lang: "<?php echo lang('language_locale_long'); ?>"
			});

			var selectValues = "";
			switch ($('#unit_type').text()){
				case "Masa":
					$('#unit').empty();
					selectValues = { "": "-", "g": "g", "Kg": "Kg", "Ton": "Ton" };					
					break;
				case "Volumen":
						$('#unit').empty();
						selectValues = { "": "-", "cc - ml": "cc - ml", "l": "l", "m3": "m3" };
						break;
						
				case "Longitud":
					$('#unit').empty();
					selectValues = { "": "-", "m": "m", "km": "km" };
					break;
					
				case "Superficie":
					$('#unit').empty();
					selectValues = { "": "-", "m2": "m2", "Km2": "Km2", "Ha": "Ha" };
					break;
					
				case "Potencia":
					$('#unit').empty();
					selectValues = { "": "-", "kW": "kW", "MW": "MW" };
					break;
					
				case "Energía":
					$('#unit').empty();
					selectValues = { "": "-", "kWh": "kWh", "MWh": "MWh", "J": "J" };
					break;
				
				default:
					$('#unit').empty();
					selectValues = { "": "-" };	
			}
			
			$.each(selectValues, function(key, value) {   
				 $('#unit').append($("<option></option>").attr("value",key).text(value)); 					
			});
			
		});
		

	</script>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#fields-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#fields-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
        $("#field_name").focus();
    });
</script>  