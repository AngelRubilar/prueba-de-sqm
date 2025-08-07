<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="unit_process_name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "unit_process_name",
            "name" => "unit_process_name",
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
   		<a id="default" title="Seleccionar color por defecto para los gráficos de reporte" href="#">Cancelar</a>
    </div>
</div>

<div class="form-group">
	<label for="icono" class="<?php echo $label_column; ?>"><?php echo lang("description"); ?></label>
	<div class="<?php echo $field_column; ?>">
        <?php
			echo form_textarea(array(
				"id" => "description",
				"name" => "description",
				"value" => $model_info->descripcion,
				"placeholder" => lang('description'),
				"class" => "form-control",
				"autocomplete"=> "off",
				"maxlength" => "2000"
			));
		?>
	</div>
</div>

<div id="fases_group">
    <?php
        
        if($fases_disponibles){
            
            $arraySelected = array();
            $arraySelected2 = array();
            $arrayFases = array();
            
            foreach($fases_pu as $innerArray){
                $arraySelected[] = $innerArray["id"];
                $arraySelected2[(string)$innerArray["id"]] = lang($innerArray["nombre_lang"]);
            }       
            foreach($fases_disponibles as $innerArray){
                if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                    $arrayFases[(string)$innerArray["id"]] = lang($innerArray["nombre_lang"]);
                }
                
            }
            $array_final = $arraySelected2 + $arrayFases;
            
            $html = '';
            $html .= '<div class="form-group">';
                $html .= '<label for="fases" class="col-md-3">'.lang('phases').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_multiselect("fases[]", $array_final, $arraySelected, "id='fases' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                $html .= '</div>';
            $html .= '</div>';
            
            echo $html;
        }

    ?>
</div>




<script type="text/javascript">
    $(document).ready(function () {
		
		$("#icono").select2().select2("val", '<?php echo $model_info->icono; ?>');
		
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
		
		function format(state) {
			//console.log(state.text);
			if(state.text != '-'){
				return "<img class='' heigth='20' width='20' src='/assets/images/unit-processes/" + state.text + "'/>" + "&nbsp;&nbsp;" + state.text;
			}else{
				return state.text;
			}
		}
		
		$("#icono").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
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
		
		$('#fases').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
			//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-6'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-6'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
			keepOrder: true,
			afterSelect: function(value){
				$('#fases option[value="'+value+'"]').remove();
				$('#fases').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
			},
			afterDeselect: function(value){ 
				$('#fases option[value="'+value+'"]').removeAttr('selected');
			},
			//dblClick: true
		});
		
    });
</script>
<style>
.multiselect-header{
  text-align: center;
  padding: 3px;
  background: #7988a2;
  color: #fff;
}
</style>