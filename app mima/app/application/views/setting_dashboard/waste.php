<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("setting_dashboard/save_waste"), array("id" => "waste-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("waste"); ?></h4>
        </div>
        <div class="panel-body">
   			<div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <th class="text-center"><?php echo lang("info"); ?></th>
                        <th class="text-center">
                            <label for="chk_waste_table_all" ><b><?php echo lang('table'); ?></b></label><br>
							<?php
                            echo form_checkbox("chk_waste_table_all", "1", $model_info->obligatorio ? true : false, "id='chk_waste_table_all'");
                            ?>                       
                        </th>
                        <th class="text-center">
                            <label for="chk_waste_graphic_all" ><b><?php echo lang('graphic'); ?></b></label><br>
							<?php
                            echo form_checkbox("chk_waste_graphic_all", "1", "", "id='chk_waste_graphic_all'");
                            ?>                       
                        </th>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias_proyecto_form_residuo as $cat){?>
                        	<?php 
								$alias_categoria = $Categories_alias_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_categoria" => $cat->id_categoria, "deleted" => 0)); 
							?>
                        
                            <tr>
                                <td><?php echo $alias_categoria->alias ? $alias_categoria->alias : $cat->nombre; ?></td>
                                <!--
                                <td>
                                    <input type="hidden" name="waste_enabled[<?php echo $cat->id_categoria?>]" value="0-<?php echo $cat->id_form?>"/>
                                    <?php  
                                        echo form_checkbox('waste_enabled['.$cat->id_categoria.']', "1-".$cat->id_form, ($cat->habilitado == 1) ? TRUE : FALSE); 
                                    ?>
                                </td>
                                -->
                                <td style="text-align: center;">
                                    <input type="hidden" name="waste_table[<?php echo $cat->id_categoria?>]" value="0-<?php echo $cat->id_form?>"/>
                                    <?php 
                                        echo form_checkbox('waste_table['.$cat->id_categoria.']', "1-".$cat->id_form, ($cat->tabla == 1) ? TRUE : FALSE, "class='chk_waste_table'"); 
                                    ?>
                                </td>
                                <td style="text-align: center;">
                                    <input type="hidden" name="waste_graphic[<?php echo $cat->id_categoria?>]" value="0-<?php echo $cat->id_form?>"/>
                                    <?php 
                                        echo form_checkbox('waste_graphic['.$cat->id_categoria.']', "1-".$cat->id_form, ($cat->grafico == 1) ? TRUE : FALSE, "class='chk_waste_graphic'"); 
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button id="btn_save_waste" type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {

		$("#waste-form").appForm({
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
		
		if($('.chk_waste_table:checked').length == $('.chk_waste_table').length){
			$('#chk_waste_table_all').prop('checked', true);
		}
		
		if($('.chk_waste_graphic:checked').length == $('.chk_waste_graphic').length){
			$('#chk_waste_graphic_all').prop('checked', true);
		}
		
		$('#chk_waste_table_all').change(function(){
			$('.chk_waste_table').prop('checked', $(this).is(':checked'));
		});
		
		$('#chk_waste_graphic_all').change(function(){
			$('.chk_waste_graphic').prop('checked', $(this).is(':checked'));
		});
		
		<?php if($puede_editar != 1) { ?>
			$('#waste-form input[type=checkbox]').attr('disabled','true');
			$('#btn_save_waste').attr('disabled','true');	
		<?php } ?>

    });
</script>