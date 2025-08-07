<div id="page-content" class="p20 clearfix">
<!--Breadcrumb section-->
<?php if($this->session->project_context){ ?>
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("help_and_support"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("what_is_mimasoft"); ?></a>
    </nav>
<?php } else { ?>
	<nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("help_and_support"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("what_is_mimasoft"); ?></a>
    </nav>
<?php } ?>

  <div class="row">
    <?php //$this->load->view("clients/info_widgets"); ?>
  </div>
  
  <?php if($puede_ver == 1) { ?>
  
  <div class="">
    <?php if (isset($page_type) && $page_type === "full") { ?>
    <div id="page-content" class="m20 clearfix">
      <?php } ?>
      <?php
      
		$html.= '<div class="row">
              		<div class="col-md-12">
                		<div class="panel-group" id="accordion">';
               	 
							foreach($mimasoft as $key){
								$codigo = $key->codigo ? $key->codigo : "-";
								if($codigo == "ACV"){
									$html .= '<div class="panel panel-default">';
									$html .= '<div class="panel-heading">';
									$html .= '<h4 class="panel-title"> <a data-toggle="collapse" href="#collapse'.$key->id.'" data-parent="#accordion" class="accordion-toggle">';
									$html .= '<h4 style="font-size:16px">'.$key->titulo.'</h4>';
									$html .= '</a>';
									$html .= '</h4>';
									$html .= '</div>';
									$html .= '<div id="collapse'.$key->id.'" class="panel-collapse collapse">';
									$html .= '<div class="panel-body">';
									$html .= '<div class="col-md-12" style="text-align: justify;">';
									$html .= ''.$key->contenido.'';
									$html .= '</div>';
									$html .= '</div>';
									$html .= '</div>';
									$html .= '</div>';
								}
							}

		$html.= 		'</div>';
		$html.= 	'</div>';
		$html.= '</div>';
		
		//ACV Paso a Paso
		$html.= '<div class="row">
				 	<div class="col-md-12">
						<div class="page-title clearfix" style="background-color:#FFF;">
                			<h1 style="font-size:20px">'.lang("acv_step_by_step").'</h1>
						</div>
						<div class="panel panel-default">
							<div class="panel-group" id="accordion2">';
						
								foreach($mimasoft as $key){
									$codigo = $key->codigo ? $key->codigo : "-";
									if($codigo == "ACVPP"){
										$html .= '<div class="panel panel-default">';
										$html .= '<div class="panel-heading">';
										$html .= '<h4 class="panel-title">';
										$html .= '<a data-toggle="collapse" href="#collapse'.$key->id.'" data-parent="#accordion2" class="accordion-toggle">';
										$html .= '<h4 style="font-size:16px">'.$key->titulo.'</h4>';
										$html .= '</a>';
										$html .= '</h4>';
										$html .= '</div>';
										$html .= '<div id="collapse'.$key->id.'" class="panel-collapse collapse">';
										$html .= '<div class="panel-body">';
										$html .= '<div class="col-md-12" style="text-align: justify;">';
										$html .= '<p>'.$key->contenido.'</p>';
										$html .= '</div>';
										$html .= '</div>';
										$html .= '</div>';
										$html .= '</div>';
									}
								}
								
					$html.='</div>';
				$html.='</div>';
			$html.='</div>';
		$html.='</div>';
		
		
		//Ventajas del ACV
		$html.= '<div class="row">
				 	<div class="col-md-12">
						<div class="page-title clearfix" style="background-color:#FFF;">
                			<h1 style="font-size:20px">'.lang("acv_adventages").'</h1>
						</div>
						<div class="panel panel-default">
							<div class="panel-group" id="accordion3">';
						
								foreach($mimasoft as $key){
									$codigo = $key->codigo ? $key->codigo : "-";
									if($codigo == "ACVV"){
										$html .= '<div class="panel panel-default">';
										$html .= '<div class="panel-heading">';
										$html .= '<h4 class="panel-title">';
										$html .= '<a data-toggle="collapse" href="#collapse'.$key->id.'" data-parent="#accordion3" class="accordion-toggle">';
										$html .= '<h4 style="font-size:16px">'.$key->titulo.'</h4>';
										$html .= '</a>';
										$html .= '</h4>';
										$html .= '</div>';
										$html .= '<div id="collapse'.$key->id.'" class="panel-collapse collapse">';
										$html .= '<div class="panel-body">';
										$html .= '<div class="col-md-12" style="text-align: justify;">';
										$html .= '<p>'.$key->contenido.'</p>';
										$html .= '</div>';
										$html .= '</div>';
										$html .= '</div>';
										$html .= '</div>';
									}
								}
								
					$html.='</div>';
				$html.='</div>';
			$html.='</div>';
		$html.='</div>';

		echo $html;
        
     ?>
                             
    <?php if (isset($page_type) && $page_type === "full") { ?>
    </div>
    <?php } ?>
    <?php
        if (!isset($project_labels_dropdown)) {
            $project_labels_dropdown = "0";
        }
        ?>
    <script type="text/javascript">
            $(document).ready(function () {
                var hideTools = "<?php
        if (isset($page_type) && $page_type === 'dashboard') {
            echo 1;
        }
        ?>" || 0;
        
        
                var filters = [{name: "project_label", class: "w200", options: <?php echo $project_labels_dropdown; ?>}];
        
                //don't show filters if hideTools is true or $project_labels_dropdown is empty
                if (hideTools || !<?php echo $project_labels_dropdown; ?>) {
                    filters = false;
                }
        
        
               
            });
        </script> 
  </div>
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