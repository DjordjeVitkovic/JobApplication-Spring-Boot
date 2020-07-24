<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusBaseService implements formsPlusServiceInterface
{
    public static function getMsgTemplates(){
        return array(
            //service is not available, properties: {name}
            'serviceNotAvailable'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> {name} service is not available.</div>",
            //failed to store data, properties: {name} {msg}
            'serviceStoreError'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> {name} service failed to store data.</div>",
        );
    }

    public function getProp($name, $service){
        return array(
            'name'                                          => isset($service['name']) ? $service['name'] : $name,
            'canIgnore'                                     => isset($service['canIgnore']) ? !!$service['canIgnore'] : false,
            'msgTemplates'                                  => isset($service['msgTemplates']) && is_array($service['msgTemplates']) ? $service['msgTemplates'] : null,
        );
    }

    public function canIgnore($service){
        return !!$service['canIgnore'];
    }

    public function isAvailable($service){
        return $this->canIgnore($service);
    }

    public function canFetch($form, $service){
        return false;
    }

    public function fetch($form, $service, $filters = null){
        return null;
    }

    public function send($form, $service, $data){
        return false;
    }

    protected function addError($form, $service, $templateName, $params = false, $name = false){
        if( !$this->canIgnore($service) ){
            $name                                               = $name ? $name : $templateName.ucfirst($service['name']);

            //Add error message from service or form msgTemplates
            $form->addError(
                $service['msgTemplates'] && $service['msgTemplates'][$templateName] ? $service['msgTemplates'][$templateName] : $templateName,
                $name,
                $params
            );
            return $this;
        }
        return $this;
    }

    public static function smartFieldContent($field, $data, $property = false){
        $value                                              = null;
        if( !is_null($field['template']) && is_string($field['template']) ){
            $value                                          = formsPlusBasicCore::buildTemplate($field['template'], $data);
        }else if( !is_null($field['byString']) && is_string($field['byString']) ){
            $value                                          = formsPlusBasicCore::getValueByString($data, $field['byString'], null);
        }else if( !is_null($field['value']) ){
            $value                                          = $field['value'];
        }else if( !is_null($field['fieldKey']) && isset($data['fields'][$field['fieldKey']]) ){
            $value                                          = $property && isset( $data['fields'][$field['fieldKey']][$property] ) ?
                $data['fields'][$field['fieldKey']][$property] :
                $data['fields'][$field['fieldKey']]['value']
            ;
        }
        return $value;
    }

    public static function getSmartFieldProp($name, $field = array()){
        if( !is_array($field) ){
            if( is_string($field) ){
                $field                                      = array(
                    'fieldKey'                                  => $field
                );
            }else{
                $field                                      = array();
            }
        }
        $prop                                               = array(
            'name'                                              => isset($field['name']) && is_string($field['name']) ? $field['name'] : $name,
            'fieldKey'                                          => isset($field['fieldKey']) ? $field['fieldKey'] : $name,
            'template'                                          => isset($field['template']) ? $field['template'] : null,
            'byString'                                          => isset($field['byString']) ? $field['byString'] : null,
            'value'                                             => isset($field['value']) ? $field['value'] : null,
            'trackData'                                         => isset($field['trackData']) ? !!$field['trackData'] : true,
            'required'                                          => isset($field['required']) ? !!$field['required'] : false,
            'canFetch'                                          => isset($field['canFetch']) ? !!$field['canFetch'] : true,
            'canInsert'                                         => isset($field['canInsert']) ? !!$field['canInsert'] : true,
            'canUpdate'                                         => isset($field['canUpdate']) ? !!$field['canUpdate'] : true,
            'type'                                              => isset($field['type']) ? !!$field['type'] : null,
            'checkOnUpdate'                                     => isset($field['checkOnUpdate']) ? !!$field['checkOnUpdate'] : false,
        );
        if( isset($field['fetchWhere']) ){
            $prop['fetchWhere']                             = $field['fetchWhere'];
        }
        $prop['title']                                      = isset($field['title']) && is_string($field['title']) ? $field['title'] : $prop['name'];
        return $prop;
    }
}