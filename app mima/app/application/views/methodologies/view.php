<?php echo form_open("", array("id" => "methodology-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
	<div class="form-group">
		<label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
		<div class="col-md-9">
			<?php echo $methodology_info->nombre ? $methodology_info->nombre : "-"; ?>
		</div>
	</div>

   <?php if($huellas_metodologia) { ?>
        <div class="form-group">
            <label for="huellas_metodologia" class="col-md-3"><?php echo lang('footprints'); ?></label>
            <div class="col-md-9">
            	<div class="row">
                    <?php 
                        $array_nombres = array();
						$array_icono = array();
						foreach($huellas_metodologia as $index => $fu){
							$array_nombres[$index] = $fu["nombre"];
							$array_icono[$index] = $fu["icono"];
							echo ' <div class="col" style="display:block; float:left; margin-right: 10px; margin-bottom: 5px;"><img heigth="20" width="20" src="/assets/images/impact-category/'.$array_icono[$index].' "/> <span class="label label-primary">'.$array_nombres[$index].'</span> </div>';
						}
                    ?>
            	</div>
            </div>
        </div>
    <?php } ?>
    
    <div class="form-group">
        <label for="description" class="col-md-3"><?php echo lang('description'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($methodology_info->descripcion) ? $methodology_info->descripcion : '-';
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $methodology_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($methodology_info->modified)?$methodology_info->modified:'-';
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
