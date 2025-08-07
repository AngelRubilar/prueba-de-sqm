<div id="page-content" class="p20 clearfix">

	<!-- MÓDULO DE PRONÓSTICOS -->
	<?php if($sector->id){ ?>

		<div class="panel">
    		<div class="panel-default">
				<div class="page-title clearfix">
					<h1><?php echo $sector->name; ?></h1>
				</div>
				<div class="panel-body">
					<?php foreach($stations as $index => $station){ ?>
						<div class="col-md-12">
							<div class="panel panel-default">
								<div class="page-title clearfix panel-success">
									<div class="pt10 pb10 text-center"> <?php echo $station->name; ?> </div>
								</div>
								<div class="panel-body text-center">
									<div id="body_calheatmap_<?php echo $station->id; ?>"></div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
        </div>

	<?php } ?>
    
</div>

<style>
	/* Estilos para CalHeatMap */
	.cal-heatmap-container .subdomain-text {
		font-size: 12px;
		fill: #FFF;
	}
</style>

<script type="text/javascript">

	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	var decimal_numbers = AppHelper.settings.decimalNumbers;

	const array_days_name = [
		'<?php echo lang("sunday"); ?>', 
		'<?php echo lang("monday"); ?>', 
		'<?php echo lang("tuesday"); ?>', 
		'<?php echo lang("wednesday"); ?>', 
		'<?php echo lang("thursday"); ?>', 
		'<?php echo lang("friday"); ?>', 
		'<?php echo lang("saturday"); ?>', 
	];

	const array_days_short_name = [
		'<?php echo lang("sun"); ?>', 
		'<?php echo lang("mon"); ?>', 
		'<?php echo lang("tue"); ?>', 
		'<?php echo lang("wed"); ?>', 
		'<?php echo lang("thu"); ?>', 
		'<?php echo lang("fri"); ?>', 
		'<?php echo lang("sat"); ?>', 
	];

	// Array para setear formato de fechas en los calhetamap
	const array_format_date_calheatmap = [];
	array_format_date_calheatmap["d-m-Y"] = "%d-%m-%Y";
	array_format_date_calheatmap["m-d-Y"] = "%m-%d-%Y";
	array_format_date_calheatmap["Y-m-d"] = "%Y-%m-%d";
	array_format_date_calheatmap["d/m/Y"] = "%d/%m/%Y";
	array_format_date_calheatmap["m/d/Y"] = "%m/%d/%Y";
	array_format_date_calheatmap["Y/m/d"] = "%Y/%m/%d";
	array_format_date_calheatmap["d.m.Y"] = "%d.%m.%Y";
	array_format_date_calheatmap["m.d.Y"] = "%m.%d.%Y";
	array_format_date_calheatmap["Y.m.d"] = "%Y.%m.%d";

	$(document).ready(function () {
		
		<?php foreach($stations as $index => $station){ ?>

			$.ajax({
				url: '<?php echo_uri("dashboard/get_widget_by_station"); ?>',
				type: 'post',
				// dataType: 'json',
				data: {id_station: <?php echo $station->id; ?>},
				beforeSend: function() {
					$("#body_calheatmap_<?php echo $station->id; ?>").html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
				},
				success: function(result){
					$("#body_calheatmap_<?php echo $station->id; ?>").html(result);
					setTimeout(function(){
						var div_width = $("#calheatmap_<?php echo $station->id; ?>").width();
						var svg_width = $(".cal-heatmap-container").width();
						var left = (div_width - svg_width) / div_width;
						left = Math.floor(left * 50) + '%';
						$("#calheatmap_<?php echo $station->id; ?>").css("margin-left",left);
					}, 10);
				}
			});

		<?php } ?>

	});
</script>
