<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("waste"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("indicators"); ?></a>
    </nav>

    <div class="panel panel-default">
		<div class="page-title clearfix">
			<h1><?php echo lang('indicators'); ?></h1>
		</div>
    </div>
	
    <?php if($puede_ver != 3) { ?>
    
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" style="padding: 0px 0px 0px 0px">
                        <?php
                        if(isset($indicators_names)){
                            $tab_view['active_tabs'] = $indicators_names;
                            $this->load->view("waste/client/indicators/tabs", $tab_view);
                        }else{ 
                        ?>		
                            <div class="panel-body">              
                                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                                    <?php echo lang("the_project"). ' "'.$project_info->title.'" ' .lang('without_indicators'); ?>
                                </div>
                            </div>	  
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row" >
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body" id="tablepanel" style="padding: 0px 0px 0px 0px">
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
    
</div>
<script type="text/javascript">
$(document).ready(function () {
	
	var id_indicador;
	
	$("#tabs li").click(function() {
		id_indicador = $(this).data("id");
		
		//alert(id_indicador);
		
		var indicator_name = $(this).data("name");
		$.ajax({
			url:  '<?php echo_uri("client_indicators/get_load_table"); ?>',
			type:  'post',
			data: {id_indicador:id_indicador, indicator_name:indicator_name},
			success: function(respuesta){	
				$('#tablepanel').html(respuesta);
				$("#client_indicators-table").appTable({
					source: '<?php echo_uri("client_indicators/list_data/"); ?>'+id_indicador,
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
						{title: "<?php echo lang("value"); ?>", "class": "text-right dt-head-center w50"},
						{title: "<?php echo lang("since"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
						{title: "<?php echo lang("until"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
						{title: "<?php echo lang("created_date"); ?>", "class": "text-left dt-head-center w100", type: "extract-date"},
						{title: "<?php echo lang("modified_date"); ?>", "class": "text-left dt-head-center w100", type: "extract-date"},
						{title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
					]
				});
			}
		});
	});
	$("#tabs li").first().addClass("active");
	$("#tabs li").first().click();
	
	/*
	$(document).on("click","#excel", function() {
		var filename = $(this).data("filename");
		generar_excel("client_indicators-table", filename);
	});
	*/
	
	var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("export_excel/excel_indicadores")?>').attr('method','POST').attr('target', '_self').appendTo('body');
	
	$(document).on("click","#excel", function() {
		//alert(id_indicador);
		$('<input>').attr({type: 'hidden', id: 'id_indicador', name: 'id_indicador', value: id_indicador}).appendTo('#gg');
		$form.submit();
	});
	
	/*
	function generar_excel(nombre_tabla, filename){

		var myTableArray = [];
		var columns_number = document.getElementById(nombre_tabla).rows[1].cells.length - 1;
		var rows_number = document.getElementById(nombre_tabla).rows.length;

		$("table#" + nombre_tabla + " tr").each(function(index) {
			var arrayOfThisRow = [];
			if(index == 0){
				var tableData = $(this).find('th');
			}else{
				var tableData = $(this).find('td');
			}
			if(index == 5){
				return true;
			}
			if (tableData.length > 0) {
				tableData.each(function() {
					if($(this).find('span').length){
						arrayOfThisRow.push($(this).find('span').text());
						//filename = $(this).find('span').text();
					} else {
						arrayOfThisRow.push($(this).text());
					}
				});
				myTableArray.push(arrayOfThisRow);
			}
		});
		//console.log(myTableArray);
		var datos = {
			tabla:myTableArray, 
			columns_number:columns_number,
			filename:filename,
			rows_number:rows_number,
			nombre_tabla:nombre_tabla,
		};

		var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("export_excel/excel_general")?>').attr('method','POST').attr('target', '_self').appendTo('body');

		for (var i in datos) {
		if (!datos.hasOwnProperty(i)) continue;
			$('<input type="hidden"/>').attr('name', i).val(JSON.stringify(datos[i])).appendTo($form);
		}
		
		$form.submit();
		
	}
	*/
	
	
	// SELECCIÓN MÚLTIPLE DE FILAS DE APPTABLE
	var data_ids = [];
	function get_selected_rows(){
		
		var rows_selected = $("#client_indicators-table").DataTable().column(0).checkboxes.selected();
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
		$("#confirmMultipleDeleteButton").attr("data-action-url", "<?php echo get_uri("client_indicators/delete_multiple/"); ?>");
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
						table = $("#client_indicators-table").dataTable();
						table.fnDeleteRow($("#client_indicators-table").DataTable().row(tr).index());
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