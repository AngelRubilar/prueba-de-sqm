<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="category_name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
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


<!-- listar todos los materiales 
<div id="materials_group">
    <div class="form-group">
        <label for="materials" class="<?php echo $label_column; ?>"><?php echo lang('materials'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("materials", $materiales, array($material_rel_categoria->id_material), "id='materials' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>
  listar todos los proyectos -->

<div id="subcategorias_group">
    <?php
        
        if($subcategorias_disponibles){
            
            $arraySelected = array();
            $arraySelected2 = array();
            $arrayCategorias = array();
            
            foreach($subcategorias_de_categoria as $innerArray){
                $arraySelected[] = $innerArray["id"];
                $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
            }       
            foreach($subcategorias_disponibles as $innerArray){
                if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                    $arrayCategorias[(string)$innerArray["id"]] = $innerArray["nombre"];
                }
                
            }
            $array_final = $arraySelected2 + $arrayCategorias;
            
            $html = '';
            $html .= '<div class="form-group">';
                $html .= '<label for="subcategorias" class="col-md-3">'.lang('subcategories').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_multiselect("subcategorias[]", $array_final, $arraySelected, "id='subcategorias' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                $html .= '</div>';
            $html .= '</div>';
            
            echo $html;
        }

    ?>
</div>


<script type="text/javascript">
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('#materials_group .select2').select2();
	
	$('input[type="text"][maxlength]').maxlength({
		//alwaysShow: true,
		threshold: 245,
		warningClass: "label label-success",
		limitReachedClass: "label label-danger",
		appendToParent:true
	});
	
	$('#subcategorias').multiSelect({
		selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
		selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
		keepOrder: true,
		afterSelect: function(value){
			$('#subcategorias option[value="'+value+'"]').remove();
			$('#subcategorias').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
		},
		afterDeselect: function(value){ 
			$('#subcategorias option[value="'+value+'"]').removeAttr('selected'); 
		}
	 });
		 
	 //Botón eliminar incluye validación de nombre de categoria, esto cierra el mensaje
	$('#ajaxModal').on('hidden.bs.modal', function () {
		$('.app-alert').hide();
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