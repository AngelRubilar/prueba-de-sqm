<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<div>
<span style="float: left !important;"><img src="<?php echo $logo_cliente; ?>"></span>
</div>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("project_report"); ?>
</h2>

<!--
<div align="center">
	<?php echo lang("datetime_download") . ": " . date('d/m/Y').' '.lang("at").' '.date('H:i:s'); ?>
</div>
-->

<h2><?php echo lang("project_background"); ?></h2>

<table cellspacing="0" cellpadding="4" border="1">
	<tr>
        <td width="160px" style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("client"); ?></td>
        <td width="160px"><?php echo $info_cliente->company_name; ?></td>
        <td width="160px" style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("environmental_authorization"); ?></td>
        <td width="160px"><?php echo lang("environmental_authorization"); ?></td>
    </tr>
    <tr>
        <td width="160px" style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("project"); ?></td>
        <td width="160px"><?php echo $info_proyecto->title; ?></td>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("phase"); ?></td>
        <td><?php echo $nombre_fase; ?></td>
    </tr>
    <tr>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("location"); ?></td>
        <td><?php echo $ubicacion; ?></td>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("start_date_of_project"); ?></td>
        <td><?php echo get_date_format($info_proyecto->start_date, $info_proyecto->id); ?></td>
    </tr>
    <tr>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("rut"); ?></td>
        <td><?php echo $info_cliente->rut; ?></td>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("deadline_of_project"); ?></td>
        <td><?php echo get_date_format($info_proyecto->deadline, $info_proyecto->id); ?></td>
    </tr>
    <tr>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("generate_by"); ?></td>
        <td><?php echo $usuario->first_name.' '.$usuario->last_name; ?></td>
        <td style="background-color:<?php echo $info_cliente->color_sitio; ?>; border: 1px solid black; "><?php echo lang("record_considerate_since"); ?></td>
        <td><?php echo get_date_format($fecha_desde, $info_proyecto->id); ?></td>	
    </tr>
</table>
<table cellspacing="0" cellpadding="4" style="border: 1px solid white;">
    <tr>
        <td width="160px"></td>
        <td width="160px"></td>
        <td width="160px" style="background-color:<?php echo $info_cliente->color_sitio; ?>; border: 1px solid black;"><?php echo lang("record_considerate_until"); ?></td>
        <td width="160px" style="border: 1px solid black;"><?php echo get_date_format($fecha_hasta, $info_proyecto->id); ?></td>
    </tr>
</table>

<?php if($report_config->consumptions){ ?>
    
    <br>
    <!-- Tabla Consumos -->
    <h2><?php echo lang("consumptions"); ?></h2>
    
    <table border="1" cellpadding="4">
       
        <tr style="background-color:<?php echo $info_cliente->color_sitio; ?>;">
        	<th colspan="3" style="text-align: center;"><?php echo lang("consumptions"); ?></th>
        </tr>
        
        <tr>
			<th style="text-align: center;"><?php echo lang("categories"); ?></th>
			<th style="text-align: center;"><?php echo lang("Reported_in_period"); ?></th>
			<th style="text-align: center;"><?php echo lang("accumulated"); ?></th>
		</tr>
        
        <?php foreach ($tabla_consumo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>
        	<?php 
				$arreglo_valores_acumulados = $tabla_consumo_volumen_acumulados[$id_categoria];
				$row_alias = $this->Categories_alias_model->get_one_where(array(
					'id_categoria' => $id_categoria, 'id_cliente' => $info_cliente->id, 'deleted' => 0
				));				
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array(
						'id' => $id_categoria, 'deleted' => 0
					));
					$nombre_categoria = $row_categoria->nombre;
				}		
			?>
            <tr>
				<td><?php echo $nombre_categoria . ' (' . $unidad_volumen . ')'; ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $info_proyecto->id); ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $info_proyecto->id); ?></td>
			</tr>
        <?php } ?>
        
        <?php foreach ($tabla_consumo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
			<?php 
				$arreglo_valores_acumulados = $tabla_consumo_masa_acumulados[$id_categoria];
				$row_alias = $this->Categories_alias_model->get_one_where(array(
					'id_categoria' => $id_categoria, 'id_cliente' => $info_cliente->id, 'deleted' => 0
				));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array(
						'id' => $id_categoria, 'deleted' => 0
					));
					$nombre_categoria = $row_categoria->nombre;
				}
			?>
            <tr>
				<td><?php echo $nombre_categoria . ' (' . $unidad_masa . ')'; ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $info_proyecto->id); ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $info_proyecto->id); ?></td>
			</tr>
		<?php } ?>
        
        <?php foreach ($tabla_consumo_energia_reportados as $id_categoria => $arreglo_valores){ ?>
			<?php
                $arreglo_valores_acumulados = $tabla_consumo_energia_acumulados[$id_categoria];  
                $row_alias = $this->Categories_alias_model->get_one_where(array(
					'id_categoria' => $id_categoria, 'id_cliente' => $info_cliente->id, 'deleted' => 0
				));
                if($row_alias->alias){
                    $nombre_categoria = $row_alias->alias;
                }else{
                    $row_categoria = $this->Categories_model->get_one_where(array(
						'id' => $id_categoria, 'deleted' => 0
					));
                    $nombre_categoria = $row_categoria->nombre;
                }
            ?>
            <tr>
				<td><?php echo $nombre_categoria . ' (' . $unidad_energia . ')'; ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $info_proyecto->id); ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $info_proyecto->id); ?></td>
			</tr>
    	<?php } ?>
        
    </table>

	<br pagebreak="true">

	<!-- Gráficos Consumos -->
	<?php foreach($graficos_consumo as $grafico){ ?>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico; ?>" style="height:380px; width:570px;" /></td>
            </tr>
        </table>
    <?php } ?>
    
