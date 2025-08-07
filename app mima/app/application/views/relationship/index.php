<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang('relationship'); ?></a> 
</nav>

    <div class="row">
        <div class="col-md-12">
            <div class="page-title clearfix">
                <h1>
                    <?php echo lang("relationship"); ?>   
                </h1>
            </div>
            <ul id="project-tabs" data-toggle="ajax-tab" class="nav nav-tabs classic" role="tablist">
            
                <li><a role="presentation" href="<?php echo_uri("relationship/criterio/"); ?>" data-target="#rule"><?php echo lang('rule'); ?></a></li>
                <li><a role="presentation" href="<?php echo_uri("relationship/asignacion/"); ?>" data-target="#assignment"><?php echo lang('assignment'); ?></a></li>
                <li><a role="presentation" href="<?php echo_uri("relationship/calculo/"); ?>" data-target="#calculation"><?php echo lang('calculation'); ?></a></li>
                
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="rule" style="min-height: 200px;"></div>
                <div role="tabpanel" class="tab-pane fade" id="assignment"></div>
                <div role="tabpanel" class="tab-pane fade" id="calculation"></div>
            </div>
        </div>
    </div>
</div>

<?php /*?><script type="text/javascript">

    $(document).ready(function () {
		

    });
</script><?php */?>