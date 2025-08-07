<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("communities"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("feedback"); ?></a>
</nav>

<?php if($puede_ver != 3) { ?>

	<?php if($id_feedback_matrix_config) { ?>
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('feedback'); ?></h1>
            <div class="title-button-group">
            	<?php 
					if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
						echo '<span style="cursor: not-allowed;">'.js_anchor("<i class='fa fa-trash'></i> ".lang("delete_selected"), array('title' => lang('delete_feedbacks'), "id" => "delete_selected_rows", "class" => "delete btn btn-danger", "data-action" => "delete-confirmation", "data-custom" => true, "disabled" => "disabled", "style" => "pointer-events: none;")).'</span>';
					} 
				?>
            	<div class="btn-group" role="group">
                    <button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
                </div>
            
                <?php 
					if($puede_agregar == 1) {
						echo modal_anchor(get_uri("communities_feedback/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_feedback'), array("class" => "btn btn-default", "title" => lang('add_feedback'), "data-post-id_feedback_matrix_config" => $id_feedback_matrix_config)); 				
					} else {
						echo modal_anchor("", "<i class='fa fa-plus-circle'></i> " . lang('add_feedback'), array("class" => "btn btn-default", "title" => lang('add_feedback'), "data-post-id_feedback_matrix_config" => $id_feedback_matrix_config, "disabled" => "disabled")); 
					}
				?>
                
            </div>
        </div>
        <div class="table-responsive">
            <table id="communities_feedback-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
    <?php } else { ?>
        <div class="panel">
            <div class="panel-body">
            
                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                    <?php echo lang('the_project').' "'.$project_info->title.'" '.lang('feedback_matrix_not_enabled'); ?>
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
    $(document).ready(function () {
		
		<?php if($id_feedback_matrix_config) { ?>
		
			$("#communities_feedback-table").appTable({
				source: '<?php echo_uri("communities_feedback/list_data/".$id_feedback_matrix_config); ?>',
				filterDropdown: [
					{name: "responsable", class: "w200", options: <?php echo $responsables_dropdown; ?>},
					{name: "proposito_visita", class: "w200", options: <?php echo $propositos_visita_dropdown; ?>},
					{name: "id_tipo_organizacion", class: "w200", options: <?php echo $tipos_organizaciones_dropdown; ?>},
				],
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
							
					{title: "<?php echo lang("id"); ?>", "class": "text-center w50 hide"},
					{title: "<?php echo lang("created_by"); ?>", "class": "text-center w50 hide"},
					{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center w100"},
					{title: "<?php echo lang("type_of_stakeholder"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("visit_purpose"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("responsible"); ?>", "class": "text-left dt-head-center no_breakline"}
					<?php echo $columnas_campos; ?>,
					{title: "<?php echo lang("created_date"); ?>", "class": "text-left dt-head-center w100", type: "extract-date"},
					{title: "<?php echo lang("modified_date"); ?>", "class": "text-left dt-head-center w100", type: "extract-date"},
					{title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"}		
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				//printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
				//xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
			});
			
			$('#excel').click(function(){
				var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("communities_feedback/get_excel")?>').attr('method','POST').attr('target', '_self').appendTo('body');
				$form.submit();
			});
			
			
			// SELECCIÓN MÚLTIPLE DE FILAS DE APPTABLE
			var data_ids = [];
			function get_selected_rows(){
				
				var rows_selected = $("#communities_feedback-table").DataTable().column(0).checkboxes.selected();
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
				$("#confirmMultipleDeleteButton").attr("data-action-url", "<?php echo get_uri("communities_feedback/delete_multiple/"); ?>");
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
							$.each( JSON.parse(data_ids), function( index, id ){
								var tr = $('a.delete[data-id="'+id+'"]').closest('tr'),
								table = $("#communities_feedback-table").dataTable();
								table.fnDeleteRow($("#communities_feedback-table").DataTable().row(tr).index());
							});
							
							$('#delete_selected_rows').attr("disabled", "disabled").css("pointer-events", "none");
							
						} else {
							appAlert.error(result.message, {duration: 20000});
						}
						appLoader.hide();
					}
				});
				
			});
			
		<?php } ?>
    });
</script>