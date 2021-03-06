<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 * (c) swebdeveloper <info@swebdeveloper.com>
 *
 * Please do not edit this file
 *
 * formsPlusBasic v1.0
 *
 * Please check ../advanced-example.php for more details
 *
 */

require_once('dataProxy/formsPlusDataProxyInterface.php');

class formsPlusBasicCore {
    public static $undefined                                = '__undefined';

    /**
     * Instance of formsPlusDataProxyInterface
     * @var formsPlusDataProxyInterface 
     */
    protected $fileProxy;

    /**
     * State
     * @var string 
     */
    protected $state;

    /**
     * List of messages (for now just errors)
     * @var array 
     */
    protected $messages;

    /**
     * List of message template
     * @var array 
     */
    protected $msgTemplates;

    public static function getMsgTemplates(){
        return array(
            //interfaces not found, properties: {interface}
            'interfaceNotFound'                             => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Please include <strong>{interface}</strong> interface to your php file.</div>",
            //class not implements required interface, properties: {class}, {interface}
            'notImplements'                                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{class}</strong> class not found or does not implements <strong>{interface}</strong>.</div>",
            //failed to set up content block, properties: {name}
            'failedSetContentBlock'                         => "<div class=\"alert alert-warning\"><strong><i class=\"fa fa-times\"></i> Warning:</strong> failed to set up <strong>{name}</strong> content block.</div>",
            //failed to set up service, properties: {name}
            'failedSetService'                              => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> failed to set up <strong>{name}</strong> service.</div>",
            /*
                Result content templates
                properties: {messages}, {fields}
                check result of $form->getNiceData
            */
            'success'                                       => "<div class=\"alert alert-valid\"><strong><i class=\"fa fa-check\"></i> Thank you</strong>, your message has been submitted to us.</div>",
            'error'                                         => "{messages:error:__join}",
        );
    }

    function __construct($options){
        $this->state                                        = 'nodata';

        //msgTemplates
            $this->msgTemplates                             = formsPlusBasicCore::getMsgTemplates();
            $this->messages                                 = array(
                'error'                                         => array(),
                'warning'                                       => array()
            );

            if( isset($options['msgTemplates']) && is_array($options['msgTemplates']) ){
                $this->msgTemplates                         = array_unique(array_merge($this->msgTemplates, $options['msgTemplates']));
            }

        //fileProxy
            $this->fileProxy                                = isset($options['fileProxy']) ? $options['fileProxy'] : false;

            if( $this->fileProxy ){
                if( is_string($this->fileProxy) && class_exists($this->fileProxy) ){
                    $this->fileProxy                            = new $this->fileProxy();
                }

                if( !interface_exists('formsPlusDataProxyInterface') ){
                    $this->addError('interfaceNotFound', 'formsPlusDataProxyInterface', array(
                        'interface'                             => 'formsPlusDataProxyInterface'
                    ));
                }else if( !($this->fileProxy instanceof formsPlusDataProxyInterface) ){
                    $this->addError("notImplements", "formsPlusDataProxy", array(
                        'interface'                             => 'formsPlusDataProxyInterface',
                        'class'                                 => gettype($this->fileProxy) == 'object' ? get_class($this->fileProxy) : ( is_string($this->fileProxy) ? $this->fileProxy : gettype($this->fileProxy) )
                    ));
                }
            }
    }

    public function setMessage($name, $msg){
        $this->msgTemplates[$name]                          = $msg;
        return $this;
    }

    public function addError($msg, $name = false, $params = false, &$container = false){
        $this->state                                        = 'invalid';
        return $this->addMessage('error', $msg, $name, $params, $container);
    }

    public function addMessage($type, $msg, $name = false, $params = false, &$container = false){
        if( strlen($msg) < 50 && isset($this->msgTemplates[$msg]) ){
            $name                                           = $name ? $name : $msg;
            $msg                                            = $this->msgTemplates[$msg];
        }

        if( is_array($params) ){
            $msg                                            = formsPlusBasicCore::buildTemplate($msg, $params);
        }

        if( !$msg ){
            return $this;
        }
        if( !is_array($container) ){
            $container                                      = &$this->messages;
        }
        if(!isset($container[$type])){
            $container[$type]                               = array();
        }
        if(is_string($name) && $name != '__push'){
            $container[$type][$name]                        = $msg;
        }else{
            $container[$type][]                             = $msg;
        }
        return $this;
    }

    public function hasData(){
        return $this->state != 'nodata';
    }

