<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
   
   	<div class="form-group">
      <label for="status" class="col-md-3"><?php echo lang('evaluation'); ?></label>
        <div class="col-md-9">
            <?php
                //echo form_dropdown("evaluation", $evaluations_dropdown, "", "id='evaluation' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                echo form_dropdown("evaluation", $evaluations_dropdown, "", "id='evaluation' class='select2' ");
            ?>
        </div>
    </div>
   
	<div id="div_archivos_evidencia">
		<?php 
			echo $html_archivos_evidencia;
		?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('[data-toggle="tooltip"]').tooltip();
		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');
		
		
		$('#evaluation').on('change', function(){
			
			id_evaluacion = $(this).val();			
			
			
			$.ajax({
				url:  '<?php echo_uri("compromises_rca_evaluation/get_files_of_evaluation") ?>',
				type:  'post',
				data: {
					id_evaluacion:id_evaluacion},
				success: function(respuesta){
					$('#div_archivos_evidencia').html(respuesta);
				}
			});
			
			
		})
		
		
	});

</script>    