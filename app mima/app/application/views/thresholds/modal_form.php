<?php echo form_open(get_uri("thresholds/save"), array("id" => "thresholds-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("thresholds/thresholds_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		$('#thresholds-form .select2').select2();
		
		$('#clients').change(function(){
			
			var id_client = $(this).val();
			if(id_client){
				
				select2LoadingStatusOn($('#project'));

				$.ajax({
					url: '<?php echo_uri("clients/get_projects_of_client_json"); ?>',
					type: 'post',
					data: {id_client:id_client},
					dataType:'json',
					success: function(respuesta){
						
						$('#project').html("");
						$.each((respuesta), function() {
							$('#project').append($("<option />").val(this.id).text(this.text));
						});
						$('#project').select2();
						select2LoadingStatusOff($('#project'));
					}
				});
			
			} else {
				$('#project').html("");
				$('#project').append($("<option />").val("").text("-"));	
				$('#project').select2();
			}
			
			$('#module').html("");
			$('#module').append($("<option />").val("").text("-"));					
			$('#module').select2();
			
			$('#forms').html("");
			$('#forms').append($("<option />").val("").text("-"));					
			$('#forms').select2();
			
			$('#material').html("");
			$('#material').append($("<option />").val("").text("-"));					
			$('#material').select2();
			
			$('#category').html("");
			$('#category').append($("<option />").val("").text("-"));					
			$('#category').select2();
			
		});
		
		$('#project').change(function(){
			
			var id_project = $(this).val();
			var id_client = $('#clients').val();
			select2LoadingStatusOn($('#module'));
			
			$.ajax({
				url: '<?php echo_uri("thresholds/get_modules") ?>',
				type: 'post',
				data: {id_client:id_client, id_project:id_project},
				dataType: 'json',
				success: function(respuesta){
					
					$('#module').html("");
					$.each((respuesta), function() {
						$('#module').append($("<option />").val(this.id).text(this.text));
					});
					$('#module').select2();
					
					select2LoadingStatusOff($('#module'));
				}
			});
		});
		
		$('#module').change(function(){
			
			var id_module = $(this).val();
			var id_project = $('#project').val();
			var id_client = $('#clients').val();
			select2LoadingStatusOn($('#forms'));
			
			$.ajax({
				url: '<?php echo_uri("thresholds/get_forms") ?>',
				type: 'post',
				data: {id_module:id_module,id_project:id_project,id_client:id_client},
				dataType: 'json',
				success: function(respuesta){
					
					$('#forms').html("");
					$.each((respuesta), function() {
						$('#forms').append($("<option />").val(this.id).text(this.text));
					});
					$('#forms').select2();
					
					select2LoadingStatusOff($('#forms'));
					
				}
			});
		});
		
		
		$('#forms').change(function(){
			
			var id_form = $(this).val();
			var id_project = $('#project').val();
			var id_client = $('#clients').val();
			select2LoadingStatusOn($('#material'));
			
			$.ajax({
				url: '<?php echo_uri("thresholds/get_materials_by_form"); ?>',
				type: 'post',
				data: {id_form:id_form},
				dataType: 'json',
				success: function(respuesta){
					
					$('#material').html("");
					$.each((respuesta), function() {
						$('#material').append($("<option />").val(this.id).text(this.text));
					});
					$('#material').select2();
					
					select2LoadingStatusOff($('#material'));
					
					
					$.ajax({
						url: '<?php echo_uri("thresholds/get_unit_type"); ?>',
						type: 'post',
						data: {id_form:id_form},
						success: function(result){
							var obj = jQuery.parseJSON(result);
							$('#unit_type').val(obj.unit_type_name);
							$('#id_unit_type').val(obj.unit_type_id);
							
							var unit_type = $('#unit_type').val();

							$.ajax({
								url: '<?php echo_uri("thresholds/get_system_unit"); ?>',
								type: 'post',
								data: {id_client:id_client,id_project:id_project,unit_type:unit_type},
								success: function(result){
									var obj_unit = jQuery.parseJSON(result);
									$('#unit_name').val(obj_unit.unit_name);
									$('#unit_name_risk_value').val(obj_unit.unit_name);
									$('#unit_name_threshold_value').val(obj_unit.unit_name);
									$('#id_unit').val(obj_unit.unit_id);
								}
							});
							
						}
					});
				}
			});						
		});
		
		$('#material').change(function(){
			
			var id_material = $(this).val();
			var id_project = $('#project').val();
			var forms = $('#forms').val();
			select2LoadingStatusOn($('#category'));
			
			$.ajax({
				url: '<?php echo_uri("thresholds/get_categorias"); ?>',
				type: 'post',
				data: {id_material:id_material,id_project:id_project,forms:forms},
				dataType: 'json',
				success: function(respuesta){
					
					$('#category').html("");
					$.each((respuesta), function() {
						$('#category').append($("<option />").val(this.id).text(this.text));
					});
					$('#category').select2();
					
					select2LoadingStatusOff($('#category'));
					
				}
			});
		});
		
        $("#thresholds-form").appForm({
            onSuccess: function(result) {
                if (result.success === "true") {
                    appAlert.success(result.message, {duration: 10000});
					
                    setTimeout(function() {
                        location.reload();
                    }, 500);
					
                } else {
                    $("#thresholds-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
		
    });
</script>
<style>
.multiselect-header{
  text-align: center;
  padding: 3px;
  background: #7988a2;
  color: #fff;
}
</style>