<?php } ?>


<?php if($report_config->waste){ ?>
    
    <br pagebreak="true">
    <!-- Tabla Residuos -->
    <h2><?php echo lang("waste"); ?></h2>
    
    <table border="1" cellpadding="4">
       
        <tr style="background-color:<?php echo $info_cliente->color_sitio; ?>;">
        	<th colspan="3" style="text-align: center;"><?php echo lang("waste"); ?></th>
        </tr>
        
        <tr>
			<th style="text-align: center;"><?php echo lang("categories"); ?></th>
			<th style="text-align: center;"><?php echo lang("Reported_in_period"); ?></th>
			<th style="text-align: center;"><?php echo lang("accumulated"); ?></th>
		</tr>
        
        <?php foreach ($tabla_residuo_volumen_reportados as $id_categoria => $arreglo_valores){ ?>  
        	<?php
				$arreglo_valores_acumulados = $tabla_residuo_volumen_acumulados[$id_categoria];
				$row_alias = $this->Categories_alias_model->get_one_where(array(
					'id_categoria' => $id_categoria, 'id_cliente' => $info_cliente->id, 'deleted' => 0
				));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array(
						'id' => $id_categoria, 'deleted' => 0
					));
					$nombre_categoria = $row_categoria->nombre;
				}
            ?>
            <tr>
				<td><?php echo $nombre_categoria . ' (' . $unidad_volumen . ')'; ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $info_proyecto->id); ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $info_proyecto->id); ?></td>
			</tr>
		<?php } ?>
		
        <?php foreach ($tabla_residuo_masa_reportados as $id_categoria => $arreglo_valores){ ?>
            <?php
            	$arreglo_valores_acumulados = $tabla_residuo_masa_acumulados[$id_categoria];
				$row_alias = $this->Categories_alias_model->get_one_where(array(
					'id_categoria' => $id_categoria, 'id_cliente' => $info_cliente->id, 'deleted' => 0
				));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array(
						'id' => $id_categoria, 'deleted' => 0
					));
					$nombre_categoria = $row_categoria->nombre;
				}
			?>
            <tr>
				<td><?php echo $nombre_categoria . ' (' . $unidad_masa . ')'; ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores), $info_proyecto->id); ?></td>
				<td style="text-align: right;"><?php echo to_number_project_format(array_sum($arreglo_valores_acumulados), $info_proyecto->id); ?></td>
			</tr>
        <?php } ?>
        
    </table>
    
    <br pagebreak="true">
    
    <!-- Gráficos Residuos-->
	<?php foreach($graficos_residuo as $grafico){ ?>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico; ?>" style="height:380px; width:570px;" /></td>
            </tr>
        </table>
    <?php } ?>

