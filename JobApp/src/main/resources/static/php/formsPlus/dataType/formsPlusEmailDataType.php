<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusEmailDataType extends formsPlusTextDataType
{
    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //invalid email, properties: {name}
                'emailError'                                => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - please enter a valid email address.</div>",
            )
        );
    }

    public function validate($form, $value, $field){
        if( $this->checkRequired($value, $field) ){
            $this->addError( $form, $field, 'fieldRequired', array(
                'name'                                      => $field['title']
            ));
        }
        if( $value ){
            if( $this->checkEmail($value) ){
                $this->addError( $form, $field, 'emailError', array(
                    'name'                                  => $field['title']
                ));
            }
        }
    }

    public function checkEmail($value){
        return !formsPlusBasic::isEmail($value);
    }
}