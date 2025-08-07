<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("#"), array("id" => "alert_settings_users-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("alert_settings"); ?></h4>
        </div>
        <div class="panel-body">
			
            <div class="row">
				<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-group" id="accordion3">
                        
 							<!-- Acordeón Registros -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_users_1" data-parent="#accordion3" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("environmental_records"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_users_1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("ayn_item");?></th>
                                                        <th><?php echo lang("risk");?></th>
                                                        <th><?php echo lang("threshold");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_ra as $ra) { ?>
                                                        <tr>
                                                            <td><?php echo $ra["nombre_item"]; ?></td>
                                                            <td id="risk_value-<?php echo $ra["id_module"]."-".$ra["id_submodule"]."-".$ra["id_categoria"]."-".$ra["id_tipo_unidad"]; ?>"><?php echo $ra["risk_value"]; ?></td>
                                                            <td id="threshold_value-<?php echo $ra["id_module"]."-".$ra["id_submodule"]."-".$ra["id_categoria"]."-".$ra["id_tipo_unidad"]; ?>"><?php echo $ra["threshold_value"]; ?></td>
                                                            <td class="text-center" id="configured-<?php echo $ra["id_module"]."-".$ra["id_submodule"]."-".$ra["id_categoria"]."-".$ra["id_tipo_unidad"]; ?>"><?php echo $ra["setting_icon"]; ?></td>
                                                            <td class="option" id="action-<?php echo $ra["id_module"]."-".$ra["id_submodule"]."-".$ra["id_categoria"]."-".$ra["id_tipo_unidad"]; ?>"><?php echo $ra["action"]; ?></td>
                                                        </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Registros -->
                            
                         	
                            <!-- Acordeón Compromisos RCA -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_users_2" data-parent="#accordion3" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("compromises_rca"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_users_2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("ayn_item");?></th>
                                                        <th class="text-center"><?php echo lang("risk");?></th>
                                                        <th class="text-center"><?php echo lang("threshold");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $array_comp_rca["nombre_item"]; ?></td>
                                                        <td class="text-center" id="risk_value-<?php echo $array_comp_rca["id_module"]."-".$array_comp_rca["id_submodule"]."-".$array_comp_rca["tipo_evaluacion"]; ?>"><?php echo $array_comp_rca["risk_value"]; ?></td>
                                                        <td class="text-center" id="threshold_value-<?php echo $array_comp_rca["id_module"]."-".$array_comp_rca["id_submodule"]."-".$array_comp_rca["tipo_evaluacion"]; ?>"><?php echo $array_comp_rca["threshold_value"]; ?></td>
                                                        <td class="text-center" id="configured-<?php echo $array_comp_rca["id_module"]."-".$array_comp_rca["id_submodule"]."-".$array_comp_rca["tipo_evaluacion"]; ?>"><?php echo $array_comp_rca["setting_icon"]; ?></td>
                                                        <td class="option" id="action-<?php echo $array_comp_rca["id_module"]."-".$array_comp_rca["id_submodule"]."-".$array_comp_rca["tipo_evaluacion"]; ?>" ><?php echo $array_comp_rca["action"]; ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Compromisos RCA -->
                            
                            <!-- Acordeón Compromisos Reportables -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_users_3" data-parent="#accordion3" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("compromises_rep"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_users_3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("ayn_item");?></th>
                                                        <th class="text-center"><?php echo lang("risk");?></th>
                                                        <th class="text-center"><?php echo lang("threshold");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $array_comp_rep["nombre_item"]; ?></td>
                                                        <td class="text-center" id="risk_value-<?php echo $array_comp_rep["id_module"]."-".$array_comp_rep["id_submodule"]."-".$array_comp_rep["tipo_evaluacion"]; ?>"><?php echo $array_comp_rep["risk_value"]; ?></td>
                                                        <td class="text-center" id="threshold_value-<?php echo $array_comp_rep["id_module"]."-".$array_comp_rep["id_submodule"]."-".$array_comp_rep["tipo_evaluacion"]; ?>"><?php echo $array_comp_rep["threshold_value"]; ?></td>
                                                        <td class="text-center" id="configured-<?php echo $array_comp_rep["id_module"]."-".$array_comp_rep["id_submodule"]."-".$array_comp_rep["tipo_evaluacion"]; ?>"><?php echo $array_comp_rep["setting_icon"]; ?></td>
                                                        <td class="option" id="action-<?php echo $array_comp_rep["id_module"]."-".$array_comp_rep["id_submodule"]."-".$array_comp_rep["tipo_evaluacion"]; ?>"><?php echo $array_comp_rep["action"]; ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Compromisos Reportables -->
                            
                             <!-- Acordeón Planificacion Reportables -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_users_4" data-parent="#accordion3" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("compromises")." - ".lang("reportable_planning"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_users_4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("ayn_item");?></th>
                                                        <th><?php echo lang("risk");?></th>
                                                        <th><?php echo lang("threshold");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_planificaciones_comp_rep as $planificacion_comp_rep) { ?>
                                                        <tr>
                                                            <td><?php echo $planificacion_comp_rep["nombre_item"]; ?></td>
                                                            <td id="risk_value-<?php echo $planificacion_comp_rep["id_module"]."-".$planificacion_comp_rep["id_submodule"]."-".$planificacion_comp_rep["id_planificacion"]."-planification"; ?>"><?php echo $planificacion_comp_rep["risk_value"]; ?></td>
                                                            <td id="threshold_value-<?php echo $planificacion_comp_rep["id_module"]."-".$planificacion_comp_rep["id_submodule"]."-".$planificacion_comp_rep["id_planificacion"]."-planification"; ?>"><?php echo $planificacion_comp_rep["threshold_value"]; ?></td>
                                                            <td class="text-center" id="configured-<?php echo $planificacion_comp_rep["id_module"]."-".$planificacion_comp_rep["id_submodule"]."-".$planificacion_comp_rep["id_planificacion"]."-planification"; ?>"><?php echo $planificacion_comp_rep["setting_icon"]; ?></td>
                                                            <td class="option" id="action-<?php echo $planificacion_comp_rep["id_module"]."-".$planificacion_comp_rep["id_submodule"]."-".$planificacion_comp_rep["id_planificacion"]."-planification"; ?>" ><?php echo $planificacion_comp_rep["action"]; ?></td>
                                                        </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Planificacion Reportables -->
                            
                            <!-- Acordeón Permisos -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_alert_users_5" data-parent="#accordion3" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                            	<i class="fa fa-plus-circle font-16"></i> <?php echo lang("permittings"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_alert_users_5" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("ayn_item");?></th>
                                                        <th class="text-center"><?php echo lang("risk");?></th>
                                                        <th class="text-center"><?php echo lang("threshold");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th><?php echo lang("action");?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $array_valores_permisos["nombre_item"]; ?></td>
                                                        <td class="text-center" id="risk_value-<?php echo $array_valores_permisos["id_module"]."-".$array_valores_permisos["id_submodule"]."-permitting"; ?>"><?php echo $array_valores_permisos["risk_value"]; ?></td>
                                                        <td class="text-center"id="threshold_value-<?php echo $array_valores_permisos["id_module"]."-".$array_valores_permisos["id_submodule"]."-permitting"; ?>"><?php echo $array_valores_permisos["threshold_value"]; ?></td>
                                                        <td class="text-center" id="configured-<?php echo $array_valores_permisos["id_module"]."-".$array_valores_permisos["id_submodule"]."-permitting"; ?>"><?php echo $array_valores_permisos["setting_icon"]; ?></td>
                                                        <td class="option" id="action-<?php echo $array_valores_permisos["id_module"]."-".$array_valores_permisos["id_submodule"]."-permitting"; ?>"><?php echo $array_valores_permisos["action"]; ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Permisos -->

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