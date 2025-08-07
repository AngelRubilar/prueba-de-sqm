<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("#"), array("id" => "alert_settings_users-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("alert_settings_for_air_quality")." - ".lang("forecasts"); ?></h4>
        </div>
        <div class="panel-body">
			
            <div class="row">
				<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-group" id="accordion_alert_air">
                        
 							
                            <!-- Acordeón Alertas de Pronóstico -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_air_forecast" data-parent="#accordion_alert_air" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("forecast_alerts"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_air_forecast" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("sector");?></th>
                                                        <th><?php echo lang("station")." / ".lang("receptor");?></th>
                                                        <th><?php echo lang("variable");?></th>
                                                        <th><?php echo lang("min_value");?></th>
                                                        <th><?php echo lang("max_value");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_air_forecast_alerts as $air) { ?>
                                                        <tr>
                                                            <td><?php echo $air["name_sector"]; ?></td>
                                                            <td><?php echo $air["name_station"]; ?></td>
                                                            <td><?php echo $air["name_variable"]; ?></td>
                                                            <td id="min_value-<?php echo $air["id_module"]."-".$air["id_submodule"]."-".$air["id_air_station"]."-".$air["id_air_sector"]."-".$air["id_air_variable"]; ?>"><?php echo $air["min_value"]; ?></td>
                                                            <td id="max_value-<?php echo $air["id_module"]."-".$air["id_submodule"]."-".$air["id_air_station"]."-".$air["id_air_sector"]."-".$air["id_air_variable"]; ?>"><?php echo $air["max_value"]; ?></td>
                                                            <td class="text-center" id="configured-<?php echo $air["id_module"]."-".$air["id_submodule"]."-".$air["id_air_station"]."-".$air["id_air_sector"]."-".$air["id_air_variable"]; ?>"><?php echo $air["config_icon"]; ?></td>
                                                            <td class="option" id="action-<?php echo $air["id_module"]."-".$air["id_submodule"]."-".$air["id_air_station"]."-".$air["id_air_sector"]."-".$air["id_air_variable"]; ?>"><?php echo $air["action"]; ?></td>
                                                        </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Alertas de Pronóstico -->
                            

                            <!-- Acordeón Plan de Acción -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_air_action_plan" data-parent="#accordion_alert_air" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("action_plan"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_air_action_plan" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("sector");?></th>
                                                        <th><?php echo lang("station")." / ".lang("receptor");?></th>
                                                        <th><?php echo lang("variable");?></th>
                                                        <th><?php echo lang("n_alerts");?></th>
                                                        <th><?php echo lang("n_action_plans");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_air_action_plan as $aap) { ?>
                                                        <tr>
                                                            <td><?php echo $aap["name_sector"]; ?></td>
                                                            <td><?php echo $aap["name_station"]; ?></td>
                                                            <td><?php echo $aap["name_variable"]; ?></td>
                                                            <td id="n_alerts-<?php echo $aap["id_module"]."-".$aap["id_submodule"]."-".$aap["id_air_station"]."-".$aap["id_air_sector"]."-".$aap["id_air_variable"]; ?>"><?php echo $aap["n_alerts"]; ?></td>
                                                            <td id="n_action_plans-<?php echo $aap["id_module"]."-".$aap["id_submodule"]."-".$aap["id_air_station"]."-".$aap["id_air_sector"]."-".$aap["id_air_variable"]; ?>"><?php echo $aap["n_action_plans"]; ?></td>
                                                            <td class="text-center" id="configured_ap-<?php echo $aap["id_module"]."-".$aap["id_submodule"]."-".$aap["id_air_station"]."-".$aap["id_air_sector"]."-".$aap["id_air_variable"]; ?>"><?php echo $aap["config_icon"]; ?></td>
                                                            <td class="option" id="action_ap-<?php echo $aap["id_module"]."-".$aap["id_submodule"]."-".$aap["id_air_station"]."-".$aap["id_air_sector"]."-".$aap["id_air_variable"]; ?>"><?php echo $aap["action"]; ?></td>
                                                        </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Plan de Acción -->

                        </div>
                    </div>
                    
				</div>
            </div>
            
        </div>
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <!--
            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        	-->
        </div>
    </div>
    <?php echo form_close(); ?>

</div>
<script type="text/javascript">
    $(document).ready(function () {
		$(document).on('click', 'a.accordion-toggle', function () {
						
			$('a.accordion-toggle i').removeClass('fa fa-minus-circle font-16');
			$('a.accordion-toggle i').addClass('fa fa-plus-circle font-16');
			
			var icon = $(this).find('i');
			
			if($(this).hasClass('collapsed')){
				icon.removeClass('fa fa-minus-circle font-16');
				icon.addClass('fa fa-plus-circle font-16');
			} else {
				icon.removeClass('fa fa-plus-circle font-16');
				icon.addClass('fa fa-minus-circle font-16');
			}
	
		});
		
    });
</script>