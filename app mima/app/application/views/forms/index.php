<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("forms"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('forms'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("forms/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_form'), array("id" => "agregar", "class" => "btn btn-default", "title" => lang('add_form'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="forms-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div> 
</div>

<script type="text/javascript"> 
    $(document).ready(function () {
		
        $("#forms-table").appTable({
            source: '<?php echo_uri("forms/list_data"); ?>',
			filterDropdown: [
				{name: "id_tipo_formulario", class: "w200", options: <?php echo $tipos_formularios_dropdown; ?>},
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("description"); ?>", "class": "text-center"},
				{title: "<?php echo lang("category"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-table"></i>', "class": "text-center option w50"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6])
        });
		
		/* $(document).on('click', '#ms-campos > .ms-selection > ul.ms-list li', function(e) {
			//alert($(this).val());
			$('#ms-campos > .ms-selection > ul.ms-list > li.seleccionado').removeClass("seleccionado");
			$(this).addClass("seleccionado");
		});
		
		//Ordenar campos (multiselect)
		$(document).on('click', '#subir_campo', function(){
			var elemento_seleccionado = $('#ms-campos > .ms-selection > ul.ms-list > li.seleccionado');
			var elemento_anterior = $(elemento_seleccionado).prevAll("li.ms-selected:first");
			$(elemento_anterior).remove();
			$(elemento_anterior).insertAfter(elemento_seleccionado);
		});
		
		$(document).on('click', '#bajar_campo', function(){
			var elemento_seleccionado = $('#ms-campos > .ms-selection > ul.ms-list > li.seleccionado');
			var elemento_posterior = $(elemento_seleccionado).nextAll("li.ms-selected:first");
			$(elemento_posterior).remove();
			$(elemento_posterior).insertBefore(elemento_seleccionado);
		}); */
		
		/* $(document).on('click', '#deseleccionar', function(){
			
			var elemento_seleccionado = $('#ms-campos > .ms-selection > ul.ms-list > li.seleccionado');
			var id = $(elemento_seleccionado).attr('data-id')
			$('#campos').multiSelect('deselect', id);
			
			//var campos = [];
			//$.each($("#campos option:selected"), function() {
			//  alert($(this).val());
			//  //campos.push($(this).val());
			//	$('#campos').multiSelect('deselect', $(this).val());
			//}); 
			//alert(campos.join(", "));
			
			//$('#campos option:selected:last').multiSelect('deselect', $(this).val());
			
		}); */
		
    });
</script>