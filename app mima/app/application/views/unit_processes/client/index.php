<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("environmental_footprints"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("unit_processes"); ?>"><?php echo lang("unit_processes"); ?></a>
</nav>

  <div class="row">  
    <div class="col-md-12">
      <div class="page-title clearfix" style="background-color:#FFF;">
	  	<h1><i class="fa fa-th-large"></i> <?php echo $project_info->title . " | " . lang("unit_processes"); ?></h1>
      </div>
    </div>
  </div>
  
  <?php if($puede_ver == 1) { ?>
  
		<?php if(count($unidades_funcionales)) { ?>
         
         <?php echo form_open(get_uri("#"), array("id" => "unit_processes-form", "class" => "general-form", "role" => "form")); ?>
          <div class="panel panel-default">
            <div class="panel-body">
            
                <div class="col-md-6">
                
                    <div class="form-group multi-column">
                
                        <label class="col-md-3" style="padding-right:0px;margin-right:0px;"><?php echo lang('date_range') ?></label>
        
                        <!--<label for="" class="col-md-2"><?php echo lang('since') ?></label>-->
                        <div class="col-md-4">
                            <?php 
                                echo form_input(array(
                                    "id" => "start_date",
                                    "name" => "start_date",
                                    "value" => "",
                                    "class" => "form-control",
                                    "placeholder" => lang('since'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => lang("field_required"),
                                    //"data-rule-greaterThanOrEqual" => 'end_date',
                                    //"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                    "autocomplete" => "off",
                                ));
                            ?>
                        </div>
                    
                    
                        <!--<label for="" class="col-md-2"><?php echo lang('until') ?></label>-->
                        <div class="col-md-4">
                            <?php 
                                echo form_input(array(
                                    "id" => "end_date",
                                    "name" => "end_date",
                                    "value" => "",
                                    "class" => "form-control",
                                    "placeholder" => lang('until'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => lang("field_required"),
                                    "data-rule-greaterThanOrEqual" => "#start_date",
                                    "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                    "autocomplete" => "off",
                                ));
                            ?>
                        </div>
                        
                    </div>  
                                 
                </div>
                
                <div class="col-md-6">
                    <div class="pull-right">
                        <div class="btn-group" role="group">
                            <button id="btn_generar" type="submit" class="btn btn-primary"><span class="fa fa-eye"></span> <?php echo lang('generate'); ?></button>
                        </div>
                        
                        <div class="btn-group" role="group">
                            <a href="#" class="btn btn-danger pull-right" id="unit_processes_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a> 
                        </div>
                        <div class="btn-group" role="group">
                            <button id="btn_clean" type="button" class="btn btn-default">
                                <i class="fa fa-broom" aria-hidden="true"></i> <?php echo lang('clean_query'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
    
        </div>        
        <?php echo form_close(); ?>
        
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
    
          <?php /*?>
            <ul class="nav nav-tabs classic" role="tablist">
                <?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
                    <?php $active = ($key == 0)? "active":""; ?>
                    <li class="<?php echo $active; ?>"><a data-toggle="tab" href="#<?php echo $unidad_funcional->id; ?>_unidad_funcional"><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></a></li>
                
                <?php } ?>
            </ul>
            
            <div role="tabpanel" class="tab-pane fade active in" id="graficos_procesos" style="min-height: 200px;">
                  <div class="clearfix bg-white">
                    <div class="row" style="background-color:#E5E9EC;">
                      <div class="col-md-12">
                        
                          <div class="tab-content">
                            
        
                            <?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
                                
                                <?php $active = ($key == 0)? "active":""; ?>
                                
                                <div id="<?php echo $unidad_funcional->id; ?>_unidad_funcional" class="tab-pane fade in <?php echo $active; ?>">
            
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
                                          
                                             <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">
                                                <div class="panel panel-default">
                                                   <div class="page-title clearfix panel-success">
                                                      <!--<h3>Cambio climático</h3> -->
                                                      <div class="pt10 pb10 text-center"> <?php echo $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')'; ?> </div>
                                                   </div>
                                                   <div class="panel-body">
                                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                                      <div id="grafico_<?php echo $huella->id?>-uf_<?php echo $unidad_funcional->id?>" style="height: 240px;"></div>
                                                   </div>
                                                </div>
                                             </div>
                                           
                                           <?php } ?>
                                       
                                       </div>
                                
                                <div class="panel">
                                
                                    <div class="table-responsive">
                                        <table id="<?php echo $unidad_funcional->id; ?>_uf-table" class="display" cellspacing="0" width="100%">            
                                        </table>
                                    </div>
        
                                    <?php //echo $unidad_funcional->nombre; ?>
                                    
                                </div>
                                
                                </div>
        
                            <?php } ?>          
                            
                           </div>
                           
                       </div>
                     </div>
                   </div>
            </div>
            <?php */?>
        <?php
            $id_proyecto = $project_info->id;
            $id_metodologia = $project_info->id_metodologia;
        ?>
    
    </div>
    
    <?php } else { ?>
    
        <div class="row"> 
            <div class="col-md-12 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="app-alert-d1via" class="app-alert alert alert-warning alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                            <div class="app-alert-message"><?php echo lang("no_information_available"); ?></div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    
    <?php } ?>

<?php } else {?>

	<div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-warning alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
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

<style>
table[id$=_uf-table] th { font-size: 12px; }
table[id$=_uf-table] td { font-size: 11px; }
</style>
<!--<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>-->
<script id="script_index" type="text/javascript">


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
		$valor_uf = get_functional_unit_value($client_info->id, $project_info->id, $unidad_funcional->id, $start_date, $end_date);
		
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
	
/* 		$("#uf-table").appTable({
		source: '<?php echo_uri("unit_processes/list_data") ?>',
		columns: [
			{title: "<?php echo lang("unit_process") ?>", "class": "text-center w50"}
			<?php echo $columnas; ?>,
			{title: '<i class="fa fa-eye"></i>', "class": "text-center option w150"}
		],
		//printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5]),
		//xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5])
	}); */
	
	$("#unit_processes_pdf").on("click", function(e) {	
		
		appLoader.show();
		
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;	
		
		var graficos_huellas_unidades_funcionales = {};

		<?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
			
			 var graficos_huellas = {};
			
			 <?php foreach($huellas as $huella){ ?>

				var id = "grafico_<?php echo $huella->id; ?>-uf_<?php echo $unidad_funcional->id; ?>";

				$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#' + id).highcharts().options.title.text = "<?php echo $huella->nombre.'<br /> ('.$nombre_unidad_huella.' '.$huella->indicador.')'; ?>";
				$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = true;
				$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
				$('#' + id).highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
				$('#' + id).highcharts().options.plotOptions.pie.size = 150;
				$('#' + id).highcharts().options.legend.itemStyle.fontSize = "15px";
				$('#' + id).highcharts().options.title.style.fontSize = "23px";
				
				var chart = $('#' + id).highcharts().options.chart;
				var series = $('#' + id).highcharts().options.series;
				var title = $('#' + id).highcharts().options.title;
				var plotOptions = $('#' + id).highcharts().options.plotOptions;
				var colors = $('#' + id).highcharts().options.colors;
				var exporting = $('#' + id).highcharts().options.exporting;
				var credits = $('#' + id).highcharts().options.credits;
				var legend = $('#' + id).highcharts().options.legend;

				var obj = {};
				obj.options = JSON.stringify({
					"chart":chart,
					"title":title,
					"series":series,
					"plotOptions":plotOptions,
					"colors":colors,
					"exporting":exporting,
					"credits":credits,
					"legend":legend,
				});
				
				obj.type = 'image/png';
				obj.width = '1600';
				obj.scale = '2';
				obj.async = true;
				
				var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
				obj.globaloptions = JSON.stringify(globalOptions);
				
				var imagen_grafico = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
				
				$('#' + id).highcharts().options.plotOptions.pie.dataLabels.enabled = false;
				$('#' + id).highcharts().options.plotOptions.pie.size = null;
				$('#' + id).highcharts().options.legend.itemStyle.fontSize = "9px;";
				
			 	graficos_huellas[<?php echo $huella->id; ?>] = imagen_grafico;

			 <?php } ?>
			 
			 graficos_huellas_unidades_funcionales[<?php echo $unidad_funcional->id; ?>] = graficos_huellas;
		
		<?php } ?>

		$.ajax({
			url:  '<?php echo_uri("unit_processes/get_pdf") ?>',
			type:  'post',
			data: {
				imagenes_graficos: graficos_huellas_unidades_funcionales,
				start_date: start_date,
				end_date: end_date
			},
			//dataType:'json',
			success: function(respuesta){
				
				var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
				var link = document.createElement("a");
				link.download = respuesta;
				link.href = uri;
				link.click();
				
				borrar_temporal(uri);
			}

		});

	});
	
	function borrar_temporal(uri){
		
		$.ajax({
			url:  '<?php echo_uri("unit_processes/borrar_temporal") ?>',
			type:  'post',
			data: {uri:uri},
			//dataType:'json',
			success: function(respuesta){
				appLoader.hide();
			}

		});

	}
	
	function getChartName(obj){
		var tmp = null;
		$.support.cors = true;
		$.ajax({
			async: false,
			type: 'post',
			dataType: 'text',
			url : AppHelper.highchartsExportUrl,
			data: obj,
			crossDomain:true,
			success: function (data) {
				tmp = data.replace(/files\//g,'');
				tmp = tmp.replace(/.png/g,'');
			}
		});
		return tmp;
	}
	
	setDatePicker("#start_date");
	setDatePicker("#end_date");
	
	$("#unit_processes-form").appForm({
            ajaxSubmit: false
	});
	$("#unit_processes-form").submit(function(e){
		e.preventDefault();
		return false;
	});
	
	$('#btn_generar').click(function(){
				
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		if(start_date && end_date){
			if((start_date < end_date) || (start_date == end_date)){
				
				$('#unit_processes_pdf').attr('disabled', true);
				
				$.ajax({
					url:'<?php echo_uri("unit_processes/get_unit_processes"); ?>',
					type:'post',
					data:{
						start_date: start_date,
						end_date: end_date
					},beforeSend: function() {
						$('#unit_processes_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
					},
					success: function(respuesta){;
						$('#unit_processes_group').html(respuesta);	
						$('#unit_processes_pdf').removeAttr('disabled');
					}
				});	
				
			}
		}
		
	});
	
	$('#btn_clean').click(function(){
		
		$('#unit_processes_pdf').attr('disabled', true);
		$('#start_date').val("");
		$('#end_date').val("");
		
		$.ajax({
			url:'<?php echo_uri("unit_processes/get_unit_processes"); ?>',
			type:'post',
			beforeSend: function() {
				$('#unit_processes_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
			},
			success: function(respuesta){;
				$('#unit_processes_group').html(respuesta);	
				$('#unit_processes_pdf').removeAttr('disabled');
			}
		});	
		
	});
		
	
});
</script> 