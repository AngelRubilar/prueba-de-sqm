<div id="page-content" class="p20 pt0 row">

    <div class="col-sm-3 col-lg-2">
        <?php
        $tab_view['active_tab'] = "general";
        $this->load->view("general_settings/tabs_clients", $tab_view);
        ?>
    </div>
	<div role="tabpanel" class="tab-pane fade active in" id="project-overview-section">
    	<div class="tab-content">
        	<div id="general" class="tab-pane fade in active">
				<?php $this->load->view('general_settings/client/general')?>
            </div>
       		<div id="client_module_availability" class="tab-pane fade">
				<?php $this->load->view('general_settings/client/client_module_availability'); ?>
            </div>
            <div id="notification_settings" class="tab-pane fade">
                <?php $this->load->view('general_settings/client/notification_settings')?>
            </div>
            <div id="transformation_factors" class="tab-pane fade">
                <?php $this->load->view('general_settings/client/transformation_factors')?>
            </div>
            <div id="report_units" class="tab-pane fade">
                <?php $this->load->view('general_settings/client/report_units')?>
            </div>
   		</div>
    </div>
    
</div>
<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>