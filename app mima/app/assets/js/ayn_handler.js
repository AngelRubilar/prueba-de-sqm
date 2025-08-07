checkNotifications = function (params, updateStatus) {
    if (params && params.notificationUrl) {
        $.ajax({
            url: params.notificationUrl,
            dataType: 'json',
            success: function (result) {
				
                if (result.success) {
                    if (result.total_notifications && result.total_notifications * 1) {
                        params.notificationSelector.html("<i class='fa " + params.icon + "'></i> <span class='badge bg-danger up'>" + result.total_notifications + "</span>");
                    
					
					}

                    params.notificationSelector.parent().find(".dropdown-details").html(result.notification_list);

                    if (updateStatus) {
                        //update last notification checking time
                        $.ajax({
                            url: params.notificationStatusUpdateUrl,
                            success: function () {
                                params.notificationSelector.html("<i class='fa " + params.icon + "'></i>");
                            }
                        });
                    }
                }
                if (!updateStatus) {
                    //check notification again after sometime
                    var check_notification_after_every = params.checkNotificationAfterEvery;
                    check_notification_after_every = check_notification_after_every * 1000;
                    if (check_notification_after_every < 10000) {
                        check_notification_after_every = 10000; //don't allow to call this requiest before 10 seconds
                    }

                    setTimeout(function () {
                        checkNotifications(params);
                    }, check_notification_after_every);
                }
            }
        });
    }
};

checkAlerts = function (params, updateStatus) {
		
    if (params && params.alertUrl) {
		
        $.ajax({
            url: params.alertUrl,
            dataType: 'json',
            success: function (result) {
				
                if (result.success) {
                    if (result.total_alerts && result.total_alerts * 1) {
                        params.alertSelector.html("<i class='fa " + params.icon + "'></i> <span class='badge bg-danger up'>" + result.total_alerts + "</span>");
					}

                    params.alertSelector.parent().find(".dropdown-details").html(result.alert_list);
					
                    if (updateStatus) {
                        $.ajax({
                            url: params.alertStatusUpdateUrl,
                            success: function () {
                                params.alertSelector.html("<i class='fa " + params.icon + "'></i>");
                            }
                        });
                    }
                }
                if (!updateStatus) {

				var check_alert_after_every = params.checkAlertAfterEvery;
                    check_alert_after_every = check_alert_after_every * 1000;
                    if (check_alert_after_every < 10000) {
                        check_alert_after_every = 10000;
                    }

                    setTimeout(function () {
                        checkAlerts(params);
                    }, check_alert_after_every);
                }
            }
        });
    }
};

executeCron = function (params) {
	
	if(params){
		
		$.post("/cron");
		
		var execute_cron_after_every = params.executeCronAfterEvery;
		execute_cron_after_every = execute_cron_after_every * 1000;
		if (execute_cron_after_every < 10000) {
			execute_cron_after_every = 10000;
		}
		
		setTimeout(function () {
			executeCron(params);
		}, execute_cron_after_every);

	}
	
}