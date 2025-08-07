<div class="tab-content">
    <?php echo form_open(get_uri("clients/save/"), array("id" => "company-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
    <div class="panel">
        <div class="panel-default panel-heading">
            <h4> <?php echo lang('client_info'); ?></h4>
        </div>
        <div class="panel-body">
            <?php $this->load->view("clients/client_form_view"); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>