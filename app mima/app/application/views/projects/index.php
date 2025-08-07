<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("projects"); ?></a>
</nav>
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('projects'); ?></h1>
            <div class="title-button-group">
                <?php
                if ($can_create_projects) {
                    echo modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_project'), array("class" => "btn btn-default", "title" => lang('add_project')));
                }
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="project-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo ($can_edit_projects || $can_delete_projects); ?>") {
            optionVisibility = true;
        }

        $("#project-table").appTable({
            source: '<?php echo_uri("projects/list_data"); ?>',
            /*radioButtons: [{text: '<?php echo lang("open"); ?>', name: "status", value: "open", isChecked: true}, {text: '<?php echo lang("completed"); ?>', name: "status", value: "completed", isChecked: false}, {text: '<?php echo lang("canceled"); ?>', name: "status", value: "canceled", isChecked: false}],*/
            /*filterDropdown: [{name: "project_label", class: "w200", options: <?php //echo $project_labels_dropdown; ?>}],*/
		    filterDropdown: [
				{name: "status", class: "w200", options: <?php echo $estados_dropdown; ?>},
				{name: "client_id", class: "w200", options: <?php echo $clientes_dropdown; ?>},	
			],
            columns: [
                {title: '<?php echo lang("id"); ?>', "class": "text-right dt-head-center w50"},
                {title: '<?php echo lang("project_name"); ?>', "class": "text-left dt-head-center"},
                {title: '<?php echo lang("client"); ?>', "class": "text-left dt-head-center w10p"},
                //{visible: optionVisibility, title: '<?php echo lang("price"); ?>', "class": "w10p"},
                {visible: false, searchable: false},
                {title: '<?php echo lang("start_date"); ?>', "class": "text-left dt-head-center w10p"},
                {visible: false, searchable: false},
                {title: '<?php echo lang("deadline"); ?>', "class": "text-left dt-head-center w10p"},
                {title: '<?php echo lang("status"); ?>', "class": "text-left dt-head-center w10p"},
                {visible: optionVisibility, title: '<i class="fa fa-bars"></i>', "class": "text-center option w120"}
            ],
            order: [[0, "asc"]]//,
          //  printColumns: [0, 1, 2, 3, 5, 7, 8, 9],
          //  xlsColumns: [0, 1, 2, 3, 5, 7, 8, 9], 
        });
    });
</script>