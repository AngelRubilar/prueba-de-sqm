<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $cliente; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $proyecto; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $subproyecto; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('unit'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->unidad;//to_decimal_format($model_info->unidad);?>
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