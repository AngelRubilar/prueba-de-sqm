<?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
?>

<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client_id2", $clientes, "", "id='client_id2' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>


<div class="form-group">
    <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("project2", array("" => "-"), "", "id='project2' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div id="criterio_group" class="form-group">
    <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("criterio2", array("" => "-"), "", "id='criterio2' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
		
		//$('#client_id2').focus();
		//$('#asignacion-form .select2').select2();
	
		$('#client_id2').change(function(){
			
			var id_client = $(this).val();
			
			if(id_client != ""){
				
				select2LoadingStatusOn($('#project2'));

				$.ajax({
					url: '<?php echo_uri("clients/get_projects_of_client_json") ?>',
					type: 'post',
					data: {id_client:id_client},
					dataType:'json',
					success: function(respuesta){
						$('#project2').html("");
						$.each((respuesta), function() {
							$('#project2').append($("<option />").val(this.id).text(this.text));
						});
						
						select2LoadingStatusOff($('#project2'));
						//$('#project2').select2();
					}
				});
			
			
			} else {
				
				$('#project2').html("");
				$('#project2').append($("<option />").val("").text("-"));
				$('#project2').select2('val','');
				$('#criterio2').html("");
				$('#criterio2').append($("<option />").val("").text("-"));	
				$('#criterio2').select2('val','');

				$('#rule_options_sp_group').html("");
				$('#rule_options_pu_group').html("");
			}
			
			
		});

		$('#project2').change(function(){
			
			var id_proyecto = $(this).val();
			
			if(id_proyecto != ""){
				
				select2LoadingStatusOn($('#criterio2'));
			
				$.ajax({
					url:  '<?php echo_uri("relationship/get_criterio_of_project_json") ?>',
					type:  'post',
					data: {id_proyecto:id_proyecto},
					dataType:'json',
					success: function(respuesta){
						$('#criterio2').html("");
						$.each((respuesta), function() {
							$('#criterio2').append($("<option />").val(this.id).text(this.text));
						});
						
						select2LoadingStatusOff($('#criterio2'));
						//$('#criterio2').select2();
					}
				});
			
			} else {

				$('#criterio2').html("");
				$('#criterio2').append($("<option />").val("").text("-"));	
				$('#criterio2').select2('val','');

			}
			
			$('#rule_options_sp_group').html("");
			$('#rule_options_pu_group').html("");

		});    
				
		$('#criterio2').on('change', function(){
			
			var id_cliente = $('#client_id2').val();
			var id_proyecto = $('#project2').val();
			var id_criterio = $(this).val();
			
			$.ajax({
				url:  '<?php echo_uri("relationship/get_rule_options") ?>',
				type:  'post',
				data: {id_criterio:id_criterio, id_proyecto:id_proyecto, id_cliente:id_cliente},
				dataType:'json',
				success: function(respuesta){
					
					var tabla_sp = respuesta[0];
					var tabla_pu = respuesta[1];
					
					$('#rule_options_sp_group').html(tabla_sp);
					$('#rule_options_sp_group .select2').select2();
					initScrollbar('#rule_options_sp_group > div', {setHeight: 300});
					$("#modelo_sp").html('');
					$('#rule_options_sp_group #tabla_asignacion_sp tbody:eq(0) > tr:eq(0) > td:eq(2) > select').clone().appendTo("#modelo_sp");
					
					$('#rule_options_pu_group').html(tabla_pu);
					$('#rule_options_pu_group .select2').select2();
					initScrollbar('#rule_options_pu_group > div', {setHeight: 300});
					$("#modelo_pu").html('');
					$('#rule_options_pu_group #tabla_asignacion_pu tbody:eq(0) > tr:eq(0) > td:eq(2) > select').clone().appendTo("#modelo_pu");
				}
			});
			
		});
		
		/*$('#ajaxModal').on('hidden.bs.modal', function () {
			$('.app-alert').hide();
		});*/
		
		    
    });
	
</script>    