<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("customer_administrator_air"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("force_sending_forecast_alert"); ?></a>
    </nav>

<?php if($puede_ver == 1) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('force_sending_forecast_alert'); ?></h1>
        </div>	
    </div>

    <div class="row">
        <div class="col-md-12">

            <ul data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
                <li><a role="presentation" href="#" data-target="#force_send"><?php echo lang('force_send'); ?></a></li>
                <li><a role="presentation" href="<?php echo_uri("Air_force_sending_forecast_alert/tab_email_content"); ?>" data-target="#tab_email_content"><?php echo lang('email_content'); ?></a></li>
            </ul>
            
            <div class="tab-content">

                <div role="tabpanel" class="tab-pane fade in active" id="force_send" style="min-height: 200px;">
                    <?php $this->load->view('air_force_sending_forecast_alert/tab_force_send'); ?>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="tab_email_content" style="min-height: 200px;"></div>

            </div>

        </div>
    </div>

    <!-- <div class="panel">
    	<div class="panel-default">
			<div id="resume_table" class="table-responsive" style="max-height:500px;"></div>
        </div>
    </div> -->
    
</div>

<?php } else { ?><!-- Se aplica la configuración de perfil (ver ninguno) -->

    <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                        <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>

<?php } ?><!-- Fin configuración de perfil (ver todos) -->

<script type="text/javascript">
    $(document).ready(function () {

		
    });
</script>
