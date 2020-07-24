<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

require_once('formsPlusBaseService.php');

class formsPlusBaseFileService extends formsPlusBaseService
{
    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //path to file is not set, properties: {name}
                'servicePathNotSet'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> File path not set (<strong>{name}</strong> service).</div>",
                //directory doesn't exists, properties: {name}
                'serviceDirectoryNotExists'                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Directory does not exists (<strong>{name}</strong> service).</div>",
                //file extension is not set or not allowed, properties: {name}
                'servicePathExtension'                      => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Wrong file extension (<strong>{name}</strong> service).</div>",
                //failed to create file, properties: {name}
                'serviceFailedCreateFile'                   => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to create file (<strong>{name}</strong> service).</div>",
                //can't write to file (file permissions error, etc.), properties: {name}
                'serviceNotWritableFile'                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> File is not writable (<strong>{name}</strong> service).</div>",
                //can't set file permissions, properties: {name}
                'serviceFailedSetPermissions'               => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to set file permissions (<strong>{name}</strong> service).</div>",
            )
        );
    }

    public function getAllowedExtensions(){
        return array('txt');
    }

    public function getProp($name, $service){
        return array_merge(parent::getProp($name, $service), array(
            'separator'                                     => isset($service['separator']) && is_string($service['separator']) ? $service['separator'] : PHP_EOL,
            'path'                                          => isset($service['path']) && is_string($service['path']) ? $service['path'] : false,
            'createPermissions'                             => '0644',
            'override'                                      => false
        ));
    }

    public function check($form, $service, $data){
        if( !$form->isValid() ){
            return false;
        }
        if( !$this->isAvailable($service) ){
            $this->addError($form, $service, 'serviceNotAvailable', array(
                'name'                                      => $service['name']
            ));
            return false;
        }

        if( !$service['path'] ){
            $this->addError($form, $service, 'servicePathNotSet', array(
                'name'                                      => $service['name']
            ));
            return false;
        }
        $pathParts                                          = pathinfo($service['path']);
        if( !file_exists($pathParts['dirname']) ){
            $this->addError($form, $service, 'serviceDirectoryNotExists', array(
                'name'                                      => $service['name']
            ));
            return false;
        }
        $allowed                                            = $this->getAllowedExtensions();
        if( ($allowed !== true) && ($allowed === false || !in_array($pathParts['extension'], $allowed)) ){
            $this->addError($form, $service, 'servicePathExtension', array(
                'name'                                      => $service['name']
            ));
            return false;
        }
        return true;
    }

    public function writeFile($service, $content, $creationContent = false){
        $mode                                               = $service['override'] ? 'w' : 'a';
        $setPermissions                                     = false;
        if( !file_exists($service['path']) ){
            if( $fp = fopen($service['path'], $mode) ){
                if( $creationContent ){
                    fwrite($fp, $creationContent.$service['separator']);
                }
                $setPermissions                             = true;
            }else{
                $this->addError($form, $service, 'serviceFailedCreateFile', array(
                    'name'                                  => $service['name']
                ));
                return false;
            }
        }else if( $fp = fopen($service['path'], $mode) ){
            $content                                        = $service['separator'].$content;
        }else{
            $this->addError($form, $service, 'serviceNotWritableFile', array(
                'name'                                      => $service['name']
            ));
            return false;
        }

        fwrite($fp, $content);
        fclose($fp);
        if( $setPermissions && $service['createPermissions'] && !chmod($service['path'], $service['createPermissions']) ){
            $this->addError($form, $service, 'serviceFailedSetPermissions', array(
                'name'                                      => $service['name']
            ));
            return false;
        }
    }
}