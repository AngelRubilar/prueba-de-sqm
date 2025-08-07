<div id="page-content" class="p20 pt0 clearfix">
    <div class="panel panel-default">
    	
            <!--
            <div class="tab-title clearfix">
                <h4><?php //echo lang('individual_load'); ?></h4>
                <div class="title-button-group">
                    <?php
                   // echo modal_anchor(get_uri("upload_compromises/modal_form_carga_individual/".$id_compromiso_proyecto), "<i class='fa fa-plus-circle'></i> " . lang('add_compromise'), array("class" => "btn btn-default", "title" => lang('add_compromise')));
                    ?>
                </div>
            </div>
            -->
            <div class="table-responsive">
                <table id="agreement_evaluation-table" class="display" width="100%">            
                </table>
            </div>  
            
      
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
		
		<?php	
			if($value_agreement_id && !$id_stakeholder){ 
				$parametros = $value_agreement_id."/0";
			}
			if(!$value_agreement_id && $id_stakeholder){
				$parametros = "0/".$id_stakeholder;
			}
			if($value_agreement_id && $id_stakeholder){
				$parametros = $value_agreement_id."/".$id_stakeholder;
			}
			if(!$value_agreement_id && !$id_stakeholder){
				$parametros = "0/0";
			}
		?>
		
		$("#agreement_evaluation-table").appTable({
			source: '<?php echo_uri("agreements_monitoring/list_data/".$id_agreements_matrix."/".$parametros); ?>',
			//order: [[1, "asc"]],
			columns: [
				{title: "ID", "class": "text-center w10 hide"},
				{title: "<?php echo lang("code"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center w100"},
				{title: "<?php echo lang("managing"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("stakeholder"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("processing_status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("activities_status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("financial_status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("observations"); ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("evidences"); ?>", "class": "text-center dt-head-center option"},
				{title: "<?php echo lang("last_monitoring"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
				{title: '<i class="fa fa-bars"></i>', "class": "text-center option w5 no_breakline"}
			],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
			//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
		});
		
		
		
		$(document).on('click', '.table_delete a.delete', function() {
			$(this).each(function () {
				$.each(this.attributes, function () {
					if (this.specified && this.name.match("^data-")) {
						$("#confirmFileDeleteButton").attr(this.name, this.value);
					}
				});
			});
			$("#confirmationFileModal").modal('show');
		});
		
		//$('#confirmationModal').on('click', '#confirmDeleteButton', function() {
		//$('#confirmDeleteButton').click(function() {
		$(document).off('click', '#confirmFileDeleteButton').on('click', '#confirmFileDeleteButton', function() {
			
			//appLoader.show();
			
			var url = $(this).attr('data-action-url'),
					agreement_monitoring_id = $(this).attr('data-agreement_monitoring_id'),
					id_evidencia = $(this).attr('data-id_evidencia');
					
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {
					agreement_monitoring_id:agreement_monitoring_id, 
					id_evidencia:id_evidencia,
					},
				success: function (result) {
					if (result.success) {
						
						/*
						$(function () {
						   $('.modal').modal('hide');
						});
						*/
						
						appAlert.warning(result.message, {duration: 20000});
						//$('#table_delete_' + result.id_evidencia).parent().parent().html("");
						//$("#agreement_evaluation-table").dataTable().fnReloadAjax();
						//initScrollbar(".modal-body", {setHeight: 280});
						
						$('#table_delete_' + result.id_evidencia).parent().parent().html(result.new_field);
						$('#agreement_evaluation-form').append('<input type="hidden" name="id_evidencia_eliminar[]" value="' + result.id_evidencia + '" />');
						

					} else {
						appAlert.error(result.message);
					}
					//appLoader.hide();
				}
			});
					
		});
		

    });
	
</script>