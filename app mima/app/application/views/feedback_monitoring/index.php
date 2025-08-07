<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("communities"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("feedback_monitoring"); ?></a>
</nav>

<?php if($puede_ver == 1) { ?>

    <div class="panel panel-default m0">  
    	<div class="page-title clearfix">
            <h1><?php echo lang("feedback_monitoring"); ?></h1>
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
                        <label for="status" class="col-md-2"><?php echo lang('visit_purpose'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown("proposito_visita", $propositos_visita_dropdown, "", "id='proposito_visita' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>

				<div id="stakeholder_group">
                    <div class="col-md-4 p0">
                        <label for="responsable" class="col-md-2 p0"><?php echo lang('responsible'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown("responsable", $responsable_dropdown, "", "id='responsable' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
		
		var id_feedback_matrix_config = '<?php echo $id_feedback_matrix_config; ?>';
		
		$.ajax({
			url:  '<?php echo_uri("feedback_monitoring/get_evaluation_table_of_feedback"); ?>',
			type:  'post',
			data: {proposito_visita:"", id_responsable:"", id_feedback_matrix_config:id_feedback_matrix_config},
			//dataType:'json',
			success: function(respuesta){
				$('#evaluation_table').html(respuesta);	
			}
		});
		
		$('#proposito_visita').select2();
		$('#responsable').select2();	
		//$('[data-toggle="tooltip"]').tooltip();
		
		$('#proposito_visita').change(function(){	
					
			var proposito_visita = $(this).val();
			var id_responsable = $('#responsable').val();
			
			$.ajax({
				url:  '<?php echo_uri("feedback_monitoring/get_evaluation_table_of_feedback"); ?>',
				type:  'post',
				data: {proposito_visita:proposito_visita, id_responsable:id_responsable, id_feedback_matrix_config:id_feedback_matrix_config},
				//dataType:'json',
				success: function(respuesta){
					$('#evaluation_table').html(respuesta);	
				}
			});
			
		});
		
		$('#responsable').change(function(){	
						
			var proposito_visita = $('#proposito_visita').val();
			var id_responsable = $(this).val();
			
			$.ajax({
				url:  '<?php echo_uri("feedback_monitoring/get_evaluation_table_of_feedback"); ?>',
				type:  'post',
				data: {proposito_visita:proposito_visita, id_responsable:id_responsable, id_feedback_matrix_config:id_feedback_matrix_config},
				//dataType:'json',
				success: function(respuesta){
					$('#evaluation_table').html(respuesta);	
				}
			});
			
		});
		
		
	});
	
</script>