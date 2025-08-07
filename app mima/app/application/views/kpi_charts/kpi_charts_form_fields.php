<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="id_cliente" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo $model_info->nombre_cliente;
		?>
    </div>
</div>

<div class="form-group">
    <label for="id_fase" class="<?php echo $label_column; ?>"><?php echo lang('phase'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo $model_info->nombre_fase;
		?>
    </div>
</div>

<div class="form-group">
    <label for="id_proyecto" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $model_info->nombre_proyecto;
        ?>
    </div>
</div>

<div class="form-group">
    <label for="item" class="<?php echo $label_column; ?>"><?php echo lang('kpi_item'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo lang($model_info->item);
        ?>
    </div>
</div>

<div class="form-group">
    <label for="subitem" class="<?php echo $label_column; ?>"><?php echo lang('kpi_subitem'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo lang($model_info->subitem);
        ?>
    </div>
</div>

<div class="form-group">
    <label for="tipo_grafico" class="<?php echo $label_column; ?>"><?php echo lang('chart_type'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo lang($model_info->tipo_grafico);
        ?>
    </div>
</div>

<div class="form-group">
    <label for="submodulo_grafico" class="<?php echo $label_column; ?>"><?php echo lang('submodule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo lang($model_info->submodulo_grafico);
        ?>
    </div>
</div>

<?php foreach($series as $nombre_serie => $valor_serie){ ?>
	
    <?php 
				
		$opciones_valores = array(
			"id_cliente" => $model_info->id_cliente,
			"id_fase" => $model_info->id_fase,
			"id_proyecto" => $model_info->id_proyecto,
			"id_tipo_unidad" => $id_tipo_unidad
		);
		$valores = $KPI_Charts_controller->get_array_values_of_series($opciones_valores);
		
	?>
    
	<div class="form-group">
        <label for="<?php echo $nombre_serie; ?>" class="<?php echo $label_column; ?>"><?php echo lang($nombre_serie)." (".$tipo_unidad.")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown($nombre_serie, $valores, array($valor_serie), "id='" . $nombre_serie . "' class='select2'");
            ?>
        </div>
    </div>

<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#kpi_charts-form .select2').select2();
		
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
		
		$(document).on('click', '.delete', function(){
			initScrollbar(".modal-body", {setHeight: 50});
		});

    });
</script>