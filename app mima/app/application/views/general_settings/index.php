<div id="page-content" class="p20 clearfix"> 

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="#"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("platform_configuration"); ?></a>
</nav>

    <div class="panel panel-default m0">  
    
		<div class="panel-body">
        
        	<div class="col-sm-2 col-md-2 col-lg-2">
            	<i class="fa fa-wrench"></i> <?php echo lang("app_settings"); ?>
            </div>   
                    
            <div class="col-sm-10 col-md-10 col-lg-10">           	
                
                <div class="col-md-5">
                    <label for="client" class="col-md-2"><?php echo lang('client'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_dropdown("client", $clientes, "", "id='client' class='select2 validate-hidden col-md-12' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                        ?>
                    </div>
                </div>

				<div id="proyectos_group">
                    <div class="col-md-5">
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

<div id="configuraciones">
	
    <div id="page-content" class="p20 pt0 row">
    
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "general";
            $this->load->view("general_settings/tabs_global", $tab_view);
            ?>
        </div>
        
        <div role="tabpanel" class="tab-pane fade active in">
            <div class="tab-content">
            	<div id="general" class="tab-pane fade in active">
                    <?php $this->load->view('general_settings/global/general')?>
                </div>
                <div id="email" class="tab-pane fade">
                    <?php $this->load->view('general_settings/global/email')?>
                </div>
                <div id="email_templates" class="tab-pane fade">
                    <?php $this->load->view('general_settings/global/email_templates')?>
                </div>
            </div>
        </div>
        
    </div>
    
</div>

<script type="text/javascript">

	$(document).ready(function(){
		
		$('#client').select2();
		$('#project').select2();
		
		$('#client').change(function(){	
			
			appLoader.show();
	
			var id_client = $(this).val();	
			var id_proyecto = $('#project').val();
			
			select2LoadingStatusOn($('#project'));
			
			$.ajax({
				url:  '<?php echo_uri("general_settings/get_projects_of_client") ?>',
				type:  'post',
				data: {id_client:id_client},
				//dataType:'json',
				success: function(respuesta){
					
					select2LoadingStatusOff($('#project'));
					
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
					
					if(!id_client){
						$('#configuraciones').html("");
					}
					
				}
			});
			
			if(id_client){
				
				$.ajax({
					url:  '<?php echo_uri("general_settings/get_client_settings") ?>',
					type:  'post',
					data: {id_client:id_client},
					//dataType:'json',
					success: function(respuesta){
						
						appLoader.hide();
						
						$('#configuraciones').html(respuesta);
						
						if(!id_client){
							$('#configuraciones').html("");
						}
						
					},
					error: function(respuesta) {
						appLoader.hide();
						$('#configuraciones').html('');
					}
				});

			} else {
				
				$('#configuraciones').html('');	
				$.ajax({
					url:  '<?php echo_uri("general_settings/get_global_settings") ?>',
					type:  'post',
					//data: {id_client:id_client},
					//dataType:'json',
					success: function(respuesta){
						
						select2LoadingStatusOff($('#project'));
						$('#configuraciones').html(respuesta);
						appLoader.hide();

					}
				});
								
			}

		});
		
		$(document).on("change","#project", function(){
			
			appLoader.show();
			
			var id_cliente = $('#client').val();
			var id_proyecto = $(this).val();
			
			if(id_proyecto){
				
				$.ajax({
					url:  '<?php echo_uri("general_settings/get_all_settings") ?>',
					type:  'post',
					data: {id_proyecto:id_proyecto, id_cliente:id_cliente},
					//dataType:'json',
					success: function(respuesta){
						
						appLoader.hide();
						if (id_proyecto){
							$('#configuraciones').html("");
							$('#configuraciones').html(respuesta);
						} else {
							$('#configuraciones').html("");
						}				
						
						if(!id_cliente){
							$('#configuraciones').html("");
						}
					}
				});
				
			} else {
				
				$.ajax({
					url:  '<?php echo_uri("general_settings/get_client_settings") ?>',
					type:  'post',
					data: {id_client:id_cliente},
					//dataType:'json',
					success: function(respuesta){
						
						appLoader.hide();
						
						$('#configuraciones').html(respuesta);
						
						if(!id_cliente){
							$('#configuraciones').html("");
						}
						
					}
				});
				
			}

		});
	
	});
	
</script>