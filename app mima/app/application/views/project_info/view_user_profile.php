<?php $this->load->view("includes/cropbox"); ?>
<div id="page-content" class="clearfix">
    <div class="row bg-primary p20">
        <div class="col-md-6">
        
           <!-- profile_image_section -->
            <div class="box">
            
                <div class="box-content w200 text-center profile-image">
                    <span class="avatar avatar-lg"><img id="profile-image-preview" src="<?php echo get_avatar($user_info->image); ?>" alt="..."></span> 
                    <h4 class=""><?php echo $user_info->first_name . " " . $user_info->last_name; ?></h4>
                    <?php echo form_close(); ?>
                </div> 
            
            
                <div class="box-content pl15">
                </div>
                
            </div>
           <!-- profile_image_section -->
        </div>
    </div>


    <ul data-toggle="ajax-tab" class="nav nav-tabs" role="tablist">
        <li style="display:none;"><a  role="presentation" href="<?php echo_uri("project_info/contact_general_info_tab/" . $user_info->id); ?>" data-target="#tab-general-info"> <?php echo lang('general_info'); ?></a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="tab-general-info"></div>
    </div>
</div>
