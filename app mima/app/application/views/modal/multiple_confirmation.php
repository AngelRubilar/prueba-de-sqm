<!-- Modal -->
<div class="modal fade" id="confirmationMultipleModal" tabindex="-1" role="dialog" aria-labelledby="confirmationMultipleModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <!--<h4 class="modal-title" id="confirmationMultipleModalTitle"><?php echo lang('delete') . "?"; ?></h4>-->
                <h4 class="modal-title" id="confirmationMultipleModalTitle"><?php echo lang('delete?'); ?></h4>
            </div>
            <div id="confirmationMultipleModalContent" class="modal-body">
                <?php echo lang('delete_multiple_confirmation_message'); ?>
            </div>
            <div class="modal-footer clearfix">
                <button id="confirmMultipleDeleteButton" type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-trash"></i> <?php echo lang("delete"); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo lang('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>