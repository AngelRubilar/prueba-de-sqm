<?php echo form_open(get_uri("feedback_monitoring/save"), array("id" => "compliance_evaluation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("feedback_monitoring/feedback_evaluation/feedback_evaluation_form_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#compliance_evaluation-form").appForm({
            onSuccess: function(result) {
				//if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        //location.reload();
						$.ajax({
							url:  '<?php echo_uri("feedback_monitoring/get_evaluation_table_of_feedback"); ?>',
							type:  'post',
							data: {
								proposito_visita:result.proposito_visita, 
								id_responsable:result.id_responsable, 
								id_feedback_matrix_config:result.id_feedback_matrix_config},
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