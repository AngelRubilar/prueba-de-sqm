<div id="page-content" class="p20 pt0 row">

    <div class="col-sm-3 col-lg-2">
        <?php
        $tab_view['active_tab'] = "general";
        $this->load->view("general_settings/tabs", $tab_view);
        ?>
    </div>
	<div role="tabpanel" class="tab-pane fade active in" id="project-overview-section">
    	<div class="tab-content">
       		<div id="general" class="tab-pane fade in active">
				<?php $this->load->view('general_settings/general')?>
            </div>
            <div id="reports_configuration" class="tab-pane fade">
                <?php $this->load->view('general_settings/reports_configuration')?>
            </div>
            <div id="module_availability" class="tab-pane fade">
                <?php $this->load->view('general_settings/module_availability')?>
            </div>
            <div id="footprint_units" class="tab-pane fade">
                <?php $this->load->view('general_settings/footprint_units')?>
            </div>
            <div id="report_units" class="tab-pane fade">
                <?php $this->load->view('general_settings/report_units')?>
            </div>
            <div id="notification_settings_users" class="tab-pane fade">
                <?php $this->load->view('general_settings/notification_settings_users')?>
            </div>
            <div id="notification_settings_admin" class="tab-pane fade">
                <?php $this->load->view('general_settings/notification_settings_admin')?>
            </div>
            <div id="alert_settings_users" class="tab-pane fade">
                <?php $this->load->view('general_settings/alert_settings_users')?>
            </div>
            <div id="alert_settings_air" class="tab-pane fade">
                <?php $this->load->view('general_settings/alert_settings_air')?>
            </div>
   		</div>
    </div>
    
</div>
<script type="text/javascript">
	$(document).ready(function(){
		
	});
</script>