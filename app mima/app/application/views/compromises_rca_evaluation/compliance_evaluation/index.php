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
                <table id="compliance_evaluation-table" class="display" width="100%">            
                </table>
            </div>  
            
      
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
		
		<?php	
			if($id_evaluado && !$id_valor_compromiso){ 
				$parametros = $id_evaluado."/0";
			}
			if(!$id_evaluado && $id_valor_compromiso){
				$parametros = "0/".$id_valor_compromiso;
			}
			if($id_evaluado && $id_valor_compromiso){
				$parametros = $id_evaluado."/".$id_valor_compromiso;
			}
			if(!$id_evaluado && !$id_valor_compromiso){
				$parametros = "0/0";
			}
		?>
		
		$("#compliance_evaluation-table").appTable({
			source: '<?php echo_uri("compromises_rca_evaluation/list_data/".$id_compromiso_proyecto."/".$parametros); ?>',
			//order: [[1, "asc"]],
			columns: [
				//{title: "ID", "class": "text-center w10"},
				{title: "<?php echo lang("compromise_number"); ?>", "class": "text-right dt-head-center"},
				{title: "<?php echo lang("compromise"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("evaluated"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("compliance_action_control"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("execution_frequency"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("state"); ?>", "class": "text-center dt-body-center", 
					render: function (data, type, row) {
						return '<center>'+data+'</center>';
					}
				},
				{title: "<?php echo lang("evidences"); ?>", "class": "text-center option"},
				{title: "<?php echo lang("observations"); ?>", "class": "text-center"},
				//{title: "<?php echo lang("responsible"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("evaluator"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("evaluation_date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
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
					id_valor_compromiso = $(this).attr('data-id_valor_compromiso'),
					id_evaluado = $(this).attr('data-id_evaluado'),
					select_evaluado = $(this).attr('data-select_evaluado'),
					select_valor_compromiso = $(this).attr('data-select_valor_compromiso');
					
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {
					id_evaluacion:id_evaluacion, 
					id_evidencia:id_evidencia,
					id_valor_compromiso:id_valor_compromiso,
					id_evaluado:id_evaluado,
					select_evaluado:select_evaluado,
					select_valor_compromiso:select_valor_compromiso,
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