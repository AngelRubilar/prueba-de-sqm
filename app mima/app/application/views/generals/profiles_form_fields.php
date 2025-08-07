<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->name,
            "class" => "form-control",
            "placeholder" => lang('name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <div class="table-responsive">
        <table id="tabla" class="table table-bordered table-hover">
            <thead>
                <tr>
                	<?php
                    $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('profiles_disabled_info').'"><i class="fa fa-question-circle"></i></span>';
					?>
                    <th class="th_border"><?php echo lang("permission").' '.$info; ?></th>
                    <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("view"); ?></th>
                    <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("add"); ?></th>
                    <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("edit"); ?></th>
                    <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("delete"); ?></th>
                </tr>
                <tr>
                	<th class="th_border"></th>

                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_select_view_all" name="radio_select_view" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_select_view_own" name="radio_select_view" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_select_view_none" name="radio_select_view" /></th>
                    
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_select_add_all" name="radio_select_add" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_select_add_own" name="radio_select_add" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_select_add_none" name="radio_select_add" /></th>
                    
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_select_edit_all" name="radio_select_edit" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_select_edit_own" name="radio_select_edit" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_select_edit_none" name="radio_select_edit" /></th>
                    
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_select_delete_all" name="radio_select_delete" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_select_delete_own" name="radio_select_delete" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_select_delete_none" name="radio_select_delete" /></th>
                </tr>
            </thead>
            <tbody>
           
            <?php foreach ($client_context_modules as $key => $module) { ?>
				<?php 
                    if ($module->id_client_context_profile) {
                        $checked_ver_1 = ($module->ver == 1) ? "checked" : "";
                        $checked_ver_2 = ($module->ver == 2) ? "checked" : "";
                        $checked_ver_3 = ($module->ver == 3) ? "checked" : "";
						$checked_agregar_1 = ($module->agregar == 1) ? "checked" : "";
                        $checked_agregar_2 = ($module->agregar == 2) ? "checked" : "";
                        $checked_agregar_3 = ($module->agregar == 3) ? "checked" : "";
						$checked_editar_1 = ($module->editar == 1) ? "checked" : "";
                        $checked_editar_2 = ($module->editar == 2) ? "checked" : "";
                        $checked_editar_3 = ($module->editar == 3) ? "checked" : "";
						$checked_eliminar_1 = ($module->eliminar == 1) ? "checked" : "";
                        $checked_eliminar_2 = ($module->eliminar == 2) ? "checked" : "";
                        $checked_eliminar_3 = ($module->eliminar == 3) ? "checked" : "";
                    }
					
					// Deshabilitar radio a módulos que no tienen tableros para añadir, editar o borrar.
					if(in_array($module->id_client_context_submodule, array(1,2,3,4,5,6,7)) || 
					$module->id_client_context_module == 4){
						$disabled = "disabled";
					} else {
						$disabled = "";
					}

                ?>
                
                
                <?php if($module->contexto == "help_and_support") { ?>
                	<?php if(!$cabecera_help_and_support) { ?>
                        <!--
                        <tr>
                            <td colspan="13" style="background-color: #ddd;"><strong><?php echo lang("help_and_support"); ?></strong></td>
                        </tr>
                        -->
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo lang("help_and_support"); ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="has_radio_select_view_all" name="has_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="has_radio_select_view_own" name="has_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="has_radio_select_view_none" name="has_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="has_radio_select_add_all" name="has_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="has_radio_select_add_own" name="has_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="has_radio_select_add_none" name="has_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="has_radio_select_edit_all" name="has_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="has_radio_select_edit_own" name="has_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="has_radio_select_edit_none" name="has_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="has_radio_select_delete_all" name="has_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="has_radio_select_delete_own" name="has_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="has_radio_select_delete_none" name="has_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_help_and_support = TRUE; ?>
                <?php } ?>
                
                <?php if($module->contexto == "kpi") { ?>
                	<?php if(!$cabecera_kpi) { ?>
                        <!--
                        <tr>
                            <td colspan="13" style="background-color: #ddd;"><strong><?php echo lang("kpi"); ?></strong></td>
                        </tr>
                        -->
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo lang("kpi"); ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="kpi_radio_select_view_all" name="kpi_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="kpi_radio_select_view_own" name="kpi_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="kpi_radio_select_view_none" name="kpi_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="kpi_radio_select_add_all" name="kpi_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="kpi_radio_select_add_own" name="kpi_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="kpi_radio_select_add_none" name="kpi_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="kpi_radio_select_edit_all" name="kpi_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="kpi_radio_select_edit_own" name="kpi_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="kpi_radio_select_edit_none" name="kpi_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="kpi_radio_select_delete_all" name="kpi_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="kpi_radio_select_delete_own" name="kpi_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="kpi_radio_select_delete_none" name="kpi_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_kpi = TRUE; ?>
                <?php } ?>
                
                <?php if($module->contexto == "circular_economy") { ?>
                	<?php if(!$cabecera_economia_circular) { ?>
                        <!--
                        <tr>
                            <td colspan="13" style="background-color: #ddd;"><strong><?php echo lang("circular_economy"); ?></strong></td>
                        </tr>
                        -->
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo lang("circular_economy"); ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ec_radio_select_view_all" name="ec_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ec_radio_select_view_own" name="ec_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ec_radio_select_view_none" name="ec_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ec_radio_select_add_all" name="ec_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ec_radio_select_add_own" name="ec_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ec_radio_select_add_none" name="ec_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ec_radio_select_edit_all" name="ec_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ec_radio_select_edit_own" name="ec_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ec_radio_select_edit_none" name="ec_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ec_radio_select_delete_all" name="ec_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ec_radio_select_delete_own" name="ec_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ec_radio_select_delete_none" name="ec_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_economia_circular = TRUE; ?>
                <?php } ?>
                
                <tr>
                	<?php
					
						if($module->contexto == "kpi"){ 
							$view_all_class = "kpi_view_all";
							$view_own_class = "kpi_view_own";
							$view_none_class = "kpi_view_none";
							
							$add_all_class = "kpi_add_all";
							$add_own_class = "kpi_add_own";
							$add_none_class = "kpi_add_none";
							
							$edit_all_class = "kpi_edit_all";
							$edit_own_class = "kpi_edit_own";
							$edit_none_class = "kpi_edit_none";
							
							$delete_all_class = "kpi_delete_all";
							$delete_own_class = "kpi_delete_own";
							$delete_none_class = "kpi_delete_none";
							
							$to_audit_all_class = "kpi_to_audit_all";
							$to_audit_own_class = "kpi_to_audit_own";
							$to_audit_none_class = "kpi_to_audit_none";
						}
						
						if($module->contexto == "circular_economy"){ 
							$view_all_class = "ec_view_all";
							$view_own_class = "ec_view_own";
							$view_none_class = "ec_view_none";
							
							$add_all_class = "ec_add_all";
							$add_own_class = "ec_add_own";
							$add_none_class = "ec_add_none";
							
							$edit_all_class = "ec_edit_all";
							$edit_own_class = "ec_edit_own";
							$edit_none_class = "ec_edit_none";
							
							$delete_all_class = "ec_delete_all";
							$delete_own_class = "ec_delete_own";
							$delete_none_class = "ec_delete_none";
							
							$to_audit_all_class = "ec_to_audit_all";
							$to_audit_own_class = "ec_to_audit_own";
							$to_audit_none_class = "ec_to_audit_none";
						}
						
						if($module->contexto == "help_and_support"){ 
							$view_all_class = "has_view_all";
							$view_own_class = "has_view_own";
							$view_none_class = "has_view_none";
							
							$add_all_class = "has_add_all";
							$add_own_class = "has_add_own";
							$add_none_class = "has_add_none";
							
							$edit_all_class = "has_edit_all";
							$edit_own_class = "has_edit_own";
							$edit_none_class = "has_edit_none";
							
							$delete_all_class = "has_delete_all";
							$delete_own_class = "has_delete_own";
							$delete_none_class = "has_delete_none";
							
							$to_audit_all_class = "has_to_audit_all";
							$to_audit_own_class = "has_to_audit_own";
							$to_audit_none_class = "has_to_audit_none";
						}
                    
					?>
                    
                	<?php if($module->id_client_context_submodule) { ?>
                		
                        <?php 
							$nombre_submodulo = "";
							if($module->nombre_submodulo){
								$nombre_submodulo = $module->nombre_submodulo;
							} else {
								if($module->id_client_context_submodule != 0){
									$nombre_submodulo = $this->Client_context_submodules_model->get_one($module->id_client_context_submodule)->name;
								}
							}
						?>

                        <td class="td_border"><?php echo $nombre_submodulo; ?></td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_context_submodule; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_all <?php echo $view_all_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_context_submodule; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='view_own ".$view_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_context_submodule; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>"  class="view_none <?php echo $view_none_class; ?>" />
                        </td>
                        	
						<td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_context_submodule; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_agregar) ? "disabled" : "class='add_all ".$add_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_context_submodule; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_agregar_propios || $disabled_agregar) ? "disabled" : "class='add_own ".$add_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_context_submodule; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_agregar) ? "disabled" : "class='add_none ".$add_none_class."'"; ?> />
                        </td>
                        
                        <?php if($module->id_client_context_submodule == 17){ ?>
                        	
                            <td style="text-align: center;">
                            	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_context_submodule; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="edit_all <?php echo $edit_all_class; ?>" />
                            </td>
                            <td style="text-align: center;">
                            	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_context_submodule; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_own ".$edit_own_class."'"; ?> />
                            </td>
                            <td class="td_border" style="text-align: center;">
                            	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_context_submodule; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="edit_none <?php echo $edit_none_class; ?>" />
                            </td>
                            
						<?php } else { ?>

                            <td style="text-align: center;">
                            	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_context_submodule; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_all ".$edit_all_class."'"; ?> />
                            </td>
                            <td style="text-align: center;">
                            	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_context_submodule; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_own ".$edit_own_class."'"; ?> />
                            </td>
                            <td class="td_border" style="text-align: center;">
                            	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_context_submodule; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_none ".$edit_none_class."'"; ?> />
                            </td>
                        
                        <?php } ?>
                        
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_context_submodule; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_borrar) ? "disabled" : "class='delete_all ".$delete_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_context_submodule; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_borrar) ? "disabled" : "class='delete_own ".$delete_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_context_submodule; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_borrar) ? "disabled" : "class='delete_none ".$delete_none_class."'"; ?> />
                        </td>
                        
                    
                    <?php } else { ?>
                    
                    	<td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_context_module; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_all <?php echo $view_all_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_context_module; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='view_own ".$view_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_context_module; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_none <?php echo $view_none_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_context_module; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_agregar) ? "disabled" : "class='add_all ".$add_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                        	<input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_context_module; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_agregar_propios || $disabled_agregar) ? "disabled" : "class='add_own ".$add_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_context_module; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_agregar) ? "disabled" : " class='add_none ".$add_none_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_context_module; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_all ".$edit_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_context_module; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_own ".$edit_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_context_module; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_none ".$edit_none_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_context_module; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_borrar) ? "disabled" : "class='delete_all ".$delete_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_context_module; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_borrar) ? "disabled" : "class='delete_own ".$delete_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_context_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_context_module; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_borrar) ? "disabled" : "class='delete_none ".$delete_none_class."'"; ?> />
                        </td>
                        
                    
                    <?php } ?>
                    
                </tr>   
            <?php } ?>                
            </tbody>
        </table>
    </div>    
