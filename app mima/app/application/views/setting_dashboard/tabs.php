<ul class="nav nav-tabs vertical" role="tablist">
	<h4><i class="fa fa-wrench"></i> <?php echo lang('setting_dashboard'); ?></h4>
    <li role="presentation" class="<?php echo ($active_tab == 'environmental_footprints') ? 'active' : ''; ?>"><a data-toggle="tab" href="#environmental_footprints"><?php echo lang("environmental_footprints"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'consumptions') ? 'active' : ''; ?>"><a data-toggle="tab" href="#consumptions"><?php echo lang("consumptions"); ?></a></li>
    <li role="presentation" class="<?php echo ($active_tab == 'waste') ? 'active' : ''; ?>"><a data-toggle="tab" href="#waste"><?php echo lang("waste"); ?></a></li>
    <?php if($puede_ver_compromisos != 3 && $disponibilidad_modulo_compromisos == 1){ ?>	
    	<li role="presentation" class="<?php echo ($active_tab == 'compromises') ? 'active' : ''; ?>"><a data-toggle="tab" href="#compromises"><?php echo lang("compromises"); ?></a></li>
	<?php } ?>
    <?php if($puede_ver_permisos != 3 && $disponibilidad_modulo_permisos == 1){ ?>
    	<li role="presentation" class="<?php echo ($active_tab == 'permittings') ? 'active' : ''; ?>"><a data-toggle="tab" href="#permittings"><?php echo lang("permittings"); ?></a></li>
	<?php } ?>
</ul>