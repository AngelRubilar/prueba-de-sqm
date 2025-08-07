<ul class="nav nav-tabs classic" id="tabs" role="tablist" style="margin-bottom:0px;">
	<?php foreach($active_tabs as $key => $tab){ ?>
		<li id="<?php echo $key ?>" data-id="<?php echo $key ?>" data-name="<?php echo $tab ?>" ><a data-toggle="tab" href="#"><?php echo $tab ?></a></li>
	<?php }?>
</ul>