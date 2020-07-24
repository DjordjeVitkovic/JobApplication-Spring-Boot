<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusFileDataType extends formsPlusTextDataType
{
    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //directory to save file does not exists or can't be written, properties: {name}
                'fieldDirectoryError'                       => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - upload directory error.</div>",
                //file upload error, properties: {name}
                'fieldFileError'                            => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file upload error.</div>",
                //no file uploaded, properties: {name}
                'fieldFileNoFile'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - no file uploaded.</div>",
                //file max size error, properties: {name}
                'fieldFileMaxSizeError'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file max size error.</div>",
                //file min size error, properties: {name}
                'fieldFileMinSizeError'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file min size error.</div>",
                //file extension/type is not in 'fileTypes' list, properties: {name}
                'fieldFileTypeError'                        => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file type is not allowed.</div>",
                //file mime type is not in 'fileMimeTypes' list, properties: {name}
                'fieldFileMimeTypeError'                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - file mime type is not allowed.</div>",
                //failed to move file to upload directory, properties: {name}
                'fieldFileStoreError'                       => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> - failed to store file.</div>",
            )
        );
    }

    public function getFieldProp($name, $field){
        $fileTypes                                          = null;
        if( isset($field['fileTypes']) ){
            if( is_array($field['fileTypes']) ){
                $fileTypes                                  = array();
                foreach ($field['fileTypes'] as $key => $value) {
                    if( is_string($value) && trim($value) ){
                        $fileTypes[]                        = trim($value);
                    }
                };
                if( !count($fileTypes) ){
                    $fileTypes                              = null;
                }
            }else if( is_string($field['fileTypes']) && trim($field['fileTypes']) ){
                $fileTypes                                  = array(trim($field['fileTypes']));
            }
        }
        $fileMimeTypes                                      = null;
        if( isset($field['fileMimeTypes']) ){
            if( is_array($field['fileMimeTypes']) ){
                $fileMimeTypes                              = array();
                foreach ($field['fileMimeTypes'] as $key => $value) {
                    if( is_string($value) && trim($value) ){
                        $fileMimeTypes[]                    = trim($value);
                    }
                };
                if( !count($fileMimeTypes) ){
                    $fileMimeTypes                          = null;
                }
            }else if( is_string($field['fileMimeTypes']) && trim($field['fileMimeTypes']) ){
                $fileMimeTypes                              = array(trim($field['fileMimeTypes']));
            }
        }
        
        return array_merge( parent::getFieldProp($name, $field), array(
            'isFile'                                        => true,
            'dir'                                           => isset($field['dir']) && is_string($field['dir']) ? $field['dir'] : './files/',
            'storeFile'                                     => isset($field['storeFile']) ? !!$field['storeFile'] : true,
            'minSize'                                       => isset($field['minSize']) ? $field['minSize'] : null,
            'maxSize'                                       => isset($field['maxSize']) ? $field['maxSize'] : null,
            'fileTypes'                                     => $fileTypes,
            'fileMimeTypes'                                 => $fileMimeTypes,
        ));
    }

    public function parseValue($form, $value, $field){
        $ret                                                = null;
        if( is_array($value) && isset($value['tmp_name']) && isset($value['name']) && isset($value['size']) ){
            $ret                                            = array(
                'fileName'                                  => $value['name'],
                'filePath'                                  => $value['tmp_name'],
                'fileSize'                                  => $value['size']
            );
            if( isset($value['error']) ){
                $ret['error']                               = $value['error'];
            }
        }
        return $ret;
    }

    public function checkRequired($value, $field){
        return !!($field['required'] && (!$value || !is_array($value)) );
    }

    public function validate($form, $value, $field){
        if( !file_exists($field['dir']) || !is_writable($field['dir']) ){
            $this->addError($form, $field, 'fieldDirectoryError', array(
                'name'                                      => $field['name']
            ));
            return;
        }
        if( $this->checkRequired($value, $field) || !is_array($value) ){
            $this->addError( $form, $field, 'fieldRequired', array(
                'name'                                      => $field['title']
            ));
        }else if($value){
            if( !(  isset($value['filePath']) && is_uploaded_file($value['filePath']) &&
                    isset($value['error']) && !is_array($value['error'])
                )){
                $this->addError($form, $field, 'fieldFileError', array(
                    'name'                                  => $field['name']
                ));
            }
            if( $value['error'] != UPLOAD_ERR_OK ){
                switch ($value['error']) {
                    case UPLOAD_ERR_NO_FILE:
                        $this->addError($form, $field, 'fieldFileNoFile', array(
                            'name'                          => $field['name']
                        ));
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->addError($form, $field, 'fieldFileMaxSizeError', array(
                            'name'                          => $field['name']
                        ));
                    default:
                        $this->addError($form, $field, 'fieldFileError', array(
                            'name'                          => $field['name']
                        ));
                }
                return;
            }
            if( $this->checkMinSize($value, $field) ){
                $this->addError($form, $field, 'fieldFileMinSizeError', array(
                    'name'                                  => $field['name']
                ));
            }
            if( $this->checkMaxSize($value, $field) ){
                $this->addError($form, $field, 'fieldFileMaxSizeError', array(
                    'name'                                  => $field['name']
                ));
            }
            if( $this->checkType($value, $field) ){
                $this->addError($form, $field, 'fieldFileTypeError', array(
                    'name'                                  => $field['name']
                ));
            }
            if( $this->checkMimeType($value, $field) ){
                $this->addError($form, $field, 'fieldFileMimeTypeError', array(
                    'name'                                  => $field['name']
                ));
            }
        }
    }

    public function store($form, &$value, $field){
        if( $this->hasErrors ){
            return false;
        }
        if( !isset($field['isStored']) || !$field['isStored'] ){
            $i                                              = 0;
            $name                                           = $value['fileName'];
            if( file_exists($field['dir'].$name) ){
                $info                                       = pathinfo($value['fileName']);
                while(true){
                    $name                                   = $info['filename'].'_'.(++$i).'.'.$info['extension'];
                    if( !file_exists($field['dir'].$name) ){
                        break;
                    }
                }
            }
            if( move_uploaded_file($value['filePath'], $field['dir'].$name) ){
                $value['fileName']                          = $name;
                $value['fileDir']                           = $field['dir'];
                $value['filePath']                          = $field['dir'].$name;
                $value['isStored']                          = true;
            }else{
                $this->addError($form, $field, 'fieldFileStoreError', array(
                    'name'                                  => $field['name']
                ));
            }
        }
        return false;
    }

    public function checkType($value, $field){
        return !!(!is_null($field['fileTypes']) && !in_array(pathinfo($value['fileName'], PATHINFO_EXTENSION), $field['fileTypes']) );
    }
    public function checkMimeType($value, $field){
        $finfo                                              = new finfo(FILEINFO_MIME_TYPE);
        return !!(!is_null($field['fileMimeTypes']) && !in_array($finfo->file($value['filePath']), $field['fileMimeTypes']) );
    }

    public function checkMinSize($value, $field){
        $size                                               = formsPlusFileDataType::toBytes($field['minSize']);
        return !!(!is_null($size) && $size > $value['fileSize'] ) ;
    }
    public function checkMaxSize($value, $field){
        $size                                               = formsPlusFileDataType::toBytes($field['maxSize']);
        return !!(!is_null($size) && $size < $value['fileSize'] ) ;
    }

    public function valueToString($value, $field){
        return is_array($value) && isset($value['fileName']) ? $value['fileName'] : '-';
    }
    public function getStoreValue($value, $field){
        return is_array($value) && isset($value['filePath']) ? $value['filePath'] : null;
    }

    public static function toBytes($value){
        if( is_string($value) ){
            preg_match("/^([0-9\.\,]*)((?:KB)|(?:MB)|(?:GB)|(?:TB)|(?:B))?$/", $value, $matches);
            if( $matches && count($matches) > 1 ){
                $value                                      = floatval($matches[1]);
                if( count($matches) > 2 ){
                    switch ($matches[2]) {
                        case 'KB':
                            $value                          = $value * 1024;
                            break;
                        case 'MB':
                            $value                          = $value * pow(1024, 2);
                            break;
                        case 'GB':
                            $value                          = $value * pow(1024, 3);
                            break;
                        case 'TB':
                            $value                          = $value * pow(1024, 4);
                            break;
                    }
                }
                return (int)$value;
            }
        }
        return is_int($value) ? $value : null;
    }
}
