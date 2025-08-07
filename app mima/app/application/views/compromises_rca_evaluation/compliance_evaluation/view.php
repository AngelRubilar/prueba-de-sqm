<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php //if($evaluations_dropdown) {?>
        <div class="form-group">
          <label for="status" class="col-md-3"><?php echo lang('evaluation'); ?></label>
            <div class="col-md-9">
                <?php
                    if($evaluations_dropdown){
						echo form_dropdown("evaluation", $evaluations_dropdown, $id_evaluacion, "id='evaluation' class='select2' ");
					} else {
						echo form_dropdown("evaluation", array("-" => "- " . lang("no_evaluations") . " -"), "", "id='evaluation' class='select2' ");
					}
                ?>
            </div>
        </div>
    <?php //} ?>
    
    <div id="div_detalle_evaluacion">
    
        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('compromise'); ?></label>
            <div class="col-md-9">
                <?php echo $nombre_compromiso; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('evaluated'); ?></label>
            <div class="col-md-9">
                <?php echo $nombre_evaluado; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('status'); ?></label>
            <div class="col-md-9">
                <?php echo $html_estado; ?>
            </div>
        </div>
        
        <?php if($info_estado->categoria == "No Cumple") { ?>
        	
            <div class="form-group">
                <label for="date_filed" class="col-md-3"><?php echo lang('critical_level'); ?></label>
                <div class="col-md-9">
                    <?php
						$criticidad = $this->Critical_levels_model->get_one($model_info->id_criticidad)->nombre; 
						echo $criticidad; 
					?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="date_filed" class="col-md-3"><?php echo lang('responsible'); ?></label>
                <div class="col-md-9">
                    <?php echo $model_info->responsable_reporte; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="date_filed" class="col-md-3"><?php echo lang('closing_term'); ?></label>
                <div class="col-md-9">
                    <?php echo get_date_format($model_info->plazo_cierre, $id_proyecto); ?>
                </div>
            </div>

        <?php } ?>
        
        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('evidence'); ?></label>
            <div class="col-md-9">
                <?php echo $evidencia; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('observations'); ?></label>
            <div class="col-md-9">
                <?php echo $observaciones; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('evaluator'); ?></label>
            <div class="col-md-9">
                <?php echo $responsable; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="date_filed" class="col-md-3"><?php echo lang('last_evaluation'); ?></label>
            <div class="col-md-9">
                <?php echo $ult_mod; ?>
            </div>
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
		
		$('#evaluation').on('change', function(){
			
			id_evaluacion = $(this).val();			
			id_proyecto = '<?php echo $id_proyecto; ?>';
			
			$.ajax({
				url:  '<?php echo_uri("compromises_rca_evaluation/view_historical_evaluations") ?>',
				type:  'post',
				data: {id_evaluacion:id_evaluacion, id_proyecto:id_proyecto},
				success: function(respuesta){
					$('#div_detalle_evaluacion').html(respuesta);
				}
			});

		})
		
	});

</script>    