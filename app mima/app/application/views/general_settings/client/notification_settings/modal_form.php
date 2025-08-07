<?php echo form_open(get_uri("general_settings/save_notification_config_client"), array("id" => "notification_config_client-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("general_settings/client/notification_settings/notification_settings_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
        $("#notification_config_client-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
					
					$('#add-' + result.item).html(result.config_icon_add);
					$('#edit-' + result.item).html(result.config_icon_edit);
					$('#delete-' + result.item).html(result.config_icon_delete);
					$('#audit-' + result.item).html(result.config_icon_audit);
					$('#close-' + result.item).html(result.config_icon_close);
					$('#send_email-' + result.item).html(result.config_icon_send_email);
					$('#action-' + result.item).html(result.btn_action);
					
                }
            }
        });
		

        //$("#company_name").focus();
    });
</script>    