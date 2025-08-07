<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("general_settings/save_transformation_factors"), array("id" => "transformation_factors-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("transformation_factors"); ?></h4>
        </div>
        <div class="panel-body">
        	
           <input type="hidden" id="id_cliente_transformation_factors" name="id_cliente" />
            
           <table class="table table-bordered">
            	<thead>
                	<tr>
                    	<th class="text-center"><?php echo lang("category"); ?></th>
                        <th class="text-center"><?php echo lang("conversion_to_mass"); ?></th>
                        <th class="text-center"><?php echo lang("efficiency"); ?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php foreach($categorias_proyectos as $categoria) { ?>
                    <tr>
                        <td>
							<?php echo $categoria["nombre_categoria"]." (".$categoria["nombre_tipo_unidad"].")"; ?>
                        </td>
                        <td>
                            <div class="col-md-6">
								<?php if($categoria["id_tipo_unidad"] == 1){ ?>
                                    1
                                <?php } else { ?>
                                <?php
                                    echo form_input(array(
                                        "id" => "valor_factor_transformacion-".$categoria['id_categoria']."-".$categoria["id_tipo_unidad"],
                                        "name" => "valor_factor_transformacion[".$categoria['id_categoria']."][".$categoria['id_tipo_unidad']."]",
                                        "value" => $categoria['valor_factor_transformacion'],
                                        "class" => "form-control",
                                        "placeholder" => lang('conversion_to_mass'),
                                        "autofocus" => true,
                                        //"data-rule-required" => true,
                                        //"data-msg-required" => lang("field_required"),
                                        "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                                        "data-msg-regex" => lang("number_or_decimal_required"),
                                        "autocomplete" => "off",
                                        //"maxlength" => "255"
                                    ));
                                ?>
                                <?php } ?>
                            </div>
                            
                            <div class="col-md-6">
                                <div id="etiqueta_unidad-<?php echo $categoria['id_categoria']."-".$categoria["id_tipo_unidad"]; ?>">
                                    <?php 
										if($categoria["id_tipo_unidad"] == 1) { // Masa
                                            echo "t"; 
                                        } elseif($categoria["id_tipo_unidad"] == 2) { // Volumen
                                            echo "t/m3"; 
                                        } elseif($categoria["id_tipo_unidad"] == 3) { // Transporte
                                            echo "t/tkm";
                                        } elseif($categoria["id_tipo_unidad"] == 4) { // Energía
                                            
                                            $html = "<div class='col-md-4' style='padding: 0px;'>";
                                            $html .= "t/Mwh";
                                            $html .= "</div>";
                                            $html .= "<div class='col-md-5' style='padding: 0px;'>";
                                            $html .= form_input(array(
                                                        "id" => "ren-".$categoria['id_categoria']."-".$categoria["id_tipo_unidad"],
                                                        "name" => "ren[".$categoria['id_categoria']."][".$categoria['id_tipo_unidad']."]",
                                                        "value" => $categoria['ren'],
                                                        "class" => "form-control",
                                                        "placeholder" => lang('ren'),
                                                        "autofocus" => true,
                                                        //"data-rule-required" => true,
                                                        //"data-msg-required" => lang("field_required"),
                                                        "data-rule-regex" => "^([0-9]|[1-9][0-9]|100)$",
                                                        "data-msg-regex" => lang("number_0_to_100_required"),
                                                        "autocomplete" => "off",
                                                        //"maxlength" => "255"
                                                    ));
                                            $html .= "</div>";
                                            $html .= "<div class='col-md-3 text-left'>";
                                            $html .= "%";
                                            $html .= "</div>";
                                            
                                            echo $html;
                                            
                                        } elseif($categoria["id_tipo_unidad"] == 9) {
                                            echo "t";
                                        } else {
                                            echo "";
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                        	<div class="col-md-8" style="padding-right: 0px;">
								<?php
                                    echo form_input(array(
                                        "id" => "eficiencia-".$categoria['id_categoria']."-".$categoria["id_tipo_unidad"],
                                        "name" => "eficiencia[".$categoria['id_categoria']."][".$categoria['id_tipo_unidad']."]",
                                        "value" => $categoria['eficiencia'],
                                        "class" => "form-control",
                                        "placeholder" => lang('efficiency'),
                                        "autofocus" => true,
                                        //"data-rule-required" => true,
                                        //"data-msg-required" => lang("field_required"),
                                        "data-rule-regex" => "^([0-9]|[1-9][0-9]|100)$",
										"data-msg-regex" => lang("number_0_to_100_required"),
                                        "autocomplete" => "off",
                                        //"maxlength" => "255"
                                    ));
                                ?>
                        	</div>
                            <div class="col-md-4" style="padding-left: 0px;">%</div>     
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

		$('#transformation_factors-form .select2').select2();
		
		var id_cliente = $('#client').val();
        $('#id_cliente_transformation_factors').val(id_cliente);

        $("#transformation_factors-form").appForm({
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
		
		/*		
		$(document).on('change','.tipo_unidad',function(){
			
			var id_tipo_unidad = $(this).val();
			var id_categoria = $(this).attr("data-id_categoria");

			$.ajax({
				url: '<?php echo_uri("general_settings/get_unit_type_label"); ?>',
				type: 'post',
				data: {id_tipo_unidad:id_tipo_unidad, id_categoria:id_categoria},
				success: function(respuesta){
					$('#etiqueta_unidad-' + id_categoria).html(respuesta);
				}
			});
			
		});
		*/
		
    });
</script>