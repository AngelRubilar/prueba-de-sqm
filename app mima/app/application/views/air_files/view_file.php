<div class="app-modal">
    <div class="app-modal-content">
        <?php //$this->load->view("includes/file_preview"); ?>
        
        <?php if ($is_image_file) { ?>
            <img src="<?php echo $file_url; ?>" />
            <?php
        } else if (is_localhost() || !$is_google_preview_available) {
            //don't show google preview in localhost
            echo lang("file_preview_is_not_available") . "<br />";
            echo anchor(get_uri("dashboard/download_file/" . $file_id), lang("download"));
        } else {
            ?>
            <iframe id='google-file-viewer' src="https://drive.google.com/viewerng/viewer?url=<?php echo $file_url; ?>?pid=explorer&efh=false&a=v&chrome=false&embedded=true" style="width: 100%; margin: 0; border: 0;"></iframe>
        
            <script type="text/javascript">
                $(document).ready(function () {
                    $("#google-file-viewer").css({height: $(window).height() + "px"});
                    $(".app-modal .expand").hide();
                });
            </script>
        <?php } ?>

    </div>

    <div class="app-modal-sidebar">
    	
        <div class="row pt10 pb10">
            <div class="col-md-4">
            	Tipo Archivo
            </div>
       		<div class="col-md-8">
            	<?php 
					if($file_info->file_type == "hes"){ $file_type = lang("hes"); }
					echo $file_type;
				?>
            </div>
        </div>
        
        <div class="row pt10 pb10">
            <div class="col-md-4">
            	<?php echo lang("file_name"); ?>
            </div>
       		<div class="col-md-8">
            	<?php echo remove_file_prefix($file_name); ?>
            </div>
        </div>
        
        <div class="row pt10 pb10">
            <div class="col-md-4">
            	<?php echo lang("uploaded_by"); ?>
            </div>
       		<div class="col-md-8">
            	<?php echo $uploaded_by; ?>
            </div>
        </div>
        
    </div>

</div>