<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("waste"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("summary"); ?></a>
    </nav>

    <div class="panel panel-default">
		<div class="page-title clearfix">
			<h1><?php echo lang('summary') ?></h1>
            <?php if($puede_ver == 1) { ?>
            	<a href="#" class="btn btn-danger pull-right" id="waste_summary_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
			<?php } ?>
        </div>
    </div>
    
<?php if($puede_ver == 1) { ?>    
    
	<?php echo form_open(get_uri("#"), array("id" => "summary-form", "class" => "general-form", "role" => "form")); ?>
        
        <div class="row" >
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0px 0px 0px 0px">
                        <div class="row">
                            <div id="vertical_stack_bar_container" class="panel-body">
                                <div class="col-md-6">
                                    <div id="vertical_stack_bar_1"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="vertical_stack_bar_2"></div>
                                </div>
                            </div
                        ></div>
                    </div>
                </div>
            </div>
        </div>		
    
    	<div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0px 0px 0px 0px">
                        <div class="row">
                            <div id="fixed_placement_columns_container" class="panel-body">
                                <div class="col-md-6">
                                    <div id="umbral_masa"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="umbral_volumen"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0px 0px 0px 0px">
                        <div class="row">
                            <div id="table_container" class="panel-body" style="padding-top: 0px;">
                                <div class="page-title clearfix" style="background-color:white;">
                                    <h1><?php echo lang("last_withdrawals")?></h1>
                                    <div class="btn-group pull-right" role="group">
                                        <button type="button" class="btn btn-success" id="excel_ultimos_retiros"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="detail-table" class="display" cellspacing="0" width="100%">
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div id="grafico_masa">
        </div>
        <div id="grafico_volumen">
        </div>
        <div id="grafico_umbral">
        </div>
        <div id="grafico_umbral_volumen">
        </div>
        
    <?php echo form_close(); ?>	

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

	var id_project = <?php echo $id_project ?>;
	var id_cliente = <?php echo $id_cliente ?>;

	$.ajax({
		url:  '<?php echo_uri("waste_summary/list_data") ?>',
		type:  'post',
		data: {id_project:id_project,id_cliente:id_cliente},
		success: function(result){
			$("#loading").addClass("hidden");
			var obj = jQuery.parseJSON(result);
			$('#grafico_masa').html(obj.grafico_masa);
			$('#grafico_volumen').html(obj.grafico_volumen);
			$('#grafico_umbral').html(obj.grafico_umnbrales);
			$('#grafico_umbral_volumen').html(obj.grafico_umbrales_volumen);
			
			$("#detail-table").appTable({
				source: '<?php echo_uri("waste_summary/list_data_table/"); ?>'+id_project,
				filterDropdown: [
					{name: "id_tratamiento", class: "w200", options: <?php echo $tratamientos_dropdown; ?>},
					{name: "id_categoria", class: "w200", options: <?php echo $categorias_dropdown; ?>},
				],
				columns: [
					{title: "<?php echo lang("material"); ?>", "class": "text-left dt-head-center w50"},
					{title: "<?php echo lang("categorie"); ?>", "class": "text-left dt-head-center w50"},
					{title: "<?php echo lang("quantity"); ?>", "class": "text-right dt-head-center"},
					{title: "<?php echo lang("treatment"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("retirement_date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
					{title: "<?php echo lang("retirement_evidence"); ?>","class": "text-center w100 no_breakline option"},
					{title: "<?php echo lang("reception_evidence"); ?>","class": "text-center w100 no_breakline option"},
				]
			});
		}
	});
	
	$('#excel_ultimos_retiros').click(function(){
		var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("waste_summary/get_excel_ultimos_retiros")?>').attr('method','POST').attr('target', '_self').appendTo('body');
		//$form.append('<input type="hidden" name="id_material" value="' + id_material + '" />');
		$form.submit();
	});
	
	$("#waste_summary_pdf").on('click', function(e) {	
		
		appLoader.show();
		
		var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
		var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
		var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';
		
		// Gráfico Residuos en Masa
		var chart = $('#vertical_stack_bar_1').highcharts().options.chart;
		var title = $('#vertical_stack_bar_1').highcharts().options.title;
		var subtitle = $('#vertical_stack_bar_1').highcharts().options.subtitle;
		var xAxis = $('#vertical_stack_bar_1').highcharts().options.xAxis;
		var yAxis = $('#vertical_stack_bar_1').highcharts().options.yAxis;
		var series = $('#vertical_stack_bar_1').highcharts().options.series;
		var plotOptions = $('#vertical_stack_bar_1').highcharts().options.plotOptions;
		var colors = $('#vertical_stack_bar_1').highcharts().options.colors;
		var exporting = $('#vertical_stack_bar_1').highcharts().options.exporting;
		var credits = $('#vertical_stack_bar_1').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
			"series":series,
			"plotOptions":plotOptions,
			"colors":colors,
			"exporting":exporting,
			"credits":credits,
		});
		
		obj.type = 'image/png';
		obj.width = '1600';
		obj.scale = '2';
		obj.async = true;
		
		var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
		obj.globaloptions = JSON.stringify(globalOptions);
		
		var image_residuos_masa = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';

		// Gráfico Residuos en Volumen
		var chart = $('#vertical_stack_bar_2').highcharts().options.chart;
		var title = $('#vertical_stack_bar_2').highcharts().options.title;
		var subtitle = $('#vertical_stack_bar_2').highcharts().options.subtitle;
		var xAxis = $('#vertical_stack_bar_2').highcharts().options.xAxis;
		var yAxis = $('#vertical_stack_bar_2').highcharts().options.yAxis;
		var series = $('#vertical_stack_bar_2').highcharts().options.series;
		var plotOptions = $('#vertical_stack_bar_2').highcharts().options.plotOptions;
		var colors = $('#vertical_stack_bar_2').highcharts().options.colors;
		var exporting = $('#vertical_stack_bar_2').highcharts().options.exporting;
		var credits = $('#vertical_stack_bar_2').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
			"series":series,
			"plotOptions":plotOptions,
			"colors":colors,
			"exporting":exporting,
			"credits":credits,
		});
		
		obj.type = 'image/png';
		obj.width = '1600';
		obj.scale = '2';
		obj.async = true;
		
		var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
		obj.globaloptions = JSON.stringify(globalOptions);
		
		var image_residuos_volumen = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
		
		// Gráfico Residuos almacenados (masa)
		$('#umbral_masa').highcharts().options.plotOptions.column.dataLabels.enabled = true;
		
		var chart = $('#umbral_masa').highcharts().options.chart;
		var title = $('#umbral_masa').highcharts().options.title;
		var subtitle = $('#umbral_masa').highcharts().options.subtitle;
		var xAxis = $('#umbral_masa').highcharts().options.xAxis;
		var yAxis = $('#umbral_masa').highcharts().options.yAxis;
		var series = $('#umbral_masa').highcharts().options.series;
		var exporting = $('#umbral_masa').highcharts().options.exporting;
		var plotOptions = $('#umbral_masa').highcharts().options.plotOptions;
		var colors = $('#umbral_masa').highcharts().options.colors;
		var credits = $('#umbral_masa').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
			"series":series,
			"plotOptions":plotOptions,
			"colors":colors,
			"exporting":exporting,
			"credits":credits,
		});
		
		obj.type = 'image/png';
		obj.width = '1600';
		obj.scale = '2';
		obj.async = true;
		
		var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
		obj.globaloptions = JSON.stringify(globalOptions);
		
		var image_residuos_almacenados_masa = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
		
		$('#umbral_masa').highcharts().options.plotOptions.column.dataLabels.enabled = false;

		// Gráfico Residuos almacenados (volumen)
		$('#umbral_volumen').highcharts().options.plotOptions.column.dataLabels.enabled = true;
		
		var chart = $('#umbral_volumen').highcharts().options.chart;
		var title = $('#umbral_volumen').highcharts().options.title;
		var subtitle = $('#umbral_volumen').highcharts().options.subtitle;
		var xAxis = $('#umbral_volumen').highcharts().options.xAxis;
		var yAxis = $('#umbral_volumen').highcharts().options.yAxis;
		var series = $('#umbral_volumen').highcharts().options.series;
		var plotOptions = $('#umbral_volumen').highcharts().options.plotOptions;
		var colors = $('#umbral_volumen').highcharts().options.colors;
		var exporting = $('#umbral_volumen').highcharts().options.exporting;
		var credits = $('#umbral_volumen').highcharts().options.credits;
		
		var obj = {};
		obj.options = JSON.stringify({
			"chart":chart,
			"title":title,
			"subtitle":subtitle,
			"xAxis":xAxis,
			"yAxis":yAxis,
			"series":series,
			"plotOptions":plotOptions,
			"colors":colors,
			"exporting":exporting,
			"credits":credits,
		});
		
		obj.type = 'image/png';
		obj.width = '1600';
		obj.scale = '2';
		obj.async = true;
		
		var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
		obj.globaloptions = JSON.stringify(globalOptions);
		
		var image_residuos_almacenados_volumen = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
		
		$('#umbral_volumen').highcharts().options.plotOptions.column.dataLabels.enabled = false;
		
		var imagenes_graficos = {
			image_residuos_masa:image_residuos_masa,
			image_residuos_volumen:image_residuos_volumen,
			image_residuos_almacenados_masa:image_residuos_almacenados_masa,
			image_residuos_almacenados_volumen,image_residuos_almacenados_volumen
		};

		$.ajax({
			url:  '<?php echo_uri("waste_summary/get_pdf") ?>',
			type:  'post',
			data: {imagenes_graficos:imagenes_graficos},
			//dataType:'json',
			success: function(respuesta){
				
				var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
				var link = document.createElement("a");
				link.download = respuesta;
				link.href = uri;
				link.click();
				
				borrar_temporal(uri);
			}

		});
		
	});	
	
	function borrar_temporal(uri){
			
		$.ajax({
			url:  '<?php echo_uri("waste_summary/borrar_temporal") ?>',
			type:  'post',
			data: {uri:uri},
			//dataType:'json',
			success: function(respuesta){
				appLoader.hide();
			}

		});

	}

	function getChartName(obj){
		var tmp = null;
		$.support.cors = true;
		$.ajax({
			async: false,
			type: 'post',
			dataType: 'text',
			url : AppHelper.highchartsExportUrl,
			data: obj,
			crossDomain:true,
			success: function (data) {
				tmp = data.replace(/files\//g,'');
				tmp = tmp.replace(/.png/g,'');
			}
		});
		return tmp;
	}
	
});
</script>