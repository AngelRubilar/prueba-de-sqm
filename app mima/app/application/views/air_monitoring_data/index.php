<div id="page-content" class="p20 clearfix" style="min-height:600px;">

<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("air_monitoring_data"); ?>"><?php echo lang("records"); ?></a>
</nav>

<div class="panel">
    
	<?php if($puede_ver != 3) { ?>
    
		<?php echo form_open(get_uri("air_monitoring_data/save"), array("id" => "air_records-form", "class" => "general-form", "role" => "form")); ?>

        <div class="panel-default">
        
            <div class="page-title clearfix">
                <h1><?php echo lang('records'); ?></h1>
            </div>

            <div class="panel-body">

            	<div class="col-md-12">
            
                    <div class="form-group col-md-3">
                        <label for="sector" class="<?php echo $label_column ?>"><?php echo lang('sector'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("sector", $air_sectors, "", "id='sector' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>

					<div id="stations_group">
						<div class="form-group col-md-3">
							<label for="station" class="<?php echo $label_column ?>"><?php echo lang('station'); ?></label>
							<div class="<?php echo $field_column ?>">
								<?php
								echo form_dropdown("station", $air_stations,"", "id='station' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>
    				</div>
        
                    

					<div class="form-group col-md-3">    
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
                    
                    <div class="form-group col-md-3">
                    
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
                
				<div class="col-md-12">
					
					<div class="form-group col-md-6">
						<label for="days" class="<?php echo $label_column; ?>"><?php echo lang('interval_days'); ?></label>
						<div class="<?php echo $field_column; ?>">
							<?php
								echo form_multiselect("days[]", $days_dropdown, array(1,2,3,4,5,6,7), "id='days' class='select2 multiple validate-hidden' multiple='multiple' data-rule-required='true' data-msg-required='".lang('field_required')."'");
							?>
						</div>
					</div>

					<div class="form-group col-md-6">
						<label for="hours" class="<?php echo $label_column; ?>"><?php echo lang('interval_hours'); ?></label>
						<div class="<?php echo $field_column; ?>">
							<?php
								echo form_multiselect("hours[]", $hours_dropdown, array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24), "id='hours' class='select2 multiple validate-hidden' multiple='multiple' data-rule-required='true' data-msg-required='".lang('field_required')."'");
							?>
						</div>
					</div>
					

            	</div>

            </div>

            <div class="panel-footer clearfix">
            	<div class="pull-right">
                    <div class="btn-group" role="group">
                     	<button type="submit" id="generar_air_records_report" class="btn btn-primary pull-right"><span class="fa fa-eye"></span> <?php echo lang('generate_report'); ?></button>
                    </div>
           		</div>
                
            </div>
        </div>

        <?php echo form_close(); ?>
     
	</div> 
	
	<div class="panel">
    	<div class="panel-default">
			<div id="air_records_group"></div>
        </div>
    </div>


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

</div>


<script type="text/javascript">
    $(document).ready(function () {

		$('#air_records-form .select2').select2();

		$('#start_date, #end_date').datetimepicker({
			format: "YYYY-MM-DD HH:mm",
			locale: moment.locale(),
			useCurrent: false,
			showClear: true,
			showClose:true,
			/*tooltips: {
				today: 'Go to today',
				clear: 'Clear selection',
				close: 'Close the picker',
				selectMonth: 'Select Month',
				prevMonth: 'Previous Month',
				nextMonth: 'Next Month',
				selectYear: 'Select Year',
				prevYear: 'Previous Year',
				nextYear: 'Next Year',
				selectDecade: 'Select Decade',
				prevDecade: 'Previous Decade',
				nextDecade: 'Next Decade',
				prevCentury: 'Previous Century',
				nextCentury: 'Next Century'
			}*/
		});
		
		$("#air_records-form").appForm({
            ajaxSubmit: false
        });

		$("#air_records-form").submit(function(e){
			e.preventDefault();
			return false;
		});

		$('#sector').change(function(){	
			
			var id_sector = $(this).val();
			select2LoadingStatusOn($('#station'));
					
			$.ajax({
				url:  '<?php echo_uri("Air_monitoring_data/get_stations_of_sector") ?>',
				type:  'post',
				data: {id_sector:id_sector},
				//dataType:'json',
				success: function(respuesta){
					
					$('#stations_group').html(respuesta);
					$('#station').select2();
					select2LoadingStatusOff($('#station'));
				}
			});

		});

		$('#generar_air_records_report').click(function(e){
			
			$("#air_records-form").valid();
			
			var id_station = $('#station').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			var days = $('#days').val();
			var hours = $('#hours').val();
			
			if(id_station && start_date && end_date && days && hours){
	
                $.ajax({
                    url:  '<?php echo_uri("Air_monitoring_data/get_report") ?>',
                    type:  'post',
                    data: {
                        id_station: id_station,
                        start_date: start_date,
                        end_date: end_date,
                        days: days,
                        hours: hours,
                    },beforeSend: function() {
                        $('#air_records_group').html('<div style="padding:20px;"><div class="circle-loader"></div></div>');
                    },
                    
                    //dataType:'json',
                    success: function(respuesta){;
                        $('#air_records_group').html(respuesta);
                    }
                });	
                
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
			
		});

		$('body').on('click', '[data-act=update-validation-status]', function () {
            
            $(this).find("span").addClass("inline-loader");
			$.ajax({
				url: '<?php echo_uri("Air_monitoring_data/save_validation_status") ?>/' + $(this).attr('data-id'),
				type: 'POST',
				dataType: 'json',
				data: {value: $(this).attr('data-value')},
				success: function (response) {
					if (response.success) {
						$("#minute-table").appTable({newData: response.data, dataId: response.id});
					}
				}
			});
        });

		$('body').on('click', '[data-act=update-calibration-status]', function () {
            
            $(this).find("span").addClass("inline-loader");
			$.ajax({
				url: '<?php echo_uri("Air_monitoring_data/save_calibration_status") ?>/' + $(this).attr('data-id'),
				type: 'POST',
				dataType: 'json',
				data: {value: $(this).attr('data-value')},
				success: function (response) {
					if (response.success) {
						$("#minute-table").appTable({newData: response.data, dataId: response.id});
					}
				}
			});
        });

        
    });
</script>