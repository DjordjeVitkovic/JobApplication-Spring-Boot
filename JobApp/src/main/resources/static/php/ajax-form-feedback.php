<?php

/* For more information check advanced-examle.php */
header('Content-Type: application/json');

require_once('./formsPlus/formsPlusBasic.php');
require_once('./formsPlus/dataProxy/formsPlusPostData.php');
require_once('./formsPlus/dataType/formsPlusTextDataType.php');
require_once('./formsPlus/dataType/formsPlusEmailDataType.php');
require_once('./formsPlus/dataType/formsPlusCaptchaDataType.php');

require_once('./formsPlus/service/formsPlusSendmailService.php');
require_once('./formsPlus/service/formsPlusSimpleMySQLiService.php');

$form                                                       = new formsPlusBasic(array(
    'dataTypes'                                                 => array(
        'email'                                                     => 'formsPlusEmailDataType',
        'captcha'                                                   => 'formsPlusCaptchaDataType'
    ),
    'fields'                                                    => array(
        'name'                                                      => array(
            'title'                                                     => 'Name',
            'required'                                                  => true
        ),
        'email'                                                     => array(
            'title'                                                     => 'Email',
            'required'                                                  => true,
            'type'                                                      => 'email'
        ),
        'department'                                                => 'Department',
        'message'                                                   => array(
            'title'                                                     => 'Message',
            'required'                                                  => true
        ),
        'productQuality'                                            => 'Product quality',
        'serviceQuality'                                            => 'Service quality',
        'supportQuality'                                            => 'Support quality',
        'captcha'                                                   => array(
            'required'                                                  => true,
            'type'                                                      => 'captcha'
        )
    ),
    'serviceTypes'                                              => array(
        'sendmail'                                                  => 'formsPlusSendmailService',
        'mysqli'                                                    => 'formsPlusSimpleMySQLiService'
    ),
    'services'                                                  => array(
        'storeFeedback'                                             => array(
            'type'                                                      => 'mysqli',
            'databaseName'                                              => 'formsplus',
            'tableName'                                                 => 'feedback',
            'trackData'                                                 => true,
            'fields'                                                    => array(
                'name',
                'email',
                'department',
                'message',
                'productQuality',
                'serviceQuality',
                'supportQuality',
            )
        ),
        'sendAdminEmail'                                            => array(
            'type'                                                      => 'sendmail',
            'to'                                                        => 'admin@mail.com',
            'from'                                                      => 'admin.services@mail.com',
            'replyTo'                                                   => '{fields:email:value}',
            'subject'                                                   => 'Forms Plus: New message from {fields:name:value}',
            'header'                                                    => "New message from {fields:name:value}",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
        ),
        'sendCustomerEmail'                                         => array(
            'type'                                                      => 'sendmail',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => 'Forms Plus: Thank You, {fields:name:value}',
            'header'                                                    => "Thank You, {fields:name:value},".PHP_EOL."for your feedback.",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            'template'                                                  => 
                "{fields:name:title}: {fields:name:value}".PHP_EOL.
                "{fields:email:title}: {fields:email:value}".PHP_EOL.
                "{fields:productQuality:title}: {fields:productQuality:value}".PHP_EOL.
                "{fields:serviceQuality:title}: {fields:serviceQuality:value}".PHP_EOL.
                "{fields:supportQuality:title}: {fields:supportQuality:value}".PHP_EOL.
                "{fields:message:title}: ".PHP_EOL."{fields:message:value}".PHP_EOL.PHP_EOL.
                "From IP: {ip:value}",
        ),
    )
));


$type                                                       = 'json';
echo $form->proccess($type);