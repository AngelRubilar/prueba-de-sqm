<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<div>
<span style="float: left !important;"><img src="<?php echo $logo_cliente; ?>"></span>
</div>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $project_info->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("report"); ?>
</h2>
<div align="center">
	<?php $hora = convert_to_general_settings_time_format($project_info->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $project_info->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $project_info->id).' '.lang("at").' '.$hora; ?>
</div>

<?php if($puede_ver_antecedentes_proyecto) { ?>
	<h2><?php echo lang("project_background"); ?></h2>
    <table cellspacing="0" cellpadding="4" border="1">
        <tr>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("client"); ?></td>
            <td width="160px"><?php echo $client_info->company_name; ?></td>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("environmental_authorization"); ?></td>
            <td width="160px"><?php echo $autorizacion_ambiental; ?></td>
        </tr>
        <tr>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("project"); ?></td>
            <td width="160px"><?php echo $project_info->title; ?></td>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("report_project_stage"); ?></td>
            <td><?php echo $etapa_proyecto; ?></td>
        </tr>
        <tr>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("location"); ?></td>
            <td><?php echo $ubicacion_proyecto; ?></td>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("start_date_of_project"); ?></td>
            <td><?php echo get_date_format($project_info->start_date, $project_info->id); ?></td>
        </tr>
        <tr>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("rut"); ?></td>
            <td><?php echo $rut = (($project_info->client_label_rut)?$project_info->client_label_rut:'-'); ?></td>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("deadline_of_project"); ?></td>
            <td><?php echo get_date_format($project_info->deadline, $project_info->id); ?></td>
        </tr>
        <tr>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>;"><?php echo lang("generate_by"); ?></td>
            <td><?php echo $usuario_info->first_name.' '.$usuario_info->last_name; ?></td>
            <td style="background-color:<?php echo $client_info->color_sitio; ?>; border: 1px solid black; "><?php echo lang("record_considerate_since"); ?></td>
            <td><?php echo get_date_format($start_date, $project_info->id); ?></td>	
        </tr>
    </table>
    <table cellspacing="0" cellpadding="4" style="border: 1px solid white;">
        <tr>
            <td width="160px"></td>
            <td width="160px"></td>
            <td width="160px" style="background-color:<?php echo $client_info->color_sitio; ?>; border: 1px solid black;"><?php echo lang("record_considerate_until"); ?></td>
            <td width="160px" style="border: 1px solid black;"><?php echo get_date_format($end_date, $project_info->id); ?></td>
        </tr>
    </table>
<?php } ?>

