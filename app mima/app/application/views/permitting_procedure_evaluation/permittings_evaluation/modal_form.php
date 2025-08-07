<?php echo form_open(get_uri("permitting_procedure_evaluation/save"), array("id" => "procedure_evaluation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("permitting_procedure_evaluation/permittings_evaluation/permittings_evaluation_form_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <?php if(!($puede_editar == 3 && $puede_agregar == 3)){ ?>
    	<button id="btn_guardar" type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
	<?php }?>
    
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		<?php if(($puede_editar == 2 && $puede_agregar == 3) && !$evaluaciones_propias) { ?>
			$("#btn_guardar").remove();
		<?php } ?>
		
        $("#procedure_evaluation-form").appForm({
            onSuccess: function(result) {
				//if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        //location.reload();
						$.ajax({
							url:  '<?php echo_uri("permitting_procedure_evaluation/get_evaluation_table_of_permitting"); ?>',
							type:  'post',
							data: {
								id_evaluado:result.id_evaluado, 
								id_valor_permiso:result.id_valor_permiso, 
								id_permiso_proyecto:result.id_permiso_proyecto},
							//dataType:'json',
							success: function(respuesta){
								$('#evaluation_table').html(respuesta);	
							}
						});
						
						//$("#compliance_evaluation-table").appTable({newData: result.data, dataId: result.id});
                    }, 500);

                //} else {
                    //$("#compliance_evaluation-table").appTable({newData: result.data, dataId: result.id});
                //}
            }
        });
    });
</script>    