<div id="page-content" class="p20 clearfix">
	<!--Breadcrumb section-->
	<nav class="breadcrumb">
		<a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?>
			/</a>
		<a class="breadcrumb-item"
			href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
		<a class="breadcrumb-item" href="<?php echo get_uri("tutorials"); ?>"><?php echo lang("tutorials"); ?></a>
	</nav>

	<div class="">
		<?php if (isset($page_type) && $page_type === "full") { ?>
		<div id="page-content" class="m20 clearfix">
			<?php } ?>

			<div class="row">
				<div class="col-md-12">
					<div class="page-title clearfix" style="background-color:#FFF;">
						<h1 style="font-size:20px"><?php echo lang("accesses"); ?></h1>
					</div>
					<div class="panel panel-default">

						<div class="panel-group" id="accordion1">

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse1" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("login_and_password_update"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse1" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/1.MLP_inicio+sesión_out.mp4" type="video/mp4">   
											</video>
										</div>

										<?php if(false){ ?>
											<div class="col-md-6" style="text-align: justify;">
												<ul>
													<li>Ingresar a <a href="https://aire.mimasoft.cl/">https://aire.mimasoft.cl/</a></li>
													<li>En el formulario de ingreso, escribir el correo coorporativo (@angloamerican.com)</li>
													<li>Si es primera vez que se hace uso de la platforma, la contraseña corresponden a:
														<ul>
														<li>La primera letra de tu nombre en Mayúscula</li>
														<li>Seguido de la primera letra de tu apellido en Mayúscula</li>
														<li>Terminando con los numeros 1234</li>
														</ul>
													</li>
													<li>Recomendamos cambiar tu contraseña para tu comodidad y control de esta, para esto debes:
													<ul>
														<li>La primera letra de tu nombre en Mayúscula</li>
														<li>Seguido de la primera letra de tu apellido en Mayúscula</li>
														<li>Terminando con los numeros 1234</li>
														</ul>
													</li>
												</ul>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse2" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("password_recovery"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse2" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/2.MLP_recuperar+contraseña_out.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse3" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("access_to_projects"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse3" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/3.MLP_acceso+proyecto_out.mp4" type="video/mp4">   
											</video>
										</div>

									</div>
								</div>
							</div>

						</div>

					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="page-title clearfix" style="background-color:#FFF;">
						<h1 style="font-size:20px"><?php echo lang("project"); ?></h1>
					</div>
					<div class="panel panel-default">

						<div class="panel-group" id="accordion1">
							
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse4" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("dashboard"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse4" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/4.MLP_panel+principal_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>

							<?php if(false) { ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" href="#collapse5" data-parent="#accordion1" class="accordion-toggle">
												<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("project_info"); ?></h4>
											</a>
										</h4>
									</div>
									<div id="collapse5" class="panel-collapse collapse">
										<div class="panel-body">
											<div class="col-md-12" style="text-align: center;">
												<video width="100%" controls="" poster="" controlsList="nodownload">
													<source src="/files/system/tutorials/05 - Información del proyecto.mp4" type="video/mp4">   
												</video>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse6" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("forecasts_part_1"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse6" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/5.aMLP_pronóstico+parte1_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>


							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse7" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("forecasts_part_2"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse7" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/5.bMLP_pronóstico+parte2_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse8" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("air_forecast_performance"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse8" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/6.MLP_desempeño+pronóstico_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse9" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("air_forecast_comparison"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse9" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/7.MLP_comparacion+pronóstico_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse10" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("monitoring")." - ".lang("graphs"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse10" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/8.aMLP_monitoreo+gráficos_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse11" data-parent="#accordion1" class="accordion-toggle">
											<h4 style="font-size:16px"><i class="fa fa-plus-circle font-16"></i> <?php echo lang("monitoring")." - ".lang("records"); ?></h4>
										</a>
									</h4>
								</div>
								<div id="collapse11" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="col-md-12" style="text-align: center;">
											<video width="100%" controls="" poster="" controlsList="nodownload">
												<source src="/files/system/tutorials/8.bMLP_monitoreo+tablas_out.mp4" type="video/mp4">   
											</video>
										</div>
									</div>
								</div>
							</div>


						</div>
						
						
					</div>
				</div>
			</div>

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
	</div>
</div>