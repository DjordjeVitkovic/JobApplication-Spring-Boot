<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

require_once('formsPlusBaseFileService.php');

class formsPlusCSVService extends formsPlusBaseFileService
{
    public static $isEnabled                                = true;

    public function getServiceName(){
        return 'CSV';
    }

    public function getAllowedExtensions(){
        return array('csv');
    }

    public function getProp($name, $service){
        $fields                                             = array();
        if( isset($service['fields']) && is_array($service['fields']) ){
            foreach ($service['fields'] as $fname => $field) {
                if( is_string($field) && !is_string($fname) ){
                    $fname                                  = $field;
                }
                $fields[$fname]                             = formsPlusBaseService::getSmartFieldProp($fname, $field);
            }
        }
        return array_merge(parent::getProp($name, $service), array(
            'fieldsSeparator'                               => isset($service['fieldsSeparator']) && is_string($service['fieldsSeparator']) ? $service['fieldsSeparator'] : ',',
            'trackData'                                     => isset($service['trackData']) ? !!$service['trackData'] : false,
            'createHeaders'                                 => isset($service['createHeaders']) ? !!$service['createHeaders'] : true,
            'fields'                                        => $fields
        ));
    }

    public function isAvailable($service){
        return formsPlusCSVService::$isEnabled;
    }

    public function send($form, $service, $data){
        $ret                                                = array(
            'status'                                            => false
        );
        if( !($this->isAvailable($service) && ($form->isValid() || $this->canIgnore($service))) ){
            return $ret;
        }

        $insertData                                         = array();
        $headers                                            = array();
        $trackData                                          = array();
        foreach ($service['fields'] as $key => $field) {
            $value                                          = formsPlusBaseService::smartFieldContent($field, $data);

            if( $service['trackData'] && $field['trackData'] ){
                $trackData[$key]                            = $value;
            }

            if( $field['required'] && !$value ){
                $error                                      = $key . " is required.";
                $this->addError($form, $service, 'serviceStoreError', array(
                    'name'                                  => $service['name'],
                    'msg'                                   => $error
                ));
                return $ret;
            }

            $insertData[$field['name']]                     = $value;
            $headers[$field['name']]                        = $field['title'];
        }
        if( $service['trackData'] ){
            $ret['trackData']                               = $trackData;
        }
        if( $this->writeFile($service, implode($service['fieldsSeparator'], $insertData), implode($service['fieldsSeparator'], $headers)) ){
            $ret['status']                                  = true;
        }
        return $ret;
    }
}