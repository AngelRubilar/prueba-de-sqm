<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$proyecto->id); ?>"><?php echo $proyecto->title; ?> /</a>
  <a class="breadcrumb-item" href="#"><?php echo lang("environmental_footprints"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("functional_units"); ?>"><?php echo lang("functional_units"); ?></a>
</nav>
	
    <div class="row">  
        <div class="col-md-12">
          <div class="page-title clearfix" style="background-color:#FFF;">
            <h1><i class="fa fa-th-large"></i> <?php echo $proyecto->title . " | " . lang("functional_units"); ?></h1>
          </div>
        </div>
    </div>
    
    <?php if($puede_ver == 1) { ?>

		<?php if(count($unidades_funcionales)) { ?>
        
            <?php echo form_open(get_uri("#"), array("id" => "functional_units-form", "class" => "general-form", "role" => "form")); ?>
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
                                <a href="#" class="btn btn-danger pull-right" id="functional_units_pdf" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo lang("export_to_pdf"); ?></a>
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
      
        <div id="functional_units_group">
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
                //$valor_uf = $unidad_funcional->valor;
                $valor_uf = get_functional_unit_value($client_info->id, $proyecto->id, $unidad_funcional->id, NULL, NULL);
                
                $html = '';
                foreach($huellas as $huella){
                    //var_dump($criterios_calculos);
                    
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
                            $elementos = $Calculation_model->get_records_of_forms_for_calculation($id_proyecto, $id_formulario, $id_campo_fc, $criterio_fc, $id_categoria)->result();
                            
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
                    $html .= '<div class="text-center p15">'.to_number_project_format($total_huella,$id_proyecto).'</div>';
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

<script type="text/javascript">
    $(document).ready(function () {

		var maxHeight_uf = Math.max.apply(null, $("#page-content .huella").map(function (){
			return $(this).find("div.b-b").height();
		}));
		
		$("#page-content .huella > div.b-b").height(maxHeight_uf);
		
		setDatePicker("#start_date");
		setDatePicker("#end_date");

		$("#functional_units_pdf").on("click", function(e) {	
			
			appLoader.show();
			
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			
			$.ajax({
				url:  '<?php echo_uri("functional_units/get_pdf") ?>',
				type:  'post',
				data:{
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
				url:  '<?php echo_uri("functional_units/borrar_temporal") ?>',
				type:  'post',
				data: {uri:uri},
				//dataType:'json',
				success: function(respuesta){
					appLoader.hide();
				}
	
			});
	
		}
		
		$("#functional_units-form").appForm({
            ajaxSubmit: false
        });
		$("#functional_units-form").submit(function(e){
			e.preventDefault();
			return false;
		});
		
		$('#btn_generar').click(function(){
			
			$('#export_pdf').attr('disabled', true);
			
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			
			if(start_date && end_date){
				if((start_date < end_date) || (start_date == end_date)){
	
					$.ajax({
						url:'<?php echo_uri("functional_units/get_functional_units"); ?>',
						type:'post',
						data:{
							start_date: start_date,
							end_date: end_date
						},beforeSend: function() {
					   		$('#functional_units_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
						},
						success: function(respuesta){;
							$('#functional_units_group').html('<div class="panel"><div class="panel-default">' + respuesta + '<div><div>');	
							$('#export_pdf').removeAttr('disabled');
						}
					});	
					
				}
			}
			
		});
		
		$('#btn_clean').click(function(){
			
			$('#export_pdf').attr('disabled', true);
			$('#start_date').val("");
			$('#end_date').val("");
			
			$.ajax({
				url:'<?php echo_uri("functional_units/get_functional_units"); ?>',
				type:'post',
				beforeSend: function() {
					$('#functional_units_group').html('<div class="panel"><div style="padding:20px;"><div class="circle-loader"></div><div><div>');
				},
				success: function(respuesta){;
					$('#functional_units_group').html('<div class="panel"><div class="panel-default">' + respuesta + '<div><div>');	
					$('#export_pdf').removeAttr('disabled');
				}
			});	
			
		});
	
    });
</script> 