<?php if($puede_ver_compromisos_rca) { ?>
	<br pagebreak="true">
	<h2><?php echo lang("environmental_commitments").' - '.$autorizacion_ambiental; ?></h2>
    <!-- Tabla Compromisos RCA -->
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("compliance_status"); ?></th>
                <?php foreach($evaluados_matriz_compromiso as $evaluado) { ?>
                    <th colspan="2" style="text-align: center;"><?php echo $evaluado->nombre_evaluado; ?></th>
                <?php } ?>
            </tr>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <?php foreach($evaluados_matriz_compromiso as $evaluado) { ?>
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">%</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="text-align: left;"><?php echo lang("total_applicable_compromises"); ?></th>
                <?php foreach($evaluados_matriz_compromiso as $evaluado) { ?>
                    <td style="text-align: right;"><?php echo to_number_project_format(array_sum($array_total_por_evaluado[$evaluado->id]), $project_info->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format(100, $project_info->id).' %'; ?></td>
                <?php } ?>
            </tr>
            <?php foreach($array_estados_evaluados_rca as $estado_evaluado){ ?>
                <tr>
                    <td style="text-align: left;"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                    <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                        <?php
                            $total_evaluado = array_sum($array_total_por_evaluado[$id_evaluado]);
							if($total_evaluado == 0){
								$porcentaje = 0;
							} else {
								$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
							}
                        ?>
                        <td style="text-align: right;"><?php echo to_number_project_format($evaluado["cant"], $project_info->id); ?></td> 
                        <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $project_info->id).' %'; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Gráfico Compromisos RCA -->
    <table border="0">
        <tr>
            <td align="center" width="45%">
            	<?php if($grafico_cumplimientos_totales){ ?>
                	<div style="font-size:20px">&nbsp;</div>
            		<img src="<?php echo $grafico_cumplimientos_totales; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <div style="font-size:20px">&nbsp;</div>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
            <td width="55%">
                <table cellspacing="0" cellpadding="4" border="1">
                <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("compromise"); ?></th>
                    <th style="text-align: center;"><?php echo lang("critical_level"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible"); ?></th>
                    <th style="text-align: center;"><?php echo lang("closing_term"); ?></th>
                </tr>
                
                <?php if($array_compromisos_evaluaciones_no_cumple){ ?>
                
					<?php foreach($array_compromisos_evaluaciones_no_cumple as $row){ ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row->nombre_compromiso; ?></td>
                            <td style="text-align: left;"><?php echo $row->criticidad; ?></td>
                            <td style="text-align: left;"><?php echo $row->responsable_reporte; ?></td>
                            <td style="text-align: left;"><?php echo get_date_format($row->plazo_cierre, $project_info->id); ?></td>
                        </tr>
                    <?php } ?>
                    
                <?php } else { ?>
                	<tr>
                        <td colspan="4" style="text-align: center;"><?php echo lang("no_information_available"); ?></td>
                    </tr>
                <?php } ?>
                </table>
            </td>
        </tr>
    </table>
        
<?php } ?>


<?php if($puede_ver_compromisos_reportables) { ?>
	<br pagebreak="true">
	<h2><?php echo lang("environmental_reportable_commitments").' - '.$autorizacion_ambiental; ?></h2>
	<!-- Tabla Compromisos Reportables -->
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("general_compliance_status"); ?></th>
                <th colspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("sub_total"); ?></th>
            </tr>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th style="text-align: center;">N°</th>
                <th style="text-align: center;">%</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($array_estados_evaluados_reportables as $estado_evaluado) { ?>
            	<?php 
					if($total_evaluado == 0){
						$porcentaje = 0;
					} else {
						$porcentaje = ($estado_evaluado["cant"] * 100) / ($total_evaluado);
					}
				?>
                <tr>
                    <th style="text-align: left;"><?php echo $estado_evaluado["nombre_estado"]; ?></th>
                    <td style="text-align: right;"><?php echo to_number_project_format($estado_evaluado["cant"], $project_info->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $project_info->id).' %'; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Gráfico Compromisos Reportables -->
    <table border="0">
        <tr>
            <td align="center" width="45%">
            	<?php if($grafico_cumplimientos_reportables){ ?>
                	<div style="font-size:20px">&nbsp;</div>
            		<img src="<?php echo $grafico_cumplimientos_reportables; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                	<div style="font-size:20px">&nbsp;</div>
                	<?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
            <td width="55%">
                <table cellspacing="0" cellpadding="4" border="1">
                <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("compromise"); ?></th>
                    <th style="text-align: center;"><?php echo lang("critical_level"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible"); ?></th>
                    <th style="text-align: center;"><?php echo lang("closing_term"); ?></th>
                </tr>
                
                <?php if($array_compromisos_reportables_evaluaciones_no_cumple){ ?>
                
					<?php foreach($array_compromisos_reportables_evaluaciones_no_cumple as $row){ ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row->nombre_compromiso; ?></td>
                            <td style="text-align: left;"><?php echo $row->criticidad; ?></td>
                            <td style="text-align: left;"><?php echo $row->responsable_reporte; ?></td>
                            <td style="text-align: left;"><?php echo get_date_format($row->plazo_cierre, $project_info->id); ?></td>
                        </tr>
                    <?php } ?>
                    
                <?php } else { ?>
                	<tr>
                        <td colspan="4" style="text-align: center;"><?php echo lang("no_information_available"); ?></td>
                    </tr>
                <?php } ?>
                
                </table>
            </td>
        </tr>
    </table>
        
<?php } ?>

<?php if($puede_ver_consumos) { ?>
	<br pagebreak="true">
    <h2><?php echo lang("consumptions"); ?></h2>
    <!-- Tabla Consumos -->
    <table cellspacing="0" cellpadding="4" border="1">
    	<tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        	<th colspan="4" style="text-align: center;"><?php echo lang("consumptions"); ?></th>
        </tr>
        <tr>
			<th style="text-align: center;"><?php echo lang("categories"); ?></th>
			<th style="text-align: center;"><?php echo lang("Reported_in_period"); ?></th>
			<th style="text-align: center;"><?php echo lang("accumulated"); ?></th>
            <th style="text-align: center;"><?php echo lang("declared"); ?></th>
		</tr>
        <?php foreach ($tabla_consumo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php 
			
				$arreglo_valores_acumulados = $tabla_consumo_volumen_acumulados[$id_categoria];

				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
					$nombre_categoria = $row_categoria->nombre;
				}
				
				// DECLARADO
				$declarado = $this->Thresholds_model->get_one_where(
					array(
						'id_client' => $client_info->id, 
						'id_project' => $project_info->id, 
						'id_module' => 5, 
						'id_category' => $id_categoria, 
						'id_unit_type' => 2,
						'deleted' => 0
					)
				);

				if($declarado->threshold_value){

					$fila_conversion = $this->Conversion_model->get_one_where(
						array(
							"id_tipo_unidad" => $declarado->id_unit_type,// 2 (VOLUMEN)
							"id_unidad_origen" => $declarado->id_unit,
							"id_unidad_destino" => $id_unidad_volumen
						)
					);
					$valor_transformacion = $fila_conversion->transformacion;

					$cant_declarado = $declarado->threshold_value * $valor_transformacion;
				}else{
					$cant_declarado = 0;
				}

				$alerta = (array_sum($arreglo_valores_acumulados) > $cant_declarado)?"background-color: #fcf8e3;":"";
				$reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
				$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
				$declarado = to_number_project_format($cant_declarado, $project_info->id);

			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right; <?php echo $alerta; ?>"><?php echo $acumulado; ?></td>
                <td style="text-align: right;"><?php echo $declarado; ?></td>
            </tr>
            
        <?php } ?>
        
        <?php foreach ($tabla_consumo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php 
			
				$arreglo_valores_acumulados = $tabla_consumo_masa_acumulados[$id_categoria];

				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
					$nombre_categoria = $row_categoria->nombre;
				}
				
				// DECLARADO
				$declarado = $this->Thresholds_model->get_one_where(
					array(
						'id_client' => $client_info->id, 
						'id_project' => $project_info->id, 
						'id_module' => 5, 
						'id_category' => $id_categoria, 
						'id_unit_type' => 1,
						'deleted' => 0
					)
				);

				if($declarado->threshold_value){

					$fila_conversion = $this->Conversion_model->get_one_where(
						array(
							"id_tipo_unidad" => $declarado->id_unit_type,// 1 (MASA)
							"id_unidad_origen" => $declarado->id_unit,
							"id_unidad_destino" => $id_unidad_masa
						)
					);
					$valor_transformacion = $fila_conversion->transformacion;

					$cant_declarado = $declarado->threshold_value * $valor_transformacion;
				}else{
					$cant_declarado = 0;
				}
				
				$alerta = (array_sum($arreglo_valores_acumulados) > $cant_declarado)?"background-color: #fcf8e3;":"";
				$reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
				$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
				$declarado = to_number_project_format($cant_declarado, $project_info->id);
				
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right; <?php echo $alerta; ?>"><?php echo $acumulado; ?></td>
                <td style="text-align: right;"><?php echo $declarado; ?></td>
            </tr>
            
        <?php } ?>
        
        
        <?php foreach ($tabla_consumo_energia_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php 
			
			$arreglo_valores_acumulados = $tabla_consumo_energia_acumulados[$id_categoria];

			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}

			// DECLARADO
			$declarado = $this->Thresholds_model->get_one_where(
				array(
					'id_client' => $client_info->id, 
					'id_project' => $project_info->id, 
					'id_module' => 5, 
					'id_category' => $id_categoria, 
					'deleted' => 0
				)
			);

			if($declarado->threshold_value){

				$fila_conversion = $this->Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => $declarado->id_unit_type,// (ENERGIA)
						"id_unidad_origen" => $declarado->id_unit,
						"id_unidad_destino" => $id_unidad_energia
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;

				$cant_declarado = $declarado->threshold_value * $valor_transformacion;
			}else{
				$cant_declarado = 0;
			}

			$alerta = (array_sum($arreglo_valores_acumulados) > $cant_declarado)?"background-color: #fcf8e3;":"";
			$reportado = to_number_project_format(array_sum($arreglo_valores), $project_info->id);
			$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id);
			$declarado = to_number_project_format($cant_declarado, $project_info->id);
			
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_energia.')'; ?></td>
                <td style="text-align: right;"><?php echo $reportado; ?></td>
                <td style="text-align: right; <?php echo $alerta; ?>"><?php echo $acumulado; ?></td>
                <td style="text-align: right;"><?php echo $declarado; ?></td>
            </tr>
            
        <?php } ?>
        
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_consumo_volumen; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_consumo_masa; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_consumo_energia; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
<?php } ?>


<?php if($puede_ver_consumos) { ?>
	<br pagebreak="true">
    <h2><?php echo lang("waste"); ?></h2>
    <!-- Tabla Consumos -->
    <table cellspacing="0" cellpadding="4" border="1">
    	<tr style="background-color:<?php echo $client_info->color_sitio; ?>;">
        	<th colspan="3" style="text-align: center;"><?php echo lang("waste"); ?></th>
        </tr>
        <tr>
			<th style="text-align: center;"><?php echo lang("categories"); ?></th>
			<th style="text-align: center;"><?php echo lang("Reported_in_period"); ?></th>
			<th style="text-align: center;"><?php echo lang("accumulated"); ?></th>
		</tr>
        
        <?php foreach($tabla_residuo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>
        	
            <?php
            
			$arreglo_valores_acumulados = $tabla_residuo_volumen_acumulados[$id_categoria];
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $client_info->id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			?>
            
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_volumen.')'; ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $project_info->id); ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id); ?></td>
            </tr>
            
        <?php } ?>
        
        <?php foreach($tabla_residuo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
        
        	<?php
            
			$arreglo_valores_acumulados = $tabla_residuo_masa_acumulados[$id_categoria];
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			?>
        	
            <tr>
            	<td><?php echo $nombre_categoria.' ('.$unidad_masa.')'; ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $project_info->id); ?></td>
                <td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $project_info->id); ?></td>
            </tr>
            
        <?php } ?>
    </table>
        
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuo_volumen; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuo_masa; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>

