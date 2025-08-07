<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="form-group">
        <label for="date" class="col-md-3"><?php echo lang('date'); ?></label>
        <div class="col-md-9">
        	<?php echo $fecha; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
        <div class="col-md-9">
        	<?php echo $nombre; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="visit_purpose" class="col-md-3"><?php echo lang('visit_purpose'); ?></label>
        <div class="col-md-9">
        	<?php echo $proposito_visita; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="responsible" class="col-md-3"><?php echo lang('responsible'); ?></label>
        <div class="col-md-9">
        	<?php echo $responsable; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="answer" class="col-md-3"><?php echo lang('answer'); ?></label>
        <div class="col-md-9">
        	<?php echo $respuesta; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="answer_status" class="col-md-3"><?php echo lang('answer_status'); ?></label>
        <div class="col-md-9">
        	<?php echo $estado_respuesta; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="last_modification" class="col-md-3"><?php echo lang('last_monitoring'); ?></label>
        <div class="col-md-9">
        	<?php echo $ultima_modificacion; ?>
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
		/*
		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');
		*/
	});

</script>    