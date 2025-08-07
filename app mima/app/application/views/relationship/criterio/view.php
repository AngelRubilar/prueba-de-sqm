<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->company_name; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->title; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="form" class="<?php echo $label_column; ?>"><?php echo lang('environmental_records'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->formulario; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="material" class="<?php echo $label_column; ?>"><?php echo lang('material'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->material; ?>
        </div>
    </div>
    
    <?php 
		
		if(isset($model_info->tipo_by_criterio)){
			$json_data = json_decode($model_info->tipo_by_criterio,true);

			if($json_data["id_campo_sp"] == "1" ){
				$campo_sp = $flujo_formulario_criterio == "Residuo" ? lang('type_of_treatment') : lang('type');
			}else{
				$campo_sp = ($model_info->campo_sp)?$model_info->campo_sp:"-";
			}
			
			if($json_data["id_campo_pu"] == "1"){
				$campo_pu = $flujo_formulario_criterio == "Residuo" ? lang('type_of_treatment') : lang('type');
			}else{
				$campo_pu = ($model_info->campo_pu)?$model_info->campo_pu:"-";
			}
			
			if($json_data["id_campo_fc"] == "1"){
				$campo_fc = $flujo_formulario_criterio == "Residuo" ? lang('type_of_treatment') : lang('type');
			}else{
				$campo_fc = ($model_info->campo_fc)?$model_info->campo_fc:"-";
			}
		}else{
			$campo_sp = ($model_info->campo_sp)?$model_info->campo_sp:"-";
			$campo_pu = ($model_info->campo_pu)?$model_info->campo_pu:"-";
			$campo_fc = ($model_info->campo_fc)?$model_info->campo_fc:"-";
		}

	?>
    
    
    <div class="form-group">
        <label for="campo_sp" class="<?php echo $label_column; ?>"><?php echo lang('subproject_rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $campo_sp; ?>
        </div>
    </div>

	<div class="form-group">
        <label for="campo_pu" class="<?php echo $label_column; ?>"><?php echo lang('unit_processes_rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $campo_pu; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="campo_fc" class="<?php echo $label_column; ?>"><?php echo lang('fc_rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $campo_fc; ?>
        </div>
    </div>
	
    <div class="form-group">
        <label for="etiqueta" class="<?php echo $label_column; ?>"><?php echo lang('label'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->etiqueta; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?$model_info->modified:'-';
            ?>
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script>    