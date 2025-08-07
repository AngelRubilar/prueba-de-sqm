<?php echo form_open(get_uri("clients/save"), array("id" => "client-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("clients/client_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<?php $this->load->view("includes/cropbox"); ?>
<style>

.modal-footer{
	position: relative;
	z-index: 999;
	background-color: #FFF;
}

</style>
<script type="text/javascript">
    $(document).ready(function() {
		
        $("#client-form").appForm({
			beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if(obj.name === "site_logo") {
                        var image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = image;
                    }
					
                });
            },
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#client-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
		
		var uploadUrl = "<?php echo get_uri("clients/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("clients/validate_file"); ?>";

        var dropzone = attachDropzoneWithForm("#client-form", uploadUrl, validationUrl, {maxFiles: 1});

        $(".cropbox-upload").change(function () {
            showCropBox(this);
        });
		
		
		
    });
</script>    