<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_reports_config_settings"), array("id" => "reports-config-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("reports_configuration"); ?></h4>
        </div>
        <div class="panel-body">
        
        <input type="hidden" id="id_report_config" name="id_report_config" value="<?php echo $reports_config_settings->id; ?>" />
        <input type="hidden" id="id_cliente_reports_config" name="id_cliente" />
        <input type="hidden" id="id_proyecto_reports_config" name="id_proyecto" />
        
          <table class="table">
                <thead>
                    <tr>
                        <th class="text-center"><?php echo lang("info"); ?></th>
                        <th class="text-center"><?php echo lang("status"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo lang("project_data"); ?></td>
                        <td class="text-center">
							<?php 
								echo form_checkbox('project_data', "1", ($reports_config_settings->project_data == 1) ? TRUE : FALSE); 
							?>
                         </td>
                    </tr>
					<tr>
                        <td><?php echo lang("ambiental_compromises"); ?></td>
                        <td class="text-center">
							<?php 
								echo form_checkbox('rca_compromises', "1", ($reports_config_settings->rca_compromises == 1) ? TRUE : FALSE); 
							?>
                         </td>
                    </tr>
					<tr>
                        <td><?php echo lang("ambiental_reportable_compromises"); ?></td>
                        <td class="text-center">
							<?php 
								echo form_checkbox('reportable_compromises', "1", ($reports_config_settings->reportable_compromises == 1) ? TRUE : FALSE); 
							?>
                         </td>
                    </tr>
                    <tr>
                        <td><?php echo lang("consumptions"); ?></td>
                        <td class="text-center">
							<?php 
								echo form_checkbox('consumptions', "1", ($reports_config_settings->consumptions == 1) ? TRUE : FALSE); 
							?>
                        </td>           
                    </tr>
                    <tr>
                        <td><?php echo lang("waste"); ?></td>
                        <td class="text-center">
							<?php 
								echo form_checkbox('waste', "1", ($reports_config_settings->waste == 1) ? TRUE : FALSE); 
							?>
                        </td>            
                    </tr>
                    <tr>
                        <td><?php echo lang("ambiental_permissions"); ?></td>
                        <td class="text-center">
							<?php 
								echo form_checkbox('permittings', "1", ($reports_config_settings->permittings == 1) ? TRUE : FALSE); 
							?>
                         </td>
                    </tr>
                </tbody>
            </table>
			
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse0" data-parent="#accordion" class="accordion-toggle">
                            	<h3 style="font-size:16px" ><i class="fa fa-plus-circle font-16"></i> <?php echo lang('materials'); ?></h3>
                            </a>
                         </h4>
                    </div>
                    <div id="collapse0" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php $array_materials = json_decode($reports_config_settings->materials); ?>
                            
                            <table class="table">
                            	<thead>
                                    <th class="text-center"><?php echo lang("info"); ?></th>
                                    <th class="text-center"><?php echo lang("status"); ?></th>
                                </thead>
                                <tbody>
                                	<?php 
										foreach($materiales as $material) { //bd fc (materiales del proyecto)
									 
											$id_material = $material->id;
											$habilitado = 0;
											
											foreach($array_materials as $mat){ //bd mimasoft (materiales de la configuracion)
												if($mat->id == $id_material){
													$habilitado = $mat->estado;
												} 
											}
									?>
                                        <tr>
                                            <td><?php echo $material->nombre?></td>
                                            <td class="text-center">
												<?php 
													echo form_checkbox('materials[]', $id_material, ($habilitado == 1) ? TRUE : FALSE, "class='checkbox_materials'"); 
											 	?>
                                               
                                            </td>
                                        </tr>
									<?php } ?>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            
            </div>

            
        </div>
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button id="btn_guardar" type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
		var id_cliente = $('#client').val();
		var id_proyecto = $('#project').val();
		$('#id_cliente_reports_config').val(id_cliente);
		$('#id_proyecto_reports_config').val(id_proyecto);
		
		$("#reports-config-form").appForm({
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
				$('#id_report_config').val(result.save_id);

                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
            }
        });
		
		
		
    });
</script>