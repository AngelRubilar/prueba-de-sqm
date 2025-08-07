<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $cliente;?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_estado; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="evaluation_type" class="<?php echo $label_column; ?>"><?php echo lang('evaluation_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->tipo_evaluacion); ?>
        </div>
	</div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->categoria; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('color'); ?></label>
        <div class="<?php echo $field_column; ?>">
        	<div id="coloricon1" style="border: 1px solid black; height:15px; width:15px; background-color:<?php echo $model_info->color; ?>; border-radius: 50%;"></div>        
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