<?php

/* For more information check advanced-examle.php */
header('Content-Type: application/json');

require_once('./formsPlus/formsPlusBasic.php');
require_once('./formsPlus/dataProxy/formsPlusPostData.php');
require_once('./formsPlus/dataProxy/formsPlusFileData.php');

require_once('./formsPlus/dataType/formsPlusTextDataType.php');
require_once('./formsPlus/dataType/formsPlusEmailDataType.php');
require_once('./formsPlus/dataType/formsPlusDateTimeDataType.php');

require_once('./swiftmailer/lib/swift_required.php');
require_once('./formsPlus/service/formsPlusSwiftmailerService.php');
require_once('./formsPlus/service/formsPlusSimpleMySQLiService.php');

$form                                                       = new formsPlusBasic(array(
    'fileProxy'                                                 => 'formsPlusFileData',
    'dataTypes'                                                 => array(
        'email'                                                     => 'formsPlusEmailDataType',
        'captcha'                                                   => 'formsPlusCaptchaDataType',
        'file'                                                      => 'formsPlusFileDataType',
        'dateTime'                                                  => 'formsPlusDateTimeDataType',
        'choice'                                                    => 'formsPlusChoiceDataType',
    ),
    'fields'                                                    => array(
        //Your details
        'firstName'                                                 => array(
            'title'                                                     => 'First name',
            'required'                                                  => true,
            'step'                                                      => 2
        ),
        'lastName'                                                  => array(
            'title'                                                     => 'Last name',
            'required'                                                  => true,
            'step'                                                      => 2
        ),
        'email'                                                     => array(
            'title'                                                     => 'Email',
            'required'                                                  => true,
            'type'                                                      => 'email',
            'step'                                                      => 2
        ),
        'contactPhone'                                              => array(
            'title'                                                     => 'Contact phone',
            'step'                                                      => 2
        ),

        //Booking details
        'adults'                                                    => array(
            'title'                                                     => 'Adults',
            'required'                                                  => true,
            'step'                                                      => 1
        ),
        'children'                                                  => array(
            'title'                                                     => 'Children',
            'step'                                                      => 1
        ),
        'checkInDate'                                               => array(
            'title'                                                     => 'Check-in date',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'd.m.y', // format for output
            'storeFormat'                                               => 'Y-m-d', // format for database storing
            'parseFormats'                                              => false, // format for parsing, can be string or array, tries to parse date-time with all available formats.
            'required'                                                  => true,
            'minTime'                                                   => date('d.m.y'), //set min time to NOW
            'step'                                                      => 1
        ),
        'checkOutDate'                                              => array(
            'title'                                                     => 'Check-out date',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'd.m.y', // format for output
            'storeFormat'                                               => 'Y-m-d', // format for database storing
            'parseFormats'                                              => false, // format for parsing, can be string or array, tries to parse date-time with all available formats.
            'required'                                                  => true,
            'minTime'                                                   => date('d.m.y'), //set min time to NOW
            'step'                                                      => 1
        ),

        'message'                                                   => array(
            'title'                                                     => 'Additional message',
            'step'                                                      => 2
        )
    ),
    'serviceTypes'                                              => array(
        'swiftmailer'                                               => 'formsPlusSwiftmailerService',
        'mysqli'                                                    => 'formsPlusSimpleMySQLiService'
    ),
    'services'                                                  => array(
        'store'                                                     => array(
            'type'                                                      => 'mysqli',
            'databaseName'                                              => 'formsplus',
            'tableName'                                                 => 'booking',
            'trackData'                                                 => true,
            'fields'                                                    => array(
                'firstName',
                'lastName',
                'email',
                'contactPhone',
                'adults',
                'children',
                'checkInDate',
                'checkOutDate',
                'message',
            )
        ),
        'sendAdminEmail'                                            => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => 'admin@mail.com',
            'from'                                                      => 'admin.services@mail.com',
            'replyTo'                                                   => '{fields:email:value}',
            'subject'                                                   => 'Forms Plus: New application from {fields:firstName:value} {fields:lastName:value}',
            'header'                                                    => "New order from {fields:firstName:value} {fields:lastName:value}",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            // Uncomment to use SMTP transport, in example you have gmail smtp.
            /*
            'transport'                                                 => array(
                'type'                                                      => 'smtp',
                'server'                                                    => 'smtp.gmail.com', // server addres 'smtp.mailserver.com'
                'port'                                                      => 465,
                'username'                                                  => '<user name>',
                'password'                                                  => '<user password>',
                'encryption'                                                => 'ssl' // 'ssl' or 'tls' or null
            )
            */
        ),
        'sendCustomerEmail'                                         => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => 'Forms Plus: Thank You, {fields:firstName:value} {fields:lastName:value}',
            'header'                                                    => "Thank You, {fields:firstName:value} {fields:lastName:value},".PHP_EOL."for your application.",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            // 'templateFile' can be php, txt or html file
            'templateFile'                                              => "./templates/email/form-booking.txt",
            'files'                                                     => array(
                './email-files/thank-you.txt'
            ),
            // Uncomment to use SMTP transport
            /*'transport'                                                 => array(
                'type'                                                      => 'smtp',
                'server'                                                    => null, // server addres 'smtp.mailserver.com'
                'port'                                                      => 25,
                'username'                                                  => '<user name>',
                'password'                                                  => '<user password>',
                'encryption'                                                => null // 'ssl' or 'tls' or null
            )*/
        ),
    ),
));


$type                                                       = 'json';
echo $form->proccess($type);