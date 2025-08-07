<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->nombre,
            "class" => "form-control",
            "placeholder" => lang('name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="tipo_unidad" class="<?php echo $label_column; ?>"><?php echo lang('unit_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("tipo_unidad", $tipos_unidad, array($model_info->id_tipo_unidad), "id='tipo_unidad' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
    <label for="indicador" class="<?php echo $label_column; ?>"><?php echo lang('indicator'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "indicador",
            "name" => "indicador",
            "value" => $model_info->indicador,
            "class" => "form-control",
            "placeholder" => lang('indicator'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
	<label for="icono" class="<?php echo $label_column; ?>"><?php echo lang("icon"); ?></label>
	<div class="<?php echo $field_column; ?>">
        <select name="icono" id="icono" class="select2 validate-hidden" data-rule-required="true" ,="" data-msg-required="Este campo es requerido." tabindex="-1" title="iconos" aria-required="true">
            <option value="">-</option>
			<?php foreach($iconos as $icono) { ?>
            	<?php if($icono == 'empty.png'){
					continue;
				}?>
                <option value="<?php echo $icono ?>" ><?php echo $icono ?></option>
            <?php } ?>
        </select>
	</div>
</div>

<div class="form-group">
    <label for="abreviatura" class="<?php echo $label_column; ?>"><?php echo lang('abbreviation'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "abreviatura",
            "name" => "abreviatura",
            "value" => $model_info->abreviatura,
            "class" => "form-control",
            "placeholder" => lang('abbreviation'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="description" class=" col-md-3"><?php echo lang('description'); ?></label>
    <div class=" col-md-9">
        <?php
        echo form_textarea(array(
            "id" => "description",
            "name" => "description",
            "value" => $model_info->descripcion,
            "class" => "form-control",
            "placeholder" => lang('description'),
            "autocomplete"=> "off",
			"maxlength" => "2000"
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $('[data-toggle="tooltip"]').tooltip();
		$('#footprints-form .select2').select2();
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$("#icono").select2().select2("val", '<?php echo $model_info->icono; ?>');
		
		function format(state) {
			console.log(state.text);
			if(state.text != '-'){
				return "<img class='' heigth='20' width='20' src='/assets/images/impact-category/" + state.text + "'/>" + "&nbsp;&nbsp;" + state.text;
			}else{
				return state.text;
			}
			
		}
		
		$("#icono").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		
		
		$('#tipo_unidad').change(function(){
			
			var tipo_unidad = $(this).val();
			select2LoadingStatusOn($('#unit'));
			
			$.ajax({
				url:  '<?php echo_uri("footprints/get_units_of_unit_type") ?>',
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