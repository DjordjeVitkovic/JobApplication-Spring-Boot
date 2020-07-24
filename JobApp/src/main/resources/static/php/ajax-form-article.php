<?php

/* For more information check advanced-examle.php */

require_once('formsPlus/formsPlusBasic.php');
require_once('formsPlus/dataProxy/formsPlusPostData.php');
require_once('formsPlus/dataType/formsPlusTextDataType.php');
require_once('formsPlus/dataType/formsPlusDateTimeDataType.php');

require_once('formsPlus/service/formsPlusSendmailService.php');
require_once('formsPlus/service/formsPlusSimpleMySQLiService.php');

$date                                                       = new DateTime();

$servicesSettings                                           = array(
    'dbSet'                                                     => array(
        'type'                                                      => 'mysqli',
        'databaseName'                                              => 'formsplus',
        'tableName'                                                 => 'article',
        'trackData'                                                 => true,
        'fields'                                                    => array(
            'id'                                                => array(
                'checkOnUpdate'                                     => true,
                'canUpdate'                                         => false,
                'canInsert'                                         => false
            ),
            'title',
            'author',
            'content',
            'date'                                              => array(
                'type'                                              => 'date'
            ),
            'updated'                                           => array(
                'value'                                             => $date->format('Y-m-d H:i:s'),
                'type'                                              => 'date'
            ),
            'created'                                           => array(
                'canUpdate'                                         => false,
                'value'                                             => $date->format('Y-m-d H:i:s'),
                'type'                                              => 'date'
            )
        )
    )
);

$successBlock                                                   = array(
    'block'                                                         => 'successContentBlock',
);

if( $_POST && isset($_POST['_FormAction']) ){
    header('Content-Type: application/json');
    switch ($_POST['_FormAction']) {
        case 'insert':
            $servicesSettings['sendAdminEmail']                 = array(
                'type'                                              => 'sendmail',
                'to'                                                => 'admin@mail.com',
                'from'                                              => 'admin.services@mail.com',
                'replyTo'                                           => 'noreply@mail.com',
                'subject'                                           => 'Forms Plus: New Article - {fields:title:value}',
                'header'                                            => "New Article from {fields:author:value}",
                'footer'                                            => "Best regards!".PHP_EOL."Created with Forms Plus",
            );
            $successBlock['template']                           = "<div class=\"alert alert-valid\"><strong><i class=\"fa fa-check\"></i> Thank you</strong>, your article was added.</div>";
            break;
        case 'update':
            $servicesSettings['dbSet']['storingType']           = 'update';
            $successBlock['template']                           = "<div class=\"alert alert-valid\"><strong><i class=\"fa fa-check\"></i> Thank you</strong>, article was updated.</div>";
            break;
    }
}

$form                                                       = new formsPlusBasic(array(
    'dataTypes'                                                 => array(
        'dateTime'                                                  => 'formsPlusDateTimeDataType',
    ),
    'contentBlocks'                                             => array(
        'success'                                                   => $successBlock,
    ),
    'fields'                                                    => array(
        'id'                                                        => 'ID',
        'title'                                                     => array(
            'title'                                                     => 'Title',
            'required'                                                  => true
        ),
        'author'                                                    => array(
            'title'                                                     => 'Author',
            'required'                                                  => true
        ),
        'date'                                                      => array(
            'title'                                                     => 'Date',
            'type'                                                      => 'dateTime',
            'required'                                                  => true,
            'default'                                                   => $date
        ),
        'content'                                                   => array(
            'title'                                                     => 'Content',
            'required'                                                  => true
        ),
    ),
    'serviceTypes'                                              => array(
        'sendmail'                                                  => 'formsPlusSendmailService',
        'mysqli'                                                    => 'formsPlusSimpleMySQLiService'
    ),
    'services'                                                  => $servicesSettings,
));

if( isset($_POST) && isset($_POST['_FormAction']) ){
    $type                                                       = 'json';
    echo $form->proccess($type);
}