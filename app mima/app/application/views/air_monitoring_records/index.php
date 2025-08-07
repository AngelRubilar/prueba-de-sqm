<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
	<nav class="breadcrumb">
	<a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
	<a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
	<a class="breadcrumb-item" href="#"><?php echo lang("air_quality_records"); ?> /</a>
	<a class="breadcrumb-item" href=""><?php echo lang("monitoring_records"); ?></a>
	</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('monitoring_records'); ?></h1>
            <div class="title-button-group">

            </div>
        </div>
        <div class="table-responsive">
            <table id="monitoring_records-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>


</div>

<script type="text/javascript">
    $(document).ready(function () {

		/*
		
		<?php if($id_stakeholder_matrix_config) { ?>
		
        $("#monitoring_records-table").appTable({
            source: '<?php echo_uri("communities_stakeholders/list_data/".$id_stakeholder_matrix_config); ?>',
			filterDropdown: [
				{name: "id_tipo_organizacion", class: "w200", options: <?php echo $tipos_organizaciones_dropdown; ?>}
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
				{title: "<?php echo lang("stakeholder"); ?>", "class": "text-left dt-head-center w100"},
				{title: "<?php echo lang("rut"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("type_of_organization"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("locality"); ?>", "class": "text-left dt-head-center"}
				<?php echo $columnas_campos; ?>,
				{title: "<?php echo lang("contact"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("contact_phone"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("contact_email"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("contact_address"); ?>", "class": "text-left dt-head-center"},	
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
		
		// SELECCIÓN MÚLTIPLE DE FILAS DE APPTABLE
		var data_ids = [];
		function get_selected_rows(){
			
			var rows_selected = $("#communities_stakeholders-table").DataTable().column(0).checkboxes.selected();
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
			$("#confirmMultipleDeleteButton").attr("data-action-url", "<?php echo get_uri("communities_stakeholders/delete_multiple/"); ?>");
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
							table = $("#communities_stakeholders-table").dataTable();
							table.fnDeleteRow($("#communities_stakeholders-table").DataTable().row(tr).index());
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

		*/
    });
</script>