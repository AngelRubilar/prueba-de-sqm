<input type="hidden" name="id" value="<?php echo $id_proyecto; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

    <div class="form-group">
		
		<?php
    
            $arraySelected = array();
            $arraySelected2 = array();
            $arrayCamposProyecto = array();
			
			//$arrayCamposProyecto["fijo_1"] = lang("n_compromise");
			$arraySelected[] = "fijo_1";
			$arraySelected2["fijo_1"] = lang("n_compromise");
			$array_campos_ocupados[] = "fijo_1";
			
			//$arrayCamposProyecto["fijo_2"] = lang("name");
			$arraySelected[] = "fijo_2";
			$arraySelected2["fijo_2"] = lang("name");
			$array_campos_ocupados[] = "fijo_2";
			
			//$arrayCamposProyecto["fijo_3"] = lang("phases");
			$arraySelected[] = "fijo_3";
			$arraySelected2["fijo_3"] = lang("phases");
			$array_campos_ocupados[] = "fijo_3";
			
			//$arrayCamposProyecto["fijo_4"] = lang("reportability");
			$arraySelected[] = "fijo_4";
			$arraySelected2["fijo_4"] = lang("reportability");
			$array_campos_ocupados[] = "fijo_4";

            foreach($campos_compromiso as $innerArray){
                $arraySelected[] = $innerArray["id"];
                $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
            }
			
            foreach($campos_proyecto as $innerArray){
                if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                    $arrayCamposProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
                }
                
            }
			
			$arrayCamposProyecto["fijo_5"] = lang("compliance_action_control");
			$arraySelected[] = "fijo_5";
			$array_campos_ocupados[] = "fijo_5";
			
			$arrayCamposProyecto["fijo_6"] = lang("execution_frequency");
			$arraySelected[] = "fijo_6";
			$array_campos_ocupados[] = "fijo_6";
			
			
            $array_final = $arraySelected2 + $arrayCamposProyecto;
			
			$info = (count($array_campos_ocupados) > 0) ? '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_compromise_fields_info').'"><i class="fa fa-question-circle"></i></span>' : '';
            $html = '';
            $html .= '<div class="form-group">';
                $html .= '<label for="fields" class="col-md-3">'.lang('fields').' '.$info.'</label>';
                $html .= '<div class="col-md-9">';
				$html .= '<div id="mensaje_validacion_campos" class="pb10"></div>';
                $html .= form_multiselect("fields[]", $array_final, $arraySelected, "id='fields' class='multiple' multiple='multiple' data-rule-required='false', data-msg-required='" . lang('field_required') . "'", $array_campos_ocupados);
                $html .= '</div>';
            $html .= '</div>';
			
            echo $html;

        ?>
            
    </div>
	
	<div id="div_carpetas_destino" class="form-group">
        <label for="evaluados" class="col-md-3">Evaluados</label>
        <div id="evaluados" class="col-md-6">
			<?php
				echo form_input(array(
					"id" => "evaluado",
					//"name" => "evaluado",
					"name" => "evaluado[]",
					"value" => $model_info->nombre,
					"class" => "form-control",
					"placeholder" => lang('name'),
					//"autofocus" => true,
					"data-rule-required" => true,
					"data-msg-required" => lang("field_required"),
					"autocomplete"=> "off",
					"maxlength" => "255"
				));
			?>
                <!-- <input type="text" id="evaluado" class="form-control" name="evaluado" placeholder="Evaluado" maxlength="20" required> -->
		</div>
     
        <div class="col-md-3">
        
            <button id="agregar_evaluado" class="btn btn-primary" ><i class="fa fa-plus" aria-hidden="true"></i></button>
            <button id="eliminar_evaluado" class="btn btn-primary"><i class="fa fa-minus" aria-hidden="true"></i></button>
       <!--
        	<input type="button" id="agregar_evaluado" class="btn btn-primary" value="+" />
            <input type="button" id="eliminar_evaluado" class="btn btn-primary" value=" - " />
       -->
        </div>
    
    </div>
    
    <div class="form-group">
    	<div class="col-md-3"></div>
        <div class="col-md-9">
    		<div id="mensaje_validacion_evaluados"></div>
        </div>
    </div>


<style>
	.multiselect-header{
	  text-align: center;
	  padding: 3px;
	  background: #7988a2;
	  color: #fff;
	}
