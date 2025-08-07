<div class="panel">

    <div class="tab-title clearfix">
        <h4><?php echo lang('individual_load'); ?></h4>
        <div class="title-button-group">
            <?php
            echo modal_anchor(get_uri("upload_permittings/modal_form_carga_individual/".$id_permiso_proyecto), "<i class='fa fa-plus-circle'></i> " . lang('add_permitting'), array("class" => "btn btn-default", "title" => lang('add_permitting')));
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="individual_upload-table" class="display" width="100%">            
        </table>
    </div>   
     
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
		<?php if($id_permiso_proyecto) { ?>
		
			$("#individual_upload-table").appTable({
				source: '<?php echo_uri("upload_permittings/list_data_carga_individual/".$id_permiso_proyecto); ?>',
				order: [[1, "asc"]],
				columns: [
					{title: "ID", "class": "text-right w10"},
					{title: "<?php echo lang("permitting_number"); ?>", "class": "text-right dt-head-center"},
					{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("phases"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("entity"); ?>", "class": "text-left dt-head-center"}
					//{title: "<?php echo lang("reportability"); ?>", "class": ""}
					<?php echo $columnas_campos; ?>,
					{title: '<i class="fa fa-bars"></i>', "class": "text-center option w15p"}
				],
				rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					$(nRow).find('[data-toggle="tooltip"]').tooltip();
				},
				//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
				//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
			});
		
		<? } ?>
    });
</script>