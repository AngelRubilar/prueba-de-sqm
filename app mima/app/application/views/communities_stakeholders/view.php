<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('stakeholder'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('rut'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->rut; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('type_of_organization'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $tipo_organizacion; ?>
        </div>
    </div>
    
	<div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('locality'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo ($model_info->localidad) ? $model_info->localidad : "-"; ?>
        </div>
    </div>
    
    <!-- CAMPOS ADICIONALES -->
    
    <?php 
        
        $html = '';
        foreach($campos_stakeholder_matrix as $campo){
            //echo $campo["nombre_campo"]."<br>";
            $html .= '<div class="form-group">';
                if(($campo["id_tipo_campo"] == 12)||($campo["id_tipo_campo"] == 11)){
					$html .= '<div class="col-md-12">';
					$html .= '<div style="word-wrap: break-word;">';
					$html .= $campo["default_value"];
					$html .= '</div>';
					$html .= '</div>';
				} else {
					$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
					$html .= '<div class="col-md-9">';
					$html .= $Communities_stakeholders_controller->get_field_value($campo["id_campo"], $model_info->id);
					$html .= '</div>';
				}
			$html .= '</div>';
        }
        
        echo $html;
    
    ?>

    <hr>
        
    <div class="pb10" style="text-align: center;">
      <h4><?php echo lang("contact_data"); ?></h4>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('contact'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombres_contacto . " " . $model_info->apellidos_contacto; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo ($model_info->telefono_contacto) ? $model_info->telefono_contacto : "-"; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo ($model_info->correo_contacto) ? $model_info->correo_contacto : "-"; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('address'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo ($model_info->direccion_contacto) ? $model_info->direccion_contacto : "-"; ?>
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