<div class="list-group" id="alert-popup-list" style="">
    <?php
    $view_data["alerts"] = $alerts;
    $this->load->view("ayn_alerts/list_data", $view_data);
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        //don't apply scrollbar for mobile devices
        if ($(window).width() > 640) {
            if ($('#alert-popup-list').height() >= 400) {
                initScrollbar('#alert-popup-list', {
                    setHeight: 400
                });
            } else {
                $('#alert-popup-list').css({"overflow-y": "auto"});
            }

        }

    });
</script>
