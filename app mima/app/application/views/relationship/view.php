<div class="page-title clearfix no-border bg-off-white">
    <h1>
        <?php echo lang('client_details') . " - " . $client_info->company_name ?>    
    </h1>
</div>

<div id="page-content" class="clearfix">

    <ul data-toggle="ajax-tab" class="nav nav-tabs" role="tablist">
    <li><a  role="presentation" href="<?php echo_uri("clients/company_info_tab/" . $client_info->id); ?>" data-target="#client-info"> <?php echo lang('client_info'); ?></a></li>
        <li><a  role="presentation" href="<?php echo_uri("clients/contacts/" . $client_info->id); ?>" data-target="#client-contacts"> <?php echo lang('contacts'); ?></a></li>
    </ul>
    <div class="tab-content">
    	<div role="tabpanel" class="tab-pane fade" id="client-info"></div>
        <div role="tabpanel" class="tab-pane fade" id="client-contacts"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        var tab = "<?php echo $tab; ?>";
        if (tab === "info") {
            $("[data-target=#client-info]").trigger("click");
        }

    });
</script>
