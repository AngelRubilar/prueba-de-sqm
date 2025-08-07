<?php echo form_open(get_uri("client_indicators/save"), array("id" => "client_indicators-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("waste/client/indicators/client_indicators_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function() {
		
		//$("#").select2();
		//$("#").select2();
		$("#icon").select2();
		$('#cp11').colorpicker({format: 'hex',});
		
        $("#client_indicators-form").appForm({
            onSuccess: function(result) {
                if (result.success === "true") {
                    appAlert.success(result.message, {duration: 10000});
					
                    setTimeout(function() {
                        location.reload();
                    }, 500);
					
                } else {
                    $("#client_indicators-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

    });
</script>