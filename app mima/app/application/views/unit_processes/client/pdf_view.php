<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $project_info->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo ucwords(lang("environmental_footprints"))." - ".lang("unit_processes"); ?>
</h2>
<div align="center">
	<?php $hora = convert_to_general_settings_time_format($project_info->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $project_info->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $project_info->id).' '.lang("at").' '.$hora; ?>
</div>
<br>
<?php $loop_unidades_funcionales = 1; ?>

<?php foreach($unidades_funcionales as $key => $unidad_funcional){ ?>
	
    <!-- Gráficos -->
    
    <table cellspacing="0" cellpadding="0" border="0">
    	
        <tr>
        	<th colspan="2">
            	<h2><?php echo lang("environmental_impacts_by") . ' ' . $unidad_funcional->unidad. ' ' . lang("of") . ' ' . $unidad_funcional->nombre; ?></h2>
            	<br>
            </th>
        </tr>
         
    	<?php $loop_huellas = 1; ?>
		
		<?php foreach($huellas as $huella){ ?>
            
            <?php if($loop_huellas % 2 == 1){ ?>
                <tr>
            <?php } ?>
            
            <td align="center">
            	<img src="<?php echo $imagenes_graficos[$unidad_funcional->id][$huella->id]; ?>" style="height: 200px; width: 300px;"/>
            </td>
            
            <?php if($loop_huellas % 2 == 0 || $loop_huellas == count($huellas)) {?>
                </tr>
            <?php } ?>
            
            <?php $loop_huellas++; ?>
            
        <?php } ?>
        
	</table>
    
    <br pagebreak="true">
    
    <table cellpadding="4" cellspacing="0" border="1">
    	<thead>
        	<tr style="background-color: <?php echo $client_info->color_sitio; ?>">
            <th><?php echo lang("unit_process"); ?></th>
        	<?php foreach($columnas as $columna) { ?>
        		<th><?php echo $columna; ?></th>
            <?php } ?>
            </tr>
        </thead>
        <tbody>
        	<?php foreach($array_unidades_funcionales as $index => $valores) { ?>
				<?php if($index == $unidad_funcional->id) { ?>
					<?php foreach($valores as $valor) { ?>
                        <tr>
                            <?php foreach($valor as $val) {?>
                            <td style="text-align: right; padding:0px;"><?php echo $val; ?></td>
                            <?php } ?>
                        </tr>
                        <?php if(!empty($valor["categorias"])) { ?>
                        	
                            <tr>
                            	<td colspan="<?php echo count($columnas) + 1; ?>"><?php echo lang("categories"); ?></td>
                            </tr>
                            
                        	<?php foreach($valor["categorias"] as $indice => $categoria) { ?>
                                <tr>
                                	<td><?php echo $indice; ?></td>
                                	<?php foreach($categoria as $valor) {?>
										<td style="text-align: right;"><?php echo $valor; ?></td>
                                    <?php } ?>
                                </tr>
                        	 <?php } ?>
   
                         <?php } ?>
                         
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
	
    <?php $loop_unidades_funcionales++; ?>
    <?php if ($loop_unidades_funcionales == count($unidades_funcionales)) { ?>
    	<br pagebreak="true">
	<?php } ?>
<?php } ?>

</body>