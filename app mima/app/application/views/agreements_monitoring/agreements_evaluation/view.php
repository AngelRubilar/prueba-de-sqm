<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="form-group">
        <label for="code" class="col-md-3"><?php echo lang('code'); ?></label>
        <div class="col-md-9">
        	<?php echo $codigo; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="agreement" class="col-md-3"><?php echo lang('agreement'); ?></label>
        <div class="col-md-9">
        	<?php echo $nombre_acuerdo; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="managing" class="col-md-3"><?php echo lang('managing'); ?></label>
        <div class="col-md-9">
        	<?php echo $nombre_gestor; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="stakeholder" class="col-md-3"><?php echo lang('stakeholder'); ?></label>
        <div class="col-md-9">
        	<?php echo $nombre_stakeholder; ?>
        </div>
    </div>
	<div class="form-group">
        <label for="processing_status" class="col-md-3"><?php echo lang('processing_status'); ?></label>
        <div class="col-md-9">
        	<?php echo $estado_tramitacion; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="activities_status" class="col-md-3"><?php echo lang('activities_status'); ?></label>
        <div class="col-md-9">
        	<?php echo $estado_actividades; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="financial_status" class="col-md-3"><?php echo lang('financial_status'); ?></label>
        <div class="col-md-9">
        	<?php echo $estado_financiero; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="observations" class="col-md-3"><?php echo lang('observations'); ?></label>
        <div class="col-md-9">
        	<?php echo $observaciones; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="last_modification" class="col-md-3"><?php echo lang('last_monitoring'); ?></label>
        <div class="col-md-9">
        	<?php echo $ult_mod; ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('[data-toggle="tooltip"]').tooltip();
		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');
		
	});

</script>    