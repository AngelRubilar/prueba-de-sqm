<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
    <div class="modal-body clearfix">

        <div class="form-group">
            <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $model_info->name; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $client->company_name; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $project->title; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sector" class="<?php echo $label_column; ?>"><?php echo lang('sector'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $air_sector->name; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sector" class="<?php echo $label_column; ?>"><?php echo lang('is_receptor?'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $model_info->is_receptor ? lang("yes") : lang("no"); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo ($model_info->description) ? $model_info->description : '-'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="latitude" class="<?php echo $label_column; ?>"><?php echo lang('location')." / ".lang("latitude"); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $model_info->latitude; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="longitude" class="<?php echo $label_column; ?>"><?php echo lang('location')." / ".lang("longitude"); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $model_info->longitude; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="variables" class="<?php echo $label_column; ?>"><?php echo lang('variables'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $html_variables; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
            <div class="col-md-9">
                <?php echo $model_info->created; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
            <div class="col-md-9">
                <?php echo ($model_info->modified) ? $model_info->modified: '-'; ?>
            </div>
        </div>
        
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    </div>

<?php echo form_close(); ?>

<script type="text/javascript">

</script> 