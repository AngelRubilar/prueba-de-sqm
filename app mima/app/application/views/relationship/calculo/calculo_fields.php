<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
?>

<div class="form-group">
    <label for="client_id" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client_id", $clientes, array($model_info->id_cliente), "id='client_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>

<div class="form-group">
    <label for="project_id" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("project_id", $proyectos, $model_info->id_proyecto, "id='project_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
    <label for="criterio_id" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("criterio_id", $criterios, $model_info->id_criterio, "id='criterio_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div id="criterio_fc_group">
    <div class="form-group">
        <label for="criterio_fc_id" class="<?php echo $label_column; ?>"><?php echo lang('fc_rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("criterio_fc_id", array("" => "-") + $opciones_criterio, $model_info->criterio_fc, "id='criterio_fc_id' class='select2 validate-hidden' data-sigla='' ");
            ?>
        </div>
    </div>
</div>

<div id="calculo_group">
    <div class="form-group">
        <label for="calculo" class="<?php echo $label_column; ?>"><?php echo lang('calculation'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_multiselect("calculo[]", $calculo, json_decode($model_info->id_campo_unidad), "id='calculo' class='select2 validate-hidden' data-sigla='' data-rule-required='true' data-msg-required='" . lang('field_required') . "' placeholder='-'");
            ?>
        </div>
    </div>
</div>

<div id="db_group">
    <div class="form-group" id="">
        <label for="database" class="<?php echo $label_column; ?>"><?php echo lang('database'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("database", $bases_de_datos, array($model_info->id_bd), "id='db_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div id="categoria_group">
    <div class="form-group">
        <label for="categoria" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("categoria", $categorias, $model_info->id_categoria, "id='categoria' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div id="subcategoria_group">
    <div class="form-group">
        <label for="subcategoria" class="<?php echo $label_column; ?>"><?php echo lang('subcategory'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("subcategoria", $subcategorias, $model_info->id_subcategoria, "id='subcategoria' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="label" class="<?php echo $label_column; ?>"><?php echo lang('label'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "label",
            "name" => "label",
            "value" => $model_info->etiqueta,
            "class" => "form-control",
            "placeholder" => lang('label'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>


<script type="text/javascript">

$(document).ready(function() {
	
	$('#client_id').focus();
	$('#calculo-form .select2').select2();
	
	$('input[type="text"][maxlength]').maxlength({
		//alwaysShow: true,
		threshold: 245,
		warningClass: "label label-success",
		limitReachedClass: "label label-danger",
		appendToParent:true
	});

	$('#client_id').change(function(){
		
		var id_client = $(this).val();
		if(id_client){
			
			select2LoadingStatusOn($('#project_id'));

			$.ajax({
				url: '<?php echo_uri("clients/get_projects_of_client_json"); ?>',
				type: 'post',
				data: {id_client:id_client},
				dataType:'json',
				success: function(respuesta){
					
					$('#project_id').html("");
					$.each((respuesta), function() {
						$('#project_id').append($("<option />").val(this.id).text(this.text));
					});
					$('#project_id').select2();
					select2LoadingStatusOff($('#project_id'));
				}
			});
		
		} else {
			$('#project_id').html("");
			$('#project_id').append($("<option />").val("").text("-"));	
			$('#project_id').select2();
		}
		
		$('#criterio_id').html("");
		$('#criterio_id').append($("<option />").val("").text("-"));					
		$('#criterio_id').select2();
		
		$('#criterio_fc_id').html("");
		$('#criterio_fc_id').append($("<option />").val("").text("-"));
		$('#criterio_fc_id').select2();
		
		$('#calculo').html("");
		$('#calculo').select2();
		
		$('#db_id').html("");
		$('#db_id').append($("<option />").val("").text("-"));
		$('#db_id').select2();
		
		$('#categoria').html("");
		$('#categoria').append($("<option />").val("").text("-"));
		$('#categoria').select2();
		
		$('#subcategoria').html("");
		$('#subcategoria').append($("<option />").val("").text("-"));
		$('#subcategoria').select2();

	});

	$('#project_id').change(function(){
		
		var id_proyecto = $(this).val();
		if(id_proyecto){
		
			select2LoadingStatusOn($('#criterio_id'));
			
			$.ajax({
				url:  '<?php echo_uri("relationship/get_criterio_of_project_json") ?>',
				type:  'post',
				data: {id_proyecto:id_proyecto},
				dataType:'json',
				success: function(respuesta){
					$('#criterio_id').html("");
					$.each((respuesta), function() {
						$('#criterio_id').append($("<option/>").val(this.id).text(this.text));
					});
					$('#criterio_id').select2();
					select2LoadingStatusOff($('#criterio_id'));
				}
			});
		
		} else {
			$('#criterio_id').html("");
			$('#criterio_id').append($("<option />").val("").text("-"));
			$('#criterio_id').select2();
		}
		
		$('#criterio_fc_id').html("");
		$('#criterio_fc_id').append($("<option />").val("").text("-"));
		$('#criterio_fc_id').select2();
		
		$('#calculo').html("");
		$('#calculo').select2();
		
		$('#db_id').html("");
		$('#db_id').append($("<option />").val("").text("-"));
		$('#db_id').select2();
		
		$('#categoria').html("");
		$('#categoria').append($("<option />").val("").text("-"));
		$('#categoria').select2();
		
		$('#subcategoria').html("");
		$('#subcategoria').append($("<option />").val("").text("-"));
		$('#subcategoria').select2();
		
		
	});    
	
			
	$('#criterio_id').on('change', function(){
		
		var id_proyecto = $('#project_id').val();
		var id_criterio = $(this).val();
		
		select2LoadingStatusOn($('#criterio_fc_id'));
		select2LoadingStatusOn($('#calculo'));
		select2LoadingStatusOn($('#db_id'));
		
		$.ajax({
			url:  '<?php echo_uri("relationship/get_options_of_criterio_fc"); ?>',
			type:  'post',
			data: {id_criterio:id_criterio},
			//dataType:'json',
			success: function(respuesta){
				$('#criterio_fc_group').html(respuesta);
				$('#criterio_fc_group .select2').select2();
				select2LoadingStatusOff($('#criterio_fc_id'));
			}
		});
		
		$.ajax({
			url:  '<?php echo_uri("relationship/get_unidades_of_criterio_fc"); ?>',
			type:  'post',
			data: {id_criterio:id_criterio},
			//dataType:'json',
			success: function(respuesta){
				$('#calculo_group').html(respuesta);
				$('#calculo_group .select2').select2();
				select2LoadingStatusOff($('#calculo'));
			}
		});
		
		$.ajax({
			url:  '<?php echo_uri("relationship/get_db_of_criterio_fc"); ?>',
			type:  'post',
			data: {id_criterio:id_criterio},
			//dataType:'json',
			success: function(respuesta){
				$('#db_group').html(respuesta);
				$('#db_group .select2').select2();
				select2LoadingStatusOff($('#db_id'));
			}
		});
		
		
		$('#categoria').html("");
		$('#categoria').append($("<option />").val("").text("-"));
		$('#categoria').select2();
		
		$('#subcategoria').html("");
		$('#subcategoria').append($("<option />").val("").text("-"));
		$('#subcategoria').select2();
		
	});
	

	$(document).on('change', '#db_id', function(event){
		
		var id_criterio = $('#criterio_id').val();
		var id_bd = $(this).val();
		select2LoadingStatusOn($('#categoria'));
		
		if(id_bd){
			
			$.ajax({
				url: '<?php echo_uri("relationship/get_categories_of_criterio_fc"); ?>',
				type: 'post',
				data: {id_criterio:id_criterio, id_bd:id_bd},
				//dataType:'json',
				success: function(respuesta){
					$('#categoria_group').html(respuesta);
					$('#categoria_group .select2').select2();
					select2LoadingStatusOff($('#categoria'));
				}
			});
			
		} else {
			
			select2LoadingStatusOff($('#categoria'));
			$('#categoria').html("");
			$('#categoria').append($("<option />").val("").text("-"));
			$('#categoria').select2();
			
			$('#subcategoria').html("");
			$('#subcategoria').append($("<option />").val("").text("-"));
			$('#subcategoria').select2();
		}
		
		event.stopImmediatePropagation();
		
	});
	
	// Se gatilla mas de una vez
	$(document).on('change', '#categoria', function(event){
		
		var id_criterio = $('#criterio_id').val();
		var id_bd = $('#db_id').val();
		var categoria = $(this).val(); 
		select2LoadingStatusOn($('#subcategoria'));

		$.ajax({
			url: '<?php echo_uri("relationship/get_subcategories_of_category"); ?>',
			type: 'post',
			data: {id_criterio:id_criterio, id_bd:id_bd, categoria:categoria},
			//dataType:'json',
			success: function(respuesta){
				select2LoadingStatusOff($('#subcategoria'));
				$('#subcategoria_group').html(respuesta);
				$('#subcategoria_group .select2').select2();
			}
		});
		
		event.stopImmediatePropagation();
		
	});
	
});

</script>