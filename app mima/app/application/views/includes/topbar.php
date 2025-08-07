<?php
$bar_color = "";
if($this->login_user->user_type === "client"){
	$client_info = $this->Clients_model->get_one($this->login_user->client_id);
	if($client_info->id){
		if($client_info->color_sitio){
			$bar_color = ' style="background-color: '.$client_info->color_sitio.';';
		}
	}
}
?>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation" <?php echo $bar_color; ?>>
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="fa fa-chevron-down"></span>
        </button>
        <button id="sidebar-toggle" type="button" class="navbar-toggle" data-target="#sidebar" style="background-color:#FFF;">
            <span class="sr-only">Toggle navigation</span>
            <span class="fa fa-bars"></span>
        </button>
        <?php
        $logo = $this->session->logo ? get_file_uri("files/mimasoft_files/client_".$this->login_user->client_id."/".$this->session->logo.".png"): get_file_uri("files/system/default-site-logo.png");
		?>
        <!--<a class="navbar-brand" href="<?php echo_uri('dashboard'); ?>"><img src="<?php echo $logo; ?>" /></a>-->
        <a class="navbar-brand" href="<?php echo_uri('home'); ?>"><img src="<?php echo $logo; ?>" style="max-height: 40px;"/></a>

    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-left">
            <li class="hidden-xs pl15 pr15  b-l">
                <button class="hidden-xs" id="sidebar-toggle-md">
                    <span class="fa fa-dedent"></span>
                </button>
            </li>


        </ul>
        <ul class="nav navbar-nav navbar-right">
        
        	<?php if($this->login_user->user_type == "client" && $this->session->project_context){ ?>
        	<li class="">
                <?php //echo anchor("inicio_projects", "<i class='fa fa-arrow-up'></i>", array("id" => "web-notification-icon")); ?>
            </li>
        	<?php } ?>
        	
            <!-- ÍCONO DE NOTIFICACIONES WEB (AYN) -->
            <!--
        	<?php if($this->login_user->user_type === "client"){ ?>
                <li class="">
                    <?php echo js_anchor("<i class='fa fa-bell-o'></i>", array("id" => "web-notification-icon", "class" => "dropdown-toggle", "data-toggle" => "dropdown")); ?>
                    <div class="dropdown-menu aside-xl m0 p0 font-100p" style="min-width: 400px;" >
                        <div class="dropdown-details panel bg-white m0">
                            <div class="list-group">
                                <span class="list-group-item inline-loader p10"></span>                          
                            </div>
                        </div>
                        <div class="panel-footer text-sm text-center">
                            <?php echo anchor("AYN_Notif_historical", lang('see_all')); ?>
                        </div>
                    </div>
                </li>
			<?php } ?>
            -->
            
            <!-- ÍCONO DE ALERTAS WEB (AYN) -->
        	<!--
            <?php if($this->login_user->user_type === "client"){ ?>
                <li class="">
                    <?php echo js_anchor("<i class='fa fa-exclamation-triangle'></i>", array("id" => "web-alert-icon", "class" => "dropdown-toggle", "data-toggle" => "dropdown")); ?>
                    <div class="dropdown-menu aside-xl m0 p0 font-100p" style="min-width: 400px;" >
                        <div class="dropdown-details panel bg-white m0">
                            <div class="list-group">
                                <span class="list-group-item inline-loader p10"></span>                          
                            </div>
                        </div>
                        <div class="panel-footer text-sm text-center">
                            <?php echo anchor("AYN_Alert_historical", lang('see_all')); ?>
                        </div>
                    </div>
                </li>
			<?php } ?>
            -->
            
            <?php $site_color = $client_info->color_sitio;
				$font_color = '#000000';
				if($site_color == '#000000'){
					$font_color = '#ffffff';
				}else if($site_color == '#ffffff'){
					$font_color = '#000000';
				}else if($site_color == '#FF0000'){
					$font_color = '#ffffff';
				}else if($site_color == '#777777'){
					$font_color = '#ffffff';
				}else if($site_color == '#337ab7'){
					$font_color = '#ffffff';
				}else if($site_color == '#5cb85c'){
					$font_color = '#ffffff';
				}else if($site_color == '#5bc0de'){
					$font_color = '#ffffff';
				}else if($site_color == '#f0ad4e'){
					$font_color = '#ffffff';
				}else if($site_color == '#d9534f'){
					$font_color = '#ffffff';
				}else if($site_color == '#8a6d3b'){
					$font_color = '#ffffff';
				}else if($site_color == '#00b393'){
					$font_color = '#ffffff';
				}
			?>
            
            <li class="dropdown pr15 dropdown-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    <span class="avatar-xs avatar pull-left mt-5 mr10" >
                        <img alt="..." src="<?php echo get_avatar($this->login_user->image); ?>">
                    </span> <span style="color:<?php echo $font_color; ?>"><?php echo $this->login_user->first_name . " " . $this->login_user->last_name; ?> <span class="caret"></span> </span></a>
                <ul class="dropdown-menu p0" role="menu">
                    <?php if ($this->login_user->user_type == "client") { ?>
                        <li><?php echo get_client_contact_profile_link($this->login_user->id . '/general', "<i class='fa fa-user mr10'></i>" . lang('my_profile')); ?></li>
                        <li><?php echo get_client_contact_profile_link($this->login_user->id . '/account', "<i class='fa fa-key mr10'></i>" . lang('change_password')); ?></li>
                    <?php } else { ?>
                        <li><?php echo get_team_member_profile_link($this->login_user->id . '/general', "<i class='fa fa-user mr10'></i>" . lang('my_profile')); ?></li>
                        <li><?php echo get_team_member_profile_link($this->login_user->id . '/account', "<i class='fa fa-key mr10'></i>" . lang('change_password')); ?></li>
                    <?php } ?>
                    <li class="divider"></li>
                    <li><a href="<?php echo_uri('signin/sign_out'); ?>"><i class="fa fa-power-off mr10"></i> <?php echo lang('sign_out'); ?></a></li>
                </ul>
            </li>
        </ul>
    </div><!--/.nav-collapse -->
