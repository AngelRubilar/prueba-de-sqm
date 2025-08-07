<div class="panel panel-default mb15">
    <div class="page-title clearfix">
        <h1><?php echo lang('email_content'); ?></h1>
    </div>
    <div class="panel-body">
        <div id="div_email_content">
            <?php echo $email_content; ?>
        </div>
    </div>
    <div class="panel-footer clearfix">
        <button id="export_to_png" type="button" class="btn btn-default pull-right"><span class="fa fa-image"></span> <?php echo lang('export_to_png'); ?></button>
        <?php echo anchor(get_uri("Air_force_sending_forecast_alert/get_last_bulletin_pdf"), "<i class='fa fa-file-pdf'></i> ".lang("export_last_bulletin"), array("title" => lang("export_last_bulletin"), "class" => "btn btn-danger mr10 pull-right")); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#export_to_png').click(function() {

            html2canvas($("#div_email_content")[0]).then(function (canvas) {
                var anchorTag = document.createElement("a");
                document.body.appendChild(anchorTag);
                // document.getElementById("div_email_content").appendChild(canvas);
                anchorTag.download = "correo_alerta_pronosticos.png";
                anchorTag.href = canvas.toDataURL();
                anchorTag.target = '_blank';
                anchorTag.click();
            });

        });
        
    });
</script>