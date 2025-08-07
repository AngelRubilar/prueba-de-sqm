<div id="page-content" class="p20 clearfix">

	<?php if ($puede_ver != 3) { ?>

		<!-- <nav class="breadcrumb">
			<a class="breadcrumb-item" href="<?php echo get_uri("report_sqm_pm10"); ?>"><?php echo lang("report_sqm_pm10"); ?> /</a>

		</nav> -->

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
			<!-- <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
			<div class="progress">
				<div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
			</div> -->
	</div>
</div>
</div>
</div>
</div>

<?php } ?>

</div>
<style>
	.full_width_height {
    width: 95vw;
    height: 110vh;
    display: flex;
    justify-content: center;
    align-items: flex-start; /* Cambia center por flex-start */
    box-sizing: border-box;
    margin-top: -20px; 
}
</style>