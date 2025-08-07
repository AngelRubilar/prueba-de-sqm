<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("compromises"); ?> /</a>
  <a class="breadcrumb-item" href=""><?php echo lang("compromises_reportables_evaluation"); ?></a>
</nav>

<?php if($puede_ver == 1) { ?>

<div class="panel panel-default">
    <div class="table-responsive">
        <table id="compliance_evaluation-table" class="display" width="100%">            
        </table>
    </div>
</div>

<?php } else { ?>

<div class="row"> 
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                    <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>
</div>

<script type="text/javascript">

	$(document).ready(function(){
	
		$('[data-toggle="tooltip"]').tooltip();
		
		$("#compliance_evaluation-table").appTable({
            source: '<?php echo_uri("compromises_reportables_evaluation/list_data"); ?>',
            columns: [
                {title: "<?php echo lang("reportable_compromise_number"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("reportable"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("status"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("evidences"); ?>", "class": "text-center option"},
				{title: "<?php echo lang("condition_or_commitment"); ?>", "class": "text-center"},
				{title: "<?php echo lang("observations"); ?>", "class": "text-center"},
				{title: "<?php echo lang("execution_date"); ?>", "class": "text-center"},
				{title: "<?php echo lang("responsible"); ?>", "class": "text-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
		$(document).on('click', '.table_delete a.delete', function() {
			console.log("delete");
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