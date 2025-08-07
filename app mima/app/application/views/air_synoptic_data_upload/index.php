<div id="page-content" class="p20 clearfix">

 <!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("customer_administrator_air"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("synoptic_data_upload"); ?></a>
    </nav>

<?php if($puede_ver != 3) { ?>

	<div class="row">
    	<div class="col-md-12">
        
        	<div class="page-title clearfix">
            	<h1><i class="fa fa-table" title="Abierto"></i> <?php echo lang("synoptic_data"); ?></h1>
            </div>
            
            <div class="row" style="background-color:#E5E9EC;">
                <div class="col-md-4">
                	<div class="row">
                    	<div class="col-md-12 col-sm-12">
                        <div class="panel">
                        <div class="panel-heading panel-sky p30"></div>
                        <div class="clearfix text-center">
                        <span class="mt-50 avatar avatar-md chart-circle">
                        <img src="<?php echo get_file_uri("assets/images/icons/cloud.png"); ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded shadow-2">
                        </span>
                        </div>
                        <div class="p10 b-t b-b"><?php echo lang("number_of_records") . ': ' ?> <span id="num_registros"> <?php echo $num_registros; ?> </span> </div>
                        <div class="p10 b-b"><?php echo lang("modified_date") . ': ' ?> <span id="fecha_modificacion"> <?php echo ($fecha_modificacion) ? $fecha_modificacion : "-"; ?> </span> </div>
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
                        	<?php echo lang("synoptic_data_upload_desc"); ?>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="page-title clearfix panel-sky">
                    <h1><?php echo lang("synoptic_data"); ?></h1>
                    <div class="title-button-group">
						<div class="btn-group" role="group">
                            <?php 
                                if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
                                    echo '<span style="cursor: not-allowed;">'.js_anchor("<i class='fa fa-trash'></i> ".lang("delete_selected"), array('title' => lang('delete_air_synoptic_data_upload'), "id" => "delete_selected_rows", "class" => "delete btn btn-danger", "data-action" => "delete-confirmation", "data-custom" => true, "disabled" => "disabled", "style" => "pointer-events: none;")).'</span>';
                                } 
                            ?>
                            <button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
						</div> 
                        <?php //echo modal_anchor(get_uri("air_synoptic_data_upload/modal_form/"), "<i class='fa fa-plus-circle'></i> " . lang('add_synoptic_data'), array("id" => "agregar_elemento", "class" => "btn btn-default", "title" => lang('add_synoptic_data'))); ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="air_synoptic_data_upload-table" class="display" cellspacing="0" width="100%"> 
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
		
            $("#air_synoptic_data_upload-table").appTable({
                source: '<?php echo_uri("air_synoptic_data_upload/list_data") ?>',
                /*filterDropdown: [
                    {name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
                    {name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
                ],*/
                columns: [
                    <?php if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno ?>
                        
                        {
                            checkboxes: {
                                selectRow: true,
                                selectCallback: function(){
                                    get_selected_rows();
                                }
                            },
                            select: {
                                style: 'multi'
                            },
                            render: function(data, type, row, meta){ 

                                data = "";
                                if(row[2] == 1){ // 1 = Perfil Eliminar Todos
                                    data = '<input type="checkbox" class="dt-checkboxes">'
                                }
                                if(row[2] == 2){ // 2 = Perfil Eliminar Propios
                                    if(row[1] == <?php echo $this->session->user_id; ?>){
                                        data = '<input type="checkbox" class="dt-checkboxes">'
                                    } else {
                                        data = '<input type="checkbox" class="dt-checkboxes" disabled>'
                                    }
                                }
                                if(row[2] == 3){ // 3 = Perfil Eliminar Ninguno
                                    data = '<input type="checkbox" class="dt-checkboxes" disabled>'
                                }
                                
                                return data;
                            
                            },
                            createdCell:  function (td, cellData, rowData, row, col){

                                if(rowData[2] == 2){ // 2 = Perfil Eliminar Propios
                                    if(rowData[1] != <?php echo $this->session->user_id; ?>){
                                        this.api().cell(td).checkboxes.disable();
                                    }
                                }
                                if(rowData[2] == 3){ // 3 = Perfil Eliminar Ninguno
                                    this.api().cell(td).checkboxes.disable();
                                }
                                
                                this.api().cell(td).checkboxes.deselect();

                            }
                            
                        },
                    
                    <?php } ?>
                    {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center hide"},
                    {title: "<?php echo lang("created_by"); ?>", "class": "text-center hide"},
                    {title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center no_breakline"},
                    {title: "<?php echo lang("pmca_24_hrs_t1"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_24_hrs_t2"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_24_hrs_t3"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_48_hrs_t1"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_48_hrs_t2"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_48_hrs_t3"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_72_hrs_t1"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_72_hrs_t2"); ?>", "class": "text-right dt-head-center"},
                    {title: "<?php echo lang("pmca_72_hrs_t3"); ?>", "class": "text-right dt-head-center"},
                    //{title: "<?php echo lang("backup_document"); ?>", "class": "text-center dt-head-center option"},
                    //{title: "<?php echo lang("observations"); ?>", "class": "text-center dt-head-center"},
                    {title: "<?php echo lang("created_date"); ?>", "class": "text-left dt-head-center no_breakline"},
                    //{title: "<?php echo lang("modified_date"); ?>", "class": "text-left dt-head-center no_breakline"},
                    {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150 no_breakline"}
                ],
                rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $(nRow).find('[data-toggle="tooltip"]').tooltip();
                }
                //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
                //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
            });



            $(document).on('click', '.table_delete a.delete', function() {
				$(this).each(function () {
					$.each(this.attributes, function () {
						if (this.specified && this.name.match("^data-")) {
							$("#confirmFileDeleteButton").attr(this.name, this.value);
						}
	
					});
				});
				$("#confirmationFileModal").modal('show');
			});

            $(document).on('click', '#confirmDeleteButton', function() {
				
				appLoader.show();
				
				var url = $(this).attr('data-action-url');
				var id = $(this).attr('data-id');
				
				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: {id: id},
					success: function (result) {
						if (result.success) {
							appAlert.warning(result.message, {duration: 20000});
							$('#fecha_modificacion').text(result.fecha_modificacion);
							$('#num_registros').text(result.num_registros);
							
							var tr = $('a.delete[data-id="'+id+'"]').closest('tr'),
							table = $("#air_synoptic_data_upload-table").dataTable();
	
							table.fnDeleteRow($("#air_synoptic_data_upload-table").DataTable().row(tr).index());
							//table.fnReloadAjax();
						} else {
							appAlert.error(result.message, {duration: 20000});
						}
						appLoader.hide();
					}
				});
	
			});
            
			
            //$('#confirmationModal').on('click', '#confirmDeleteButton', function() {
            //$('#confirmDeleteButton').click(function() {
            $(document).off('click', '#confirmFileDeleteButton').on('click', '#confirmFileDeleteButton', function() {
                
                appLoader.show();
                
                var url = $(this).attr('data-action-url'),
                        id = $(this).attr('data-id'),
                        file_type = $(this).attr('data-file_type');
                        
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id, 
                        file_type: file_type,
                    },
                    success: function (result) {
                        if (result.success) {
                            appAlert.warning(result.message, {duration: 20000});
                            $('#table_delete_' + result.file_type).parent().parent().html(result.new_field);
                            $('#air_synoptic_data_upload-form').append('<input type="hidden" name="file_type_to_delete[]" value="' + result.file_type + '" />');
                        } else {
                            appAlert.error(result.message);
                        }
                        appLoader.hide();
                    }
                });
                        
            });
			
			

			$('#excel').click(function(){
				var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("air_synoptic_data_upload/get_excel"); ?>').attr('method','POST').attr('target', '_self').appendTo('body');
				$form.submit();
			});
			
            <?php if($puede_agregar != 1) { ?>
                $('#agregar_elemento').removeAttr("data-action-url").attr('disabled','true');
            <?php } ?>
            
            <?php if($puede_ver == 3) { ?>
                $('#excel').attr('disabled','true');
            <?php } ?>
		
		
			// SELECCIÓN MÚLTIPLE DE FILAS DE APPTABLE
			var data_ids = [];
			function get_selected_rows(){
				
				var rows_selected = $("#air_synoptic_data_upload-table").DataTable().column(0).checkboxes.selected();
				var ids = rows_selected.join(",");
				data_ids = ids.split(",");
								
				if(data_ids[0] !== ""){
					$('#delete_selected_rows').attr("disabled", false).css("pointer-events", "auto");
				} else {
					$('#delete_selected_rows').attr("disabled", "disabled").css("pointer-events", "none");
				}
	
			};

            $(document).on('click', '#delete_selected_rows', function() {
				$("#confirmMultipleDeleteButton").attr("data-ids", JSON.stringify(data_ids));
				$("#confirmMultipleDeleteButton").attr("data-action-url", "<?php echo get_uri("air_synoptic_data_upload/delete_multiple/"); ?>");
				$('#confirmationMultipleModal').modal('show');
			});
			
			$(document).on('click', '#confirmMultipleDeleteButton', function() {
				
				var url = $(this).attr('data-action-url');
				var data_ids = $(this).attr('data-ids');
	
				appLoader.show();
				
				$.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: {data_ids: data_ids},
					success: function (result) {
						if (result.success) {
							appAlert.warning(result.message, {duration: 20000});
							$('#fecha_modificacion').text(result.fecha_modificacion);
							$('#num_registros').text(result.num_registros);
							
							$.each( JSON.parse(data_ids), function( index, id ){
								var tr = $('a.delete[data-id="'+id+'"]').closest('tr'),
								table = $("#air_synoptic_data_upload-table").dataTable();
								table.fnDeleteRow($("#air_synoptic_data_upload-table").DataTable().row(tr).index());
							});
							
							$('#delete_selected_rows').attr("disabled", "disabled").css("pointer-events", "none");
							
						} else {
							appAlert.error(result.message, {duration: 20000});
						}
						appLoader.hide();
					}
				});
				
			});
			
        });  
		 
    </script>
</div>