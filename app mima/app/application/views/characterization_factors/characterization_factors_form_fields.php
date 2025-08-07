<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group" id="">
    <label for="database" class="<?php echo $label_column; ?>"><?php echo lang('databases'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("database", array("" => "-") + $bases_de_datos, array($model_info->id_bd), "id='database' class='select2 validate-hidden' data-sigla='' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="footprint_format" class=" col-md-3"><?php echo lang('footprint_format'); ?></label>
    <div class="col-md-9">
        <?php
        echo form_dropdown("footprint_format", $formato_huellas, $model_info->id_formato_huella, "id='id_footprint_format' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group" id="methodologies_group">
    <!--<label for="calculation_methodology" class="<?php echo $label_column; ?>"><?php echo lang('calculation_methodology'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("calculation_methodology",array("" => "-") + $metodologias, array($model_info->id_metodologia), "id='calculation_methodology' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>-->
    
    <div id="methodologies_group">
        <div class="form-group">
            <label for="id_methodology" class=" col-md-3"><?php echo lang('calculation_methodology'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("id_methodology", array("" => "-") + $metodologias, $model_info->id_metodologia, "id='metodologiaCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>
    </div>
    
</div>

<div class="form-group">
    <label for="environmental_footprint" class="<?php echo $label_column; ?>"><?php echo lang('environmental_footprint'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("environmental_footprint", array("" => "-") + $huellas, array($model_info->id_huella), "id='environmental_footprint' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="materials" class="<?php echo $label_column; ?>"><?php echo lang('materials'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("materials", array("" => "-") + $materiales, array($model_info->id_material), "id='materials' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group" id="categoria">
    <label for="category" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("category", $categorias, array($model_info->id_categoria), "id='category' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div id="subcategoria_group">
    <div class="form-group">
        <label for="subcategory" class="<?php echo $label_column; ?>"><?php echo lang('subcategory'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("subcategory", array("" => "-") + $subcategorias, array($model_info->id_subcategoria), "id='subcategory' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div> 
    </div>
</div>

<div class="form-group">
    <label for="unit_type" class="<?php echo $label_column; ?>"><?php echo lang('unit_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("unit_type", array("" => "-") +  $tipos_de_unidad, array($model_info->id_tipo_unidad), "id='unit_type' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div> 
</div>

<div id="unit_group">
    <div class="form-group">
        <label for="unit" class="<?php echo $label_column; ?>"><?php echo lang('unit'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("unit", $unidades, array($model_info->id_unidad), "id='unit' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div> 
    </div>
</div>

<div class="form-group">
    <label for="factor" class="<?php echo $label_column; ?>"><?php echo lang('factor'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "factor",
            "name" => "factor",
            "type" => "text",
            "value" => $model_info->factor,
            "class" => "form-control",
            "placeholder" => lang('factor'),
            "data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"data-rule-number" => true,
			"data-msg-number" => lang("enter_a_number"),
            "autocomplete"=> "off",
			//"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
		
		$('#characterization_factors-form .select2').select2();
		
		$('input[type="number"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#id_footprint_format').change(function(){
			
			var id_footprint_format = $(this).val();
			select2LoadingStatusOn($('#metodologiaCH'));
			
			$.ajax({
				url:  '<?php echo_uri("projects/get_methodologies_of_fh") ?>',
				type:  'post',
				data: {id_footprint_format:id_footprint_format},
				//dataType:'json',
				success: function(respuesta){
					$('#methodologies_group').html(respuesta);    
					$("#characterization_factors-form #metodologiaCH").select2();
				}
				
			});
		});

        $('#materials').change(function(){  
                   
            var id_material = $(this).val(); 
			select2LoadingStatusOn($('#category')); 
                    
            $.ajax({
                url:  '<?php echo_uri("characterization_factors/get_category_of_material") ?>',
                type:  'post',
                data: {id_material:id_material},
                //dataType:'json',
                success: function(respuesta){
                    $('#categoria').html(respuesta);
                    $('#category').select2();
                }
            });
         
        }); 		
		
		$(document).off().on('change', '#category', function(){
			
			var categoria = $(this).val(); 
			select2LoadingStatusOn($('#subcategory'));

			$.ajax({
				url:  '<?php echo_uri("categories/get_subcategories_of_category") ?>',
				type:  'post',
				data: {categoria:categoria},
				//dataType:'json',
				success: function(respuesta){
					$('#subcategoria_group').html(respuesta);
                    $('#subcategory').select2();
				}
			});
			
		});
		
		$('#unit_type').change(function(){
			
			var tipo_unidad = $(this).val(); 
			select2LoadingStatusOn($('#unit'));
			
			$.ajax({
				url:  '<?php echo_uri("characterization_factors/get_units_of_unit_type") ?>',
				type:  'post',
				data: {tipo_unidad:tipo_unidad},
				//dataType:'json',
				success: function(respuesta){
					$('#unit_group').html(respuesta);
                    $('#unit').select2();
				}
			});
			
		});

    });
</script>