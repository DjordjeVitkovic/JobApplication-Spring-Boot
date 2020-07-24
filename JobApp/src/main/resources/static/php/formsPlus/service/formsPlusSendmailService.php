<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

require_once('formsPlusMailingService.php');

class formsPlusSendmailService extends formsPlusMailingService
{
    public static $isEnabled                                = true;

    public function getServiceName(){
        return 'Sendmail';
    }

    public function isAvailable($service){
        return function_exists( 'mail' ) && formsPlusSendmailService::$isEnabled;
    }

    public function send($form, $service, $data){
        $ret                                                = array(
            'status'                                            => false
        );
        if( !($this->isAvailable($service) && ($form->isValid() || $this->canIgnore($service))) ){
            return $ret;
        }

        $mail                                               = $this->getMail($service, $data, true);
        if( !$mail ){
            return $ret;
        }
        if( !mail( $this->emailsToString($mail['to']), $mail['subject'], $mail['template'], $mail['headers']) ){
            $this->addError($form, $service, 'failedSendEmail', array(
                'name'                                      => $service['name']
            ), '__push');
            return $ret;
        }
        $ret['status']                                      = true;
        return $ret;
    }
}