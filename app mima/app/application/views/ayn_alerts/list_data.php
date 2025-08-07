<?php
if (count($alerts)) {
		
    foreach ($alerts as $alert) {		
        $url = "#";
        $ajax_modal_url = "";
        $app_modal_url = "";
        $url_id = "";

        //prepare url
        $info = get_alert_config($alert);
		
		if(!$info["message_type"]){
			continue;
		}
		
        if (is_array($info)) {
			
            /*
			$url = get_array_value($info, "url");
            $ajax_modal_url = get_array_value($info, "ajax_modal_url");
            $app_modal_url = get_array_value($info, "app_modal_url");
            $url_id = get_array_value($info, "id");
			$message = get_array_value($info, "message");
			$message_type = get_array_value($info, "message_type");
			*/
			
			$url_attributes = get_array_value($info, "url_attributes");
			$message = get_array_value($info, "message");
			$message_type = get_array_value($info, "message_type");
			
        }
		
		/*
        if ($ajax_modal_url) {
            $url_attributes = "href='#' data-act='ajax-modal' data-action-url='$ajax_modal_url' data-post-id='$url_id' ";
        } else if ($app_modal_url) {
            $url_attributes = "href='#' data-toggle='app-modal' data-url='$app_modal_url' ";
        } else {
            $url_attributes = "href='$url'";
        }
		*/

        //check read/unread class
        $alert_class = "";
        if (!$alert->viewed) {
            $alert_class = "unread-notification";
        }

        if (!$url || $url == "#") {
            $alert_class.=" not-clickable";
        }
        ?>

        <a class="list-group-item <?php echo $alert_class; ?>" data-alert-id="<?php echo $alert->id; ?>" <?php echo $url_attributes; ?> >
            <div class="media-left">
                <span class="avatar avatar-xs">
                    <?php if($message_type == "caution" || $message_type == "reminder_caution"){ ?>
                    	<i class="fa fa-exclamation-triangle" style="color: #f0ad4e; font-size:25px;"></i>
                    <?php } ?>
                    <?php if($message_type == "alert" || $message_type == "reminder_alert"){ ?>
                        <i class="fa fa-exclamation-circle" style="color: #f06c71; font-size:25px;"></i>
                    <?php } ?>
                </span>
            </div>
            <div class="media-body w100p">
                <div class="media-heading">
                    <strong><?php echo lang($message_type); ?></strong>
                    
                    <?php if($this->session->project_context){ ?>
						<span class="text-off pull-right"><small><?php echo format_to_relative_time_for_projects($alert->alert_date, $this->session->project_context); ?></small></span>
					<?php } else { ?>
                    	<span class="text-off pull-right"><small><?php echo format_to_relative_time_for_clients($alert->alert_date, $alert->id_client); ?></small></span>
                    <?php } ?>
                    
                </div>
                <div class="media m0">
                    <?php echo $message; ?>
                </div>
            </div>
        </a>
        <?php
    }

    if ($result_remaining) {
        $next_container_id = "load" . $next_page_offset;
        ?>
        <div id="<?php echo $next_container_id; ?>">

        </div>

        <div id="loader-<?php echo $next_container_id; ?>" >
            <div class="text-center p20 clearfix mt-5">
                <?php
                echo ajax_anchor(get_uri("alerts/load_more/" . $next_page_offset), lang("load_more"), array("class" => "btn btn-default load-more mt15 p10", "data-remove-on-success" => "#loader-" . $next_container_id, "title" => lang("load_more"), "data-inline-loader" => "1", "data-real-target" => "#" . $next_container_id));
                ?>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <span class="list-group-item"><?php echo lang("no_new_alerts"); ?></span>               
<?php } ?>


<script type="text/javascript">
    $(document).ready(function () {
        $(".unread-notification").click(function (e) {
            $.ajax({
                url: '<?php echo get_uri("AYN_Alert_historical/set_alert_status_as_read") ?>/' + $(this).attr("data-alert-id")
            });
            $(this).removeClass("unread-alert");
        });
    });
</script>