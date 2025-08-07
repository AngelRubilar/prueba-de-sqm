<?php if($view_1min || $view_5min || $view_15min || $view_1hour){ ?>
	
	<ul class="nav nav-tabs" role="tablist">
		<?php if($id_station == 5 || $id_station == 6 || $id_station == 7 || $id_station == 11){ ?>
			
			<li class="<?php if($view_1min){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_1_min"><?php echo lang("records_per_minute"); ?></a>
			</li>

			<li class="<?php if(!$view_1min && $view_5min){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_5_min"><?php echo lang("records_per_5_minutes"); ?></a>
			</li>

			<li class="<?php if(!$view_1min && !$view_5min && $view_15min){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_15_min"><?php echo lang("records_per_15_minutes"); ?></a>
			</li>

			<li class="<?php if(!$view_1min && !$view_5min && !$view_15min && $view_1hour){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_1_hour"><?php echo lang("records_per_1_hour"); ?></a>
			</li>
		<?php } else { ?>
			<!-- 
			<li class="<?php if($view_1min){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_1_min"><?php echo lang("records_per_minute"); ?></a> 
			</li>
			-->
			<li class="<?php if(!$view_1min && $view_5min){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_5_min"><?php echo lang("records_per_5_minutes"); ?></a>
			</li>

			<li class="<?php if(!$view_1min && !$view_5min && $view_15min){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_15_min"><?php echo lang("records_per_15_minutes"); ?></a>
			</li>

			<li class="<?php if(!$view_1min && !$view_5min && !$view_15min && $view_1hour){ ?> active <?php } ?>">
				<a data-toggle="tab" href="#tab_1_hour"><?php echo lang("records_per_1_hour"); ?></a>
			</li>
		<?php } ?>
	</ul>

	<div role="tabpanel" class="tab-pane fade active in" id="" style="min-height: 200px;">
		<div class="tab-content">
			
			<div id="tab_1_min" class="tab-pane fade in <?php if($view_1min){ ?> active <?php } ?>">
				<div class="col-md-12 p0">
					<div class="panel panel-default mb15">
						
						<div class="page-title clearfix">
							<h4><?php echo lang("records_per_minute"); ?></h4>
							
							<div class="btn-group pull-right" role="group">
								<button type="button" class="btn btn-success" id="export_1min"><i class='fa fa-download'></i> <?php echo lang('export_csv'); ?></button>>
								<button type="button" class="btn btn-success" id="export_excel_1min"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
							</div>
						</div>
						
						<div class="panel-body">
							
							<?php if($view_1min){ ?>
								<div class="table-responsive">
									<table id="table_1min" class="display" cellspacing="0" width="100%"></table>
								</div>
							<?php }else{ ?>
								<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12">
									<?php echo lang("time_range_exceeded_1min"); ?>
								</div>
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>


			<div id="tab_5_min" class="tab-pane fade in <?php if(!$view_1min && $view_5min){ ?> active <?php } ?>">
				<div class="col-md-12 p0">
					<div class="panel panel-default mb15">
						
						<div class="page-title clearfix">
							<h4><?php echo lang("records_per_5_minutes"); ?></h4>
							
							<div class="btn-group pull-right" role="group">
								<button type="button" class="btn btn-success" id="export_5min"><i class='fa fa-download'></i> <?php echo lang('export_csv'); ?></button>
								<button type="button" class="btn btn-success" id="export_excel_5min"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
							</div>
						</div>

						<div class="panel-body">
						
							<?php if($view_5min){ ?>
								<div class="table-responsive">
									<table id="table_5min" class="display" cellspacing="0" width="100%"></table>
								</div>
							<?php }else{ ?>
								<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12">
									<?php echo lang("time_range_exceeded_5min"); ?>
								</div>
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>


			<div id="tab_15_min" class="tab-pane fade in <?php if(!$view_1min && !$view_5min && $view_15min){ ?> active <?php } ?>">
				<div class="col-md-12 p0">
					<div class="panel panel-default mb15">
						
						<div class="page-title clearfix">
							<h4><?php echo lang("records_per_15_minutes"); ?></h4>
							
							<div class="btn-group pull-right" role="group">
								<button type="button" class="btn btn-success" id="export_15min"><i class='fa fa-download'></i> <?php echo lang('export_csv'); ?></button>
								<button type="button" class="btn btn-success" id="export_excel_15min"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
							</div>
						</div>
						
						<div class="panel-body">
						
							<?php if($view_15min){ ?>
							<div class="table-responsive">
								<table id="table_15min" class="display" cellspacing="0" width="100%"></table>
							</div>
							<?php }else{ ?>
							<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12">
								<?php echo lang("time_range_exceeded_15min"); ?>
							</div>
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>


			<div id="tab_1_hour" class="tab-pane fade in <?php if(!$view_1min && !$view_5min && !$view_15min && $view_1hour){ ?> active <?php } ?>">
				<div class="col-md-12 p0">
					<div class="panel panel-default mb15">
						
						<div class="page-title clearfix">
							<h4><?php echo lang("records_per_1_hour"); ?></h4>
							
							<div class="btn-group pull-right" role="group">
								<button type="button" class="btn btn-success" id="export_1hour"><i class='fa fa-download'></i> <?php echo lang('export_csv'); ?></button>
								<button type="button" class="btn btn-success" id="export_excel_1hour"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
							</div>
						</div>
						
						<div class="panel-body">
						
							<?php if($view_1hour){ ?>
							<div class="table-responsive">
								<table id="table_1hour" class="display" cellspacing="0" width="100%"></table>
							</div>
							<?php }else{ ?>
							<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12">
								<?php echo lang("time_range_exceeded_1hour"); ?>
							</div>
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>

			
		</div>
	</div>

<?php }else{ ?>

	<div class="panel panel-default mb0">
		<div class="panel-body">              
			<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-3">
				<?php echo lang("time_range_exceeded"); ?>
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

		<?php if($view_1min){ ?>

			$("#table_1min").appTable({
				source: '<?php echo_uri("air_monitoring_data/list_data_minutes/"); ?>',
				filterParams: {
					station: "<?php echo $id_station; ?>", 
					start_date: "<?php echo $start_date; ?>", 
					end_date: "<?php echo $end_date; ?>",
					days: <?php echo json_encode($days); ?>,
					hours: <?php echo json_encode($hours); ?>,
				},
				columns: [
					{data: "id_dato", title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{data: "date", visible: false, searchable: false},
					{data: "date", title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center sorting_asc",
						render: function (data, type, row) {
								var fecha = moment(data, 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat));
								return fecha;
						},
						type: "extract-date"
					},
					{data: "hour", title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{data: "minute", title: "<?php echo lang("minute"); ?>", "class": "text-left dt-head-center"},

					<?php foreach($variables as $id_variable => $variable) { ?>

						{data: "<?php echo $variable; ?>", title: "<?php echo $variable.' ('.$variables_unidad[$id_variable].')'; ?>", "class": "text-left dt-head-center",
							render: function (data, type, row) {
								if(data != '-' && data != 'null'){
									var value = numberFormat(data, decimal_numbers, decimals_separator, thousands_separator);
								}else{
									var value = '-';
								}

								return value;
							}
						},
					<?php } ?>
					
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				order: [[0, "asc"],[1, "asc"],[2, "asc"]],
				lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
				/*columnDefs: [ {
					targets: [ 4 ],
					orderData: [ 4, 5 ]
				}, {
					targets: [ 5 ],
					orderData: [ 5, 4 ]
				}]*/
			});

			// BOTÓN EXPORTAR CSV
			$('#export_1min').click(function(){
				var $form = $('<form id="csv2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_csv/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='1min'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						//console.log(result);
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});

			// BOTÓN EXPORTAR EXCEL
			$('#export_excel_1min').click(function(){
				var $form = $('<form id="excel2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_excel/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='1min'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});
		<?php } ?>


		<?php if($view_5min){ ?>
			$("#table_5min").appTable({
				source: '<?php echo_uri("air_monitoring_data/list_data_5_min/"); ?>',
				filterParams: {
					station: "<?php echo $id_station; ?>", 
					start_date: "<?php echo $start_date; ?>", 
					end_date: "<?php echo $end_date; ?>",
					days: <?php echo json_encode($days); ?>,
					hours: <?php echo json_encode($hours); ?>,
				},
				columns: [
					{data: "id_dato", title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{data: "date", visible: false, searchable: false},
					{data: "date", title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center sorting_asc",
						render: function (data, type, row) {
								var fecha = moment(data, 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat));
								return fecha;
						},
						type: "extract-date"
					},
					{data: "hour", title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{data: "minute", title: "<?php echo lang("minute"); ?>", "class": "text-left dt-head-center"},

					<?php foreach($variables as $id_variable => $variable) { ?>

						{data: "<?php echo $variable; ?>", title: "<?php echo $variable.' ('.$variables_unidad[$id_variable].')'; ?>", "class": "text-left dt-head-center",
							render: function (data, type, row) {
								if(data != '-' && data != 'null'){
									var value = numberFormat(data, decimal_numbers, decimals_separator, thousands_separator);
								}else{
									var value = '-';
								}

								return value;
							}
						},
					<?php } ?>
					
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				order: [[0, "asc"],[1, "asc"],[2, "asc"]],
				lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
				/*columnDefs: [ {
					targets: [ 4 ],
					orderData: [ 4, 5 ]
				}, {
					targets: [ 5 ],
					orderData: [ 5, 4 ]
				}]*/
			});

			// BOTÓN EXPORTAR CSV
			$('#export_5min').click(function(){
				var $form = $('<form id="csv2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_csv/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='5min'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						//console.log(result);
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});

			// BOTÓN EXPORTAR EXCEL
			$('#export_excel_5min').click(function(){
				var $form = $('<form id="excel2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_excel/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='5min'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});
		<?php } ?>


		<?php if($view_15min){ ?>
			$("#table_15min").appTable({
				source: '<?php echo_uri("air_monitoring_data/list_data_15_min/"); ?>',
				filterParams: {
					station: "<?php echo $id_station; ?>", 
					start_date: "<?php echo $start_date; ?>", 
					end_date: "<?php echo $end_date; ?>",
					days: <?php echo json_encode($days); ?>,
					hours: <?php echo json_encode($hours); ?>,
				},
				/* filterDropdown: [{name: "just_audio", class: "w200", 
					options: [
						{'id':0, 'text':'- Todos -'},
						{'id':1, 'text':'Solo Audios'}
					]
				}], */
				/*checkBoxes: [
					{text: '<?php echo lang("audio") ?>', name: "recording_", value: "2", isChecked: false}
				],*/
				columns: [
					{data: "id_dato", title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{data: "date", visible: false, searchable: false},
					{data: "date", title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center sorting_asc",
						render: function (data, type, row) {
								var fecha = moment(data, 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat));
								return fecha;
						},
						type: "extract-date"
					},
					{data: "hour", title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{data: "minute", title: "<?php echo lang("minute"); ?>", "class": "text-left dt-head-center"},

					<?php foreach($variables as $id_variable => $variable) { ?>

						{data: "<?php echo $variable; ?>", title: "<?php echo $variable.' ('.$variables_unidad[$id_variable].')'; ?>", "class": "text-left dt-head-center",
							render: function (data, type, row) {
								if(data != '-' && data != 'null'){
									var value = numberFormat(data, decimal_numbers, decimals_separator, thousands_separator);
								}else{
									var value = '-';
								}

								return value;
							}
						},
					<?php } ?>
					
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				order: [[0, "asc"],[1, "asc"],[2, "asc"]],
				lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
				/*columnDefs: [ {
					targets: [ 4 ],
					orderData: [ 4, 5 ]
				}, {
					targets: [ 5 ],
					orderData: [ 5, 4 ]
				}]*/
			});

			// BOTÓN EXPORTAR CSV
			$('#export_15min').click(function(){
				var $form = $('<form id="csv2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_csv/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='15min'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						//console.log(result);
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});

			// BOTÓN EXPORTAR EXCEL
			$('#export_excel_15min').click(function(){
				var $form = $('<form id="excel2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_excel/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='15min'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});
		<?php } ?>


		<?php if($view_1hour){ ?>
			$("#table_1hour").appTable({
				source: '<?php echo_uri("air_monitoring_data/list_data_hour/"); ?>',
				filterParams: {
					station: "<?php echo $id_station; ?>", 
					start_date: "<?php echo $start_date; ?>", 
					end_date: "<?php echo $end_date; ?>",
					days: <?php echo json_encode($days); ?>,
					hours: <?php echo json_encode($hours); ?>,
				},
				columns: [
					{data: "id_dato", title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{data: "date", visible: false, searchable: false},
					{data: "date", title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center sorting_asc",
						render: function (data, type, row) {
								var fecha = moment(data, 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat));
								return fecha;
						},
						type: "extract-date"
					},
					{data: "hour", title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},

					<?php foreach($variables as $id_variable => $variable) { ?>

						{data: "<?php echo $variable; ?>", title: "<?php echo $variable.' ('.$variables_unidad[$id_variable].')'; ?>", "class": "text-left dt-head-center",
							render: function (data, type, row) {
								if(data != '-' && data != 'null'){
									var value = numberFormat(data, decimal_numbers, decimals_separator, thousands_separator);
								}else{
									var value = '-';
								}

								return value;
							}
						},
					<?php } ?>
					
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				order: [[0, "asc"],[1, "asc"]],
				lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
				/*columnDefs: [ {
					targets: [ 4 ],
					orderData: [ 4, 5 ]
				}, {
					targets: [ 5 ],
					orderData: [ 5, 4 ]
				}]*/
			});

			// BOTÓN EXPORTAR CSV
			$('#export_1hour').click(function(){
				var $form = $('<form id="csv2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_csv/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='1hour'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						//console.log(result);
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});

			// BOTÓN EXPORTAR EXCEL
			$('#export_excel_1hour').click(function(){
				var $form = $('<form id="excel2"></form>');
				$form.attr('action','<?php echo_uri("air_monitoring_data/export_custom_excel/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='time_range' value='1hour'></input>");
				$form.append("<input type='hidden' name='station' value='<?php echo $id_station; ?>'></input>");
				$form.append("<input type='hidden' name='start_date' value='<?php echo $start_date; ?>'></input>");
				$form.append("<input type='hidden' name='end_date' value='<?php echo $end_date; ?>'></input>");
				$form.append("<input type='hidden' name='days' value='<?php echo implode(',', $days); ?>'></input>");
				$form.append("<input type='hidden' name='hours' value='<?php echo implode(',', $hours); ?>'></input>");

				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					isModal: false,
					onAjaxSuccess: function (result) {
						
						appLoader.hide();
						
						var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
			});
		<?php } ?>

		function borrar_temporal(uri){
			$.ajax({
				url:  '<?php echo_uri("air_monitoring_data/borrar_temporal") ?>',
				type:  'post',
				data: {uri:uri},
				//dataType:'json',
				success: function(respuesta){
					appLoader.hide();
				}
			});
		}


			
	});
</script> 