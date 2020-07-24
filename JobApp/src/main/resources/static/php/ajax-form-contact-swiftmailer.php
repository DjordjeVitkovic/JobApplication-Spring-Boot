<?php

/* For more information check advanced-examle.php */
header('Content-Type: application/json');

require_once('./formsPlus/formsPlusBasic.php');
require_once('./formsPlus/dataProxy/formsPlusPostData.php');
require_once('./formsPlus/dataType/formsPlusTextDataType.php');
require_once('./formsPlus/dataType/formsPlusEmailDataType.php');
require_once('./formsPlus/dataType/formsPlusCaptchaDataType.php');

require_once('./swiftmailer/lib/swift_required.php');
require_once('./formsPlus/service/formsPlusSwiftmailerService.php');
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
        'subject'                                                   => 'Subject',
        'message'                                                   => array(
            'title'                                                     => 'Message',
            'required'                                                  => true
        ),
        'captcha'                                                   => array(
            'required'                                                  => true,
            'type'                                                      => 'captcha'
        )
    ),
    'serviceTypes'                                              => array(
        'swiftmailer'                                               => 'formsPlusSwiftmailerService',
        'mysqli'                                                    => 'formsPlusSimpleMySQLiService'
    ),
    'services'                                                  => array(
        'storeContact'                                              => array(
            'type'                                                      => 'mysqli',
            'databaseName'                                              => 'formsplus',
            'tableName'                                                 => 'contact',
            'trackData'                                                 => true,
            'fields'                                                    => array(
                'name',
                'email',
                'subject',
                'message'
            )
        ),
        'sendAdminEmail'                                            => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => 'admin@mail.com',
            'from'                                                      => 'admin.services@mail.com',
            'replyTo'                                                   => '{fields:email:value}',
            'subject'                                                   => 'Forms Plus: New message from {fields:name:value}',
            'header'                                                    => "New message from {fields:name:value}",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            'transport'                                                 => array(
                'type'                                                      => 'smtp',
                'server'                                                    => null, // server addres 'smtp.mailserver.com'
                'port'                                                      => 25,
                'username'                                                  => '<user name>',
                'password'                                                  => '<user password>',
                'encryption'                                                => null // 'ssl' or 'tls' or null
            )
        ),
        'sendCustomerEmail'                                         => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => 'Forms Plus: Thank You, {fields:name:value}',
            'header'                                                    => "Thank You, {fields:name:value},".PHP_EOL."for your contacting us.",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            'template'                                                  => 
                "{fields:name:title}: {fields:name:value}".PHP_EOL.
                "{fields:email:title}: {fields:email:value}".PHP_EOL.
                "{fields:message:title}: ".PHP_EOL."{fields:message:value}".PHP_EOL.PHP_EOL.
                "From IP: {ip:value}",
            'transport'                                                 => array(
                'type'                                                      => 'smtp',
                'server'                                                    => null, // server addres 'smtp.mailserver.com'
                'port'                                                      => 25,
                'username'                                                  => '<user name>',
                'password'                                                  => '<user password>',
                'encryption'                                                => null // 'ssl' or 'tls' or null
            )
        ),
    )
));


$type                                                       = 'json';
echo $form->proccess($type);