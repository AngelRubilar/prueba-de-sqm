<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <div class="form-group">
        <label for="date_filed" class="col-md-3"><?php echo lang('permitting_number'); ?></label>
        <div class="col-md-9">
        	<?php echo $model_info->numero_permiso; ?>
        </div>
    </div>
	
    <div class="form-group">
        <label for="date_filed" class="col-md-3"><?php echo lang('name'); ?></label>
        <div class="col-md-9">
        	<?php echo $model_info->nombre_permiso; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('phases'); ?></label>
        <div class="col-md-9">
         	<?php
				echo $html_fases;
			?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="entity" class="col-md-3"><?php echo lang('entity'); ?></label>
        <div class="col-md-9">
        	<?php echo $model_info->entidad; ?>
        </div>
    </div>
    
    <!--
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('reportability'); ?></label>
        <div class="col-md-9">
         	<?php echo ($model_info->reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>'; ?>
        </div>
    </div>
    -->
    
	<?php 
        
        $html = '';
        foreach($campos_permiso as $campo){
			
			// 11 = texto fijo | 12 = divisor
			if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
	
				$html .= '<div class="form-group">';
					$html .= '<div class="col-md-12">';
					$html .= $Upload_permittings_controller->get_field_value($campo["id_campo"], $model_info->id);
					$html .= '</div>';
				$html .= '</div>';
				
			} else {
				
				//echo $campo["nombre_campo"]."<br>";
				$html .= '<div class="form-group">';
					$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
					$html .= '<div class="col-md-9">';
					$html .= $Upload_permittings_controller->get_field_value($campo["id_campo"], $model_info->id);
					$html .= '</div>';
				$html .= '</div>';
				
			}
 
        }
        
        echo $html;
    
    ?>
    
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
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('[data-toggle="tooltip"]').tooltip();
		$('#environmental_records-form .select2').select2();
		setDatePicker("#environmental_records-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');
		
	});

</script>    