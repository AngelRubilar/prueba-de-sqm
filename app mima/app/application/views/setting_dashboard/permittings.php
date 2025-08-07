<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("setting_dashboard/save_permittings"), array("id" => "permittings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("permittings"); ?></h4>
        </div>
		<div class="panel-body">
           <div class="form-group">
			   <table class="table">
					<thead>
						<th class="text-center"><?php echo lang("info"); ?></th>
						<th class="text-center"><?php echo lang("enabled"); ?></th>
					</thead>
					<tbody>
						<?php if($client_permitting_settings) { ?>
							<tr>
								<td><?php echo lang("table"); ?></td>
								<td class="text-center">
									<input type="hidden" name="table_enabled" value="0"/>
									<?php 
										echo form_checkbox('table_enabled', "1", ($client_permitting_settings->tabla == 1) ? TRUE : FALSE); 
									?>
								</td>
							</tr>
							<tr>
								<td><?php echo lang("graphs"); ?></td>
								<td class="text-center">
									<input type="hidden" name="graphs_enabled" value="0"/>
									<?php 
										echo form_checkbox('graphs_enabled', "1", ($client_permitting_settings->grafico == 1) ? TRUE : FALSE); 
									?>
								</td>
							</tr>
						<?php } else { ?>
							<tr>
								<td><?php echo lang("table"); ?></td>
								<td class="text-center">
									<?php 
										echo form_checkbox('table_enabled', "1", ($client_permitting_settings->tabla == 1) ? TRUE : FALSE); 
									?>
								</td>
							</tr>
							<tr>
								<td><?php echo lang("graphs"); ?></td>
								<td class="text-center">
									<?php 
										echo form_checkbox('graphs_enabled', "1", ($client_permitting_settings->grafico == 1) ? TRUE : FALSE); 
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table> 
           </div>
        </div>
		<input type="hidden" id="permitting_setting_id" name="permitting_setting_id" value="<?php echo $client_permitting_settings->id; ?>" />
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button type="submit" id="btn_save_permittings" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {

		$("#permittings-form").appForm({
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
		
		<?php if($puede_editar != 1) { ?>
			$('#permittings-form input[type=checkbox]').attr('disabled','true');
			$('#btn_save_permittings').attr('disabled','true');	
		<?php } ?>

    });
</script>