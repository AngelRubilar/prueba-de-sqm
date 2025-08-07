
<div id="modelo_sp" style="display:none;">
<?php
echo $modelo_sp;
?>
</div>
<div id="rule_options_sp_group">
<?php
echo $html_sp;
?>
</div>

<script type="text/javascript">

$(document).ready(function() {
	
	$(".tipo_asignacion_sp").each(function(index) {
		
		if($(this).val() == 'Porcentual'){
		
			var $tbody = $(this).closest('tbody');
			var row_number = $tbody.attr('id').replace('row_', '');
			var $tr = $(this).closest('tr');
			var $primer_td = $tr.find('td:eq(0)');
			var $segundo_td = $tr.find('td:eq(1)');
			var $tercer_td = $tr.find('td:eq(2)');
			var $cuarto_td = $tr.find('td:eq(3)');
		
			var $campo_porc_total = $tbody.find('.campo_porc_total');
			var sliders = $tbody.find(".slider");
			sliders.each(function() {
				var value = parseInt($(this).siblings("span.value").text(), 10),
					availableTotal = 100;
					
				$(this).empty().slider({
					value: value,
					min: 0,
					max: value,
					range: "max",
					step: 1,
					animate: 100,
					slide: function(event, ui) {
						// Update display to current value
						$(this).siblings("span.value").text(ui.value + '%');
						$(this).siblings("input.porc").val(ui.value);
			
						// Get current total
						var total = 0;
			
						sliders.not(this).each(function() {
							total += $(this).slider("option", "value");
						});
			
						// Need to do this because apparently jQ UI
						// does not update value until this event completes
						total += ui.value;
						$campo_porc_total.val(total);
			
						var max = availableTotal - total;
			
						// Update each slider
						sliders.not(this).each(function() {
							var t = $(this),
								value = t.slider("option", "value");
			
							t.slider("option", "max", max + value)
								.siblings("span.value").text(value + '/' + (max + value) + '%');
							t.slider('value', value);
						});
					}
				});
			});
		}
	});
	
	//$('.tipo_asignacion_sp').change(function(){
	//$('.tipo_asignacion_sp').on('change', function(){
	$(document).on("change", ".tipo_asignacion_sp", function() {

		var $select_sp = $('#modelo_sp > select').clone();
		
		var $tbody = $(this).closest('tbody');
		var row_number = $tbody.attr('id').replace('row_', '');
		var $tr = $(this).closest('tr');
		var $primer_td = $tr.find('td:eq(0)');
		var $segundo_td = $tr.find('td:eq(1)');
		var $tercer_td = $tr.find('td:eq(2)');
		var $cuarto_td = $tr.find('td:eq(3)');
		
		if($(this).val() == 'Porcentual'){
			
			var $opciones = $(this).closest('tr').find('.subproject option').map(function() {return $(this).text();}).get();
			var $ides = $(this).closest('tr').find('.subproject option').map(function() {return $(this).val();}).get();
			$opciones = $opciones.slice(1);
			$ides = $ides.slice(1);
			
			var $num_sp = $ides.length;
			$primer_td.attr("rowspan", $num_sp);
			$segundo_td.attr("rowspan", $num_sp);
			
			$.each($opciones, function(index, sp){
				var id_opcion = $ides[index];
				if(index == 0){
					$tercer_td.html(sp);
					$cuarto_td.html("<div class='slider'></div><span class='value'>0</span><input type='hidden' name='porc_sp["+row_number+"]["+id_opcion+"]' class='porc' value='0'/>");
				}else if(index > 0 && index < ($num_sp - 1)){
					$tbody.find("tr:last").after("<tr><td>"+sp+"</td><td><div class='slider'></div><span class='value'>0</span><input type='hidden' name='porc_sp["+row_number+"]["+id_opcion+"]' class='porc' value='0'/></td></tr>");
				}else{
					$tbody.find("tr:last").after("<tr><td>"+sp+"</td><td><div class='slider'></div><span class='value'>0</span><input type='hidden' name='porc_sp["+row_number+"]["+id_opcion+"]' class='porc'  value='0'/><input type='hidden' name='porc_total_sp["+row_number+"]' value='' class='campo_porc_total' data-rule-required='true' data-msg-required='<?php echo lang('field_required'); ?>' data-rule-equals='100' data-msg-equals='<?php echo lang('field_must_be_equals_to'); ?>'/></td></tr>");
				}
				
			});
			
			var $campo_porc_total = $tbody.find('.campo_porc_total');
			var sliders = $tbody.find(".slider");
			sliders.each(function() {
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
			
						sliders.not(this).each(function() {
							total += $(this).slider("option", "value");
						});
			
						// Need to do this because apparently jQ UI
						// does not update value until this event completes
						total += ui.value;
						$campo_porc_total.val(total);
			
						var max = availableTotal - total;
			
						// Update each slider
						sliders.not(this).each(function() {
							var t = $(this),
								value = t.slider("option", "value");
			
							t.slider("option", "max", max + value)
								.siblings("span.value").text(value + '/' + (max + value) + '%');
							t.slider('value', value);
						});
					}
				});
			});
			
		//}else if($(this).val() == 'Total'){
		}else{
			$tbody.find('tr').not($tr).remove();
			$tercer_td.html($select_sp.show());
			$tercer_td.find('select').attr('name', 'subproject['+row_number+']');
			$tercer_td.find('select').attr('aria-describedby', 'subproject\\['+row_number+'\\]-error');
			$tercer_td.find('select').select2();
			$cuarto_td.html('-');
		}
		
		
	});
	
	$('#ajaxModal').on('hidden.bs.modal', function () {
		$('.app-alert').hide();
	});
	
		
});
	
</script>    