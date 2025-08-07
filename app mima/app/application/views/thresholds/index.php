<div id="page-content" class="p20 clearfix">
	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("thresholds"); ?></a> 
    </nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('thresholds'); ?></h1>
            <div class="title-button-group">
				<!--<div class="btn-group" role="group">
					<button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
				</div> -->
                <?php echo modal_anchor(get_uri("thresholds/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_threshold'), array("class" => "btn btn-default", "title" => lang('add_threshold'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="thresholds-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
	
</div>
<script type="text/javascript">
$(document).ready(function () {

	$("#thresholds-table").appTable({
		source: '<?php echo_uri("thresholds/list_data"); ?>',
		filterDropdown: [
			{name: "id_material", class: "w200", options: <?php echo $materials_dropdown; ?>},
			{name: "id_form", class: "w200", options: <?php echo $forms_dropdown; ?>},
			{name: "id_module", class: "w200", options: <?php echo $modules_dropdown; ?>},
			{name: "id_project", class: "w200", options: <?php echo $project_dropdown; ?>},
			{name: "id_client", class: "w200", options: <?php echo $client_dropdown; ?>},
		],
		columns: [
			{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
			{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("module"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("form"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("label"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("material"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("categories"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("unit_type"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("unit"); ?>", "class": "text-left dt-head-center"},
			//{title: "< ?php echo lang("unit_value") ?>"},
			{title: "<?php echo lang("risk_value"); ?>", "class": "text-right dt-head-center"},
			{title: "<?php echo lang("threshold_value"); ?>", "class": "text-right dt-head-center"},
			{title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
		]
	});
	
	$(document).on("click","#excel", function() {	
		generar_excel("thresholds-table");
	});
	
	
	function generar_excel(nombre_tabla){
		
		var myTableArray = [];
		var columns_number = document.getElementById(nombre_tabla).rows[1].cells.length;
		var rows_number = document.getElementById(nombre_tabla).rows.length;

		
		$("table#" + nombre_tabla + " tr").each(function(index) {
			var arrayOfThisRow = [];
			if(index == 0){
				var tableData = $(this).find('th');
			}else{
				var tableData = $(this).find('td');
			}
			if (tableData.length > 0) {
				tableData.each(function() {
					if($(this).find('span').length){
						arrayOfThisRow.push($(this).find('span').text());
						filename = $(this).find('span').text();
					} else {
						arrayOfThisRow.push($(this).text());
					}
				});
				myTableArray.push(arrayOfThisRow);
			}
		});

		var datos = {
			tabla:myTableArray, 
			columns_number:columns_number,
			filename:"<?php echo lang('thresholds'); ?>",
			rows_number:rows_number,
			nombre_tabla:nombre_tabla,
		};
		
		var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("export_excel/excel_general"); ?>').attr('method','POST').attr('target', '_self').appendTo('body');

		for (var i in datos) {
		if (!datos.hasOwnProperty(i)) continue;
			$('<input type="hidden"/>').attr('name', i).val(JSON.stringify(datos[i])).appendTo($form);
		}
		$form.submit();
	}
	
});
</script>