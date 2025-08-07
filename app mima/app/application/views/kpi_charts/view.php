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

    <div class="form-group">
        <label for="item" class="<?php echo $label_column; ?>"><?php echo lang('kpi_item'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->item); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="subitem" class="<?php echo $label_column; ?>"><?php echo lang('kpi_subitem'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->subitem); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="tipo_grafico" class="<?php echo $label_column; ?>"><?php echo lang('chart_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->tipo_grafico); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="submodulo_grafico" class="<?php echo $label_column; ?>"><?php echo lang('submodule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->submodulo_grafico); ?>
        </div>
    </div>
        
    <?php foreach($series as $nombre_serie => $serie){ ?>

        <div class="form-group">
            <label for="<?php echo $nombre_serie; ?>" class="<?php echo $label_column; ?>"><?php echo lang($nombre_serie)." (".$serie["tipo_unidad"].")"; ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $serie["valor"]; ?>
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