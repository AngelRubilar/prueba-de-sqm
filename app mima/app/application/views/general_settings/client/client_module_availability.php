<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_client_module_availability_settings"), array("id" => "client_module_availability-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("module_availability"); ?></h4>
        </div>
        <div class="panel-body">
			
            <input type="hidden" id="id_cliente_module_availability" name="id_cliente" />
            
            <table class="table">
            	<thead>
                	<tr>
                    	<th class="text-center"><?php echo lang("info"); ?></th>
                        <th class="text-center"><?php echo lang("status"); ?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach($client_module_availability_settings as $mod){?>
                    	<?php 
							$contexto = "";
							if ($mod->contexto == "agreements_territory") { 
								$contexto = "(" . lang("agreements") . " - " . lang("territory") . ")";
                    		} 
                         	if ($mod->contexto == "agreements_distribution") {
								$contexto = "(" . lang("agreements") . " - " . lang("distribution") . ")";
                    	 	}
							if ($mod->contexto == "recordbook") {
								$contexto = "(" . lang("recordbook") . ")";
                    	 	}
						?>
                        <tr>
                            <td><?php echo $mod->name . " " . $contexto; ?></td>
                            <td class="text-center">
                                <input type="hidden" name="clients_modules_availability[<?php echo $mod->id?>]" value="0"/>
                                <?php
                                    $checked = ($mod->disponible) ? TRUE : FALSE;
                                    echo form_checkbox("clients_modules_availability[".$mod->id."]", "1", $checked);
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            
        </div>
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>

</div>
<script type="text/javascript">
    $(document).ready(function () {
		
		var id_cliente = $('#client').val();
		$('#id_cliente_module_availability').val(id_cliente);
		
		$("#client_module_availability-form").appForm({
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
            }
        });
		
    });
</script>