<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

require_once('formsPlusBaseService.php');

class formsPlusMailingService extends formsPlusBaseService
{
    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //invalid reciever email, properties: {name}
                'notValidRecieverEmail'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Not valid reciever email in <strong>{name}</strong> service.</div>",
                //invalid sender email, properties: {name}
                'notValidSenderEmail'                       => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Not valid sender email in <strong>{name}</strong> service.</div>",
                //failed to send email, properties: {name}
                'failedSendEmail'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to send email (<strong>{name}</strong> service).</div>",
            )
        );
    }

    public function getServiceName(){
        return 'Mailing';
    }

    public function getProp($name, $service){
        return array_merge(parent::getProp($name, $service), array(
            'to'                                            => isset($service['to']) ? $service['to'] : false,
            'cc'                                            => isset($service['cc']) ? $service['cc'] : false,
            'bcc'                                           => isset($service['bcc']) ? $service['bcc'] : false,
            'from'                                          => isset($service['from']) ? $service['from'] : false,
            'replyTo'                                       => isset($service['replyTo']) ? $service['replyTo'] : false,
            'subject'                                       => isset($service['subject']) && is_string($service['subject']) ? $service['subject'] : "<no subject>",
            'contentType'                                   => isset($service['contentType']) && is_string($service['contentType']) ? $service['contentType'] : 'text/plain',
            'charset'                                       => isset($service['charset']) && is_string($service['charset']) ? $service['charset'] : 'utf-8',
            'templateFile'                                  => isset($service['templateFile']) && is_string($service['templateFile']) ? $service['templateFile'] : false,
            'template'                                      => isset($service['template']) && is_string($service['template']) ? $service['template'] : false,
            'header'                                        => isset($service['header']) && is_string($service['header']) ? $service['header'] : false,
            'footer'                                        => isset($service['footer']) && is_string($service['footer']) ? $service['footer'] : false,
        ));
    }

    public function check($form, $service, $data){
        if( !$form->isValid() ){
            return false;
        }
        if( !$this->isAvailable($service) ){
            $this->addError($form, $service, 'serviceNotAvailable', array(
                'name'                                      => $service['name']
            ));
            return false;
        }

        $mailData                                            = $this->formatEmails($service, $data);
        if( !($mailData['to'] && $mailData['from']) ){
            if( !$this->canIgnore($service) ){
                if( !$mailData['to'] ){
                    $this->addError($form, $service, 'notValidRecieverEmail', array(
                        'name'                              => $service['name']
                    ), '__push');
                }
                if( !$mailData['from'] ){
                    $this->addError($form, $service, 'notValidSenderEmail', array(
                        'name'                              => $service['name']
                    ), '__push');
                }
            }
            return false;
        }

        return true;
    }

    protected function getMail($service, $data, $buildHeaders = false){
        $mailData                                           = $this->formatEmails($service, $data);
        if( !($service['to'] && $service['from']) ){
            return false;
        }

        $mailData['template']                               = $this->buildMailTemplate($service, $data);
        $mailData['subject']                                = formsPlusBasicCore::buildTemplate($service['subject'], $data);
        $mailData['charset']                                = $service['charset'];
        $mailData['contentType']                            = $service['contentType'];
        if( $buildHeaders ){
            $mailData['headers']                            = $this->buildMailHeaders($service, $mailData);
        }

        return $mailData;
    }

    protected function buildMailTemplate($service, $data){
        $template                                           = '';
        if( $service['header'] ){
            $template                                       = formsPlusBasicCore::buildTemplate($service['header'], $data).PHP_EOL.PHP_EOL;
        }
        $data['service']                                    = $service;
        $hasFileTemplate                                    = false;
        if( $service['templateFile'] ){
            $tmp                                            = formsPlusBasicCore::buildFileTemplate($service['templateFile'], $data);
            if( !is_null($tmp) ){
                $template                                   .= $tmp;
                $hasFileTemplate                            = true;
            }
        }
        if( !$hasFileTemplate ){
            if( $service['template'] ){
                $template                                   .= formsPlusBasicCore::buildTemplate($service['template'], $data);
            }else{
                $service['contentType']                     = 'text/plain';
                foreach ($data['fields'] as $key => $field){
                    $template                               .= $field['title'].": ".$field['value'].PHP_EOL;
                }
                $template                                   .= PHP_EOL."IP: ".$data['ip']['value'];
            }
            if( $service['footer'] ){
                $template                                   .= PHP_EOL.PHP_EOL.formsPlusBasicCore::buildTemplate($service['footer'], $data);
            }
        }

        return $template;
    }

    protected function buildMailHeaders($service, $mailData){
        $headers                                            =  "From: ".$mailData['from'].PHP_EOL;
        if( $mailData['cc'] ){
            $headers                                        .= "Cc: ".$this->emailsToString($mailData['cc']).PHP_EOL;
        }
        if( $mailData['bcc'] ){
            $headers                                        .= "Bcc: ".$this->emailsToString($mailData['bcc']).PHP_EOL;
        }

        $headers                                            .= "Reply-To: ".$mailData['replyTo'].PHP_EOL;
        $headers                                            .= "MIME-Version: 1.0".PHP_EOL;
        $headers                                            .= "Content-type: ".$mailData['contentType']."; charset=".$mailData['charset'].PHP_EOL;
        $headers                                            .= "Content-Transfer-Encoding: quoted-printable".PHP_EOL;

        return $headers;
    }

    protected function formatEmails($service, $data){
        $mailData                                           = array(
            'to'                                                => $this->formatEmail($service['to'], $data),
            'cc'                                                => $this->formatEmail($service['cc'], $data),
            'bcc'                                               => $this->formatEmail($service['bcc'], $data),
            'from'                                              => $this->formatEmail($service['from'], $data),
            'replyTo'                                           => $this->formatEmail($service['replyTo'], $data),
        );
        
        //fall to from if replyTo is not available
        if( !$mailData['replyTo'] && $mailData['from'] ){
            $mailData['replyTo']                            = $mailData['from'];
        }

        return $mailData;
    }

    public function formatEmail($email, $data){
        if( is_string($email) ){
            $email                                          = trim($email);
            $email                                          = $email ? formsPlusBasicCore::buildTemplate($email, $data) : false;
            return $email && formsPlusBasic::isEmail($email) ? $email : false;
        }else if( is_array($email) ){
            $ret                                            = array();
            foreach ($email as $key => $val) {
                if( is_string($key) ){
                    $mail                                   = $key;
                    $value                                  = $val;
                }else{
                    $mail                                   = $val;
                    $value                                  = null;
                }
                if( $mail = $this->formatEmail($mail, $data) ){
                    $ret[$mail]                             = $value;
                }
            }
            return count($ret) ? $ret : false;
        }
        return false;
    }

    public function emailsToString($emails){
        if( is_string($emails) ){
            return $emails;
        }else if( is_array($emails) ){
            $ret                                            = array();
            foreach ($emails as $key => $val) {
                if( is_string($key) ){
                    $mail                                   = $key;
                    $value                                  = is_string($val) ? trim($val) : null;
                }else{
                    $mail                                   = ''.$val;
                    $value                                  = null;
                }
                if( ($mail = trim($mail)) && formsPlusBasic::isEmail($mail) ){
                    $ret[]                                  = $value ? $value.' <'.$mail.'>' : $mail;
                }
            }
            return count($ret) ? implode(', ', $ret) : null;
        }
        return null;
    }
}