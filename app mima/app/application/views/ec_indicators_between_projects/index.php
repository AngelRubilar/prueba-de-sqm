<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("circular_economy"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("ec_indicators_between_projects"); ?></a>
    </nav>

    <div class="panel">
    
    <?php if($puede_ver == 1) { ?>
    
		<?php echo form_open(get_uri("EC_Indicators_between_projects/save"), array("id" => "ec_indicators_between_projects-form", "class" => "general-form", "role" => "form")); ?>

        <div class="panel-default">
        
            <div class="page-title clearfix">
                <h1><?php echo lang('ec_indicators_between_projects'); ?></h1>
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
                        <button id="generar_ec_indicators_between_projects" type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('generate_ec_indicators_between_projects'); ?></button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger" id="export_pdf" disabled="disabled"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <?php echo form_close(); ?>
     
    </div> 
    
    <div class="panel">
    	<div class="panel-default">
			<div id="ec_indicators_between_projects_group"></div>
        </div>
    </div>
    
     <?php } else { ?>

            <div class="page-title clearfix">
                <h1><?php echo lang('charts_between_projects'); ?></h1>
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
        
		$('#ec_indicators_between_projects-form .select2').select2();
		
		setDatePicker("#start_date");
		setDatePicker("#end_date");
		
		$("#ec_indicators_between_projects-form").appForm({
            ajaxSubmit: false
        });
		
		$("#ec_indicators_between_projects-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		$('#generar_ec_indicators_between_projects').click(function(e){
			
			$("#export_pdf").off('click');
			$('#export_pdf').attr('disabled', true);
			
			$("#ec_indicators_between_projects-form").valid();
			
			var id_pais = $('#pais').val();
			var id_fase = $('#fase').val();
			var id_tech = $('#tecnologia').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			
			if(id_pais && id_fase && id_tech && start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:  '<?php echo_uri("EC_Indicators_between_projects/get_ec_report") ?>',
						type:  'post',
						data: {
							id_pais: id_pais,
							id_fase: id_fase,
							id_tech: id_tech,
							start_date: start_date,
							end_date: end_date
						},beforeSend: function() {
					   		$('#ec_indicators_between_projects_group').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
						},
						
						//dataType:'json',
						success: function(respuesta){;
							$('#ec_indicators_between_projects_group').html(respuesta);
							$('#export_pdf').removeAttr('disabled');
						}
					});	
					
				}
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
			
		});
		
		$("#ec_indicators_between_projects-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
    });
</script>