</div>
<style>
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
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		// CABECERAS PARA SELECCIONAR TODOS LOS RADIO BUTTONS DE UNA COLUMNA
		
		// ---------- VER ----------
		// GENERAL
		$(document).on("click","#radio_select_view_all", function(event){ 
			$('.view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_view_own", function(event){
			$('.view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_view_none", function(event){
			$('.view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ACUERDOS TERRITORIO
		$(document).on("click","#at_radio_select_view_all", function(event){
			$('.at_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#at_radio_select_view_own", function(event){
			$('.at_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#at_radio_select_view_none", function(event){
			$('.at_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ACUERDOS DISTRIBUCIÓN
		$(document).on("click","#ad_radio_select_view_all", function(event){
			$('.ad_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ad_radio_select_view_own", function(event){
			$('.ad_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ad_radio_select_view_none", function(event){
			$('.ad_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// RECORDBOOK
		$(document).on("click","#rb_radio_select_view_all", function(event){
			$('.rb_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rb_radio_select_view_own", function(event){
			$('.rb_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rb_radio_select_view_none", function(event){
			$('.rb_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// KPI
		$(document).on("click","#kpi_radio_select_view_all", function(event){
			$('.kpi_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_view_own", function(event){
			$('.kpi_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_view_none", function(event){
			$('.kpi_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ECONOMÍA CIRCULAR
		$(document).on("click","#ec_radio_select_view_all", function(event){
			$('.ec_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_view_own", function(event){
			$('.ec_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_view_none", function(event){
			$('.ec_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// AYUDA Y SOPORTE
		$(document).on("click","#has_radio_select_view_all", function(event){
			$('.has_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_view_own", function(event){
			$('.has_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_view_none", function(event){
			$('.has_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN VER ----------
		

		// ---------- AÑADIR ----------
		// GENERAL
		$(document).on("click","#radio_select_add_all", function(event){
			$('.add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_add_own", function(event){
			$('.add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_add_none", function(event){
			$('.add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// KPI
		$(document).on("click","#kpi_radio_select_add_all", function(event){
			$('.kpi_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_add_own", function(event){
			$('.kpi_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_add_none", function(event){
			$('.kpi_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ECONOMÍA CIRCULAR
		$(document).on("click","#ec_radio_select_add_all", function(event){
			$('.ec_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_add_own", function(event){
			$('.ec_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_add_none", function(event){
			$('.ec_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// AYUDA Y SOPORTE
		$(document).on("click","#has_radio_select_add_all", function(event){
			$('.has_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_add_own", function(event){
			$('.has_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_add_none", function(event){
			$('.has_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN AÑADIR ----------
		
		
		// ---------- EDITAR ----------
		// GENERAL
		$(document).on("click","#radio_select_edit_all", function(event){
			$('.edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_edit_own", function(event){
			$('.edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_edit_none", function(event){
			$('.edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// KPI
		$(document).on("click","#kpi_radio_select_edit_all", function(event){
			$('.kpi_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_edit_own", function(event){
			$('.kpi_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_edit_none", function(event){
			$('.kpi_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ECONOMÍA CIRCULAR
		$(document).on("click","#ec_radio_select_edit_all", function(event){
			$('.ec_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_edit_own", function(event){
			$('.ec_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_edit_none", function(event){
			$('.ec_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// AYUDA Y SOPORTE
		$(document).on("click","#has_radio_select_edit_all", function(event){
			$('.has_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_edit_own", function(event){
			$('.has_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_edit_none", function(event){
			$('.has_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN EDITAR ----------
		

		// ---------- BORRAR ----------
		$(document).on("click","#radio_select_delete_all", function(event){
			$('.delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_delete_own", function(event){
			$('.delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#radio_select_delete_none", function(event){
			$('.delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// KPI
		$(document).on("click","#kpi_radio_select_delete_all", function(event){
			$('.kpi_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_delete_own", function(event){
			$('.kpi_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#kpi_radio_select_delete_none", function(event){
			$('.kpi_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ECONOMÍA CIRCULAR
		$(document).on("click","#ec_radio_select_delete_all", function(event){
			$('.ec_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_delete_own", function(event){
			$('.ec_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ec_radio_select_delete_none", function(event){
			$('.ec_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// AYUDA Y SOPORTE
		$(document).on("click","#has_radio_select_delete_all", function(event){
			$('.has_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_delete_own", function(event){
			$('.has_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#has_radio_select_delete_none", function(event){
			$('.has_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN BORRAR ----------

    });
</script>