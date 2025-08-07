<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="database" class="<?php echo $label_column; ?>"><?php echo lang('databases'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_bd; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="footprint_format" class="<?php echo $label_column; ?>"><?php echo lang('footprint_format'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_formato_huella; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="calculation_methodology" class="<?php echo $label_column; ?>"><?php echo lang('calculation_methodology'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_metodologia; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="environmental_footprint" class="<?php echo $label_column; ?>"><?php echo lang('environmental_footprint'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_huella; ?>
        </div>
    </div>
    
     <div class="form-group">
        <label for="material" class="<?php echo $label_column; ?>"><?php echo lang('material'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_material; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="category" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_categoria; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="subcategory" class="<?php echo $label_column; ?>"><?php echo lang('subcategory'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_subcategoria; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="unit_type" class="<?php echo $label_column; ?>"><?php echo lang('unit_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $tipo_unidad; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="unit" class="<?php echo $label_column; ?>"><?php echo lang('unit'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $unidad; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="factor" class="<?php echo $label_column; ?>"><?php echo lang('factor'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->factor; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="<?php echo $label_column; ?>"><?php echo lang('created_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="<?php echo $label_column; ?>"><?php echo lang('modified_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
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