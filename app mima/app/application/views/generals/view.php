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
                        <th colspan="3" class="th_border" style="text-align: center;"><?php echo lang("to_audit"); ?></th>
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
                        <th><?php echo lang("all"); ?></th>
                    	<th><?php echo lang("own"); ?></th>
                    	<th class="th_border"><?php echo lang("none"); ?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach ($clients_modules as $key => $module) { ?>
						<?php 
                            if ($module->id_client_context_profile) {
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
								$checked_auditar_1 = ($module->auditar == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_auditar_2 = ($module->auditar == 2) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                                $checked_auditar_3 = ($module->auditar == 3) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "";
                            }
                        ?>
                        
                        <?php if($module->contexto == "agreements_territory") { ?>
							<?php if(!$cabecera_agreements_territory) { ?>
                                <tr>
                                    <td colspan="16" style="background-color: #ddd;"><strong><?php echo lang("agreements") . " " . lang("territory"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_agreements_territory = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->contexto == "agreements_distribution") { ?>
                            <?php if(!$cabecera_agreements_distribution) { ?>
                                <tr>
                                    <td colspan="16" style="background-color: #ddd;"><strong><?php echo lang("agreements") . " " . lang("distribution"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_agreements_distribution = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->contexto == "recordbook") { ?>
                            <?php if(!$cabecera_recordbook) { ?>
                                <tr>
                                    <td colspan="16" style="background-color: #ddd;"><strong><?php echo lang("recordbook"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_recordbook = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->contexto == "help_and_support") { ?>
                            <?php if(!$cabecera_help_and_support) { ?>
                                <tr>
                                    <td colspan="16" style="background-color: #ddd;"><strong><?php echo lang("help_and_support"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_help_and_support = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->contexto == "kpi") { ?>
                            <?php if(!$cabecera_kpi) { ?>
                                <tr>
                                    <td colspan="16" style="background-color: #ddd;"><strong><?php echo lang("kpi"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_kpi = TRUE; ?>
                        <?php } ?>
                        
                        <?php if($module->contexto == "circular_economy") { ?>
							<?php if(!$cabecera_economia_circular) { ?>
                                <tr>
                                    <td colspan="16" style="background-color: #ddd;"><strong><?php echo lang("circular_economy"); ?></strong></td>
                                </tr>
                            <?php } ?>
                            <?php $cabecera_economia_circular = TRUE; ?>
                        <?php } ?>
                        
                        <tr>
                        
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
                                <td style="text-align: center;">
                                    <?php echo $checked_auditar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_auditar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_auditar_3; ?>
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
                                <td style="text-align: center;">
                                    <?php echo $checked_auditar_1; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $checked_auditar_2; ?>
                                </td>
                                <td class="td_border" style="text-align: center;">
                                    <?php echo $checked_auditar_3; ?>
                                </td>
                                
							<?php } ?>
                            
                        </tr>   
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
		width:90% !important;
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