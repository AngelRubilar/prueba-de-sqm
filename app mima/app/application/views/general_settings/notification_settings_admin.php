<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("#"), array("id" => "notification_settings_admin-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("notification_settings_admin"); ?></h4>
        </div>
        <div class="panel-body">
			
            <div class="row">
				<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-group" id="accordion_notif_admin">
                        
 							<!-- Acordeón Proyectos -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_notif_admin_1" data-parent="#accordion_notif_admin" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("projects"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_notif_admin_1" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th><?php echo lang("event");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_proyectos as $index => $proyecto) { ?>
                                                    <?php foreach($proyecto["events"] as $event => $action) { ?>
                                                        <tr>
                                                            <td>
                                                                <?php  echo $proyecto["module"]; ?>
                                                            </td>
                                                            <td><?php echo lang($event); ?></td>
                                                            <td class="text-center" id="configured-<?php echo $proyecto["id_module"]."-".$proyecto["id_submodule"]."-".$event; ?>"><?php echo $action[0]; ?></td>
                                                            <td class="option text-center" id="action-<?php echo $proyecto["id_module"]."-".$proyecto["id_submodule"]."-".$event; ?>"><?php echo $action[1]; ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Proyectos -->
                            

                            <!-- Acordeón Registros -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_notif_admin_2" data-parent="#accordion_notif_admin" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("records"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_notif_admin_2" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th class="text-center"><?php echo lang("form_add");?></th>
                                                        <th class="text-center"><?php echo lang("form_edit_name");?></th>
                                                        <th class="text-center"><?php echo lang("form_edit_cat");?></th>
                                                        <th class="text-center"><?php echo lang("form_delete");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_registros as $index => $registros) { ?>
                                                    	                                                        
                                                        <tr>
                                                            <td> <?php echo $registros["submodule"]; ?></td>
                                                            <?php if($registros["id_module"] == "5"){ // Registros ?>
                                                                <td id="form_add-<?php echo $registros["item"]; ?>" class="text-center"><?php echo $events_records_icons["form_add"]; ?></td>
                                                                <td id="form_edit_name-<?php echo $registros["item"]; ?>" class="text-center"><?php echo $events_records_icons["form_edit_name"]; ?></td>
                                                                <td id="form_edit_cat-<?php echo $registros["item"]; ?>" class="text-center"><?php echo $events_records_icons["form_edit_cat"]; ?></td>
                                                                <td id="form_delete-<?php echo $registros["item"]; ?>" class="text-center"><?php echo $events_records_icons["form_delete"]; ?></td>
                                                                <td class="option text-center" id="action-<?php echo $registros["item"]; ?>" ><?php echo $events_records_btn; ?></td>
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

                            <!-- Acordeón Indicadores -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_notif_admin_3" data-parent="#accordion_notif_admin" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("indicators"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_notif_admin_3" class="panel-collapse collapse">
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
                                                <?php foreach($array_indicadores as $index => $indicador) { ?>
                                                    	                                                        
                                                        <tr>
                                                            <td><?php echo ($indicador["submodule"]); ?></td>

                                                            <?php if($indicador["id_module"] == "7"){ // Indicadores ?>
                                                                <td id="uf_add_element-<?php echo $indicador["item"]; ?>" class="text-center"><?php echo $events_indicators_icons["uf_add_element"]; ?></td>
                                                                <td id="uf_edit_element-<?php echo $indicador["item"]; ?>" class="text-center"><?php echo $events_indicators_icons["uf_edit_element"]; ?></td>
                                                                <td id="uf_delete_element-<?php echo $indicador["item"]; ?>" class="text-center"><?php echo $events_indicators_icons["uf_delete_element"]; ?></td>
                                                                <td class="option text-center" id="action-<?php echo $indicador["item"]; ?>" ><?php echo $events_indicators_btn; ?></td>
                                                            <?php } ?>
                                                            
                                                        </tr>
                                                    
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Indicadores -->
                            
                            <!-- Acordeón Compromisos -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_notif_admin_4" data-parent="#accordion_notif_admin" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("compromises"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_notif_admin_4" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th><?php echo lang("event");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_compromisos_admin as $index => $compromiso) { ?>
                                                    <?php foreach($compromiso["events"] as $event => $action) { ?>
                                                        <tr>
                                                            <td>
                                                                <?php  echo $compromiso["submodule"]; ?>
                                                            </td>
                                                            <td><?php echo lang($event); ?></td>
                                                            <td class="text-center" id="configured-<?php echo $compromiso["id_module"]."-".$compromiso["id_submodule"]."-".$event; ?>"><?php echo $action[0]; ?></td>
                                                            <td class="option text-center" id="action-<?php echo $compromiso["id_module"]."-".$compromiso["id_submodule"]."-".$event; ?>" ><?php echo $action[1]; ?></td>
                                                        </tr>
                                                    <?php } ?>
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
                                        <a data-toggle="collapse" href="#collapse_notif_admin_5" data-parent="#accordion_notif_admin" class="accordion-toggle">
                                            <h4 style="font-size:16px">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("permittings"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse_notif_admin_5" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th><?php echo lang("event");?></th>
                                                        <th class="text-center"><?php echo lang("configured");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_permisos_admin as $index => $permiso) { ?>
                                                    <?php foreach($permiso["events"] as $event => $action) { ?>
                                                        <tr>
                                                            <td>
                                                                <?php  echo $permiso["submodule"]; ?>
                                                            </td>
                                                            <td><?php echo lang($event); ?></td>
                                                            <td class="text-center" id="configured-<?php echo $permiso["id_module"]."-".$permiso["id_submodule"]."-".$event; ?>"><?php echo $action[0]; ?></td>
                                                            <td class="option text-center" id="action-<?php echo $permiso["id_module"]."-".$permiso["id_submodule"]."-".$event; ?>" ><?php echo $action[1]; ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
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