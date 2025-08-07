<div id="page-content" class="p20 clearfix">
	
<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$proyecto->id); ?>"><?php echo $proyecto->title; ?></a>
</nav>


  <div class="row mb20">
  
    <div class="col-md-12">
      <div class="page-title clearfix" style="background-color:#FFF;">
        <div class="col-md-2 col-sm-6 pt10"> <span class="avatar avatar-lg chart-circle border-circle"> <img src="<?php echo $proyecto->icono?get_file_uri("assets/images/icons/".$proyecto->icono):get_file_uri("assets/images/icons/empty.png"); ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded shadow-2"> </span> </div>
        <div class="col-md-4 col-sm-6">
          <h3><span><?php echo $proyecto->title; ?></span></h3>
          <div class="pt10 pb10 b-t b-b"> <?php echo lang("start_date") . ': ' . get_date_format($proyecto->start_date,$proyecto->id)/*$proyecto->start_date;*/ ?></div>
          <div class="pt10 pb10 b-b"> <?php echo lang("deadline") . ': ' . get_date_format($proyecto->deadline,$proyecto->id) /*$proyecto->deadline*/ ?></div>
          <div class="pt10 pb10 b-b"> <?php echo lang("industry") . ': ' . $rubro; ?> </div>
          <div class="pt10 pb10 b-b"> <?php echo lang("subindustry") . ': ' . $subrubro; ?> </div>
        </div>
        <div class="col-md-6 col-sm-12 pt10" style="text-align:justify;"><?php echo $proyecto->description; ?></div>
      </div>
    </div>
    
  </div>
  <?php 
  	  $visible_total_impacts;
	  $visible_impacts_by_functional_units;
	  foreach($environmental_footprints_settings as $setting) { 
			if($setting->informacion == "total_impacts"){
				$visible_total_impacts = ($setting->habilitado == 1) ? TRUE : FALSE;
			}
			if($setting->informacion == "impacts_by_functional_units"){
				$visible_impacts_by_functional_units = ($setting->habilitado == 1) ? TRUE : FALSE;
			}
	  } 
  ?>
  
  <?php 
 		//$huellas = $Project_rel_footprints_model->get_footprints_of_project($proyecto->id)->result();
		//$calculos = $Calculation_model->get_calculations_field_ids_of_project($client_id, $proyecto->id)->result();
   ?>

	<?php if(count($air_sectors)){ // Módulo de Pronósticos ?>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-group" id="accordion_alert_air">

						<?php $loop_count = 0; ?>
						<?php foreach($stations_of_sectors_by_model as $id_sector => $stations_by_model){ ?>

							<?php $sector = $this->Air_sectors_model->get_one($id_sector); ?>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#sector_<?php echo $id_sector; ?>" data-parent="#accordion_alert_air" class="accordion-toggle">
											<h4 style="font-size:16px">
												<i class="<?php echo $loop_count == 0 ? "fa fa-minus-circle font-16" : "fa fa-plus-circle font-16"; ?> "></i> <?php echo $sector->name; ?>
											</h4>
										</a>
									</h4>
								</div>
								
								<div id="sector_<?php echo $id_sector; ?>" class="panel-collapse collapse <?php echo $loop_count == 0 ? " in" : ""; ?>">

									<div class="panel-body">

										<?php if(count($stations_by_model)){ ?>

											<?php if($stations_by_model[3]){ // Si la estación del sector tiene el modelo Numérico ?>

												<?php $model = $this->Air_models_model->get_one(3); ?>

												<div class="col-md-12">
													<div class="pull-right">
														<?php $id_sector_encrypt = urlencode($this->encrypt->encode($id_sector)); ?>
														<p><em><?php echo lang("forecasts_corresponding_to"); ?> </em>  <a href="<?php echo ($puede_ver_sectores != 3) ? get_uri("air_forecast_sectors/index/".$id_sector_encrypt."#div_numerical_model") : "#"; ?>" class="btn btn-success btn-sm" <?php echo ($puede_ver_sectores != 3) ? "" : "disabled style'pointer-events: none;'"; ?> ><?php echo lang("model")." ".lang($model->name); ?></a></p>
													</div>
												</div>

												<?php foreach($stations_by_model[3] as $id_station => $variables){ ?>

													<?php $station = $this->Air_stations_model->get_one($id_station); ?>
													
													<div class="col-md-6" style="/*text-align: justify;*/">
														<div class="estacion">
															<div class="page-title clearfix panel-success">
																<div class="pt10 pb10 pl10 text-left"><strong><?php echo $station->name; ?></strong></div>
															</div>
															<div class="panel-body p0">
																<table class="table table-bordered">
																	<tbody>
																		<tr>
																			<td rowspan="4" style="vertical-align: middle">
																				<div class="col-md-2 col-sm-6 pt10 text-center"> 
																					<span class=""> 
																						<img src="<?php echo get_file_uri("assets/images/icons/caseta.png"); ?>" alt="..." heigth='100' width='100'>  
																					</span>
																				</div>
																			</td>
																			<td colspan="2"><strong><?php echo lang("next_hour"); ?></strong></td>
																		</tr>
																		<tr>
																			<td>
																				<?php foreach($variables as $variable){ ?>
																				
																					<?php if($variable->id_air_variable_type == 1){ ?> 
																						<?php 
																							$variable_data = $Dashboard_controller->get_forecast_variable_data(array(
																								"id_sector" => $id_sector,
																								"id_station" => $id_station,
																								"id_model" => 3,
																								"id_variable" => $variable->id_air_variable
																							));
																						?>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center mb15" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8 mb15" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$variable_data["range"]." ".$variable_data["unit_name"]; ?></label></div><br>
																					<?php } ?> 
		
																				<?php } ?>
																			</td>
																			<td>
																				<?php foreach($variables as $variable){ ?>
																					<?php if($variable->id_air_variable_type == 2){ ?> 
																						<?php 
																							$variable_data = $Dashboard_controller->get_forecast_variable_data(array(
																								"id_sector" => $id_sector,
																								"id_station" => $id_station,
																								"id_model" => 3,
																								"id_variable" => $variable->id_air_variable
																							));
																						?>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center mb15" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8 mb15" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$variable_data["range"]." ".$variable_data["unit_name"]; ?></label></div><br>
																					<?php } ?> 
																				<?php } ?>
																			</td>
																		</tr>
																		<?php foreach($variables as $variable){ ?>

																			<?php if($variable->id_air_variable == 8){ // Si la variable es SO2, mostrar Próximo evento crítico ?> 
																				<?php 
																					$next_critical_event = $Dashboard_controller->get_next_critical_event(array(
																						"id_sector" => $id_sector,
																						"id_station" => $id_station,
																						"id_model" => 3,
																						"id_variable" => $variable->id_air_variable
																					));
																				?>
																				<tr>
																					<td colspan="2"><strong><?php echo lang("next_critical_event"); ?></strong></td>
																				</tr>
																				<tr>
																					<td>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$next_critical_event["next_critical_event"]; ?></label></div><br>
																					</td>
																					<td>
																						<label style="font-size: 14px; "><?php echo $next_critical_event["html_action_plan"]; ?></label>
																						<div id="action_plan_content-<?php echo $id_sector."-"."3"."-".$id_station."-".$variable->id_air_variable; ?>" class="hide">
																							<?php echo $next_critical_event["html_action_plan_content"]?>
																						</div>
																					</td>
																				</tr>
																				<?php break; ?>
																			<?php } ?>

																		<?php } ?>

																	</tbody>
																</table>
															</div>
														</div>
													</div>
													
												<?php } ?>

											<?php } ?>



											<?php //if($stations_by_model[2]){ // Si la estación del sector tiene el modelo Estadístico ?>
											<?php if(false){ // Momentaneamente oculto para Chagres ?>

												<?php $model = $this->Air_models_model->get_one(2); ?>

												<div class="col-md-12">
													<div class="pull-right">
														<?php $id_sector_encrypt = urlencode($this->encrypt->encode($id_sector)); ?>
														<p><em><?php echo lang("forecasts_corresponding_to"); ?> </em>  <a href="<?php echo ($puede_ver_sectores != 3) ? get_uri("air_forecast_sectors/index/".$id_sector_encrypt."#div_stat_model") : "#"; ?>" class="btn btn-success btn-sm" <?php echo ($puede_ver_sectores != 3) ? "" : "disabled style'pointer-events: none;'"; ?> ><?php echo lang("model")." ".lang($model->name); ?></a></p>
													</div>
												</div>

												<?php foreach($stations_by_model[2] as $id_station => $variables){ ?>

													<?php $station = $this->Air_stations_model->get_one($id_station); ?>
													
													<div class="col-md-6" style="/*text-align: justify;*/">
														<div class="estacion">
															<div class="page-title clearfix panel-success">
																<div class="pt10 pb10 pl10 text-left"><strong><?php echo $station->name; ?></strong></div>
															</div>
															<div class="panel-body p0">
																<table class="table table-bordered">
																	<tbody>
																		<tr>
																			<td rowspan="4" style="vertical-align: middle">
																				<div class="col-md-2 col-sm-6 pt10 text-center"> 
																					<span class=""> 
																						<img src="<?php echo get_file_uri("assets/images/icons/caseta.png"); ?>" alt="..." heigth='100' width='100'> 
																					</span>
																				</div>
																			</td>
																			<td colspan="2"><strong><?php echo lang("next_hour"); ?></strong></td>
																		</tr>

																		<tr>
																			<td>
																				<?php foreach($variables as $variable){ ?>

																					<?php if($variable->id_air_variable_type == 1){ ?> 
																						<?php 
																							$variable_data = $Dashboard_controller->get_forecast_variable_data(array(
																								"id_sector" => $id_sector,
																								"id_station" => $id_station,
																								"id_model" => 2,
																								"id_variable" => $variable->id_air_variable
																							));
																						?>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center mb15" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8 mb15" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$variable_data["range"]." ".$variable_data["unit_name"]; ?></label></div><br>
																					<?php } ?> 

																				<?php } ?>
																			</td>
																			<td>
																				<?php foreach($variables as $variable){ ?>
																					<?php if($variable->id_air_variable_type == 2){ ?> 
																						<?php 
																							$variable_data = $Dashboard_controller->get_forecast_variable_data(array(
																								"id_sector" => $id_sector,
																								"id_station" => $id_station,
																								"id_model" => 2,
																								"id_variable" => $variable->id_air_variable
																							));
																						?>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center mb15" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8 mb15" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$variable_data["range"]." ".$variable_data["unit_name"]; ?></label></div><br>
																					<?php } ?> 
																				<?php } ?>
																			</td>
																		</tr>
																		<?php foreach($variables as $variable){ ?>

																			<?php if($variable->id_air_variable == 8){ // Si la variable es SO2, mostrar Próximo evento crítico ?> 
																				<?php 
																					$next_critical_event = $Dashboard_controller->get_next_critical_event(array(
																						"id_sector" => $id_sector,
																						"id_station" => $id_station,
																						"id_model" => 2,
																						"id_variable" => $variable->id_air_variable
																					));
																				?>
																				<tr>
																					<td colspan="2"><strong><?php echo lang("next_critical_event"); ?></strong></td>
																				</tr>
																				<tr>
																					<td>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$next_critical_event["next_critical_event"]; ?></label></div><br>
																					</td>
																					<td>
																						<label style="font-size: 14px; "><?php echo $next_critical_event["html_action_plan"]; ?></label>
																						<div id="action_plan_content-<?php echo $id_sector."-"."3"."-".$id_station."-".$variable->id_air_variable; ?>" class="hide">
																							<?php echo $next_critical_event["html_action_plan_content"]?>
																						</div>
																					</td>
																				</tr>
																				<?php break; ?>
																			<?php } ?>

																		<?php } ?>
																		
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													
												<?php } ?>

											<?php } ?>



											<?php //if($stations_by_model[1]){ // Si la estación del sector tiene el modelo Neuronal ?>
											<?php if(false){ // Momentaneamente oculto para Chagres ?>
											
												<?php $model = $this->Air_models_model->get_one(1); ?>

												<div class="col-md-12">
													<div class="pull-right">
														<?php $id_sector_encrypt = urlencode($this->encrypt->encode($id_sector)); ?>
														<p><em><?php echo lang("forecasts_corresponding_to"); ?> </em>  <a href="<?php echo ($puede_ver_sectores != 3) ? get_uri("air_forecast_sectors/index/".$id_sector_encrypt."#div_neur_model") : "#"; ?>" class="btn btn-success btn-sm" <?php echo ($puede_ver_sectores != 3) ? "" : "disabled style'pointer-events: none;'"; ?> ><?php echo lang("model")." ".lang($model->name); ?></a></p>
													</div>
												</div>

												<?php foreach($stations_by_model[1] as $id_station => $variables){ ?>

													<?php $station = $this->Air_stations_model->get_one($id_station); ?>
													
													<div class="col-md-6" style="/*text-align: justify;*/">
														<div class="estacion">
															<div class="page-title clearfix panel-success">
																<div class="pt10 pb10 pl10 text-left"><strong><?php echo $station->name; ?></strong></div>
															</div>
															<div class="panel-body p0">
																<table class="table table-bordered">
																	<tbody>
																		<tr>
																			<td rowspan="4" style="vertical-align: middle">
																				<div class="col-md-2 col-sm-6 pt10 text-center"> 
																					<span class=""> 
																						<img src="<?php echo get_file_uri("assets/images/icons/caseta.png"); ?>" alt="..." heigth='100' width='100'> 
																					</span>
																				</div>
																			</td>
																			<td colspan="2"><strong><?php echo lang("next_hour"); ?></strong></td>
																		</tr>
																		<tr>
																			<td>
																				<?php foreach($variables as $variable){ ?>

																					<?php if($variable->id_air_variable_type == 1){ ?> 
																						<?php 
																							$variable_data = $Dashboard_controller->get_forecast_variable_data(array(
																								"id_sector" => $id_sector,
																								"id_station" => $id_station,
																								"id_model" => 1,
																								"id_variable" => $variable->id_air_variable
																							));
																						?>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center mb15" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8 mb15" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$variable_data["range"]." ".$variable_data["unit_name"]; ?></label></div><br>
																					<?php } ?> 

																				<?php } ?>
																			</td>
																			<td>
																				<?php foreach($variables as $variable){ ?>
																					<?php if($variable->id_air_variable_type == 2){ ?> 
																						<?php 
																							$variable_data = $Dashboard_controller->get_forecast_variable_data(array(
																								"id_sector" => $id_sector,
																								"id_station" => $id_station,
																								"id_model" => 1,
																								"id_variable" => $variable->id_air_variable
																							));
																						?>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center mb15" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8 mb15" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$variable_data["range"]." ".$variable_data["unit_name"]; ?></label></div><br>
																					<?php } ?> 
																				<?php } ?>
																			</td>
																		</tr>
																		<?php foreach($variables as $variable){ ?>

																			<?php if($variable->id_air_variable == 8){ // Si la variable es SO2, mostrar Próximo evento crítico ?> 
																				<?php 
																					$next_critical_event = $Dashboard_controller->get_next_critical_event(array(
																						"id_sector" => $id_sector,
																						"id_station" => $id_station,
																						"id_model" => 1,
																						"id_variable" => $variable->id_air_variable
																					));
																				?>
																				<tr>
																					<td colspan="2"><strong><?php echo lang("next_critical_event"); ?></strong></td>
																				</tr>
																				<tr>
																					<td>
																						<div class="col-sm-12 col-md-4 col-lg-4 text-center" style="height: 100%;"><img src='<?php echo get_file_uri("assets/images/air_variables/".$variable->icono_variable); ?>' heigth='35' width='35' ></div>
																						<div class="col-sm-12 col-md-8 col-lg-8" style="height: 100%;"><label style="font-size: 14px; "><?php echo $variable->name_air_variable.": ".$next_critical_event["next_critical_event"]; ?></label></div><br>
																					</td>
																					<td>
																					<label style="font-size: 14px; "><?php echo $next_critical_event["html_action_plan"]; ?></label>
																						<div id="action_plan_content-<?php echo $id_sector."-"."3"."-".$id_station."-".$variable->id_air_variable; ?>" class="hide">
																							<?php echo $next_critical_event["html_action_plan_content"]?>
																						</div> 
																					</td>
																				</tr>
																				<?php break; ?>
																			<?php } ?>

																		<?php } ?>


																	</tbody>
																</table>
															</div>
														</div>
													</div>
													
												<?php } ?>

											<?php } ?>

										<?php } else { ?>

											<div class="col-md-12">
												<div class="panel panel-default mb15">
													<div class="panel-body">              
														<div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
															<?php echo lang("sector_without_stations_msj"); ?>
														</div>
													</div>	  
												</div>
											</div>

										<?php } ?>

									</div>

								</div>

							</div>

							<?php $loop_count++; ?>

						<?php } ?>
					</div>
                </div>
                    
			</div>
        </div>

	<?php } ?>


  <?php $disponibilidad_modulo_huellas = $this->Module_availability_model->get_one_where(array("id_cliente" => $client_id, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 1, "deleted" => 0))->available; ?>
  
  <?php if($disponibilidad_modulo_huellas) { ?>
	  <?php if($visible_total_impacts) { ?>
          <div class="row">
            <div class="col-md-12 col-sm-12 widget-container">
              <div class="panel panel-white">
                <div class="panel-heading" style="background-color:#00b393;color:white;">
                  <h3><?php echo lang("total_impacts"); ?></h3>
                </div>
                  <div class="panel-body">
                  <?php
                    
                    $id_proyecto = $proyecto->id;
                    $id_metodologia = $proyecto->id_metodologia;
                    
                    $html = '';
                    $array_cifras_huellas = array();
                    $array_unidades_proyecto = array();
                    
                    foreach($huellas as $huella){
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
                        //print_r($Conversion_model);
                        $fila_conversion = $Conversion_model->get_one_where(
                            array(
                                "id_tipo_unidad" => $id_tipo_unidad_origen,
                                "id_unidad_origen" => $id_unidad_origen,
                                "id_unidad_destino" => $id_unidad_destino
                            )
                        );
                        $valor_transformacion = $fila_conversion->transformacion;
						
                        // FIN VALOR DE CONVERSION
                        
                        $icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
                        $html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
                            $html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
                            
                            foreach($criterios_calculos as $calculo){
                                
                                $total_calculo = 0;
                                
                                $id_material = $calculo->id_material;
                                $id_categoria = $calculo->id_categoria;
                                $id_subcategoria = $calculo->id_subcategoria;
                                $id_formulario = $calculo->id_formulario;
                                
                                /* NO BORRAR
                                $id_campo_fc = $calculo->id_campo_fc;
                                $criterio_fc = $calculo->criterio_fc;
                                */
                                
                                if(isset($calculo->tipo_by_criterio) && json_decode($calculo->tipo_by_criterio)->id_campo_fc){
                                    $j_datos = json_decode($calculo->tipo_by_criterio);
                                    
                                    if($j_datos->id_campo_fc == 1){
                                        $id_campo_fc ="tipo_tratamiento";
                                    }else{
                                        $id_campo_fc = $calculo->id_campo_fc;
                                    }
                                    
                                }else{
                                     $id_campo_fc = $calculo->id_campo_fc;
                                }
                                
                                if($calculo->criterio_fc == "Disposición"){
                                    $value = $Tipo_tratamiento_model->get_one_where(array("nombre" => "Disposición", "deleted" => 0));
                                    $criterio_fc = $value->id;
                                }else if($calculo->criterio_fc == "Reutilización"){
                                    $value = $Tipo_tratamiento_model->get_one_where(array("nombre" => "Reutilización", "deleted" => 0));
                                    $criterio_fc = $value->id;
                                }else if($calculo->criterio_fc == "Reciclaje"){
                                    $value = $Tipo_tratamiento_model->get_one_where(array("nombre" => "Reciclaje", "deleted" => 0));
                                    $criterio_fc = $value->id;
                                }else{
                                    $criterio_fc = $calculo->criterio_fc;
                                }
                                
                                $ides_campo_unidad = json_decode($calculo->id_campo_unidad, true);
                                
                                //Deduzco el id de unidad al que debe consultar para el factor
                                $array_unidades = array();
                                $array_id_unidades = array();
                                $array_id_tipo_unidades = array();
                                
                                // POR CADA CAMPO UNIDAD SELECCIONADO EN EL CALCULO
                                foreach($ides_campo_unidad as $id_campo_unidad){
    
                                    if($id_campo_unidad == 0){
                                        $id_formulario = $calculo->id_formulario;
                                        $form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
                                        $json_unidad_form = json_decode($form_data->unidad, true);
                                        
                                        $id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
                                        $id_unidad = $json_unidad_form["unidad_id"];
                                        
                                        $fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
                                        $array_unidades[] = $fila_unidad->nombre;
                                        $array_id_unidades[] = $id_unidad;
                                        $array_id_tipo_unidades[] = $id_tipo_unidad;
                                    }else{
                                        $fila_campo = $Fields_model->get_one_where(array("id" => $id_campo_unidad, "deleted" => 0));
                                        $info_campo = $fila_campo->opciones;
                                        $info_campo = json_decode($info_campo, true);
    
                                        $id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
                                        $id_unidad = $info_campo[0]["id_unidad"];
                                        
                                        $fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
                                        $array_unidades[] = $fila_unidad->nombre;
                                        $array_id_unidades[] = $id_unidad;
                                        $array_id_tipo_unidades[] = $id_tipo_unidad;
                                    }
                                    // Para graficos
                                    $array_unidades_proyecto[$id_unidad] = $fila_unidad->nombre;
                                }
								
                                
                                /*if(count($array_id_unidades) > 1){
									
                                    $existe_longitud = in_array(5, $array_id_tipo_unidades);
                                    $existe_kg = in_array("kg", $array_unidades, true);
                                    $existe_ton = in_array("t", $array_unidades, true);
                                    
                                    if($existe_longitud && ($existe_kg || $existe_ton)){
                                        $id_unidad = ($existe_kg)?6:(($existe_ton)?5:0);
                                    }
                                }else{
                                    
                                }*/
								
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
                                
                                // Al total hay que multiplicarlo por el factor correspondiente
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
                                
                                //echo $id_metodologia.'|'.$id_huella.'|'.$id_material.'|'.$id_categoria.'|'.$id_subcategoria.'|'.$id_unidad.'|<br>';
                                
                                $valor_factor = 0;
                                if($fila_factor->id){
                                    $valor_factor = $fila_factor->factor;
                                }
                                
                                $elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
                                //echo $id_proyecto.'|'.$id_formulario.'|'.$id_campo_fc.'|'.$criterio_fc.'|'.$id_categoria;
                                //var_dump($elementos);
                                foreach($elementos as $elemento){
                                    $total_elemento = 0;
                                    $datos_decoded = json_decode($elemento->datos, true);
                                    
                                    $mult = 1;
                                    foreach($ides_campo_unidad as $id_campo_unidad){
                                        if($id_campo_unidad == 0){
                                            $mult *= $datos_decoded["unidad_residuo"];
                                        }else{
                                            $mult *= $datos_decoded[$id_campo_unidad];
                                        }
                                    }
                                    
                                    $total_elemento = $mult * $valor_factor;
                                    $total_calculo += $total_elemento;
                                    
                                }
                                
                                //$total_huella = $total_calculo * $valor_factor;
                                $total_huella += $total_calculo;
                                
                            }// FIN EACH CALCULO
                            
                            
                            $total_huella *= $valor_transformacion;
                            
                            
                            //$html .= '<div class="text-center p15">2,07*10<sup>2</sup></div>';
                            $array_cifras_huellas[$id_huella] = $total_huella;
                            //$html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto).'</div>';
                            $html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto).'</div>';
                            $html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
                        $html .= '</div>';
                    }
                    
                    echo $html;
                    ?>
                    
                  </div>
              </div>
           </div>
        </div>
      <?php } ?>
  <?php } ?>



  <?php if($disponibilidad_modulo_huellas) { ?>
    
      <?php if($visible_impacts_by_functional_units) { ?>
          <div class="row">
            <?php foreach($unidades_funcionales as $unidad_funcional){ ?>
            <div class="col-md-12 col-sm-12">
              <div class="panel panel-default">
                <div class="page-title clearfix panel-success">
                  <h1><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h1>
                </div>
                <div class="panel-body">
                <?php
                
                $id_proyecto = $proyecto->id;
                $id_metodologia = $proyecto->id_metodologia;
                
                $nombre_uf = $unidad_funcional->nombre;
                $id_subproyecto_uf = $unidad_funcional->id_subproyecto;
                $valor_uf = get_functional_unit_value($client_id, $id_proyecto, $unidad_funcional->id, NULL, NULL);
                
                $html = '';
                foreach($huellas as $huella){
    
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
                    $valor_transformacion = $fila_conversion->transformacion;/**/
                    // FIN VALOR DE CONVERSION
                    
                    $icono = $huella->icono ? base_url("assets/images/impact-category/".$huella->icono) : base_url("assets/images/impact-category/empty.png");
                    $html .= '<div class="col-md-2 col-sm-6 col-xs-6 text-center huella">';
                    $html .= '<div class="text-center p15"><img src="'.$icono.'" alt="..." height="50" width="50" class="mCS_img_loaded"></div>';
                    
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
                            /* NO BORRAR
                            $id_campo_sp = $criterio_calculo->id_campo_sp;
                            $id_campo_pu = $criterio_calculo->id_campo_pu;
                            $id_campo_fc = $criterio_calculo->id_campo_fc;
                            */
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
                            //$criterio_fc = $criterio_calculo->criterio_fc;
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
                                    $form_data = $Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
                                    $json_unidad_form = json_decode($form_data->unidad,true);
                                
                                    $id_tipo_unidad = $json_unidad_form["tipo_unidad_id"];
                                    $id_unidad = $json_unidad_form["unidad_id"];
                                    
                                    $fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
                                    $array_unidades[] = $fila_unidad->nombre;
                                    $array_id_unidades[] = $id_unidad;
                                    $array_id_tipo_unidades[] = $id_tipo_unidad;
                                }else{
                                    $fila_campo = $Fields_model->get_one_where(array("id"=>$id_campo_unidad,"deleted" => 0));
                                    $info_campo = $fila_campo->opciones;
                                    $info_campo = json_decode($info_campo, true);
                                    
                                    $id_tipo_unidad = $info_campo[0]["id_tipo_unidad"];
                                    $id_unidad = $info_campo[0]["id_unidad"];
                                    
                                    $fila_unidad = $Unity_model->get_one_where(array("id" => $id_unidad, "deleted" => 0));
                                    $array_unidades[] = $fila_unidad->nombre;
                                    $array_id_unidades[] = $id_unidad;
                                    $array_id_tipo_unidades[] = $id_tipo_unidad;
                                }
                                // Para graficos
                                //$array_unidades_proyecto[$id_unidad] = $fila_unidad->nombre;
                            }
    
                            // OBTENER UNIDAD FINAL
                            /*if(count($array_id_unidades) > 1){
                                $existe_longitud = in_array(5, $array_id_tipo_unidades);
                                $existe_kg = in_array("kg", $array_unidades, true);
                                $existe_ton = in_array("t", $array_unidades, true);
                                
                                if($existe_longitud && ($existe_kg || $existe_ton)){
                                    $id_unidad = ($existe_kg)?6:($existe_ton)?5:0;
                                }
                            }else{
                                $id_unidad = $array_id_unidades[0];
                            }*/
							
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
                            $elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
            
                            foreach($elementos as $elemento){
                                
                                $total_elemento = 0;
                                $datos_decoded = json_decode($elemento->datos, true);
                                
                                $mult = 1;
                                foreach($ides_campo_unidad as $id_campo_unidad){
                                    if($id_campo_unidad == 0){
                                        $mult *= $datos_decoded["unidad_residuo"];
                                        
                                    }else{
                                        $mult *= $datos_decoded[$id_campo_unidad];
                                    }
                                    //$mult *= $datos_decoded[$id_campo_unidad];
                                }
                                // AL CALCULAR A NIVEL DE ELEMENTO, EL RESULTADO MULTIPLICARLO POR EL FC
                                $total_elemento_interno = $mult * $valor_factor;
                                //print_r($total_elemento_interno);
                                /*if($id_categoria == 49){
                                    if($mult > 0){
                                        echo 'Valor elemento * factor = '.$mult.' * '.$valor_factor.'<br>';
                                    }
                                }*/
    
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
                                //echo $total_elemento_interno.'<br>';
                                //echo $id_campo_sp.'|'.$id_campo_pu.'<br>';
                                
                                if($id_campo_sp && !$id_campo_pu){
                                    
                                    if($id_campo_sp == "tipo_tratamiento"){
                                        $value = $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_sp]);
                                        $valor_campo_sp = $value->nombre;
                                    }else{
                                        $valor_campo_sp = $datos_decoded[$id_campo_sp];
                                    }
                                    //$valor_campo_sp = $datos_decoded[$id_campo_sp];
                                    //echo $obj_asignacion.'<br>';
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
                                
                                
                                /*
                                if(!$id_campo_sp && $id_campo_pu){
                                    $valor_campo_pu = $datos_decoded[$id_campo_pu];
                                    
                                    foreach($array_asignaciones as $array_asignacion){
                                        if($array_asignacion["criterio_pu"] == $valor_campo_pu){
                                            $total_elemento += $total_elemento_interno;
                                        }
                                    }
                                }
                                */
                                
                                if(!$id_campo_sp && $id_campo_pu){
                                    
                                    if($id_campo_pu == "tipo_tratamiento"){
                                        $value= $this->Tipo_tratamiento_model->get_one($datos_decoded[$id_campo_pu]);
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
                                /*
                                if($id_campo_sp && $id_campo_pu){
                                    $valor_campo_sp = $datos_decoded[$id_campo_sp];
                                    $valor_campo_pu = $datos_decoded[$id_campo_pu];
                                    
                                    foreach($array_asignaciones as $array_asignacion){
                                        if($array_asignacion["criterio_sp"] == $valor_campo_sp && $array_asignacion["criterio_pu"] == $valor_campo_pu){
                                            $total_elemento += $total_elemento_interno;
                                        }
                                    }
                                }
                                */
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
                                        
                                        /*if($id_categoria == 49){
                                            echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'| '.$criterio_calculo->etiqueta.'|ASIGNACION -  criterio sp: '.$criterio_sp.'| '.$tipo_asignacion_sp.'| sp destino:'.$sp_destino.'|porcentajes sp: '.$porcentajes_sp.'|criterio pu: '.$criterio_pu.'| '.$tipo_asignacion_pu.'| pu destino:'.$pu_destino.'|porcentajes pu: '.$porcentajes_pu.'<br>';
                                        }*/
                                        
                                        if($tipo_asignacion_sp == "Total" && $tipo_asignacion_pu == "Total" && $sp_destino == $id_subproyecto_uf && $pu_destino == $id_pu){
                                            
                                            /*if($id_categoria == 49){
                                                echo $tipo_asignacion_sp.' == "Total" && '.$tipo_asignacion_pu.' == "Total" && '.$sp_destino.' == '.$id_subproyecto_uf.' && '.$pu_destino.' == '.$id_pu.'<br>';
                                            }*/
                                            
                                            /*if($id_categoria == 49){
                                                echo $criterio_sp.' == '.$valor_campo_sp.' && '.$criterio_pu.' == '.$valor_campo_pu.'<br>';
                                            }*/
                                            
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
                                
                                /*if($total_elemento > 0){
                                    echo $unidad_funcional->nombre.'|'.$huella->nombre.'|'.$nombre_pu.'|'.$criterio_calculo->nombre_criterio.'|'.$criterio_calculo->etiqueta.'|'.$total_elemento.'<br>';
                                }*/
                            }// FIN ELEMENTO
                            
                            $total_pu += $total_criterio;
                            
                        }// FIN CRITERIO-CALCULO
                        /*if($total_pu > 0){
                            echo 'Total Proceso Unitario <strong>'.$nombre_pu.'</strong> sin dividir = '.$total_pu.'<br>';
                            echo '='.$total_pu.' / '.$valor_uf.'<br>';
                        }*/
                        $total_pu = $total_pu/$valor_uf;
                        
                        /*if($total_pu > 0){
                            echo 'Total Proceso Unitario <strong>'.$nombre_pu.'</strong> dividido = '.$total_pu.'<br><br>';
                        }*/
                        $total_huella += $total_pu;
                    
                    }// FIN PROCESO UNITARIO
                    
                    //echo '------------------<br>';
                    //echo 'Total sin multiplicar ='.$total_huella.'<br>';
                    //echo '= '.$total_huella.' * '.$valor_transformacion.'<br>';
                    $total_huella *= $valor_transformacion;
                    //echo 'TOTAL = '.$total_huella.'<br><br>';
                    //$total_huella_por_uf = ($array_cifras_huellas[$id_huella])/$unidad_funcional->valor;
                    //$total_huella_por_uf = ($total_huella)/$unidad_funcional->valor;
                        
                    //$html .= '<div class="text-center p15">2,07*10<sup>2</sup></div>';
                    $html .= '<div class="text-center p15">'.to_number_project_format($total_huella, $id_proyecto).'</div>';
                    $html .= '<div class="pt10 pb10 b-b"> '.$huella->nombre.' ('.$nombre_unidad_huella.' '.$huella->indicador.') </div>';
                    $html .= '</div>';
                }
                echo $html;
                ?>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
      <?php } ?>
  
  <?php } ?>
  
  
  <div class="row">
  <div class="col-md-12">
  
  <div id="div_consumos" class="panel panel-body">
	<!-- GRAFICO Y TABLA CONSUMO VOLUMEN -->
	<div class="col-md-6" style="padding-left:0px; padding-right:0px;">
    	
        <?php
			
			/*
			1.- CONSULTAR LA UNIDAD SETEADA PARA EL TIPO DE UNIDAD VOLUMEN Y PARA EL TIPO UNIDAD MASA
			2.- CONSULTAR LOS FORMULARIOS DEL PROYECTO, QUE SEAN DE TIPO CONSUMO, QUE POSEA CAMPOS DE TIPO UNIDAD, QUE ESE ID UNIDAD SEA IGUAL A LA UNIDAD SETEADA DE SU TIPO DE UNIDAD
			3.- CONSULTAR SUS CATEGORIAS
			4.- CONSULTAR ELEMENTOS MARCADOS CON ESAS CATEGORIAS Y AGRUPARLAS A NIVEL EXTRA-FORMULARIOS
			*/
			
			$array_id_categorias_valores_volumen = array();
			foreach($campos_unidad_consumo as $formulario_campo){
				$id_campo = $formulario_campo->id_campo;
				/*
				$datos_campo = json_decode($formulario_campo->opciones, true);
				$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
				$id_unidad = $datos_campo[0]["id_unidad"];
				*/
				
				$datos_campo = json_decode($formulario_campo->unidad, true);
				$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
				$id_unidad = $datos_campo["unidad_id"];
				
				
				// SI ES VOLUMEN // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 2/* && $id_unidad == $id_unidad_volumen*/){
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_volumen[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Consumo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_volumen){
								/*
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								*/
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								
								
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_volumen
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								/*
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded[$id_campo];
								*/
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								
								
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
						}
					}
				}
			}
		?>
        
        
        <div id="grafico_consumo_volumen" class="col-md-12 p0 page-title">
        	<div class="panel-body p20">
            <h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)</strong></h4>
        	</div>
            <div class="grafico page-title" id="consumo_volumen"></div>
		</div>

        <div id="tabla_consumo_volumen" class="col-md-12 p0">
            <div class="page-title p10" style="border-bottom: none !important;">
            
            	<div class="panel-group" id="accordion_consumos_v">
                
                	<div class="panel panel-default">

                        <div class="panel-heading p0">
                        	<a data-toggle="collapse" href="#collapse_consumos_v" data-parent="#accordion_consumos_v" class="accordion-toggle">
                        		<div id="titulo_tabla_consumo_volumen">
                                	<h4 style="float:unset !important;" class="text-center"><strong><i class="fa fa-plus-circle font-16"></i> <?php echo lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)</strong></h4>
                                </div>
                            </a>
                        </div>
                        
                        <div id="collapse_consumos_v" class="panel-collapse collapse">
                            <table class="table table-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang('category'); ?></th>
                                        <th class="text-center"><?php echo lang('value'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
									<?php
                
                                        $array_grafico_consumos_volumen_categories = array();
                                        $array_grafico_consumos_volumen_data = array();
                                        $html = '';
                                        foreach ($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores){
                                            $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                            if($row_alias->alias){
                                                $nombre_categoria = $row_alias->alias;
                                            }else{
                                                $row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                                $nombre_categoria = $row_categoria->nombre;
                                            }
                                            
                                            // ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
                                            // UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
                                            // SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
                                            // SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
                                            // NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
                                            // NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
                                            
                                            // en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
                                            // EXISTE UNA EXCEPCION, ENEL, LISTA 5
                                            
                                            $row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
                                            
                                            if($row_categoria->grafico){
                                                $array_grafico_consumos_volumen_categories[] = $nombre_categoria;
                                                $array_grafico_consumos_volumen_data[] = array_sum($arreglo_valores);
                                            }
                                            
                                            if($row_categoria->tabla){
                                                $html .= '<tr>';
                                                $html .= '<td class="text-left">'.$nombre_categoria.'</td>';
                                                $html .= '<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
                                                $html .= '</tr>';
                                            }
         
                                        }
                                        
                                        echo $html;
                                    ?>
                                </tbody>
                            </table>
                        </div>
					
                    </div>
                </div>
 
            </div>
        </div>

	</div>
	<!-- FIN GRAFICO Y TABLA CONSUMO VOLUMEN -->
    
    <!-- GRAFICO Y TABLA CONSUMO MASA -->
	<div class="col-md-6" style="padding-left:0px; padding-right:0px;">
    	
        <?php
			
			/*
			1.- CONSULTAR LA UNIDAD SETEADA PARA EL TIPO DE UNIDAD VOLUMEN Y PARA EL TIPO UNIDAD MASA
			2.- CONSULTAR LOS FORMULARIOS DEL PROYECTO, QUE SEAN DE TIPO CONSUMO, QUE POSEA CAMPOS DE TIPO UNIDAD, QUE ESE ID UNIDAD SEA IGUAL A LA UNIDAD SETEADA DE SU TIPO DE UNIDAD
			3.- CONSULTAR SUS CATEGORIAS
			4.- CONSULTAR ELEMENTOS MARCADOS CON ESAS CATEGORIAS Y AGRUPARLAS A NIVEL EXTRA-FORMULARIOS
			*/
			
			$array_id_categorias_valores_masa = array();
			foreach($campos_unidad_consumo as $formulario_campo){
				$id_campo = $formulario_campo->id_campo;
				/*
				$datos_campo = json_decode($formulario_campo->opciones, true);
				$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
				$id_unidad = $datos_campo[0]["id_unidad"];
				*/
				
				$datos_campo = json_decode($formulario_campo->unidad, true);
				$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
				$id_unidad = $datos_campo["unidad_id"];
				

				// SI ES MASA // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 1/* && $id_unidad == $id_unidad_masa*/){
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_masa[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Consumo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_masa){
								/*
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								*/
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 1 (MASA)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_masa
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								/*
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded[$id_campo];
								*/
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}

						}
						
						
					}
					
					
				}
				
				
			}
			
		?>
                
        <div id="grafico_consumo_masa" class="col-md-12 p0 page-title">
        	<div class="panel-body p20">
				<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)</strong></h4>
        	</div>
            <div class="grafico page-title" id="consumo_masa"></div>
		</div>

        <div id="tabla_consumo_masa" class="col-md-12 p0">
            <div class="page-title p10" style="border-bottom: none !important;">
            
            	<div class="panel-group" id="accordion_consumos_m">
                
                	<div class="panel panel-default">
            			
                        <div class="panel-heading p0">
                        	<a data-toggle="collapse" href="#collapse_consumos_m" data-parent="#accordion_consumos_m" class="accordion-toggle">
                        		<div id="titulo_tabla_consumo_masa">
									<h4 style='float:unset !important;' class="text-center"><strong><i class="fa fa-plus-circle font-16"></i> <?php echo lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)</strong></h4>
                                </div>
                            </a>
                        </div>
                        
                        <div id="collapse_consumos_m" class="panel-collapse collapse">   
                            <table class="table table-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang('category'); ?></th>
                                        <th class="text-center"><?php echo lang('value'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                
                                $array_grafico_consumos_masa_categories = array();
                                $array_grafico_consumos_masa_data = array();
                                $html = '';
                                foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
                                    $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                    if($row_alias->alias){
                                        $nombre_categoria = $row_alias->alias;
                                    }else{
                                        $row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                        $nombre_categoria = $row_categoria->nombre;
                                    }
                                    
                                    // ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
                                    // UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
                                    // SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
                                    // SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
                                    // NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
                                    // NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
                                    
                                    // en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
                                    // EXISTE UNA EXCEPCION, ENEL, LISTA 5
                                    
                                    $row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
            
                                    if($row_categoria->grafico){
                                        $array_grafico_consumos_masa_categories[] = $nombre_categoria;
                                        $array_grafico_consumos_masa_data[] = array_sum($arreglo_valores);
                                    }
                                    
                                    if($row_categoria->tabla){
                                        $html .= '<tr>';
                                        $html .= '<td class="text-left">'.$nombre_categoria.'</td>';
                                        $html .= '<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                echo $html;
                                ?>
                                </tbody>
                            </table>
                		</div>
					</div>
                </div>
                
            </div>
        </div>
		
	</div>
	<!-- FIN GRAFICO Y TABLA CONSUMO MASA -->
    
    <!-- GRAFICO Y TABLA CONSUMO ENERGIA -->
	<div class="col-md-12" style="padding-left:0px; padding-right:0px;">
    	
        <?php
			
			/*
			1.- CONSULTAR LA UNIDAD SETEADA PARA EL TIPO DE UNIDAD ENERGIA
			2.- CONSULTAR LOS FORMULARIOS DEL PROYECTO, QUE SEAN DE TIPO CONSUMO, QUE POSEA CAMPOS DE TIPO UNIDAD, QUE ESE ID UNIDAD SEA IGUAL A LA UNIDAD SETEADA DE SU TIPO DE UNIDAD
			3.- CONSULTAR SUS CATEGORIAS
			4.- CONSULTAR ELEMENTOS MARCADOS CON ESAS CATEGORIAS Y AGRUPARLAS A NIVEL EXTRA-FORMULARIOS
			*/
			
			$array_id_categorias_valores_energia = array();
			foreach($campos_unidad_consumo as $formulario_campo){
				$id_campo = $formulario_campo->id_campo;
				/*
				$datos_campo = json_decode($formulario_campo->opciones, true);
				$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
				$id_unidad = $datos_campo[0]["id_unidad"];
				*/
				
				$datos_campo = json_decode($formulario_campo->unidad, true);
				$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
				$id_unidad = $datos_campo["unidad_id"];
				

				// SI ES ENERGIA // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 4){
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_energia[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Consumo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_energia){
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_energia[$cat->id_categoria][] = $valor;
								
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 4 (ENERGIA)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_energia
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								/*
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded[$id_campo];
								*/
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								
								$array_id_categorias_valores_energia[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}

						}
						
						
					}
					
					
				}
				
				
			}
			
		?>
        
        <?php if(count($array_id_categorias_valores_energia) > 0){ ?>
        <div id="grafico_consumo_energia" class="col-md-12 p0 page-title">
        	<div class="panel-body p20">
				<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)</strong></h4>
        	</div>
            <div class="grafico page-title" id="consumo_energia"></div>
		</div>

        <div id="tabla_consumo_energia" class="col-md-12 p0">
            <div class="page-title p10" style="border-bottom: none !important;">
            
            	<div class="panel-group" id="accordion_consumos_e">
                
                	<div class="panel panel-default">
            			
                        <div class="panel-heading p0">
                        	<a data-toggle="collapse" href="#collapse_consumos_e" data-parent="#accordion_consumos_e" class="accordion-toggle">
                        		<div id="titulo_tabla_consumo_energia">
									<h4 style='float:unset !important;' class="text-center"><strong><i class="fa fa-plus-circle font-16"></i> <?php echo lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)</strong></h4>
                                </div>
                            </a>
                        </div>
                        
                        <div id="collapse_consumos_e" class="panel-collapse collapse">   
                            <table class="table table-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang('category'); ?></th>
                                        <th class="text-center"><?php echo lang('value'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                
                                $array_grafico_consumos_energia_categories = array();
                                $array_grafico_consumos_energia_data = array();
                                $html = '';
                                foreach ($array_id_categorias_valores_energia as $id_categoria => $arreglo_valores){
                                    $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                    if($row_alias->alias){
                                        $nombre_categoria = $row_alias->alias;
                                    }else{
                                        $row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                        $nombre_categoria = $row_categoria->nombre;
                                    }
                                    
                                    // ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
                                    // UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
                                    // SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
                                    // SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
                                    // NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
                                    // NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
                                    
                                    // en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
                                    // EXISTE UNA EXCEPCION, ENEL, LISTA 5
                                    
                                    $row_categoria = $Client_consumptions_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
            
                                    if($row_categoria->grafico){
                                        $array_grafico_consumos_energia_categories[] = $nombre_categoria;
                                        $array_grafico_consumos_energia_data[] = array_sum($arreglo_valores);
                                    }
                                    
                                    if($row_categoria->tabla){
                                        $html .= '<tr>';
                                        $html .= '<td class="text-left">'.$nombre_categoria.'</td>';
                                        $html .= '<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                echo $html;
                                ?>
                                </tbody>
                            </table>
                		</div>
					</div>
                </div>
                
            </div>
        </div>
        <?php } ?>
		
	</div>
	<!-- FIN GRAFICO Y TABLA CONSUMO ENERGIA -->
    
    
    
    
    
    </div>
    </div>
    </div>
    
    <div class="row">
  	<div class="col-md-12">
    <div id="div_residuos" class="panel panel-body mb0">
    <!-- GRAFICO Y TABLA RESIDUO VOLUMEN -->
	<div class="col-md-6" style="padding-left:0px; padding-right:0px;">
    	
        <?php
			/*
			1.- CONSULTAR LA UNIDAD SETEADA PARA EL TIPO DE UNIDAD VOLUMEN Y PARA EL TIPO UNIDAD MASA
			2.- CONSULTAR LOS FORMULARIOS DEL PROYECTO, QUE SEAN DE TIPO CONSUMO, QUE POSEA CAMPOS DE TIPO UNIDAD, QUE ESE ID UNIDAD SEA IGUAL A LA UNIDAD SETEADA DE SU TIPO DE UNIDAD
			3.- CONSULTAR SUS CATEGORIAS
			4.- CONSULTAR ELEMENTOS MARCADOS CON ESAS CATEGORIAS Y AGRUPARLAS A NIVEL EXTRA-FORMULARIOS
			*/
			$array_id_categorias_valores_volumen = array();
			foreach($campos_unidad_residuo as $formulario_campo){
				$datos_campo = json_decode($formulario_campo->unidad, true);
				
				$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
				$id_unidad = $datos_campo["unidad_id"];

				// SI ES VOLUMEN // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 2/* && $id_unidad == $id_unidad_masa || $tipo_unidad_id == 2*/){
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_volumen[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_volumen){
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_volumen
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
						}
					}
				}
			}

			/*
			foreach($campos_unidad_residuo as $formulario_campo){
				
				$id_campo = $formulario_campo->id_campo;
				$datos_campo = json_decode($formulario_campo->opciones, true);
				$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
				$id_unidad = $datos_campo[0]["id_unidad"];
				
				$datos_unidad_formulario = json_decode($formulario_campo->unidad, true);
				$tipo_unidad_id = $datos_unidad_formulario["tipo_unidad_id"];
				
				// SI ES VOLUMEN // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 2/* && $id_unidad == $id_unidad_masa*//* || $tipo_unidad_id == 2){
					/*
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_volumen[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_volumen){
								$datos_decoded = json_decode($ef->datos, true);
								//$valor = $datos_decoded[$id_campo];
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_volumen
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								//$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
						}
					}
				}
			}
			*/
		?>
        
        <div id="grafico_residuo_volumen" class="col-md-12 p0 page-title">
        	<div class="panel-body p20">
            	<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('waste'); ?> (<?php echo $unidad_volumen; ?>)</strong></h4>
        	</div>
            <div class="grafico page-title" id="residuo_volumen"></div>
		</div>

        <div id="tabla_residuo_volumen" class="col-md-12 p0">
            <div class="page-title p10" style="border-bottom: none !important;">
            
            	<div class="panel-group" id="accordion_residuos_v">
            		<div class="panel panel-default">
            			
                        <div class="panel-heading">
                        	<a data-toggle="collapse" href="#collapse_residuos_v" data-parent="#accordion_residuos_v" class="accordion-toggle">
                        		<div id="titulo_tabla_residuo_volumen">
									<h4 style='float:unset !important;' class="text-center"><strong><i class="fa fa-plus-circle font-16"></i> <?php echo lang('waste'); ?> (<?php echo $unidad_volumen; ?>)</strong></h4>
                                </div>
                            </a>
                        </div>
						
                        <div id="collapse_residuos_v" class="panel-collapse collapse">
                            <table class="table table-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang('category'); ?></th>
                                        <th class="text-center"><?php echo lang('value'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                
                                $array_grafico_residuos_volumen_categories = array();
                                $array_grafico_residuos_volumen_data = array();
                                $html = '';
                                foreach ($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores){
                                    $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                    if($row_alias->alias){
                                        $nombre_categoria = $row_alias->alias;
                                    }else{
                                        $row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                        $nombre_categoria = $row_categoria->nombre;
                                    }
                                    
                                    // ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
                                    // UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
                                    // SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
                                    // SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
                                    // NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
                                    // NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
                                    
                                    // en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
                                    // EXISTE UNA EXCEPCION, ENEL, LISTA 5
                                    
                                    $row_categoria = $Client_waste_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
                                    
                                    if($row_categoria->grafico){
                                        $array_grafico_residuos_volumen_categories[] = $nombre_categoria;
                                        $array_grafico_residuos_volumen_data[] = array_sum($arreglo_valores);
                                    }
                                    
                                    if($row_categoria->tabla){
                                        $html .= '<tr>';
                                        $html .= '<td class="text-left">'.$nombre_categoria.'</td>';
                                        $html .= '<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                
                                echo $html;
                                
                                ?>
                                </tbody>
                            </table>
                		</div>

                	</div>
                </div>
                
            </div>
        </div>
        

	</div>
	<!-- FIN GRAFICO Y TABLA RESIDUO VOLUMEN -->
    
    <!-- GRAFICO Y TABLA RESIDUO MASA -->
	<div class="col-md-6" style="padding-left:0px; padding-right:0px;">
    	
        <?php
			/*
			1.- CONSULTAR LA UNIDAD SETEADA PARA EL TIPO DE UNIDAD VOLUMEN Y PARA EL TIPO UNIDAD MASA
			2.- CONSULTAR LOS FORMULARIOS DEL PROYECTO, QUE SEAN DE TIPO CONSUMO, QUE POSEA CAMPOS DE TIPO UNIDAD, QUE ESE ID UNIDAD SEA IGUAL A LA UNIDAD SETEADA DE SU TIPO DE UNIDAD
			3.- CONSULTAR SUS CATEGORIAS
			4.- CONSULTAR ELEMENTOS MARCADOS CON ESAS CATEGORIAS Y AGRUPARLAS A NIVEL EXTRA-FORMULARIOS
			*/
			$array_id_categorias_valores_masa = array();
			foreach($campos_unidad_residuo as $formulario_campo){
				$datos_campo = json_decode($formulario_campo->unidad, true);
				$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
				$id_unidad = $datos_campo["unidad_id"];

				// SI ES MASA // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 1){
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_masa[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_masa){
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 1 (MASA)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_masa
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								//$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
						}
					}
				}
			}
			/*
			$array_id_categorias_valores_masa = array();
			foreach($campos_unidad_residuo as $formulario_campo){
				$id_campo = $formulario_campo->id_campo;
				$datos_campo = json_decode($formulario_campo->opciones, true);
				$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
				$id_unidad = $datos_campo[0]["id_unidad"];
				
				$datos_unidad_formulario = json_decode($formulario_campo->unidad, true);
				$tipo_unidad_id = $datos_unidad_formulario["tipo_unidad_id"];
				
				// SI ES MASA // Y UNIDAD DE LA CONFIGURACION
				if($id_tipo_unidad == 1/* && $id_unidad == $id_unidad_masa || $tipo_unidad_id == 1*//*){
					$id_formulario = $formulario_campo->id;
					$categorias = $Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario))->result();
					// POR CADA CATEGORIA DEL FORMULARIO
					foreach($categorias as $cat){
						// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
						$array_id_categorias_valores_masa[$cat->id_categoria][] = 0;
						// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
						$elementos_form = $Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
						// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
						foreach($elementos_form as $ef){
							
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_masa){
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								//$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 1 (MASA)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_masa
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								//$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
						}
					}
				}
			}
			*/
		?>
        
        <div id="grafico_residuo_masa" class="col-md-12 p0 page-title">
        	<div class="panel-body p20">
            	<h4 style='float:unset !important; text-align:center;'><strong><?php echo lang('waste'); ?> (<?php echo $unidad_masa; ?>)</strong></h4>
        	</div>
            <div class="grafico page-title" id="residuo_masa"></div>
		</div>

        <div id="tabla_residuo_masa" class="col-md-12 p0">
            <div class="page-title p10" style="border-bottom: none !important;">
            
            	<div class="panel-group" id="accordion_residuos_m">
                
            		<div class="panel panel-default">
                    	
                        <div class="panel-heading">
                        	<a data-toggle="collapse" href="#collapse_residuos_m" data-parent="#accordion_residuos_m" class="accordion-toggle">
                        		<div id="titulo_tabla_residuo_masa">
									<h4 style='float:unset !important;' class="text-center"><strong><i class="fa fa-plus-circle font-16"></i> <?php echo lang('waste'); ?> (<?php echo $unidad_masa; ?>)</strong></h4>
                                </div>
                            </a>
                        </div>

						<div id="collapse_residuos_m" class="panel-collapse collapse">
                            <table class="table table-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo lang('category'); ?></th>
                                        <th class="text-center"><?php echo lang('value'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                
                                $array_grafico_residuos_masa_categories = array();
                                $array_grafico_residuos_masa_data = array();
                                $html = '';
                                foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
                                    $row_alias = $Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
                                    if($row_alias->alias){
                                        $nombre_categoria = $row_alias->alias;
                                    }else{
                                        $row_categoria = $Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
                                        $nombre_categoria = $row_categoria->nombre;
                                    }
                                    
                                    // ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
                                    // UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
            
                                    // SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
                                    // SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
                                    // NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
                                    // NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO
                                    
                                    // en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
                                    // EXISTE UNA EXCEPCION, ENEL, LISTA 5
                                    
                                    $row_categoria = $Client_waste_settings_model->get_one_where(array('id_cliente' => $client_id, 'id_proyecto' => $proyecto->id, 'id_categoria' => $id_categoria, 'deleted' => 0));
                                    
                                    if($row_categoria->grafico){
                                        $array_grafico_residuos_masa_categories[] = $nombre_categoria;
                                        $array_grafico_residuos_masa_data[] = array_sum($arreglo_valores);
                                    }
                                    
                                    if($row_categoria->tabla){
                                        $html .= '<tr>';
                                        $html .= '<td class="text-left">'.$nombre_categoria.'</td>';
                                        $html .= '<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                
                                echo $html;
                                
                                ?>
                                </tbody>
                            </table>
               			 </div>
                	
                    </div>
                </div>
                
            </div>
        </div>

    </div>
	<!-- FIN GRAFICO Y TABLA RESIDUO MASA -->
    
    </div>
    </div>
    </div>
    
<?php 
	if($Client_compromises_settings_model){
		$consumptions_settings = $Client_compromises_settings_model->get_one_where(array("id_cliente" => $client_id, "id_proyecto" => $proyecto->id, "deleted" => 0)); 
	}
?>
	
<?php 
	$visible_consumptions;
	if(($consumptions_settings->tabla == 0) && ($consumptions_settings->grafico == 0)){
		$visible_consumptions = FALSE;
	}else{
		$visible_consumptions = TRUE;
	}

?>
	
<?php if($visible_consumptions && ($puede_ver_compromisos != 3) && $disponibilidad_modulo_compromisos == 1){ ?>	
	 <div class="row" >
		<div class="col-md-12 col-sm-12" style="padding-top: 20px;">
			<div class="panel panel-default mb0">
				<div class="page-title clearfix panel-success">
				<h1><?php echo lang('compliance_summary'); ?></h1>
				</div>
				<div class="panel-body">
					<?php if($consumptions_settings->tabla == 1){ ?>
					<div class="col-md-6" id="tabla_compromisos">
						<table class="table table-striped">
							<thead>
								<tr>
									<th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_compliance_status"); ?></th>
									<th colspan="2" class="text-center"><?php echo lang("total"); ?></th>
								</tr>
								<tr>
									<th class="text-center">N°</th>
									<th class="text-center">%</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-left"><strong><?php echo lang("total_applicable_compromises"); ?></strong></td>
									<td class="text-right"><?php echo to_number_project_format($total_compromisos_aplicables, $id_proyecto); ?></td>
									<td class="text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
								</tr>
								<?php foreach($total_cantidades_estados_evaluados as $estado) { ?>
									<tr>
										<td class="text-left"><?php echo $estado["nombre_estado"]?></td>
                                        <td class="text-right"><?php echo to_number_project_format($estado["cantidad_categoria"], $id_proyecto); ?></td>
                                        <td class="text-right"><?php echo to_number_project_format(($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables, $id_proyecto); ?> %</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<?php }?>
					<?php if($consumptions_settings->grafico == 1){ ?>
					<div class="col-md-6" id="grafico_compromisos">
						<div class="panel panel-default">
						   <div class="page-title clearfix panel-success">
							  <div class="pt10 pb10 text-center"> <?php echo lang("total_compliances"); ?> </div>
						   </div>
						   <div class="panel-body">
							  <div id="grafico_cumplimientos_totales" style="height: 240px;"></div>
						   </div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

    
<?php 
	if($Client_permitting_settings_model){
		$permitting_settings = $Client_permitting_settings_model->get_one_where(array("id_cliente" => $client_id, "id_proyecto" => $proyecto->id, "deleted" => 0)); 
	}
?>
    
<?php 
	$visible_permittings;
	if(($permitting_settings->tabla == 0) && ($permitting_settings->grafico == 0)){
		$visible_permittings = FALSE;
	}else{
		$visible_permittings = TRUE;
	}
?>

<?php if($visible_permittings && ($puede_ver_permisos != 3) && $disponibilidad_modulo_permisos == 1){ ?>	
	 <div class="row" >
		<div class="col-md-12 col-sm-12" style="padding-top: 20px;">
			<div class="panel panel-default mb0">
				<div class="page-title clearfix panel-success">
				<h1><?php echo lang('procedure_summary'); ?></h1>
				</div>
				<div class="panel-body">
					<?php if($permitting_settings->tabla == 1){ ?>
					<div class="col-md-6" id="tabla_permisos">
						<table class="table table-striped">
							<thead>
								<tr>
									<th rowspan="2" class="text-center" style="vertical-align:middle;"><?php echo lang("general_procedure_status"); ?></th>
									<th colspan="2" class="text-center"><?php echo lang("total"); ?></th>
								</tr>
								<tr>
									<th class="text-center">N°</th>
									<th class="text-center">%</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-left"><strong><?php echo lang("total_applicable_procedures"); ?></strong></td>
									<td class="text-right"><?php echo to_number_project_format($total_permisos_aplicables, $id_proyecto); ?></td>
									<td class="text-right"><?php echo to_number_project_format(100, $id_proyecto); ?> %</td>
								</tr>
								<?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
									<tr>
										<td class="text-left"><?php echo $estado["nombre_estado"]?></td>
										<td class="text-right"><?php echo to_number_project_format($estado["cantidad_categoria"], $id_proyecto); ?></td>
										<td class="text-right"><?php echo to_number_project_format(($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables, $id_proyecto); ?> %</td>
                                    </tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<?php }?>
					<?php if($permitting_settings->grafico == 1){ ?>
					<div class="col-md-6" id="grafico_permisos">
						<div class="panel panel-default">
						   <div class="page-title clearfix panel-success">
							  <div class="pt10 pb10 text-center"> <?php echo lang("total_procedures"); ?> </div>
						   </div>
						   <div class="panel-body">
							  <div id="grafico_tramitaciones_totales" style="height: 240px;"></div>
						   </div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php }  ?>
    
</div>
<script type="text/javascript">
$(document).ready(function () {
	
	//CONSUMOS
	
	<?php if($ocultar_tabla_consumos_volumen) {?>
		$("#tabla_consumo_volumen").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_consumos_volumen) {?>
		$("#grafico_consumo_volumen").remove();
		//$("#titulo_tabla_consumo_volumen").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('consumptions'); ?> (<?php //echo $unidad_volumen; ?>)</strong></h4>");
	<?php } ?>
	
	<?php if($ocultar_tabla_consumos_masa) {?>
		$("#tabla_consumo_masa").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_consumos_masa) {?>
		$("#grafico_consumo_masa").remove();
		//$("#titulo_tabla_consumo_masa").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('consumptions'); ?> (<?php //echo $unidad_masa; ?>)</strong></h4>");
	<?php } ?>
	
	//RESIDUOS
	<?php if($ocultar_tabla_residuos_volumen) {?>
		$("#tabla_residuo_volumen").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_residuos_volumen) {?>
		$("#grafico_residuo_volumen").remove();
		//$("#titulo_tabla_residuo_volumen").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('waste'); ?> (<?php //echo $unidad_volumen; ?>)</strong></h4>");
	<?php } ?>
	
	<?php if($ocultar_tabla_residuos_masa) {?>
		$("#tabla_residuo_masa").remove();
	<?php } ?>
	
	<?php if($ocultar_grafico_residuos_masa) {?>
		
		//$("#titulo_tabla_residuo_masa").append("<h4 style='float:unset !important; text-align:center;'><strong><?php //echo lang('waste'); ?> (<?php //echo $unidad_masa; ?>)</strong></h4>");
	<?php } ?>
	
	<?php if($ocultar_tabla_consumos_volumen && $ocultar_grafico_consumos_volumen && $ocultar_tabla_consumos_masa && $ocultar_grafico_consumos_masa) { ?>
		$("#div_consumos").remove();
	<?php } ?>
	
	<?php if($ocultar_tabla_residuos_volumen && $ocultar_grafico_residuos_volumen && $ocultar_tabla_residuos_masa && $ocultar_grafico_residuos_masa) { ?>
		$("#div_residuos").remove();
	<?php } ?>
	
	
	//General Settings
	var decimals_separator = AppHelper.settings.decimalSeparator;
	var thousands_separator = AppHelper.settings.thousandSeparator;
	var decimal_numbers = AppHelper.settings.decimalNumbers;	
	
	var maxHeight = Math.max.apply(null, $("#page-content .estacion").map(function (){
		return $(this).height();
	}));
	$("#page-content .estacion").height(maxHeight);
	
	<?php if($consumptions_settings->tabla == 0){ ?>
		$("#grafico_compromisos").attr("class","col-md-12");
	<?php } ?>
	<?php if($consumptions_settings->grafico == 0){ ?>
		$("#tabla_compromisos").attr("class","col-md-12");
	<?php }?>
	
	<?php if($permitting_settings->tabla == 0){ ?>
		$("#grafico_permisos").attr("class","col-md-12");
	<?php } ?>
	<?php if($permitting_settings->grafico == 0){ ?>
		$("#tabla_permisos").attr("class","col-md-12");
	<?php }?>
	
	$('#consumo_volumen').highcharts({
		chart: {
			zoomType: 'x',
			reflow: true,
			vresetZoomButton: {
				position: {
					align: 'left',
					x: 0
				}
			},
			type: 'column',
			events: {
				load: function(event){
					
				}
			} 
		},
		title: {
			//text: '<strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_volumen; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			min: 0,
			categories: <?php echo json_encode($array_grafico_consumos_volumen_categories); ?>,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>'
			},
			labels:{
				formatter: function(){
					return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
				}
			},
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_volumen; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		colors: ['#4CD2B1','#5C6BC0'],
		series: [{
			name: "<?php echo lang("accumulated"); ?>",
			//data: [1600,1300]
			data: <?php echo json_encode($array_grafico_consumos_volumen_data); ?>,
		}]
	});
	
	$('#consumo_masa').highcharts({
		chart: {
			zoomType: 'x',
			reflow: true,
			vresetZoomButton: {
				position: {
					align: 'left',
					x: 0
				}
			},
			type: 'column',
			events: {
				load: function(event){
					
				}
			} 
		},
		title: {
			//text: '<strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_masa; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			min: 0,
			categories: <?php echo json_encode($array_grafico_consumos_masa_categories); ?>,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>'
			},
			labels:{
				formatter: function(){
					return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					//return (this.value);
				}
			},
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_masa; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		colors: ['#4CD2B1','#5C6BC0'],
		series: [{
			name: "<?php echo lang("accumulated"); ?>",
			//data: [7,6,9,19,10]
			data: <?php echo json_encode($array_grafico_consumos_masa_data); ?>,
		}]
	});
	
	
	$('#consumo_energia').highcharts({
		chart: {
			zoomType: 'x',
			reflow: true,
			vresetZoomButton: {
				position: {
					align: 'left',
					x: 0
				}
			},
			type: 'column',
			events: {
				load: function(event){
					
				}
			} 
		},
		title: {
			//text: '<strong><?php echo lang('consumptions'); ?> (<?php echo $unidad_energia; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			min: 0,
			categories: <?php echo json_encode($array_grafico_consumos_energia_categories); ?>,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_energia_nombre_real.' ('.$unidad_energia.')'; ?>'
			},
			labels:{
				formatter: function(){
					return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					//return (this.value);
				}
			},
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_energia; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		colors: ['#4CD2B1','#5C6BC0'],
		series: [{
			name: "<?php echo lang("accumulated"); ?>",
			//data: [7,6,9,19,10]
			data: <?php echo json_encode($array_grafico_consumos_energia_data); ?>,
		}]
	});
	
	
	$('#residuo_volumen').highcharts({
		chart: {
			zoomType: 'x',
			reflow: true,
			vresetZoomButton: {
				position: {
					align: 'left',
					x: 0
				}
			},
			type: 'column',
			events: {
				load: function(event){
					
				}
			} 
		},
		title: {
			//text: '<strong><?php echo lang('waste'); ?> (<?php echo $unidad_volumen; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			min: 0,
			categories: <?php echo json_encode($array_grafico_residuos_volumen_categories); ?>,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_volumen_nombre_real.' ('.$unidad_volumen.')'; ?>'
			},
			labels:{
				formatter: function(){
					return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator)
					//return (this.value);
				}
			},
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_volumen; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		colors: ['#4CD2B1','#5C6BC0'],
		series: [{
			name: "<?php echo lang("accumulated"); ?>",
			//data: [7,6,9,19,10]
			data: <?php echo json_encode($array_grafico_residuos_volumen_data); ?>,
		}]
	});
	
	
	$('#residuo_masa').highcharts({
		chart: {
			zoomType: 'x',
			reflow: true,
			vresetZoomButton: {
				position: {
					align: 'left',
					x: 0
				}
			},
			type: 'column',
			events: {
				load: function(event){
					
				}
			} 
		},
		title: {
			//text: '<strong><?php echo lang('waste'); ?> (<?php echo $unidad_masa; ?>)</strong>'
			text: ''
		},
		subtitle: {
			text: ''
		},
		exporting:{
			enabled: false
		},
		xAxis: {
			min: 0,
			categories: <?php echo json_encode($array_grafico_residuos_masa_categories); ?>,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: '<?php echo $unidad_masa_nombre_real.' ('.$unidad_masa.')'; ?>'
			},
			labels:{
				formatter: function(){
					return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					//return (this.value);
				}
			},
		},
		credits: {
			enabled: false
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			//pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'+'<td style="padding:0"><b>{point.y:.1f} m³</b></td></tr>',
			pointFormatter: function(){
				return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+' <?php echo $unidad_masa; ?></b></td></tr>';
			},
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels: {
					enabled: true,
					//rotation: -90,
					color: '#000000',
					align: 'center',
					//format: '{point.y:.0f}', // one decimal
					formatter: function(){
						return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
					},
					//y: -2, // 10 pixels down from the top
					style: {
						fontSize: '10px',
						fontFamily: 'Segoe ui, sans-serif'
					}
				}
			}
		},
		colors: ['#4CD2B1','#5C6BC0'],
		series: [{
			name: "<?php echo lang("accumulated"); ?>",
			//data: [7,6,9,19,10]
			data: <?php echo json_encode($array_grafico_residuos_masa_data); ?>,
		}]
	});
	
	
	
		<?php if(!empty(array_filter($total_cantidades_estados_evaluados))){ ?>
		
		$('#grafico_cumplimientos_totales').highcharts({
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
				//pointFormat: '{series.name}: <b>{point.y}%</b>'
			},
			plotOptions: {
				pie: {
				//size: 80,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
				filename: "<?php echo lang("total_compliances"); ?>",
				buttons: {
					contextButton: {
						enabled: false,
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
			colors: [
				<?php 
					foreach($total_cantidades_estados_evaluados as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
			],
			//colors: ['#398439', '#ac2925', '#d58512'],
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
				<?php foreach($total_cantidades_estados_evaluados as $estado) { ?>
					{
						name: '<?php echo $estado["nombre_estado"]; ?>',
						y: <?php echo ($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables; /*echo to_number_project_format($estado["porcentaje"], $id_proyecto);*/ ?>
					},
				<?php } ?>
				
				]
			}]
		});
		
		<?php }else{?>
				$('#grafico_cumplimientos_totales').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		<?php } ?>
	
	
		<?php if(!empty(array_filter($total_cantidades_estados_evaluados_permisos))){ ?>
		
		$('#grafico_tramitaciones_totales').highcharts({
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
				//pointFormat: '{series.name}: <b>{point.y}%</b>'
			},
			plotOptions: {
				pie: {
				//size: 80,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %',
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
				filename: "<?php echo lang("total_permittings"); ?>",
				buttons: {
					contextButton: {
						enabled: false,
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
			colors: [
				<?php 
					foreach($total_cantidades_estados_evaluados_permisos as $estado) { 
						echo "'".$estado["color"]."',";
					}
				?>
			],
			//colors: ['#398439', '#ac2925', '#d58512'],
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
				
				<?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
					{
						name: '<?php echo $estado["nombre_estado"]; ?>',
						y: <?php echo ($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables; /*echo to_number_project_format($estado["porcentaje"], $id_proyecto);*/ ?>
					},
				<?php } ?>
				
				]
			}]
		});
		
		<?php }else{?>
				$('#grafico_tramitaciones_totales').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
		<?php } ?>	
	
	/*$(document).on('click', 'a.accordion-toggle', function () {
		
		var icon = $(this).find('i');
		
		if($(this).hasClass('collapsed')){
			icon.removeClass('fa fa-minus-circle font-16');
			icon.addClass('fa fa-plus-circle font-16');
		} else {
			icon.removeClass('fa fa-plus-circle font-16');
			icon.addClass('fa fa-minus-circle font-16');
		}

	});*/

	$(document).on('click', 'a.accordion-toggle', function () {
		
		$('a.accordion-toggle i').removeClass('fa fa-minus-circle font-16');
		$('a.accordion-toggle i').addClass('fa fa-plus-circle font-16');
		
		var icon = $(this).find('i');
		
		if($(this).hasClass('collapsed')){
			icon.removeClass('fa fa-minus-circle font-16');
			icon.addClass('fa fa-plus-circle font-16');
		} else {
			icon.removeClass('fa fa-plus-circle font-16');
			icon.addClass('fa fa-minus-circle font-16');
		}

	});

	$("[data-toggle='popover']").popover({
		container: 'body',
		trigger:'click',
		placement: 'left',
		title: '<?php echo lang("action_plan"); ?>',
		html:true,
		content: function() {
			id_action_plan_content = $(this).attr("data-action_plan_content_id");
        	return $('#'+id_action_plan_content).html();
        }
	});

	$('body').on('click', function (e) {
		//did not click a popover toggle or popover
		if ($(e.target).data('toggle') !== 'popover'
			&& $(e.target).parents('.popover.in').length === 0) { 
			$('[data-toggle="popover"]').popover('hide');
		}
	});

});
</script> 