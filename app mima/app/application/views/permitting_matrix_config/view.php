<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

	<div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?$model_info->modified:'-';
            ?>
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table id="individual_upload-table" class="display" width="100%">            
            </table>
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>
<style>
#ajaxModal > .modal-dialog {
    width:80% !important;
}
</style>
<script type="text/javascript">
    $(document).ready(function () {
				
		$("#individual_upload-table").appTable({
			//source: '<?php //echo_uri("upload_compromises/list_data_carga_individual/". $id_compromiso_proyecto) ?>',
			source: '<?php echo_uri("permitting_matrix_config/list_data_view_matrix/". $id_permiso_proyecto); ?>',
			//order: [[1, "asc"]],
			columns: [				
				{title: "ID", "class": "text-center w10"},
				{title: "<?php echo lang("permitting_number"); ?>", "class": "text-right dt-head-center"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("phases"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("entity"); ?>", "class": "text-right dt-head-center"}
				//{title: "<?php echo lang("reportability"); ?>", "class": ""}
				<?php echo $columnas_campos; ?>
				
			],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
			//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
		});
		
		$('.column-show-hide-popover').click(function(e){
			e.preventDefault();
		})
		
    });
</script>