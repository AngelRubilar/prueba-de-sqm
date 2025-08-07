<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_global_general_settings"), array("id" => "save_global_general_settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("general"); ?></h4>
        </div>
		<div class="panel-body">
        
            <div class="form-group">
                <label for="max_file_size" class=" col-md-2"><?php echo lang('upload_max_files_size'). " (MB)"; ?></label>
                
                <div class="col-md-10">
                    <?php
                    echo form_input(array(
                        "id" => "max_file_size",
                        "name" => "max_file_size",
                        "value" => get_setting('max_file_size'),
                        "class" => "form-control",
                        "placeholder" => lang("upload_max_files_size"),
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required"),
						"data-rule-regex" => "^([1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000)$",
						"data-msg-regex" => lang("positive_number_1_to_1000"),
						"autocomplete" => "off"
                    ));
                    ?>
                </div>
            </div>

        </div>
        
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>    
    <?php echo form_close(); ?>
    
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#save_global_general_settings-form .select2").select2();
		
		$("#save_global_general_settings-form").appForm({
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
                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
            }
        });
		
    });
</script>