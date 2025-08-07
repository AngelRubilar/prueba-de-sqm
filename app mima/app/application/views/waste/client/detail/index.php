<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("waste"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("detail"); ?></a>
    </nav>

    <div class="panel panel-default m0">
		<div class="page-title clearfix">
			<h1><?php echo lang('detail') ?></h1>
		</div>
    </div>

	<?php if($puede_ver == 1) { ?>
    
        <div class="row" >
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0px 0px 0px 0px">
                    <?php echo form_open(get_uri("client_waste_detail/save"), array("id" => "client_waste_detail-form", "class" => "general-form", "role" => "form")); ?>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="row">
                                <div class="col-md-4 p0">
                                    <div class="p18" style="text-align: center;">
                                        <label for="material"><?php echo lang("materials");?></label>
                                    </div>
                                </div>
                                <div class="col-md-8 p0">
                                    <div class="p10" style="padding-right:0px;">
                                        <div class="form-group" style="margin-bottom: 0px;">
                                            <?php
                                            echo form_dropdown("material", $dropdown_material, "", "id='material' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="padding-right:0px;">
                            <div class="p18 m20_left" style="text-align: center;">
                                <label><?php echo lang("date_range");?></label>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6" style="padding-left:0px;">
                            <div class= "row">
                                <div class="form-group" style="margin: 0px 0px 0px 0px;">
                                    <div class="col-md p10">
                                        <?php
                                        echo form_input(array(
                                            "id" => "date_since",
                                            "name" => "date_since",
                                            "value" => "",
                                            "class" => "form-control datepicker",
                                            "placeholder" => lang('since'),
                                            "data-rule-required" => true,
                                            "data-msg-required" => lang("field_required"),
                                            "autocomplete" => "off",
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6">
                            <div class= "row">
                                <div class="form-group" style="margin: 0px 0px 0px 0px;">
                                    <div class="col-md p10">
                                        <?php
                                        echo form_input(array(
                                            "id" => "date_until",
                                            "name" => "date_until",
                                            "value" => "",
                                            "class" => "form-control datepicker",
                                            "placeholder" => lang('until'),
                                            "data-rule-required" => true,
                                            "data-msg-required" => lang("field_required"),
                                            "data-rule-greaterThanOrEqual" => "#date_since",
                                            "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                            "autocomplete" => "off",
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="row">
                                <div class="col-md p10" style="text-align:center;">
                                    <button type="submit" id="generate" class="btn btn-primary" data-dismiss="modal" style="height:34px;"><span class="fa fa-eye"></span> <?php echo lang('generate'); ?></button>
                                	<button type="button" class="btn btn-danger pull-right" id="waste_detail_pdf" disabled="disabled"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></button>
                                </div>
                            </div>
                        </div>
                    <?php echo form_close(); ?>	
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row hidden" id="row-loading">
            <div class="col-md-12">
                <div class="panel">
                    <div id="loading" class="panel-body" style="padding: 0px 0px 0px 0px">
    
                    </div>
                </div>
            </div>
        </div>
    
        <div class="row" >
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0px 0px 0px 0px">
                        <div id="indicator" class="row">
                            
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
                            <div id="vertical_stack_bar_container" class="panel-body hidden">
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
                            <div id="fixed_placement_columns_container" class="panel-body hidden">
                                <div class="col-md-6">
                                <div id="contenedor_grafico_masa"></div>
                                </div>
                                <div class="col-md-6">
                                <div id="contenedor_grafico_volumen"></div>
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
                            <div id="table_container" class="panel-body hidden" style="padding-top: 0px;">
                                <div class="page-title clearfix" style="background-color:white;">
                                    <h1><?php echo lang("last_withdrawals")?></h1>
                                    <div class="btn-group pull-right" role="group">
                                        <button type="button" class="btn btn-success" id="excel_ultimos_retiros"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                                    </div>
                                </div>
                                <div class="table-responsive" id="detail-table_container">
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
        <div id="grafico_umbrales_masa">
        </div>
        <div id="grafico_umbrales_volumen">
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

	$("#client_waste_detail-form").appForm({
		ajaxSubmit: false
	});

	$("#client_waste_detail-form").submit(function(e){
		e.preventDefault();
		return false;
	});
	
	$("#material").select2();
	setDatePicker("#date_since");
	setDatePicker('#date_until');
	
	var id_material;
	var date_since;
	var date_until;
	var id_project = <?php echo $id_project ?>;
	var id_cliente = <?php echo $id_cliente ?>;
	
	$("#generate").click(function() {
		//$("#detail-table").html("<table id='detail-table' class='display' cellspacing='0' width='100%'></table>");
		id_material = $('#material').val();
		date_since = $('#date_since').val();
		date_until = $('#date_until').val();

		if(id_cliente && id_project && date_since && date_until && id_material ){
			if((date_since < date_until) || (date_since == date_until)){
				
				$.ajax({
					url:  '<?php echo_uri("client_waste_detail/list_data") ?>',
					type:  'post',
					data: {id_material:id_material,date_since:date_since,date_until:date_until,id_project:id_project,id_cliente:id_cliente},
					beforeSend: function(){ 
					
						$("#row-loading").removeClass("hidden");
						
						$("#indicator").addClass("hidden");
						$("#fixed_placement_columns_container").addClass("hidden");
						$("#vertical_stack_bar_container").addClass("hidden");
						$("#table_container").addClass("hidden");
						$("#grafico_masa").addClass("hidden");
						$("#grafico_volumen").addClass("hidden");
						$("#grafico_umbrales_masa").addClass("hidden");
						$("#grafico_umbrales_volumen").addClass("hidden");
						
						$('#loading').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');

					},
					success: function(result){
						
						$("#row-loading").addClass("hidden");
						var obj = jQuery.parseJSON(result);
						
						$("#indicator").removeClass("hidden");
						$("#fixed_placement_columns_container").removeClass("hidden");
						$("#vertical_stack_bar_container").removeClass("hidden");
						$("#table_container").removeClass("hidden");
						$("#grafico_masa").removeClass("hidden");
						$("#grafico_volumen").removeClass("hidden");
						$("#grafico_umbrales_masa").removeClass("hidden");
						$("#grafico_umbrales_volumen").removeClass("hidden");
						
						$('#indicator').html(obj.indicators_view);
						$('#grafico_masa').html(obj.grafico_masa);
						$('#grafico_volumen').html(obj.grafico_volumen);
						$('#grafico_umbrales_masa').html(obj.grafico_umbrales_masa);
						$('#grafico_umbrales_volumen').html(obj.grafico_umbrales_volumen);
						
						/*
						var check_table = $.fn.DataTable.isDataTable('#detail-table');
						if(check_table == true){
							$('#detail-table').DataTable().clear();
							$('#detail-table').DataTable().destroy();
							
							//$('#detail-table_container').replaceWith("<div class='table-responsive' id='detail-table_container'><table id='detail-table' class='display' cellspacing='0' width='100%'></table></div>");
						}
						*/
						
						$('#detail-table_container').html('<table id="detail-table" class="display" cellspacing="0" width="100%"></table>');
						$("#detail-table").appTable({
							source: '<?php echo_uri("client_waste_detail/list_data_table/"); ?>'+id_project+'/'+id_material+'/'+date_since+'/'+date_until+'',
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
						
						$('#waste_detail_pdf').removeAttr('disabled');
						
					}
				});
			}
		}
	});
	
	$('#excel_ultimos_retiros').click(function(){
		var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("client_waste_detail/get_excel_ultimos_retiros")?>').attr('method','POST').attr('target', '_self').appendTo('body');
		$form.append('<input type="hidden" name="id_material" value="' + id_material + '" />');
		$form.append('<input type="hidden" name="date_since" value="' + date_since + '" />');
		$form.append('<input type="hidden" name="date_until" value="' + date_until + '" />');
		$form.submit();
	});

	$("#waste_detail_pdf").on('click', function(e) {
		
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
		var chart = $('#contenedor_grafico_masa').highcharts().options.chart;
		var title = $('#contenedor_grafico_masa').highcharts().options.title;
		var subtitle = $('#contenedor_grafico_masa').highcharts().options.subtitle;
		var xAxis = $('#contenedor_grafico_masa').highcharts().options.xAxis;
		var yAxis = $('#contenedor_grafico_masa').highcharts().options.yAxis;
		var series = $('#contenedor_grafico_masa').highcharts().options.series;
		//var plotOptions = $('#umbral_masa').highcharts().options.plotOptions;
		$("#contenedor_grafico_masa").highcharts().update({
			plotOptions: {
				column: {
					dataLabels: {
						enabled: true,
					}
				}
			}
		});

		var exporting = $('#contenedor_grafico_masa').highcharts().options.exporting;
		var plotOptions = $('#contenedor_grafico_masa').highcharts().options.plotOptions;
		var colors = $('#contenedor_grafico_masa').highcharts().options.colors;
		var credits = $('#contenedor_grafico_masa').highcharts().options.credits;
		
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
		
		$("#contenedor_grafico_masa").highcharts().update({
			plotOptions: {
				column: {
					dataLabels: {
						enabled: false,
					}
				}
			}
		});

		// Gráfico Residuos almacenados (volumen)
		var chart = $('#contenedor_grafico_volumen').highcharts().options.chart;
		var title = $('#contenedor_grafico_volumen').highcharts().options.title;
		var subtitle = $('#contenedor_grafico_volumen').highcharts().options.subtitle;
		var xAxis = $('#contenedor_grafico_volumen').highcharts().options.xAxis;
		var yAxis = $('#contenedor_grafico_volumen').highcharts().options.yAxis;
		var series = $('#contenedor_grafico_volumen').highcharts().options.series;
		//var plotOptions = $('#umbral_volumen').highcharts().options.plotOptions;
		
		$("#contenedor_grafico_volumen").highcharts().update({
			plotOptions: {
				column: {
					dataLabels: {
						enabled: true,
					}
				}
			}
		});
		
		var plotOptions = $('#contenedor_grafico_volumen').highcharts().options.plotOptions;
		var colors = $('#contenedor_grafico_volumen').highcharts().options.colors;
		var exporting = $('#contenedor_grafico_volumen').highcharts().options.exporting;
		var credits = $('#contenedor_grafico_volumen').highcharts().options.credits;
		
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
		
		$("#contenedor_grafico_volumen").highcharts().update({
			plotOptions: {
				column: {
					dataLabels: {
						enabled: false,
					}
				}
			}
		});
		
		var imagenes_graficos = {
			image_residuos_masa:image_residuos_masa,
			image_residuos_volumen:image_residuos_volumen,
			image_residuos_almacenados_masa:image_residuos_almacenados_masa,
			image_residuos_almacenados_volumen,image_residuos_almacenados_volumen
		};
		
		$.ajax({
			url:  '<?php echo_uri("client_waste_detail/get_pdf") ?>',
			type:  'post',
			data: {
				id_material:id_material,
				date_since:date_since,
				date_until:date_until,
				imagenes_graficos:imagenes_graficos
			},
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