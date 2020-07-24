<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusCaptchaDataType extends formsPlusTextDataType
{
    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //wrong captcha code, properties: -
                'captchaError'                              => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please enter correct captcha code.</div>",
            )
        );
    }

    public function getFieldProp($name, $field){
        $temp                                               = parent::getFieldProp($name, $field);
        $temp['hashField']                                  = isset($field['hashField']) && is_string($field['hashField']) ? $field['hashField'] : $temp['name'].'Hash';
        return $temp;
    }

    public function parseValue($form, $value, $field){
        $value                                              = array(
            'code'                                              => is_string($value) ? $value : '',
            'hash'                                              => $form->getProxyValue( $field['name'].'Hash' )
        );
        return $value;
    }

    public function valueToString($value, $field){
        return '-';
    }

    public function getStoreValue($value, $field){
        return null;
    }

    public function validate($form, $value, $field){
        if( $this->checkRequired($value, $field) ){
            $this->addError( $form, $field, 'fieldRequired', array(
                'name'                                      => $field['title']
            ));
        }
        if( $value ){
            if( $this->checkHash($value) ){
                $this->addError( $form, $field, 'captchaError');
            }
        }
    }

    public function checkHash($value){
        return !( isset($value['code']) && isset($value['hash']) && ($this->getHash($value['code']) == $value['hash']) );
    }

    public function getHash($value){
        switch(PHP_INT_SIZE) {
            case 8:
                return $this->getHash64($value);
                break;
            default:
                return $this->getHash32($value);
                break;
        }
    }

    public function getHash32($value) {
        $hash = 5381; 
        $value = strtoupper($value); 
        for($i = 0; $i < strlen($value); $i++) { 
            $hash = (($hash << 5) + $hash) + ord(substr($value, $i)); 
        } 
        return $hash;
    }

    public function getHash64($value) { 
        $hash = 5381; 
        $value = strtoupper($value); 
        for($i = 0; $i < strlen($value); $i++) { 
            $hash = ($this->leftShift32($hash, 5) + $hash) + ord(substr($value, $i)); 
        } 
        return $hash; 
    }

    public function leftShift32($number, $steps) { 
        $binary = decbin($number);
        $binary = str_pad($binary, 32, "0", STR_PAD_LEFT);
        $binary = $binary.str_repeat("0", $steps);
        $binary = substr($binary, strlen($binary) - 32);
        return ($binary{0} == "0" ? bindec($binary) : 
            -(pow(2, 31) - bindec(substr($binary, 1)))); 
    }

    public function isIgnored($field, $value = false){
        return true;
    }
}