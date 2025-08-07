<div id="page-content" class="p20 clearfix">

	<?php if ($puede_ver != 3) { ?>

		<nav class="breadcrumb">
			<a class="breadcrumb-item" href="<?php echo get_uri("camaras"); ?>"><?php echo lang("report_sqm_pm10"); ?> /</a>

		</nav>
		<div id="alert_camara" class="alert alert-info  alert-dismissible fade in" role="alert">
			<strong>Por favor, sea paciente.</strong> Estamos conectando con un sistema externo y esto puede tomar unos momentos.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php } ?>

	<div id="models_group" class="full_width_height">

		<?php if ($puede_ver != 3) { ?>

			<iframe src="<?php echo $iframe_src; ?>" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
		<?php } else { ?>
			<!-- 
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
<style>
	.full_width_height {
		width: 88vw;
		height: 80vh;
		display: flex;
		justify-content: center;
		align-items: center;
		box-sizing: border-box;
	}

	.alert_camara {
		position: relative;
		opacity: 1;
		transition: opacity 1s linear;
	}

	.alert.fade-out {
		opacity: 0;
	}
</style>
<script>
	$(document).ready(function() {
		setTimeout(function() {
			$('#alert_camara').fadeTo(1500, 0, function() {
				$(this).remove();
			});
		}, 40000);
	});
</script>