</nav>

<script type="text/javascript">
    $(document).ready(function () {
		
        <?php if($this->login_user->user_type === "client"){ ?>
			
			// var	notificationOptions = {},
			// 	$notificationIcon = $("#web-notification-icon"),
			// 	alertOptions = {},
			// 	$alertIcon = $("#web-alert-icon"),
			// 	cronOptions = {};
			
			// // Notificaciones
			// notificationOptions.notificationUrl = "<?php echo_uri('AYN_Notif_historical/count_notifications'); ?>";
			// notificationOptions.notificationStatusUpdateUrl = "<?php echo_uri('AYN_Notif_historical/update_notification_checking_status'); ?>";
			// notificationOptions.checkNotificationAfterEvery = "<?php echo get_setting('check_notification_after_every'); ?>";
			// notificationOptions.icon = "fa-bell-o";
			// notificationOptions.notificationSelector = $notificationIcon;
	
			// checkNotifications(notificationOptions);
	
			// $notificationIcon.click(function () {
			// 	notificationOptions.notificationUrl = "<?php echo_uri('AYN_Notif_historical/get_notifications'); ?>";
			// 	checkNotifications(notificationOptions, true);
			// 	notificationOptions.notificationUrl = "<?php echo_uri('AYN_Notif_historical/count_notifications'); ?>";
			// });
			

			// // Alertas
			// alertOptions.alertUrl = "<?php echo_uri('AYN_Alert_historical/count_alerts'); ?>";
			// alertOptions.alertStatusUpdateUrl = "<?php echo_uri('AYN_Alert_historical/update_alert_checking_status'); ?>";
			// alertOptions.checkAlertAfterEvery = "<?php echo get_setting('check_alert_after_every'); ?>";
			// alertOptions.icon = "fa fa-exclamation-triangle";
			// alertOptions.alertSelector = $alertIcon;
						
			// checkAlerts(alertOptions);
			
			// $alertIcon.click(function () {
			// 	alertOptions.alertUrl = "<?php echo_uri('AYN_Alert_historical/get_alerts'); ?>";
			// 	checkAlerts(alertOptions, true);
			// 	alertOptions.alertUrl = "<?php echo_uri('AYN_Alert_historical/count_alerts'); ?>";
			// });
			
			// // Cron
			// cronOptions.executeCronAfterEvery = "<?php echo get_setting('check_notification_after_every'); ?>";
			// executeCron(cronOptions);
			
		<?php } ?>
		
    });
</script>
