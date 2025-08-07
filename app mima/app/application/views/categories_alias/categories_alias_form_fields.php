<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<!-- listar todos los clientes -->
<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='client_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
<!-- listar todos los clientes -->

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

 
<div class="form-group">
    <label for="alias" class="<?php echo $label_column; ?>"><?php echo lang('alias'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "alias",
            "name" => "alias",
            "value" => $model_info->alias,
            "class" => "form-control",
            "placeholder" => lang('alias'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
 


<script type="text/javascript">
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('#categories_alias-form .select2').select2();
	
	$('input[type="text"][maxlength]').maxlength({
		//alwaysShow: true,
		threshold: 245,
		warningClass: "label label-success",
		limitReachedClass: "label label-danger",
		appendToParent:true
	});
	
	$('#client_id').on('change', function(){
		
		var id_cliente = $(this).val();
		select2LoadingStatusOn($('#categoria'));
		
		$.ajax({
			url:  '<?php echo_uri("categories_alias/get_categories_of_client") ?>',
			type:  'post',
			data: {id_cliente:id_cliente},
			//dataType:'json',
			success: function(respuesta){
				$('#categoria_group').html(respuesta);
				$('#categoria_group .select2').select2();
			}
		});
		
	});
	
	
});
</script>