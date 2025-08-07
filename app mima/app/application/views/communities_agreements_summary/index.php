<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("communities"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("communities_summary"); ?></a>
</nav>

<div class="panel panel-default mb15">
    <div class="page-title clearfix">
        <h1><?php echo lang('communities_summary'); ?></h1>
        <?php if($puede_ver == 1 && $id_agreement_matrix_config) { ?>
        	<a href="#" class="btn btn-danger pull-right" id="communities_summary_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
    	<?php } ?>
    </div>
</div>

<?php if($puede_ver == 1) { ?>

	<?php if($id_agreement_matrix_config) { ?>
    
    	<!-- INICIO SECCIÓN STAKEHOLDERS -->
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('stakeholders'); ?></h1>
            </div>
            <div class="panel-body">
    
                <div class="col-md-6">
            
                <table class="table table-striped">
                    <thead>
                    	<tr>
                        	<th class="text-center"><?php echo lang("stakeholder_category"); ?></th>
                            <th class="text-center">N°</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stakeholders_categories as $sc) { ?>
                            <tr>
                                <td class="text-left"><?php echo lang($sc->nombre); ?></td>
                                <td class="text-right"><?php echo to_number_project_format($sc->cant_tipo_org_stakeholder, $project_info->id); ?></td>
                            </tr> 
                        <?php } ?>
                    </tbody>
                </table>
            
                </div>
            
                <div class="col-md-6">
                    <div class="panel panel-default">
                       <div class="page-title clearfix panel-success">
                          <div class="pt10 pb10 text-center"> <?php echo lang("total_stakeholders_categories"); ?> </div>
                       </div>
                       <div class="panel-body">
                          <div id="grafico_categorias_totales_sh" style="height: 240px;"></div>
                       </div>
                    </div>
                 </div>
            
            </div> 
            
        </div>
        <!-- FIN SECCIÓN STAKEHOLDERS -->
        
        <!-- INICIO SECCIÓN ACUERDOS -->
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('agreements'); ?></h1>
            </div>
            <div class="panel-body">
                
                <div class="col-md-4">
                    <div class="panel panel-default panel_graficos_acuerdos">
                       <div class="page-title clearfix panel-success">
                          <div class="pt10 pb10 text-center"> <?php echo lang("processing_status"); ?> </div>
                       </div>
                       <div class="panel-body">
                          <div id="grafico_estado_tramitacion" style="height: 240px;"></div>
                       </div>
                       <!--
                       <div class="panel-body">
                            <table id="tabla_estado_tramitacion" class="display" cellspacing="0" width="100%">            
           					</table>
                        </div>
                        -->
                    </div>
                 </div>
                 
                 <div class="col-md-4">
                    <div class="panel panel-default panel_graficos_acuerdos">
                       <div class="page-title clearfix panel-success">
                          <div class="pt10 pb10 text-center"> <?php echo lang("activities_status"); ?> </div>
                       </div>
                       <div class="panel-body">
                          <div id="grafico_estado_actividades" style="height: 240px;"></div>
                       </div>
                       <!--
                       <div class="panel-body">
                            <table id="tabla_estado_actividades" class="display" cellspacing="0" width="100%">            
           					</table>
                        </div>
                        -->
                    </div>
                 </div>
                 
                 <div class="col-md-4">
                    <div class="panel panel-default panel_graficos_acuerdos">
                       <div class="page-title clearfix panel-success">
                          <div class="pt10 pb10 text-center"> <?php echo lang("financial_status"); ?> </div>
                       </div>
                       <div class="panel-body">
                          <div id="grafico_estado_financiero" style="height: 240px;"></div>
                       </div>
                       <!--
                       <div class="panel-body">
                       		<table id="tabla_estado_financiero" class="display" cellspacing="0" width="100%">            
           					</table>
                        </div>
                        -->
                    </div>
                 </div>
               
            </div>
            
            <div class="page-title clearfix" style="border-top: 1px solid rgba(221, 230, 233, 0.48);">
                <h1><?php echo lang('detail'); ?></h1>
                <div class="btn-group pull-right" role="group">
                    <button type="button" class="btn btn-success" id="excel_agreements"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                </div>
            </div>
          
            <!-- TABLERO CONSOLIDADO -->
            <div class="panel-body">
            	<table id="tabla_acuerdos_consolidado" class="display" cellspacing="0" width="100%"></table>
            </div>
            <!-- FIN TABLERO CONSOLIDADO-->
            
        </div>
		<!-- FIN SECCIÓN ACUERDOS -->
        
        <!-- INICIO SECCIÓN FEEDBACK -->
        <div class="panel panel-default mb15">
            <div class="page-title clearfix">
                <h1><?php echo lang('feedback'); ?></h1>
            </div>
            <div class="panel-body">
               
               <div class="col-md-6">
            
                <table class="table table-striped">
                    <thead>
                    	<tr>
                        	<th class="text-center"><?php echo lang("stakeholder_category"); ?></th>
                            <th class="text-center">N°</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($number_of_visits_by_type_of_stakeholder as $nv) { ?>
                            <tr>
                                <td class="text-left"><?php echo lang($nv->nombre); ?></td>
                                <td class="text-right"><?php echo to_number_project_format($nv->numero_visitas, $project_info->id); ?></td>
                            </tr> 
                        <?php } ?>
                    </tbody>
                </table>
            
                </div>
            
                <div class="col-md-6">
                    <div class="panel panel-default">
                       <div class="page-title clearfix panel-success">
                          <div class="pt10 pb10 text-center"> <?php echo lang("visit_purpose"); ?> </div>
                       </div>
                       <div class="panel-body">
                          <div id="grafico_feedback_visit_purpose" style="height: 240px;"></div>
                       </div>
                    </div>
                 </div>
               
               
            </div>
        </div>
        <!-- FIN SECCIÓN FEEDBACK -->
        
    </div>
    
    <?php } else { ?>
    
        <div class="panel panel-default mb15">
            <div class="panel-body">              
                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                    <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('communities_matrix_not_enabled'); ?>
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

<style>

#tabla_estado_tramitacion_length, #tabla_estado_actividades_length, #tabla_estado_financiero_length{
	display: none;
}

</style>

<script type="text/javascript">

	$(document).ready(function(){
				
		//INICIO SECCIÓN STAKEHOLDERS	
		<?php if(!empty(array_filter($stakeholders_categories))){ ?>
		
			$('#grafico_categorias_totales_sh').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("communities").'_'.clean(lang("total_stakeholders_categories")).'_'.date("Y-m-d"); ?>
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
				//colors: ['#398439', '#ac2925', '#d58512'],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($stakeholders_categories as $sc) { ?>
						{
							name: '<?php echo lang($sc->nombre); ?>',
							y: <?php echo $sc->cant_tipo_org_stakeholder; ?>
						},
					<?php } ?>
					
					]
				}]
			});
		
		<?php }else{ ?>
		
			$('#grafico_categorias_totales_sh').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		
		<?php } ?>
		//FIN SECCIÓN STAKEHOLDERS
		
		//INICIO SECCIÓN ACUERDOS
		<?php //if(!empty(array_filter($total_agreements_by_estado_tramitacion_graphic))){ ?>
		<?php if(!empty(array_filter($estados_tramitacion))){ ?>
			$('#grafico_estado_tramitacion').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("communities").'_'.clean(lang("processing_status")).'_'.date("Y-m-d"); ?>
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
				//colors: ['#398439', '#ac2925', '#d58512'],
				colors: [
				<?php 
					foreach($estados_tramitacion as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
				],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($estados_tramitacion as $estado) { ?>
						{
							name: '<?php echo $estado["nombre_estado"]; ?>',
							y: <?php echo $estado["cantidad"]; ?>
						},
					<?php } ?>
					
					]
				}]
			});
		
		<?php }else{ ?>
		
			$('#grafico_estado_tramitacion').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		
		<?php } ?>

		/*
		$("#tabla_estado_tramitacion").appTable({
            source: '<?php echo_uri("communities_agreements_summary/list_data_tabla_estado_tramitacion") ?>',
            columns: [
                {title: "<?php echo lang("agreement") ?>", "class": ""},
				{title: "<?php echo lang("status_name") ?>", "class": ""},
				{title: "<?php echo lang("quantity") ?>", "class": ""},
            ],
			columnShowHideOption: false,
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		*/
		
		/*
		$('#tabla_estado_tramitacion').dataTable({
			lengthMenu: [[10, 30, 50, 100, -1], [10, 30, 50, 100, "Todos"]],
			pagingType: "full_numbers",
			stateSave: true,// Permite guardar el estado de la tabla despues de recargar la pag
			scrollY: "400px",
			scrollX: true,
			processing: true,
			//serverSide: true,
			//ajax: "./ajax/usuarios_get.php",
				language: {
					processing:"Cargando...",
					search: "Buscar:",
					lengthMenu: "Mostrar _MENU_ registros por página.",
					zeroRecords: "Sin Registros",
					info: "Mostrando página _PAGE_ de _PAGES_",
					infoEmpty: "Sin registros disponibles",
					infoFiltered: "(Búsqueda de un total de _MAX_ registros)",
					paginate: {
					first: "Primera",
					previous: "Anterior",
					next: "Siguiente",
					last: "Última"
				}
			}
			
		});
		*/
		
		<?php //if(!empty(array_filter($total_agreements_by_estado_actividades_graphic))){ ?>
		<?php if(!empty(array_filter($estados_cumplimiento_actividades))){ ?>
			$('#grafico_estado_actividades').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("communities").'_'.clean(lang("activities_status")).'_'.date("Y-m-d"); ?>
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
				//colors: ['#398439', '#ac2925', '#d58512'],
				colors: [
				<?php 
					foreach($estados_cumplimiento_actividades as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
				],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($estados_cumplimiento_actividades as $estado) { ?>
						{
							name: '<?php echo $estado["nombre_estado"]; ?>',
							y: <?php echo $estado["cantidad"]; ?>
						},
					<?php } ?>
					
					]
				}]
			});
		
		<?php }else{ ?>
		
			$('#grafico_estado_actividades').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		
		<?php } ?>
		
		/*
		$("#tabla_estado_actividades").appTable({
            source: '<?php echo_uri("communities_agreements_summary/list_data_tabla_estado_actividades") ?>',
            columns: [
                {title: "<?php echo lang("agreement") ?>", "class": ""},
				{title: "<?php echo lang("status_name") ?>", "class": ""},
				{title: "<?php echo lang("quantity") ?>", "class": ""},
            ],
			columnShowHideOption: false,
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		*/
		
		<?php //if(!empty(array_filter($total_agreements_by_estado_financiero_graphic))){ ?>
		<?php if(!empty(array_filter($estados_cumplimiento_financiero))){ ?>
		
			$('#grafico_estado_financiero').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("communities").'_'.clean(lang("financial_status")).'_'.date("Y-m-d"); ?>
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
				//colors: ['#398439', '#ac2925', '#d58512'],
				colors: [
				<?php 
					foreach($estados_cumplimiento_financiero as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
				],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($estados_cumplimiento_financiero as $estado) { ?>
						{
							name: '<?php echo $estado["nombre_estado"]; ?>',
							y: <?php echo $estado["cantidad"]; ?>
						},
					<?php } ?>
					
					]
				}]
			});
		
		<?php }else{ ?>
		
			$('#grafico_estado_financiero').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		
		<?php } ?>
		
		/*
		$("#tabla_estado_financiero").appTable({
            source: '<?php echo_uri("communities_agreements_summary/list_data_tabla_estado_financiero") ?>',
            columns: [
                {title: "<?php echo lang("agreement") ?>", "class": ""},
				{title: "<?php echo lang("status_name") ?>", "class": ""},
				{title: "<?php echo lang("quantity") ?>", "class": ""},
            ],
			columnShowHideOption: false,
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		*/
		
		//Igualar el alto de los divs que contienen los gráficos y tablas
		/*
		var maxHeight_uf = Math.max.apply(null, $("#page-content > div:nth-child(2) div.huella").map(function (){
			return $(this).find("div.b-b").height();
			//return $(this).height();
		}).get());
		$("div.huella > div:nth-child(3)").height(maxHeight_uf);
		*/
		/*
		var maxHeight = Math.max.apply(null, $("#page-content > div:nth-child(4) > div.panel-body > div").map(function (){
			//return $(this).find("div").height();
			alert($(this).height());
			return $(this).height();
			
		}).get());
		*/
		
		//#page-content > div:nth-child(4) > div.panel-body > div:nth-child(1) > div
		//#page-content > div:nth-child(4) > div.panel-body > div:nth-child(2) > div
		//#page-content > div:nth-child(4) > div.panel-body > div:nth-child(3) > div
		
		//$('#page-content > div:nth-child(4) > div.panel-body > div:nth-child(1) > div').css('display', 'none');
		
		
		//LIST DATA CONSOLIDADO ACUERDOS
		$("#tabla_acuerdos_consolidado").appTable({
            source: '<?php echo_uri("communities_agreements_summary/list_data_consolidated_agreements_evaluations/".$id_agreement_matrix_config) ?>',
			filterDropdown: [
				{name: "estado_financiero", class: "w200", options: <?php echo $estados_financieros_dropdown; ?>},
				{name: "estado_actividades", class: "w200", options: <?php echo $estados_actividades_dropdown; ?>},
				{name: "estado_tramitacion", class: "w200", options: <?php echo $estados_tramitacion_dropdown; ?>}
			],
            columns: [
                {title: "<?php echo lang("agreement") ?>", "class": "text-left dt-head-center w100"},
				{title: "<?php echo lang("stakeholder") ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("processing_status") ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("activities_status") ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("financial_status") ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("observations") ?>", "class": "text-center dt-head-center"},
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//columnShowHideOption: false,
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
		
		//FIN SECCIÓN ACUERDOS
		
		//INICIO SECCIÓN FEEDBACK
		<?php if(!empty(array_filter($number_of_visits_by_visit_purpose))){ ?>
		
			$('#grafico_feedback_visit_purpose').highcharts({
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
						return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
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
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
					<?php $filename = $sigla_cliente.'_'.$sigla_proyecto.'_'.lang("communities").'_'.clean(lang("visit_purpose")).'_'.date("Y-m-d"); ?>
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
				//colors: ['#398439', '#ac2925', '#d58512'],
				series: [{
					name: 'Porcentaje',
					colorByPoint: true,
					data: [
					<?php foreach($number_of_visits_by_visit_purpose as $nv) { ?>
						{
							name: '<?php echo lang($nv->proposito_visita); ?>',
							y: <?php echo $nv->numero_visitas; ?>
						},
					<?php } ?>
					
					]
				}]
			});
		
		<?php }else{ ?>
		
			$('#grafico_feedback_visit_purpose').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		
		<?php } ?>

		// FIN SECCIÓN FEEDBACK
		
		$('#excel_agreements').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("communities_agreements_summary/get_excel_agreements")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});
		
		$("#communities_summary_pdf").on('click', function(e) {
		
			appLoader.show();
			
			var decimal_numbers = '<?php echo $general_settings->decimal_numbers; ?>';
			var decimals_separator = '<?php echo ($general_settings->decimals_separator == 1) ? "." : ","; ?>';
			var thousands_separator = '<?php echo ($general_settings->thousands_separator == 1)? "." : ","; ?>';
			
			// Gráfico Totales Categorías Stakeholders
			if($('#grafico_categorias_totales_sh').highcharts()){
				$('#grafico_categorias_totales_sh').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_categorias_totales_sh').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_categorias_totales_sh').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_categorias_totales_sh').highcharts().options.plotOptions.pie.size = 150;
				
				var chart = $('#grafico_categorias_totales_sh').highcharts().options.chart;
				var title = $('#grafico_categorias_totales_sh').highcharts().options.title;
				var series = $('#grafico_categorias_totales_sh').highcharts().options.series;
				var plotOptions = $('#grafico_categorias_totales_sh').highcharts().options.plotOptions;
				var colors = $('#grafico_categorias_totales_sh').highcharts().options.colors;
				var exporting = $('#grafico_categorias_totales_sh').highcharts().options.exporting;
				var credits = $('#grafico_categorias_totales_sh').highcharts().options.credits;
	
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
				
				//var image_categorias_totales_sh = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var image_categorias_totales_sh = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_categorias_totales_sh').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_categorias_totales_sh').highcharts().options.plotOptions.pie.size = null;
			}
			
			
			// Gráfico Estado Tramitación
			if($('#grafico_estado_tramitacion').highcharts()){
				$('#grafico_estado_tramitacion').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_estado_tramitacion').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
				$('#grafico_estado_tramitacion').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_estado_tramitacion').highcharts().options.plotOptions.pie.size = 150;
				$('#grafico_estado_tramitacion').highcharts().options.legend.itemStyle.fontSize = "20px";
				$('#grafico_estado_tramitacion').highcharts().options.title.text = '<?php echo lang("processing_status"); ?>';
				$('#grafico_estado_tramitacion').highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#grafico_estado_tramitacion').highcharts().options.chart;
				var series = $('#grafico_estado_tramitacion').highcharts().options.series;
				var title = $('#grafico_estado_tramitacion').highcharts().options.title;
				var plotOptions = $('#grafico_estado_tramitacion').highcharts().options.plotOptions;
				var colors = $('#grafico_estado_tramitacion').highcharts().options.colors;
				var exporting = $('#grafico_estado_tramitacion').highcharts().options.exporting;
				var credits = $('#grafico_estado_tramitacion').highcharts().options.credits;
				var legend = $('#grafico_estado_tramitacion').highcharts().options.legend;
	
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
				
				//var image_estado_tramitacion = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var image_estado_tramitacion = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_estado_tramitacion').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_estado_tramitacion').highcharts().options.plotOptions.pie.size = null;
				$('#grafico_estado_tramitacion').highcharts().options.legend.itemStyle.fontSize = "9px;";
			}
			
			
			// Gráfico Estado Actividades
			if($('#grafico_estado_actividades').highcharts()){
				$('#grafico_estado_actividades').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_estado_actividades').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
				$('#grafico_estado_actividades').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_estado_actividades').highcharts().options.plotOptions.pie.size = 150;
				$('#grafico_estado_actividades').highcharts().options.legend.itemStyle.fontSize = "20px";
				$('#grafico_estado_actividades').highcharts().options.title.text = '<?php echo lang("activities_status"); ?>';
				$('#grafico_estado_actividades').highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#grafico_estado_actividades').highcharts().options.chart;
				var series = $('#grafico_estado_actividades').highcharts().options.series;
				var title = $('#grafico_estado_actividades').highcharts().options.title;
				var plotOptions = $('#grafico_estado_actividades').highcharts().options.plotOptions;
				var colors = $('#grafico_estado_actividades').highcharts().options.colors;
				var exporting = $('#grafico_estado_actividades').highcharts().options.exporting;
				var credits = $('#grafico_estado_actividades').highcharts().options.credits;
				var legend = $('#grafico_estado_actividades').highcharts().options.legend;
	
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
				
				//var image_estado_actividades = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var image_estado_actividades = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_estado_actividades').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_estado_actividades').highcharts().options.plotOptions.pie.size = null;
				$('#grafico_estado_actividades').highcharts().options.legend.itemStyle.fontSize = "9px;";
			}
			
			
			
			// Gráfico Estado Financiero
			if($('#grafico_estado_financiero').highcharts()){
				$('#grafico_estado_financiero').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_estado_financiero').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
				$('#grafico_estado_financiero').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_estado_financiero').highcharts().options.plotOptions.pie.size = 150;
				$('#grafico_estado_financiero').highcharts().options.legend.itemStyle.fontSize = "20px";
				$('#grafico_estado_financiero').highcharts().options.title.text = '<?php echo lang("financial_status"); ?>';
				$('#grafico_estado_financiero').highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#grafico_estado_financiero').highcharts().options.chart;
				var series = $('#grafico_estado_financiero').highcharts().options.series;
				var title = $('#grafico_estado_financiero').highcharts().options.title;
				var plotOptions = $('#grafico_estado_financiero').highcharts().options.plotOptions;
				var colors = $('#grafico_estado_financiero').highcharts().options.colors;
				var exporting = $('#grafico_estado_financiero').highcharts().options.exporting;
				var credits = $('#grafico_estado_financiero').highcharts().options.credits;
				var legend = $('#grafico_estado_financiero').highcharts().options.legend;
	
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
				
				//var image_estado_financiero = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var image_estado_financiero = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_estado_financiero').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_estado_financiero').highcharts().options.plotOptions.pie.size = null;
				$('#grafico_estado_financiero').highcharts().options.legend.itemStyle.fontSize = "9px;";
			}
			
			
			// Gráfico Propósito Visita
			if($('#grafico_feedback_visit_purpose').highcharts()){
				$('#grafico_feedback_visit_purpose').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#grafico_feedback_visit_purpose').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "12px";
				$('#grafico_feedback_visit_purpose').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#grafico_feedback_visit_purpose').highcharts().options.plotOptions.pie.size = 150;
				
				var chart = $('#grafico_feedback_visit_purpose').highcharts().options.chart;
				var title = $('#grafico_feedback_visit_purpose').highcharts().options.title;
				var series = $('#grafico_feedback_visit_purpose').highcharts().options.series;
				var plotOptions = $('#grafico_feedback_visit_purpose').highcharts().options.plotOptions;
				var colors = $('#grafico_feedback_visit_purpose').highcharts().options.colors;
				var exporting = $('#grafico_feedback_visit_purpose').highcharts().options.exporting;
				var credits = $('#grafico_feedback_visit_purpose').highcharts().options.credits;
	
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
				
				//var image_feedback_visit_purpose = 'http://export.highcharts.com/'+getChartName(obj)+'.png';
				var image_feedback_visit_purpose = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#grafico_feedback_visit_purpose').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#grafico_feedback_visit_purpose').highcharts().options.plotOptions.pie.size = null;
			}
			
			
			var imagenes_graficos = {
				image_categorias_totales_sh:image_categorias_totales_sh,
				image_estado_tramitacion: image_estado_tramitacion,
				image_estado_actividades:image_estado_actividades,
				image_estado_financiero:image_estado_financiero,
				image_feedback_visit_purpose:image_feedback_visit_purpose
			};
			
			$.ajax({
				url:  '<?php echo_uri("communities_agreements_summary/get_pdf") ?>',
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
				url:  '<?php echo_uri("communities_agreements_summary/borrar_temporal") ?>',
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
				//url :'http://export.highcharts.com/',
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