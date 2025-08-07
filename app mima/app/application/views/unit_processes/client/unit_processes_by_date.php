	<div id="unit_processes_group">
   
       <div class="col-sm-3 col-lg-2">
        <ul class="nav nav-tabs vertical" role="tablist">
            <?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
            <?php $active = ($key == 0)? "active":""; ?>
                <li class="<?php echo $active; ?>"><a data-toggle="tab" href="#<?php echo $unidad_funcional->id; ?>_unidad_funcional"><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></a></li>
            <?php } ?>
        </ul>
       </div>
       
       <div role="tabpanel" class="tab-pane fade active in" id="graficos_procesos" style="min-height: 200px;">
           <div class="tab-content">
           
           <?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
           <?php $active = ($key == 0)? "active":""; ?>
               <div id="<?php echo $unidad_funcional->id; ?>_unidad_funcional" class="tab-pane fade in <?php echo $active; ?>">
                   <div class="col-sm-9 col-lg-10 p0">
                       <div class="panel">
                            <div class="panel-default panel-heading">
                                <h4><?php echo $unidad_funcional->nombre; ?></h4>
                            </div>
                            <div class="panel-body">
                            
                                <!-- START ROW -->
                                <div class="row">
                                      <?php foreach($huellas as $huella){ ?>
                                      <?php
                                      
                                      $id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
                                            "id_cliente" => $client_info->id, 
                                            "id_proyecto" => $project_info->id, 
                                            "id_tipo_unidad" => $huella->id_tipo_unidad, 
                                            "deleted" => 0
                                       ))->id_unidad;
                                            
                                       $nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
                                                                        
                                       ?>
                                      
                                         <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 col-xl-2">
                                            <div class="panel panel-default">
                                               <div class="page-title clearfix panel-success">
                                                  <!--<h3>Cambio climático</h3> -->
                                                  <div class="pt10 pb10 text-center"> <?php echo $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')'; ?> </div>
                                               </div>
                                               <div class="panel-body">
                                                     <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                                  <div id="grafico_<?php echo $huella->id?>-uf_<?php echo $unidad_funcional->id?>" style="height: 240px;" class="chart"></div>
                                               </div>
                                            </div>
                                         </div>
                                       
                                       <?php } ?>
                                 </div>
                                 <!-- END ROW -->
                                 
                                 <div class="table-responsive">
                                     <table id="<?php echo $unidad_funcional->id; ?>_uf-table" class="display" cellspacing="0" width="100%">            
                                     </table>
                                 </div>
                                
                            </div>
                       </div>
                   </div>
               </div>
           <?php } ?>
                    
            </div>
        </div>
   
    <?php
        $id_proyecto = $project_info->id;
        $id_metodologia = $project_info->id_metodologia;
    ?>

</div>

<style>
table[id$=_uf-table] th { font-size: 12px; }
table[id$=_uf-table] td { font-size: 11px; }
</style>
<!--<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>-->
<script type="text/javascript">


