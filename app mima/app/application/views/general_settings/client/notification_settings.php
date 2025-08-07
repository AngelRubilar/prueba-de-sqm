<div class="col-sm-9 col-lg-10">
    <?php echo form_open(get_uri("#"), array("id" => "client_module_availability-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4><?php echo lang("notification_settings"); ?></h4>
        </div>
        <div class="panel-body">
			
            <div class="row">
				<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-group" id="accordion">

                            <!-- Acordeón Ayuda y Soporte -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse3" data-parent="#accordion" class="accordion-toggle">
                                            <h4 style="font-size:16px; float:unset !important;">
                                                <i class="fa fa-plus-circle font-16"></i> <?php echo lang("help_and_support"); ?>
                                            </h4>
                                        </a>
                                    </h4>
                                </div>
                                
                                <div id="collapse3" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="col-md-12" style="text-align: justify;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo lang("submodule");?></th>
                                                        <th class="option text-center"><?php echo lang("send_email");?></th>
                                                        <th class="text-center"><i class="fa fa-bars"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($array_help_and_support as $index => $help_and_support) { ?>
                                                    <tr>
                                                        <td><?php echo ($help_and_support["submodule"]) ? $help_and_support["submodule"] : ""; ?></td>
                                                        <?php if($help_and_support["id_module"] == "1"){ // Ayuda y Soporte - Contacto ?>
                                                            <td id="send_email-<?php echo $help_and_support["item"]; ?>" class="text-center"><?php echo $events_help_and_support_contact_icons["send_email"]; ?></td>
                                                            <td class="option text-center" id="action-<?php echo $help_and_support["item"]; ?>" ><?php echo $events_help_and_support_contact_btn; ?></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin Acordeón Ayuda y Soporte --> 
                                     
                        </div>
                    </div>
                    
				</div>
            </div>
            
        </div>
        <div class="panel-footer col-xs-12 col-md-12 col-lg-12">
            <!--
            <button type="submit" class="btn btn-primary pull-right"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
        	-->
        </div>
    </div>
    <?php echo form_close(); ?>

</div>
<script type="text/javascript">
    $(document).ready(function () {
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
		
    });
</script>