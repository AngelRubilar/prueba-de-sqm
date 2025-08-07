<?php echo form_open("", array("id" => "view_thresholds-form", "class" => "general-form", "role" => "form")); ?>
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
		<label for="module" class="col-md-3"><?php echo lang('module'); ?></label>
		<div class="col-md-9">
			<?php echo $module_name ? $module_name : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="form" class="col-md-3"><?php echo lang('form'); ?></label>
		<div class="col-md-9">
			<?php echo $form ? $form : "-"; ?>
		</div>
	</div>
	
    <div class="form-group">
		<label for="label" class="col-md-3"><?php echo lang('label'); ?></label>
		<div class="col-md-9">
			<?php echo $label ? $label : "-"; ?>
		</div>
	</div>

    <div class="form-group">
		<label for="material" class="col-md-3"><?php echo lang('material'); ?></label>
		<div class="col-md-9">
			<?php echo $material_name ? $material_name : "-"; ?></i>
		</div>
	</div>
	
    <div class="form-group">
		<label for="categorie" class="col-md-3"><?php echo lang('categorie'); ?></label>
		<div class="col-md-9">
			<?php echo $category_name ? $category_name : "-"; ?>
		</div>
	</div>
	

    <div class="form-group">
		<label for="unit_type" class="col-md-3"><?php echo lang('unit_type'); ?></label>
		<div class="col-md-9">
			<?php echo $unit_type ? $unit_type : "-"; ?>
		</div>
	</div>

	<div class="form-group">
		<label for="unit_value" class="col-md-3"><?php echo lang('unit_value'); ?></label>
		<div class="col-md-9">
			<div class="col-md-10 p0">
				<?php echo $unit_value ? $unit_value : "-"; echo " ". $unit ?>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="risk_value" class="col-md-3"><?php echo lang('risk_value'); ?></label>
		<div class="col-md-9">
			<?php echo $risk_value ? $risk_value : "-"; ?>
		</div>
	</div>	
	
	<div class="form-group">
		<label for="threshold_value" class="col-md-3"><?php echo lang('threshold_value'); ?></label>
		<div class="col-md-9">
			<?php echo $threshold_value ? $threshold_value : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $thresholds_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($thresholds_info->modified)?$thresholds_info->modified:'-';
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