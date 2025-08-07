<script type="text/javascript">
    AppHelper = {};
    AppHelper.baseUrl = "<?php echo base_url(); ?>";
    AppHelper.assetsDirectory = "<?php echo base_url("assets") . "/"; ?>";
    AppHelper.settings = {};
    AppHelper.settings.firstDayOfWeek = <?php echo get_setting("first_day_of_week") * 1; ?> || 0;
    AppHelper.settings.currencySymbol = "<?php echo get_setting("currency_symbol"); ?>";
    AppHelper.settings.currencyPosition = "<?php echo get_setting("currency_position"); ?>" || "left";
	
	AppHelper.settings.decimalSeparator = "<?php echo (get_setting_mimasoft($this->session->project_context, "decimals_separator") == 1)?".":","; ?>";
	AppHelper.settings.thousandSeparator = "<?php echo (get_setting_mimasoft($this->session->project_context, "thousands_separator") == 1)?".":","; ?>";
	AppHelper.settings.decimalNumbers = "<?php echo get_setting_mimasoft($this->session->project_context, "decimal_numbers"); ?>";
    
	AppHelper.settings.decimalSeparatorClient = "<?php echo (get_setting_client_mimasoft($this->login_user->client_id, "decimals_separator") == 1)?".":","; ?>";
	AppHelper.settings.thousandSeparatorClient = "<?php echo (get_setting_client_mimasoft($this->login_user->client_id, "thousands_separator") == 1)?".":","; ?>";
	AppHelper.settings.decimalNumbersClient = "<?php echo get_setting_client_mimasoft($this->login_user->client_id, "decimal_numbers"); ?>";
	
	AppHelper.settings.displayLength = "<?php echo get_setting("rows_per_page"); ?>";
    //AppHelper.settings.timeFormat = "<?php echo get_setting("time_format"); ?>";
	//AppHelper.settings.timeFormat = "<?php echo get_time_format($this->session->project_context); ?>";
	AppHelper.settings.timeFormat = "24_hours";
	AppHelper.settings.dateFormat = "<?php echo get_date_format(NULL, $this->session->project_context, true); ?>";
    AppHelper.settings.scrollbar = "<?php echo get_setting("scrollbar"); ?>";
    AppHelper.userId = "<?php if(isset($this->login_user->id)){echo $this->login_user->id;}; ?>";
	
	AppHelper.context = "<?php echo ($this->session->project_context) ? "project" : "client"; ?>";
	AppHelper.settings.dateFormatClient = "<?php echo get_setting_client_mimasoft($this->login_user->client_id, "date_format"); ?>";
	//AppHelper.highchartsExportUrl = "https://highchart.mimasoft.cl:39095";
	//AppHelper.highchartsExportUrl = "https://highcharts.enel.mimasoft.cl:39095";
	AppHelper.highchartsExportUrl = "https://dev.highcharts.mimasoft.cl:4001";
	AppHelper.highchartsExportUrlQuery = "https://dev.highcharts.mimasoft.cl";
</script>