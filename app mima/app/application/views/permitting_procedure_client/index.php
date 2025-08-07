<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("permittings"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("permittings_procedure"); ?></a>
</nav>

<div class="panel panel-default mb15">
    <div class="page-title clearfix">
        <h1><?php echo lang('permittings_procedure'); ?></h1>
         <?php if($puede_ver == 1 && $id_permiso) { ?>
        	<a href="#" class="btn btn-danger pull-right" id="permittings_procedure_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
    	 <?php } ?>
    </div>
</div>

<?php if($puede_ver == 1) { ?>

	<?php if($id_permiso) { ?>
    
    
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('procedure_summary'); ?></h1>
                <?php if($puede_ver == 1 && $id_permiso) { ?>
            		<?php echo modal_anchor(get_uri("permitting_matrix_config/view/" . $id_permiso), lang('view_matrix')." "."<i class='fa fa-eye'></i>", array("class" => "btn btn-default pull-right", "title" => lang('view_matrix'), "data-post-id_permitting" => $id_permiso));?>
				<?php } ?>
            </div>
            <div class="panel-body">
    
                <div class="col-md-6">
            
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_procedure_status"); ?></th>
                            <th colspan="2" class="text-center"><?php echo lang("total"); ?></th>
                        </tr>
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left"><strong><?php echo lang("total_applicable_procedures"); ?></strong></td>
                            <td class="text-right"><?php echo to_number_project_format($total_permisos_aplicables, $id_proyecto); ?></td>
                            <td class="text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
                        </tr>
                        <?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
                            <tr>
                                <td class="text-left"><?php echo $estado["nombre_estado"]; ?></td>
                                <td class="text-right"><?php echo to_number_project_format($estado["cantidad_categoria"], $id_proyecto); ?></td>
                            	<td class="text-right"><?php echo to_number_project_format(($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables, $id_proyecto); ?> %</td>
                            </tr>
                            
                        <?php } ?>
    
                    </tbody>
                </table>
            
                </div>
            
                <div class="col-md-6">
                    <div class="panel panel-default">
                       <div class="page-title clearfix panel-success">
                          <!--<h3>Cambio climático</h3> -->
                          <div class="pt10 pb10 text-center"> <?php echo lang("total_procedures"); ?> </div>
                       </div>
                       <div class="panel-body">
                             <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                          <div id="grafico_tramitaciones_totales" style="height: 240px;"></div>
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
                <?php foreach($evaluados_permisos as $evaluado) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">
                        <div class="panel panel-default">
                           <div class="page-title clearfix panel-success">
                              <!--<h3>Cambio climático</h3> -->
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
                                <th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("status_procedure"); ?></th>
                               
                                <?php foreach($evaluados_permisos as $evaluado) { ?>
                                    <th colspan="2" class="text-center"><?php echo $evaluado->nombre_evaluado; ?></th>
                                <?php } ?>                                                
                            </tr>
                            <tr>
                                <?php foreach($evaluados_permisos as $evaluado) { ?>
                                    <th class="text-center">N°</th>
                                    <th class="text-center">%</th>
                                <?php } ?>                                                 
                            </tr>
                         
                         </thead>
                         <tbody>
                         
                           <tr>
                               <th class="text-left"><?php echo lang("total_applicable_procedures"); ?></th>
                               <?php foreach($evaluados_permisos as $evaluado) { ?>
                                    <td class=" text-right"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_permisos[$evaluado->id]), $id_proyecto); ?></td>
                                    <td class=" text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
                               <?php } ?>
                            </tr>
                            
                            <?php foreach($array_estados_evaluados_permisos as $id_estado => $estado_evaluado) { ?>
                            
                                <tr>
                                   <td class="text-left"><?php echo $total_cantidades_estados_evaluados_permisos[$id_estado]["nombre_estado"]; ?></td>
                                   <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                      			   <?php
									 $total_evaluado = array_sum($array_total_por_evaluado_permisos[$id_evaluado]);
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
                <h1><?php echo lang('status_procedure'); ?></h1>
                <div class="btn-group pull-right" role="group">
                    <button type="button" class="btn btn-success" id="excel_status_procedure"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="compliance_status-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
        
    </div>
    
    <?php } else { ?>
    
        <div class="panel panel-default mb15">
            <div class="panel-body">              
                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                    <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('permitting_matrix_not_enabled'); ?>
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


<script type="text/javascript">
$(document).ready(function(){
		$("#compliance_status-table").appTable({
            source: '<?php echo_uri("permitting_procedure_client/list_data/".$id_permiso); ?>',
            columns: [
                {title: "<?php echo lang("permitting_number"); ?>", "class": "text-right dt-head-center w50"}
				<?php echo $columnas;  ?>,
				//{title: '<i class="fa fa-bars" style="padding: 0px 70px"; ></i>', "class": "text-center option w150p"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		$('[data-toggle="tooltip"]').tooltip();
})
</script>
<script type="text/javascript">

	$(document).ready(function(){
		
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;	
		
		<?php if(!empty(array_filter($total_cantidades_estados_evaluados_permisos))){ ?>
		
		$('#grafico_tramitaciones_totales').highcharts({
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
				<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("permittings").'_'.clean(lang("total_procedures")).'_'.date("Y-m-d"); ?>
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
					foreach($total_cantidades_estados_evaluados_permisos as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
			],
			//colors: ['#398439', '#ac2925', '#d58512'],
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
				<?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
					{
						name: '<?php echo $estado["nombre_estado"]; ?>',
						y: <?php echo ($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables; ?>
					},
				<?php } ?>
				]
			}]
		});
		
		<?php }else{?>
				$('#grafico_tramitaciones_totales').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		<?php } ?>
		 
		<?php 

			$array_nombre_porcentaje = array();
			$array_colores = array();
			
		    foreach($evaluados_matriz_permiso as $evaluado) { 
				foreach($estados as $estado) {
					$array_colores[$estado["id_estado"]] = $Permitting_procedure_client_controller->get_color_of_status($estado["id_estado"]);
					$cantidad = $Permitting_procedure_client_controller->get_quantity_of_status_evaluated($estado["id_estado"], $evaluado["id"]);
					$array_nombre_porcentaje[$estado["nombre_estado"]] = $Permitting_procedure_client_controller->get_percentage_of_status_evaluated($cantidad, $estado["id_estado"], $evaluado["id"]);
				}
		?>
		
		<?php if(!empty(array_filter($array_nombre_porcentaje))){?>
		
				$('#grafico_resumen_evaluado_<?php echo $evaluado["id"]; ?>').highcharts({
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
						<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("permittings").'_'.clean(lang("summary_evaluated")).'_'.clean($evaluado["nombre_evaluado"]).'_'.date("Y-m-d"); ?>
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
							foreach($array_colores as $color) { 
								echo "'".$color."',";
							}
						?>
					],
					//colors: ['#398439', '#ac2925', '#d58512'],	
					series: [{
					   name: 'Porcentaje',
					   colorByPoint: true,					   
					   data: [
					   <?php foreach($array_nombre_porcentaje as $nombre => $porcentaje) { ?>
							{
								name: '<?php echo $nombre; ?>',
								y: <?php echo $porcentaje; ?>
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
		
		$('#excel_status_procedure').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("permitting_procedure_client/get_excel_status_procedure")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});
		
		$("#permittings_procedure_pdf").on('click', function(e) {
			
			appLoader.show();
				
			var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
			var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
			var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';
			
			// Gráfico Tramitaciones Totales
			var image_cumplimientos_totales;
			<?php if(!empty($total_cantidades_estados_evaluados_permisos)){ ?>
			
				$('#grafico_tramitaciones_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_tramitaciones_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_tramitaciones_totales').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_tramitaciones_totales').highcharts().options.plotOptions.pie.size = 150;
				
				var chart = $('#grafico_tramitaciones_totales').highcharts().options.chart;
				var title = $('#grafico_tramitaciones_totales').highcharts().options.title;
				var series = $('#grafico_tramitaciones_totales').highcharts().options.series;
				var plotOptions = $('#grafico_tramitaciones_totales').highcharts().options.plotOptions;
				var colors = $('#grafico_tramitaciones_totales').highcharts().options.colors;
				var exporting = $('#grafico_tramitaciones_totales').highcharts().options.exporting;
				var credits = $('#grafico_tramitaciones_totales').highcharts().options.credits;
		
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
			
				$('#grafico_tramitaciones_totales').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_tramitaciones_totales').highcharts().options.plotOptions.pie.size = null;
			
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
					
					var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator}};
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
			
			var imagenes_graficos = {
				image_cumplimientos_totales:image_cumplimientos_totales,
				graficos_resumen_evaluados: graficos_resumen_evaluados
			};
			
			$.ajax({
				url:  '<?php echo_uri("permitting_procedure_client/get_pdf"); ?>',
				type:  'post',
				data: {imagenes_graficos:imagenes_graficos},
				//dataType:'json',
				success: function(respuesta){
					
					var uri = '<?php echo get_setting("temp_file_path"); ?>' + respuesta;
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
				url:  '<?php echo_uri("permitting_procedure_client/borrar_temporal"); ?>',
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