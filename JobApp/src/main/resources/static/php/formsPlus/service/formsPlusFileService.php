<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

require_once('formsPlusBaseFileService.php');

class formsPlusFileService extends formsPlusBaseFileService
{
    public static $isEnabled                                = true;

    public function getServiceName(){
        return 'File';
    }

    public function getAllowedExtensions(){
        return array('', 'txt', 'log');
    }

    public function getProp($name, $service){
        return array_merge(parent::getProp($name, $service), array(
            'template'                                      => isset($service['template']) && is_string($service['template']) ? $service['template'] : false,
            'header'                                        => isset($service['header']) && is_string($service['header']) ? $service['header'] : false,
            'footer'                                        => isset($service['footer']) && is_string($service['footer']) ? $service['footer'] : false,
        ));
    }

    public function isAvailable($service){
        return formsPlusFileService::$isEnabled;
    }

    public function send($form, $service, $data){
        $ret                                                = array(
            'status'                                            => false
        );
        if( !($this->isAvailable($service) && ($form->isValid() || $this->canIgnore($service))) ){
            return $ret;
        }

        $template                                           = '';
        if( $service['header'] ){
            $template                                       = formsPlusBasicCore::buildTemplate($service['header'], $data);
        }
        if( $service['template'] ){
            $data['service']                                = $service;
            $template                                       = formsPlusBasicCore::buildTemplate($service['template'], $data);
        }else{
            if( $service['header'] ){
                $template                                   .= PHP_EOL.PHP_EOL;
            }
            foreach ($data['fields'] as $key => $field){
                $template                                   .= $field['title'].": ".$field['value'].PHP_EOL;
            }
            $template                                       .= PHP_EOL."IP: ".$data['ip']['value'].PHP_EOL;
            $template                                       .= '-----------------------------------------'.PHP_EOL;
            if( $service['footer'] ){
                $template                                   .= PHP_EOL;
            }
        }
        if( $service['footer'] ){
            $template                                       .= formsPlusBasicCore::buildTemplate($service['footer'], $data);
        }
        if( $this->writeFile($service, $template) ){
            $ret['status']                                  = true;
        }
        return $ret;
    }
}