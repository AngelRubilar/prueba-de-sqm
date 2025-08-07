<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_report_units_settings"), array("id" => "report_units-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("report_units"); ?></h4>
        </div>
        <div class="panel-body">
            
            <input type="hidden" id="id_report_units_settings" name="id_report_units_settings" value="<?php echo $reports_units_settings->id; ?>" />
            <input type="hidden" id="id_cliente_report_units_settings" name="id_cliente_report_units_settings" />
            <input type="hidden" id="id_proyecto_report_units_settings" name="id_proyecto_report_units_settings" />
            
            <div class="col-md-6">
                <?php foreach($tipos_de_unidad as $tipo_unidad){?>  
                    <div class="form-group">                 
                        <label for="unidad" class="col-md-2"><?php echo $tipo_unidad->nombre ?></label>
                        <div class="col-md-10">
                            <input type="hidden" name="unidad[<?php echo $tipo_unidad->id?>]" value="0"/>
                            <?php 

                                $unidades = $this->Unity_model->get_dropdown_list(array("nombre"), "id", array("id_tipo_unidad" => $tipo_unidad->id));       
                                $unidad = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => $tipo_unidad->id));  
                                echo form_dropdown(
                                    "unidad_report_units_settings[".$tipo_unidad->id."]",
                                    $unidades, 
                                    array($unidad->id_unidad), "id='unidad' class='select2 mini validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'"); 
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <?php foreach($tipos_de_unidad_air as $tipo_unidad){?>  
                    <div class="form-group">                 
                        <label for="unidad" class="col-md-2"><?php echo $tipo_unidad->nombre ?></label>
                        <div class="col-md-10">
                            <input type="hidden" name="unidad[<?php echo $tipo_unidad->id?>]" value="0"/>
                            <?php 

                                $unidades = $this->Unity_model->get_dropdown_list(array("nombre"), "id", array("id_tipo_unidad" => $tipo_unidad->id));       
                                $unidad = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => $tipo_unidad->id));  
                                echo form_dropdown(
                                    "unidad_report_units_settings[".$tipo_unidad->id."]",
                                    $unidades, 
                                    array($unidad->id_unidad), "id='unidad' class='select2 mini validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'"); 
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
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
        
        $('#id_cliente_report_units_settings').val(id_cliente);
        $('#id_proyecto_report_units_settings').val(id_proyecto);

		$("#report_units-form .select2").select2();

        $("#report_units-form").appForm({
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

                $('#id_report_units_settings').val(result.save_id);

                appAlert.success(result.message, {duration: 10000});
                if ($("#site_logo").val() || $("#invoice_logo").val()) {
                    location.reload();
                }
            }
        });
        
    });
</script>