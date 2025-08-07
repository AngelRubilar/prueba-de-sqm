<?php echo form_open(get_uri("general_settings/save_notification_config_admin"), array("id" => "notification_config_admin-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("general_settings/notification_settings_admin/notification_settings_admin_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
        $("#notification_config_admin-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
					
					if(result.item == "records"){
						
						$('#form_add-' + result.item).html(result.config_icon_form_add);
						$('#form_edit_name-' + result.item).html(result.config_icon_form_edit_name);
						$('#form_edit_cat-' + result.item).html(result.config_icon_form_edit_cat);
						$('#form_delete-' + result.item).html(result.config_icon_form_delete);
						$('#action-' + result.item).html(result.btn_action);
						
					} if(result.item == "indicators"){
						
						$('#uf_add_element-' + result.item).html(result.config_icon_add);
						$('#uf_edit_element-' + result.item).html(result.config_icon_edit);
						$('#uf_delete_element-' + result.item).html(result.config_icon_delete);
						$('#action-' + result.item).html(result.btn_action);
						
					} else {
						
						$('#configured-' + result.id_item_config).html(result.config_icon);
						$('#action-' + result.id_item_config).html(result.btn_action);
						
					}	
					
                }
            }
        });
		

        //$("#company_name").focus();
    });
</script>    