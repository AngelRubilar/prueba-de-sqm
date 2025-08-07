<div class="panel panel-default mb15">
    <div class="page-title clearfix">
        <h1><?php echo lang('force_send'); ?></h1>
    </div>
    <div class="panel-body">
        <?php echo lang('force_sending_forecast_alert_msj'); ?>
    </div>
    <div class="panel-footer clearfix">
        <?php echo modal_anchor(get_uri("air_force_sending_forecast_alert/modal_form_force_send/"), "<i class='fa fa-envelope'></i> " . lang('force_send'), array("id" => "force_send", "class" => "btn btn-danger pull-right", "title" => lang('force_send'), "data-post-flujo" => $record_info->flujo)); ?>
    </div>
</div>