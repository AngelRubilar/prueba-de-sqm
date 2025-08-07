<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo ucwords(lang("environmental_footprints"))." - ".lang("functional_units"); ?>
</h2>
<div align="center">
	<?php $hora = convert_to_general_settings_time_format($proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), $format = "H:i:s", $proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

  <?php if($puede_ver == 1) { ?>
  
        <?php foreach($unidades_funcionales as $unidad_funcional){ ?>

              <h2><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h2>
       		  <br>
            <?php
			
			$id_proyecto = $proyecto->id;
			$id_metodologia = $proyecto->id_metodologia;
			
			$nombre_uf = $unidad_funcional->nombre;
			$id_subproyecto_uf = $unidad_funcional->id_subproyecto;
			//$valor_uf = $unidad_funcional->valor;
            $valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, $start_date, $end_date);
			
            $html = '';
			
			$html .= '<div style="width: 100%;">';
			$html .= '<table cellspacing="0" cellpadding="0" border="0">';
			
			$loop = 1;
			
            foreach($huellas as $huella){
				//var_dump($criterios_calculos);
				
				if($loop % 4 == 1){
					$html .= '<tr>';
				}
				
				$html .= '<td style="text-align: center;">';

				$html .= '<table style="float: left;" border="0">';
				$html .= '<tr>';
				$html .= '<td style="text-align: center;">';
				
				
                $id_huella = $huella->id;
				$total_huella = 0;
				//$nombre_unidad_huella = $this->Unity_model->get_one($huella->id_unidad)->nombre;
				$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
						"id_cliente" => $client_id, 
						"id_proyecto" => $id_proyecto, 
						"id_tipo_unidad" => $huella->id_tipo_unidad, 
						"deleted" => 0
				))->id_unidad;
				
				$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
				
				// VALOR DE CONVERSION
				$id_tipo_unidad_origen = $huella->id_tipo_unidad;
				$id_unidad_origen = $huella->id_unidad;
				$fila_config_huella = $Module_footprint_units_model->get_one_where(
					array(
						"id_cliente" => $client_id,
						"id_proyecto" => $id_proyecto,
						"id_tipo_unidad" => $id_tipo_unidad_origen,
						"deleted" => 0
					)
				);
				$id_unidad_destino = $fila_config_huella->id_unidad;
				$fila_conversion = $Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => $id_tipo_unidad_origen,
						"id_unidad_origen" => $id_unidad_origen,
						"id_unidad_destino" => $id_unidad_destino
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;
				// FIN VALOR DE CONVERSION
				
				//$icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
				$icono = $huella->icono ? "assets/images/impact-category/".$huella->icono : "assets/images/impact-category/empty.png";
				$html .= '<img src="'.$icono.'" style="height:50px; width:50px;" />';
				$html .= "<br>";
				
				foreach($procesos_unitarios as $pu){
					
					$id_pu = $pu["id"];
					$nombre_pu = $pu["nombre"];
					$total_pu = 0;
					
					foreach($criterios_calculos as $criterio_calculo){
						
						$total_criterio = 0;
						
						$id_criterio = $criterio_calculo->id_criterio;
						$id_formulario = $criterio_calculo->id_formulario;
						$id_material = $criterio_calculo->id_material;
						$id_categoria = $criterio_calculo->id_categoria;
						$id_subcategoria = $criterio_calculo->id_subcategoria;
						
						/*
						$id_campo_sp = $criterio_calculo->id_campo_sp;
						$id_campo_pu = $criterio_calculo->id_campo_pu;
						$id_campo_fc = $criterio_calculo->id_campo_fc;
						$criterio_fc = $criterio_calculo->criterio_fc;
						*/
						
						/*SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO */
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
						/*FIN SECCION NUEVA DE CODIGO TIPOS DE TRATAMIENTO */	
						$ides_campo_unidad = json_decode($criterio_calculo->id_campo_unidad, true);
						/*
						// CONSULTAR LAS ASIGNACIONES DEL CRITERIO-CALCULO 
						// DONDE SP DESTINO = ID_SP Y PU DESTINO = ID_PU
						$asignaciones_de_criterio = $Assignment_model->get_details(
							array("id_criterio" => $id_criterio, 
							"sp_destino" => $id_subproyecto_uf, 
							"pu_destino" => $id_pu
							)
						)->result();
						*/
						
						// NUEVA ASIGNACION
						// CONSULTAR TODAS ASIGNACIONES DEL CRITERIO-CALCULO 
						$asignaciones_de_criterio = $Assignment_combinations_model->get_details(array("id_criterio" => $id_criterio))->result();
						//var_dump($asignaciones_de_criterio);
						
						// GUARDAR FILAS DE ASIGNACIONES EN UN ARREGLO BIDIMENSIONAL
						/*$array_asignaciones = array();
						foreach($asignaciones_de_criterio as $asignacion){
							$array_asignaciones[] = array(
								"criterio_sp" => $asignacion->criterio_sp,
								"criterio_pu" => $asignacion->criterio_pu
							);
						}*/
						//echo json_encode($array_asignaciones).'<br>';
						
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
							// Para graficos
							//$array_unidades_proyecto[$id_unidad] = $fila_unidad->nombre;
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
							/*
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
								
								foreach($array_asignaciones as $array_asignacion){
									if($array_asignacion["criterio_sp"] == $valor_campo_sp){
										$total_elemento += $total_elemento_interno;
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
								
								foreach($array_asignaciones as $array_asignacion){
									if($array_asignacion["criterio_pu"] == $valor_campo_pu){
										$total_elemento += $total_elemento_interno;
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
										$total_elemento += $total_elemento_interno;
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
								//var_dump($asignaciones_de_criterio);
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
							
							$total_criterio += $total_elemento;
						}// FIN ELEMENTO

						$total_pu += $total_criterio;
						
					}// FIN CRITERIO-CALCULO
					
					$total_pu = $total_pu/$valor_uf;
					$total_huella += $total_pu;
				
				}// FIN PROCESO UNITARIO
                
				$total_huella *= $valor_transformacion;
				
                //$total_huella_por_uf = ($array_cifras_huellas[$id_huella])/$unidad_funcional->valor;
				//$total_huella_por_uf = ($total_huella)/$unidad_funcional->valor;
                    
                //$html .= '<div class="text-center p15">2,07*10<sup>2</sup></div>';
                $html .= to_number_project_format($total_huella,$id_proyecto).'<br>';
                $html .= $huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') <br><br>';

				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</table>';
				
				$html .= '</td>';
				
				if($loop % 4 == 0 || $loop == count($huellas)){
					$html .= '</tr>';
				}
				
				$loop++;
				
            }
			
			$html .= '</table>';
			$html .= '</div>';
            echo $html;
            ?>
            
        <!-- <br pagebreak="true"> -->
        
        <?php } ?>
  
  <?php } else { ?>
  
  <div style="width: 100%;"> 
  	<?php echo lang("content_disabled"); ?>
  </div>
  
  <?php } ?>

</body>