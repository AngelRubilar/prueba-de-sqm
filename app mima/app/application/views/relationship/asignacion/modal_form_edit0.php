<?php echo form_open(get_uri("relationship/save_asignacion_edit"), array("id" => "asignacion-form-edit", "class" => "general-form", "role" => "form", "autocomplete" => "off")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("relationship/asignacion2/asignacion_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary" id="edit-form-submit"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
        $("#asignacion-form-edit").appForm({
            onSuccess: function(result) {
				
				if(result.ingreso){
					$.each(result.datos, function(index, row){
						$("#asignacion-table").appTable({newData: row.data, dataId: row.id});
					});
				}else{
					$("#asignacion-table").appTable({newData: result.data, dataId: result.id});
				}
            },
			onSubmit: function() {
				
            },
        });
		$('#asignacion-form-edit').validate().settings.ignore = "";
		
		
		$("#edit-form-submit").click(function() {
			var $modelo_sp = $('#asignacion-form-edit #modelo_sp').clone();
			var $modelo_pu = $('#asignacion-form-edit #modelo_pu').clone();
			
			if(!$("#asignacion-form-edit").valid()){
				
				$('#asignacion-form-edit #modelo_sp').html('');
				$('#asignacion-form-edit #modelo_pu').html('');
				
				if($("#asignacion-form-edit").valid()){
					$("#asignacion-form-edit").submit();
				}else{
					$('#asignacion-form-edit #modelo_sp').replaceWith($modelo_sp);
					$('#asignacion-form-edit #modelo_pu').replaceWith($modelo_pu);
				}
				
			}else{
				$('#asignacion-form-edit #modelo_sp').html('');
				$('#asignacion-form-edit #modelo_pu').html('');
			}
			
			
		});
    });
</script>    