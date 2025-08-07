<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("meteorological_conditions"); ?> /</a>
		<a class="breadcrumb-item" href=""><?php echo lang("display_images"); ?></a>
    </nav>

<?php if($puede_ver == 1) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->


	<div class="panel-group" id="accordion1">

		<?php foreach($array_groups as $group => $group_name){ ?>

			<div class="panel panel-white">

				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#collapse-<?php echo $group; ?>" data-slider_group="<?php echo $group; ?>" data-parent="#" class="accordion-toggle">

							<div class="row">
								<div class="col-md-8">
									<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> 
										<?php echo $group_name; ?>
									</h4>
								</div>
							</div>

						</a>
					</h4>
				</div>
				
				<div id="collapse-<?php echo $group; ?>" class="panel-collapse collapse">
					<div class="panel-body p30 div_slider-<?php echo $group; ?>"></div>
				</div>

			</div>

		<?php } ?>

	</div>
    
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

		$(document).on('click', 'a.accordion-toggle', function () {
            // $('a.accordion-toggle i').removeClass('fa fa-minus-circle font-16');
            // $('a.accordion-toggle i').addClass('fa fa-plus-circle font-16');
            
            var icon = $(this).find('i');
            
            if($(this).hasClass('collapsed')){
                icon.removeClass('fa fa-minus-circle font-16');
                icon.addClass('fa fa-plus-circle font-16');
            } else {
                icon.removeClass('fa fa-plus-circle font-16');
                icon.addClass('fa fa-minus-circle font-16');
            }

			var data_slider_group = $(this).attr("data-slider_group");

			if(data_slider_group){

				$.ajax({
					url:  '<?php echo_uri("air_mc_display_images/get_images_by_group"); ?>',
					type:  'post',
					data: {data_slider_group: data_slider_group},
					//dataType:'json',
					success: function(respuesta){
						$(".div_slider-"+data_slider_group).append(respuesta);	
						$(".slider-"+data_slider_group).slick({
							dots: false,
							infinite: false,
							speed: 300,
							slidesToShow: 2,
							slidesToScroll: 2,
							// adaptiveHeight: true
						});
									
					}
				});

				$(this).removeAttr("data-slider_group");	

			}
			
        });

    });
</script>
