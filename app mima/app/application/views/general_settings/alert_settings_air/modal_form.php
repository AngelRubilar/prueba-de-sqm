<?php echo form_open(get_uri("general_settings/save_alert_config_air"), array("id" => "alert_config_air-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("general_settings/alert_settings_air/alert_settings_air_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button id="btn_save" type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        <?php if($accordeon == "forecast_alerts"){ ?>

            $("#btn_save").click(function(){
                $('#alert_config_air-form input[name^="nc_active"]').each(function(){
                    if ($(this).is(":checked")) {
                        $input_hidden = $(this).next('input');
                        $input_hidden.remove();
                    }
                });
                if (!$('#alert_config_air-form input[name^="nc_name"], input[name^="min_value"]').valid()) {	
                    return false;
                }   
            });

        <?php } ?>

        <?php if($accordeon == "action_plan"){ ?>

            $("#btn_save").click(function(){
                $('#alert_config_air-form input[name^="ap_active"]').each(function(){
                    if ($(this).is(":checked")) {
                        $input_hidden = $(this).next('input');
                        $input_hidden.remove();
                    }
                }); 
                $('#alert_config_air-form input[name^="ap_email"]').each(function(){
                    if ($(this).is(":checked")) {
                        $input_hidden = $(this).next('input');
                        $input_hidden.remove();
                    }
                }); 
                $('#alert_config_air-form input[name^="ap_web"]').each(function(){
                    if ($(this).is(":checked")) {
                        $input_hidden = $(this).next('input');
                        $input_hidden.remove();
                    }
                }); 
            });

        <?php } ?>
		
        $("#alert_config_air-form").appForm({
            onSuccess: function(result) {

                <?php if($accordeon == "forecast_alerts"){ ?>
					$('#min_value-' + result.id_item_config).html(result.min_value);
					$('#max_value-' + result.id_item_config).html(result.max_value);
					$('#configured-' + result.id_item_config).html(result.config_icon);
					$('#action-' + result.id_item_config).html(result.btn_action);
                <?php } ?>

                <?php if($accordeon == "action_plan"){ ?>
                    $('#n_alerts-' + result.id_item_config).html(result.n_alerts);
                    $('#n_action_plans-' + result.id_item_config).html(result.n_action_plans);
                    $('#configured_ap-' + result.id_item_config).html(result.config_icon);
                    $('#action_ap-' + result.id_item_config).html(result.btn_action);
                <?php } ?>

            }
        });
		
    });
</script>    