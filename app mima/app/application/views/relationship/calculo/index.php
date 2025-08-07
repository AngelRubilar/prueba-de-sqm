<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('calculation'); ?></h4>
        <div class="title-button-group">
            <?php
            echo modal_anchor(get_uri("relationship/add_calculation"), "<i class='fa fa-plus-circle'></i> " . lang('add_calculation'), array("class" => "btn btn-default", "title" => lang('add_calculation'), "data-modal" => true));
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="calculo-table" class="display" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#calculo-table").appTable({
            source: '<?php echo_uri("relationship/calculo_list_data"); ?>',
			filterDropdown: [
				{name: "id_bd", class: "w200", options: <?php echo $bases_de_datos_dropdown; ?>},
				//{name: "id_criterio", class: "w200", options: <?php //echo $criterios_dropdown; ?>},
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>}
			],
            //order: [[1, "asc"]],
            columns: [
                {title: "ID", "class": "text-right dt-head-center w10"},
                {title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("rule"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang('fc_rule'); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("calculation"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("database"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("category"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("subcategory"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("label"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w10p"}
            ],
            //printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
            //xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
        });
    });
</script>