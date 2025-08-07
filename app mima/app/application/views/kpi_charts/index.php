<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("kpi"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("KPI_Charts"); ?>"><?php echo lang("kpi_charts") ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('kpi_charts'); ?></h1>
        </div>
        <div class="table-responsive">
            <table id="kpi_charts-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#kpi_charts-table").appTable({
            source: '<?php echo_uri("KPI_Charts/list_data") ?>',
			filterDropdown: [
				{name: "item", class: "w200", options: <?php echo $items_dropdown; ?>},
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_fase", class: "w200", options: <?php echo $fases_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("phase"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("kpi_item"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("kpi_subitem"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("chart_type"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
		
		$(document).on('click', '.delete', function(){
			initScrollbar(".modal-body", {setHeight: 50});
		});
			
    });
</script>