<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('code'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->codigo; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre_acuerdo; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('description'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->descripcion; ?>
        </div>
    </div>
    
	<div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('period'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
				$periodo = json_decode($model_info->periodo);	
				echo get_date_format($periodo->start_date, $id_proyecto) .  " - " . get_date_format($periodo->end_date, $id_proyecto);
			?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('managing'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php 
				echo $nombre_gestor;
			?>
        </div>
    </div>
    <!--
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('observations'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php 
				echo $model_info->observaciones;
			?>
        </div>
    </div>
    -->
    <!-- CAMPOS ADICIONALES -->
    
    <?php 
        
        $html = '';
        foreach($campos_agreement_matrix as $campo){
            //echo $campo["nombre_campo"]."<br>";
            $html .= '<div class="form-group">';
                $html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
                $html .= '<div class="col-md-9">';
                $html .= $Communities_agreements_controller->get_field_value($campo["id_campo"], $model_info->id);
                $html .= '</div>';
            $html .= '</div>';
        }
        
        echo $html;
    
    ?>
	
    <div class="form-group">
    <label for="stakeholders" class="<?php echo $label_column; ?>"><?php echo lang('stakeholders'); ?></label>
        <div class="<?php echo $field_column; ?>">
        	<?php 
				echo $stakeholders;
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