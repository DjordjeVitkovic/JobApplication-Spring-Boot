<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusTextDataType implements formsPlusDataTypeInterface
{
    protected $hasErrors                                    = false;

    public static function getMsgTemplates(){
        return array(
            'fieldRequired'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> is required.</div>",
        );
    }

    public function getFieldProp($name, $field){
        return array(
            'name'                                          => isset($field['name']) ? $field['name'] : $name,
            'title'                                         => isset($field['title']) ? $field['title'] : $name,
            'required'                                      => isset($field['required']) ? !!$field['required'] : false,
            'ignore'                                        => isset($field['ignore']) ? !!$field['ignore'] : false,
            'default'                                       => isset($field['default']) ? $field['default'] : null,
            'valuesList'                                    => isset($field['valuesList']) && is_array($field['valuesList']) ? $field['valuesList'] : null,
            'step'                                          => isset($field['step']) ? $field['step'] : null,
            'msgTemplates'                                  => isset($field['msgTemplates']) && is_array($field['msgTemplates']) ? $field['msgTemplates'] : null,
            'storeNice'                                     => isset($field['storeNice']) ? !!$field['storeNice'] : true,
        );
    }

    public function parseValue($form, $value, $field){
        if( is_null($value) && !is_null($field['default']) ){
            $value                                          = $field['default'];
        }
        return $this->buildValue($value, $field);
    }

    public function buildValue($value, $field){
        return $value || $value == '0' || $value == 0 ? trim((string)$value) : null;
    }

    public function validate($form, $value, $field){
        if( $this->checkRequired($value, $field) ){
            $this->addError( $form, $field, 'fieldRequired', array(
                'name'                                      => $field['title']
            ));
        }
    }

    public function getNiceValue($value, $field){
        return $field['valuesList'] && isset($field['valuesList'][$value]) ? $field['valuesList'][$value] : (string)$value;
    }

    public function getTitle($field, $value = false){
        return isset($field['title']) && is_string($field['title']) ? (string)$field['title'] : 'unknown';
    }

    public function valueToString($value, $field){
        return $value || $value == '0' || $value == 0 ? $this->getNiceValue($value, $field) : '-';
    }

    public function getStoreValue($value, $field){
        if( $value ){
            return $field['storeNice'] ? $this->getNiceValue($value, $field) : $this->buildValue($value, $field);
        }
        return null;
    }

    public function checkRequired($value, $field){
        return !!($field['required'] && !$value);
    }

    protected function addError($form, $field, $templateName, $params = false, $name = false){
        $this->hasErrors                                    = true;
        $name                                               = $name ? $name : $templateName.ucfirst($field['name']);

        //Jump to field step on error
        if( is_integer($field['step']) ){
            $form->setStep($field['step']);
        }

        //Add error message from field or form msgTemplates
        $messages                                           = isset($field['_messages']) ? $field['_messages'] : false;
        $form->addError(
            $field['msgTemplates'] && $field['msgTemplates'][$templateName] ? $field['msgTemplates'][$templateName] : $templateName,
            $name,
            $params,
            $messages
        );
        return $this;
    }

    public function isIgnored($field, $value = false){
        return $field['ignore'];
    }
}