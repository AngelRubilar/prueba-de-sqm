<?php echo form_open("", array("id" => "projects-form", "class" => "general-form", "role" => "form")); ?>
<input type="hidden" name="id" value="<?php echo $project_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="modal-body clearfix">
    
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('client'); ?></label>
        <div class="col-md-9">
            <?php
            echo $cliente->company_name;
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('client_label'); ?></label>
        <div class="col-md-9">
            <?php
			echo ($project_info->client_label) ? $project_info->client_label : "-";
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('client_label_rut'); ?></label>
        <div class="col-md-9">
            <?php
			echo ($project_info->client_label_rut) ? $project_info->client_label_rut : "-";
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('legal_representative'); ?></label>
        <div class="col-md-9">
            <?php
			echo ($project_info->legal_representative) ? $project_info->legal_representative : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('project_name'); ?></label>
        <div class="col-md-9">
            <?php
            echo $project_info->title;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('industry'); ?></label>
        <div class="col-md-9">
            <?php
			echo ($industria->nombre) ? $industria->nombre : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('subindustry'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($subrubro->nombre) ? $subrubro->nombre : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('technology'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($tecnologia->nombre) ? $tecnologia->nombre : "-";
            ?>
        </div>
    </div>
    
    <!--
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('country'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->country) ? $project_info->country : "-";
            ?>
        </div>
    </div>
    -->
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('country'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->id_pais) ? $pais->nombre : "-";
            ?>
        </div>
    </div>
   
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('city'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->city) ? $project_info->city : "-";
            ?>
        </div>
    </div>
    
     <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('town'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->state) ? $project_info->state : "-";
            ?>
        </div>
    </div>
	
     <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('environmental_authorization'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->environmental_authorization) ? $project_info->environmental_authorization : "-";
            ?>
        </div>
    </div>
	
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('start_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->start_date != "0000-00-00") ? $project_info->start_date : "-";
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('deadline'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->deadline != "0000-00-00") ? $project_info->deadline : "-";
            ?>
        </div>
    </div>
     <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('initial'); ?></label>
        <div class="col-md-9">
            <?php
            echo $project_info->sigla;
            ?>
        </div>
    </div>
    
     <?php if($miembros_de_proyecto) { ?>
        <div class="form-group">
            <label for="miembros" class="col-md-3"><?php echo lang('members'); ?></label>
            <div class="col-md-9">
            
                <?php 
                    $array_nombres = array();
                        foreach($miembros_de_proyecto as $index => $miembro){
                            $array_nombres[$index] = $miembro["first_name"]." ".$miembro["last_name"];
                        }
                    echo implode(', ', $array_nombres);
                ?>
            </div>
        </div>
    <? } ?>
    
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('description'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->description) ? $project_info->description : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('icon'); ?></label>
        <div class="col-md-9">
        	<?php if($project_info->icono) { ?>
            	<img heigth='20' width='20' src='/assets/images/icons/<?php echo $project_info->icono; ?>'/> &nbsp;<?php echo $project_info->icono; ?>
        	<?php 
				} else {
					echo "-";	
				}
			 ?>
            
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('content'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->contenido) ? $project_info->contenido : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('status'); ?></label>
        <div class="col-md-9">
            <?php
            //echo ($project_info->status) ? $project_info->status : "-";
			if($project_info->status == "open"){
				$status = '<span class="label label-success">'.lang("open").'</span>';
			}elseif($project_info->status == "closed"){
				$status = '<span class="label label-warning">'.lang("closed").'</span>';
			}elseif($project_info->status == "canceled"){
				$status = '<span class="label label-danger">'.lang("canceled").'</span>';
			}else{
				$status = '-';
			}
			echo $status;
            ?>
        </div>
    </div>
    
    <div class="form-group" style="text-align: center;">
    	<h4>Datos de ACV</h4>
	</div>
  
    <div class="form-group">
	<label for="codigo" class="col-md-3"><?php echo lang("phase"); ?></label>
	<div class="col-md-9">
    	<?php 
			echo lang($fase->nombre_lang);
		?>
	</div>
</div>

<?php if($procesos_unitarios) { ?>
        <div class="form-group">
            <label for="pu" class="col-md-3"><?php echo lang('unit_processes'); ?></label>
            <div class="col-md-9">
            
                <?php 
                    $array_nombres = array();
					foreach($procesos_unitarios as $index => $pu){
						$array_nombres[$index] = $pu["nombre"];
					}
                    $nombres= implode(', ', $array_nombres);
					echo $nombres;
 
                ?>
            </div>
        </div>
    <? } ?>
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('calculation_methodology'); ?></label>
        <div class="col-md-9">
    	<?php 
			echo $metodologia->nombre;
		?>
	</div>
    </div>
    
     <?php if($materiales) { ?>
        <div class="form-group">
            <label for="pu" class="col-md-3"><?php echo lang('materials'); ?></label>
            <div class="col-md-9">
            
                <?php 
                    $array_nombres = array();
					foreach($materiales as $material){
						$array_nombres[] = $material->nombre;
					}
                    $nombres = implode(', ', $array_nombres);
					echo $nombres; 
                ?>
            </div>
        </div>
    <? } ?>
    
    
    <?php if($huellas) { ?>
        <div class="form-group">
            <label for="pu" class="col-md-3"><?php echo lang('footprints'); ?></label>
            <div class="col-md-9">
            
                <?php
					
					$array_nombres = array();
					$array_icono = array();
					foreach($huellas as $index => $pu){
						$array_nombres[$index] = $pu["nombre"];
						$array_icono[$index] = $pu["icono"];
						
						echo '<div class="col-md" style="display:block; float:left; margin-right: 10px; margin-bottom: 5px;"><img heigth="20" width="20" src="/assets/images/impact-category/'.$pu["icono"].' "/> <span class="label label-primary">'.$pu["nombre"].'</span> </div>';
					}
                ?>
            </div>
        </div>
    <? } ?>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $project_info->created_date;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($project_info->modified)?$project_info->modified:'-';
            ?>
        </div>
    </div>

    

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>


<script type="text/javascript">
    $(document).ready(function() {
		
        
    });
</script>    