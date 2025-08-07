<div id="page-content" class="p20 clearfix">
<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("project_info"); ?>"><?php echo lang("project_info"); ?></a>
</nav>

	<div class="row">
    	<div class="col-md-12">
        
        	<div class="page-title clearfix">
            	<h1><i class="fa fa-th-large" title="Abierto"></i> <?php echo $project_info->title; ?></h1>
            </div>
            
            <?php
            	$icono = $project_info->icono?get_file_uri("assets/images/icons/".$project_info->icono):get_file_uri("assets/images/icons/empty.png");
			?>
            
            <div class="row" style="background-color:#E5E9EC;">                
                <div class="col-md-4">
                
                	<div class="row">
                    	<div class="col-md-12 col-sm-12">
                        <div class="panel">
                        <div class="panel-heading bg-info p30"></div>
                        <div class="clearfix text-center">
                        <span class="mt-50 avatar avatar-md chart-circle">
                        <img src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded shadow-2">
                        </span>
                        </div>

                            <?php if(false){ // OCULTAR FECHA DE INICIO Y TÉRMINO DEL CONTRATO ?>
                                <div class="p10 b-t b-b">
                                <?php echo lang("start_date") . ': ' . get_date_format($project_info->start_date, $project_info->id); ?>
                                </div>
                                <div class="p10 b-b">
                                <?php echo lang("deadline") . ': ' . get_date_format($project_info->deadline, $project_info->id); ?>
                                </div>
                            <?php } ?>

                            <div class="p10 b-b">
                            <?php echo lang("industry") . ': ' . $rubro; ?>
                            </div>
                            <div class="p10 b-b">
                            <?php echo lang("subindustry") . ': ' . $subrubro; ?>
                            </div>
                        </div>
                        </div>
                    </div>
                    
                    <?php if(false){ // OCULTAR FECHA DE INICIO Y TÉRMINO DEL CONTRATO ?>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel no-border">
                                    <div class="tab-title clearfix">
                                        <h4><?php echo lang("project_members") ?></h4>
                                        <div class="title-button-group">
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div id="project-member-table_wrapper" class="dataTables_wrapper no-footer"></div>
                                        <table id="project-member-table" class="b-b-only no-thead dataTable no-footer" width="100%">
                                            <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="project-member-table" rowspan="1" colspan="1" aria-label=": activate to sort column descending" aria-sort="ascending"></th>
                                                <th class="text-center option w100 sorting" tabindex="0" aria-controls="project-member-table" rowspan="1" colspan="1" aria-label=": activate to sort column ascending"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($miembros_de_proyecto) { ?>                                      
                                                    <?php foreach($miembros_de_proyecto as $miembro){?>
                                                        <tr role="row" class="even">
                                                            <td>
                                                                <div class="pull-left">
                                                                    <a href="<?php echo get_uri("project_info/view_user_profile/".$miembro["id"]); ?>">
                                                                        <?php
                                                                            $logo = $miembro["image"] ? get_file_uri("files/profile_images/".$miembro["image"]): get_file_uri("assets/images/avatar.jpg");
                                                                        ?>
                                                                        <span class="avatar avatar-xs mr10"><img src="<?php echo $logo; ?>" alt="..." class="mCS_img_loaded"></span> 
                                                                        <?php echo $miembro["first_name"] . ' ' . $miembro["last_name"] ?> 
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                
                            </div>                        
                        </div>
                    <?php } ?>

                </div>
                <div class="col-md-8">
                
                	<div class="row">
                    
                        <div class="col-md-12 col-sm-12">
                        	<div class="panel">
                        		<div class="p15" align="justify">
                        			<?php echo $project_info->description; ?>
                       			</div>
                        	</div>
                        </div>
                        
                        <div class="col-md-12 col-sm-12">
                        	<div class="panel">
                                <div class="tab-title clearfix">
                                    <h4><?php echo lang("content");?></h4>
                                </div>
                        		<div class="p15" align="justify">
                        			<?php echo $project_info->contenido; ?>
                       			</div>
                        	</div>
                        </div>

                    </div>
                </div>
            </div>

        </div>       
    </div>

</div>