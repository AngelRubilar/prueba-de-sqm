<div id="page-content" class="p20 clearfix">

    <!--Breadcrumb section-->
    <nav class="breadcrumb">
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("other_records"); ?>"><?php echo lang("other_records"); ?></a>
    </nav>

<?php if($puede_ver != 3) { ?>

    <div class="panel">
    	<?php
       $id_proyecto = $this->session->project_context;
	   $opciones = array("id_proyecto" => $id_proyecto, "id_tipo_formulario" => 3);
	   $formularios = $this->Forms_model->get_forms_of_project($opciones)->result();
	   $formularios_fijos = $this->Forms_model->get_fixed_forms_of_project($opciones)->result();
	   ?>
       <div class="page-title panel-sky clearfix">
          <h1><?php echo lang("other_records");?></h1>
       </div>
       
       <?php
	   
	   $loop = 1;
	   $html = '';
       foreach($formularios as $key => $ra){
		   
		   if($loop % 2 == 1){
			   $html .= '<div class="row">';
		   }
		   
		   $icono = $ra->icono?get_file_uri("assets/images/icons/".$ra->icono):get_file_uri("assets/images/icons/empty.png");
		   
		   $html .= '<div class="col-md-6 col-sm-6 widget-container">';
		   $html .= '<a href="'.get_uri("other_records/view/".$ra->id).'" class="white-link">';
		   $html .= '<div class="panel panel-list">';
		   $html .= '<div class="panel-body">';
		   $html .= '<div class="col-md-2">';
		   $html .= '<div class="media-left">';
		   $html .= '<span class="avatar avatar-sm border-circle">';
		   $html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
		   $html .= '</span>';
		   $html .= '</div>';
		   $html .= '</div>';
		   $html .= '<div class="col-md-10 col-sm-10">';
		   $html .= '<h4>'.$ra->nombre.'</h4>';
		   $html .= '<p class="m0"><label class="label label-info large">'.$ra->codigo.'</label></p>';
		   $html .= '</div>';
		   $html .= '</div>';
		   $html .= '</div>';
		   $html .= '</a>';
		   $html .= '</div>';
                         
		   
		   if($loop % 2 == 0){
			   $html .= '</div>';
		   }
		   $loop++;
	   }
	   
	   // OCULTAR FORMULARIOS FIJOS
	   if(false){
		foreach($formularios_fijos as $key => $ra){
			if($loop % 2 == 1){
				$html .= '<div class="row">';
			}
			
			$icono = $ra->icono?get_file_uri("assets/images/icons/".$ra->icono):get_file_uri("assets/images/icons/empty.png");
			
			$html .= '<div class="col-md-6 col-sm-6 widget-container">';
			$html .= '<a href="'.get_uri("other_records/view/".$ra->id).'" class="white-link">';
			$html .= '<div class="panel panel-list">';
			$html .= '<div class="panel-body">';
			$html .= '<div class="col-md-2">';
			$html .= '<div class="media-left">';
			$html .= '<span class="avatar avatar-sm border-circle">';
			$html .= '<img src="'.$icono.'" alt="..." class="mCS_img_loaded">';
			$html .= '</span>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="col-md-10 col-sm-10">';
			$html .= '<h4>'.$ra->nombre.'</h4>';
			$html .= '<p class="m0"><label class="label label-info large">'.$ra->codigo.'</label></p>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div>';
						  
			
			if($loop % 2 == 0){
				$html .= '</div>';
			}
			  $loop++;
		}
	   }
	   
	   
	   echo $html;
	   
	   ?>
       
                     
    </div>
    
    
<?php } else { ?>

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

<?php } ?>    
    
    
</div>

<script type="text/javascript">
    $(document).ready(function () {

        
    });
</script>