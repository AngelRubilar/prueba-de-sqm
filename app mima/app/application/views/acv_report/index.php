<?php
$this->load->view("includes/summernote");
?>

<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("acv"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("acv_report"); ?></a>
    </nav>

    <div class="panel">
    
		<?php echo form_open(get_uri("acv_report/save"), array("id" => "acv_report-form", "class" => "general-form", "role" => "form")); ?>

        <div class="panel-default">
        
            <div class="page-title clearfix">
                <h1><?php echo lang('acv_reports'); ?></h1>
            </div>

            <div class="panel-body">
            
                <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
                
                <div class="form-group">
                    <label for="client" class="<?php echo $label_column ?>"><?php echo lang('client'); ?></label>
                    <div class="<?php echo $field_column ?>">
                        <?php
                        echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='clients' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                        ?>
                    </div>
                </div>
                
                <div id="proyectos_group">
                    <div class="form-group">
                        <label for="project" class="<?php echo $label_column ?>"><?php echo lang('project'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("project", $proyectos, array($model_info->id_proyecto), "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
                
                <div id="subproyectos_group">
                    <div class="form-group">
                        <label for="subproject" class="<?php echo $label_column ?>"><?php echo lang('subproject'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("subproject", $subproyectos, array($model_info->id_subproyecto), "id='subproject' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
                
                <div id="uf_group">
                    <div class="form-group">
                        <label for="functional_unit" class="<?php echo $label_column ?>"><?php echo lang('functional_unit'); ?></label>
                        <div class="<?php echo $field_column ?>">
                            <?php
                            echo form_dropdown("functional_unit", $unidades_funcionales, array($model_info->id_unidad_funcional), "id='functional_unit' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="" class="col-md-2"><?php echo lang('date_range') ?></label>
                    
                    
                       <!--<label for="" class="col-md-2"><?php echo lang('since') ?></label>-->
                        <div class="col-md-5">
                            <?php 
                                echo form_input(array(
                                    "id" => "start_date",
                                    "name" => "start_date",
                                    "value" => '',
                                    "class" => "form-control",
									"data-rule-required" => "true",
									"data-msg-required" => lang('field_required'),
                                    "placeholder" => lang('since'),
                                    "autocomplete" => "off",
                                ));
                            ?>
                        </div>
                 
                  
                        <!--<label for="" class="col-md-2"><?php echo lang('until') ?></label>-->
                        <div class="col-md-5">
                            <?php 
                                echo form_input(array(
                                    "id" => "end_date",
                                    "name" => "end_date",
                                    "value" => '',
                                    "class" => "form-control",
                                    "placeholder" => lang('until'),
									"data-rule-required" => "true",
									"data-msg-required" => lang('field_required'),
                                    "data-rule-greaterThanOrEqual" => "#start_date",
                                    "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                    "autocomplete" => "off",
                                ));
                            ?>
                        </div>
                                     
                </div>
                
            </div>

            <div class="panel-footer clearfix">
                <button id="generar_informe_acv" type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('generate_acv_report'); ?></button>
            </div>
        </div>

        <?php echo form_close(); ?>
     
    </div> 
    
    <div class="panel">
    	<div class="panel-default">
			<div id="acv_report_group"></div>
        </div>
    </div>
    
    
</div>
<script type="text/javascript">
	$(document).ready(function () {
        
		$('#acv_report-form .select2').select2();
		
		setDatePicker("#start_date");
		setDatePicker("#end_date");
		
		$("#acv_report-form").appForm({
            ajaxSubmit: false
        });
		
		$('#clients').change(function(){	
			
			var id_client = $(this).val();	
			select2LoadingStatusOn($('#project'));
					
			$.ajax({
				url:  '<?php echo_uri("acv_report/get_projects_of_client") ?>',
				type:  'post',
				data: {id_client:id_client},
				//dataType:'json',
				success: function(respuesta){
					
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
					select2LoadingStatusOff($('#project'));
				}
			});

		});	
		
		$(document).on("change", "#project", function() {	
		
			var id_proyecto = $(this).val();
			select2LoadingStatusOn($('#subproject'));
					
			$.ajax({
				url:  '<?php echo_uri("acv_report/get_subprojects_of_projects") ?>',
				type:  'post',
				data: {id_proyecto:id_proyecto},
				//dataType:'json',
				success: function(respuesta){
					
					$('#subproyectos_group').html(respuesta);
					$('#subproject').select2();
					select2LoadingStatusOff($('#subproject'));
				}
			});
	
		});	
		
		$(document).on("change", "#subproject", function() {	
		
			var id_subproyecto = $(this).val();
			select2LoadingStatusOn($('#functional_unit'));
					
			$.ajax({
				url:  '<?php echo_uri("acv_report/get_functional_units_of_subproject") ?>',
				type:  'post',
				data: {id_subproyecto:id_subproyecto},
				//dataType:'json',
				success: function(respuesta){	
					$('#uf_group').html(respuesta);
					$('#functional_unit').select2();
					select2LoadingStatusOff($('#functional_unit'));
				}
			});
	
		});	

		$("#acv_report-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		$('#generar_informe_acv').click(function(e){
			$("#acv_report-form").valid();
			
			var id_cliente = $('#clients').val();
			var id_proyecto = $('#project').val();
			var id_subproyecto = $('#subproject').val();
			var id_unidad_funcional = $('#functional_unit').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			
			if(id_cliente && id_proyecto && id_subproyecto && id_unidad_funcional && start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:  '<?php echo_uri("acv_report/get_acv_report") ?>',
						type:  'post',
						data: {
							id_cliente: id_cliente,
							id_proyecto: id_proyecto,
							id_subproyecto: id_subproyecto,
							id_unidad_funcional: id_unidad_funcional,
							start_date: start_date,
							end_date: end_date
						},beforeSend: function() {
					   		$('#acv_report_group').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
						},
						
						//dataType:'json',
						success: function(respuesta){;
							$('#acv_report_group').html(respuesta);
							//$('#unit_symbol').select2();
						}
					});	
					
				}
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		});	
		
		$(document).on("click", "#excel_units", function(e) {	
			generar_excel("unidades-table");
			e.preventDefault();
			//return false;

		});
		$(document).on("click", "#excel_info_empresa", function() {	
			generar_excel("info_empresa-table");
			//return false;
		});
		$(document).on("click", "#excel_info_proyecto", function() {	
			generar_excel("info_proyecto-table");
			//return false;
		});
		$(document).on("click", "#excel_procesos_unitarios", function() {	
			generar_excel("procesos_unitarios-table");
			//return false;
		});
		$(document).on("click", "#excel_listado_materiales", function() {	
			generar_excel("listado_materiales-table");
			//return false;
		});
		$(document).on("click", "#excel_flujos_procesos_unitarios", function() {	
			generar_excel("flujos_procesos_unitarios-table");
			//return false;
		});
		$(document).on("click", "#excel_asignacion", function() {	
			generar_excel("asignacion-table");
			//return false;
		});
		$(document).on("click", "#excel_categorias_impacto", function() {	
			generar_excel("categorias_impacto-table");
		});
		$(document).on("click", "#excel_modelo_caracterizacion", function() {	
			generar_excel("modelo_caracterizacion-table");
		});
		$(document).on("click", "#excel_category_indicators", function() {	
			generar_excel("category_indicators-table");
		});
		$(document).on("click", "#excel_resultados_cat_impact", function() {	
			generar_excel("resultados_cat_impact-table");
		});
		$(document).on("click", "#excel_puntos_criticos", function() {	
			generar_excel("puntos_criticos-table");
		});

		function generar_excel(nombre_tabla){
			
			var myTableArray = [];
			var columns_number = document.getElementById(nombre_tabla).rows[1].cells.length;
			var rows_number = document.getElementById(nombre_tabla).rows.length;
			var filename = "";
			var id_cliente = $('#clients').val();
			var id_proyecto = $('#project').val();
			var id_subproyecto = $('#subproject').val();
			var id_unidad_funcional = $('#functional_unit').val();
			var fecha_desde = $('#start_date').val();
			var fecha_hasta = $('#end_date').val();
			
			$("table#" + nombre_tabla + " tr").each(function(index) {
				var arrayOfThisRow = [];
				if(index == 0){
					var tableData = $(this).find('th');
				}else{
					var tableData = $(this).find('td');
				}
				if (tableData.length > 0) {
					tableData.each(function() {
						if($(this).find('span').length){
							arrayOfThisRow.push($(this).find('span').text());
							filename = $(this).find('button[data-filename]').data("filename");
						} else {
							arrayOfThisRow.push($(this).text());
						}
					});
					myTableArray.push(arrayOfThisRow);
				}
			});
			
			var datos = {
				tabla:myTableArray, 
				columns_number:columns_number,
				filename:filename,
				rows_number:rows_number,
				nombre_tabla:nombre_tabla,
				id_cliente:id_cliente,
				id_proyecto:id_proyecto,
				id_subproyecto:id_subproyecto,
				id_unidad_funcional:id_unidad_funcional,
				fecha_desde:fecha_desde,
				fecha_hasta:fecha_hasta
			};
			
			if((nombre_tabla == "info_empresa-table") || (nombre_tabla == "info_proyecto-table")){
				var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("export_excel/excel_acv_report_2/")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			} else {
				var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("export_excel/excel_acv_report/")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			}
			
			
			for (var i in datos) {
			if (!datos.hasOwnProperty(i)) continue;
				$('<input type="hidden"/>').attr('name', i).val(JSON.stringify(datos[i])).appendTo($form);
			}
			$form.submit();
			
			//var scr = $("#page-container").find(".mCSB_dragger").position().top;
			var top = $("#"+nombre_tabla).position().top;
			var left = $("#"+nombre_tabla).position().left;
			$(".scrollable-page").mCustomScrollbar("scrollTo", {y:top, x:left}, {scrollInertia:0});
			
		}		
		
    });
</script>