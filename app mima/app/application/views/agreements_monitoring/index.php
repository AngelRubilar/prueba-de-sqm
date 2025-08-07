<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("communities"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("agreements_monitoring"); ?></a>
</nav>

<?php if($puede_ver == 1) { ?>

    <div class="panel panel-default m0">  
    	<div class="page-title clearfix">
            <h1><?php echo lang("agreements_monitoring"); ?></h1>
        </div>
		<div class="panel-body">
        	<!--
        	<div class="col-sm-2 col-md-2 col-lg-2">
            	<i class="fa fa-wrench"></i> Configuración de la aplicación
            </div>   
            -->        
            <div class="col-sm-12 col-md-12 col-lg-12 p0">           	
                
                <div id="agreement_group">
                    <div class="col-md-4 p0">
                        <label for="agreement" class="col-md-2"><?php echo lang('agreement'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown("agreement", $dropdown_acuerdos, "", "id='agreement' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>

				<div id="stakeholder_group">
                    <div class="col-md-4 p0">
                        <label for="stakeholder" class="col-md-2 p0"><?php echo lang('stakeholder'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown("stakeholder", $dropdown_stakeholders, "", "id='stakeholder' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>  
                </div> 
                            
            </div> 
     
        </div>
    </div> 
</div>
<div id="evaluation_table"></div>

<?php } else { ?>

<div class="row"> 
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                    <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>


<script type="text/javascript">

	$(document).ready(function(){
		
		var id_agreements_matrix = '<?php echo $id_agreements_matrix; ?>';
		
		$('#agreement').select2();
		$('#stakeholder').select2();	

		$(document).on('change', '#agreement', function(e){	
		
			var id_acuerdo = $(this).val();
			var id_stakeholder = $('#stakeholder').val();
		
			$.ajax({
				url:  '<?php echo_uri("agreements_monitoring/get_evaluation_table_of_agreement") ?>',
				type:  'post',
				data: {id_acuerdo:id_acuerdo, id_stakeholder:id_stakeholder, id_agreements_matrix:id_agreements_matrix},
				//dataType:'json',
				success: function(respuesta){
					$('#evaluation_table').html(respuesta);
				}
			});
		
		});
		
		$(document).on('change', '#stakeholder', function(e){	
		
			var id_acuerdo = $('#agreement').val();
			var id_stakeholder = $(this).val();
		
			$.ajax({
				url:  '<?php echo_uri("agreements_monitoring/get_evaluation_table_of_agreement") ?>',
				type:  'post',
				data: {id_acuerdo:id_acuerdo, id_stakeholder:id_stakeholder, id_agreements_matrix:id_agreements_matrix},
				//dataType:'json',
				success: function(respuesta){
					$('#evaluation_table').html(respuesta);
				}
			});
		
		});
			
		
		
		/*
		$(document).on('change', '#agreement', function(e){	
					
			var id_acuerdo = $(this).val();
			var id_stakeholder = $('#stakeholder').val();
			
			if(id_stakeholder){
				
				if(id_acuerdo){

					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_agreements_of_stakeholder"); ?>',
						type:  'post',
						data: {id_stakeholder:id_stakeholder},
						//dataType:'json',
						success: function(respuesta){
							$('#agreement_group').html(respuesta);
							$('#agreement').select2();
							$('#agreement').select2('val', id_acuerdo);
						}
					});
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_stakeholders_of_agreement") ?>',
						type:  'post',
						data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#stakeholder_group').html(respuesta);
							$('#stakeholder').select2();
							$('#stakeholder').select2('val', id_stakeholder);
						}
					});
					
					//$('#evaluation_table').html("FILAS | id_acuerdo: " + id_acuerdo + " | id_stakeholder: " + id_stakeholder);
					
					//AJAX PARA TRAER LA FILA DE EVALUACIÓN
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_evaluation_table_of_agreement") ?>',
						type:  'post',
						data: {id_acuerdo:id_acuerdo, id_stakeholder:id_stakeholder},
						//dataType:'json',
						success: function(respuesta){
							$('#evaluation_table').html(respuesta);
						}
					});
					
				} else {
					$('#evaluation_table').html("");
				}
						
			} else {
				
				if(id_acuerdo){
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_stakeholders_of_agreement") ?>',
						type:  'post',
						data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#stakeholder_group').html(respuesta);
							$('#stakeholder').select2();
						}
					});
					
				} else {
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_stakeholders_dropdown") ?>',
						type:  'post',
						//data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#stakeholder_group').html(respuesta);
							$('#stakeholder').select2();
						}
					});
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_agreements_dropdown") ?>',
						type:  'post',
						//data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#agreement_group').html(respuesta);
							$('#agreement').select2();
						}
					});
					
				}

			}

		});
		
		
		$(document).on('change', '#stakeholder', function(e){	
						
			var id_stakeholder = $(this).val();
			var id_acuerdo = $('#agreement').val();
			
			if(id_acuerdo){
				
				if(id_stakeholder){
				
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_stakeholders_of_agreement") ?>',
						type:  'post',
						data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#stakeholder_group').html(respuesta);
							$('#stakeholder').select2();
							$('#stakeholder').select2('val', id_stakeholder);
						}
					});
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_agreements_of_stakeholder"); ?>',
						type:  'post',
						data: {id_stakeholder:id_stakeholder},
						//dataType:'json',
						success: function(respuesta){
							$('#agreement_group').html(respuesta);
							$('#agreement').select2();
							$('#agreement').select2('val', id_acuerdo);
						}
					});
					
					//$('#evaluation_table').html("FILAS | id_acuerdo: " + id_acuerdo + " | id_stakeholder: " + id_stakeholder);
					
					//AJAX PARA TRAER LA FILA DE EVALUACIÓN
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_evaluation_table_of_agreement") ?>',
						type:  'post',
						data: {id_acuerdo:id_acuerdo, id_stakeholder:id_stakeholder},
						//dataType:'json',
						success: function(respuesta){
							$('#evaluation_table').html(respuesta);
						}
					});
					
				} else {
					$('#evaluation_table').html("");
				}

			} else {
				
				if(id_stakeholder){
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_agreements_of_stakeholder"); ?>',
						type:  'post',
						data: {id_stakeholder:id_stakeholder},
						//dataType:'json',
						success: function(respuesta){
							$('#agreement_group').html(respuesta);
							$('#agreement').select2();
						}
					});
					
				} else {
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_stakeholders_dropdown") ?>',
						type:  'post',
						//data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#stakeholder_group').html(respuesta);
							$('#stakeholder').select2();
						}
					});
					
					$.ajax({
						url:  '<?php echo_uri("agreements_monitoring/get_agreements_dropdown") ?>',
						type:  'post',
						//data: {id_acuerdo:id_acuerdo},
						//dataType:'json',
						success: function(respuesta){
							$('#agreement_group').html(respuesta);
							$('#agreement').select2();
						}
					});
					
				}
				
			}
	
		});	
		
		*/
		
		$('[data-toggle="tooltip"]').tooltip();
		
	});
	
</script>