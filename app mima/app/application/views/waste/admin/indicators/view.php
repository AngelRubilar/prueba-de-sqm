<?php echo form_open("", array("id" => "view_indicator-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
	<div class="form-group">
		<label for="client" class="col-md-3"><?php echo lang('client'); ?></label>
		<div class="col-md-9">
			<?php echo $client ? $client : "-"; ?>
		</div>
	</div>
	
    <div class="form-group">
		<label for="project" class="col-md-3"><?php echo lang('project'); ?></label>
		<div class="col-md-9">
			<?php echo $project ? $project : "-"; ?>
		</div>
	</div>
    
	<div class="form-group">
		<label for="indicator_name" class="col-md-3"><?php echo lang('indicator_name'); ?></label>
		<div class="col-md-9">
			<?php echo $indicator_name ? $indicator_name : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="unit" class="col-md-3"><?php echo lang('unit'); ?></label>
		<div class="col-md-9">
			<?php echo $unit ? $unit : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="color" class="col-md-3"><?php echo lang('color'); ?></label>
		<div class="col-md-9" style="display: inline-block">
			<div class="col-md 4" style="border: 1px solid black; height:15px; width:15px; border-radius: 50%; background:<?php echo $color ?>;"></div>
			<div class="col-md 4"><?php echo $color ? $color : "-"; ?></div>
		</div>
	</div>
    
    <div class="form-group">
		<label for="icon" class="col-md-3"><?php echo lang('icon'); ?></label>
		<div class="col-md-9">
			<i class="fa <?php echo $icon ? $icon : "-"; ?>"> <?php echo $icon ? $icon : "-"; ?></i>
		</div>
	</div>
	
    <div class="form-group">
		<label for="category" class="col-md-3"><?php echo lang('category'); ?></label>
		<div class="col-md-9">
			<?php echo $categories ? $categories : "-"; ?>
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
	$(document).ready(function(){
		
	});

</script>