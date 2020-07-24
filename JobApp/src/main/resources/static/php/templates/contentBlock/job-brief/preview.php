<?php
    function jobBriefPreviewOutField($form, $field, $icon = null){
        if( !($field instanceof formsPlusDataTypeInterface) ){
            $field = $form->getField($field);
        }
        if($field && $field->checkRequiredFields()){
            jobBriefPreviewOutValue($field->getTitle(), $field->getOutputValue('Not Specified'), $icon);
            return true;
        }
        return false;
    }
    function jobBriefPreviewOutFirstField($form, $fields){
        foreach ($fields as $field) {
            if(jobBriefPreviewOutField($form, $field, $icon)){
                return;
            }
        }
    }
    function jobBriefPreviewOutFieldValue($form, $field, $icon = null){
        if( !($field instanceof formsPlusDataTypeInterface) ){
            $field = $form->getField($field);
        }
        if($field){
            jobBriefPreviewOutValue(null, $field->getOutputValue('Not Specified'), $icon);
            return true;
        }
        return false;
    }
    function jobBriefPreviewOutExtraCheckField($form, $fieldID, $icon = null){
        $field = $form->getField($fieldID);
        if($field){
            if( !$field->getValue() ){
                jobBriefPreviewOutField($form, $field);
                return;
            }
            $id = $field->getFullID();
            jobBriefPreviewOutField($form, $field, $icon );
            jobBriefPreviewOutField($form, $id . 'List', $icon );
            jobBriefPreviewOutField($form, $id . 'ListOther', 'pencil-square-o');
        }
    }
    function jobBriefPreviewOutValue($title = null, $value = null, $icon = null){
?>
            <div class="p-field-value">
                <?php if( !is_null($icon) ){ ?><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php } ?>
                <?php if( !is_null($title) ){ ?><span class="p-value-label"><?php echo $title ?>:</span><?php } ?>
                <?php if( !is_null($value) ){ ?><span class="p-value-text p-colored-text"><?php echo $value ?></span><?php } ?>
            </div>
<?php } ?>
<br class="hidden-print" />
<div class="row">
    <div class="col-sm-6">
        <?php jobBriefPreviewOutValue('Job Number', $data['extraData']['jobNumber'], 'tag') ?>
    </div>
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'datetime', 'calendar') ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'username', 'user') ?>
    </div>
    <div class="col-sm-2">
        <?php jobBriefPreviewOutField($form, 'userid') ?>
    </div>
    <div class="col-sm-4">
        <?php jobBriefPreviewOutValue('IP Address', $form->getIP()) ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'refnumber', 'bookmark-o') ?>
    </div>
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'contactsource', 'paper-plane') ?>
    </div>
</div>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">01&nbsp;&nbsp;&nbsp;ABOUT THE CLIENT</span></div>
<?php jobBriefPreviewOutField($form, 'client.name', 'bookmark') ?>
<?php jobBriefPreviewOutField($form, 'client.country', 'globe') ?>
<?php jobBriefPreviewOutField($form, 'client.brand', 'university') ?>
<?php jobBriefPreviewOutField($form, 'client.website', 'home') ?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">02&nbsp;&nbsp;&nbsp;ABOUT THE JOB</span></div>
<?php
    $job = $form->getField('job');
    $jobType                                                = $job->getValue();
    if( !$jobType ){
        jobBriefPreviewOutField($form, $job, 'briefcase');
    }else{
        jobBriefPreviewOutFieldValue(null, $job, 'briefcase');
        $jdType = $form->getField('jobDetails.type');
        if( $jdType->getValue() == 'Other' ){
            jobBriefPreviewOutValue($jdType->getTitle(), $form->getField('jobDetails.typeOther')->getOutputValue('Not Specified'), 'cog');
        }else{
            jobBriefPreviewOutField($form, $jdType, 'cog');
        }
        switch ($jobType) {
            case '02A':
?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.language', 'pencil-square-o') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.duration', 'clock-o') ?>
                    </div>
                </div>
<?php
                
                break;
            case '02B':
?>
                <br class="hidden-print" />
                <div class="text-left"><span class="p-title-side"><i class="fa fa-university"></i>&nbsp;&nbsp;&nbsp;EXISTING BRAND IDENTITY (Current)</span></span></div>
                <div class="p-sub-block"><?php jobBriefPreviewOutFieldValue($form, 'jobDetails.current'); ?></div>
                <br class="hidden-print" />
                <div class="text-left"><span class="p-title-side"><i class="fa fa-university"></i>&nbsp;&nbsp;&nbsp;NEW BRAND IDENTITY (Desired)</span></span></div>
                <div class="p-sub-block"><?php jobBriefPreviewOutFieldValue($form, 'jobDetails.new'); ?></div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.language', 'pencil-square-o') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.format', 'cog') ?>
                    </div>
                </div>
