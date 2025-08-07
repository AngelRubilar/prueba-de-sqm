<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo get_date_format($model_info->fecha, $id_proyecto); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="type_of_stakeholder" class="<?php echo $label_column; ?>"><?php echo lang('type_of_stakeholder'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $tipo_stakeholder; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="visit_purpose" class="<?php echo $label_column; ?>"><?php echo lang('visit_purpose'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo lang($model_info->proposito_visita); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="responsible" class="<?php echo $label_column; ?>"><?php echo lang('responsible'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php 
				echo $responsable;
			?>
        </div>
    </div>

    <!-- CAMPOS ADICIONALES -->
    
    <?php 
        
        $html = '';
        foreach($campos_feedback_matrix as $campo){
            //echo $campo["nombre_campo"]."<br>";
            $html .= '<div class="form-group">';
                $html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
                $html .= '<div class="col-md-9">';
                $html .= $Communities_feedback_controller->get_field_value($campo["id_campo"], $model_info->id);
                $html .= '</div>';
            $html .= '</div>';
        }
        
        echo $html;
    
    ?>
    
    <div class="form-group">
		<label for="requires_monitoring" class="col-md-3"><?php echo lang('requires_monitoring'); ?></label>
		<div class="col-md-9">
			<?php
			echo intval($model_info->requires_monitoring) === 1 ? lang('yes') : lang('no');
			?>
		</div>
	</div>
    
    <hr>
	
	<div class="form-group">
		<label for="created_by" class="col-md-3"><?php echo lang('created_by'); ?></label>
		<div class="col-md-9">
			<?php
			echo $model_info->created_by;
			?>
		</div>
	</div>
	
	<div class="form-group">
		<label for="modified_by" class="col-md-3"><?php echo lang('modified_by'); ?></label>
		<div class="col-md-9">
			<?php
			echo $model_info->modified_by ? $model_info->modified_by : "-";
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
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 