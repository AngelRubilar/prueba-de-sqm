<?php echo form_open("", array("id" => "categories_alias-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('client'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->cliente;
            ?>
        </div>
    </div>
   
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('category'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->categoria;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('alias'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->alias;
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