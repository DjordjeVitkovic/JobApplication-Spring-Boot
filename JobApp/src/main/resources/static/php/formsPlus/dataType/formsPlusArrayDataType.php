<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusArrayDataType extends formsPlusTextDataType
{
    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //properties: {name}, {min}
                'arrayMinError'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - please choose {min} or more.</div>",
                //properties: {name}, {max}
                'arrayMaxError'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - please choose {max} or less.</div>",
            )
        );
    }

    public function getFieldProp($name, $field){
        return array_merge( parent::getFieldProp($name, $field), array(
            'min'                                           => isset($field['min']) && is_integer($field['min']) ? $field['min'] : null,
            'max'                                           => isset($field['max']) && is_integer($field['max']) ? $field['max'] : null,
            'join'                                          => isset($field['join']) && is_string($field['join']) ? $field['join'] : ', ',
        ));
    }

    public function parseValue($form, $value, $field){
        if( is_null($value) && !is_null($field['default']) ){
            $value                                          = is_array($field['default']) ? $field['default'] : array($field['default']);
        }else if( !is_array($value) ){
            $value                                          = array($value);
        }
        foreach($value as $key => $val) {
            $value[$key]                                    = $this->buildValue($value[$key], $field);
            if( is_null($value[$key]) ){
                unset($value[$key]);
            }
        }

        return count($value) ? array_values($value) : null;
    }

    public function validate($form, $value, $field){
        if( $this->checkRequired($value, $field) ){
            $this->addError( $form, $field, 'fieldRequired', array(
                'name'                                      => $field['title']
            ));
        }

        if( $this->checkMin($value, $field) ){
            $this->addError( $form, $field, 'arrayMinError', array(
                'name'                                      => $field['title'],
                'min'                                       => $field['min']
            ));
        }

        if( $this->checkMax($value, $field) ){
            $this->addError( $form, $field, 'arrayMaxError', array(
                'name'                                      => $field['title'],
                'max'                                       => $field['max']
            ));
        }
    }

    public function checkMin($value, $field){
        return !!( is_integer($field['min']) && (!$value || (count($value) < $field['min']) ) );
    }

    public function checkMax($value, $field){
        return !!( is_integer($field['max']) && $value && (count($value) > $field['max']) );
    }

    public function valueToString($value, $field){
        $value                                              = is_array($value) ? $value : array($value);
        $ret                                                = array();
        foreach ($value as $key => $val) {
            $val                                            = $this->getNiceValue($val, $field);
            if( $val ){
                $ret[$key]                                  = $val;
            }
        }
        return count($ret) ? implode($field['join'], $ret) : '-';
    }

    public function getStoreValue($value, $field){
        $value                                              = is_array($value) ? $value : array($value);
        $ret                                                = array();
        foreach ($value as $key => $val) {
            $val                                            = parent::getStoreValue($val, $field);
            if( $val ){
                $ret[$key]                                  = $val;
            }
        }
        return count($ret) ? implode($field['join'], $ret) : null;
    }
}