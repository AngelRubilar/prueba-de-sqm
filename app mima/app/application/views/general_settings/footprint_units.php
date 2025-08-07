<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_unity_settings"), array("id" => "module-footprints-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("footprint_units"); ?></h4>
        </div>
        <div class="panel-body">
            
            <input type="hidden" id="id_module_footprints_units" name="id_module_footprints_units" value="<?php echo $module_footprints_units->id; ?>" />
            <input type="hidden" id="id_cliente_footprints_units" name="id_cliente" />
            <input type="hidden" id="id_proyecto_footprints_units" name="id_proyecto" />
            
			<?php foreach($tipos_de_unidad as $tipo_unidad){?>  
            	<?php if($tipo_unidad->id == 1 || $tipo_unidad->id == 2 || $tipo_unidad->id == 7) { ?>
                    <div class="form-group">                 
                        <label for="unidad" class="col-md-2"><?php echo $tipo_unidad->nombre ?></label>
                        <div class="col-md-10">
                            <input type="hidden" name="unidad[<?php echo $tipo_unidad->id?>]" value="0"/>
                            <?php 
    
                                $unidades = $this->Unity_model->get_dropdown_list(array("nombre"), "id", array("id_tipo_unidad" => $tipo_unidad->id));       
                                $unidad = $this->Module_footprint_units_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => $tipo_unidad->id));  
                                echo form_dropdown(
                                    "unidad[".$tipo_unidad->id."]",
                                     $unidades, 
                                     array($unidad->id_unidad), "id='unidad' class='select2 mini validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'"); 
                            ?>
    
                        </div>
                    </div>
            	<?php } ?>
            <?php } ?>

        </div>
        <br/><br/>

            
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
        var id_cliente = $('#client').val();
        var id_proyecto = $('#project').val();
        
        $('#id_cliente_footprints_units').val(id_cliente);
        $('#id_proyecto_footprints_units').val(id_proyecto);
        
        $('#general_color').colorpicker({
            format: 'hex'   
        });
        
        $("#module-footprints-form .select2").select2();
        $("#general-settings-form .select2").select2();
        
        
        $("#module-footprints-form").appForm({
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

                $('#id_module_footprints_units').val(result.save_id);

                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
            }
        });
        
    });
</script>