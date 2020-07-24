<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 *  notSentEmails           - rejected email addresses, properties: {name}, {emails}
 *
 */

require_once('formsPlusMailingService.php');

class formsPlusSwiftmailerService extends formsPlusMailingService
{
    public static $isEnabled                                = true;

    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //rejected email addresses, properties: {name}, {emails}
                'notSentEmails'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to send email (<strong>{name}</strong> service) to: {emails:__join:, }.</div>",
            )
        );
    }

    public function getServiceName(){
        return 'Swiftmailer';
    }

    public function getProp($name, $service){
        $transports                                         = array();
        if( isset($service['transport']) && is_array($service['transport']) ){
            if(isset($service['transport']['type']) || isset($service['transport']['command']) || isset($service['transport']['server'])){
                $transports[]                               = $this->getTransportProp($service['transport']);
            }else{
                foreach ($service['transport'] as $key => $transport) {
                    $transports[$key]                       = $this->getTransportProp($transport);
                }
            }
        }else{
            $transports[]                                   = $this->getTransportProp();
        }

        $useTransport                                       = isset($service['useTransport']) ? $service['useTransport'] : null;

        if( is_string($useTransport) ){
            $useTransport                                   = array($useTransport);
        }

        $files                                              = array();
        if( isset($service['files']) && is_array($service['files']) ){
            $tmp                                            = is_array($service['files']) ? $service['files'] : array($service['files']);
            foreach ($tmp as $file) {
                if( is_string($file) && trim($file) ){
                    $files[]                                = trim($file);
                }
            }
        }

        return array_merge(parent::getProp($name, $service), array(
            'transport'                                     => $transports,
            'useTransport'                                  => $useTransport,
            'useNextTransport'                              => isset($service['useNextTransport']) ? !!$service['useNextTransport'] : false,
            'files'                                         => count($files) ? $files : null
        ));
    }

    public function getTransportProp($transport = array()){
        if( !is_array($transport) ){
            $transport                                      = array();
        }
        $ret                                                = array(
            'type'                                              => isset($transport['type']) && is_string($transport['type']) ? strtolower($transport['type']) : 'sendmail',
            'nextTransport'                                     => isset($transport['nextTransport']) ? strtolower($transport['nextTransport']) : true
        );

        switch($ret['type']) {
            case 'smtp':
                $ret                                        = array_merge($ret, array(
                    'server'                                    => isset($transport['server']) && is_string($transport['server']) ? strtolower($transport['server']) : false,
                    'port'                                      => isset($transport['port']) && $transport['port'] ? $transport['port'] : 25,
                    'username'                                  => isset($transport['username']) ? $transport['username'] : null,
                    'password'                                  => isset($transport['password']) ? $transport['password'] : null,
                    'encryption'                                => isset($transport['encryption']) && is_string($transport['encryption']) ? $transport['encryption'] : null,
                ));
                break;
            //sendmail - is default
            default:
                $ret                                        = array_merge($ret, array(
                    'command'                                   => isset($transport['command']) && is_string($transport['command']) ? $transport['command'] : null
                ));
                break;
        }

        return $ret;
    }

    public function isAvailable($service){
        return formsPlusSwiftmailerService::$isEnabled;
    }

    public function send($form, $service, $data){
        $ret                                                = array(
            'status'                                            => false
        );
        if( !($this->isAvailable($service) && ($form->isValid() || $this->canIgnore($service))) ){
            return $ret;
        }
        if( !is_null($service['useTransport']) && !$service['useTransport'] ){
            $ret['status']                                  = true;
            return $ret;
        }

        $mail                                               = $this->getMail($service, $data);
        if( !$mail ){
            return $ret;
        }
        $ret['status']                                      = true;
        $message                                            = $this->createMessage($mail);
        $transports                                         = $service['transport'];

        if( $service['files'] ){
            foreach ($service['files'] as $path) {
                $attachment                                 = $this->createAttachment($form, $service, $path, $data);
                if( $attachment ){
                    $message->attach($attachment);
                }
            }
        }

        if(is_array($service['useTransport'])){
            $transports                                     = array_intersect_key($transports, array_flip($service['useTransport']));
        }else if($service['useTransport'] === true){
            $transports                                     = array( array_shift($transports) );
        }

        //The check before trarting sending
        if( !($this->isAvailable($service) && ($form->isValid() || $this->canIgnore($service))) ){
            return $ret;
        }
        foreach($transports as $transport) {
            if( $this->sendBy($form, $service, $transport, $message) ){
                continue;
            }
            $ret['status']                                  = false;
            $this->addError($form, $service, 'failedSendEmail', array(
                'name'                                          => $service['name']
            ), '__push');
            break;
        }

        return $ret;
    }

    public function sendBy($form, $service, $transportParams, $message, $ignore = false){
        $transport                                          = $this->getTransport($form, $service, $transportParams);
        $result                                             = false;
        if( $transport ){
            $mailer                                         = Swift_Mailer::newInstance($transport);
            $result                                         = $mailer->send($message, $failures);
        }
        if( !$result && ($service['useNextTransport'] !== false) && ($transportParams['nextTransport'] !== false) ){
            $nextTransport                                  = false;
            if( $transportParams['nextTransport'] && isset($service['transport'][$transportParams['nextTransport']]) ){
                $nextTransport                              = $service['transport'][$transportParams['nextTransport']];
            }else if( $transportParams['nextTransport'] === true ){
                $catchNext                                  = false;
                foreach ($service['transport'] as $value) {
                    if( $value === $transportParams ){
                        $catchNext                          = true;
                    }else if( $catchNext ){
                        $nextTransport                      = $value;
                        break;
                    }
                }
            }
            if( $nextTransport ){
                $newMsg                                     = is_array($failures) && count($failures) ? $this->filterMessageRecievers($message, $failures) : $message;

                if( $newMsg ){
                    $result                                     = $this->sendBy($form, $service, $nextTransport, $newMsg, true);

                    if( is_array($result) ){
                        $failures                               = $result;
                        $result                                 = false;          
                    }
                }
                
            }
        }
        if( !$result ){
            if( !$ignore && is_array($failures) ){
                $this->addError($form, $service, 'notSentEmails', array(
                    'name'                                  => $service['name'],
                    'emails'                                => $failures
                ), '__push');
            }
            return $ignore && is_array($failures) ? $failures : false;
        }
        return true;
    }

    public function filterMessageRecievers($message, $allowed){
        $newMsg                                             = clone $message;
        $newMsg->setTo($this->filterEmails($newMsg->getTo(), $allowed));
        if( $emails = $message->getCc() ){
            $newMsg->setCc($newMsg->filterEmails($emails, $allowed));
        }
        if( $emails = $message->getBcc() ){
            $newMsg->setBcc($newMsg->filterEmails($emails, $allowed));
        }
        return $newMsg;
    }

    public function filterEmails($emails, $allowed){
        if( !is_array($emails) ){
            return null;
        }
        $ret                                                = array();
        foreach ($emails as $key => $value) {
            if( is_string($key) && in_array($key, $allowed) ){
                $ret[$key]                                  = $value;
            }else if( in_array($value, $allowed) ){
                $ret[]                                      = $value;
            }
        }
        return count($ret) ? $ret : null;
    }

    protected function createAttachment($form, $service, $path, $data){
        $path                                               = formsPlusBasicCore::buildTemplate($path, $data);
        if( !file_exists($path) ){
            $this->addError($form, $service, 'serviceFileNotFound', array(
                'name'                                      => $service['name'],
                'path'                                      => $path
            ));
            return false;
        }else{
            return Swift_Attachment::fromPath($path);
        }
    }

    public function getTransport($form, $service, $params){
        $ret                                                = false;
        switch($params['type']) {
            case 'smtp':
                if($params['server']){
                    $ret                                    = Swift_SmtpTransport::newInstance($params['server'], $params['port']);
                    if($params['username']){
                        $ret->setUsername($params['username']);
                    }
                    if($params['password']){
                        $ret->setPassword($params['password']);
                    }
                    if(!is_null($params['encryption'])){
                        $ret->setEncryption($params['encryption']);
                    }
                }
                break;
            //sendmail - is default
            default:
                if($params['command']){
                    $ret                                    = Swift_SendmailTransport::newInstance($params['command']);
                }else{
                    $ret                                    = Swift_MailTransport::newInstance();
                }
                break;
        }
        return $ret;
    }

    public function createMessage($params){
        $message                                            = Swift_Message::newInstance()
            ->setSubject($params['subject'])
            ->setFrom($params['from'])
            ->setReplyTo($params['replyTo'])
            ->setTo($params['to'])
            ->setBody($params['template'], $params['contentType'], $params['charset'])
        ;
        if( $params['cc'] ){
            $message->setCc($params['cc']);
        }
        if( $params['bcc'] ){
            $message->setBcc($params['bcc']);
        }
        return $message;
    }
}