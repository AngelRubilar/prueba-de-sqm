<?php echo form_open(get_uri("other_records/save/".$id_other_record), array("id" => "other_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("other_records/records/other_records_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#other_records-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#other_records-table").appTable({newData: result.data, dataId: result.id});
                }
				
				$('#fecha_modificacion').text(result.fecha_modificacion);
				$('#num_registros').text(result.num_registros);
            }
        });
		$("#other_records-form").validate().settings.ignore = "";
        //$("#company_name").focus();
    });
</script>    