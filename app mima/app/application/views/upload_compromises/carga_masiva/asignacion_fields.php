<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
?>

<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client_id2", $clientes, array($model_info->id_cliente), "id='client_id2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<?php if($model_info->id){ ?>

    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project2", $proyectos, $model_info->id_proyecto, "id='project2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div id="criterio_group" class="form-group">
        <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            //echo form_dropdown("criterio2", $criterios, array($model_info->id_criterio), "id='criterio2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            echo form_dropdown("criterio2", $criterios, $model_info->id_criterio, "id='criterio2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="criterio_sp" class="<?php echo $label_column; ?>"><?php echo lang('subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
				echo form_input(array(
					"id" => "criterio_sp",
					"name" => "criterio_sp",
					"value" => $model_info->criterio_sp,
					"class" => "form-control",
					"placeholder" => lang('subproject'),
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"autocomplete" => "off",
				));
			?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="criterio_pu" class="<?php echo $label_column; ?>"><?php echo lang('unit_process'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
				echo form_input(array(
					"id" => "criterio_pu",
					"name" => "criterio_pu",
					"value" => $model_info->criterio_pu,
					"class" => "form-control",
					"placeholder" => lang('unit_process'),
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"autocomplete" => "off",
				));
			?>
        </div>
    </div>
    
    <div id="div_target_subproject" class="form-group">
        <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('target_subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            	echo form_dropdown("target_subproject", array("" => "-") + $subproyectos, $model_info->sp_destino, "id='target_subproject' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div id="div_unit_process" class="form-group">
        <label for="unit_process" class="<?php echo $label_column; ?>"><?php echo lang('unit_process'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            	echo form_dropdown("unit_process", array("" => "-") + $unit_processes_select, $model_info->pu_destino, "id='target_subproject' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
		
<?php } else { ?>

	<div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("project2", array("" => "-"), "", "id='project2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div id="criterio_group" class="form-group">
        <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            //echo form_dropdown("criterio2", $criterios, array($model_info->id_criterio), "id='criterio2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            echo form_dropdown("criterio2", array("" => "-"), "", "id='criterio2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div id="rule_options_group"></div>
                
<?php } ?>







<!--

<div class="form-group">
    <label for="subproject2" class="<?php echo $label_column; ?>"><?php echo lang('subproject'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("subproject2", $subproject2, array($model_info->id_criterio), "id='subproject2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>




<div class="form-group">
    <label for="pu2" class="<?php echo $label_column; ?>"><?php echo lang('target_unitary_process'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("pu2", $pu2, array($model_info->id_criterio), "id='pu2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

 -->

<script type="text/javascript">

    $(document).ready(function() {
		
		
		
		$('#client_id2').focus();
		$('#asignacion-form .select2').select2();
	
		$('#client_id2').change(function(){
			
			var id_client = $(this).val();
			
			if(id_client != ""){

				$.ajax({
					url:  '<?php echo_uri("clients/get_projects_of_client_json") ?>',
					type:  'post',
					data: {id_client:id_client},
					dataType:'json',
					success: function(respuesta){
						$('#project2').html("");
						$.each((respuesta), function() {
							$('#project2').append($("<option />").val(this.id).text(this.text));
						});
						$('#project2').select2();
					}
				});
			
			
			} else {
				
				$.ajax({
					url:  '<?php echo_uri("clients/get_projects_of_client_json") ?>',
					type:  'post',
					data: {id_client:id_client},
					dataType:'json',
					success: function(respuesta){
						$('#project2').html("");
						$('#project2').append($("<option />").val("").text("-"));	
						$('#project2').select2();
						$('#criterio2').html("");
						$('#criterio2').append($("<option />").val("").text("-"));					
						$('#criterio2').select2();
					}
				});

				$('#rule_options_group').html("");
			}
			
			
		});

		$('#project2').change(function(){
			
			var id_proyecto = $(this).val();
			
			if(id_proyecto != ""){
			
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
						$('#criterio2').select2();
					}
				});
				
				<?php if($model_info->id){ ?>
				
					$.ajax({
						url:  '<?php echo_uri("relationship/get_target_subprojects_of_projects") ?>',
						type:  'post',
						data: {id_proyecto:id_proyecto},
						//dataType:'json',
						success: function(respuesta){
							$('#div_target_subproject').html("");
							$('#div_target_subproject').html(respuesta);
							$('#div_target_subproject .select2').select2();
						}
					});
					
					$.ajax({
						url:  '<?php echo_uri("relationship/get_pu_destino_of_projects") ?>',
						type:  'post',
						data: {id_proyecto:id_proyecto},
						//dataType:'json',
						success: function(respuesta){
							$('#div_unit_process').html("");
							$('#div_unit_process').html(respuesta);
							$('#div_unit_process .select2').select2();
						}
					});
					
				
				<?php } ?>
			
			} else {

				$.ajax({
					url:  '<?php echo_uri("relationship/get_criterio_of_project_json") ?>',
					type:  'post',
					data: {id_proyecto:id_proyecto},
					dataType:'json',
					success: function(respuesta){
						$('#criterio2').html("");
						$('#criterio2').append($("<option />").val("").text("-"));
						$('#criterio2').select2();
					}
				});

			}
			
			$('#rule_options_group').html("");

		});    
				
		$('#criterio2').on('change', function(){
			
			var id_cliente = $('#client_id2').val();
			var id_proyecto = $('#project2').val();
			var id_criterio = $(this).val();
			
			$.ajax({
				url:  '<?php echo_uri("relationship/get_rule_options") ?>',
				type:  'post',
				data: {id_criterio:id_criterio, id_proyecto:id_proyecto, id_cliente:id_cliente},
				//dataType:'json',
				success: function(respuesta){
					$('#rule_options_group').html(respuesta);
					$('#rule_options_group .select2').select2();
					initScrollbar('#rule_options_group > div', {setHeight: 300});

				}
			});
			
		});
		
		$('#ajaxModal').on('hidden.bs.modal', function () {
			$('.app-alert').hide();
		});
		    
    });
	
</script>    