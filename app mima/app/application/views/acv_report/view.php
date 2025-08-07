<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
    <div class="form-group">
        <label for="name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->nombre; ?>
        </div>
    </div>
    
    <!-- listar todos los clientes -->
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $cliente;?>
        </div>
    </div>
     <!-- listar todos los clientes -->
    
    <!-- listar todos los proyectos -->
    <div id="proyectos_group">
        <div class="form-group">
            <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php echo $proyecto; ?>
            </div>
        </div>
    </div>
     <!-- listar todos los proyectos -->
	
    <!--
	<?php if($unidades_funcionales_subproyecto) { ?>
        <div class="form-group">
            <label for="materiales" class="<?php echo $label_column; ?>"><?php echo lang('functional_units'); ?></label>
            <div class="<?php echo $field_column; ?>">
            
                <?php 
                    $array_nombres = array();
                        foreach($unidades_funcionales_subproyecto as $index => $fu){
                            $array_nombres[$index] = $fu["nombre"];
                        }
                    echo implode(', ', $array_nombres);
                ?>
            </div>
        </div>
    <? } ?>
	-->
    
    <div class="form-group">
        <label for="infrastructure_type" class="<?php echo $label_column; ?>"><?php echo lang("infrastructure_type") ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php echo $model_info->tipo_infraestructura; ?>
        </div>
    </div> 

    <div class="form-group">
        <?php 
		
			if($model_info->tipo_infraestructura == "Generación"){
				
				$html = '';
				$html = '<div style="text-align: center;"><h5>'.lang("generation").'</h5></div>';
				$html .= '<br />';
				/*
				$html .= '<div class="form-group">';
				$html .= '<label for="technologies" class="col-md-3">'.lang('technology').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $model_info->tecnologia;				
				$html .= '</div>';
				$html .= '</div>';
				*/
				
				$html .= '<div class="form-group">';
				$html .= '<label for="no_of_generation_equipment" class="col-md-3">'.lang('no_of_generation_equipment').'</label>';
				$html .= '<div class="col-md-9">';			
				$html .= $model_info->num_equipos_generacion;
				$html .= '</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="unit_power_of_equipment" class="col-md-3">'.lang('unit_power_of_equipment_mw').'</label>';
				$html .= '<div class="col-md-9">';				
				$html .= $model_info->potencia_unitaria_equipos;
				$html .= '</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="surface" class="col-md-3">'.lang('surface_km2').'</label>';
				$html .= '<div class="col-md-9">';			
				$html .= $model_info->superficie;							
				$html .= '</div>';
				$html .= '</div>';
				
				echo $html;
				
			}
			
			if($model_info->tipo_infraestructura == "Transmisión"){
				
				$html = '';
				$html = '<div style="text-align: center;"><h5>'.lang("transmission").'</h5></div>';
				$html .= '<br />';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="type_of_substation" class="col-md-3">'.lang('type_of_substation').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $model_info->tipo_subestacion;
				$html .= '</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="transformation_capacity" class="col-md-3">'.lang('transformation_capacity_kv').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $model_info->capacidad_transformacion;
				$html .= '</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="number_of_high_voltage_towers" class="col-md-3">'.lang('number_of_high_voltage_towers').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $model_info->num_torres_alta_tension;		
				$html .= '</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="line_length" class="col-md-3">'.lang('line_length').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $model_info->longitud_linea;			
				$html .= '</div>';
				$html .= '</div>';
					
				$html .= '<div class="form-group">';
				$html .= '<label for="surface" class="col-md-3">'.lang('surface_km2').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= $model_info->superficie;
				$html .= '</div>';
				$html .= '</div>';
				
				echo $html;
				
			}
		
		?>
    </div> 

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script>    