<?php echo form_open("", array("id" => "view_element_details-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <?php if(!$deleted_element){ ?>

    <?php } else { ?>
    	
        <div class="panel panel-default">
            <div class="">              
                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                    <?php echo lang('deleted_element_msj'); ?>
                </div>
            </div>	  
        </div>
        
     <?php } ?>
	
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 