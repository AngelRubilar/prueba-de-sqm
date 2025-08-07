<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_general_settings_client"), array("id" => "general-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("general"); ?></h4>
        </div>
		<div class="panel-body">
        	
            <input type="hidden" id="id_general_setting" name="id_general_setting" value="<?php echo $general_settings->id; ?>" />
            <input type="hidden" id="id_cliente_general_setting" name="id_cliente" />
            
            <div class="form-group">
                <label for="thousands_separator" class=" col-md-2"><?php echo lang('thousands_separator'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_dropdown(
                            "thousands_separator", array(
                                "1" => lang("thousands_separator_config_opt1"),
                                "2" => lang("thousands_separator_config_opt2")
                            ), $general_settings->thousands_separator, "class='select2 mini' id='thousands_separator'"
                    );
                    ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="decimals_separator" class=" col-md-2"><?php echo lang('decimals_separator'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_dropdown(
                            "decimals_separator", array(
                                "1" => lang("decimals_separator_config_opt1"),
                                "2" => lang("decimals_separator_config_opt2")
                            ), $general_settings->decimals_separator, "class='select2 mini' id='decimals_separator'"
                    );
                    ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="decimal_numbers_config" class=" col-md-2"><?php echo lang('decimal_numbers_config'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_dropdown(
                            "decimal_numbers_config", array(
                                "0" => lang("no_decimals"),
                                "1" => "1 ".lang("decimal"),
                                "2" => "2 ".lang("decimals"),
                                "3" => "3 ".lang("decimals"),
								"4" => "4 ".lang("decimals"),
								"5" => "5 ".lang("decimals")
                            ), $general_settings->decimal_numbers, "class='select2 mini'"
                    );
                    ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="date_format" class=" col-md-2"><?php echo lang('date_format'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_dropdown(
                            "date_format", array(
                        /*"d-m-Y" => "d-m-Y",
                        "m-d-Y" => "m-d-Y",
                        "Y-m-d" => "Y-m-d",
                        "d/m/Y" => "d/m/Y",
                        "m/d/Y" => "m/d/Y",
                        "Y/m/d" => "Y/m/d",
                        "d.m.Y" => "d.m.Y",
                        "m.d.Y" => "m.d.Y",
                        "Y.m.d" => "Y.m.d",*/
						"d-m-Y" => lang('d-m-Y'),
                        "m-d-Y" => lang('m-d-Y'),
                        "Y-m-d" => lang('Y-m-d'),
                        "d/m/Y" => lang('d/m/Y'),
                        "m/d/Y" => lang('m/d/Y'),
                        "Y/m/d" => lang('Y/m/d'),
                        "d.m.Y" => lang('d.m.Y'),
                        "m.d.Y" => lang('m.d.Y'),
                        "Y.m.d" => lang('Y.m.d'),
                            ), $general_settings->date_format , "class='select2 mini'"
                    );
                    ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="timezone" class=" col-md-2"><?php echo lang('timezone'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_dropdown(
                            "timezone", $timezone_dropdown, $general_settings->timezone, "class='select2 mini'"
                    );
					
                    ?>
                </div>
            </div>
                   
            <div class="form-group">
                <label for="time_format" class=" col-md-2"><?php echo lang('time_format'); ?></label>
                <div class="col-md-10">
                    <?php
                    echo form_dropdown(
                            "time_format", array(
								"capital" => "12 AM/PM",
								"small" => "12 am/pm",
								//"24_hours" => "24 hours"
								"24_hours" => "24 ".lang('hours')
                            ), $general_settings->time_format, "class='select2 mini'"
                    );
                    ?>
                </div>
            </div>
           
        </div>
        
        <br/><br/>
            
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
		var id_cliente = $('#client').val();
		
		$("#general-settings-form .select2").select2();
		
		$('#id_cliente_general_setting').val(id_cliente);
		$('#decimals_separator option[value="1"]').prop('disabled', true);
		
		$('#general_color').colorpicker({
			format: 'hex'	
		});
		
        //$("#general-settings-form .select2").select2();
		
		$('#thousands_separator').change(function(){
			
			//$('#decimals_separator').select2('val', '');
			if($(this).val() == 1){
				$('#decimals_separator option[value="1"]').prop('disabled', true);
				$('#decimals_separator option[value="2"]').prop('disabled', false);
				$('#decimals_separator').select2('val', 2);
			}
			if($(this).val() == 2){
				$('#decimals_separator option[value="1"]').prop('disabled', false);
				$('#decimals_separator option[value="2"]').prop('disabled', true);
				$('#decimals_separator').select2('val', 1);
			}
			
		});
		
		$("#general-settings-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
									
                    if (obj.name === "invoice_logo" || obj.name === "site_logo") {
                        var image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = image;
                    }
                });
            },
            onSuccess: function (result) {

				$('#id_general_setting').val(result.save_id);

                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
            }
        });
		
    });
</script>