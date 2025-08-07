<div class="app-modalx">
    <div class="app-modal-contentx">
        <?php //$this->load->view("includes/file_preview"); ?>
        
        <?php if ($is_image_file) { ?>
            <img src="<?php echo $file_url; ?>" />
            <?php
        } else if (is_localhost() || !$is_google_preview_available) {
            //don't show google preview in localhost
            echo lang("file_preview_is_not_available") . "<br />";
            echo anchor($file_url, lang("download"));
        } else {
            ?>
            <!-- <iframe id='google-file-viewer' src="https://drive.google.com/viewerng/viewer?url=<?php echo $file_url; ?>&embedded=true" style="width: 100%; margin: 0; border: 0;"></iframe> -->
            <object data="<?php echo $file_url; ?>#toolbar=0&navpanes=0" type="application/pdf" width="100%" height="500px">
            <p>Unable to display file. <a href="<?php echo $file_url; ?>">Download</a> instead.</p>
            </object>
        
            <script type="text/javascript">
                $(document).ready(function () {
                    $("#google-file-viewer").css({height: $(window).height() + "px"});
                    $(".app-modal .expand").hide();
                });
            </script>
        <?php } ?>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
        </div>
    </div>

</div>