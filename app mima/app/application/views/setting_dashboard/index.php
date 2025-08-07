<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("customer_administrator"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("setting_dashboard"); ?></a>
    </nav>

	<?php if($puede_ver == 1) { ?>

        <div id="page-content" class="p20 pt0 row">
        
            <div class="col-sm-3 col-lg-2">
                <?php
                $tab_view['active_tab'] = "environmental_footprints";
                $this->load->view("setting_dashboard/tabs", $tab_view);
                ?>
            </div>
            <div role="tabpanel" class="tab-pane fade active in" id="project-overview-section">
                <div class="tab-content">
                    <div id="environmental_footprints" class="tab-pane fade in active">
                        <?php $this->load->view('setting_dashboard/environmental_footprints')?>
                    </div>
                    <div id="consumptions" class="tab-pane fade">
                        <?php $this->load->view('setting_dashboard/consumptions')?>
                    </div>
                    <div id="waste" class="tab-pane fade">
                        <?php $this->load->view('setting_dashboard/waste')?>
                    </div>
					<div id="compromises" class="tab-pane fade">
                        <?php $this->load->view('setting_dashboard/compromises')?>
                    </div>
                    <div id="permittings" class="tab-pane fade">
                        <?php $this->load->view('setting_dashboard/permittings')?>
                    </div>
                </div>
            </div>
            
        </div>

	<?php } else {?>
    
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
        
    <?php } ?>
        
</div>
<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>