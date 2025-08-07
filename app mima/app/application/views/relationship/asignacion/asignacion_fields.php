
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
?>

<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client_id2", $clientes, array($model_info->id_cliente), "id='client_id2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>


<div class="form-group">
    <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("project2", $proyectos, $model_info->id_proyecto, "id='project2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div id="criterio_group" class="form-group">
    <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        //echo form_dropdown("criterio2", $criterios, array($model_info->id_criterio), "id='criterio2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        echo form_dropdown("criterio2", $criterios, $model_info->id_criterio, "id='criterio2' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="criterio_sp" class="<?php echo $label_column; ?>"><?php echo lang('subproject'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_input(array(
                "id" => "criterio_sp",
                "name" => "criterio_sp",
                "value" => $model_info->criterio_sp,
                "class" => "form-control",
                "placeholder" => lang('subproject'),
                //"data-rule-required" => true,
                //"data-msg-required" => lang("field_required"),
                "autocomplete" => "off",
            ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="assignment_type_sp" class="<?php echo $label_column; ?>"><?php echo lang('assignment_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("assignment_type_sp", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $model_info->tipo_asignacion_sp, "id='' class='edit_tipo_asignacion_sp select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div id="grupo_porcentaje_sp">

<?php if($model_info->tipo_asignacion_sp == 'Porcentual'){ ?>
<div class="form-group">
    <label for="porcentaje" class="<?php echo $label_column; ?>">%</label>
    <div class="<?php echo $field_column; ?>">
        <table class="table">
            <tbody>
            <?php
            $porcentajes = $model_info->porcentajes_sp;
            $porcentajes_decoded = json_decode($porcentajes, true);
            
            $html = '';
			$porc_total = 0;
            foreach($porcentajes_decoded as $id_subproyecto => $porc){
                
                $subproyecto = $this->Subprojects_model->get_one($id_subproyecto);
                $nombre_subproyecto = ($subproyecto->nombre)?$subproyecto->nombre:'-';
                
                $html .= '<tr>';
                
                $html .= '<td class="w10p">';
                $html .= $nombre_subproyecto;
                $html .= '</td>';
                
                $html .= '<td class="w10p">';
                $html .= '<div class="slider">'.$porc.'</div>';
                $html .= '<span class="value">'.$porc.'%</span>';
                $html .= '<input type="hidden" name="porc_sp['.$id_subproyecto.']" class="porc" value="'.$porc.'"/>';
                $html .= '</td>';
                
                $html .= '</tr>';
				
				$porc_total += $porc;
            }
            
            $html .= '<tr><td></td><td><input type="hidden" name="porc_total_sp" value="'.$porc_total.'" class="campo_porc_total" data-rule-required="true" data-msg-required="'.lang('field_required').'" data-rule-equals="100" data-msg-equals="'.lang('field_must_be_equals_to').'"/></td></tr>';
            
            echo $html;
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php }else{ ?>

<div id="div_target_subproject" class="form-group">
    <label for="target_subproject" class="<?php echo $label_column; ?>"><?php echo lang('target_subproject'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("target_subproject", array("" => "-") + $subproyectos, $model_info->sp_destino, "id='target_subproject' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>
<?php } ?>

</div>

<div id="modelo_sp" class="form-group" style="display:none;">
    <div id="div_target_subproject" class="form-group">
        <label for="target_subproject" class="<?php echo $label_column; ?>"><?php echo lang('target_subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("target_subproject", array("" => "-") + $subproyectos, "", "id='target_subproject' class='validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>


<div class="form-group">
    <label for="criterio_pu" class="<?php echo $label_column; ?>"><?php echo lang('unit_process'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_input(array(
                "id" => "criterio_pu",
                "name" => "criterio_pu",
                "value" => $model_info->criterio_pu,
                "class" => "form-control",
                "placeholder" => lang('unit_process'),
                //"data-rule-required" => true,
                //"data-msg-required" => lang("field_required"),
                "autocomplete" => "off",
            ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="assignment_type_pu" class="<?php echo $label_column; ?>"><?php echo lang('assignment_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("assignment_type_pu", array("" => "-", "Total" => lang('total'), "Porcentual" => lang('percentage')), $model_info->tipo_asignacion_pu, "id='' class='edit_tipo_asignacion_pu select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'")
        ?>
    </div>
</div>

<div id="grupo_porcentaje_pu">

<?php if($model_info->tipo_asignacion_pu == 'Porcentual'){ ?>
<div class="form-group">
    <label for="porcentaje" class="<?php echo $label_column; ?>">%</label>
    <div class="<?php echo $field_column; ?>">
        <table class="table">
            <tbody>
            <?php
            $porcentajes = $model_info->porcentajes_pu;
            $porcentajes_decoded = json_decode($porcentajes, true);
            
            $html = '';
			$porc_total = 0;
            foreach($porcentajes_decoded as $id_pu => $porc){
                
                $pu = $this->Unit_processes_model->get_one($id_pu);
                $nombre_pu = ($pu->nombre)?$pu->nombre:'-';
                
                $html .= '<tr>';
                
                $html .= '<td class="w10p">';
                $html .= $nombre_pu;
                $html .= '</td>';
                
                $html .= '<td class="w10p">';
                $html .= '<div class="slider">'.$porc.'</div>';
                $html .= '<span class="value">'.$porc.'%</span>';
                $html .= '<input type="hidden" name="porc_pu['.$id_pu.']" class="porc" value="'.$porc.'"/>';
                $html .= '</td>';
                
                $html .= '</tr>';
				
				$porc_total += $porc;
            }
            
            $html .= '<tr><td></td><td><input type="hidden" name="porc_total_pu" value="'.$porc_total.'" class="campo_porc_total" data-rule-required="true" data-msg-required="'.lang('field_required').'" data-rule-equals="100" data-msg-equals="'.lang('field_must_be_equals_to').'"/></td></tr>';
            
            echo $html;
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php }else{ ?>

<div id="div_target_unit_process" class="form-group">
    <label for="target_unit_process" class="<?php echo $label_column; ?>"><?php echo lang('target_unitary_process'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown("target_unit_process", array("" => "-") + $unit_processes_select, $model_info->pu_destino, "id='target_unit_process' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<?php } ?>

</div>

<div id="modelo_pu" class="form-group" style="display:none;">
    <div id="div_target_unit_process" class="form-group">
        <label for="target_unit_process" class="<?php echo $label_column; ?>"><?php echo lang('target_unitary_process'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("target_unit_process", array("" => "-") + $unit_processes_select, "", "id='target_unit_process' class='validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>




<script type="text/javascript">

    $(document).ready(function() {
		
		$('#client_id2').focus();
		$('#asignacion-form-edit .select2').select2();
	
		$('#client_id2').change(function(){
			
			var id_client = $(this).val();
			
			if(id_client != ""){

				$.ajax({
					url:  '<?php echo_uri("clients/get_projects_of_client_json") ?>',
					type:  'post',
					data: {id_client:id_client},
					dataType:'json',
					success: function(respuesta){
						$('#project2').html("");
						$.each((respuesta), function() {
							$('#project2').append($("<option />").val(this.id).text(this.text));
						});
						$('#project2').select2();
					}
				});
			
			
			} else {
				
				$.ajax({
					url:  '<?php echo_uri("clients/get_projects_of_client_json") ?>',
					type:  'post',
					data: {id_client:id_client},
					dataType:'json',
					success: function(respuesta){
						$('#project2').html("");
						$('#project2').append($("<option />").val("").text("-"));	
						$('#project2').select2();
						$('#criterio2').html("");
						$('#criterio2').append($("<option />").val("").text("-"));					
						$('#criterio2').select2();
					}
				});
				
			}
			
			
		});

		$('#project2').change(function(){
			
			var id_proyecto = $(this).val();
			
			if(id_proyecto != ""){
			
				$.ajax({
					url:  '<?php echo_uri("relationship/get_criterio_of_project_json") ?>',
					type:  'post',
					data: {id_proyecto:id_proyecto},
					dataType:'json',
					success: function(respuesta){
						$('#criterio2').html("");
						$.each((respuesta), function() {
							$('#criterio2').append($("<option />").val(this.id).text(this.text));
						});
						$('#criterio2').select2();
					}
				});
				
				<?php if($model_info->id){ ?>
				
					$.ajax({
						url:  '<?php echo_uri("relationship/get_target_subprojects_of_projects") ?>',
						type:  'post',
						data: {id_proyecto:id_proyecto},
						//dataType:'json',
						success: function(respuesta){
							$('#div_target_subproject').html("");
							$('#div_target_subproject').html(respuesta);
							$('#div_target_subproject .select2').select2();
						}
					});
					
					$.ajax({
						url:  '<?php echo_uri("relationship/get_pu_destino_of_projects") ?>',
						type:  'post',
						data: {id_proyecto:id_proyecto},
						//dataType:'json',
						success: function(respuesta){
							$('#div_unit_process').html("");
							$('#div_unit_process').html(respuesta);
							$('#div_unit_process .select2').select2();
						}
					});
					
				
				<?php } ?>
			
			} else {

				$.ajax({
					url:  '<?php echo_uri("relationship/get_criterio_of_project_json") ?>',
					type:  'post',
					data: {id_proyecto:id_proyecto},
					dataType:'json',
					success: function(respuesta){
						$('#criterio2').html("");
						$('#criterio2').append($("<option />").val("").text("-"));
						$('#criterio2').select2();
					}
				});

			}

		});
		
		<?php if($model_info->tipo_asignacion_sp == 'Porcentual'){ ?>
			var sliders_sp = $("#grupo_porcentaje_sp").find(".slider");
			sliders_sp.each(function() {
				var value = parseInt($(this).text(), 10),
					availableTotal = 100;
			
				$(this).empty().slider({
					value: value,
					min: 0,
					max: 100,
					range: "max",
					step: 1,
					animate: 100,
					slide: function(event, ui) {
						// Update display to current value
						$(this).siblings("span.value").text(ui.value + '%');
						$(this).siblings("input.porc").val(ui.value);
			
						// Get current total
						var total = 0;
			
						sliders_sp.not(this).each(function() {
							total += $(this).slider("option", "value");
						});
			
						// Need to do this because apparently jQ UI
						// does not update value until this event completes
						total += ui.value;
						$("#grupo_porcentaje_sp .campo_porc_total").val(total);
						
						var max = availableTotal - total;
			
						// Update each slider
						sliders_sp.not(this).each(function() {
							var t = $(this),
								value = t.slider("option", "value");
							if(value > (max + value)){
								t.slider("option", "max", max + value).siblings("span.value").text((max + value) + '/' + (max + value) + '%');
								t.slider('value', (max + value));
							}else{
								t.slider("option", "max", max + value).siblings("span.value").text(value + '/' + (max + value) + '%');
								t.slider('value', value);
							}
							
						});
					}
				});
			});
		<?php } ?>
		
		<?php if($model_info->tipo_asignacion_pu == 'Porcentual'){ ?>
			var sliders_pu = $("#grupo_porcentaje_pu").find(".slider");
			sliders_pu.each(function() {
				var value = parseInt($(this).text(), 10),
					availableTotal = 100;
			
				$(this).empty().slider({
					value: value,
					min: 0,
					max: 100,
					range: "max",
					step: 1,
					animate: 100,
					slide: function(event, ui) {
						// Update display to current value
						$(this).siblings("span.value").text(ui.value + '%');
						$(this).siblings("input.porc").val(ui.value);
			
						// Get current total
						var total = 0;
			
						sliders_pu.not(this).each(function() {
							total += $(this).slider("option", "value");
						});
			
						// Need to do this because apparently jQ UI
						// does not update value until this event completes
						total += ui.value;
						$("#grupo_porcentaje_pu .campo_porc_total").val(total);
						
						var max = availableTotal - total;
			
						// Update each slider
						sliders_pu.not(this).each(function() {
							var t = $(this),
								value = t.slider("option", "value");
							if(value > (max + value)){
								t.slider("option", "max", max + value).siblings("span.value").text((max + value) + '/' + (max + value) + '%');
								t.slider('value', (max + value));
							}else{
								t.slider("option", "max", max + value).siblings("span.value").text(value + '/' + (max + value) + '%');
								t.slider('value', value);
							}
							
						});
					}
				});
			});
		<?php } ?>
		
		// ON CHANGE SUBPROYECTO
		$('.edit_tipo_asignacion_sp').change(function() {

			var $select_sp = $('#modelo_sp > #div_target_subproject').clone();
			
			if($(this).val() == 'Porcentual'){
				
				var $opciones = $('#grupo_porcentaje_sp #target_subproject option').map(function() {return $(this).text();}).get();
				var $ides = $('#grupo_porcentaje_sp #target_subproject option').map(function() {return $(this).val();}).get();
				$opciones = $opciones.slice(1);
				$ides = $ides.slice(1);
				
				var $num_sp = $ides.length;
				var tabla = $('<div class="form-group"><label class="col-md-3">%</label><div class="col-md-9"><table class="table"><tbody></tbody></table></div></div>');
				$('#grupo_porcentaje_sp').html(tabla);
				
				$.each($opciones, function(index, sp){
					var id_opcion = $ides[index];
					$('#grupo_porcentaje_sp table > tbody').append("<tr><td class='w10p'>"+sp+"</td><td class='w10p'><div class='slider'></div><span class='value'>0</span><input type='hidden' name='porc_sp["+id_opcion+"]' class='porc'/></td></tr>");
				});
				
				$('#grupo_porcentaje_sp table > tbody').append("<input type='hidden' name='porc_total_sp' value='0' class='campo_porc_total' data-rule-required='true' data-msg-required='<?php echo lang('field_required'); ?>' data-rule-equals='100' data-msg-equals='<?php echo lang('field_must_be_equals_to'); ?>'/>");
				
				var $campo_porc_total = $('#grupo_porcentaje_sp table .campo_porc_total');
				var sliders_sp = $("#grupo_porcentaje_sp").find(".slider");
				sliders_sp.each(function() {
					var value = parseInt($(this).text(), 10),
						availableTotal = 100;
				
					$(this).empty().slider({
						value: 0,
						min: 0,
						max: 100,
						range: "max",
						step: 1,
						animate: 100,
						slide: function(event, ui) {
							// Update display to current value
							$(this).siblings("span.value").text(ui.value + '%');
							$(this).siblings("input.porc").val(ui.value);
				
							// Get current total
							var total = 0;
				
							sliders_sp.not(this).each(function() {
								total += $(this).slider("option", "value");
							});
				
							// Need to do this because apparently jQ UI
							// does not update value until this event completes
							total += ui.value;
							$campo_porc_total.val(total);
				
							var max = availableTotal - total;
				
							// Update each slider
							sliders_sp.not(this).each(function() {
								var t = $(this),
									value = t.slider("option", "value");
				
								t.slider("option", "max", max + value)
									.siblings("span.value").text(value + '/' + (max + value) + '%');
								t.slider('value', value);
							});
						}
					});
				});
			
			}else{
				$("#grupo_porcentaje_sp").html($select_sp);
				$("#grupo_porcentaje_sp").find('select').width('100%').select2();
			}
		});
		
		// ON CHANGE PROCESO UNITARIO
		$('.edit_tipo_asignacion_pu').change(function() {

			var $select_pu = $('#modelo_pu > #div_target_unit_process').clone();
			
			if($(this).val() == 'Porcentual'){
				
				var $opciones = $('#grupo_porcentaje_pu #target_unit_process option').map(function() {return $(this).text();}).get();
				var $ides = $('#grupo_porcentaje_pu #target_unit_process option').map(function() {return $(this).val();}).get();
				$opciones = $opciones.slice(1);
				$ides = $ides.slice(1);
				
				var $num_sp = $ides.length;
				var tabla = $('<div class="form-group"><label class="col-md-3">%</label><div class="col-md-9"><table class="table"><tbody></tbody></table></div></div>');
				$('#grupo_porcentaje_pu').html(tabla);
				
				$.each($opciones, function(index, pu){
					var id_opcion = $ides[index];
					$('#grupo_porcentaje_pu table > tbody').append("<tr><td class='w10p'>"+pu+"</td><td class='w10p'><div class='slider'></div><span class='value'>0</span><input type='hidden' name='porc_pu["+id_opcion+"]' class='porc'/></td></tr>");
				});
				
				$('#grupo_porcentaje_pu table > tbody').append("<input type='hidden' name='porc_total_pu' value='0' class='campo_porc_total' data-rule-required='true' data-msg-required='<?php echo lang('field_required'); ?>' data-rule-equals='100' data-msg-equals='<?php echo lang('field_must_be_equals_to'); ?>'/>");
				
				var $campo_porc_total = $('#grupo_porcentaje_pu table .campo_porc_total');
				var sliders_pu = $("#grupo_porcentaje_pu").find(".slider");
				sliders_pu.each(function() {
					var value = parseInt($(this).text(), 10),
						availableTotal = 100;
				
					$(this).empty().slider({
						value: 0,
						min: 0,
						max: 100,
						range: "max",
						step: 1,
						animate: 100,
						slide: function(event, ui) {
							// Update display to current value
							$(this).siblings("span.value").text(ui.value + '%');
							$(this).siblings("input.porc").val(ui.value);
				
							// Get current total
							var total = 0;
				
							sliders_pu.not(this).each(function() {
								total += $(this).slider("option", "value");
							});
				
							// Need to do this because apparently jQ UI
							// does not update value until this event completes
							total += ui.value;
							$campo_porc_total.val(total);
				
							var max = availableTotal - total;
				
							// Update each slider
							sliders_pu.not(this).each(function() {
								var t = $(this),
									value = t.slider("option", "value");
				
								t.slider("option", "max", max + value)
									.siblings("span.value").text(value + '/' + (max + value) + '%');
								t.slider('value', value);
							});
						}
					});
				});
			
			}else{
				$("#grupo_porcentaje_pu").html($select_pu);
				$("#grupo_porcentaje_pu").find('select').width('100%').select2();
			}
		});
		
		
		
		$('#ajaxModal').on('hidden.bs.modal', function () {
			$('.app-alert').hide();
		});
		    
    });
	
</script>    