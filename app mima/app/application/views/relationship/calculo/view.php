<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="client_id" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->company_name;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->title;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->etiqueta;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="fc_rule" class="<?php echo $label_column; ?>"><?php echo lang('fc_rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo ($calculo->criterio_fc)?$calculo->criterio_fc:"-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="calculation" class="<?php echo $label_column; ?>"><?php echo lang('calculation'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $campos_unidad;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="database" class="<?php echo $label_column; ?>"><?php echo lang('database'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->nombre_bd;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="category" class="<?php echo $label_column; ?>"><?php echo lang('category'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->nombre_categoria;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="subcategory" class="<?php echo $label_column; ?>"><?php echo lang('subcategory'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->nombre_subcategoria;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="etiqueta" class="<?php echo $label_column; ?>"><?php echo lang('label'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo $calculo->etiqueta_calculo;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $calculo->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($calculo->modified)?$calculo->modified:'-';
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