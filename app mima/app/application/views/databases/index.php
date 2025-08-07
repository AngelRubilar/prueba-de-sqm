<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("databases"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('databases'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("databases/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_database'), array("class" => "btn btn-default", "title" => lang('add_database'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="databases-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#databases-table").appTable({
            source: '<?php echo_uri("databases/list_data"); ?>',
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("description"); ?>", "class": "text-center dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2])
        });
    });
</script>