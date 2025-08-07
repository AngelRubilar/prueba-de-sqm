<?php echo form_open(get_uri("compromises_reportables_evaluation/save"), array("id" => "compliance_evaluation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("compromises_reportables_evaluation/compliance_evaluation/compliance_evaluation_form_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <?php if(!($puede_editar == 3 && $puede_agregar == 3)){ ?>
		<button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
	<?php }?>

</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		<?php if(($puede_editar == 2 && $puede_agregar == 3) && !$evaluaciones_propias) { ?>
			$("#btn_guardar").remove();
		<?php } ?>
		
        $("#compliance_evaluation-form").appForm({
            onSuccess: function(result) {
				
				if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#compliance_evaluation-table").appTable({newData: result.data, dataId: result.id});
                }

            }
        });
    });
</script>    