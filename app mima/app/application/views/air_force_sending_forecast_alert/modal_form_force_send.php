<?php echo form_open(get_uri("air_force_sending_forecast_alert/save_force_send"), array("id" => "air_force_sending_forecast_alert-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="form-group">
        <?php echo lang('force_sending_forecast_alert_msj_2'); ?>
    </div>
    <br>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-danger"> <i class='fa fa-envelope'></i> <?php echo lang('force_send'); ?></button>
</div>
<?php echo form_close(); ?>

<style>
#ajaxModal > .modal-dialog {
    width:30% !important;
}
</style>

<script type="text/javascript">
    $(document).ready(function () {

        $("#air_force_sending_forecast_alert-form").appForm({
            onSuccess: function(result) {
                appAlert.success(result.message, {duration: 10000});
                // setTimeout(function() {
                //     location.reload();
                // }, 500);
                // $("#subprojects-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        
    });
</script>