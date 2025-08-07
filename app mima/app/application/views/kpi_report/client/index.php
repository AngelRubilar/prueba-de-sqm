<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("kpi"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("kpi_report"); ?></a>
    </nav>

    <div class="panel">
    
    <?php if($puede_ver == 1) { ?>
    
		<?php echo form_open(get_uri("KPI_Report/save"), array("id" => "kpi_report-form", "class" => "general-form", "role" => "form")); ?>

        <div class="panel-default">
        
            <div class="page-title clearfix">
                <h1><?php echo lang('kpi_report'); ?></h1>
            </div>

            <div class="panel-body">

            	<div class="col-md-12">
            
                    <div class="form-group col-md-4">
                        <label for="pais" class="<?php echo $label_column ?>"><?php echo lang('country'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("pais", $paises, "", "id='pais' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
        
                    <div class="form-group col-md-4">
                        <label for="fase" class="<?php echo $label_column ?>"><?php echo lang('phase'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("fase", $fases,"", "id='fase' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
        
                    <div class="form-group col-md-4">
                        <label for="tecnologia" class="<?php echo $label_column ?>"><?php echo lang('technology'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("tecnologia", $tecnologias, "", "id='tecnologia' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                
            	</div>    
                
				<div class="col-md-12">
            		
                    <div id="proyectos_group">
                        <div class="form-group col-md-4">
                            <label for="proyecto" class="<?php echo $label_column ?>"><?php echo lang('project'); ?></label>
                            <div class="<?php echo $field_column ?>">
                                <?php
                                echo form_dropdown("proyecto", $proyectos, "", "id='proyecto' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
                                ?>
                            </div>
                        </div>
    				</div>
                    
                    <div class="form-group col-md-4">    
                        <label for="" class="<?php echo $label_column ?>"><?php echo lang('since') ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php 
                                echo form_input(array(
                                    "id" => "start_date",
                                    "name" => "start_date",
                                    "value" => '',
                                    "class" => "form-control",
                                    "data-rule-required" => "true",
                                    "data-msg-required" => lang('field_required'),
                                    "placeholder" => lang('since'),
                                    "autocomplete" => "off",
                                ));
                            ?>
                        </div> 
					</div>
                    
                    <div class="form-group col-md-4">
                    
                        <label for="" class="<?php echo $label_column ?>"><?php echo lang('until') ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php 
                                echo form_input(array(
                                    "id" => "end_date",
                                    "name" => "end_date",
                                    "value" => '',
                                    "class" => "form-control",
                                    "placeholder" => lang('until'),
                                    "data-rule-required" => "true",
                                    "data-msg-required" => lang('field_required'),
                                    "data-rule-greaterThanOrEqual" => "#start_date",
                                    "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                    "autocomplete" => "off",
                                ));
                            ?>
                        </div>
                        
					</div>

            	</div>
                
            </div>

            <div class="panel-footer clearfix">
            	<div class="pull-right">
                    <div class="btn-group" role="group">
                     	<button type="submit" id="generar_kpi_report" class="btn btn-primary pull-right"><span class="fa fa-eye"></span> <?php echo lang('generate_kpi_report'); ?></button>
                    </div>
           		</div>
                
            </div>
        </div>

        <?php echo form_close(); ?>
     
    </div> 
    
    <div class="panel">
    	<div class="panel-default">
			<div id="kpi_report_group"></div>
        </div>
    </div>
    
    <?php } else { ?>
		

            <div class="page-title clearfix">
                <h1><?php echo lang('kpi_report'); ?></h1>
            </div>
            <div class="row"> 
                <div class="col-md-12 col-sm-12">
                    <div class="panel panel-default m0">
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

</div>
<script type="text/javascript">
	$(document).ready(function () {
        
		$('#kpi_report-form .select2').select2();
		
		setDatePicker("#start_date");
		setDatePicker("#end_date");
		
		$("#kpi_report-form").appForm({
            ajaxSubmit: false
        });
		
		/*$("#kpi_report-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                }
            }
        });*/
		
		$('#pais').change(function(){	
			
			var id_pais = $(this).val();
			var id_fase = $("#fase").val();
			var id_tecnologia = $("#tecnologia").val();
			
			select2LoadingStatusOn($('#proyecto'));
					
			$.ajax({
				url:  '<?php echo_uri("KPI_Report/get_project_filter") ?>',
				type:  'post',
				data: {id_pais:id_pais, id_fase:id_fase, id_tecnologia:id_tecnologia},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#proyecto').select2();
					select2LoadingStatusOff($('#proyecto'));
				}
			});

		});	
		
		$(document).on("change", "#fase", function() {	
		
			var id_pais = $("#pais").val();
			var id_fase = $(this).val();
			var id_tecnologia = $("#tecnologia").val();
			
			select2LoadingStatusOn($('#proyecto'));
					
			$.ajax({
				url:  '<?php echo_uri("KPI_Report/get_project_filter") ?>',
				type:  'post',
				data: {id_pais:id_pais, id_fase:id_fase, id_tecnologia:id_tecnologia},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#proyecto').select2();
					select2LoadingStatusOff($('#proyecto'));
				}
			});
	
		});	
		
		$(document).on("change", "#tecnologia", function() {	
		
			var id_pais = $("#pais").val();
			var id_fase = $("#fase").val();
			var id_tecnologia = $(this).val();
			
			select2LoadingStatusOn($('#proyecto'));
					
			$.ajax({
				url:  '<?php echo_uri("KPI_Report/get_project_filter") ?>',
				type:  'post',
				data: {id_pais:id_pais, id_fase:id_fase, id_tecnologia:id_tecnologia},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#proyecto').select2();
					select2LoadingStatusOff($('#proyecto'));
				}
			});
	
		});	

		$("#kpi_report-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		$('#generar_kpi_report').click(function(e){
			
			$("#kpi_report-form").valid();
			
			var id_proyecto = $('#proyecto').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			
			if(id_proyecto && start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:  '<?php echo_uri("KPI_Report/get_kpi_report") ?>',
						type:  'post',
						data: {
							id_proyecto: id_proyecto,
							start_date: start_date,
							end_date: end_date
						},beforeSend: function() {
					   		$('#kpi_report_group').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
						},
						
						//dataType:'json',
						success: function(respuesta){;
							$('#kpi_report_group').html(respuesta);
						}
					});	
					
				}
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
			
		});		
		
    });
</script>