<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("contingency_tables"); ?></a>
    </nav>

<?php if(/*$puede_ver == 1*/ true) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('contingency_tables'); ?></h1>
        </div>	
    </div>

    <div class="row">
        <div class="col-md-12">
            <iframe height="400" width="100%" frameborder="no" src="<?php echo $shiny_url; ?>"> </iframe>
        </div>
    </div>

    <!-- <div class="panel">
    	<div class="panel-default">
			<div id="resume_table" class="table-responsive" style="max-height:500px;"></div>
        </div>
    </div> -->
    
</div>

<?php } else { ?><!-- Se aplica la configuración de perfil (ver ninguno) -->

    <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                        <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>

<?php } ?><!-- Fin configuración de perfil (ver todos) -->

<script type="text/javascript">
    $(document).ready(function () {

		
    });
</script>