<?php } ?>


<?php if($report_config->compromises){ ?>
	<?php if($disponibilidad_modulo_compromisos == 1){ ?>
    	<?php if($perfil_puede_ver_compromisos == 1){ ?>
    		
            <br pagebreak="true">
			<h2><?php echo lang("compromises") . " - " . lang('summary_by_evaluated'); ?></h2>
            
            <!-- Gráficos Compromisos -->
            <table cellspacing="0" cellpadding="4" border="0">
                <?php $loop = 1; ?>
                <?php foreach($graficos_resumen_evaluado_compromisos as $grafico) { ?>
                    
                    <?php if($loop % 2 == 1){ ?>
                        <tr>
                    <?php } ?>
                    
                    <?php if(strpos($grafico, 'grafico_resumen_evaluado_compromisos_') !== false){ ?>
                    
                        <?php 
                            $grafico_evaluado_compromiso = str_split($grafico, 37);		
                            $id_evaluado = $grafico_evaluado_compromiso[1];
                            $evaluado = $this->Evaluated_compromises_model->get_one($id_evaluado);
                        ?>
                        <td align="center">
                            <div style="font-size:20px">&nbsp;</div>
                            <?php echo $evaluado->nombre_evaluado . " - " . lang("no_information_available"); ?>
                        </td>			
                        
                    <?php } else { ?>
                    
                    	<?php if (strpos($grafico, 'null.png') !== false) { ?>
                        	 <td align="center"></td>
                    	<?php } else { ?>                         	 
                             <td align="center"><img src="<?php echo $grafico; ?>"/></td>
                        <?php } ?> 

                    <?php } ?>    
                
                    <?php if($loop % 2 == 0 || $loop == count($graficos_resumen_evaluado_compromisos)) {?>
                        </tr>
                    <?php } ?>
                    
                    <?php $loop++; ?>
            
                <? } ?>
                
            </table>
            
            <br pagebreak="true">
            
            <!-- Tabla Compromisos -->
            <table cellspacing="0" cellpadding="4" border="1">
            	<thead>
                	<tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
						<th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("compliance_status"); ?></th>
                        <?php foreach($contenido_tabla_compromisos["evaluados_matriz_compromiso"] as $evaluado) { ?>
                        	<th colspan="2" style="text-align: center;"><?php echo $evaluado["nombre_evaluado"]; ?></th>
                        <?php } ?>
                    </tr>
                    <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    	<?php foreach($contenido_tabla_compromisos["evaluados_matriz_compromiso"] as $evaluado) { ?>
                        	<th style="text-align: center;">N°</th>
                            <th style="text-align: center;">%</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
					<tr>
						<th style="text-align: left;"><?php echo lang("total_applicable_compromises"); ?></th>
                        <?php foreach($contenido_tabla_compromisos["array_total_compromisos_aplicables_por_evaluado"] as $total) { ?>
                        	<td style="text-align: right;"><?php echo to_number_project_format($total, $info_proyecto->id); ?></td>
							<td style="text-align: right;"><?php echo to_number_project_format(100, $info_proyecto->id).' %'; ?></td>
                        <?php } ?>
                    </tr>
                    <?php foreach($contenido_tabla_compromisos["result_estados"] as $estado){ ?>
                    	<tr>
                        	<td style="text-align: left;"><?php echo $estado["nombre_estado"]; ?></td>
                            <?php foreach($contenido_tabla_compromisos["evaluados_matriz_compromiso"] as $evaluado) { ?>
                            	<?php
                            		$cantidad = $Reports_controller->get_quantity_of_status_evaluated_for_compromises($estado["id_estado"], $evaluado["id"], $fecha_desde, $fecha_hasta); 
									$porcentaje = $Reports_controller->get_percentage_of_status_evaluated_for_compromises($cantidad, $estado["id_estado"], $evaluado["id"], $fecha_desde, $fecha_hasta);
								?>
                                <td style="text-align: right;"><?php echo to_number_project_format($cantidad, $info_proyecto->id); ?></td> 
								<td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $info_proyecto->id).' %'; ?></td>
                            <?php } ?>
                    	</tr>
                    <?php } ?>
            	</tbody>
            </table>
            
		<?php } ?>
	<?php } ?>
<?php } ?>


