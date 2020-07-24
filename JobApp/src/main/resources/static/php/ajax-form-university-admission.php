<?php

/* For more information check advanced-examle.php */
header('Content-Type: application/json');

require_once('./formsPlus/formsPlusBasic.php');
require_once('./formsPlus/dataProxy/formsPlusPostData.php');
require_once('./formsPlus/dataProxy/formsPlusFileData.php');

require_once('./formsPlus/dataType/formsPlusTextDataType.php');
require_once('./formsPlus/dataType/formsPlusEmailDataType.php');
require_once('./formsPlus/dataType/formsPlusFileDataType.php');
require_once('./formsPlus/dataType/formsPlusDateTimeDataType.php');
require_once('./formsPlus/dataType/formsPlusArrayDataType.php');
require_once('./formsPlus/dataType/formsPlusChoiceDataType.php');
require_once('./formsPlus/dataType/formsPlusCaptchaDataType.php');

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
        'faculty'                                                   => array(
            'title'                                                     => 'Faculty',
            'required'                                                  => true,
            'type'                                                      => 'choice',
            'storeNice'                                                 => false,
            'valuesList'                                                => array(
                "business"      => "Business",
                "medicine"      => "Medicine",
                "design"        => "Design",
                "law"           => "Law",
                "informatics"   => "Informatics",
            )
        ),

        //Your details
        'name'                                                      => array(
            'title'                                                     => 'Name',
            'required'                                                  => true
        ),
        'dateBirth'                                                 => array(
            'title'                                                     => 'Date of birth',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'd.m.y', // format for output
            'storeFormat'                                               => 'Y-m-d', // format for database storing
            'parseFormats'                                              => false, // format for parsing, can be string or array, tries to parse date-time with all available formats.
            'required'                                                  => true,
            'maxTime'                                                   => new DateTime() //set max time to NOW
        ),
        'father'                                                    => array(
            'title'                                                     => 'Father name',
            'required'                                                  => true
        ),
        'mother'                                                    => array(
            'title'                                                     => 'Mother name',
            'required'                                                  => true
        ),
        'email'                                                     => array(
            'title'                                                     => 'Email',
            'required'                                                  => true,
            'type'                                                      => 'email'
        ),
        'contactPhone'                                              => 'Contact phone',
        'gender'                                                    => array(
            'title'                                                     => 'Gender',
            'type'                                                      => 'choice',
            'storeNice'                                                 => false,
            'valuesList'                                                => array(
                "male"          => "Male",
                "female"        => "Female",
                "other"         => "other",
            )
        ),

        //Your address
        'address'                                                   => array(
            'title'                                                     => 'Address',
            'required'                                                  => true
        ),
        'postalCode'                                                => array(
            'title'                                                     => 'Postal code',
            'required'                                                  => true
        ),
        'city'                                                      => array(
            'title'                                                     => 'City',
            'required'                                                  => true
        ),
        'country'                                                   => array(
            'title'                                                     => 'Country',
            'required'                                                  => true
        ),

        //School details
        'schoolName'                                                => array(
            'title'                                                     => 'School name',
            'required'                                                  => true
        ),
        'schoolPassingYear'                                         => array(
            'title'                                                     => 'School passing year',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'Y', // format for output
            'storeFormat'                                               => 'Y', // format for database storing
            'required'                                                  => true,
            'maxTime'                                                   => new DateTime() //set max time to NOW
        ),
        'schoolGrade'                                               => array(
            'title'                                                     => 'School grade',
            'required'                                                  => true
        ),
        'schoolMarksheet'                                           => array(
            'title'                                                     => 'School marksheet',
            'required'                                                  => true,
            'type'                                                      => 'file'
        ),
        //Academic Information
        'insituteName'                                              => array(
            'title'                                                     => 'Insitute name'
        ),
        'insitutePassingYear'                                       => array(
            'title'                                                     => 'Insitute passing year',
            'type'                                                      => 'dateTime',
            'format'                                                    => 'Y', // format for output
            'storeFormat'                                               => 'Y', // format for database storing
            'maxTime'                                                   => new DateTime() //set max time to NOW
        ),
        'insituteGrade'                                             => array(
            'title'                                                     => 'Insitute grade'
        ),
        'insituteMarksheet'                                         => array(
            'title'                                                     => 'Insitute marksheet',
            'type'                                                      => 'file'
        ),

        //Extra data
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
        'store'                                                     => array(
            'type'                                                      => 'mysqli',
            'databaseName'                                              => 'formsplus',
            'tableName'                                                 => 'university_admission',
            'trackData'                                                 => true,
            'fields'                                                    => array(
                'faculty',
                'name',
                'dateBirth',
                'father',
                'mother',
                'email',
                'contactPhone',
                'gender',
                'address',
                'postalCode',
                'city',
                'country',
                'schoolName',
                'schoolPassingYear',
                'schoolGrade',
                'schoolMarksheet',
                'insituteName',
                'insitutePassingYear',
                'insituteGrade',
                'insituteMarksheet',
                'message',
            )
        ),
        'sendAdminEmail'                                            => array(
            'type'                                                      => 'swiftmailer',
            'to'                                                        => 'admin@mail.com',
            'from'                                                      => 'admin.services@mail.com',
            'replyTo'                                                   => '{fields:email:value}',
            'subject'                                                   => 'Forms Plus: New application from {fields:name:value}',
            'header'                                                    => "New application from {fields:name:value}",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            'files'                                                     => array(
                '{fields:schoolMarksheet:raw:filePath}',
                './email-files/thank-you.txt'
            ),
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
            'subject'                                                   => 'Forms Plus: Thank You, {fields:name:value}',
            'header'                                                    => "Thank You, {fields:name:value},".PHP_EOL."for your application.",
            'footer'                                                    => "Best regards!".PHP_EOL."Created with Forms Plus",
            // 'templateFile' can be php, txt or html file
            //'templateFile'                                              => "./templates/email/form-university-admission.php",
            'templateFile'                                              => "./templates/email/form-university-admission.txt",
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