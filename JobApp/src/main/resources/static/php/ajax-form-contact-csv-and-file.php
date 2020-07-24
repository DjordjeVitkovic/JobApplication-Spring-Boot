<?php

/* For more information check advanced-examle.php */
header('Content-Type: application/json');

require_once('./formsPlus/formsPlusBasic.php');
require_once('./formsPlus/dataProxy/formsPlusPostData.php');
require_once('./formsPlus/dataType/formsPlusTextDataType.php');
require_once('./formsPlus/dataType/formsPlusEmailDataType.php');
require_once('./formsPlus/dataType/formsPlusCaptchaDataType.php');

require_once('./formsPlus/service/formsPlusCSVService.php');
require_once('./formsPlus/service/formsPlusFileService.php');

$form                                                       = new formsPlusBasic(array(
    'dataTypes'                                                 => array(
        'email'                                                     => 'formsPlusCSVService',
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
        'csv'                                                       => 'formsPlusCSVService',
        'file'                                                      => 'formsPlusFileService',
    ),
    'services'                                                  => array(
        'storeContactCSV'                                           => array(
            'type'                                                      => 'csv',
            'path'                                                      => './files/contact.csv',
            'fieldsSeparator'                                           => ';',
            'fields'                                                    => array(
                'name',
                'email',
                'subject',
                'message'
            )
        ),
        'storeContactFile'                                          => array(
            'type'                                                      => 'file',
            'path'                                                      => './files/contact.txt',
        ),
        'logContact'                                                => array(
            'type'                                                      => 'file',
            'path'                                                      => 'files/contact.log',
            'template'                                                  => date("M d Y H:i:s")." - {fields:name:value} {fields:email:value} {ip:value}"
        )
    )
));


$type                                                       = 'json';
echo $form->proccess($type);