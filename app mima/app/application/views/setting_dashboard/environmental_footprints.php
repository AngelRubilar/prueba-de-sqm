<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("setting_dashboard/save_environmental_footprints"), array("id" => "environmental-footprints-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("environmental_footprints"); ?></h4>
        </div>
		<div class="panel-body">
        	
            <input type="hidden" id="id_impactos_totales" name="id_impactos_totales" value="<?php echo $id_impactos_totales; ?>" />
            <input type="hidden" id="id_impactos_por_uf" name="id_impactos_por_uf" value="<?php echo $id_impactos_por_uf; ?>" />
            <!-- <input type="hidden" id="id_impactos_por_categoria" name="id_impactos_por_categoria" value="<?php echo $id_impactos_por_categoria; ?>" /> -->
            
           <div class="form-group">
           
           <table class="table">
                <thead>
                    <th class="text-center"><?php echo lang("info"); ?></th>
                    <th class="text-center"><?php echo lang("enabled"); ?></th>
                </thead>
                <tbody>
                
                	<?php if($client_environmental_footprints_settings) { ?>
                
                		<?php foreach ($client_environmental_footprints_settings as $index => $setting) { ?> <!-- $index 0, 1, 2 -->
                            <tr>
                                <td><?php echo lang($setting->informacion); ?></td>
                                
                                	<?php if($index == 0 ) { ?>
                                        <td class="text-center">
                                            <?php 
                                                echo form_checkbox('total_impacts_enabled', "1", ($setting->habilitado == 1) ? TRUE : FALSE); 
                                            ?>
                                        </td>
                                    <?php } ?>
                                    
                                    <?php if($index == 1) { ?>
                                        <td class="text-center">
                                            <?php 
                                                echo form_checkbox('impacts_by_functional_units_enabled', "1", ($setting->habilitado == 1) ? TRUE : FALSE); 
                                            ?>
                                        </td>
                                    <?php } ?>
                            </tr>
                    	<?php } ?>
        		
                	<?php } else { ?>
                    
                        <tr>
                            <td><?php echo lang("total_impacts"); ?></td>
                            <td class="text-center">
                                <?php 
                                    echo form_checkbox('total_impacts_enabled', "1", ($environmental_footprints_settings->enabled == 1) ? TRUE : FALSE); 
                                ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><?php echo lang("impacts_by_functional_units"); ?></td>
                            <td class="text-center">
                                <?php 
                                    echo form_checkbox('impacts_by_functional_units_enabled', "1", ($environmental_footprints_settings->enabled == 1) ? TRUE : FALSE); 
                                ?>
                            </td>
                            
                        </tr>
                        
                    <?php } ?>
                </tbody>
            </table>
                
           </div>
           
        </div>
            
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button type="submit" id="btn_save_env_footprint" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
		$("#environmental-footprints-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
									
                    if (obj.name === "invoice_logo" || obj.name === "site_logo") {
                        var image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = image;
                    }
                });
            },
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
				$('#id_impactos_totales').val(result.id_impactos_totales);
				$('#id_impactos_por_uf').val(result.id_impactos_por_uf);
				$('#id_impactos_por_categoria').val(result.id_impactos_por_categoria);
            }
        });
		<?php if($puede_editar != 1) { ?>
			$('#environmental-footprints-form input[type=checkbox]').attr('disabled','true');
			$('#btn_save_env_footprint').attr('disabled','true');		
		<?php } ?>
    });
</script>