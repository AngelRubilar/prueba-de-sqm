<?php echo form_open(get_uri("agreements_monitoring/save"), array("id" => "agreement_evaluation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("agreements_monitoring/agreements_evaluation/agreements_evaluation_form_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		$("#agreement_evaluation-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
					$("#agreement_evaluation-table").dataTable().fnReloadAjax(null, false);
                    //$("#agreement_evaluation-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
		
    });
</script>    