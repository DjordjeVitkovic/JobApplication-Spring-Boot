<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusChoiceDataType extends formsPlusArrayDataType
{
    public function getFieldProp($name, $field){
        $properties                                         = parent::getFieldProp($name, $field);

        $properties['multiple']                             = isset($field['multiple']) ? !!$field['multiple'] : false;
        if( !$properties['multiple'] ){
            $properties['max']                              = 1;
            $properties['min']                              = $properties['min'] ? 1 : null;
        }
        
        return $properties;
    }

    public function buildValue($value, $field){
        return $field['valuesList'] && isset($field['valuesList'][$value]) ? $value : null;
    }

    public function getNiceValue($value, $field){
        return $field['valuesList'] && isset($field['valuesList'][$value]) ? $field['valuesList'][$value] : null;
    }
}