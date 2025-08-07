<?php echo form_open("", array("id" => "clients-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    
	<div class="form-group">
		<label for="company_name" class="col-md-3"><?php echo lang('company_name'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->company_name ? $client_info->company_name : "-"; ?>
		</div>
	</div>
	
    <div class="form-group">
		<label for="sigla" class="col-md-3"><?php echo lang('initial'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->sigla ? $client_info->sigla : "-"; ?>
		</div>
	</div>
    
	<div class="form-group">
		<label for="rut" class="col-md-3"><?php echo lang('rut'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->rut ? $client_info->rut : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="giro" class="col-md-3"><?php echo lang('giro'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->giro ? $client_info->giro : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="pais" class="col-md-3"><?php echo lang('country'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->pais ? $client_info->pais : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="ciudad" class="col-md-3"><?php echo lang('city'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->ciudad ? $client_info->ciudad : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="comuna" class="col-md-3"><?php echo lang('town'); ?></label>
		<div class="col-md-9">
            <?php echo $client_info->comuna ? $client_info->comuna : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="direccion" class="col-md-3"><?php echo lang('address'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->direccion ? $client_info->direccion : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="fono" class="col-md-3"><?php echo lang('phone'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->fono ? $client_info->fono : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="fono" class="col-md-3"><?php echo lang('contact_email'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->contacto ? $client_info->contacto : "-"; ?>
		</div>
	</div>
    
    <div class="form-group">
		<label for="groups" class="col-md-3"><?php echo lang('groups'); ?></label>
		<div class="col-md-9">
			<?php 
				$html_groups = "";
				if(count($client_groups)){
					foreach($client_groups as $group){
						$html_groups .= "&bull; ".$group["group_name"]."<br>";
					}
				} else {
					$html_groups = "-";
				}
				
				echo $html_groups; 
			?>
		</div>
	</div>
    
     <div class="form-group">
		<label for="website" class="col-md-3"><?php echo lang('website'); ?></label>
		<div class="col-md-9">
			<?php echo $client_info->website ? $client_info->website : "-"; ?>
		</div>
	</div>
    
    
    <?php
	$url_logo = "";
	if($client_info->logo){
		$last_modif = filemtime("files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png");
		$url_logo = get_file_uri("files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png?=".$last_modif);
	} else {
		$url_logo = get_file_uri("files/system/default-site-logo.png");
	}	
	?>
     <div class="form-group">
		<label for="logo" class=" col-md-3"><?php echo lang('site_logo'); ?></label>
		<div class="col-md-9">
			<div class="pull-left mr15">
            	<img id="site-logo-preview" src="<?php echo $url_logo; ?>" alt="..." />
        	</div>
		</div>
	</div>
    
     <div class="form-group">
		<label for="color_sitio" class="col-md-3"><?php echo lang('site_color'); ?></label>
		<div class="col-md-9">
			<i style="border: solid black 1px; background-color: <?php echo $client_info->color_sitio; ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>
		<?php echo $client_info->color_sitio ? $client_info->color_sitio : "-"; ?> 
		</div>
	</div>
    
     <div class="form-group">
        <label for="habilitado" class="col-md-3"><?php echo lang('site_status'); ?></label>
        <div class="col-md-9">
            <?php
			echo form_checkbox("habilitado", "1", ($client_info->habilitado) ? true : false, "id='habilitado' onclick='return false;'");
            ?>
        </div>
     </div>
     
     <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $client_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($client_info->modified)?$client_info->modified:'-';
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
