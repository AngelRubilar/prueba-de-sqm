<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="name" class="col-md-2"><?php echo lang('name'); ?></label>
        <div class="col-md-10">
            <?php
            echo $profile_info->name;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-2"><?php echo lang('created_date'); ?></label>
        <div class="col-md-10">
            <?php
            echo $profile_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-2"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-10">
            <?php
            echo ($profile_info->modified)?$profile_info->modified:'-';
            ?>
        </div>
    </div>
    
    <div class="form-group">
    	<div class="table-responsive">
            <table id="tabla" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="th_border"><?php echo lang("permission"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("view"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("add"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("edit"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("delete"); ?></th>
                    </tr>
                    <tr>
                        <th class="th_border"></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach ($clients_modules as $key => $module) { ?>
						<?php 
                            if ($module->id_profile) {
                                $checked_ver_1 = ($module->ver == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_ver_2 = ($module->ver == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_ver_3 = ($module->ver == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_1 = ($module->agregar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_2 = ($module->agregar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_3 = ($module->agregar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_1 = ($module->editar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_2 = ($module->editar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_3 = ($module->editar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_1 = ($module->eliminar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_2 = ($module->eliminar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_3 = ($module->eliminar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                            }
                        ?>
                        
                        
                         <?php if($module->id_client_module == 1) { // HUELLAS AMBIENTALES ?>
							<?php if(!$cabecera_huellas_ambientales) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_huellas_ambientales = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 2 || $module->id_client_module == 3 || $module->id_client_module == 4) { // RA, MANT, OR ?>
							<?php if(!$cabecera_records) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo lang("records"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_records = TRUE; ?>
                        <?php } ?>
                        
                         <?php if($module->id_client_module == 5) { // REPORTES ?>
							<?php if(!$cabecera_reportes) { ?>
								<tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_reportes = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 6) { // COMPROMISOS ?>
							<?php if(!$cabecera_compromisos) { ?>
								<tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo lang("compromises"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_compromisos = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 7) { // PERMISOS ?>
							<?php if(!$cabecera_permisos) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo lang("permittings"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_permisos = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 8) { // RESIDUOS ?>
							<?php if(!$cabecera_residuos) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_residuos = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 9) { // COMUINIDADES ?>
							<?php if(!$cabecera_comunidades) { ?>
                            	<tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_comunidades = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 10) { // AYUDA Y SOPORTE ?>
							<?php if(!$cabecera_ayuda_soporte) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_ayuda_soporte = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 11) { // ADMINISTRACIÓN CLIENTE ?>

                            <?php if($module->id_client_submodule == 27){ continue; } // SUBMÓDULO CARGA MASIVA DE MIMAIRE ?>

							<?php if(!$cabecera_ac) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_ac = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 12) { // RECORDBOOK ?>
							<?php if(!$cabecera_rb) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_rb = TRUE; ?>
                        <?php } ?>
                
                        <tr>
                        
                        	<?php if($module->id_client_submodule) { ?>
                        		
                                 <?php 
									$nombre_submodulo = "";
									if($module->nombre_submodulo){
										$nombre_submodulo = $module->nombre_submodulo;
									} else {
										if($module->id_client_submodule != 0){
											$nombre_submodulo = $this->Clients_submodules_model->get_one($module->id_client_submodule)->name;
										}
									}
								?>
                                
                                <td class="td_border"><?php echo $nombre_submodulo; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_ver_3; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                   <?php echo $checked_agregar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_editar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_eliminar_3; ?>
                                </td>
                            
                            <?php } else { ?>
                            	
                                <td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_ver_3; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                   <?php echo $checked_agregar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_editar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_eliminar_3; ?>
                                </td>
                                
							<?php } ?>
                            
                        </tr>   
                    <?php } ?> 
                </tbody>
            </table>
        </div>
    </div>
    
    
    <div class="form-group pt10">
    	<div class="table-responsive">
            <table id="tabla" class="table table-bordered table-hover">
                <thead>
                	<tr>
                        <th class="th_border"><?php echo lang("permission"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("view"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("add"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("edit"); ?></th>
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("delete"); ?></th>
                    </tr>
                    <tr>
                        <th class="th_border"></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                        <th><?php echo lang("all"); ?></th>
                        <th><?php echo lang("own"); ?></th>
                        <th class="th_border"><?php echo lang("none"); ?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach ($air_modules as $key => $module) { ?>
                    
						<?php 
                            if ($module->id_profile) {
                                $checked_ver_1 = ($module->ver == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_ver_2 = ($module->ver == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_ver_3 = ($module->ver == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_1 = ($module->agregar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_2 = ($module->agregar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_3 = ($module->agregar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_1 = ($module->editar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_2 = ($module->editar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_3 = ($module->editar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_1 = ($module->eliminar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_2 = ($module->eliminar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_3 = ($module->eliminar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                            }
                        ?>
                        
                        <?php if($module->id_client_module == 12) { // REGISTROS CALIDAD DEL AIRE ?>
							<?php if(!$cabecera_registros_calidad_air) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_registros_calidad_air = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 13) { // MONITOREO Y ALERTAS ?>
							<?php if(!$cabecera_monitoreo_alertas) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_monitoreo_alertas = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->id_client_module == 14) { // PRONOSTICO ?>
							<?php if(!$cabecera_pronostico) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_pronostico = TRUE; ?>
                        <?php } ?>

                        <?php if($module->id_client_module == 20) { // CONDICIONES METEOROLÓGICAS ?>
							<?php if(!$cabecera_condiciones_meteo) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_condiciones_meteo = TRUE; ?>
                        <?php } ?>

                        <?php if($module->id_client_module == 15) { // ADMINISTRACIÓN CLIEBNTE MIMAIRE ?>
							<?php if(!$cabecera_admin_cliente_mimaire) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_admin_cliente_mimaire = TRUE; ?>
                        <?php } ?>

                        <?php if($module->id_client_module == 11) { // ADMINISTRACIÓN CLIENTE - CARGA MASIVA MIMAire ?>
                            <?php if($module->id_client_submodule == 20 || $module->id_client_submodule == 21 || $module->id_client_submodule == 27){ continue; } // SUBMÓDULOS CARGA MASIVA MIMASOFT ?>
                        <?php } ?>
                        
                        <tr>
                        
                        	<?php if($module->id_client_submodule) { ?>
                        		
								<?php 
									$nombre_submodulo = "";
									if($module->nombre_submodulo){
										$nombre_submodulo = $module->nombre_submodulo;
									} else {
										if($module->id_client_submodule != 0){
											$nombre_submodulo = $this->Clients_submodules_model->get_one($module->id_client_submodule)->name;
										}
									}
								?>
                                
                                <td class="td_border"><?php echo $nombre_submodulo; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_ver_3; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                   <?php echo $checked_agregar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_editar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_eliminar_3; ?>
                                </td>

                                <!-- 
                                    Excepción: forzar creación de td para módulo 16 (Resumen de Pronósticos), para posicionarlo después del submódulo es 28
                                -->
                                <?php if($module->id_client_submodule == 28){ ?>
                                    
                                    <?php foreach($air_module_forecast_summary as $module){ ?>

                                        <?php 
                                            if ($module->id_profile) {
                                                $checked_ver_1 = ($module->ver == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_ver_2 = ($module->ver == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_ver_3 = ($module->ver == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_agregar_1 = ($module->agregar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_agregar_2 = ($module->agregar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_agregar_3 = ($module->agregar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_editar_1 = ($module->editar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_editar_2 = ($module->editar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_editar_3 = ($module->editar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_eliminar_1 = ($module->eliminar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_eliminar_2 = ($module->eliminar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_eliminar_3 = ($module->eliminar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                            }
                                        ?>

                                        <tr>    
                                            <td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_ver_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_ver_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                                <?php echo $checked_ver_3; ?>
                                            </td>
                                            <td style="text-align: center;">
                                            <?php echo $checked_agregar_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                            <?php echo $checked_agregar_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                            <?php echo $checked_agregar_3; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_editar_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_editar_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                                <?php echo $checked_editar_3; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_eliminar_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_eliminar_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                                <?php echo $checked_eliminar_3; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php foreach($air_module_forecast_performance as $module){ ?>

                                        <?php 
                                            if ($module->id_profile) {
                                                $checked_ver_1 = ($module->ver == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_ver_2 = ($module->ver == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_ver_3 = ($module->ver == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_agregar_1 = ($module->agregar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_agregar_2 = ($module->agregar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_agregar_3 = ($module->agregar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_editar_1 = ($module->editar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_editar_2 = ($module->editar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_editar_3 = ($module->editar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_eliminar_1 = ($module->eliminar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_eliminar_2 = ($module->eliminar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                                $checked_eliminar_3 = ($module->eliminar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                            }
                                        ?>

                                        <tr>    
                                            <td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_ver_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_ver_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                                <?php echo $checked_ver_3; ?>
                                            </td>
                                            <td style="text-align: center;">
                                            <?php echo $checked_agregar_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                            <?php echo $checked_agregar_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                            <?php echo $checked_agregar_3; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_editar_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_editar_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                                <?php echo $checked_editar_3; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_eliminar_1; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $checked_eliminar_2; ?>
                                            </td>
                                            <td class="td_border" style="text-align: center;">
                                                <?php echo $checked_eliminar_3; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    

                                <?php } ?>
                                
							<?php } else { ?>
                            	
                                <td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_ver_3; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $checked_agregar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                   <?php echo $checked_agregar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_editar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_eliminar_3; ?>
                                </td>
                                
							<?php } ?>
                        
					<?php } ?>

                    
                    
                	<?php foreach ($air_modules as $key => $module) { ?>
                    
                        <?php 
                            if ($module->id_profile) {
                                $checked_ver_1 = ($module->ver == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_ver_2 = ($module->ver == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_ver_3 = ($module->ver == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_1 = ($module->agregar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_2 = ($module->agregar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_agregar_3 = ($module->agregar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_1 = ($module->editar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_2 = ($module->editar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_editar_3 = ($module->editar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_1 = ($module->eliminar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_2 = ($module->eliminar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_eliminar_3 = ($module->eliminar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                            }
                        ?>

                        <?php if($module->id_client_module == 11) { // ADMINISTRACIÓN CLIENTE - CARGA MASIVA MIMAire ?>
                           
                            <?php if($module->id_client_submodule == 20 || $module->id_client_submodule == 21){ continue; } // SUBMÓDULOS CARGA MASIVA MIMASOFT ?>
                            <?php if(!$cabecera_air_ac) { ?>
                                <tr>
                                    <td colspan="13" style="background-color: #ddd;"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                                </tr>
                            <?php } ?>

                            <?php $cabecera_air_ac = TRUE; ?>
                        <?php } else { continue; } ?>

                        <tr>
                        
                            <?php if($module->id_client_submodule) { ?>
                                
                                <?php 
                                    $nombre_submodulo = "";
                                    if($module->nombre_submodulo){
                                        $nombre_submodulo = $module->nombre_submodulo;
                                    } else {
                                        if($module->id_client_submodule != 0){
                                            $nombre_submodulo = $this->Clients_submodules_model->get_one($module->id_client_submodule)->name;
                                        }
                                    }
                                ?>
                                
                                <td class="td_border"><?php echo $nombre_submodulo; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_ver_3; ?>
                                </td>
                                <td style="text-align: center;">
                                <?php echo $checked_agregar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                <?php echo $checked_agregar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                <?php echo $checked_agregar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_editar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_eliminar_3; ?>
                                </td>
                                
                            <?php } else { ?>
                                
                                <td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_ver_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_ver_3; ?>
                                </td>
                                <td style="text-align: center;">
                                <?php echo $checked_agregar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                <?php echo $checked_agregar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                <?php echo $checked_agregar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_editar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_editar_3; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_eliminar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_eliminar_3; ?>
                                </td>
                                
                            <?php } ?>
                    
                    <?php } ?>



                    
                </tbody>
            </table>
        </div>
	</div>
    
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>
<style>
	#ajaxModal > .modal-dialog {
		width:80% !important;
	}
	
	#tabla {
		border: 1px solid;		
	}
	#tabla .th_border {
		border-left: 1px solid;
		border-right: 1px solid;
	}
	#tabla .td_border{
		border-left: 1px solid;
		border-right: 1px solid;
	}
	
	#tabla tbody > tr:last-child > td {
		border-bottom: 1px solid;
	}

</style>
<script type="text/javascript">
    $(document).ready(function() {
        $("#users-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#users-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
        //$("#company_name").focus();
    });
</script>    