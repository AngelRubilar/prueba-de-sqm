<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("#"), array("id" => "notification_settings_users-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("notification_settings_users"); ?></h4>
        </div>
        <div class="panel-body">
			
            <div class="row">
				<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-group" id="accordion2">
                        
 							<!-- Acordeón Registros -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse1" data-parent="#accordion2" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("records"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th class="text-center"><?php echo lang("add");?></th>
                                                        <th class="text-center"><?php echo lang("edit");?></th>
                                                        <th class="text-center"><?php echo lang("delete");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_registros_proyecto as $index => $registros_proyecto) { ?>
                                                    	                                                        
                                                        <tr>
                                                            <td>
                                                                <?php 
                                                                    echo $registros_proyecto["module"];
                                                                    echo ($registros_proyecto["submodule"]) ? " | ".$registros_proyecto["submodule"] : "";
                                                                ?>
                                                            </td>

                                                            <?php if($registros_proyecto["id_module"] == "2"){ // Registros Ambientales ?>
                                                                <td id="add-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_environmental_records_icons["add"]; ?></td>
                                                                <td id="edit-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_environmental_records_icons["edit"]; ?></td>
                                                                <td id="delete-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_environmental_records_icons["delete"]; ?></td>
                                                                <td class="option text-center" id="action-<?php echo $registros_proyecto["item"]; ?>" ><?php echo $events_environmental_records_btn; ?></td>
                                                            <?php } ?>
                                                            
                                                            <?php if($registros_proyecto["id_module"] == "3"){ // Mantenedoraos ?>
																<td id="add-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_feeders_icons["add"]; ?></td>
                                                                <td id="edit-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_feeders_icons["edit"]; ?></td>
                                                                <td id="delete-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_feeders_icons["delete"]; ?></td>
                                                                <td class="option text-center" id="action-<?php echo $registros_proyecto["item"]; ?>" ><?php echo $events_feeders_btn; ?></td>
															<?php } ?>
                                                            
                                                            <?php if($registros_proyecto["id_module"] == "4"){ // Otros Registros ?>
                                                                <td id="add-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_other_records_icons["add"]; ?></td>
                                                                <td id="edit-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_other_records_icons["edit"]; ?></td>
                                                                <td id="delete-<?php echo $registros_proyecto["item"]; ?>" class="text-center"><?php echo $events_other_records_icons["delete"]; ?></td>
                                                                <td class="option text-center" id="action-<?php echo $registros_proyecto["item"]; ?>" ><?php echo $events_other_records_btn; ?></td>
															<?php } ?>

                                                        </tr>
                                                    
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Registros -->
                            
                            <!-- Acordeón Compromisos -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse2" data-parent="#accordion2" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("compromises"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th class="text-center"><?php echo lang("add");?></th>
                                                        <th class="text-center"><?php echo lang("edit");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_compromisos as $index => $compromisos) { ?>
                                                    
                                                        <tr>
                                                            <td><?php echo $compromisos["submodule"]; ?></td>
                                                            
                                                            <?php if($compromisos["id_module"] == "6"){ // Compromisos ?>

																<?php if($compromisos["id_submodule"] == "4"){ // Evaluación de Compromisos RCA ?>
                                                                    <td id="add-<?php echo $compromisos["item"]; ?>" class="text-center"><?php echo $events_compromises_rca_icons["add"]; ?></td>
                                                                    <td id="edit-<?php echo $compromisos["item"]; ?>" class="text-center"><?php echo $events_compromises_rca_icons["edit"]; ?></td>
                                                                    <td class="option text-center" id="action-<?php echo $compromisos["item"]; ?>" ><?php echo $events_compromises_rca_btn; ?></td>
                                                                <?php } ?>
                                                                
                                                                <?php if($compromisos["id_submodule"] == "22"){ // Evaluación de Compromisos Reportables ?>
                                                                    <td class="text-center">-</td>
                                                                    <td id="edit-<?php echo $compromisos["item"]; ?>" class="text-center"><?php echo $events_compromises_rep_icons["edit"]; ?></td>
                                                                    <td class="option text-center" id="action-<?php echo $compromisos["item"]; ?>" ><?php echo $events_compromises_rep_btn; ?></td>
                                                                <?php } ?>
                                                            
                                                            <?php } ?>

                                                        </tr>
                                                    
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Compromisos -->
                            
                            <!-- Acordeón Permisos -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse3" data-parent="#accordion2" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("permittings"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th class="text-center"><?php echo lang("add");?></th>
                                                        <th class="text-center"><?php echo lang("edit");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_permisos as $index => $permisos) { ?>
                                                    
                                                        <tr>
                                                            <td><?php echo $permisos["submodule"]; ?></td>
                                                            
                                                            <?php if($permisos["id_module"] == "7"){ // Permisos ?>
                                                                <td id="add-<?php echo $permisos["item"]; ?>" class="text-center"><?php echo $events_permittings_icons["add"]; ?></td>
                                                                <td id="edit-<?php echo $permisos["item"]; ?>" class="text-center"><?php echo $events_permittings_icons["edit"]; ?></td>
                                                                <td class="option text-center" id="action-<?php echo $permisos["item"]; ?>" ><?php echo $events_permittings_btn; ?></td>
                                                            <?php } ?>

                                                        </tr>
                                                    
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Permisos -->
                            
                            <!-- Acordeón Administración Cliente -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse4" data-parent="#accordion2" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("customer_administrator"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th class="text-center"><?php echo lang("edit");?></th>
                                                        <th class="text-center"><?php echo lang("bulk_load");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_administracion_cliente as $index => $administracion_cliente) { ?>
                                                    
                                                        <tr>
                                                            <td><?php echo $administracion_cliente["submodule"]; ?></td>
                                                            
                                                            <?php if($administracion_cliente["id_module"] == "11"){ // Administración Cliente ?>
                                                            
                                                            	<?php if($administracion_cliente["id_submodule"] == "20"){ // Configuración Panel Principal ?>
                                                            
                                                                    <td id="edit-<?php echo $administracion_cliente["item"]; ?>" class="text-center"><?php echo $events_setting_dashboard_icons["edit"]; ?></td>
                                                                    <td class="text-center">-</td>
                                                                    <td class="option text-center" id="action-<?php echo $administracion_cliente["item"]; ?>" ><?php echo $events_setting_dashboard_btn; ?></td>
                                                                    
                                                                <?php } ?>
                                                                
                                                                <?php if($administracion_cliente["id_submodule"] == "21"){ // Carga Masiva ?>
                                                            		
                                                                    <td class="text-center">-</td>
                                                                    <td id="bulk_load-<?php echo $administracion_cliente["item"]; ?>" class="text-center"><?php echo $events_bulk_load_icons["bulk_load"]; ?></td>
                                                                    <td class="option text-center" id="action-<?php echo $administracion_cliente["item"]; ?>" ><?php echo $events_bulk_load_btn; ?></td>
                                                                    
                                                                <?php } ?>
                                                                
                                                            <?php } ?>

                                                        </tr>
                                                    
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Administración Cliente -->

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