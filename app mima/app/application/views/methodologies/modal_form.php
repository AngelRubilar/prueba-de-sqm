<?php echo form_open(get_uri("methodologies/save"), array("id" => "methodology-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("methodologies/methodology_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>
<style>

.modal-footer{
	position: relative;
	z-index: 999;
	background-color: #FFF;
}

</style>
<script type="text/javascript">
    $(document).ready(function() {
		
        $("#methodology-form").appForm({
			onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#methodology-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
		
		
		
    });
</script>    