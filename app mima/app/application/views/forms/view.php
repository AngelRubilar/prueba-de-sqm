<?php echo form_open("", array("id" => "forms-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
	<input type="hidden" name="id" value="<?php echo $form_info->id; ?>" />
	<div class="form-group">
		<label for="nombre" class="col-md-3"><?php echo lang('name'); ?></label>
		<div class="col-md-9">
			<?php
			echo $form_info->nombre;
			?>
		</div>
	</div>
	
    <div class="form-group">
		<label for="descripcion" class="col-md-3"><?php echo lang('description'); ?></label>
		<div class="col-md-9">
			<?php
			if($form_info->descripcion){
				echo htmlspecialchars_decode($form_info->descripcion);
			} else {
				echo "-";
			}
			?>
		</div>
	</div>
    
	<div class="form-group">
		<label for="tipo_formulario" class="col-md-3"><?php echo lang('client'); ?></label>
		<div class="col-md-9">
			<?php
			echo $cliente->company_name;
			?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="tipo_formulario" class="col-md-3"><?php echo lang('project'); ?></label>
		<div class="col-md-9">
			<?php
			echo $proyecto->title;
			?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="form_number" class="col-md-3"><?php echo lang('form_number'); ?></label>
		<div class="col-md-9">
			<?php
			echo $form_info->numero;
			?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="codigo" class="col-md-3"><?php echo lang('code'); ?></label>
		<div class="col-md-9">
			<?php
			echo $form_info->codigo;
			?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="codigo" class="col-md-3"><?php echo lang('icon'); ?></label>
		<div class="col-md-9">
        <!-- <img class='' heigth='20' width='20' src='/assets/images/icons/" + state.text + "'/>" + "&nbsp;&nbsp;" + state.text; -->
       		<img heigth='20' width='20' src='/assets/images/icons/<?php echo $form_info->icono; ?>'/> &nbsp;<?php echo $form_info->icono; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="tipo_formulario" class="col-md-3"><?php echo lang('category'); ?></label>
		<div class="col-md-9">
			<?php
			echo $form_info->tipo_formulario;
			?>
		</div>
	</div>
    
    <?php if ($form_info->flujo) { ?>
        <div class="form-group">
            <label for="flow" class="col-md-3"><?php echo lang('flow'); ?></label>
            <div class="col-md-9">
                <?php
                echo $form_info->flujo;
                ?>
            </div>
        </div>
    <?php } ?>
    
	<?php if ($nombre_unidad) { ?>
        <div class="form-group">
            <label for="unit_field_name" class="col-md-3"><?php echo lang('unit_field_name'); ?></label>
            <div class="col-md-9">
                <?php
                echo $nombre_unidad;
                ?>
            </div>
        </div>
	<?php } ?>
	
	<?php if ($tipo_unidad) { ?>
        <div class="form-group">
            <label for="unit_type" class="col-md-3"><?php echo lang('unit_type'); ?></label>
            <div class="col-md-9">
                <?php
                echo $tipo_unidad;
                ?>
            </div>
        </div>
	<?php } ?>
	
	<?php if ($unidad) { ?>
        <div class="form-group">
            <label for="unit" class="col-md-3"><?php echo lang('unit'); ?></label>
            <div class="col-md-9">
                <?php
                echo $unidad;
                ?>
            </div>
        </div>
	<?php } ?>
    
    <?php if ($form_info->flujo == "Residuo") { ?>
        <div class="form-group">
            <label for="type_of_treatment" class="col-md-3"><?php echo lang('default_type_of_treatment'); ?></label>
            <div class="col-md-9">
                <?php
                echo $tipo_tratamiento;
                ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?></label>
            <div class="col-md-9">
                <?php
				echo form_checkbox("disabled_field", "1", ($disabled_field) ? true : false, "id='disabled_field' onclick='return false;'");
				?>
            </div>
        </div>
	<?php } ?>
	
    <?php if ($form_info->flujo == "Consumo"){ ?>
    	
        <div class="form-group">
            <label for="type_of_treatment" class="col-md-3"><?php echo lang('type_of_origin'); ?></label>
            <div class="col-md-9">
                <?php
                echo $type_of_origin;
                ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?></label>
            <div class="col-md-9">
                <?php
				echo form_checkbox("disabled_field", "1", ($type_of_origin_disabled_field) ? true : false, "id='disabled_field' onclick='return false;'");
				?>
            </div>
        </div>
        
        <?php if($default_matter) { ?>
        	
            <div class="form-group">
                <label for="disabled_field" class="col-md-3"><?php echo lang('default_matter'); ?></label>
                <div class="col-md-9">
                    <?php
					echo $default_matter;
                    ?>
                </div>
            </div>
            
		<?php } ?>
        
    <?php } ?>
    
    <?php if ($form_info->flujo == "No Aplica"){ ?>
	
    	<div class="form-group">
            <label for="default_type" class="col-md-3"><?php echo lang('default_type'); ?></label>
            <div class="col-md-9">
                <?php
                echo $default_type;
                ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="disabled_field" class="col-md-3"><?php echo lang('disabled_field'); ?></label>
            <div class="col-md-9">
                <?php
				echo form_checkbox("disabled_field", "1", ($disabled_field) ? true : false, "id='disabled_field' onclick='return false;'");
				?>
            </div>
        </div>
	
	<?php } ?>
    
    <?php if($materiales_de_formulario) { ?>
        <div class="form-group">
            <label for="materiales" class="col-md-3"><?php echo lang('materials'); ?></label>
            <div class="col-md-9">
            
                <?php 
                    $array_nombres = array();
                        foreach($materiales_de_formulario as $index => $material){
                            $array_nombres[$index] = $material["nombre"];
                        }
                    echo implode(', ', $array_nombres);
                ?>
            </div>
        </div>
    <? } ?>
    
    <?php if($categorias_de_formulario) { ?>
        <div class="form-group">
            <label for="categorias" class="col-md-3"><?php echo lang('categories'); ?></label>
            <div class="col-md-9">
            
                <?php 
                    $array_nombres = array();
                        foreach($categorias_de_formulario as $index => $cat){
                            $array_nombres[$index] = $cat->nombre;
                        }
                    echo implode(', ', $array_nombres);
                ?>
            </div>
        </div>
    <? } ?>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $form_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($form_info->modified)?$form_info->modified:'-';
            ?>
        </div>
    </div>
    
    <div class="form-group" style="text-align: center;">
    	<h4><?php echo lang("preview"); ?></h4>
	</div>

	<?php echo $preview_form_fields; ?>
   
</div>

<div class="modal-footer">

    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		setTimePicker('.time_preview');
		$('.select2').select2();
		$('.rut').rut({
			formatOn: 'keyup',
			minimumLength: 8,
			validateOn: 'change'
		});
		setDatePicker(".fecha, .datepicker");
	});

</script>