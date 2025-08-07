<div id="page-content" class="p20 clearfix">

 <!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("air_forecast_records");?>"><?php echo lang("forecast_records"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo $air_record_info->name; ?></a>
    </nav>

<?php if($puede_ver != 3) { ?>

	<div class="row">
    	<div class="col-md-12">
        
        	<div class="page-title clearfix">
            	<h1><i class="fa fa-table" title="Abierto"></i> <?php echo $air_record_info->name; ?></h1>
            </div>
            
            <?php
            $icono = $air_record_info->icon ? get_file_uri("assets/images/icons/".$air_record_info->icon) : get_file_uri("assets/images/icons/empty.png");
			$descripcion = $air_record_info->descripcion;
			?>
            
            <div class="row" style="background-color:#E5E9EC;">
                <div class="col-md-4">
                	<div class="row">
                    	<div class="col-md-12 col-sm-12">
                        <div class="panel">
                        <div class="panel-heading panel-sky p30"></div>
                        <div class="clearfix text-center">
                        <span class="mt-50 avatar avatar-md chart-circle">
                        <img src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded shadow-2">
                        </span>
                        </div>
                        <div class="p10 b-t b-b"><?php echo lang("number_of_records") . ': ' ?> <span id="num_registros"> <?php echo $num_registros; ?> </span> </div>
						<div class="p10 b-b"><?php echo lang("sector") . ': ' ?> <span id="sector"> <?php echo $air_record_info->name_sector; ?> </span> </div>
						<div class="p10 b-b"><?php echo lang("model") . ': ' ?> <span id="model"> <?php echo lang($air_record_info->name_model); ?> </span> </div>
						<div class="p10 b-b"><?php echo lang("station") . ': ' ?> <span id="station"> <?php echo ($air_record_info->name_station) ? $air_record_info->name_station : "-"; ?> </span> </div>
                        <div class="p10 b-b"><?php echo lang("modified_date") . ': ' ?> <span id="fecha_modificacion"> <?php echo ($fecha_modificacion)?time_date_zone_format($fecha_modificacion,$project_info->id):"-"; ?> </span> </div>
                        </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                	<div class="row">
                    	<div class="col-md-12 col-sm-12">
                        <div class="panel">
                        <div class="tab-title clearfix">
                            <h4><?php echo lang("description"); ?></h4>
                        </div>
                        <div class="p15" align="justify">
                        	<?php echo $descripcion; ?>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="page-title clearfix panel-sky">
                    <h1><?php echo $air_record_info->nombre; ?></h1>
                    <div class="title-button-group">
						<div class="btn-group" role="group">
						</div> 
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="air_forecast_records-table" class="display" cellspacing="0" width="100%"> 
                    </table>
                </div>
            </div>
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

	<script type="text/javascript">
        $(document).ready(function () {

			//General Settings
			var decimals_separator = AppHelper.settings.decimalSeparator;
			var thousands_separator = AppHelper.settings.thousandSeparator;
			var decimal_numbers = AppHelper.settings.decimalNumbers;
		
            $("#air_forecast_records-table").appTable({
                source: '<?php echo_uri("air_forecast_records/list_data/".$air_record_info->id); ?>',
				filterDropdown: [{name: "id_variable", class: "w200", options: <?php echo $array_variables; ?>}],
           		rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
                columns: [
                    {data: "id", title: "<?php echo lang("id"); ?>", "class": "text-center w50 hide"},
					//{title: "<?php //echo lang("created_by"); ?>", "class": "text-center w50 hide"},
					{data: "variable_name", title: "<?php echo lang("variable"); ?>", "class": "text-center"},
					
					<?php if($air_record_info->id_air_model == 3) { // Modelo numérico ?>
						{data: "latitude", title: "<?php echo lang("latitude"); ?>", "class": "text-center"},
						{data: "longitude", title: "<?php echo lang("longitude"); ?>", "class": "text-center"},
					<?php } ?>

					{data: "date", title: "<?php echo lang("date"); ?>", "class": "text-center", 
						render: function (data, type, row) {
							var fecha = moment(data, 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat));
							return fecha;
						}
					},
					<?php for($time = 0; $time <= 23; $time++) { ?>
						{data: "<?php echo ($time < 10 ) ? "time_0".$time : "time_".$time; ?>", title: "<?php echo ($time < 10 ) ? "0".$time : $time; ?>", "class": "text-center", 
							render: function (data, type, row) {
								var value = numberFormat(data, decimal_numbers, decimals_separator, thousands_separator);
								return value;
							}
						},
					<?php } ?>
                ]
			});
			
        });  
		 
    </script>
</div>