<?php
                
                break;
            case '02C':
            case '02E':
?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.language', 'pencil-square-o') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.duration', 'clock-o') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.screenplay', 'sign-in') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.audioMix', 'sign-in') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.shootingType', 'sign-in') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.locationsNumber', 'sign-in') ?>
                    </div>
                </div>
<?php
                
                break;
            case '02D':
?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.venue', 'pencil-square-o') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.dateTime', 'pencil-square-o') ?>
                    </div>
                </div>
<?php
                
                break;
            case '02F':
            case '02G':
            case '02I':
?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.language', 'pencil-square-o') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.size', 'sign-in') ?>
                    </div>
                </div>
<?php
                
                break;
            case '02H':
?>
                <br class="hidden-print" />
                <div class="text-left"><span class="p-title-side"><i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Job Specification</span></div>
                <div class="p-sub-block">
<?php
                    jobBriefPreviewOutField($form, 'jobDetails.location');
                    jobBriefPreviewOutField($form, 'jobDetails.purpose');
                    jobBriefPreviewOutField($form, 'jobDetails.image');
?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.daysNumber', 'sign-in') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.shotsNumber', 'sign-in') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.shootingType', 'sign-in') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'jobDetails.spotsNumber', 'sign-in') ?>
                    </div>
                </div>
<?php
                
                break;
        }
        jobBriefPreviewOutField($form, 'jobDetails.communicationObjective', 'pencil-square-o');
?>
        <br class="hidden-print" />
        <div class="text-left"><span class="p-title-side"><i class="fa fa-list visible-print-inline" aria-hidden="true"></i>&nbsp;SCOPE OF WORK</span></div>
        <div class="p-sub-block">
<?php
            jobBriefPreviewOutFieldValue($form, 'jobDetails.scope');
            jobBriefPreviewOutField($form, 'jobDetails.scopeOtherList');
?>
        </div>
<?php
        if( in_array($jobType, array('02C', '02E', '02H', '02I')) ){
?>
            <div class="row">
                <div class="col-sm-6">
                    <?php jobBriefPreviewOutExtraCheckField($form, 'jobDetails.models', 'users') ?>
                </div>
                <div class="col-sm-6">
                    <?php jobBriefPreviewOutExtraCheckField($form, 'jobDetails.locations', 'map-marker') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?php jobBriefPreviewOutExtraCheckField($form, 'jobDetails.materialAvailable', 'file-o') ?>
                </div>
                <div class="col-sm-6">
                    <?php jobBriefPreviewOutExtraCheckField($form, 'jobDetails.filmingPermit', 'pencil-square-o') ?>
                </div>
            </div>
            <br class="hidden-print" />
            <div class="text-left"><span class="p-title-side"><i class="fa fa-calendar-o"></i>&nbsp;&nbsp;PREFERRED DATES FOR PHOTO SHOOT</span></div>
            <div class="p-sub-block"><?php jobBriefPreviewOutFieldValue($form, 'jobDetails.dates'); ?></div>
<?php
        }
        jobBriefPreviewOutField($form, 'jobDetails.details', 'file-text-o');
    }
?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">03&nbsp;&nbsp;&nbsp;TYPE OF CAMPAIGN</span></div>
<?php
    jobBriefPreviewOutFieldValue($form, 'campaign.type', 'circle');
    jobBriefPreviewOutField($form, 'campaign.typeOther', 'pencil');
?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">04&nbsp;&nbsp;&nbsp;TARGET AUDIENCE</span></div>
<?php
    $jobTA = array('primary', 'secondary');
    jobBriefPreviewOutField($form, 'targetAudience.details', 'eye');
    foreach ($jobTA as $key => $ta) {
        $taf = $form->getField('targetAudience.' . $ta);
        if( $taf && $taf->checkRequiredFields() ){
?>
            <br class="hidden-print" />
            <div class="text-left"><span class="p-title-side"><i class="fa fa-male"></i><i class="fa fa-child"></i><i class="fa fa-female"></i>&nbsp;&nbsp;&nbsp;<?php echo $taf->getTitle() ?></span></div>
            <div class="p-sub-block">
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'targetAudience.' . $ta . '.ageGroup', 'user') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'targetAudience.' . $ta . '.gender', 'circle') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'targetAudience.' . $ta . '.country', 'globe') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'targetAudience.' . $ta . '.comments', 'pencil-square-o') ?>
                    </div>
                </div>
            </div>
<?php
        }
    }
