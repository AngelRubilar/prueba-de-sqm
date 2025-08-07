<?php echo form_open("", array("id" => "materials-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->nombre;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="categoria" class="col-md-3"><?php echo lang('categories'); ?></label>
        <div class="col-md-9">
        
            <?php 
                $array_nombres = array();
                foreach($categorias2 as $index => $cat){
                    $array_nombres[$index] = $cat["nombre"];
                }
                $nombres = implode(', ', $array_nombres);
                echo ($nombres) ? $nombres : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model->modified)?$model->modified:'-';
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