<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('icon'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <img heigth='20' width='20' src='/assets/images/unit-processes/<?php echo $model_info->icono; ?>'/> &nbsp;<?php echo $model_info->icono; ?>
        </div>
    </div>
    
    <div class="form-group">
		<label for="color" class="<?php echo $label_column; ?>"><?php echo lang('color'); ?></label>
		<div class="<?php echo $field_column; ?>">
			<i style="border: solid black 1px; background-color: <?php echo $model_info->color; ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>
		<?php echo $model_info->color ? $model_info->color : "-"; ?> 
		</div>
	</div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo ($model_info->descripcion) ? $model_info->descripcion : "-"; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="fases" class="col-md-3"><?php echo lang('phases'); ?></label>
        <div class="col-md-9">
        
            <?php 
                $array_nombres = array();
				foreach($fases as $index => $fase){
					$array_nombres[$index] = $fase["nombre"];
				}
                $nombres= implode(', ', $array_nombres);
                echo ($nombres) ? $nombres : "-";
            ?>
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