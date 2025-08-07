<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('assignment'); ?></h4>
        <div class="title-button-group">
            <?php
            echo modal_anchor(get_uri("relationship/add_assignment"), "<i class='fa fa-plus-circle'></i> " . lang('add_assignment'), array("class" => "btn btn-default", "title" => lang('add_assignment'), "data-modal-lg" => true));
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="asignacion-table" class="display" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#asignacion-table").appTable({
            source: '<?php echo_uri("relationship/asignacion_list_data/") ?>',
			filterDropdown: [
				//{name: "id_criterio", class: "w200", options: <?php //echo $criterios_dropdown; ?>},
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>}
			],
            order: [[1, "asc"]],
            columns: [
                {title: "ID", "class": "text-right dt-head-center w10"},
                {title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("rule"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w5"}
            ],
            //printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
            //xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
        });
    });
</script>