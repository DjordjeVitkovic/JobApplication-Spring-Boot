<div class="alert alert-info">
    <strong><i class="fa fa-info"></i> Current Job Number:</strong> <?php echo $form->getExtraData('jobNumber') ?>
    <?php if($form->getExtraData('isAdmin') && $form->getExtraData('isDraft') && $form->getExtraData('jobNumber')){ ?>(DRAFT)<?php } ?>
</div>