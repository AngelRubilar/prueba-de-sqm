<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='clientes' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "name",
            "name" => "name",
            "value" => $model_info->nombre_estado,
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
    <label for="evaluation_type" class="<?php echo $label_column; ?>"><?php echo lang('evaluation_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		
		$tipos = array(
		 	"" => "-",
			"rca" => lang("rca"),
			"reportable" => lang("reportable"),
		);
		
		echo form_dropdown("evaluation_type", $tipos, array($model_info->tipo_evaluacion), "id='tipos' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="categories" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		
		$categorias = array(
		 	"" => "-",
			"Cumple" => lang("fulfill"),
			"No Cumple" => lang("does_not_fulfill"),
			"Pendiente" => lang("pending"),
			"No Aplica" => lang("does_not_apply"),
		);
		
		echo form_dropdown("categoria", $categorias, array($model_info->categoria), "id='categories' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="color" class="<?php echo $label_column; ?>"><?php echo lang('color'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <div id="cp11" class="input-group colorpicker-component colorpicker-default">
        <?php
        echo form_input(array(
            "id" => "color",
            "name" => "color",
            "value" => ($model_info->color)?$model_info->color:'',
            "class" => "form-control",
            "placeholder" => lang('color'),
            "autocomplete"=> "off",
            //"readonly"=> true
			"data-rule-regex" => "^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$",
			"data-msg-regex" => lang('valid_color_code'),
        ));
        ?>
        <span class="input-group-addon"><i id="coloricon" style="border: solid black 1px;"></i></span>
        </div>
    </div>
    <label for="color" class="<?php echo $label_column; ?>"></label>
    <div class="<?php echo $field_column; ?>" style="text-align:center;">
   		<a id="default" title="Seleccionar color por defecto para el estado" href="#">Cancelar</a>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

		$('#compromises_compliance_status-form .select2').select2();
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#cp11').colorpicker({
            format: 'hex',
			extensions: [{
			  name: 'swatches',
			  colors: {
				'#000000': '#000000',
				'#ffffff': '#ffffff',
				'#FF0000': '#FF0000',
				'#777777': '#777777',
				'#337ab7': '#337ab7',
				'#5cb85c': '#5cb85c',
				'#5bc0de': '#5bc0de',
				'#f0ad4e': '#f0ad4e',
				'#d9534f': '#d9534f',
				'#8a6d3b': '#8a6d3b',
			  },
			  namesAsValues: true
			}],
			template: '<div class="colorpicker dropdown-menu"><div class="colorpicker-palette"></div><div class="colorpicker-color"><div /></div></div>'
        });
		
		$('#default').click(function(){
            $('#color').val("");
            $('#coloricon').css('background-color', '');
        });
		
    });
</script>