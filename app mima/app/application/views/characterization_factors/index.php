<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("characterization_factors"); ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('characterization_factors'); ?></h1>
			<div class="title-button-group">
                <?php echo modal_anchor(get_uri("characterization_factors/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_characterization_factor'), array("id" => "agregar", "class" => "btn btn-default", "title" => lang('add_characterization_factor'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="characterization_factors-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
		$("#characterization_factors-table").appTable({
            source: '<?php echo_uri("characterization_factors/list_data2") ?>',
			//serverSide: true,
			filterDropdown: [
				{name: "id_material", class: "w150", options: <?php echo $materiales_dropdown; ?>},
				{name: "id_huella", class: "w150", options: <?php echo $huellas_dropdown; ?>},
				{name: "id_bd", class: "w150", options: <?php echo $bases_de_datos_dropdown; ?>},
				{name: "id_metodologia", class: "w150", options: <?php echo $metodologias_dropdown; ?>},
				{name: "id_formato_huella", class: "w150", options: <?php echo $formato_huellas_dropdown; ?>}
			],
            columns: [
                {data: "id", title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
				{data: "nombre_bd", title: "<?php echo lang("database"); ?>", "class": "text-left dt-head-center"},
				{data: "nombre_formato_huella", title: "<?php echo lang("footprint_format"); ?>", "class": "text-left dt-head-center"},
                {data: "nombre_metodologia", title: "<?php echo lang("calculation_methodology"); ?>", "class": "text-left dt-head-center",
				render: function (data, type, row) {
					return '<a href="#" title="<?php echo lang('view_characterization_factor'); ?>" data-act="ajax-modal" data-title="<?php echo lang('view_characterization_factor') ?>" data-action-url="<?php echo get_uri("characterization_factors/view/"); ?>' + row.id + '">' + data + '</a>';
					}
				},
				{data: "nombre_huella", title: "<?php echo lang("environmental_footprint"); ?>", "class": "text-left dt-head-center"},
				{data: "nombre_material", title: "<?php echo lang("material"); ?>", "class": "text-left dt-head-center"},
				{data: "nombre_categoria", title: "<?php echo lang("category"); ?>", "class": "text-left dt-head-center"},
				{data: "nombre_subcategoria", title: "<?php echo lang("subcategory"); ?>", "class": "text-left dt-head-center"},
				{data: "nombre_unidad", title: "<?php echo lang("unit"); ?>", "class": "text-left dt-head-center"},
				{data: "factor", title: "<?php echo lang("factor"); ?>", "class": "text-right dt-head-center"},
                {data: "id", title: '<i class="fa fa-bars"></i>', "class": "text-center option w150",
				render: function (data, type, row) {
					return '<a href="#" title="<?php echo lang('view_characterization_factor'); ?>" data-act="ajax-modal" data-title="<?php echo lang('view_characterization_factor'); ?>" data-action-url="<?php echo get_uri("characterization_factors/view/"); ?>' + data + '"><i class="fa fa-eye"></i></a>'
						+'<a href="#" class="edit" title="<?php echo lang('edit_characterization_factor'); ?>" data-post-id="' + data + '" data-act="ajax-modal" data-title="<?php echo lang('edit_characterization_factor'); ?>" data-action-url="<?php echo get_uri("characterization_factors/modal_form"); ?>"><i class="fa fa-pencil"></i></a>'
						+'<a href="#" title="<?php echo lang('delete_characterization_factor'); ?>" class="delete" data-id="' + data + '" data-action-url="<?php echo get_uri("characterization_factors/delete"); ?>" data-action="delete-confirmation"><i class="fa fa-times fa-fw"></i></a>';
					}
				}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20])
        });
		
		
    });
</script>