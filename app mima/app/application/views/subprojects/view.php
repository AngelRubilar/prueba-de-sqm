<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre; ?>
        </div>
    </div>
    
    <!-- listar todos los clientes -->
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $cliente; ?>
        </div>
    </div>
     <!-- listar todos los clientes -->
    
    <!-- listar todos los proyectos -->
    <div id="proyectos_group">
        <div class="form-group">
            <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $proyecto; ?>
            </div>
        </div>
    </div>
     <!-- listar todos los proyectos -->
     
    <div id="proyectos_group">
        <div class="form-group">
            <label for="description" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo ($model_info->descripcion)?$model_info->descripcion:'-'; ?>
            </div>
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