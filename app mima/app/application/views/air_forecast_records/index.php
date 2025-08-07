<div id="page-content" class="p20 clearfix">

<nav class="breadcrumb">
  <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("air_forecast_records"); ?>"><?php echo lang("forecast_records"); ?></a>
</nav>

<?php if($puede_ver != 3) { ?>

    <div class="panel" style="background-color:#f1f1f1;">

       <div class="page-title panel-sky clearfix mb15">
          <h1><?php echo lang("forecast_records"); ?></h1>
       </div>
       
		<?php
			$html = '';
			foreach($air_forecast_records as $key => $forecast_record){
				
				$flujo = ($forecast_record->id_air_station) ? ' <label class="label label-success large">'.$forecast_record->name_station.'</label>' : "";
				
				if(($key+1)%2 == 1){
					$html .= '<div class="row">';
				}
				
				$icono = $forecast_record->icon ? get_file_uri("assets/images/icons/".$forecast_record->icon):get_file_uri("assets/images/icons/empty.png");
				
				$html .= '<div class="col-md-6 col-sm-6 widget-container">';
				$html .= 	'<a href="'.get_uri("air_forecast_records/view/".$forecast_record->id).'" class="white-link">';
				$html .= 		'<div class="panel panel-list shadow-2">';
				$html .= 			'<div class="panel-body">';
				$html .= 				'<div class="col-md-2">';
				$html .= 					'<div class="media-left">';
				$html .= 						'<span class="avatar avatar-sm border-circle">';
				$html .= 							'<img src="'.$icono.'" alt="..." class="mCS_img_loaded shadow-2">';
				$html .= 						'</span>';
				$html .= 					'</div>';
				$html .= 				'</div>';
				$html .= 				'<div class="col-md-10 col-sm-10">';
				$html .= 					'<h4>'.$forecast_record->name.'</h4>';
				$html .= 					'<h5 style="font-weight: normal;">'.$forecast_record->name_sector.'</h5>';
				$html .= 					'<p class="m0">';
				$html .= 						'<label class="label label-default large">'.$forecast_record->code.'</label>';
				$html .= 						' <label class="label label-info large">'.lang($forecast_record->name_model).'</label>'.$flujo;
				$html .= 					'</p>';
				$html .= 				'</div>';
				$html .= 			'</div>';
				$html .= 		'</div>';
				$html .= 	'</a>';
				$html .= '</div>';			
				
				if(($key+1)%2 == 0){
					$html .= '</div>';
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