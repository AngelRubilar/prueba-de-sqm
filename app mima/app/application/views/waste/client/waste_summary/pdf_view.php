<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("waste") . " - " . lang("summary"); ?>
</h2>
<div align="center">
    <?php $hora = convert_to_general_settings_time_format($info_proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $info_proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $info_proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

<br>
<?php if($puede_ver == 1) { ?>
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_masa; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    <br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_volumen ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_almacenados_masa; ?>" style="height:380px; width:570px;" /></td>
        </tr>
    </table>
    <br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center"><img src="<?php echo $grafico_residuos_almacenados_volumen ?>" style="height:380px; width:570px;" /></td>
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
</body>