<?php } ?>

<?php if($puede_ver_permittings) { ?>
<br pagebreak="true">
    <h2><?php echo lang("environmental_permittings"); ?></h2>
	<!-- Tabla Permisos -->
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("general_procedure_status"); ?></th>
                <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
                    <th colspan="2" style="text-align: center;"><?php echo $evaluado->nombre_evaluado; ?></th>
                <?php } ?>
            </tr>
            <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">%</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="text-align: left;"><?php echo lang("total_applicable_permittings"); ?></th>
                <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
                    <td style="text-align: right;"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_permiso[$evaluado->id]), $project_info->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format(100, $project_info->id).' %'; ?></td>
                <?php } ?>
            </tr>
            <?php foreach($array_estados_evaluados_permiso as $estado_evaluado){ ?>
                <tr>
                    <td style="text-align: left;"><?php echo $estado_evaluado["nombre_estado"]; ?></td>
                    <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                        <?php
                            $total_evaluado = array_sum($array_total_por_evaluado[$id_evaluado]);
							if($total_evaluado == 0){
								$porcentaje = 0;
							} else {
								$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
							}
                        ?>
                        <td style="text-align: right;"><?php echo to_number_project_format($evaluado["cant"], $project_info->id); ?></td> 
                        <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $project_info->id).' %'; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Gráfico Permisos -->
    <table border="0">
        <tr>
            <td align="center" width="45%">
            	<?php if($grafico_cumplimientos_totales_permisos){ ?>
                	<div style="font-size:20px">&nbsp;</div>
            		<img src="<?php echo $grafico_cumplimientos_totales_permisos; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                	<div style="font-size:20px">&nbsp;</div>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
            <td width="55%">
                <table cellspacing="0" cellpadding="4" border="1">
                <tr style="background-color: <?php echo $client_info->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("compromise"); ?></th>
                    <th style="text-align: center;"><?php echo lang("critical_level"); ?></th>
                    <th style="text-align: center;"><?php echo lang("responsible"); ?></th>
                    <th style="text-align: center;"><?php echo lang("closing_term"); ?></th>
                </tr>

                <?php if($array_permisos_evaluaciones_no_cumple){ ?>
                
					<?php foreach($array_permisos_evaluaciones_no_cumple as $row){ ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row->nombre_permiso; ?></td>
                            <td style="text-align: left;"><?php echo $row->criticidad; ?></td>
                            <td style="text-align: left;"><?php echo $row->responsable_reporte; ?></td>
                            <td style="text-align: left;"><?php echo get_date_format($row->plazo_cierre, $project_info->id); ?></td>
                        </tr>
                    <?php } ?>
                    
                <?php } else { ?>
                	<tr>
                        <td colspan="4" style="text-align: center;"><?php echo lang("no_information_available"); ?></td>
                    </tr>
                <?php } ?>
                
                </table>
            </td>
        </tr>
    </table>

<?php } ?>

</body>