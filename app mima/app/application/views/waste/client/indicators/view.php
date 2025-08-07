<?php echo form_open("", array("id" => "view_client_indicator-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
	<div class="form-group">
		<label for="value" class="col-md-3"><?php echo lang('value'); ?></label>
		<div class="col-md-9">
			<?php echo $value ? $value : "-"; ?>
		</div>
	</div>
	
    <div class="form-group">
		<label for="date_since" class="col-md-3"><?php echo lang('date_since'); ?></label>
		<div class="col-md-9">
			<?php echo $date_since ? $date_since : "-"; ?>
		</div>
	</div>
    
	<div class="form-group">
		<label for="date_until" class="col-md-3"><?php echo lang('date_until'); ?></label>
		<div class="col-md-9">
			<?php echo $date_until ? $date_until : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
        <label for="created_by" class="col-md-3"><?php echo lang('created_by'); ?></label>
        <div class="col-md-9">
            <?php
			echo $created_by;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_by" class="col-md-3"><?php echo lang('modified_by'); ?></label>
        <div class="col-md-9">
            <?php
            echo $modified_by;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
			echo time_date_zone_format($model_info->created, $id_proyecto);
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?time_date_zone_format($model_info->modified, $id_proyecto):'-';
            ?>
        </div>
    </div>
    
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		
	});

</script>