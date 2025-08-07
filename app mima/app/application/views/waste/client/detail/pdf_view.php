<?php if($puede_ver == 1) { ?>

	<!-- <br pagebreak="true"> -->

    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_masa; ?>" style="height:350px; width:525px;" /></td>
        </tr>
    </table>
    <br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_volumen ?>" style="height:350px; width:525px;" /></td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_almacenados_masa; ?>" style="height:350px; width:525px;" /></td>
        </tr>
    </table>
    <br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_almacenados_volumen ?>" style="height:350px; width:525px;" /></td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <h2><?php echo lang("last_withdrawals"); ?></h2>

    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr>
                <th style="background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("material"); ?></th>
                <th style="background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("categorie"); ?></th>
                <th style="background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("quantity"); ?></th>
                <th style="background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("treatment"); ?></th>
                <th style="background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("retirement_date"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($ultimos_retiros as $row){ ?>
            <tr>
                <td><?php echo $row["material"]; ?></td>
                <td><?php echo $row["categoria"]; ?></td>
                <td><?php echo $row["cantidad"]; ?></td>
                <td><?php echo $row["tipo_tratamiento"]; ?></td>
                <td><?php echo $row["fecha_retiro"]; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    
	<?php echo lang('content_disabled'); ?>

<?php } ?>