?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">05&nbsp;&nbsp;&nbsp;COMPETITOR ACTIVITIES</span></div>
<?php
    $jobCA = array('competitors', 'campaigns');
    foreach ($jobCA as $key => $ca) {
        jobBriefPreviewOutField($form, 'competitorActivities.' . $ca, 'user-secret');
        if( $form->getField('competitorActivities.' . $ca . 'Details')->checkRequiredFields() ){
?>
            <div class="p-sub-block">
                <div class="row">
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'competitorActivities.' . $ca . 'Details.name', 'university') ?>
                    </div>
                    <div class="col-sm-6">
                        <?php jobBriefPreviewOutField($form, 'competitorActivities.' . $ca . 'Details.reference', 'search') ?>
                    </div>
                </div>
            </div>
<?php
        }
    }
    jobBriefPreviewOutField($form, 'competitorActivities.comments', 'pencil');
?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">06&nbsp;&nbsp;&nbsp;DELIVERY</span></div>
<div class="row">
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'delivery.date', 'calendar') ?>
    </div>
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'delivery.type', 'cog') ?>
    </div>
</div>
<?php jobBriefPreviewOutField($form, 'delivery.comments', 'pencil') ?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">07&nbsp;&nbsp;&nbsp;PAYMENT TERMS</span></div>
<div class="row">
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'payment.currency', 'dollar') ?>
    </div>
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'payment.budget', 'money') ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'payment.advance', 'money') ?>
    </div>
    <div class="col-sm-6">
        <?php jobBriefPreviewOutField($form, 'payment.schedule', 'calendar') ?>
    </div>
</div>
<?php jobBriefPreviewOutField($form, 'payment.paymentTerms', 'pencil') ?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">08&nbsp;&nbsp;&nbsp;UPLOAD FILES</span></div>
<?php jobBriefPreviewOutField($form, 'uploadFiles.images', 'picture-o') ?>
<?php jobBriefPreviewOutField($form, 'uploadFiles.other', 'circle') ?>
<?php jobBriefPreviewOutField($form, 'uploadFiles.files', 'file-o') ?>
<?php jobBriefPreviewOutField($form, 'uploadFiles.comments', 'pencil') ?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">09&nbsp;&nbsp;&nbsp;CONTACT PERSON</span></div>
<br class="hidden-print" />
<div class="text-left"><span class="p-title-side"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;First Contact Person</span></div>
<div class="p-sub-block">
    <div class="row">
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.first.name', 'user') ?>
        </div>
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.first.phone', 'phone') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.first.email1', 'envelope-o') ?>
        </div>
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.first.email2', 'envelope-o') ?>
        </div>
    </div>
</div>
<br class="hidden-print" />
<div class="text-left"><span class="p-title-side"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;Second Contact Person</span></div>
<div class="p-sub-block">
    <div class="row">
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.second.name', 'user') ?>
        </div>
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.second.phone', 'phone') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.second.email1', 'envelope-o') ?>
        </div>
        <div class="col-sm-6">
            <?php jobBriefPreviewOutField($form, 'contact.second.email2', 'envelope-o') ?>
        </div>
    </div>
</div>
<?php jobBriefPreviewOutField($form, 'contact.comments', 'pencil') ?>
<div class="clearfix"></div>
<div class="p-subtitle text-left"><span class="p-title-side">10&nbsp;&nbsp;&nbsp;ADDITIONAL REMARKS (if any)</span></div>
<?php jobBriefPreviewOutField($form, 'remarks', 'pencil') ?>
<div class="clearfix"></div>
<input type="hidden" value="<?php echo $form->getExtraData('jobNumber') ?>" name="__jobNumber" />
<div class="hidden-print">
    <hr class="p-flat" />
    <div class="collapse" data-js-block="njFormInfo"></div>
    <div class="collapse" data-js-block="njSuccessMsgBlock"></div>
    <div data-js-block="njSuccessBlock" class="collapse text-right">
        <button class="btn" type="submit" name="action" value="newForm" data-js-ajax-submit="">ADD NEW</button>
    </div>
    <div data-js-block="njFailBlock" class="collapse">
        <h4>Form data send failed!</h4>
        <div data-js-block="njErrorContentBlock"></div>
    </div>
    <br/>
    <div data-js-block="njLoadingBlock" class="progress collapse">
        <div class="progress-bar progress-bar-fp progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
    </div>
    <div class="text-right" data-js-block="njFormPreviewBtnBlock">
        <br/><br/><br/>
        <button class="btn" type="submit" name="action" value="restoreDraft" data-js-ajax-submit=""><i class="fa fa-pencil-square-o"></i>&nbsp;EDIT</button>
        <a class="btn" type="button" href="javascript:window.print()" ><i class="fa fa-print" aria-hidden="true"></i>&nbsp;PRINT</a>
        <button class="btn" type="submit" name="action" value="store" data-js-ajax-submit=""><i class="fa fa-check-square-o"></i>&nbsp;SUBMIT</button>
    </div>
</div>
<div class="collapse">
    <?php
        //TODO
        include_once('../job-brief-form.php');
    ?>
</div>