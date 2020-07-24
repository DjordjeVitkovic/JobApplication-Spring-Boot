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

// List of translations for Your languages
$myLanguages = array(
    'en' => array(
        // list of all available 'msgTemplates' can be found at advanced-example.php
        'msgTemplates' => array(
            'fieldRequired' => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> is required.</div>",
        ),
        'emailTextes' => array(
            'admin' => array(
                'subject' => 'Forms Plus: New message from {fields:name:value}',
                'header' => 'New message from {fields:name:value}',
                'footer' => 'Best regards!'.PHP_EOL.'Created with Forms Plus',
            ),
            'customer' => array(
                'subject' => 'Forms Plus: Thank You, {fields:name:value}',
                'header' => 'Thank You, {fields:name:value},'.PHP_EOL.'for your contacting us.',
                'footer' => 'Best regards!'.PHP_EOL.'Created with Forms Plus',
                'template' =>
                    "{fields:name:title}: {fields:name:value}".PHP_EOL.
                    "{fields:email:title}: {fields:email:value}".PHP_EOL.
                    "{fields:message:title}: ".PHP_EOL."{fields:message:value}".PHP_EOL.PHP_EOL.
                    "From IP: {ip:value}",
            ),
        ),
    ),
    'fr' => array(
        'msgTemplates' => array(
            'fieldRequired' => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Erreur:</strong> <strong>{name}</strong> est requis.</div>",
        ),
        'emailTextes' => array(
            'admin' => array(
                'subject' => 'Forms Plus: Nouveau message de {fields:name:value}',
                'header' => 'Nouveau message de {fields:name:value}',
                'footer' => 'Cordialement!'.PHP_EOL.'Créé avec Forms Plus',
            ),
            'customer' => array(
                'subject' => 'Forms Plus: Merci, {fields:name:value}',
                'header' => 'Merci, {fields:name:value},'.PHP_EOL.'pour votre contact.',
                'footer' => 'Cordialement!'.PHP_EOL.'Créé avec Forms Plus',
                'template' =>
                    "{fields:name:title}: {fields:name:value}".PHP_EOL.
                    "{fields:email:title}: {fields:email:value}".PHP_EOL.
                    "{fields:message:title}: ".PHP_EOL."{fields:message:value}".PHP_EOL.PHP_EOL.
                    "De IP: {ip:value}",
            ),
        ),
    ),
);
$currentLanguage = 'en'; // provide it with some of your functionality

$currentLanguageTextes = isset($myLanguages[$currentLanguage]) ? $myLanguages[$currentLanguage]['msgTemplates'] : $myLanguages['en'];

$form                                                       = new formsPlusBasic(array(
    'msgTemplates'                                              => $currentLanguageTextes['msgTemplates'], // override message templates with $currentLanguage textes
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
        'sendmail'                                                  => 'formsPlusSendmailService',
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
            'type'                                                      => 'sendmail',
            'to'                                                        => 'admin@mail.com',
            'from'                                                      => 'admin.services@mail.com',
            'replyTo'                                                   => '{fields:email:value}',
            'subject'                                                   => $currentLanguageTextes['emailTextes']['admin']['subject'], // override with $currentLanguage textes
            'header'                                                    => $currentLanguageTextes['emailTextes']['admin']['header'], // override with $currentLanguage textes
            'footer'                                                    => $currentLanguageTextes['emailTextes']['admin']['footer'], // override with $currentLanguage textes
        ),
        'sendCustomerEmail'                                         => array(
            'type'                                                      => 'sendmail',
            'to'                                                        => '{fields:email:value}',
            'from'                                                      => 'noreply@mail.com',
            'subject'                                                   => $currentLanguageTextes['emailTextes']['customer']['subject'], // override with $currentLanguage textes
            'header'                                                    => $currentLanguageTextes['emailTextes']['customer']['header'], // override with $currentLanguage textes
            'footer'                                                    => $currentLanguageTextes['emailTextes']['customer']['footer'], // override with $currentLanguage textes
            'template'                                                  => $currentLanguageTextes['emailTextes']['customer']['template'], // override with $currentLanguage textes
        ),
    ),
));


$type                                                       = 'json';
echo $form->proccess($type);