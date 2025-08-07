<?php echo form_open(get_uri("general_settings/save_alert_config_users"), array("id" => "alert_config_users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("general_settings/alert_settings_users/alert_settings_users_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
        $("#alert_config_users-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
					$('#risk_value-' + result.id_item_config).html(result.risk_value);
					$('#threshold_value-' + result.id_item_config).html(result.threshold_value);
					$('#configured-' + result.id_item_config).html(result.config_icon);
					$('#action-' + result.id_item_config).html(result.btn_action);
                }
            }
        });
		

        //$("#company_name").focus();
    });
</script>    