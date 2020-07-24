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

require_once('./GoogleApi/src/Google/autoload.php');
require_once('./formsPlus/service/formsPlusGoogleSheetsService.php');

$form                                                       = new formsPlusBasic(array(
    'fileProxy'                                                 => 'formsPlusFileData',
    'dataTypes'                                                 => array(
        'email'                                                     => 'formsPlusEmailDataType',
        'captcha'                                                   => 'formsPlusCaptchaDataType',
        'file'                                                      => 'formsPlusFileDataType',
        'dateTime'                                                  => 'formsPlusDateTimeDataType',
        'choice'                                                    => 'formsPlusChoiceDataType',
    ),
    'contentBlocks'                                             => array(
        'success'                                                   => array(
            'block'                                                     => 'successContentBlock',
            'template'                                                  => "<div class=\"alert alert-valid\"><strong><i class=\"fa fa-check\"></i> Thank you</strong>, you can view your data <a href=\"https://docs.google.com/spreadsheets/d/14S9PRToNegRXKHfZsTj5eazo0yAxepHgvSxgQ3sFmSw/edit#gid=0\" target=\"_blank\">here</a>.</div>"
        ),
    ),
    'fields'                                                    => array(
        //Your details
        'firstName'                                                 => array(
            'title'                                                     => 'First name',
            'required'                                                  => true,
        ),
        'lastName'                                                  => array(
            'title'                                                     => 'Last name',
            'required'                                                  => true,
        ),
        'email'                                                     => array(
            'title'                                                     => 'Email',
            'required'                                                  => true,
            'type'                                                      => 'email',
        ),
        'contactPhone'                                              => array(
            'title'                                                     => 'Contact phone',
        ),

        //Booking details
        'adults'                                                    => array(
            'title'                                                     => 'Adults',
            'required'                                                  => true,
        ),
        'children'                                                  => array(
            'title'                                                     => 'Children',
        ),
        'checkInDate'                                               => array(
            'title'                                                     => 'Check-in date',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'd.m.y', // format for output
            'storeFormat'                                               => 'Y-m-d', // format for database storing
            'parseFormats'                                              => false, // format for parsing, can be string or array, tries to parse date-time with all available formats.
            'required'                                                  => true,
            'minTime'                                                   => date('d.m.y'), //set min time to NOW
        ),
        'checkOutDate'                                              => array(
            'title'                                                     => 'Check-out date',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'd.m.y', // format for output
            'storeFormat'                                               => 'Y-m-d', // format for database storing
            'parseFormats'                                              => false, // format for parsing, can be string or array, tries to parse date-time with all available formats.
            'required'                                                  => true,
            'minTime'                                                   => date('d.m.y'), //set min time to NOW
        ),

        'message'                                                   => array(
            'title'                                                     => 'Additional message',
        )
    ),
    'serviceTypes'                                              => array(
        'swiftmailer'                                               => 'formsPlusSwiftmailerService',
        'googleSheets'                                              => 'formsPlusGoogleSheetsService'
    ),
    'services'                                                  => array(
        //Remember to enable Google Sheets API at google console
        'store'                                                     => array(
            'type'                                                      => 'googleSheets',
            'createHeaders'                                             => true,
            'spreadsheetId'                                             => '<your spreedsheetID>',
            'worksheetId'                                               => '<your worksheet name>',
            'appName'                                                   => 'Codecanyon test',
            /* Uncomment if you want it to be done with Service account
            
            // Set authType = 'service' only if your server has configured openssl!!! and you will need to create at google console both OAuth 2.0 client ID and Service account key with p12 key
            // If authType is skipped - you can be asked to authorize on first form submit and create authFile.
            'authType'                                                  => 'service',

            //If authType = 'service' you will need to set 'serviceEmail' and 'clientId'
            //Remember to share your Spreadsheet with this email!!! without sharing you won't be able to add data to it
            'serviceEmail'                                              => '<service account ID/email>',
            'clientId'                                                  => '<your OAuth 2.0 client ID>',
            //Path to your .p12 key file
            'keyFile'                                                   => './private-data/Google/.key/key.p12',

            */
            //Path to your json file from/for OAuth 2.0 client ID
            'authFile'                                                  => './private-data/Google/.secret/client_secret.json',
            //Path where it will save credentials file for Google API authorization. The directory should exist and be writable, but don't create file itself, only if asked.
            'credentialsFile'                                           => './private-data/Google/.credentials/credentials.json',
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