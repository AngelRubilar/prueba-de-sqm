<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="material_name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
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

<div id="categorias_group">
    <?php
        
        if($categorias_disponibles){
            
            $arraySelected = array();
            $arraySelected2 = array();
            $arrayCategorias = array();
			$array_categorias_ocupadas = array();
            
            foreach($categorias_de_material as $innerArray){
                $arraySelected[] = $innerArray["id"];
                $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
            }       
            foreach($categorias_disponibles as $innerArray){
                if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                    $arrayCategorias[(string)$innerArray["id"]] = $innerArray["nombre"];
                }
                
            }
            $array_final = $arraySelected2 + $arrayCategorias;
            
			foreach($categorias_deshabilitadas as $row){
				$array_categorias_ocupadas[] = $row->id_categoria;
			}
			
			$info = (count($array_categorias_ocupadas) > 0)?'<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('categories_disabled_info').'"><i class="fa fa-question-circle"></i></span>':'';
			
            $html = '';
            $html .= '<div class="form-group">';
                $html .= '<label for="categorias" class="col-md-3">'.lang('categories').' '.$info.'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_multiselect("categorias[]", $array_final, $arraySelected, "id='categorias' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'", $array_categorias_ocupadas);
                $html .= '</div>';
            $html .= '</div>';
            
            echo $html;
        }

    ?>
</div>


<script type="text/javascript">
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	
	$('input[type="text"][maxlength]').maxlength({
		//alwaysShow: true,
		threshold: 245,
		warningClass: "label label-success",
		limitReachedClass: "label label-danger",
		appendToParent:true
	});
	
	$('#categorias').multiSelect({
		selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
		selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
		keepOrder: true,
		afterSelect: function(value){
			$('#categorias option[value="'+value+'"]').remove();
			$('#categorias').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
		},
		afterDeselect: function(value){ 
			$('#categorias option[value="'+value+'"]').removeAttr('selected'); 
		}
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