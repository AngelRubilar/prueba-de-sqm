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
                	<th class="th_border text-center"></th>
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
            <?php foreach ($clients_modules as $key => $module) { ?>
			
				<?php 
	
						$checked_ver_1 = "";
						$checked_ver_2 = "";
						$checked_ver_3 = "";
					
                    if ($module->id_profile) {
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
	
	
					if ($module->id_client_module == 10) {
						$checked_ver_1 = "";
						$checked_ver_2 = "";
						$checked_ver_3 = "checked";
					}
					
					//Deshabilitar radio a módulos que no tienen tableros para añadir, editar o borrar.
					if($module->id_client_submodule == 1 || 
						$module->id_client_submodule == 2 || 
						$module->id_client_module == 5 || 
						$module->id_client_submodule == 16 || 
						$module->id_client_submodule == 17 || 
						$module->id_client_submodule == 18 || 
						$module->id_client_submodule == 19 || 
						$module->id_client_submodule == 3 || 
						$module->id_client_submodule == 5 || 
						$module->id_client_submodule == 7 || 
						$module->id_client_submodule == 8 || 
						$module->id_client_submodule == 10 ||
					  	$module->id_client_submodule == 25){
						$disabled = "disabled";
					} else {
						$disabled = "";
					}
					
					// Desactiva: Permiso VER: propios, Permiso AÑADIR: completo, Permiso EDITAR: propios y BORRAR: completo
					if($module->id_client_submodule == 20 || $module->id_client_submodule == 21 || $module->id_client_submodule == 4 || $module->id_client_submodule == 22 || $module->id_client_submodule == 6 || $module->id_client_submodule == 13
					|| $module->id_client_submodule == 15 || $module->id_client_submodule == 24){
						$disabled_add_delete = "disabled";
					} else {
						$disabled_add_delete = "";
					}
					
					// Desactiva: Permiso AÑADIR: propios
					if($module->id_client_module == 2 || $module->id_client_module == 3 || $module->id_client_module == 4 || $module->id_client_submodule == 22 || $module->id_client_submodule == 9 || $module->id_client_submodule == 11
						|| $module->id_client_submodule == 12 || $module->id_client_submodule == 14 || $module->id_client_submodule == 23){
						$disabled_own = "disabled";
					} else {
						$disabled_own = "";
					}
					
                ?>
                
                
                <?php if($module->id_client_module == 1) { // HUELLAS AMBIENTALES ?>
					<?php if(!$cabecera_huellas_ambientales) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ha_radio_select_view_all" name="ha_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ha_radio_select_view_own" name="ha_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ha_radio_select_view_none" name="ha_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ha_radio_select_add_all" name="ha_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ha_radio_select_add_own" name="ha_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ha_radio_select_add_none" name="ha_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ha_radio_select_edit_all" name="ha_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ha_radio_select_edit_own" name="ha_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ha_radio_select_edit_none" name="ha_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ha_radio_select_delete_all" name="ha_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ha_radio_select_delete_own" name="ha_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ha_radio_select_delete_none" name="ha_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_huellas_ambientales = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 2 || $module->id_client_module == 3 || $module->id_client_module == 4) { // RA, MANT, OR ?>
					<?php if(!$cabecera_records) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo lang("records"); ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rc_radio_select_view_all" name="rc_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rc_radio_select_view_own" name="rc_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rc_radio_select_view_none" name="rc_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rc_radio_select_add_all" name="rc_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rc_radio_select_add_own" name="rc_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rc_radio_select_add_none" name="rc_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rc_radio_select_edit_all" name="rc_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rc_radio_select_edit_own" name="rc_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rc_radio_select_edit_none" name="rc_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rc_radio_select_delete_all" name="rc_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rc_radio_select_delete_own" name="rc_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rc_radio_select_delete_none" name="rc_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_records = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 5) { // REPORTES ?>
					<?php if(!$cabecera_reportes) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rep_radio_select_view_all" name="rep_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rep_radio_select_view_own" name="rep_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rep_radio_select_view_none" name="rep_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rep_radio_select_add_all" name="rep_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rep_radio_select_add_own" name="rep_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rep_radio_select_add_none" name="rep_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rep_radio_select_edit_all" name="rep_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rep_radio_select_edit_own" name="rep_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rep_radio_select_edit_none" name="rep_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="rep_radio_select_delete_all" name="rep_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="rep_radio_select_delete_own" name="rep_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="rep_radio_select_delete_none" name="rep_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_reportes = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 6) { // COMPROMISOS ?>
					<?php if(!$cabecera_compromisos) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo lang("compromises"); ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comp_radio_select_view_all" name="comp_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comp_radio_select_view_own" name="comp_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comp_radio_select_view_none" name="comp_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comp_radio_select_add_all" name="comp_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comp_radio_select_add_own" name="comp_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comp_radio_select_add_none" name="comp_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comp_radio_select_edit_all" name="comp_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comp_radio_select_edit_own" name="comp_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comp_radio_select_edit_none" name="comp_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comp_radio_select_delete_all" name="comp_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comp_radio_select_delete_own" name="comp_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comp_radio_select_delete_none" name="comp_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_compromisos = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 7) { // PERMISOS ?>
					<?php if(!$cabecera_permisos) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo lang("permittings"); ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="perm_radio_select_view_all" name="perm_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="perm_radio_select_view_own" name="perm_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="perm_radio_select_view_none" name="perm_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="perm_radio_select_add_all" name="perm_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="perm_radio_select_add_own" name="perm_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="perm_radio_select_add_none" name="perm_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="perm_radio_select_edit_all" name="perm_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="perm_radio_select_edit_own" name="perm_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="perm_radio_select_edit_none" name="perm_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="perm_radio_select_delete_all" name="perm_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="perm_radio_select_delete_own" name="perm_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="perm_radio_select_delete_none" name="perm_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_permisos = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 8) { // RESIDUOS ?>
					<?php if(!$cabecera_residuos) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="res_radio_select_view_all" name="res_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="res_radio_select_view_own" name="res_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="res_radio_select_view_none" name="res_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="res_radio_select_add_all" name="res_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="res_radio_select_add_own" name="res_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="res_radio_select_add_none" name="res_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="res_radio_select_edit_all" name="res_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="res_radio_select_edit_own" name="res_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="res_radio_select_edit_none" name="res_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="res_radio_select_delete_all" name="res_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="res_radio_select_delete_own" name="res_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="res_radio_select_delete_none" name="res_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_residuos = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 9) { // COMUINIDADES ?>
					<?php if(!$cabecera_comunidades) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comu_radio_select_view_all" name="comu_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comu_radio_select_view_own" name="comu_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comu_radio_select_view_none" name="comu_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comu_radio_select_add_all" name="comu_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comu_radio_select_add_own" name="comu_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comu_radio_select_add_none" name="comu_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comu_radio_select_edit_all" name="comu_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comu_radio_select_edit_own" name="comu_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comu_radio_select_edit_none" name="comu_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="comu_radio_select_delete_all" name="comu_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="comu_radio_select_delete_own" name="comu_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="comu_radio_select_delete_none" name="comu_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_comunidades = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 10) { // AYUDA Y SOPORTE ?>
					<?php if(!$cabecera_ayuda_soporte) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="help_radio_select_view_all" name="help_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="help_radio_select_view_own" name="help_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="help_radio_select_view_none" name="help_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="help_radio_select_add_all" name="help_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="help_radio_select_add_own" name="help_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="help_radio_select_add_none" name="help_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="help_radio_select_edit_all" name="help_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="help_radio_select_edit_own" name="help_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="help_radio_select_edit_none" name="help_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="help_radio_select_delete_all" name="help_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="help_radio_select_delete_own" name="help_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="help_radio_select_delete_none" name="help_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_ayuda_soporte = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 11) { // ADMINISTRACIÓN CLIENTE ?>

					<?php if($module->id_client_submodule == 27){ continue; } // SUBMÓDULO CARGA MASIVA DE MIMAIRE ?>

					<?php if(!$cabecera_ac) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ac_radio_select_view_all" name="ac_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ac_radio_select_view_own" name="ac_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ac_radio_select_view_none" name="ac_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ac_radio_select_add_all" name="ac_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ac_radio_select_add_own" name="ac_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ac_radio_select_add_none" name="ac_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ac_radio_select_edit_all" name="ac_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ac_radio_select_edit_own" name="ac_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ac_radio_select_edit_none" name="ac_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="ac_radio_select_delete_all" name="ac_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="ac_radio_select_delete_own" name="ac_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="ac_radio_select_delete_none" name="ac_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_ac = TRUE; ?>
                <?php } ?>

                <tr>
                
                	<?php
                    	
						if($module->id_client_module == 1) { // HUELLAS AMBIENTALES
							$view_all_class = "ha_view_all";
							$view_own_class = "ha_view_own";
							$view_none_class = "ha_view_none";
							
							$add_all_class = "ha_add_all";
							$add_own_class = "ha_add_own";
							$add_none_class = "ha_add_none";
							
							$edit_all_class = "ha_edit_all";
							$edit_own_class = "ha_edit_own";
							$edit_none_class = "ha_edit_none";
							
							$delete_all_class = "ha_delete_all";
							$delete_own_class = "ha_delete_own";
							$delete_none_class = "ha_delete_none";
						}
						
						if($module->id_client_module == 2 || $module->id_client_module == 3 || $module->id_client_module == 4) { // RA, MANT, OR
							$view_all_class = "rc_view_all";
							$view_own_class = "rc_view_own";
							$view_none_class = "rc_view_none";
							
							$add_all_class = "rc_add_all";
							$add_own_class = "rc_add_own";
							$add_none_class = "rc_add_none";
							
							$edit_all_class = "rc_edit_all";
							$edit_own_class = "rc_edit_own";
							$edit_none_class = "rc_edit_none";
							
							$delete_all_class = "rc_delete_all";
							$delete_own_class = "rc_delete_own";
							$delete_none_class = "rc_delete_none";	
						}
						
						if($module->id_client_module == 5) { // REPORTES
							$view_all_class = "rep_view_all";
							$view_own_class = "rep_view_own";
							$view_none_class = "rep_view_none";
							
							$add_all_class = "rep_add_all";
							$add_own_class = "rep_add_own";
							$add_none_class = "rep_add_none";
							
							$edit_all_class = "rep_edit_all";
							$edit_own_class = "rep_edit_own";
							$edit_none_class = "rep_edit_none";
							
							$delete_all_class = "rep_delete_all";
							$delete_own_class = "rep_delete_own";
							$delete_none_class = "rep_delete_none";
						}
						
						if($module->id_client_module == 6) { // COMPROMISOS
							$view_all_class = "comp_view_all";
							$view_own_class = "comp_view_own";
							$view_none_class = "comp_view_none";
							
							$add_all_class = "comp_add_all";
							$add_own_class = "comp_add_own";
							$add_none_class = "comp_add_none";
							
							$edit_all_class = "comp_edit_all";
							$edit_own_class = "comp_edit_own";
							$edit_none_class = "comp_edit_none";
							
							$delete_all_class = "comp_delete_all";
							$delete_own_class = "comp_delete_own";
							$delete_none_class = "comp_delete_none";
						}
						
						if($module->id_client_module == 7) { // PERMISOS
							$view_all_class = "perm_view_all";
							$view_own_class = "perm_view_own";
							$view_none_class = "perm_view_none";
							
							$add_all_class = "perm_add_all";
							$add_own_class = "perm_add_own";
							$add_none_class = "perm_add_none";
							
							$edit_all_class = "perm_edit_all";
							$edit_own_class = "perm_edit_own";
							$edit_none_class = "perm_edit_none";
							
							$delete_all_class = "perm_delete_all";
							$delete_own_class = "perm_delete_own";
							$delete_none_class = "perm_delete_none";
						}
						
						if($module->id_client_module == 8) { // RESIDUOS
							$view_all_class = "res_view_all";
							$view_own_class = "res_view_own";
							$view_none_class = "res_view_none";
							
							$add_all_class = "res_add_all";
							$add_own_class = "res_add_own";
							$add_none_class = "res_add_none";
							
							$edit_all_class = "res_edit_all";
							$edit_own_class = "res_edit_own";
							$edit_none_class = "res_edit_none";
							
							$delete_all_class = "res_delete_all";
							$delete_own_class = "res_delete_own";
							$delete_none_class = "res_delete_none";
						}
						
						if($module->id_client_module == 9) { // COMUINIDADES
							$view_all_class = "comu_view_all";
							$view_own_class = "comu_view_own";
							$view_none_class = "comu_view_none";
							
							$add_all_class = "comu_add_all";
							$add_own_class = "comu_add_own";
							$add_none_class = "comu_add_none";
							
							$edit_all_class = "comu_edit_all";
							$edit_own_class = "comu_edit_own";
							$edit_none_class = "comu_edit_none";
							
							$delete_all_class = "comu_delete_all";
							$delete_own_class = "comu_delete_own";
							$delete_none_class = "comu_delete_none";
						}
						
						if($module->id_client_module == 10) { // AYUDA Y SOPORTE
							$view_all_class = "help_view_all";
							$view_own_class = "help_view_own";
							$view_none_class = "help_view_none";
							
							$add_all_class = "help_add_all";
							$add_own_class = "help_add_own";
							$add_none_class = "help_add_none";
							
							$edit_all_class = "help_edit_all";
							$edit_own_class = "help_edit_own";
							$edit_none_class = "help_edit_none";
							
							$delete_all_class = "help_delete_all";
							$delete_own_class = "help_delete_own";
							$delete_none_class = "help_delete_none";
						}
						
						if($module->id_client_module == 11) { // ADMINISTRACIÓN CLIENTE
							$view_all_class = "ac_view_all";
							$view_own_class = "ac_view_own";
							$view_none_class = "ac_view_none";
							
							$add_all_class = "ac_add_all";
							$add_own_class = "ac_add_own";
							$add_none_class = "ac_add_none";
							
							$edit_all_class = "ac_edit_all";
							$edit_own_class = "ac_edit_own";
							$edit_none_class = "ac_edit_none";
							
							$delete_all_class = "ac_delete_all";
							$delete_own_class = "ac_delete_own";
							$delete_none_class = "ac_delete_none";
						}

					?>
                
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
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_all <?php echo $view_all_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='view_own ".$view_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_none <?php echo $view_none_class; ?>" />
                        </td>
                        
                        <?php if($module->id_client_submodule == 4 || $module->id_client_submodule == 6) { // Si el submódulo es Evaluación de Compromisos RCA ?>
                        
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo "class='add_all ".$add_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled/>
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo "class='add_none ".$add_none_class."'"; ?> />
                        </td>
                        
                        <?php } else { ?>
							
						<td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='add_all ".$add_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete || $disabled_own) ? "disabled" : "class='add_own ".$add_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='add_none ".$add_none_class."'"; ?> />
                        </td>
							
						<?php }?>
                        
                        
                        <?php if($module->id_client_submodule == 4 || $module->id_client_submodule == 6) { // Si el submódulo es Evaluación de Compromisos RCA?>
                        
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="edit_all <?php echo $edit_all_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_own ".$edit_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="edit_none <?php echo $edit_none_class; ?>" />
                        </td>
                        
                        <?php } else { ?>
                        
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_all ".$edit_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='edit_own ".$edit_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_none ".$edit_none_class."'"; ?> />
                        </td>
                        
                        <?php }?>
                        
                        
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='delete_all ".$delete_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='delete_own ".$delete_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='delete_none ".$delete_none_class."'"; ?> />
                        </td>
                    
                    <?php } else { ?>
                    
                    	<td class="td_border"><?php echo $module->nombre_modulo; ?></td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_all <?php echo $view_all_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='view_own ".$view_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="view_none <?php echo $view_none_class; ?>" />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='add_all ".$add_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete || $disabled_own) ? "disabled" : "class='add_own ".$add_own_class."'"; ?>/>
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='add_none ".$add_none_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_all ".$edit_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='edit_own ".$edit_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='edit_none ".$edit_none_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='delete_all ".$delete_all_class."'"; ?> />
                        </td>
                        <td style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='delete_own ".$delete_own_class."'"; ?> />
                        </td>
                        <td class="td_border" style="text-align: center;">
                            <input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='delete_none ".$delete_none_class."'"; ?> />
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
                	<th class="th_border text-center"></th>
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_air_select_view_all" name="radio_air_select_view" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_air_select_view_own" name="radio_air_select_view" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_air_select_view_none" name="radio_air_select_view" /></th>
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_air_select_add_all" name="radio_air_select_add" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_air_select_add_own" name="radio_air_select_add" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_air_select_add_none" name="radio_air_select_add" /></th>
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_air_select_edit_all" name="radio_air_select_edit" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_air_select_edit_own" name="radio_air_select_edit" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_air_select_edit_none" name="radio_air_select_edit" /></th>
                    <th class="text-center"><?php echo lang("all"); ?><br /><input type="radio" id="radio_air_select_delete_all" name="radio_air_select_delete" /></th>
                    <th class="text-center"><?php echo lang("own"); ?><br /><input type="radio" id="radio_air_select_delete_own" name="radio_air_select_delete" /></th>
                    <th class="th_border text-center"><?php echo lang("none"); ?><br /><input type="radio" id="radio_air_select_delete_none" name="radio_air_select_delete" /></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($air_modules as $key => $module) { ?>
            	
                <?php
				
                	$checked_ver_1 = "";
					$checked_ver_2 = "";
					$checked_ver_3 = "";
					
                    if ($module->id_profile) {
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
					
					//Deshabilitar radio a módulos que no tienen tableros para añadir, editar o borrar.
					if($module->id_client_submodule == 23 || $module->id_client_submodule == 25 || $module->id_client_submodule == 26 || $module->id_client_submodule == 28 || $module->id_client_module == 16 || $module->id_client_module == 17 || $module->id_client_module == 18 || $module->id_client_module == 19 || $module->id_client_submodule == 33){
						$disabled = "disabled";
					} else {
						$disabled = "";
					}
					
					// Desactiva: Permiso VER: propios, Permiso AÑADIR: completo, Permiso EDITAR: propios y BORRAR: completo
					if($module->id_client_submodule == 23 || $module->id_client_submodule == 25 || $module->id_client_submodule == 26 || $module->id_client_submodule == 27 || $module->id_client_submodule == 26 || $module->id_client_submodule == 34 || $module->id_client_submodule == 36){
						$disabled_add_delete = "disabled";
					} else {
						$disabled_add_delete = "";
					}
					

				?>
                
                <?php if($module->id_client_module == 12) { // REGISTROS CALIDAD DEL AIRE ?>
					<?php if(!$cabecera_registros_calidad_air) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cda_radio_select_view_all" name="air_cda_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cda_radio_select_view_own" name="air_cda_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cda_radio_select_view_none" name="air_cda_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cda_radio_select_add_all" name="air_cda_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cda_radio_select_add_own" name="air_cda_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cda_radio_select_add_none" name="air_cda_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cda_radio_select_edit_all" name="air_cda_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cda_radio_select_edit_own" name="air_cda_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cda_radio_select_edit_none" name="air_cda_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cda_radio_select_delete_all" name="air_cda_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cda_radio_select_delete_own" name="air_cda_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cda_radio_select_delete_none" name="air_cda_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_registros_calidad_air = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 13) { // MONITOREO Y ALERTAS ?>
					<?php if(!$cabecera_monitoreo_alertas) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_mya_radio_select_view_all" name="air_mya_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_mya_radio_select_view_own" name="air_mya_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_mya_radio_select_view_none" name="air_mya_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_mya_radio_select_add_all" name="air_mya_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_mya_radio_select_add_own" name="air_mya_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_mya_radio_select_add_none" name="air_mya_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_mya_radio_select_edit_all" name="air_mya_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_mya_radio_select_edit_own" name="air_mya_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_mya_radio_select_edit_none" name="air_mya_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_mya_radio_select_delete_all" name="air_mya_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_mya_radio_select_delete_own" name="air_mya_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_mya_radio_select_delete_none" name="air_mya_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_monitoreo_alertas = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 14) { // PRONOSTICO ?>
					<?php if(!$cabecera_pronostico) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_pron_radio_select_view_all" name="air_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_pron_radio_select_view_own" name="air_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_pron_radio_select_view_none" name="air_pron_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_pron_radio_select_add_all" name="air_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_pron_radio_select_add_own" name="air_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_pron_radio_select_add_none" name="air_pron_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_pron_radio_select_edit_all" name="air_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_pron_radio_select_edit_own" name="air_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_pron_radio_select_edit_none" name="air_pron_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_pron_radio_select_delete_all" name="air_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_pron_radio_select_delete_own" name="air_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_pron_radio_select_delete_none" name="air_pron_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_pronostico = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 16) { //  RESUMEN DE PRONÓSTICOS ?>
					<?php if(!$cabecera_res_pronostico) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_res_pron_radio_select_view_all" name="air_res_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_res_pron_radio_select_view_own" name="air_res_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_res_pron_radio_select_view_none" name="air_res_pron_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_res_pron_radio_select_add_all" name="air_res_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_res_pron_radio_select_add_own" name="air_res_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_res_pron_radio_select_add_none" name="air_res_pron_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_res_pron_radio_select_edit_all" name="air_res_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_res_pron_radio_select_edit_own" name="air_res_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_res_pron_radio_select_edit_none" name="air_res_pron_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_res_pron_radio_select_delete_all" name="air_res_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_res_pron_radio_select_delete_own" name="air_res_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_res_pron_radio_select_delete_none" name="air_res_pron_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_res_pronostico = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 18) { //  DESEMPEÑO DE PRONÓSTICO ?>
					<?php if(!$cabecera_des_pronostico) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_des_pron_radio_select_view_all" name="air_des_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_des_pron_radio_select_view_own" name="air_des_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_des_pron_radio_select_view_none" name="air_des_pron_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_des_pron_radio_select_add_all" name="air_des_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_des_pron_radio_select_add_own" name="air_des_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_des_pron_radio_select_add_none" name="air_des_pron_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_des_pron_radio_select_edit_all" name="air_des_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_des_pron_radio_select_edit_own" name="air_des_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_des_pron_radio_select_edit_none" name="air_des_pron_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_des_pron_radio_select_delete_all" name="air_des_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_des_pron_radio_select_delete_own" name="air_des_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_des_pron_radio_select_delete_none" name="air_des_pron_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_des_pronostico = TRUE; ?>
                <?php } ?>
                
                <?php if($module->id_client_module == 19) { //  COMPARACIÓN DE PRONÓSTICOS ?>
					<?php if(!$cabecera_comp_pronostico) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_comp_pron_radio_select_view_all" name="air_comp_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_comp_pron_radio_select_view_own" name="air_comp_pron_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_comp_pron_radio_select_view_none" name="air_comp_pron_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_comp_pron_radio_select_add_all" name="air_comp_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_comp_pron_radio_select_add_own" name="air_comp_pron_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_comp_pron_radio_select_add_none" name="air_comp_pron_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_comp_pron_radio_select_edit_all" name="air_comp_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_comp_pron_radio_select_edit_own" name="air_comp_pron_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_comp_pron_radio_select_edit_none" name="air_comp_pron_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_comp_pron_radio_select_delete_all" name="air_comp_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_comp_pron_radio_select_delete_own" name="air_comp_pron_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_comp_pron_radio_select_delete_none" name="air_comp_pron_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_comp_pronostico = TRUE; ?>
                <?php } ?>

				<?php if($module->id_client_module == 17) { // MONITOREO ?>
					<?php if(!$cabecera_monitoreo) { ?>
						<tr>
							<td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
							
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_monitoring_radio_select_view_all" name="air_monitoring_radio_select_view" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_monitoring_radio_select_view_own" name="air_monitoring_radio_select_view" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_monitoring_radio_select_view_none" name="air_monitoring_radio_select_view" /></strong></td>
						
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_monitoring_radio_select_add_all" name="air_monitoring_radio_select_add" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_monitoring_radio_select_add_own" name="air_monitoring_radio_select_add" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_monitoring_radio_select_add_none" name="air_monitoring_radio_select_add" /></strong></td>
							
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_monitoring_radio_select_edit_all" name="air_monitoring_radio_select_edit" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_monitoring_radio_select_edit_own" name="air_monitoring_radio_select_edit" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_monitoring_radio_select_edit_none" name="air_monitoring_radio_select_edit" /></strong></td>
							
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_monitoring_radio_select_delete_all" name="air_monitoring_radio_select_delete" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_monitoring_radio_select_delete_own" name="air_monitoring_radio_select_delete" /></strong></td>
							<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_monitoring_radio_select_delete_none" name="air_monitoring_radio_select_delete" /></strong></td>
						</tr>
					<?php } ?>
					<?php $cabecera_monitoreo = TRUE; ?>
				<?php } ?>

				<?php if($module->id_client_module == 20) { // CONDICIONES METEOROLÓGICAS ?>
					<?php if(!$cabecera_condiciones_meteo) { ?>
                        <tr>
                            <td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_view_all" name="air_cond_meteo_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_view_own" name="air_cond_meteo_radio_select_view" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_view_none" name="air_cond_meteo_radio_select_view" /></strong></td>
                           
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_add_all" name="air_cond_meteo_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_add_own" name="air_cond_meteo_radio_select_add" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_add_none" name="air_cond_meteo_radio_select_add" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_edit_all" name="air_cond_meteo_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_edit_own" name="air_cond_meteo_radio_select_edit" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_edit_none" name="air_cond_meteo_radio_select_edit" /></strong></td>
                            
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_delete_all" name="air_cond_meteo_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_delete_own" name="air_cond_meteo_radio_select_delete" /></strong></td>
                            <td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_cond_meteo_radio_select_delete_none" name="air_cond_meteo_radio_select_delete" /></strong></td>
                        </tr>
                	<?php } ?>
                    <?php $cabecera_condiciones_meteo = TRUE; ?>
                <?php } ?>

				<?php if($module->id_client_module == 15) { // ADMINISTRACIÓN CLIENTE - CARGA MASIVA MIMAire ?>
					<?php if($module->id_client_submodule == 27 || $module->id_client_submodule == 29 || $module->id_client_submodule == 30 || $module->id_client_submodule == 36){ continue; } // SUBMÓDULOS CARGA MASIVA MIMASOFT ?>
				<?php } ?>

                <tr>

					<?php

						if($module->id_client_module == 12) { // REGISTROS CALIDAD DEL air
							$view_all_class = "cda_view_all";
							$view_own_class = "cda_view_own";
							$view_none_class = "cda_view_none";
							
							$add_all_class = "cda_add_all";
							$add_own_class = "cda_add_own";
							$add_none_class = "cda_add_none";
							
							$edit_all_class = "cda_edit_all";
							$edit_own_class = "cda_edit_own";
							$edit_none_class = "cda_edit_none";
							
							$delete_all_class = "cda_delete_all";
							$delete_own_class = "cda_delete_own";
							$delete_none_class = "cda_delete_none";
						}
						
						if($module->id_client_module == 13) { // MONITOREO Y ALERTAS
							$view_all_class = "mya_view_all";
							$view_own_class = "mya_view_own";
							$view_none_class = "mya_view_none";
							
							$add_all_class = "mya_add_all";
							$add_own_class = "mya_add_own";
							$add_none_class = "mya_add_none";
							
							$edit_all_class = "mya_edit_all";
							$edit_own_class = "mya_edit_own";
							$edit_none_class = "mya_edit_none";
							
							$delete_all_class = "mya_delete_all";
							$delete_own_class = "mya_delete_own";
							$delete_none_class = "mya_delete_none";
						}
						
						if($module->id_client_module == 14) { // PRONOSTICO
							$view_all_class = "pron_view_all";
							$view_own_class = "pron_view_own";
							$view_none_class = "pron_view_none";
							
							$add_all_class = "pron_add_all";
							$add_own_class = "pron_add_own";
							$add_none_class = "pron_add_none";
							
							$edit_all_class = "pron_edit_all";
							$edit_own_class = "pron_edit_own";
							$edit_none_class = "pron_edit_none";
							
							$delete_all_class = "pron_delete_all";
							$delete_own_class = "pron_delete_own";
							$delete_none_class = "pron_delete_none";
						}
						
						if($module->id_client_module == 16) { // RESUMEN DE PRONÓSTICOS
							$view_all_class = "res_pron_view_all";
							$view_own_class = "res_pron_view_own";
							$view_none_class = "res_pron_view_none";
							
							$add_all_class = "res_pron_add_all";
							$add_own_class = "res_pron_add_own";
							$add_none_class = "res_pron_add_none";
							
							$edit_all_class = "res_pron_edit_all";
							$edit_own_class = "res_pron_edit_own";
							$edit_none_class = "res_pron_edit_none";
							
							$delete_all_class = "res_pron_delete_all";
							$delete_own_class = "res_pron_delete_own";
							$delete_none_class = "res_pron_delete_none";
						}
						
						if($module->id_client_module == 18) { // DESEMPEÑO DE PRONÓSTICO
							$view_all_class = "des_pron_view_all";
							$view_own_class = "des_pron_view_own";
							$view_none_class = "des_pron_view_none";
							
							$add_all_class = "des_pron_add_all";
							$add_own_class = "des_pron_add_own";
							$add_none_class = "des_pron_add_none";
							
							$edit_all_class = "des_pron_edit_all";
							$edit_own_class = "des_pron_edit_own";
							$edit_none_class = "des_pron_edit_none";
							
							$delete_all_class = "des_pron_delete_all";
							$delete_own_class = "des_pron_delete_own";
							$delete_none_class = "des_pron_delete_none";
						}
						
						if($module->id_client_module == 19) { // COMPARACIÓN DE PRONÓSTICOS
							$view_all_class = "comp_pron_view_all";
							$view_own_class = "comp_pron_view_own";
							$view_none_class = "comp_pron_view_none";
							
							$add_all_class = "comp_pron_add_all";
							$add_own_class = "comp_pron_add_own";
							$add_none_class = "comp_pron_add_none";
							
							$edit_all_class = "comp_pron_edit_all";
							$edit_own_class = "comp_pron_edit_own";
							$edit_none_class = "comp_pron_edit_none";
							
							$delete_all_class = "comp_pron_delete_all";
							$delete_own_class = "comp_pron_delete_own";
							$delete_none_class = "comp_pron_delete_none";
						}

						
						if($module->id_client_module == 17) { // MONITOREO
							$view_all_class = "monitoring_view_all";
							$view_own_class = "monitoring_view_own";
							$view_none_class = "monitoring_view_none";
							
							$add_all_class = "monitoring_add_all";
							$add_own_class = "monitoring_add_own";
							$add_none_class = "monitoring_add_none";
							
							$edit_all_class = "monitoring_edit_all";
							$edit_own_class = "monitoring_edit_own";
							$edit_none_class = "monitoring_edit_none";
							
							$delete_all_class = "monitoring_delete_all";
							$delete_own_class = "monitoring_delete_own";
							$delete_none_class = "monitoring_delete_none";
						}

						if($module->id_client_module == 20) { // CONDICIONES METEOROLÓGICAS
							$view_all_class = "cond_meteo_view_all";
							$view_own_class = "cond_meteo_view_own";
							$view_none_class = "cond_meteo_view_none";
							
							$add_all_class = "cond_meteo_add_all";
							$add_own_class = "cond_meteo_add_own";
							$add_none_class = "cond_meteo_add_none";
							
							$edit_all_class = "cond_meteo_edit_all";
							$edit_own_class = "cond_meteo_edit_own";
							$edit_none_class = "cond_meteo_edit_none";
							
							$delete_all_class = "cond_meteo_delete_all";
							$delete_own_class = "cond_meteo_delete_own";
							$delete_none_class = "cond_meteo_delete_none";
						}

					?>
					
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
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_all <?php echo $view_all_class; ?>" />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_view_own ".$view_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_none <?php echo $view_none_class; ?>" />
						</td>
						
						<?php if($module->id_client_submodule == 24){ ?>
						
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled />
							</td>
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled />
							</td>
							<td class="td_border" style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled />
							</td>
						
						<?php } else { ?>
						
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_all ".$add_all_class."'"; ?> />
							</td>
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete || $disabled_own) ? "disabled" : "class='air_add_own ".$add_own_class."'"; ?> />
							</td>
							<td class="td_border" style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_none ".$add_none_class."'"; ?> />
							</td>
						
						<?php } ?>
						
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_all ".$edit_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_edit_own ".$edit_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_none ".$edit_none_class."'"; ?> />
						</td>
						
						<?php if($module->id_client_submodule == 24){?>
						
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled />
							</td>
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled />
							</td>
							<td class="td_border" style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" disabled />
							</td>
						
						<?php } else { ?>
						
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_all ".$delete_all_class."'"; ?> />
							</td>
							<td style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_own ".$delete_own_class."'"; ?> />
							</td>
							<td class="td_border" style="text-align: center;">
								<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_none ".$delete_none_class."'"; ?> />
							</td>
						
						<?php } ?>
						
					<?php } else { ?>
											
						<td class="td_border"><?php echo $module->nombre_modulo; ?></td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_all <?php echo $view_all_class; ?>" />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_view_own ".$view_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_none <?php echo $view_none_class; ?>" />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_all ".$add_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete || $disabled_own) ? "disabled" : "class='air_add_own ".$add_own_class."'"; ?>/>
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_none ".$add_none_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_all ".$edit_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_edit_own ".$edit_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_none ".$edit_none_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_all ".$delete_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_own ".$delete_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_none ".$delete_none_class."'"; ?> />
						</td>
						
						

					<?php } ?>
				
				</tr>
                
            <?php } ?>



			<?php foreach ($air_modules as $key => $module) { ?>
            	
                <?php

                	$checked_ver_1 = "";
					$checked_ver_2 = "";
					$checked_ver_3 = "";
					
                    if ($module->id_profile) {
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
					
					//Deshabilitar radio a módulos que no tienen tableros para añadir, editar o borrar.
					if($module->id_client_submodule == 29 || $module->id_client_submodule == 36){
						$disabled = "disabled";
					} else {
						$disabled = "";
					}

					// Desactiva: Permiso VER: propios, Permiso AÑADIR: completo, Permiso EDITAR: propios y BORRAR: completo
					if($module->id_client_submodule == 27){
						$disabled_add_delete = "disabled";
					} else {
						$disabled_add_delete = "";
					}

					// Desactiva: Permiso AÑADIR: propios
					if($module->id_client_submodule == 30){
						$disabled_own = "disabled";
					} else {
						$disabled_own = "";
					}
					

				?>

					<?php if($module->id_client_module == 15) { // ADMINISTRACIÓN CLIENTE - CARGA MASIVA MIMAire ?>
						
						<?php if($module->id_client_submodule == 20 || $module->id_client_submodule == 21){ continue; } // SUBMÓDULOS CARGA MASIVA MIMASOFT ?>
						
						<?php if(!$cabecera_air_ac) { ?>
							<tr>
								<td style="background-color: #f0f0f0;" class="td_border text-center"><strong><?php echo $module->nombre_modulo; ?></strong></td>
								
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_ac_radio_select_view_all" name="air_ac_radio_select_view" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_ac_radio_select_view_own" name="air_ac_radio_select_view" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_ac_radio_select_view_none" name="air_ac_radio_select_view" /></strong></td>
							
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_ac_radio_select_add_all" name="air_ac_radio_select_add" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_ac_radio_select_add_own" name="air_ac_radio_select_add" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_ac_radio_select_add_none" name="air_ac_radio_select_add" /></strong></td>
								
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_ac_radio_select_edit_all" name="air_ac_radio_select_edit" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_ac_radio_select_edit_own" name="air_ac_radio_select_edit" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_ac_radio_select_edit_none" name="air_ac_radio_select_edit" /></strong></td>
								
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("all"); ?><br /><input type="radio" id="air_ac_radio_select_delete_all" name="air_ac_radio_select_delete" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="text-center"><strong><?php echo lang("own"); ?><br /><input type="radio" id="air_ac_radio_select_delete_own" name="air_ac_radio_select_delete" /></strong></td>
								<td style="background-color: #f0f0f0; font-size:10px;" class="td_border text-center"><strong><?php echo lang("none"); ?><br /><input type="radio" id="air_ac_radio_select_delete_none" name="air_ac_radio_select_delete" /></strong></td>
							</tr>
						<?php } ?>
						<?php $cabecera_air_ac = TRUE; ?>
						
					<?php } else { continue; } ?>

                <tr>
				
					<?php
					
						if($module->id_client_module == 15) { // ADMINISTRACIÓN CLIENTE
							$view_all_class = "air_ac_view_all";
							$view_own_class = "air_ac_view_own";
							$view_none_class = "air_ac_view_none";
							
							$add_all_class = "air_ac_add_all";
							$add_own_class = "air_ac_add_own";
							$add_none_class = "air_ac_add_none";
							
							$edit_all_class = "air_ac_edit_all";
							$edit_own_class = "air_ac_edit_own";
							$edit_none_class = "air_ac_edit_none";
							
							$delete_all_class = "air_ac_delete_all";
							$delete_own_class = "air_ac_delete_own";
							$delete_none_class = "air_ac_delete_none";
						}
						
					?>
					
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
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_all <?php echo $view_all_class; ?>" />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_view_own ".$view_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_ver" value="<?php echo $module->id_client_submodule; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_none <?php echo $view_none_class; ?>" />
						</td>
						
					
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_all ".$add_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete || $disabled_own) ? "disabled" : "class='air_add_own ".$add_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_agregar" value="<?php echo $module->id_client_submodule; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_none ".$add_none_class."'"; ?> />
						</td>
						
					
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_all ".$edit_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_edit_own ".$edit_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_editar" value="<?php echo $module->id_client_submodule; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_none ".$edit_none_class."'"; ?> />
						</td>
						

						
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_all ".$delete_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_own ".$delete_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_submodule; ?>-permisos_submodulo_borrar" value="<?php echo $module->id_client_submodule; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_none ".$delete_none_class."'"; ?> />
						</td>
						
						
						
					<?php } else { ?>
											
						<td class="td_border"><?php echo $module->nombre_modulo; ?></td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-1" <?php echo $checked_ver_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_all <?php echo $view_all_class; ?>" />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-2" <?php echo $checked_ver_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_view_own ".$view_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_ver" value="<?php echo $module->id_client_module; ?>-ver-3" <?php echo $checked_ver_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" class="air_view_none <?php echo $view_none_class; ?>" />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-1" <?php echo $checked_agregar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_all ".$add_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-2" <?php echo $checked_agregar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete || $disabled_own) ? "disabled" : "class='air_add_own ".$add_own_class."'"; ?>/>
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_agregar" value="<?php echo $module->id_client_module; ?>-agregar-3" <?php echo $checked_agregar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_add_none ".$add_none_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-1" <?php echo $checked_editar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_all ".$edit_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-2" <?php echo $checked_editar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_edit_own ".$edit_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_editar" value="<?php echo $module->id_client_module; ?>-editar-3" <?php echo $checked_editar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled) ? $disabled : "class='air_edit_none ".$edit_none_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-1" <?php echo $checked_eliminar_1; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_all ".$delete_all_class."'"; ?> />
						</td>
						<td style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-2" <?php echo $checked_eliminar_2; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_own ".$delete_own_class."'"; ?> />
						</td>
						<td class="td_border" style="text-align: center;">
							<input type="radio" name="<?php echo $module->id_client_module; ?>-permisos_modulo_borrar" value="<?php echo $module->id_client_module; ?>-borrar-3" <?php echo $checked_eliminar_3; ?> data-rule-required="true" data-msg-required="<?php echo lang("radio_required"); ?>" <?php echo ($disabled || $disabled_add_delete) ? "disabled" : "class='air_delete_none ".$delete_none_class."'"; ?> />
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
		
		// ---------- TABLA DE MÓDULOS Y SUBMÓDULOS DE PROYECTO ----------
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
		
		// HUELLAS AMBIENTALES
		$(document).on("click","#ha_radio_select_view_all", function(event){
			$('.ha_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ha_radio_select_view_own", function(event){
			$('.ha_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ha_radio_select_view_none", function(event){
			$('.ha_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// REGISTROS
		$(document).on("click","#rc_radio_select_view_all", function(event){
			$('.rc_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rc_radio_select_view_own", function(event){
			$('.rc_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rc_radio_select_view_none", function(event){
			$('.rc_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// REPORTES
		$(document).on("click","#rep_radio_select_view_all", function(event){
			$('.rep_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rep_radio_select_view_own", function(event){
			$('.rep_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rep_radio_select_view_none", function(event){
			$('.rep_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMPROMISOS
		$(document).on("click","#comp_radio_select_view_all", function(event){
			$('.comp_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comp_radio_select_view_own", function(event){
			$('.comp_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comp_radio_select_view_none", function(event){
			$('.comp_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// PERMISOS
		$(document).on("click","#perm_radio_select_view_all", function(event){
			$('.perm_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#perm_radio_select_view_own", function(event){
			$('.perm_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#perm_radio_select_view_none", function(event){
			$('.perm_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// RESIDUOS
		$(document).on("click","#res_radio_select_view_all", function(event){
			$('.res_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#res_radio_select_view_own", function(event){
			$('.res_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#res_radio_select_view_none", function(event){
			$('.res_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMUNIDADES
		$(document).on("click","#comu_radio_select_view_all", function(event){
			$('.comu_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comu_radio_select_view_own", function(event){
			$('.comu_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comu_radio_select_view_none", function(event){
			$('.comu_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// AYUDA Y SOPORTE
		$(document).on("click","#help_radio_select_view_all", function(event){
			$('.help_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#help_radio_select_view_own", function(event){
			$('.help_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#help_radio_select_view_none", function(event){
			$('.help_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ADMINISTRACIÓN CLIENTE
		$(document).on("click","#ac_radio_select_view_all", function(event){
			$('.ac_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ac_radio_select_view_own", function(event){
			$('.ac_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ac_radio_select_view_none", function(event){
			$('.ac_view_none').prop('checked', 'checked');
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
		
		// REGISTROS
		$(document).on("click","#rc_radio_select_add_all", function(event){
			$('.rc_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#rc_radio_select_add_own", function(event){
			$('.rc_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#rc_radio_select_add_none", function(event){
			$('.rc_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
			
		// COMPROMISOS
		$(document).on("click","#comp_radio_select_add_all", function(event){
			$('.comp_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#comp_radio_select_add_own", function(event){
			$('.comp_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#comp_radio_select_add_none", function(event){
			$('.comp_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// PERMISOS
		$(document).on("click","#perm_radio_select_add_all", function(event){
			$('.perm_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#perm_radio_select_add_own", function(event){
			$('.perm_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#perm_radio_select_add_none", function(event){
			$('.perm_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// RESIDUOS
		$(document).on("click","#res_radio_select_add_all", function(event){
			$('.res_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#res_radio_select_add_own", function(event){
			$('.res_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#res_radio_select_add_none", function(event){
			$('.res_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMUNIDADES
		$(document).on("click","#comu_radio_select_add_all", function(event){
			$('.comu_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#comu_radio_select_add_own", function(event){
			$('.comu_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#comu_radio_select_add_none", function(event){
			$('.comu_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMUNIDADES
		$(document).on("click","#comu_radio_select_add_all", function(event){
			$('.comu_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#comu_radio_select_add_own", function(event){
			$('.comu_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#comu_radio_select_add_none", function(event){
			$('.comu_add_none').prop('checked', 'checked');
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
		
		// REGISTROS
		$(document).on("click","#rc_radio_select_edit_all", function(event){
			$('.rc_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rc_radio_select_edit_own", function(event){
			$('.rc_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rc_radio_select_edit_none", function(event){
			$('.rc_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMPROMISOS
		$(document).on("click","#comp_radio_select_edit_all", function(event){
			$('.comp_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comp_radio_select_edit_own", function(event){
			$('.comp_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comp_radio_select_edit_none", function(event){
			$('.comp_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// PERMISOS
		$(document).on("click","#perm_radio_select_edit_all", function(event){
			$('.perm_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#perm_radio_select_edit_own", function(event){
			$('.perm_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#perm_radio_select_edit_none", function(event){
			$('.perm_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// RESIDUOS
		$(document).on("click","#res_radio_select_edit_all", function(event){
			$('.res_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#res_radio_select_edit_own", function(event){
			$('.res_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#res_radio_select_edit_none", function(event){
			$('.res_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMUNIDADES
		$(document).on("click","#comu_radio_select_edit_all", function(event){
			$('.comu_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comu_radio_select_edit_own", function(event){
			$('.comu_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comu_radio_select_edit_none", function(event){
			$('.comu_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// ADMINISTRACIÓN CLIENTE
		$(document).on("click","#ac_radio_select_edit_all", function(event){
			$('.ac_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ac_radio_select_edit_own", function(event){
			$('.ac_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#ac_radio_select_edit_none", function(event){
			$('.ac_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN EDITAR ----------
		
		
		// ---------- BORRAR ----------
		// GENERAL
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
		
		// REGISTROS
		$(document).on("click","#rc_radio_select_delete_all", function(event){
			$('.rc_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rc_radio_select_delete_own", function(event){
			$('.rc_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#rc_radio_select_delete_none", function(event){
			$('.rc_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// RESIDUOS
		$(document).on("click","#res_radio_select_delete_all", function(event){
			$('.res_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#res_radio_select_delete_own", function(event){
			$('.res_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#res_radio_select_delete_none", function(event){
			$('.res_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// COMUNIDADES
		$(document).on("click","#comu_radio_select_delete_all", function(event){
			$('.comu_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comu_radio_select_delete_own", function(event){
			$('.comu_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#comu_radio_select_delete_none", function(event){
			$('.comu_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		
		// ---------- TABLA DE MÓDULOS Y SUBMÓDULOS MIMair ----------
		// ---------- VER ----------
		// GENERAL
		$(document).on("click","#radio_air_select_view_all", function(event){
			$('.air_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_view_own", function(event){
			$('.air_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_view_none", function(event){
			$('.air_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});	
		
		// REGISTROS CALIDAD DEL AIRE
		$(document).on("click","#air_cda_radio_select_view_all", function(event){
			$('.cda_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_cda_radio_select_view_own", function(event){
			$('.cda_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_cda_radio_select_view_none", function(event){
			$('.cda_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// MONITOREO Y ALERTAS
		$(document).on("click","#air_mya_radio_select_view_all", function(event){
			$('.mya_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_mya_radio_select_view_own", function(event){
			$('.mya_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_mya_radio_select_view_none", function(event){
			$('.mya_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// PRONÓSTICO
		$(document).on("click","#air_pron_radio_select_view_all", function(event){
			$('.pron_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_pron_radio_select_view_own", function(event){
			$('.pron_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_pron_radio_select_view_none", function(event){
			$('.pron_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// RESUMEN DE PRONÓSTICOS
		$(document).on("click","#air_res_pron_radio_select_view_all", function(event){
			$('.res_pron_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_res_pron_radio_select_view_own", function(event){
			$('.res_pron_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_res_pron_radio_select_view_none", function(event){
			$('.res_pron_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// DESEMPEÑO DE PRONÓSTICO
		$(document).on("click","#air_des_pron_radio_select_view_all", function(event){
			$('.des_pron_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_des_pron_radio_select_view_own", function(event){
			$('.des_pron_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_des_pron_radio_select_view_none", function(event){
			$('.des_pron_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// COMPARACIÓN DE PRONÓSTICOS
		$(document).on("click","#air_comp_pron_radio_select_view_all", function(event){
			$('.comp_pron_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_comp_pron_radio_select_view_own", function(event){
			$('.comp_pron_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_comp_pron_radio_select_view_none", function(event){
			$('.comp_pron_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		})

		// CONDICIONES METEOROLÓGICAS
		$(document).on("click","#air_cond_meteo_radio_select_view_all", function(event){
			$('.cond_meteo_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_cond_meteo_radio_select_view_own", function(event){
			$('.cond_meteo_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_cond_meteo_radio_select_view_none", function(event){
			$('.cond_meteo_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		$(document).on("click","#air_cond_meteo_radio_select_edit_all", function(event){
			$('.cond_meteo_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#air_cond_meteo_radio_select_edit_own", function(event){
			$('.cond_meteo_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#air_cond_meteo_radio_select_edit_none", function(event){
			$('.cond_meteo_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});


		// MONITOREO
		$(document).on("click","#air_monitoring_radio_select_view_all", function(event){
			$('.monitoring_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_monitoring_radio_select_view_own", function(event){
			$('.monitoring_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_monitoring_radio_select_view_none", function(event){
			$('.monitoring_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		

		// ADMINISTRACIÓN CLIENTE
		$(document).on("click","#air_ac_radio_select_view_all", function(event){
			$('.air_ac_view_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_view_own", function(event){
			$('.air_ac_view_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_view_none", function(event){
			$('.air_ac_view_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN VER ----------

		
		// ---------- AÑADIR ----------
		// GENERAL
		$(document).on("click","#radio_air_select_add_all", function(event){
			$('.air_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_add_own", function(event){
			$('.air_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_add_none", function(event){
			$('.air_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// ADMINISTRACIÓN CLIENTE
		$(document).on("click","#air_ac_radio_select_add_all", function(event){
			$('.air_ac_add_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_add_own", function(event){
			$('.air_ac_add_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_add_none", function(event){
			$('.air_ac_add_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		// ---------- FIN AÑADIR ----------

		
		// ---------- EDITAR ----------
		// GENERAL
		$(document).on("click","#radio_air_select_edit_all", function(event){
			$('.air_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_edit_own", function(event){
			$('.air_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_edit_none", function(event){
			$('.air_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		// REGISTROS CALIDAD DEL air
		$(document).on("click","#air_cda_radio_select_edit_all", function(event){
			$('.cda_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_cda_radio_select_edit_own", function(event){
			$('.cda_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_cda_radio_select_edit_none", function(event){
			$('.cda_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// ADMINISTRACIÓN CLIENTE
		$(document).on("click","#air_ac_radio_select_edit_all", function(event){
			$('.air_ac_edit_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_edit_own", function(event){
			$('.air_ac_edit_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_edit_none", function(event){
			$('.air_ac_edit_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// ---------- BORRAR ----------
		// GENERAL
		$(document).on("click","#radio_air_select_delete_all", function(event){
			$('.air_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_delete_own", function(event){
			$('.air_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
		$(document).on("click","#radio_air_select_delete_none", function(event){
			$('.air_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});

		// ADMINISTRACIÓN CLIENTE
		$(document).on("click","#air_ac_radio_select_delete_all", function(event){
			$('.air_ac_delete_all').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_delete_own", function(event){
			$('.air_ac_delete_own').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		$(document).on("click","#air_ac_radio_select_delete_none", function(event){
			$('.air_ac_delete_none').prop('checked', 'checked');
			event.stopImmediatePropagation();
		});
		
    });
</script>