$(document).ready(function () {
	
	adaptarAltura();
	
	function adaptarAltura(e){
		
		if(e){
			var id_tab = $(e.target).attr("href");
		}else{
			var id_tab = "#"+$("#graficos_procesos .tab-pane:first").attr("id");
		}
		
		// cabezera graficos
		var maxHeight = Math.max.apply(null, $(id_tab+" > div > div > div.panel-body > div > div > div > div.page-title.clearfix.panel-success").map(function (){
			return $(this).height();
		}).get());
		
		$(id_tab+" > div > div > div.panel-body > div > div > div > div.page-title.clearfix.panel-success").height(maxHeight);
		
		// contenido graficos
		var maxHeight2 = Math.max.apply(null, $(id_tab+" > div > div > div.panel-body > div > div > div.panel").map(function (){
			return $(this).height();
		}).get());
		
		$(id_tab+" > div > div > div.panel-body > div > div > div.panel").height(maxHeight2);
	}
	
	$('#page-content a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		adaptarAltura(e);
		$('.chart').each(function() { 
			$(this).highcharts().reflow();
		});
	});
	
	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	var decimal_numbers = AppHelper.settings.decimalNumbers;	
	
	<?php
	
	foreach($unidades_funcionales as $key => $unidad_funcional){
		
		$nombre_uf = $unidad_funcional->nombre;
		$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
		//$valor_uf = $unidad_funcional->valor;
		$valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $start_date, $end_date);
		
	   'console.log("'.$valor_uf.'");';
		//echo 'console.log("'.$unidad_funcional->id.' / '.$id_subproyecto_uf.'");';
		
		foreach($huellas as $huella){
		
			$id_huella = $huella->id;
			$total_huella = 0;
			$array_valores_pu = array();
			$array_colores_pu = array();
			
			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
				"id_cliente" => $client_info->id, 
				"id_proyecto" => $project_info->id, 
				"id_tipo_unidad" => $huella->id_tipo_unidad, 
				"deleted" => 0
			))->id_unidad;
			
			$nombre_unidad_huella = $Unity_model->get_one($id_unidad_huella_config)->nombre;
			
			foreach($procesos_unitarios as $pu){
				
				$id_pu = $pu["id"];
				$nombre_pu = $pu["nombre"];
				$total_pu = 0;
				
				foreach($criterios_calculos as $criterio_calculo){
					
					$id_criterio = $criterio_calculo->id_criterio;
					$id_formulario = $criterio_calculo->id_formulario;
					$id_material = $criterio_calculo->id_material;
					$id_categoria = $criterio_calculo->id_categoria;
					$id_subcategoria = $criterio_calculo->id_subcategoria;
					
					/* NO BORRAR
					$id_campo_sp = $criterio_calculo->id_campo_sp;
					$id_campo_pu = $criterio_calculo->id_campo_pu;
					$id_campo_fc = $criterio_calculo->id_campo_fc;
					$criterio_fc = $criterio_calculo->criterio_fc;
					*/
					
					/*SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC */
					if(isset($criterio_calculo->tipo_by_criterio)){
						$j_datos = json_decode($criterio_calculo->tipo_by_criterio,true);

						if($j_datos["id_campo_sp"] == "1"){
							$id_campo_sp ="tipo_tratamiento";
						}else{
							$id_campo_sp = $criterio_calculo->id_campo_sp;
						}

						if($j_datos["id_campo_pu"] == "1"){
							$id_campo_pu ="tipo_tratamiento";
						}else{
							$id_campo_pu = $criterio_calculo->id_campo_pu;
						}

						if($j_datos["id_campo_fc"] == "1"){
							$id_campo_fc ="tipo_tratamiento";
						}else{
							$id_campo_fc = $criterio_calculo->id_campo_fc;
						}

					}else{
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
					}

					if($criterio_calculo->criterio_fc == "Disposición"){
						$value= $this->Tipo_tratamiento_model->get_one_where(array("nombre" =>"Disposición" ,"deleted"=>0));
						$criterio_fc = $value->id;
					}else if($criterio_calculo->criterio_fc == "Reutilización"){
						$value= $this->Tipo_tratamiento_model->get_one_where(array("nombre" =>"Reutilización" ,"deleted"=>0));
						$criterio_fc = $value->id;

					}else if($criterio_calculo->criterio_fc == "Reciclaje"){
						$value= $this->Tipo_tratamiento_model->get_one_where(array("nombre" =>"Reciclaje" ,"deleted"=>0));
						$criterio_fc = $value->id;

					}else{
						$criterio_fc = $criterio_calculo->criterio_fc;
					}
					/*FIN SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO E IDS CAMPOS SP,PU,FC Y CRITERIO FC */
					
					$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
					
					/*
					// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
					// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
					$asignaciones_de_criterio = $Assignment_model->get_details(
						array("id_criterio" => $id_criterio, 
						"sp_destino" => $id_subproyecto_uf, 
						"pu_destino" => $id_pu
						)
					)->result();*/
					$asignaciones_de_criterio = $Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
					/*
					// GUARDAR FILAS DE ASIGNACIONES EN UN ARREGLO BIDIMENSIONAL
					$array_asignaciones = array();
					foreach($asignaciones_de_criterio as $asignacion){
						$array_asignaciones[] = array(
							"criterio_sp" => $asignacion->criterio_sp,
							"criterio_pu" => $asignacion->criterio_pu
						);
					}
					//echo json_encode($array_asignaciones).'<br>';
					*/
					
					// CONSULTAR CAMPOS UNIDAD DEL RA
					$array_unidades = array();
					$array_id_unidades = array();
					$array_id_tipo_unidades = array();

					foreach($ides_campo_unidad as $id_campo_unidad){

						if($id_campo_unidad == 0){
							$id_formulario = $criterio_calculo->id_formulario;
							$form_data = $Forms_model->get_one_where(array("id"=>$id_formulario, "deleted"=>0));
							$json_unidad_form = json_decode($form_data->unidad,true);

							$id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
							$id_unidad = $json_unidad_form["unidad_id"];

							$fila_unidad = $Unity_model->get_one_where(array("id"=>$id_unidad, "deleted"=>0));
							$array_unidades[] = $fila_unidad->nombre;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}else{
							$fila_campo = $Fields_model->get_one_where(array("id"=>$id_campo_unidad,"deleted"=>0));
							$info_campo = $fila_campo->opciones;
							$info_campo = json_decode($info_campo, true);

							$id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
							$id_unidad = $info_campo[0]["id_unidad"];

							$fila_unidad = $Unity_model->get_one_where(array("id"=>$id_unidad,"deleted"=>0));
							$array_unidades[] = $fila_unidad->nombre;
							$array_id_unidades[] = $id_unidad;
							$array_id_tipo_unidades[] = $id_tipo_unidad;
						}
					}
					
					// OBTENER UNIDAD FINAL
					// Se ampliaron unidades de cálculo 
					if(count($array_id_unidades) == 1){
						$id_unidad = $array_id_unidades[0];
					}elseif(count($array_id_unidades) == 2){
						
						if($array_id_unidades[0] == 18 && $array_id_unidades[1] != 18){
							$id_unidad = $array_id_unidades[1];
						}elseif($array_id_unidades[0] != 18 && $array_id_unidades[1] == 18){
							$id_unidad = $array_id_unidades[0];
						}elseif(in_array(9, $array_id_unidades) && in_array(1, $array_id_unidades)){
							$id_unidad = 5;
						}elseif(in_array(9, $array_id_unidades) && in_array(2, $array_id_unidades)){
							$id_unidad = 6;
						}
						
					}elseif(count($array_id_unidades) == 3){
						
						if(
							in_array(18, $array_id_unidades) && 
							in_array(9, $array_id_unidades) && 
							in_array(1, $array_id_unidades)
						){
							$id_unidad = 5;
						}elseif(
							in_array(18, $array_id_unidades) && 
							in_array(9, $array_id_unidades) && 
							in_array(2, $array_id_unidades)
						){
							$id_unidad = 6;
						}else{
							
						}
						
					}else{
						
					}
					
					// CONSULTAR FC
					$fila_factor = $Characterization_factors_model->get_one_where(
						array(
							"id_metodologia" => $id_metodologia,
							"id_huella" => $id_huella,
							"id_material" => $id_material,
							"id_categoria" => $id_categoria,
							"id_subcategoria" => $id_subcategoria,
							"id_unidad" => $id_unidad,
							"deleted" => 0
						)
					);
					
					$valor_factor = 0;
					if($fila_factor->id){
						$valor_factor = $fila_factor->factor;
					}
					
					// UNA VEZ QUE YA TENGO FC PARA A NIVEL DE CRITERIO(RA) - CALCULO, RECORRO LOS ELEMENTOS ASOCIADOS
					$elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria, $start_date, $end_date)->result();

					foreach($elementos as $elemento){
						$total_elemento = 0;
						$datos_decoded = json_decode($elemento->datos, true);

						$mult = 1;
						/* NO BORRAR.
						foreach($ides_campo_unidad as $id_campo_unidad){
							$mult *= $datos_decoded[$id_campo_unidad];
						}
						*/
						foreach($ides_campo_unidad as $id_campo_unidad){
							if($id_campo_unidad == 0){
								$mult *= $datos_decoded["unidad_residuo"];
							}else{
								$mult *= $datos_decoded[$id_campo_unidad];
							}
						}
						// AL CALCULAR A NIVEL DE ELEMENTO, EL RESULTADO MULTIPLICARLO POR EL FC
						
						$total_elemento_interno = $mult * $valor_factor;
						
						// IF VALOR DE CAMPO DE CRITERIO SP EN CRITERIO = VALOR DE CRITERIO SP DE ARRAY DE ASIGNACIONES Y
						// VALOR DE CAMPO DE CITERIO PU EN CRITERIO = VALOR DE CRITERIO UF DE ARRAY DE ASIGNACIONES
						
						/*if($id_campo_sp && !$id_campo_pu){
							$valor_campo_sp = $datos_decoded[$id_campo_sp];
							
							//$array_a_buscar = array("criterio_sp" => $valor_campo_sp, "criterio_pu" => "");
							//echo '<br>'.json_encode($array_a_buscar).'<br>-----------------------<br>';
							foreach($array_asignaciones as $array_asignacion){
								if($array_asignacion["criterio_sp"] == $valor_campo_sp){
									$total_pu += $total_elemento;
								}
							}
						}*/
						
						if($id_campo_sp && !$id_campo_pu){
							
							if($id_campo_sp == "tipo_tratamiento"){
								$value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
								$valor_campo_sp = $value->nombre;
							}else{
								$valor_campo_sp = $datos_decoded[$id_campo_sp];
							}
							
							//$valor_campo_sp = $datos_decoded[$id_campo_sp];
							
							foreach($asignaciones_de_criterio as $obj_asignacion){
								
								$criterio_sp = $obj_asignacion->criterio_sp;
								$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
								$sp_destino = $obj_asignacion->sp_destino;
								$porcentajes_sp = $obj_asignacion->porcentajes_sp;
								
								$criterio_pu = $obj_asignacion->criterio_pu;
								$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
								$pu_destino = $obj_asignacion->pu_destino;
								$porcentajes_pu = $obj_asignacion->porcentajes_pu;
								
								if($tipo_asignacion_sp == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
									
									if($criterio_sp == $valor_campo_sp){
										$total_elemento += $total_elemento_interno;
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
									}
									
								}else if($tipo_asignacion_sp == "Porcentual" && $pu_destino == $id_pu){
									
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
									if($porcentaje_sp != 0){
										$porcentaje_sp = ($porcentaje_sp/100);
									}
									
									if($criterio_sp == $valor_campo_sp){
										$total_elemento += ($total_elemento_interno * $porcentaje_sp);
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.'<br>';
									}
								}
							}
						}

						/*if(!$id_campo_sp && $id_campo_pu){
							$valor_campo_pu = $datos_decoded[$id_campo_pu];
							
							//$array_a_buscar = array("criterio_sp" => $valor_campo_sp, "criterio_pu" => "");
							//echo '<br>'.json_encode($array_a_buscar).'<br>-----------------------<br>';
							foreach($array_asignaciones as $array_asignacion){
								if($array_asignacion["criterio_pu"] == $valor_campo_pu){
									$total_pu += $total_elemento;
								}
							}
						}*/

						if(!$id_campo_sp && $id_campo_pu){
							
							if($id_campo_pu == "tipo_tratamiento"){
								$value = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
								$valor_campo_pu = $value->nombre;
							}else{
								$valor_campo_pu = $datos_decoded[$id_campo_pu];
							}
							
							//$valor_campo_pu = $datos_decoded[$id_campo_pu];
							
							foreach($asignaciones_de_criterio as $obj_asignacion){
								
								
								$criterio_sp = $obj_asignacion->criterio_sp;
								$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
								$sp_destino = $obj_asignacion->sp_destino;
								$porcentajes_sp = $obj_asignacion->porcentajes_sp;
								
								$criterio_pu = $obj_asignacion->criterio_pu;
								$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
								$pu_destino = $obj_asignacion->pu_destino;
								$porcentajes_pu = $obj_asignacion->porcentajes_pu;
								
								if($tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
									
									if($criterio_pu == $valor_campo_pu){
										$total_elemento += $total_elemento_interno;
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.($total_elemento_interno).'<br>';
									}
									
								}else if($tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
									
									$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
									$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
									if($porcentaje_pu != 0){
										$porcentaje_pu = ($porcentaje_pu/100);
									}
									
									if($criterio_pu == $valor_campo_pu){
										$total_elemento += ($total_elemento_interno * $porcentaje_pu);
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
									}
								}
							}
						}
						
						/*if($id_campo_sp && $id_campo_pu){
							$valor_campo_sp = $datos_decoded[$id_campo_sp];
							$valor_campo_pu = $datos_decoded[$id_campo_pu];
							
							foreach($array_asignaciones as $array_asignacion){
								if($array_asignacion["criterio_sp"] == $valor_campo_sp && $array_asignacion["criterio_pu"] == $valor_campo_pu){
									$total_pu += $total_elemento;
								}
							}
						}*/
						
						if($id_campo_sp && $id_campo_pu){
							
							if(($id_campo_pu == "tipo_tratamiento")&&($id_campo_sp == "tipo_tratamiento")){

								$value_sp = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
								$valor_campo_sp = $value_sp->nombre;
								$value_pu = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
								$valor_campo_pu = $value_pu->nombre;

							}else{
								$valor_campo_sp = $datos_decoded[$id_campo_sp];
								$valor_campo_pu = $datos_decoded[$id_campo_pu];
							}
							/*
							$valor_campo_sp = $datos_decoded[$id_campo_sp];
							$valor_campo_pu = $datos_decoded[$id_campo_pu];
							*/
							foreach($asignaciones_de_criterio as $obj_asignacion){
								
								$criterio_sp = $obj_asignacion->criterio_sp;
								$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
								$sp_destino = $obj_asignacion->sp_destino;
								$porcentajes_sp = $obj_asignacion->porcentajes_sp;
								
								$criterio_pu = $obj_asignacion->criterio_pu;
								$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
								$pu_destino = $obj_asignacion->pu_destino;
								$porcentajes_pu = $obj_asignacion->porcentajes_pu;
								
								if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
									
									if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$total_elemento += $total_elemento_interno;
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.'<br>';
									}
									
								}else if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Porcentual" && $sp_destino == $id_subproyecto_uf){
									$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
									$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
									if($porcentaje_pu != 0){
										$porcentaje_pu = ($porcentaje_pu/100);
									}
									
									if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$total_elemento += ($total_elemento_interno * $porcentaje_pu);
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_pu.'<br>';
									}
									
								}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Total" && $pu_destino == $id_pu){
									
									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
									if($porcentaje_sp != 0){
										$porcentaje_sp = ($porcentaje_sp/100);
									}
									
									if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$total_elemento += ($total_elemento_interno * $porcentaje_sp);
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.'<br>';
									}
									
								}else if($tipo_asignacion_sp == "Porcentual" && $tipo_asignacion_pu == "Porcentual"){
									
									//echo $porcentajes_sp.'|'.$porcentajes_pu.'<br>';

									$porcentajes_sp_decoded = json_decode($porcentajes_sp, true);
									$porcentaje_sp = $porcentajes_sp_decoded[$id_subproyecto_uf];
									if($porcentaje_sp != 0){
										$porcentaje_sp = ($porcentaje_sp/100);
									}

									$porcentajes_pu_decoded = json_decode($porcentajes_pu, true);
									$porcentaje_pu = $porcentajes_pu_decoded[$id_pu];
									if($porcentaje_pu != 0){
										$porcentaje_pu = ($porcentaje_pu/100);
									}
									
									if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$total_elemento += ($total_elemento_interno * $porcentaje_sp * $porcentaje_pu);
										//echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento_interno.' * '.$porcentaje_sp.' * '.$porcentaje_pu.'<br>';
									}
								}
							}
						}
						
						if(!$id_campo_sp && !$id_campo_pu){
						
							foreach($asignaciones_de_criterio as $obj_asignacion){
								
								$criterio_sp = $obj_asignacion->criterio_sp;
								$tipo_asignacion_sp = $obj_asignacion->tipo_asignacion_sp;
								$sp_destino = $obj_asignacion->sp_destino;
								$porcentajes_sp = $obj_asignacion->porcentajes_sp;
								
								$criterio_pu = $obj_asignacion->criterio_pu;
								$tipo_asignacion_pu = $obj_asignacion->tipo_asignacion_pu;
								$pu_destino = $obj_asignacion->pu_destino;
								$porcentajes_pu = $obj_asignacion->porcentajes_pu;
								
								if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
									
									//if($criterio_sp == $valor_campo_sp && $criterio_pu == $valor_campo_pu){
										$total_elemento += $total_elemento_interno;
									//}
									
								}
							}
						}
						
						
						$total_pu += $total_elemento;
					}
				}

				$total_pu = $total_pu/$valor_uf;
				$total_huella += $total_pu;
				$array_valores_pu[] = array("nombre_pu" => $nombre_pu, "total_pu" => $total_pu);
				$array_colores_pu[] = ($pu["color"]) ? $pu["color"] : "#00b393";
			}
			
			$array_data = array();
			foreach($array_valores_pu as $dato_pu){
				if($dato_pu["total_pu"] == 0){
					$porc_pu = 0;
				}else{
					$porc_pu = ($dato_pu["total_pu"]*100)/$total_huella;
				}
				
				$array_data[] = array("name" => $dato_pu["nombre_pu"], "y" => $porc_pu);
			}
			
			$nombre_grafico = $client_info->sigla.'_'.$project_info->sigla.'_PU_'.$huella->abreviatura.'_'.$nombre_unidad_huella.'_'.date("Y-m-d");

			?>
			
			$('#grafico_<?php echo $huella->id; ?>-uf_<?php echo $unidad_funcional->id; ?>').highcharts({
				chart: {
				   plotBackgroundColor: null,
				   plotBorderWidth: null,
				   plotShadow: false,
				   type: 'pie',
				   events: {
					   load: function() {
						   if (this.options.chart.forExport) {
							   Highcharts.each(this.series, function (series) {
								   series.update({
									   dataLabels: {
										   enabled: true,
										}
									}, false);
								});
								this.redraw();
							}
						}
					}
				},
				title: {
				   text: '',
				},
				credits: {
				   enabled: false
				},
				tooltip: {
				   formatter: function() {
				       return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
				   },
				  // pointFormat: '{series.name}: <b>{point.y}%</b>'
				},
				plotOptions: {
					pie: {
						//size: 80,
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false,
							format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
								fontSize: "9px",
								distance: -30
							},
							crop: false
						},
						showInLegend: true
					}
				},
				legend: {
				   enabled: true,
				   itemStyle:{
					fontSize: "9px"
				   }
				},
				exporting: {
					filename: "<?php echo $nombre_grafico; ?>",
					buttons: {
						contextButton: {
							menuItems: [{
								text: "<?php echo lang('export_to_png'); ?>",
								onclick: function() {
									this.exportChart();
								},
								separator: false
							}]
						}
					}
				},
				colors: <?php echo json_encode($array_colores_pu);?>,
				series: [{
				   name: 'Porcentaje',
				   colorByPoint: true,
				   
				   data: <?php echo json_encode($array_data);?>
				}]
			});
		
		<?php } ?>
		
		$("#<?php echo $unidad_funcional->id; ?>_uf-table").appTable({
			<?php if ($start_date && $end_date){ ?>
			source: '<?php echo_uri("unit_processes/list_data/".$id_subproyecto_uf."/".$unidad_funcional->id."/".$start_date."/".$end_date); ?>',
			<?php } else { ?>
			source: '<?php echo_uri("unit_processes/list_data/".$id_subproyecto_uf."/".$unidad_funcional->id); ?>',
			<?php } ?>
			columns: [
				{title: "", "class": "text-center w50"},
				{title: "ID", "class": "text-center w50 hide"},
				{title: "<?php echo lang("unit_process") ?>", "class": "text-center w50"}
				<?php echo $columnas; ?>,
				//{title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
			],
			order: [[1, "asc"]],
			/*scrollX:true,
			fixedColumns:{
				leftColumns: 3
			}*/
			//printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5]),
			//xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5])
		});
		
		$("#<?php echo $unidad_funcional->id; ?>_uf-table").on('click', 'a.details-control', function () {
			var table = $("#<?php echo $unidad_funcional->id; ?>_uf-table").DataTable();
			var tr = $(this).closest('tr');
			var row = table.row(tr);
	 
			if (row.child.isShown()) {
				// This row is already open - close it
				/*row.child.hide();
				tr.removeClass('shown');
				$(this).html('<i class="fa fa-plus-circle font-16"></i>');*/
				$('div.slider', row.child()).slideUp(function () {
					row.child.hide();
					tr.removeClass('shown');
				});
				$(this).html('<i class="fa fa-plus-circle font-16"></i>');
				
			}else{
				// Open this row
				row.child(format(row.data())).show();
				tr.addClass('shown');
				//$('div.slider', row.child()).slideDown('slow');
				
				row.child().find('td:first').css('padding', '0');
				row.child().find('td:first table > tbody tr:first td').each(function(index, td){
					$(td).css('width', (tr.children('td:eq('+(index+1)+')').width()));
				});
				
				$(this).html('<i class="fa fa-minus-circle font-16"></i>');
			}
		} );
		
		
	<?php } ?>
	
	
	function format(d){
		
		var html = '<div class="table-responsive slider"><table class="table">';
		
		html += '<thead><tr><th></th><th class=" text-center"><?php echo lang("category"); ?></th><th colspan="'+d.num_huellas+'"></th></tr></thead>';
		$.each(d.categorias, function(categoria, huellas){
			html += '<tr>';
			html += '<td class=" text-center"></td>';
			html += '<td class=" text-center">'+categoria+'</td>';
			$.each(huellas, function(huella, valor){
				html += '<td>'+valor+'</td>';
			});
			
			html += '</tr>';
		});
		html += '</table></div>';
		
		return html;
	}
		
});
</script> 