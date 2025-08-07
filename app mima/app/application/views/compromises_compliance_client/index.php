<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("compromises"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("compromises_compliance"); ?></a>
</nav>

<div class="panel panel-default mb15">
    <div class="page-title clearfix">
        <h1><?php echo lang('compromises_compliance'); ?></h1>
        <?php if($puede_ver == 1 && $id_compromiso_rca) { ?>
        	<a href="#" class="btn btn-danger pull-right" id="compromises_compliance_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
    	<?php } ?>
    </div>
</div>

<?php if($puede_ver == 1) { ?>

	<?php if($id_compromiso_rca) { ?>
    
    
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('compliance_summary'); ?></h1>
                <?php if($puede_ver == 1 && $id_compromiso_rca) { ?>
                	<?php echo modal_anchor(get_uri("compromises_rca_matrix_config/view/" . $id_compromiso_rca), lang('view_matrix')." "."<i class='fa fa-eye'></i>", array("class" => "btn btn-default pull-right", "title" => lang('view_matrix'), "data-post-id_compromiso" => $id_compromiso_rca)); ?>
            	<?php } ?>
            </div>
            <div class="panel-body">
    
                <div class="col-md-6">
            
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_compliance_status"); ?></th>
                            <th colspan="2" class="text-center"><?php echo lang("total"); ?></th>
                        </tr>
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left"><strong><?php echo lang("total_applicable_compromises"); ?></strong></td>
                            <td class="text-right"><?php echo to_number_project_format($total_compromisos_aplicables_rca, $id_proyecto); ?></td>
                            <td class="text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
                        </tr>
                        <?php foreach($total_cantidades_estados_evaluados_rca as $estado) { ?>
                            <tr>
                                <td class="text-left"><?php echo $estado["nombre_estado"]; ?></td>
                                <td class="text-right"><?php echo to_number_project_format($estado["cantidad_categoria"], $id_proyecto); ?></td>
                                <td class="text-right"><?php echo to_number_project_format(($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables_rca, $id_proyecto); ?> %</td>
                            </tr>
                            
                        <?php } ?>
    
                    </tbody>
                </table>
            
                </div>
            
                <div class="col-md-6">
                    <div class="panel panel-default">
                       <div class="page-title clearfix panel-success">
                          <!--<h3>Cambio climático</h3> -->
                          <div class="pt10 pb10 text-center"> <?php echo lang("total_compliances"); ?> </div>
                       </div>
                       <div class="panel-body">
                             <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                          <div id="grafico_cumplimientos_totales" style="height: 240px;"></div>
                       </div>
                    </div>
                 </div>
            
            </div> 
            
        </div>
        
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('summary_by_evaluated'); ?></h1>
            </div>
            <div class="panel-body">
                
                <!-- UN GRÁFICO POR CADA EVALUADO -->
                <?php foreach($evaluados_rca as $evaluado) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">
                        <div class="panel panel-default">
                           <div class="page-title clearfix panel-success">
                              <div class="pt10 pb10 text-center"> <?php echo $evaluado->nombre_evaluado; ?> </div>
                           </div>
                           <div class="panel-body">
                                 <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                              <div id="grafico_resumen_evaluado_<?php echo $evaluado->id; ?>" style="height: 240px;" class="grafico_resumen_evaluado" data-nombre_evaluado="<?php echo $evaluado->nombre_evaluado; ?>" data-tiene_evaluacion="1"></div>
                           </div>
                        </div>
                    </div>
                <? } ?>
            </div>
            
            <div class="panel-body">
                
                <div class="table-responsive">
                   <div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">
                      
                      <table id="tabla_resumen_por_evaluado" class="table table-striped">
                         <thead>
                         
                            <tr>
                                <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("compliance_status"); ?></th>
                               
                                <?php foreach($evaluados_rca as $evaluado) { ?>
                                    <th colspan="2" class="text-center"><?php echo $evaluado->nombre_evaluado; ?></th>
                                <?php } ?>                                                
                            </tr>
                            <tr>
                                <?php foreach($evaluados_rca as $evaluado) { ?>
                                    <th class="text-center">N°</th>
                                    <th class="text-center">%</th>
                                <?php } ?>                                                 
                            </tr>
                         
                         </thead>
                         <tbody>
                         
                           <tr>
                               <th class="text-left"><?php echo lang("total_applicable_compromises"); ?></th>
                               <?php foreach($evaluados_rca as $evaluado) { ?>
                                    <td class=" text-right"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_rca[$evaluado->id]), $id_proyecto); ?></td>
                                    <td class=" text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
                               <?php } ?>
                            </tr>
                            
                            <?php foreach($total_cantidades_estados_evaluados_rca as $estado_evaluado) { ?>
                            
                                <tr>
                                   <td class="text-left"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                                   <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                      
                                        <?php 
										$total_evaluado = array_sum($array_total_por_evaluado_rca[$id_evaluado]);
										if($total_evaluado == 0){
											$porcentaje = 0;
										} else {
											$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
										}
										?>
                                   
                                        <td class="text-right"><?php echo to_number_project_format($evaluado["cant"], $id_proyecto); ?></td>
                                        <td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?> %</td>
                                   <?php } ?>
                                </tr>
                            
                            <?php } ?>
    
                         </tbody>
                      </table>
                 
                   </div>
                </div>
                
            </div>
            
        </div>
        
        
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('compliance_status'); ?></h1>
                <div class="btn-group pull-right" role="group">
                    <button type="button" class="btn btn-success" id="excel_compliance_status"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="compliance_status-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
        
	
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('reportable_compromises'); ?></h1>
                <?php if($puede_ver == 1 && $id_compromiso_reportables) { ?>
                	<?php echo modal_anchor(get_uri("compromises_reportables_matrix_config/view/" . $id_compromiso_reportables), lang('view_matrix')." "."<i class='fa fa-eye'></i>", array("class" => "btn btn-default pull-right", "title" => lang('view_matrix'), "data-post-id_compromiso" => $id_compromiso_reportables)); ?>
            	<?php } ?>
            </div>
    
            <div class="panel-body">
                                                    
                <div class="col-md-6">
                
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_compliance_status"); ?></th>
                                <th colspan="2" class="text-center"><?php echo lang("sub_total"); ?></th>
                            </tr>
							<tr>
								<th class="text-center">N°</th>
								<th class="text-center">%</th>
							</tr>
                        </thead>
                        <tbody>
                        <?php foreach($compromisos_reportables as $cr) { ?>
                        <?php
							if($total_reportables == 0){
								$porcentaje = 0;
							} else {
								$porcentaje = ($cr["cant"] * 100) / ($total_reportables);
							}
						?>
                            <tr>
                                <td class="text-left"><?php echo $cr["nombre_estado"]; ?></td>
                                <td class="text-right"><?php echo to_number_project_format($cr["cant"], $id_proyecto); ?></td>
                                <td class="text-right"><?php echo to_number_project_format($porcentaje, $id_proyecto); ?> %</td>
                            </tr>
                        <?php } ?>    
                        </tbody>
                    </table>
                
                </div>
                
                <div class="col-md-6">
                    <div class="panel panel-default">
                       <div class="page-title clearfix panel-success">
                          <!--<h3>Cambio climático</h3> -->
                          <div class="pt10 pb10 text-center"><?php echo lang("reportable_compliances"); ?></div>
                       </div>
                       <div class="panel-body">
                             <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                          <div id="grafico_cumplimientos_reportables" style="height: 240px;"></div>
                       </div>
                    </div>
                </div>
                
            </div> 
    
        </div>
        
        
        
    </div>
    
    <?php } else { ?>
    
        <div class="panel panel-default mb15">
            <div class="panel-body">              
                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                    <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('compromise_matrix_not_enabled'); ?>
                </div>
            </div>	  
        </div>
    
    <?php } ?>

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
$(document).ready(function(){
	
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;	
	
		$("#compliance_status-table").appTable({
            source: '<?php echo_uri("compromises_compliance_client/list_data/".$id_compromiso_rca); ?>',
			filterDropdown: [
				{name: "reportabilidad", class: "w200", <?php if($reportabilidad_dropdown){ ?>options: <?php echo $reportabilidad_dropdown; ?><?php } ?>}
			],
            columns: [
                {title: "<?php echo lang("compromise_number"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("reportability"); ?>", "class": "text-center dt-head-center w50"}
				<?php echo $columnas;  ?>,
				//{title: '<i class="fa fa-bars" style="padding: 0px 70px"; ></i>', "class": "text-center option w150p"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		$('[data-toggle="tooltip"]').tooltip();


		<?php if($total_compromisos_aplicables_rca){ ?>
		
		
		$('#grafico_cumplimientos_totales').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie',
				events: {
				   load: function() {
					   if (this.options.chart.forExport) {
						   Highcharts.each(this.series, function (series) {
							   series.update({
								   dataLabels: {
									   enabled: true,
									}
								}, false);
							});
							this.redraw();
						}
					}
				}
			},
			title: {
				text: '',
			},
			credits: {
				enabled: false
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
				},
				//pointFormat: '{series.name}: <b>{point.y}%</b>'
			},
			plotOptions: {
				pie: {
				//size: 80,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false,
					format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
						fontSize: "9px",
						distance: -30
					},
					crop: false
				},
				showInLegend: true
				}
			},
			legend: {
				enabled: true,
				itemStyle:{
					fontSize: "9px"
				}
			},
			exporting: {
				<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compromises").'_'.clean(lang("total_compliances")).'_'.date("Y-m-d"); ?>
				filename: "<?php echo $filename; ?>",
				buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang('export_to_png'); ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			},
			colors: [
				<?php 
					foreach($total_cantidades_estados_evaluados_rca as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
			],
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
				<?php foreach($total_cantidades_estados_evaluados_rca as $estado) { ?>
					{
						name: '<?php echo $estado["nombre_estado"]; ?>',
						y: <?php echo ($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables_rca; ?>
					},
				<?php } ?>
				
				]
			}]
		});
		
		<?php }else{?>
				$('#grafico_cumplimientos_totales').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		<?php } ?>
		 
		<?php if(!empty(array_filter($compromisos_reportables))){ ?>
		
		$('#grafico_cumplimientos_reportables').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie',
				events: {
				   load: function() {
					   if (this.options.chart.forExport) {
						   Highcharts.each(this.series, function (series) {
							   series.update({
								   dataLabels: {
									   enabled: true,
									}
								}, false);
							});
							this.redraw();
						}
					}
				}
			},
			title: {
				text: '',
			},
			credits: {
				enabled: false
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
			   	},
				//pointFormat: '{series.name}: <b>{point.y}%</b>'
			},
			plotOptions: {
				pie: {
				//size: 80,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false,
					format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
						fontSize: "9px",
						distance: -30
					},
					crop: false
				},
				showInLegend: true
				}
			},
			legend: {
				enabled: true,
				itemStyle:{
					fontSize: "9px"
				}
			},
			exporting: {
				<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compromises").'_'.clean(lang("reportable_compliances")).'_'.date("Y-m-d"); ?>
				filename: "<?php echo $filename; ?>",
				buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang('export_to_png'); ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			},
			colors: [
				<?php 
					foreach($grafico_reportables as $cr) { 
						echo "'".$cr["color"]."',";
					}
				?>
			],
			
			//colors: ['#398439', '#ac2925', '#d58512'],
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
				<?php foreach($grafico_reportables as $cr) { ?>
					{
						name: '<?php echo $cr["nombre_estado"]; ?>',
						y: <?php echo $cr["porcentaje"]; ?>
					},
				<?php } ?>
				
				]
			}]
		});
		
		<?php }else{ ?>
			$('#grafico_cumplimientos_reportables').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		<?php }?>
		<?php 

			$array_nombre_porcentaje = array();
			$array_colores = array();
			
		    foreach($evaluados_rca as $evaluado) {
				$total = array_sum($array_total_por_evaluado_rca[$evaluado->id]);
		?>
		
		<?php if($array_total_por_evaluado_rca[$evaluado->id]){?>
		
				$('#grafico_resumen_evaluado_<?php echo $evaluado->id; ?>').highcharts({
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie',
						events: {
						   load: function() {
							   if (this.options.chart.forExport) {
								   Highcharts.each(this.series, function (series) {
									   series.update({
										   dataLabels: {
											   enabled: true,
											}
										}, false);
									});
									this.redraw();
								}
							}
						}
					},
					title: {
						text: '',
					},
					credits: {
						enabled: false
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
						},
						//pointFormat: '{series.name}: <b>{point.y}%</b>'
					},
					plotOptions: {
						pie: {
						//size: 80,
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false,
							format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
								fontSize: "9px",
								distance: -30
							},
							crop: false
						},
						showInLegend: true
						}
					},
					legend: {
						enabled: true,
						itemStyle:{
							fontSize: "9px"
						}
					},
					exporting: {
						<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("compromises").'_'.clean(lang("summary_evaluated")).'_'.clean($evaluado->nombre_evaluado).'_'.date("Y-m-d"); ?>
						filename: "<?php echo $filename; ?>",
						buttons: {
							contextButton: {
								menuItems: [{
									text: "<?php echo lang('export_to_png'); ?>",
									onclick: function() {
										this.exportChart();
									},
									separator: false
								}]
							}
						}
					},
					colors: [
						<?php 
							foreach($total_cantidades_estados_evaluados_rca as $estado) { 
								echo "'".$estado["color"]."',";
							}
						?>
					],	
					series: [{
					   name: 'Porcentaje',
					   colorByPoint: true,					   
					   data: [
					   <?php foreach($total_cantidades_estados_evaluados_rca as $id_estado => $estado) { 
					   
					   if($total == 0){
							$porcentaje = 0;
						} else {
							$valor_evaluado_estado = array_sum($total_cantidades_evaluados_estados_rca[$evaluado->id][$id_estado]);
							$porcentaje = ($valor_evaluado_estado * 100) / ($total); 
						}
					   
					   ?>
							{
								name: '<?php echo $estado["nombre_estado"]; ?>',
								y: <?php echo $porcentaje; ?>1
							},	
					   <?php } ?>
					   ]		   
					}]
				});
							 
			<?php }else{ ?>
							 
				$('#grafico_resumen_evaluado_<?php echo $evaluado["id"]; ?>')
				.html("<strong><?php echo lang("no_information_available") ?></strong>")
				.css({"text-align":"center", "vertical-align":"middle", "display":"table-cell"})
				.attr("data-tiene_evaluacion", "0")
				.attr("data-nombre_evaluado", "<?php echo $evaluado["nombre_evaluado"]; ?>");
							 
			<?php } ?>
			 
		<?php } ?>		
		
		$('#excel_compliance_status').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("compromises_compliance_client/get_excel_compliance_status")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});
		
		
		$("#compromises_compliance_pdf").on('click', function(e) {
			
			appLoader.show();
			
			var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
			var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
			var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';

			// Gráfico Cumplimientos Totales
			var image_cumplimientos_totales;
			<?php if($total_compromisos_aplicables_rca){ ?>
			
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.size = 150;
				
				var chart = $('#grafico_cumplimientos_totales').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_totales').highcharts().options.title;
				var series = $('#grafico_cumplimientos_totales').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_totales').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_totales').highcharts().options.colors;
				var exporting = $('#grafico_cumplimientos_totales').highcharts().options.exporting;
				var credits = $('#grafico_cumplimientos_totales').highcharts().options.credits;
	
				var obj = {};
				obj.options = JSON.stringify({
					"chart":chart,
					"title":title,
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

				image_cumplimientos_totales = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_cumplimientos_totales').highcharts().options.plotOptions.pie.size = null;
			
			<?php } ?>

			// Gráficos Resumen por Evaluado
			var graficos_resumen_evaluados = {};
			$('.grafico_resumen_evaluado').each(function(){
			
				var id = $(this).attr('id');
				var nombre_evaluado = $(this).attr("data-nombre_evaluado");
				var tiene_evaluacion = $(this).attr("data-tiene_evaluacion");
				
				if(tiene_evaluacion == "1"){
					
					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
					$('#' + id).highcharts().options.title.text = nombre_evaluado;
					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
					$('#' + id).highcharts().options.plotOptions.pie.size = 150;
					$('#' + id).highcharts().options.legend.itemStyle.fontSize = "15px";
					$('#' + id).highcharts().options.title.style.fontSize = "23px";
					
					var chart = $('#' + id).highcharts().options.chart;
					var series = $('#' + id).highcharts().options.series;
					var title = $('#' + id).highcharts().options.title;
					var plotOptions = $('#' + id).highcharts().options.plotOptions;
					var colors = $('#' + id).highcharts().options.colors;
					var exporting = $('#' + id).highcharts().options.exporting;
					var credits = $('#' + id).highcharts().options.credits;
					var legend = $('#' + id).highcharts().options.legend;
	
					var obj = {};
					obj.options = JSON.stringify({
						"chart":chart,
						"title":title,
						"series":series,
						"plotOptions":plotOptions,
						"colors":colors,
						"exporting":exporting,
						"credits":credits,
						"legend":legend,
					});
					
					obj.type = 'image/png';
					obj.width = '1600';
					obj.scale = '2';
					obj.async = true;
					
					var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
					obj.globaloptions = JSON.stringify(globalOptions);
					
					var image_resumen_evaluado = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
					
					graficos_resumen_evaluados[id] = image_resumen_evaluado;
					
					$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = false;
					$('#' + id).highcharts().options.plotOptions.pie.size = null;
					$('#' + id).highcharts().options.legend.itemStyle.fontSize = "9px;";
					
				} else {

					var image = id;
					graficos_resumen_evaluados[id] = image;
					
				}

			});
			
			// Gráfico Cumplimientos Reportables
			var image_cumplimientos_reportables;
			<?php if(!empty(array_filter($compromisos_reportables))){ ?>
			
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.size = 150;
				
				var chart = $('#grafico_cumplimientos_reportables').highcharts().options.chart;
				var title = $('#grafico_cumplimientos_reportables').highcharts().options.title;
				var series = $('#grafico_cumplimientos_reportables').highcharts().options.series;
				var plotOptions = $('#grafico_cumplimientos_reportables').highcharts().options.plotOptions;
				var colors = $('#grafico_cumplimientos_reportables').highcharts().options.colors;
				var exporting = $('#grafico_cumplimientos_reportables').highcharts().options.exporting;
				var credits = $('#grafico_cumplimientos_reportables').highcharts().options.credits;
				
				var obj = {};
				obj.options = JSON.stringify({
					"chart":chart,
					"title":title,
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
				
				image_cumplimientos_reportables = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_cumplimientos_reportables').highcharts().options.plotOptions.pie.size = null;
			
			<?php } ?>
			
			var imagenes_graficos = {
				image_cumplimientos_totales:image_cumplimientos_totales,
				graficos_resumen_evaluados: graficos_resumen_evaluados,
				image_cumplimientos_reportables:image_cumplimientos_reportables
			};
			
			$.ajax({
				url:  '<?php echo_uri("compromises_compliance_client/get_pdf") ?>',
				type:  'post',
				data: {imagenes_graficos:imagenes_graficos},
				//dataType:'json',
				success: function(respuesta){
					
					var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
					console.log(uri);
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
				url:  '<?php echo_uri("compromises_compliance_client/borrar_temporal") ?>',
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