<?php echo form_open(get_uri("air_synoptic_data_upload/save/"), array("id" => "air_synoptic_data_upload-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("air_synoptic_data_upload/air_synoptic_data_upload_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#air_synoptic_data_upload-form").appForm({
            onSuccess: function(result) {

				//console.log(result);
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#air_synoptic_data_upload-table").appTable({newData: result.data, dataId: result.id});
                }
				
				$('#fecha_modificacion').text(result.fecha_modificacion);
				$('#num_registros').text(result.num_registros);
            }
            
        });
		//$("#air_synoptic_data_upload-form").validate().settings.ignore = "";
        //$("#company_name").focus();
    });

    $("#air_synoptic_data_upload-form").validate().settings.ignore = "";
</script>    