<?php if($report_config->permittings){ ?>
	<?php if($disponibilidad_modulo_permisos == 1){ ?>
    	<?php if($perfil_puede_ver_permisos == 1){ ?>
        	
            <br pagebreak="true">
			<h2><?php echo lang("permittings") . " - " . lang('summary_by_evaluated'); ?></h2>
            
            <!-- Gráficos Permisos -->
            <table cellspacing="0" cellpadding="4" border="0">
                <?php $loop = 1; ?>
                <?php foreach($graficos_resumen_evaluado_permisos as $grafico) { ?>
                    
                    <?php if($loop % 2 == 1){ ?>
                        <tr>
                    <?php } ?>
                    
                    <?php if(strpos($grafico, 'grafico_resumen_evaluado_permisos_') !== false){ ?>
                    
                        <?php 
                            $grafico_evaluado_permiso = str_split($grafico, 34);		
                            $id_evaluado = $grafico_evaluado_permiso[1];
                            $evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado);
                        ?>
                        <td align="center">
                            <div style="font-size:20px">&nbsp;</div>
                            <?php 
								echo $evaluado->nombre_evaluado . " - " . lang("no_information_available");
							?>
                        </td>			
                        
                    <?php } else { ?>
                    
                    	<?php if (strpos($grafico, 'null.png') !== false) { ?>
                        	 <td align="center"></td>
                    	<?php } else { ?>                         	 
                             <td align="center"><img src="<?php echo $grafico; ?>"/></td>
                        <?php } ?> 
                       
                    <?php } ?>    
                
                    <?php if($loop % 2 == 0 || $loop == count($graficos_resumen_evaluado_permisos)) {?>
                        </tr>
                    <?php } ?>
                    
                    <?php $loop++; ?>
            
                <? } ?>
                
            </table>
            
            <br pagebreak="true">
            
            <!-- Tabla Permisos -->
            <table cellspacing="0" cellpadding="4" border="1">
            	<thead>
                	<tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
						<th rowspan="2" style="vertical-align:middle; text-align: center;"><?php echo lang("compliance_status"); ?></th>
                        <?php foreach($contenido_tabla_permisos["evaluados_matriz_permisos"] as $evaluado) { ?>
                        	<th colspan="2" style="text-align: center;"><?php echo $evaluado["nombre_evaluado"]; ?></th>
                        <?php } ?>
                    </tr>
                    <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    	<?php foreach($contenido_tabla_permisos["evaluados_matriz_permisos"] as $evaluado) { ?>
                        	<th style="text-align: center;">N°</th>
                            <th style="text-align: center;">%</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
					<tr>
						<th style="text-align: left;"><?php echo lang("total_applicable_permittings"); ?></th>
                        <?php foreach($contenido_tabla_permisos["array_total_permisos_aplicables_por_evaluado"] as $total) { ?>
                        	<td style="text-align: right;"><?php echo to_number_project_format($total, $info_proyecto->id); ?></td>
							<td style="text-align: right;"><?php echo to_number_project_format(100, $info_proyecto->id).' %'; ?></td>
                        <?php } ?>
                    </tr>
                    <?php foreach($contenido_tabla_permisos["result_estados"] as $estado){ ?>
                    	<tr>
                        	<td style="text-align: left;"><?php echo $estado["nombre_estado"]; ?></td>
                            <?php foreach($contenido_tabla_permisos["evaluados_matriz_permisos"] as $evaluado) { ?>
                            	<?php
                            		$cantidad = $Reports_controller->get_quantity_of_status_evaluated_for_permitting($estado["id_estado"], $evaluado["id"], $fecha_desde, $fecha_hasta); 
									$porcentaje = $Reports_controller->get_percentage_of_status_evaluated_for_permitting($cantidad, $estado["id_estado"], $evaluado["id"], $fecha_desde, $fecha_hasta);
								?>
                                <td style="text-align: right;"><?php echo to_number_project_format($cantidad, $info_proyecto->id); ?></td> 
								<td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $info_proyecto->id).' %'; ?></td>
                            <?php } ?>
                    	</tr>
                    <?php } ?>
            	</tbody>
            </table>
            
            
		<?php } ?>
	<?php } ?>
<?php } ?>
</body>