    public function isValid(){
        return $this->state == 'valid';
    }

    public function getFile($name){
        return $this->fileProxy ? $this->fileProxy->get($name) : null;
    }

    public static function buildFileTemplate($path, $data, $default = null){
        $content                                            = null;
        if( is_string($path) && file_exists($path) ){
            switch (pathinfo($path, PATHINFO_EXTENSION)) {
                case 'php':
                    ob_start();
                    include($path);
                    $content                                = ob_get_contents(); 
                    ob_end_clean();
                    break;
                case 'txt':
                case 'html':
                    $content                                = formsPlusBasicCore::buildTemplate( file_get_contents($path), $data, $default );
                    break;
            }
        }
        
        return $content;
    }

    public static function buildTemplate($template, $params, $default = null){
        if( !(is_string($template) && is_array($params)) ){
            return $template;
        }
        $template = preg_replace_callback(
            '|{([^}]+)}|',
            function ($matches) use($params, $default){
                return formsPlusBasicCore::getValueByString($params, $matches[1], is_null($default) ? $matches[0] : $default );
            },
            $template
        );
        return $template;
    }

    public static function getValueByString($params, $str, $default = null){
        if( trim($str) == '' ){
            return $default;
        }
        $keys                                               = explode(':', $str);
        $ret                                                = $params;
        $count                                              = count($keys);
        for ($i=0; $i < $count; $i++) {
            $break                                          = false;
            switch ($keys[$i]) {
                case '__join'       :
                case '__first'      :
                case '__last'       :
                    $ret                                    = formsPlusBasicCore::getSubVariable( $ret, $keys, $i );
                    break;
                default:
                    switch ( mb_substr($keys[$i], 0, 1) ) {
                        case '?':
                            $tmp                            = explode('?', mb_substr($keys[$i], 1));
                            $ret                            = formsPlusBasicCore::getConditionalVariable(
                                $ret,
                                is_array($ret) && isset($ret[trim($tmp[0])]) && $ret[trim($tmp[0])],
                                count($tmp) > 1 ? implode('?', array_slice($tmp, 1)) : false
                            );
                            break;
                        case '!':
                            $tmp                            = explode('?', mb_substr($keys[$i], 1));
                            $ret                            = formsPlusBasicCore::getConditionalVariable(
                                $ret,
                                !(is_array($ret) && isset($ret[trim($tmp[0])]) && $ret[trim($tmp[0])]),
                                count($tmp) > 1 ? implode('?', array_slice($tmp, 1)) : false
                            );
                            break;
                        default:
                            $ret                            = formsPlusBasicCore::getByKey( $ret, $keys[$i] );
                            break;
                    }
                    
                    break;
            }
            if( $ret == formsPlusBasicCore::$undefined ){
                break;
            }
        }
        return $ret != '__undefined' ? $ret : $default;
    }

    public static function getByKey($variable, $key){
        $key                                                = trim($key);
        return  mb_strlen($key) > mb_strlen(trim($key, "'\"")) ?
                    mb_substr($key, 1, mb_strlen($key) - 2) :
                    (is_array($variable) && isset($variable[$key]) ?
                        $variable[$key] :
                        formsPlusBasicCore::$undefined)
        ;
    }

    public static function getSubVariable($variable, $keys, &$i = 0){
        if( !(is_array($variable) && count($variable)) ){
            return formsPlusBasicCore::$undefined;
        }
        $ret                                                = '';
        if( !is_array($keys) ){
            $keys                                           = array($keys);
            $i                                              = 0;
        }
        $count                                              = count($keys);

        $next                                               = $count > ($i + 1) ? $keys[$i + 1] : false;
        switch ($keys[$i]) {
            case '__join'        :
                if( $next !== false ){
                    ++$i;
                }
                $ret                                        = implode($next ? $next : '', $variable);
                break;
            case '__first'      :
                $ret                                        = array_shift($variable);
                break;
            case '__last'       :
                $ret                                        = array_pop($variable);
                break;
        }

        return $ret;
    }

    public static function getConditionalVariable($variable, $isTrue, $result = false){
        if( $result ){
            $tmp                                            = explode('|', $result);
            $false                                          = count($tmp) > 1 ? formsPlusBasicCore::getByKey( $variable, $tmp[1] ) : formsPlusBasicCore::$undefined;
            $variable                                       = formsPlusBasicCore::getByKey( $variable, $tmp[0] );
        }
        
        return $isTrue ? $variable : $false;
    }
}