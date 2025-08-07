<?php
if (count($notifications)) {

    foreach ($notifications as $notification) {
		
        $url = "#";
        $ajax_modal_url = "";
        $app_modal_url = "";
        $url_id = "";

        //prepare url
        $info = get_notification_config($notification);
        if (is_array($info)) {
			$url_attributes = get_array_value($info, "url_attributes");
			$message = get_array_value($info, "message");
        }
		
        //check read/unread class
        $notification_class = "";
        if (!$notification->viewed) {
            $notification_class = "unread-notification";
        }

        if (!$url || $url == "#") {
            $notification_class.=" not-clickable";
        }
        ?>

        <a class="list-group-item <?php echo $notification_class; ?>" data-notification-id="<?php echo $notification->id; ?>" <?php echo $url_attributes; ?> >
            <div class="media-left">
                <span class="avatar avatar-xs">
                    <img src="<?php echo get_avatar($notification->id_user ? $notification->user_image : "system_bot"); ?>" alt="..." />
                    <!--  if user name is not present then -->
                </span>
            </div>
            <div class="media-body w100p">
                <div class="media-heading">
                    <strong><?php echo $notification->id_user ? $notification->user_name : get_setting("app_title"); ?></strong>
                    
                    <?php if($this->session->project_context){ ?>
						<span class="text-off pull-right"><small><?php echo format_to_relative_time_for_projects($notification->notified_date, $this->session->project_context); ?></small></span>
					<?php } else { ?>
                    	<span class="text-off pull-right"><small><?php echo format_to_relative_time_for_clients($notification->notified_date, $notification->id_client); ?></small></span>
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
                echo ajax_anchor(get_uri("notifications/load_more/" . $next_page_offset), lang("load_more"), array("class" => "btn btn-default load-more mt15 p10", "data-remove-on-success" => "#loader-" . $next_container_id, "title" => lang("load_more"), "data-inline-loader" => "1", "data-real-target" => "#" . $next_container_id));
                ?>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <span class="list-group-item"><?php echo lang("no_new_notifications"); ?></span>               
<?php } ?>


<script type="text/javascript">
    $(document).ready(function () {
        $(".unread-notification").click(function (e) {
            $.ajax({
                url: '<?php echo get_uri("AYN_Notif_historical/set_notification_status_as_read") ?>/' + $(this).attr("data-notification-id")
            });
            $(this).removeClass("unread-notification");
        });
    });
</script>