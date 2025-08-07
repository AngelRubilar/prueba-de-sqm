<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("compromises"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("compliance_status"); ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('compliance_status'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("compromises_compliance_status/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_compliance_status'), array("class" => "btn btn-default", "title" => lang('add_compliance_status'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="compromises_compliance_status-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#compromises_compliance_status-table").appTable({
            source: '<?php echo_uri("compromises_compliance_status/list_data"); ?>',
			filterDropdown: [
				{name: "tipo_evaluacion", class: "w200", options: <?php echo $tipo_evaluacion_dropdown; ?>},
				{name: "categoria", class: "w200", options: <?php echo $categorias_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("evaluation_type"); ?>", "class": "text-left dt-head-center"},			
				{title: "<?php echo lang("category"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("color"); ?>", "class": "text-center dt-body-center", 
					render: function (data, type, row) {
						return '<center>'+data+'</center>';
					}
					
				},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
    });
</script>