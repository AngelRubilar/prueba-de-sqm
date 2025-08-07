<?php echo form_open("", array("id" => "forms-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

	<div class="form-group">
        <label for="number_of_records" class="col-md-3"><?php echo lang('number_of_records'); ?></label>
        <div class="col-md-9">
            <?php
            echo $num_registros;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($fecha_modificacion)?time_date_zone_format($fecha_modificacion, $project_info->id):"-";
            ?>
        </div>
    </div>
        
    <div class="panel">
        <div class="table-responsive">
            <table id="record-table" class="display" cellspacing="0" width="100%"> 
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
#record-table th { font-size: 12px; }
#record-table td { font-size: 11px; }

</style>
<script type="text/javascript">
	$(document).ready(function(){
		
		$("#record-table").appTable({
			source: '<?php echo_uri("forms/view_list_data/".$record_info->id); ?>',
			<?php if($record_info->id_tipo_formulario == 1){ ?>
			filterDropdown: [
				{name: "id_categoria", class: "w200", options: <?php echo $categorias_dropdown; ?>}
			],
			<?php }?>
			columns: [
				{title: "<?php echo lang("id"); ?>", "class": "text-center w50 hide"}
				<?php echo $columnas; ?>
			],
			//summation: [{column: 3, dataType: 'number'}],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//orderMulti: true
			//columnDefs : [{targets: 1, type: "extract-date"}],
			order: [1 , "desc"],
			columnShowHideOption: false
		});
		
		setTimePicker('.time_preview');
		$('.select2').select2();
		$('.rut').rut({
			formatOn: 'keyup',
			minimumLength: 8,
			validateOn: 'change'
		});
		setDatePicker(".fecha, .datepicker");
	});

</script>