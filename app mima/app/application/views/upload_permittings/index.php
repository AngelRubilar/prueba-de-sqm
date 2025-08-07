<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("permittings"); ?> /</a>
  <a class="breadcrumb-item" href=<?php echo get_uri(); ?>><?php echo lang("upload_permittings"); ?></a>
</nav>

    <div class="panel panel-default m0">  
    	
        <div class="page-title clearfix">
            <h1><?php echo lang("upload_permittings"); ?></h1>
        </div>
		<div class="panel-body">
        	<!--
        	<div class="col-sm-2 col-md-2 col-lg-2">
            	<i class="fa fa-wrench"></i> Configuración de la aplicación
            </div>   
            -->        
            <div class="col-sm-12 col-md-12 col-lg-12 p0">           	
                
                <div class="col-md-4 p0">
                    <label for="client" class="col-md-2"><?php echo lang('client'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown("client", $clientes, "", "id='client' class='select2 validate-hidden col-md-12' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                        ?>
                    </div>
                </div>

				<div id="proyectos_group">
                    <div class="col-md-4 p0">
                        <label for="project" class="col-md-2"><?php echo lang('project'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown("project", array("" => "-"), "", "id='project' class='select2 validate-hidden col-md-12' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>  
                </div> 
                            
            </div> 
     
        </div>
    </div> 
</div>

<div id="configuraciones"></div>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('#client').select2();
		$('#project').select2();
		
		$('#client').change(function(){	
			
			$('#fields_group').html("");		
			var id_client = $(this).val();	
			select2LoadingStatusOn($('#project'));
			
			$.ajax({
				url:  '<?php echo_uri("upload_permittings/get_projects_of_client") ?>',
				type:  'post',
				data: {id_client:id_client},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
					
					if(!id_client){
						$('#configuraciones').html("");
					}
					if(!$('#project').val()){
						$('#configuraciones').html("");
					}
					
					select2LoadingStatusOff($('#project'));
				}
			});
			
		});
		
		$(document).on("change","#project", function(){	
			
			var id_cliente = $('#client').val();
			var id_proyecto = $(this).val();
			
			$.ajax({
				url:  '<?php echo_uri("upload_permittings/get_upload_permittings_of_project") ?>',
				type:  'post',
				data: {id_proyecto:id_proyecto, id_cliente:id_cliente},
				//dataType:'json',
				success: function(respuesta){
					
					if (id_proyecto){
						$('#configuraciones').html("");
						$('#configuraciones').html(respuesta);
						$('#tab_carga_individual').trigger('click');
					} else {
						$('#configuraciones').html("");
					}				
				}
			});
			//alert("llamar a la configuración del proyecto " + id_proyecto);
		});
	
	});
	
</script>