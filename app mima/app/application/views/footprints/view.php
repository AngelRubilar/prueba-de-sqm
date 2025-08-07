<?php echo form_open("", array("id" => "materials-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->nombre;
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="tipo_unidad" class="col-md-3"><?php echo lang('unit_type'); ?></label>
        <div class="col-md-9">
            <?php
            echo $tipo_unidad->nombre;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="unit" class="col-md-3"><?php echo lang('unit'); ?></label>
        <div class="col-md-9">
            <?php
            echo $unidad->nombre;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="indicador" class="col-md-3"><?php echo lang('indicator'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->indicador;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="icono" class="col-md-3"><?php echo lang("icon"); ?></label>
        <div class="col-md-9">
        	<img heigth='40' width='40' src='/assets/images/impact-category/<?php echo $model_info->icono; ?>'/> &nbsp;<?php echo $model_info->icono; ?>
        </div>
    </div>  
	
    <div class="form-group">
        <label for="abreviatura" class="col-md-3"><?php echo lang('abbreviation'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->abreviatura) ? $model_info->abreviatura : '-';
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-md-3"><?php echo lang('description'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->descripcion) ? $model_info->descripcion : '-';
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


<script type="text/javascript">
    $(document).ready(function() {
		
        
    });
</script>    