</style> 

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

		$('#fields').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
			keepOrder: true,
			afterSelect: function(value){
				var text = $('#fields option[value="'+value+'"]').text();
				$('#fields option[value="'+value+'"]').remove();
				//$('#fields').append($("<option></option>").attr("value", value).attr('selected', 'selected'));
				($("<option>"+text+"</option>").attr("value", value).attr('selected', 'selected')).insertBefore($('#fields option[value="fijo_5"]'));
				
				$('#fields option[value="fijo_5"]').prop("disabled", false);
				$('#fields').multiSelect('refresh');
				$('#fields option[value="fijo_5"]').prop("disabled", true);
				$('#fields').multiSelect('refresh');
				
				$('#fields option[value="fijo_6"]').prop("disabled", false);
				$('#fields').multiSelect('refresh');
				$('#fields option[value="fijo_6"]').prop("disabled", true);
				$('#fields').multiSelect('refresh');
				
			},
			afterDeselect: function(value){ 
				$('#fields option[value="'+value+'"]').removeAttr('selected');
			}
		});
		
		//Si se está editando
		<?php if ($id_compromiso) { ?>
		
			var contador_evaluado = 1;
			var primer_evaluado = '<?php echo $evaluados_compromiso[0]["nombre_evaluado"] ?>';
			$('#evaluado').val(primer_evaluado);
			var evaluados_compromiso = [];
			
			<?php foreach($evaluados_compromiso as $evaluado){ ?>
				
				var nombre_evaluado = '<?php echo $evaluado["nombre_evaluado"] ?>';	
				
				if(primer_evaluado != nombre_evaluado){	
					evaluados_compromiso.push(nombre_evaluado);
					$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado[]', style:'margin-top:10px; margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last')).val(nombre_evaluado);	
					contador_evaluado++;
				}

			<?php } ?>
			
			$('#agregar_evaluado').on('click', function(e){
				e.preventDefault();
				
				if(contador_evaluado == 1){
					//$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado_'+(contador_evaluado), style:'margin-top:10px; margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
					$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado[]', style:'margin-top:10px; margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
				} else {
					//$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado_'+(contador_evaluado), style:'margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
					$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado[]', style:'margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
				}
				
				$('#evaluado_' + contador_evaluado).val("");
				contador_evaluado++;
				
				$('#evaluado_' + contador_evaluado).maxlength({
					//alwaysShow: true,
					threshold: 245,
					warningClass: "label label-success",
					limitReachedClass: "label label-danger",
					appendToParent:true
				});
					
			});
			
			$('#eliminar_evaluado').on('click', function(e){
				e.preventDefault();
				var attr = $("#evaluados > input:last").attr('id');
				if(attr != "evaluado"){
					$("#evaluados > input:last").remove();
					contador_evaluado--;
				}
			});
	
		<?php } else { ?>
			
			//Funcionalidad para agregar o quitar inputs para ingreso evaluados
			var contador_evaluado = 0;
		
			$('#agregar_evaluado').on('click', function(e){
				e.preventDefault();
				contador_evaluado++;
				if(contador_evaluado == 1){
					//$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado_'+(contador_evaluado), style:'margin-top:10px; margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
					$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado[]', style:'margin-top:10px; margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
				} else {
					//$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado_'+(contador_evaluado), style:'margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
					$('#evaluado').clone().attr({id:'evaluado_'+(contador_evaluado), name:'evaluado[]', style:'margin-bottom:10px;'}).insertAfter($('[id^=evaluado]:last'));
				}
				
				$('#evaluado_' + contador_evaluado).val("");
				
				$('#evaluado_' + contador_evaluado).maxlength({
					//alwaysShow: true,
					threshold: 245,
					warningClass: "label label-success",
					limitReachedClass: "label label-danger",
					appendToParent:true
				});
					
			});
			
			$('#eliminar_evaluado').on('click', function(e){
				e.preventDefault();
				var attr = $("#evaluados > input:last").attr('id');
				if(attr != "evaluado"){
					$("#evaluados > input:last").remove();
					contador_evaluado--;
				}
			});
			//FIN Funcionalidad para agregar o quitar inputs para ingreso evaluados
			
		<?php } ?>
		

		$('#guardar_matriz').click(function(event){
			
			event.preventDefault();

			var campos_seleccionados = true;
			$('#fields option').each(function() {
				if ($(this).is(':selected')) {
					//alert("si");
					campos_seleccionados = true;
					return false;
				} else {
					//alert("no");
					campos_seleccionados = false;
				}
			});
			
			var evaluados = [];
			$('input[name^="evaluado"]').each(function() {
				evaluados.push($(this).val());
			});

			var evaluados_distintos = checkIfArrayIsUnique(evaluados);
			var evaluado_vacio = checkIfArrayHasEmptyString(evaluados);
			
			if(evaluados_distintos === false){
				$('#mensaje_validacion_evaluados').html('<span style="color: #ec5855;">' + '<?php echo lang("repeated_evaluated_message"); ?>' + '</span>');
			} else if(evaluado_vacio === true){
				$('#mensaje_validacion_evaluados').html('<span style="color: #ec5855;">' + '<?php echo lang("no_name_evaluated_message"); ?>' + '</span>');
			} else {
				if(!campos_seleccionados){
					$('#mensaje_validacion_campos').html('<span style="color: #ec5855;">' + '<?php echo lang("field_required"); ?>' + '</span>');
				} else {
					$('#mensaje_validacion_campos').html('');
					$('#matrix_config-form').submit();
				}
			}
		
		});
		
		//Funcion que retorna true solo si el array tiene todos sus valores distintos
		function checkIfArrayIsUnique(myArray) {
			return myArray.length === new Set(myArray).size;
		}
		
		//Funcion que retorna true solo si el array posee algun(os) string vacío(s)
		function checkIfArrayHasEmptyString(myArray){
		   for(var i=0; i < myArray.length; i++){
			   if(myArray[i] === "") {  
				  return true;
			   }
		   }
		   return false;
		}
		
    });
</script>