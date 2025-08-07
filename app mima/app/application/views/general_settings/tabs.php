<ul class="nav nav-tabs vertical" role="tablist">
    <li role="presentation" class="<?php echo ($active_tab == 'general') ? 'active' : ''; ?>"><a data-toggle="tab" href="#general"><?php echo lang("general"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'reports_configuration') ? 'active' : ''; ?>"><a data-toggle="tab" href="#reports_configuration"><?php echo lang("reports_configuration"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'module_availability') ? 'active' : ''; ?>"><a data-toggle="tab" href="#module_availability"><?php echo lang("module_availability"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'footprint_units') ? 'active' : ''; ?>"><a data-toggle="tab" href="#footprint_units"><?php echo lang("footprint_units"); ?></a></li>
	<li role="presentation" class="<?php echo ($active_tab == 'report_units') ? 'active' : ''; ?>"><a data-toggle="tab" href="#report_units"><?php echo lang("report_units"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'notification_settings_users') ? 'active' : ''; ?>"><a data-toggle="tab" href="#notification_settings_users"><?php echo lang("notification_settings_users"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'notification_settings_admin') ? 'active' : ''; ?>"><a data-toggle="tab" href="#notification_settings_admin"><?php echo lang("notification_settings_admin"); ?></a></li>
	<li role="presentation" class="<?php echo ($active_tab == 'alert_settings_users') ? 'active' : ''; ?>"><a data-toggle="tab" href="#alert_settings_users"><?php echo lang("alert_settings"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'alert_settings_air') ? 'active' : ''; ?>"><a data-toggle="tab" href="#alert_settings_air"><?php echo lang("alert_settings_for_air_quality")." - ".lang("forecasts"); ?></a></li>
</ul>