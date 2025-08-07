<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="<?php echo get_uri("AYN_Notif_historical"); ?>"><?php echo lang("notifications") ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('notifications'); ?></h1>
            <div class="title-button-group">
                <?php //echo modal_anchor(get_uri("subprojects/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_subproject'), array("class" => "btn btn-default", "title" => lang('add_subproject'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="notifications-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#notifications-table").appTable({
            source: '<?php echo_uri("AYN_Notif_historical/list_data") ?>',
			filterDropdown: [
				{name: "id_admin_module", class: "w250", options: <?php echo $admin_modules; ?>},
				{name: "id_client_context_module", class: "w250", options: <?php echo $client_context_modules; ?>},
				{name: "id_client_module", class: "w250", options: <?php echo $clients_modules; ?>},
			],
			checkBoxes: [
				{text: '<?php echo lang("others_actions"); ?>', name: "actions", value: "others", isChecked: true},
				{text: '<?php echo lang("my_actions"); ?>', name: "actions", value: "own", isChecked: false},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
                {title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("module"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("message"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("date"); ?>", "class": "text-center"},
                //{title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			order: [0 , "desc"],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
    });
</script>