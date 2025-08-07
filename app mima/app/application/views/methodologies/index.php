<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang('methodologies'); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('methodologies'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("methodologies/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_methodology'), array("class" => "btn btn-default", "title" => lang('add_methodology'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="methodology-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#methodology-table").appTable({
            source: '<?php echo_uri("methodologies/list_data"); ?>',
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("description"); ?>", "class": "text-center dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
          // printColumns: [0, 1, 2, 3, 4],
         // xlsColumns: [0, 1, 2, 3, 4]
        }); 
    });   
</script>