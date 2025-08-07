<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_cliente; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="phase" class="<?php echo $label_column; ?>"><?php echo lang('phase'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_fase; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_proyecto; ?>
        </div>
    </div>
    
    <?php foreach($datos as $nombre_indicador => $datos_indicador) { ?>
            
   		<div class="form-group">
        <?php $unidad = $this->Unity_model->get_one($datos_indicador["unidad"]); ?>
        <?php $nombre_unidad = $unidad->nombre; ?>
        <?php $nombre_unidad_real = $unidad->nombre_real; ?>
        
		<?php $tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad); ?>
        <?php $nombre_tipo_unidad = $tipo_unidad->nombre; ?>
       
        <?php 
			/*
			// Exepciones
			if($nombre_indicador == "operating_hours" || $nombre_indicador == "enel_hours_worked" 
			|| $nombre_indicador == "contractor_hours_worked" || $nombre_indicador == "enel_lost_days"
			|| $nombre_indicador == "contractor_lost_days"){
				$unidad = $nombre_unidad_real;
			} else if($nombre_indicador == "expenses_local_suppliers" || $nombre_indicador == "opex_total"
					|| $nombre_indicador == "environmental_expenses"){
				$unidad = "€";
			} else if($nombre_indicador == "noise_levels_near_population"){
				$unidad = "db";
			} else {
				$unidad = $nombre_unidad; 
			}
			*/
		?>

            <label for="<?php echo $nombre_indicador; ?>" class="<?php echo $label_column; ?>"><?php echo lang($nombre_indicador) . " (" . $nombre_tipo_unidad . ")"; ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
					if($datos_indicador["valor"]){
						$valor = $this->KPI_Values_model->get_one($datos_indicador["valor"]);
						$nombre_valor = $valor->nombre_valor;
					} else {
						$nombre_valor = "-";
					}
                	echo $nombre_valor;
                ?>
            </div>
        </div>
    
    <?php } ?>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 