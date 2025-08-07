<div class="page-title clearfix">
<!--
    <div class="page-title clearfix">
        <h1><?php echo lang('kpi_report'); ?></h1>
    </div>
-->

	<?php echo form_open(get_uri("KPI_Report/save_report"), array("id" => "save_report-form", "class" => "general-form", "role" => "form")); ?>
        
        <!-- Filtros de consulta del reporte -->
        <input type="hidden" name="id_pais" value="<?php echo $id_pais; ?>" />
        <input type="hidden" name="id_fase" value="<?php echo $id_fase; ?>" />
        <input type="hidden" name="id_tecnologia" value="<?php echo $id_tecnologia; ?>" />
        <input type="hidden" name="id_proyecto" value="<?php echo $id_proyecto; ?>" />
        <input type="hidden" name="fecha_desde" value="<?php echo $fecha_desde; ?>" />
        <input type="hidden" name="fecha_hasta" value="<?php echo $fecha_hasta; ?>" />
        
    <div class="panel-body">
        <div class="form-group">
            <div class="table-responsive">
                <table id="kpi_report-table" class="table table-bordered table-hover table-striped">    
                    <thead>
                        <tr>
                            <th class=""><?php echo lang('code'); ?></th>
                            <th class="hidden-xs"><?php echo lang('name'); ?></th>
                            <th class=""><?php echo lang('value'); ?></th>
                            <th class=""><?php echo lang('unit'); ?></th>
                            <!-- <th class=""><?php echo lang('unit_type'); ?></th> -->
                            <th class="hidden-xs w50p"><?php echo lang('description'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php foreach($datos_kpi_reporte as $nombre_indicador => $datos_indicador){ ?>
                        	                            
                        	<?php
								// Traigo el valor del indicador
								$valor = $this->KPI_Values_model->get_one($datos_indicador["valor"]);
								// Traer Valores Condición
								$valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor->id,
									"deleted" => 0
								))->result_array();
								// Traigo el formulario del valor
								$formulario_valor = $this->Forms_model->get_one($valor->id_formulario);
								// Traigo la relación del formulario del valor con el proyecto
								$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
									"id_formulario" => $formulario_valor->id,
									"deleted" => 0
								));
								// Traigo los elementos del formulario a partir de su relación con el proyecto
								$elementos_formulario = $this->Form_values_model->get_all_where(array(
									"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
									"deleted" => 0
								))->result_array();
								
								// Declaro variable para el cálculo del valor del indicador
								$total_valor = 0;
								
								if($valor->id && $valor->tipo_valor == "simple") {

									if(count($valores_condicion)){
													
										$array_datos_valores_condicion = array();
										
										foreach($valores_condicion as $valor_condicion){
											
											$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
	
										}
	
									}
									
									if(count($elementos_formulario)){
	
										foreach($elementos_formulario as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
	
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											// Si los datos de las condiciones del valor son iguales a los del elemento del formulario del valor, suma los valores de las unidades de los elementos del formulario
											if($array_datos_valores_condicion == $elemento_campos_dinamicos){
												if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
													$campo_indicador = $valor->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$total_valor = $total_valor + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador]; 
														$total_valor = $total_valor + $valor_unidad_dinamica;
													}
												}
											}

										}
	
									}
									
									if($valor->operador){
										
										$operador = $valor->operador;
										$valor_operador = $valor->valor_operador;
										
										if($operador == "+"){
											$total_valor = $total_valor + $valor_operador;
										}
										if($operador == "-"){
											$total_valor = $total_valor - $valor_operador;
										}
										if($operador == "*"){
											$total_valor = $total_valor * $valor_operador;
										}
										if($operador == "/"){
											$total_valor = $total_valor / $valor_operador;
										}
											
									}
									
									$id_unidad_valor = $valor->id_unidad;
									$id_tipo_unidad_valor = $valor->id_tipo_unidad;
									//$id_unidad_indicador = $datos_indicador["unidad"];
									$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => $id_tipo_unidad_valor))->id_unidad; 

									// transformar valores en reporte, que tengan una unidad distinta a la unidad del indicador.
									// si el valor tiene una unidad distinta a la del indicador, transformar a la unidad del indicador.
									//if($id_unidad_valor != $id_unidad_indicador){
									if($id_unidad_valor != $id_unidad_destino){
										if($id_unidad_indicador != 18){ // Unidad de tipo unidad (id 18) no tiene conversión
											$fila_conversion = $this->Conversion_model->get_one_where(
												array(
													"id_tipo_unidad" => $id_tipo_unidad_valor,
													"id_unidad_origen" => $id_unidad_valor,
													//"id_unidad_destino" => $id_unidad_indicador
													"id_unidad_destino" => $id_unidad_destino
												)
											);
											$valor_transformacion = $fila_conversion->transformacion;
											$total_valor = $total_valor * $valor_transformacion;
										}
									} 
									
									unset($array_datos_valores_condicion);

								}
								
								if($valor->id && $valor->tipo_valor == "compound") {
									
									// Cálculo valor inicial
									$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
									$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_inicial->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
									
									if(!$formulario_valor_inicial->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_inicial->id,
											"deleted" => 0
										));
										$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_inicial->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_inicial = 0;
									
									if($valor_inicial->id && count($valores_condicion_inicial)) {
										
										$array_datos_valores_condicion_inicial = array();
										
										foreach($valores_condicion_inicial as $valor_condicion_inicial){
											
											$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
					
										}
										
									}
									
									if(count($elementos_formulario_inicial)){
					
										foreach($elementos_formulario_inicial as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
					
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
												
												if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador]; 
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												}	
					
											}
				
										}
					
									}
									
									if($valor_inicial->operador){
									
										$operador = $valor_inicial->operador;
										$valor_operador = $valor_inicial->valor_operador;
										
										if($operador == "+"){
											$total_valor_inicial = $total_valor_inicial + $valor_operador;
										}
										if($operador == "-"){
											$total_valor_inicial = $total_valor_inicial - $valor_operador;
										}
										if($operador == "*"){
											$total_valor_inicial = $total_valor_inicial * $valor_operador;
										}
										if($operador == "/"){
											$total_valor_inicial = $total_valor_inicial / $valor_operador;
										}
										
									}
									// Fin Cálculo valor inicial
					
									// Cálculo valores operación
									$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
									
									$total_valor_calculo_final = 0;
									$array_valores_operacion_compuesta = array();
									
									foreach($array_operacion_compuesta as $index => $operacion_compuesta){
										
										$operador = key($operacion_compuesta);
										$id_valor = $operacion_compuesta[key($operacion_compuesta)];
				
										$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
										$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
											"id_kpi_valores" => $valor_calculo->id,
											"deleted" => 0
										))->result_array();
										$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
										
										if(!$formulario_valor_calculo->fijo){
											$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
												"id_formulario" => $formulario_valor_calculo->id,
												"deleted" => 0
											));
											$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
												"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
												"deleted" => 0
											))->result_array();
										} else {
											$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
												"id_formulario" => $formulario_valor_calculo->id,
												"deleted" => 0
											))->result_array();
										}
										
										$total_valor_calculo = 0;
										
										if($valor_calculo->id && count($valores_condicion_calculo)) {
											
											$array_datos_valores_condicion_calculo = array();
											
											foreach($valores_condicion_calculo as $valor_condicion_calculo){
												
												$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
												$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
												$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
												$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
												
												if($valor_condicion_categoria){
													$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
												}
												if($valor_condicion_tipo_tratamiento){
													$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
												}
												if($valor_condicion_id_campo){
													$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
												}
												if($valor_condicion_id_campo_fijo){
													$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
												}
						
											}
											
										}
										
										if(count($elementos_formulario_calculo)){
						
											foreach($elementos_formulario_calculo as $elemento){
										
												$datos = json_decode($elemento["datos"], TRUE);
												$fecha_elemento = $datos["fecha"];
						
												$elemento_campos_dinamicos = array();
												foreach($datos as $key => $value){
													if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
														$elemento_campos_dinamicos[$key] = $datos[$key];
													}
												}
												
												if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
													
													if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador]; 
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													}	
						
												}
					
											}
						
										}
										
										if($valor_calculo->operador){
										
											$valor_operador = $valor_calculo->valor_operador;
											
											if($valor_calculo->operador == "+"){
												$total_valor_calculo = $total_valor_calculo + $valor_operador;
											}
											if($valor_calculo->operador == "-"){
												$total_valor_calculo = $total_valor_calculo - $valor_operador;
											}
											if($valor_calculo->operador == "*"){
												$total_valor_calculo = $total_valor_calculo * $valor_operador;
											}
											if($valor_calculo->operador == "/"){
												$total_valor_calculo = $total_valor_calculo / $valor_operador;
											}
											
										}
										
										if($operador){
											
											$array_valores_operacion_compuesta[] = array(
												$operador => $total_valor_calculo
											);
				
										}
										
									}
									// Fin Cálculo valores operación (cada valor se almacena en $array_valores_operacion_compuesta
									
									// Cálculo total final
									$total_valor_calculo_final = $total_valor_inicial;
									
									foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
										
										$operador = key($valor_operacion_compuesta);
										$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
										
										if($operador == "+"){
											$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
										}
										if($operador == "-"){
											$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
										}
										if($operador == "*"){
											$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
										}
										if($operador == "/"){
											$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
										}
				
									}
									
									$total_valor = $total_valor_calculo_final;
									// Fin Cálculo total final
									
									$id_unidad_valor = $valor->id_unidad;
									$id_tipo_unidad_valor = $valor->id_tipo_unidad;
									//$id_unidad_indicador = $datos_indicador["unidad"];
									$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => $id_tipo_unidad_valor))->id_unidad; 
									
									// transformar valores en reporte, que tengan una unidad distinta a la unidad del indicador.
									// si el valor tiene una unidad distinta a la del indicador, transformar a la unidad del indicador.
									//if($id_unidad_valor != $id_unidad_indicador){
									if($id_unidad_valor != $id_unidad_destino){
										if($id_unidad_indicador != 18){ // Unidad de tipo unidad (id 18) no tiene conversión
											$fila_conversion = $this->Conversion_model->get_one_where(
												array(
													"id_tipo_unidad" => $id_tipo_unidad_valor,
													"id_unidad_origen" => $id_unidad_valor,
													//"id_unidad_destino" => $id_unidad_indicador
													"id_unidad_destino" => $id_unidad_destino
												)
											);
											$valor_transformacion = $fila_conversion->transformacion;
											$total_valor = $total_valor * $valor_transformacion;
										}
									} 
									
								}
								
								$unidad = $this->Unity_model->get_one($datos_indicador["unidad"]);
								//$nombre_unidad = $unidad->nombre;
								//$nombre_unidad_real = $unidad->nombre_real;
								$unidad_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => $unidad->id_tipo_unidad));
								$unidad_client_config = $this->Unity_model->get_one($unidad_config->id_unidad);
								$nombre_unidad = $unidad_client_config->nombre;
								$nombre_unidad_real = $unidad_client_config->nombre_real;

								$tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad);
								$nombre_tipo_unidad = $tipo_unidad->nombre;

								if($valor->id){// Se asignó un valor al item
									//$valor = to_number_project_format($total_valor, $id_proyecto);
									$valor = to_number_client_format($total_valor, $id_cliente);
									
									echo '<input type="hidden" name="valor['.$nombre_indicador.'][valor]" value="'.$datos_indicador["valor"].'">';
									echo '<input type="hidden" name="valor['.$nombre_indicador.'][valor_cliente]" value="'.$datos_plantilla[$nombre_indicador]["valor_cliente"].'">';
								}else{// Se muestra el input text
									
									$disabled = "";
									if($puede_editar == 3){
										$disabled = "disabled";
									}
								
									$valor = form_input(array(
										"id" => "valor",
										//"name" => $datos_indicador["codigo"]."_valor",
										"name" => "valor[".$nombre_indicador."][valor_cliente]",
										//"value" => "",//$model_info->nombre_valor,
										"value" => $datos_plantilla[$nombre_indicador]["valor_cliente"],
										"class" => "form-control",
										"placeholder" => lang('value'),
										//"autofocus" => true,
										//"data-rule-required" => true,
										//"data-msg-required" => lang("field_required"),
										"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
										"data-msg-regex" => lang("number_or_decimal_required"),
										"autocomplete"=> "off",
										"maxlength" => "255",
										$disabled => $disabled
									));
								}
								
							?>
                        
                        	<tr>
                            	<td class=""><?php echo $datos_indicador["codigo"]; ?></td>
                            	<td class="hidden-xs"><?php echo lang($nombre_indicador); ?></td>
                                <!--<td class=""><?php echo ($valor->id) ? to_number_project_format($total_valor, $id_proyecto) : lang("no_information_available"); ?></td>-->
                                <td class=""><?php echo $valor; ?></td>
                                <td class="">
								<?php 
									
								    // Exepciones
									if($nombre_indicador == "operating_hours" || $nombre_indicador == "enel_hours_worked" 
									|| $nombre_indicador == "contractor_hours_worked" || $nombre_indicador == "enel_lost_days"
									|| $nombre_indicador == "contractor_lost_days"){
										echo $nombre_unidad_real;
									} else if($nombre_indicador == "expenses_local_suppliers" || $nombre_indicador == "opex_total"
												|| $nombre_indicador == "environmental_expenses"){
										echo "€";
									} else if($nombre_indicador == "noise_levels_near_population"){
										echo "db";
									} else {
										echo $nombre_unidad; 
									}
									
									//echo $nombre_tipo_unidad;
									
								?>
                                </td>
                                <td class="hidden-xs"><?php echo $datos_indicador["descripcion"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>        
                </table>
            </div>
        </div>
    </div>
    
    <div class="panel-footer clearfix">
        <div class="pull-right">
        	<?php if($puede_editar == 3) { ?>
                <div class="btn-group" role="group">
                	<a href="#" class="btn btn-primary" disabled="disabled"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></a>
                </div>
            <?php } else { ?>
            	<div class="btn-group" role="group">
                    <button type="submit" id="guardar_reporte" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
                </div>
            <?php } ?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success" id="export_excel" disabled="disabled"><i class="fa fa-table" aria-hidden="true"></i> <?php echo lang("export_to_excel"); ?></button>
            </div>
        </div>
    </div>
    
    <?php echo form_close(); ?>
    
</div>
<script type="text/javascript">
	$(document).ready(function () {
		
		/*$("#save_report-form").appForm({
            ajaxSubmit: false
        });*/
		var id_plantilla = '<?php echo $id_plantilla; ?>';
				
		$("#save_report-form").appForm({
            onSuccess: function(result) {
				
				if(result.plantilla_completa){
					id_plantilla = result.id_plantilla;
					$('#export_excel').prop('disabled', false);
				} else {
					$('#export_excel').prop('disabled', true);
				}
				
				appAlert.success(result.message, {duration: 10000});
				javascript_abort();			
				/*
				setTimeout(function() {
					location.reload();
				}, 500);
				*/
            }
        });
		
		function javascript_abort(){
		   throw new Error('This is not an error. This is just to abort javascript');
		}
		/*$("#kpi_report-form").submit(function(e){
			e.preventDefault();
			return false;
		});*/
		
		// Variable $plantilla_completa seteada al generar reporte kpi
		<?php if($plantilla_completa){ ?>
			$('#export_excel').prop('disabled', false);
		<?php } else { ?>
			$('#export_excel').prop('disabled', true);
		<?php } ?>
		
		$(document).on("click","#export_excel", function(e) {
			generar_excel(id_plantilla);
			e.preventDefault();
		});

		function generar_excel(id_plantilla){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("KPI_Report/get_excel/")?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$('<input type="hidden"/>').attr('name', 'id_plantilla').val(id_plantilla).appendTo($form);
			$form.submit();
		}
		
		
    });
</script>