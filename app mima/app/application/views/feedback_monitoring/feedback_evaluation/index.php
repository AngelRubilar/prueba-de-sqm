<div id="page-content" class="p20 pt0 clearfix">
    <div class="panel panel-default">
    	
            <!--
            <div class="tab-title clearfix">
                <h4><?php //echo lang('individual_load'); ?></h4>
                <div class="title-button-group">
                    <?php
                   // echo modal_anchor(get_uri("upload_compromises/modal_form_carga_individual/".$id_feedback_matrix_config), "<i class='fa fa-plus-circle'></i> " . lang('add_compromise'), array("class" => "btn btn-default", "title" => lang('add_compromise')));
                    ?>
                </div>
            </div>
            -->
        
            <div class="table-responsive">
                <table id="compliance_evaluation-table" class="display" width="100%">            
                </table>
            </div>  
            
      
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
		
		<?php	
			if($proposito_visita && !$id_responsable){ 
				$parametros = $proposito_visita."/0";
			}
			if(!$proposito_visita && $id_responsable){
				$parametros = "0/".$id_responsable;
			}
			if($proposito_visita && $id_responsable){
				$parametros = $proposito_visita."/".$id_responsable;
			}
			if(!$proposito_visita && !$id_responsable){
				$parametros = "0/0";
			}
			
		?>
		
		$("#compliance_evaluation-table").appTable({
			source: '<?php echo_uri("feedback_monitoring/list_data/".$id_feedback_matrix_config."/".$parametros); ?>',
			//order: [[1, "asc"]],
			columns: [
				{title: "ID", "class": "text-center w10 hide"},
				//{title: "Número compromiso", "class": ""},
				{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center w100"},
				{title: "<?php echo lang("visit_purpose"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("responsible"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("answer"); ?>", "class": "text-center dt-head-center"},
				{title: "<?php echo lang("answer_status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("evidences"); ?>", "class": "text-center dt-head-center option"},
				{title: "<?php echo lang("last_monitoring"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
				{title: '<i class="fa fa-bars"></i>', "class": "text-center option w5"}
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
					id_evaluacion = $(this).attr('data-id_evaluacion'),
					id_evidencia = $(this).attr('data-id_evidencia'),
					id_responsable = $(this).attr('data-id_responsable'),
					proposito_visita = $(this).attr('data-proposito_visita'),
					select_proposito_visita = $(this).attr('data-select_proposito_visita'),
					select_responsable = $(this).attr('data-select_responsable');
					
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {
					id_evaluacion:id_evaluacion, 
					id_evidencia:id_evidencia,
					id_responsable:id_responsable,
					proposito_visita:proposito_visita,
					select_proposito_visita:select_proposito_visita,
					select_responsable:select_responsable,
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
						//$("#compliance_evaluation-table").dataTable().fnReloadAjax();
						//initScrollbar(".modal-body", {setHeight: 280});
						
						$('#table_delete_' + result.id_evidencia).parent().parent().html(result.new_field);
						$('#compliance_evaluation-form').append('<input type="hidden" name="id_evidencia_eliminar[]" value="' + result.id_evidencia + '" />');				
						
					} else {
						appAlert.error(result.message);
					}
					//appLoader.hide();
				}
			});
					
		});

    });
	
</script>