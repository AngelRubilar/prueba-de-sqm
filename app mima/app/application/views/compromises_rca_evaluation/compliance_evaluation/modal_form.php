<?php echo form_open(get_uri("compromises_rca_evaluation/save"), array("id" => "compliance_evaluation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("compromises_rca_evaluation/compliance_evaluation/compliance_evaluation_form_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <?php if(!($puede_editar == 3 && $puede_agregar == 3)){ ?>
		<button id="btn_guardar" type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
	<?php }?>

</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		<?php if(($puede_editar == 2 && $puede_agregar == 3) && !$evaluaciones_propias) { ?>
			$("#btn_guardar").remove();
		<?php } ?>
		
        $("#compliance_evaluation-form").appForm({
            onSuccess: function(result) {
				//if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        //location.reload();
						$.ajax({
							url:  '<?php echo_uri("compromises_rca_evaluation/get_evaluation_table_of_compromise"); ?>',
							type:  'post',
							data: {
								id_evaluado:result.id_evaluado, 
								id_valor_compromiso:result.id_valor_compromiso, 
								id_compromiso_proyecto:result.id_compromiso_proyecto},
							//dataType:'json',
							success: function(respuesta){
								$('#evaluation_table').html(respuesta);	
							}
						});
						
						//$("#compliance_evaluation-table").appTable({newData: result.data, dataId: result.id});
                    }, 500);

                //} else {
                    //$("#compliance_evaluation-table").appTable({newData: result.data, dataId: result.id});
                //}
            }
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