<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $cliente->company_name; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php echo $proyecto->title; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="rule" class="<?php echo $label_column; ?>"><?php echo lang('rule'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php echo $criterio->etiqueta; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="subproject_rule" class="<?php echo $label_column; ?>"><?php echo lang('subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php echo $criterio_sp?$criterio_sp:"-"; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="unit_processes_rule" class="<?php echo $label_column; ?>"><?php echo lang('unit_process'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php echo $criterio_pu?$criterio_pu:"-"; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="target_subproject" class="<?php echo $label_column; ?>"><?php echo lang('target_subproject'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php echo $subproyecto->nombre; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="target_unitary_process" class="<?php echo $label_column; ?>"><?php echo lang('target_unitary_process'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php echo $proceso_unitario->nombre; ?>
        </div>
    </div>
    
    

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script>    