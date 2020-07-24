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

require_once('formsPlusBasicCore.php');
require_once('dataProxy/formsPlusDataProxyInterface.php');
require_once('dataType/formsPlusDataTypeInterface.php');
require_once('service/formsPlusServiceInterface.php');

class formsPlusBasic extends formsPlusBasicCore {
    /**
     * Instance of formsPlusDataProxyInterface
     * @var formsPlusDataProxyInterface 
     */
    protected $dataProxy;

    /**
     * List of formsPlusDataTypeInterface objects
     * @var array 
     */
    protected $dataTypes;

    /**
     * Name of default data type
     * @var string 
     */
    protected $defaultDataType;

    /**
     * List of fields
     * @var array 
     */
    protected $fields;

    /**
     * Submited data
     * @var array 
     */
    protected $data;

    /**
     * Services result data
     * @var array 
     */
    protected $serviceData;

    /**
     * List of formsPlusServiceInterface objects
     * @var array 
     */
    protected $serviceTypes;

    /**
     * List of services settings
     * @var array 
     */
    protected $services;

    /**
     * List of content blocks
     * @var array 
     */
    protected $contentBlocks;

    /**
     * Go to step
     * @var integer 
     */
    protected $step;

    /**
     * Whether to use proxy addresses or not.
     * @var bool
     */
    protected $useProxy                                     = false;
    /**
     * List of trusted proxy IP addresses
     * @var array
     */
    protected $trustedProxies                               = array();
    /**
     * HTTP header to introspect for proxies
     * @var string
     */
    protected $proxyHeader                                  = 'HTTP_X_FORWARDED_FOR';

    function __construct($options){
        parent::__construct($options);
        $this->data                                         = array();
        $this->serviceData                                  = array();

        //dataProxy
            $this->dataProxy                                = isset($options['dataProxy']) ? $options['dataProxy'] : 'formsPlusPostData';

            if( is_string($this->dataProxy) && class_exists($this->dataProxy) ){
                $this->dataProxy                            = new $this->dataProxy();
            }

            if( !interface_exists('formsPlusDataProxyInterface') ){
                $this->addError('interfaceNotFound', 'formsPlusDataProxyInterface', array(
                    'interface'                             => 'formsPlusDataProxyInterface'
                ));
            }else if( !($this->dataProxy instanceof formsPlusDataProxyInterface) ){
                $this->addError("notImplements", "formsPlusDataProxy", array(
                    'interface'                             => 'formsPlusDataProxyInterface',
                    'class'                                 => gettype($this->dataProxy) == 'object' ? get_class($this->dataProxy) : ( is_string($this->dataProxy) ? $this->dataProxy : gettype($this->dataProxy) )
                ));
            }

        //dataTypes
            $this->dataTypes                                = array();
            $this->defaultDataType                          = isset($options['defaultDataType']) ? $options['defaultDataType'] : 'text';
            $dataTypes                                      = array(
                'text'                                          => 'formsPlusTextDataType',
            );
            if( isset($options['dataTypes']) && is_array($options['dataTypes']) ){
                $dataTypes                                  = array_merge($dataTypes, $options['dataTypes']);
            }
            foreach ($dataTypes as $key => $dataType) {
                $this->setDataType($key, $dataType);
            }

        //fields
            $this->fields                                   = array();
            if( isset($options['fields']) ){
                foreach ($options['fields'] as $key => $field) {
                    $this->setField($key, $field);
                }
            }

        //contentBlocks
            $contentBlocks                                  = array(
                'error'                                         => array(
                    'templateFile'                                  => './templates/contentBlock/error.html',
                    'block'                                         => 'errorContentBlock'
                ),
                'success'                                       => array(
                    'templateFile'                                  => './templates/contentBlock/success.html',
                    'block'                                         => 'successContentBlock'
                )
            );
            if( isset($options['contentBlocks']) && is_array($options['contentBlocks']) ){
                $contentBlocks                              = array_merge($contentBlocks, $options['contentBlocks']);
            }
            foreach ($contentBlocks as $key => $contentBlock) {
                $this->setContentBlock($key, $contentBlock);
            }

        //serviceTypes
            $this->serviceTypes                             = array();
            $serviceTypes                                   = array(
                'sendmail'                                      => 'formsPlusSendmailService',
            );
            if( isset($options['serviceTypes']) && is_array($options['serviceTypes']) ){
                $serviceTypes                               = array_merge($serviceTypes, $options['serviceTypes']);
            }
            foreach ($serviceTypes as $key => $serviceType) {
                $this->setServiceType($key, $serviceType);
            }

        //services
            $this->services                                 = array();
            if( isset($options['services']) ){
                foreach ($options['services'] as $key => $service) {
                    $this->setService($key, $service);
                }
            }

        // IP
            if( isset($options['useProxy']) ){
                $this->setUseProxy($options['useProxy']);
            }
            if( isset($options['trustedProxies']) ){
                $this->setTrustedProxies($options['trustedProxies']);
            }
            if( isset($options['proxyHeader']) ){
                $this->setProxyHeader($options['proxyHeader']);
            }
    }

    public function proccess($type = false, $services = true){
        $data                                               = $this->getData(true);
        $ret                                                = $data;

        $this->store();
        switch ($type) {
            case 'json':
                $ret                                        = $this->getJSON();
                break;
            case 'result':
                $ret                                        = $this->getResult();
                break;
            case 'nice':
                $ret                                        = $this->getNiceData();
                break;
        }

        return $ret;
    }

    public function fetch($services = true, $filters = null){
        if( !$services ){
            return $this;
        }

        $list                                               = array();
        $data                                               = array();
        if( $services === true ){
            $list                                           = $this->services;
        }else if( is_array($services) ){
            foreach ($services as $key => $value){
                if( $value === true ){
                    if( isset($this->services[$key]) ){
                        $list[$key]                         = $this->services[$key];
                    }
                }
                //TODO custom settings
            }
        }else if( is_string($services) && isset($this->services[$services]) ){
            $list[$services]                                = $this->services[$services];
        }

        if( count($list) ){
            //Walk throught all services to push 
            foreach($list as $name => $service){
                if( !$service['service']->canFetch($this, $service) ){
                    unset($list[$name]);
                }
            }

            foreach($list as $key => $service){
                $data[$key]                                 = $service['service']->fetch($this, $service, $filters);
            }
        }
        
        return $data;
    }

    public function store($services = true){
        if( !$services ){
            return $this;
        }

        $list                                               = array();
        if( $services === true ){
            $list                                           = $this->services;
        }else if( is_array($services) ){
            foreach ($services as $key => $value){
                if( $value === true ){
                    if( isset($this->services[$key]) ){
                        $list[$key]                         = $this->services[$key];
                    }
                }
                //TODO custom settings
            }
        }else if( is_string($services) && isset($this->services[$services]) ){
            $list[$services]                                = $this->services[$services];
        }

        if( count($list) ){
            $data                                           = $this->getNiceData();

            //Walk throught all services to push 
            foreach($list as $name => $service){
                if( !$service['service']->check($this, $service, $data) ){
                    unset($list[$name]);
                }
            }

            foreach($list as $key => $service){
                $this->serviceData[$key]                    = $service['service']->send($this, $service, $data);
            }
        }
        
        return $this;
    }

    public function getResult(){
        $ret                                                = false;
        if( $this->isValid() ){
            $ret                                            = $this->getContentBlockResult('success');
        }else{
            $ret                                            = array(
                'errorData'                                     => $this->getContentBlockResult('error')
            );

            //Back to step with error
            if( !is_null($this->step) ){
                $ret['errorData']['step']                   = $this->step;
            }
        }
        return $ret;
    }

    public function getContentBlockResult($name){
        if( !isset($this->contentBlocks[$name]) ){
            return null;
        }
        $template                                           = false;
        $data                                               = $this->getNiceData();
        if( $this->contentBlocks[$name]['templateFile'] ){
            $tmp                                            = formsPlusBasicCore::buildFileTemplate($this->contentBlocks[$name]['templateFile'], $data);
            if( !is_null($tmp) ){
                $template                                   = $tmp;
            }
        }
        if( !$template ){
            $template                                       = $this->contentBlocks[$name]['template']
                ? $this->contentBlocks[$name]['template']
                : ( isset($this->msgTemplates[$name]) ? $this->msgTemplates[$name] : false )
            ;
            if( $template ){
                $template                                   = formsPlusBasicCore::buildTemplate($template, $data);
            }
        }

        return array(
            'block'                                         => $this->contentBlocks[$name]['block'],
            'content'                                       => $template ? $template : null,
            'showBlocks'                                    => $this->contentBlocks[$name]['showBlocks'] ? $this->contentBlocks[$name]['showBlocks'] : null,
            'hideBlocks'                                    => $this->contentBlocks[$name]['hideBlocks'] ? $this->contentBlocks[$name]['hideBlocks'] : null,
            'clearBlocks'                                   => $this->contentBlocks[$name]['clearBlocks'] ? $this->contentBlocks[$name]['clearBlocks'] : null,
            'fieldValues'                                   => $this->contentBlocks[$name]['fieldValues'] ? $this->contentBlocks[$name]['fieldValues'] : null,
        );
    }

    public function getNiceData(){
        $fields                                             = array();
        foreach ($this->fields as $name => $field) {
            if( $field['dataType']->isIgnored($field, $this->data[$name]) ){
                continue;
            }
            $fields[$name]                                  = array(
                'title'                                         => $field['dataType']->getTitle(
                    $field,
                    $this->data[$name]
                ),
                'raw'                                           => $this->data[$name],
                'value'                                         => $field['dataType']->valueToString(
                    $this->data[$name],
                    $field
                ),
                'storeValue'                                    => $field['dataType']->getStoreValue(
                    $this->data[$name],
                    $field
                )
            );
        }

        $ip                                                 = $this->getIP();

        return array(
            'fields'                                        => $fields,
            'messages'                                      => &$this->messages,
            'serviceData'                                   => &$this->serviceData,
            'ip'                                            => array(
                'raw'                                           => $ip,
                'value'                                         => $ip ? $ip : '-'
            )
        );
    }

    public function getJSON(){
        return json_encode( $this->getResult() );
    }

    public function setServiceType($name, $serviceType){
        if( is_string($serviceType) && class_exists($serviceType) ){
            $serviceType                                    = new $serviceType();
        }

        if( !interface_exists('formsPlusServiceInterface') ){
            $this->addMessage('warning', 'interfaceNotFound', 'formsPlusServiceInterface', array(
                'interface'                                 => "formsPlusServiceInterface"
            ));
        }else if( !($serviceType instanceof formsPlusServiceInterface) ){
            $this->addMessage('warning', 'notImplements', 'formsPlusServiceType' . ucfirst( strtolower($name) ) , array(
                'interface'                                 => 'formsPlusServiceInterface',
                'class'                                     => gettype($this->dataProxy) == 'object' ? get_class($this->dataProxy) : ( is_string($this->dataProxy) ? $this->dataProxy : gettype($this->dataProxy) )
            ));
        }else{
            $msgTemplates                                   = $serviceType::getMsgTemplates();
            if( $msgTemplates && count($msgTemplates) ){
                foreach ($msgTemplates as $key => $msgTemplate) {
                    if( !isset($this->msgTemplates[$key]) ){
                        $this->msgTemplates[$key]           = $msgTemplate;
                    }
                }
            }
            $this->serviceTypes[strtolower($name)]          = $serviceType;
        }
        return $this;
    }

    public function setDataType($name, $dataType){
        if( is_string($dataType) && class_exists($dataType) ){
            $dataType                                       = new $dataType();
        }

        if( !interface_exists('formsPlusDataTypeInterface') ){
            $this->addMessage('warning', 'interfaceNotFound', 'formsPlusDataTypeInterface', array(
                'interface'                                 => "formsPlusDataTypeInterface"
            ));
        }else if( !($dataType instanceof formsPlusDataTypeInterface) ){
            $this->addMessage('warning', 'notImplements', 'formsPlusDataType' . ucfirst( strtolower($name) ) , array(
                'interface'                                 => 'formsPlusDataTypeInterface',
                'class'                                     => gettype($this->dataProxy) == 'object' ? get_class($this->dataProxy) : ( is_string($this->dataProxy) ? $this->dataProxy : gettype($this->dataProxy) )
            ));
        }else{
            $msgTemplates                                   = $dataType::getMsgTemplates();
            if( $msgTemplates && count($msgTemplates) ){
                foreach ($msgTemplates as $key => $msgTemplate) {
                    if( !isset($this->msgTemplates[$key]) ){
                        $this->msgTemplates[$key]           = $msgTemplate;
                    }
                }
            }
            $this->dataTypes[strtolower($name)]             = $dataType;
        }
        return $this;
    }

    public function setContentBlock($name, $contentBlock){
        if( !is_array($contentBlock) ){
            $contentBlock                                   = array(
                'block'                                         => $contentBlock
            );
        }

        if( !(isset($contentBlock['block']) && is_string($contentBlock['block']) ) ){
            $this->addMessage('warning', 'failedSetContentBlock', '__push', array(
                'name'                                      => $name
            ));
            return $this;
        }

        $this->contentBlocks[$name]                         = array(
            'block'                                             => $contentBlock['block'],
            'templateFile'                                      => isset($contentBlock['templateFile']) && is_string($contentBlock['templateFile']) ? $contentBlock['templateFile'] : false,
            'template'                                          => isset($contentBlock['template']) && is_string($contentBlock['template']) ? $contentBlock['template'] : false,
            'showBlocks'                                        => isset($contentBlock['showBlocks']) && is_array($contentBlock['showBlocks']) ? $contentBlock['showBlocks'] : false,
            'hideBlocks'                                        => isset($contentBlock['hideBlocks']) && is_array($contentBlock['hideBlocks']) ? $contentBlock['hideBlocks'] : false,
            'clearBlocks'                                       => isset($contentBlock['clearBlocks']) && is_array($contentBlock['clearBlocks']) ? $contentBlock['clearBlocks'] : false,
            'fieldValues'                                       => isset($contentBlock['fieldValues']) && is_array($contentBlock['fieldValues']) ? $contentBlock['fieldValues'] : false,
        );
        return $this;
    }

    public function validate(){
        if( $this->state == 'hasData' ){
            $this->state                                    = 'valid';
            foreach ($this->fields as $name => $field) {
                $field['dataType']->validate(
                    $this,
                    $this->data[$name],
                    $field
                );
            }
            if( $this->isValid() ){
                foreach ($this->fields as $name => $field) {
                    if( isset($field['isFile']) && $field['isFile'] && $field['storeFile'] ){
                        $field['dataType']->store($this, $this->data[$name], $field);
                    }
                }
            }
        }
        return $this->isValid();
    }

    public function setService($name, $service){
        $failed                                             = true;
        if( isset($service['type']) && $this->serviceTypes[ strtolower($service['type']) ] ){
            $type                                           = strtolower($service['type']);
            $temp                                           = $this->serviceTypes[ $type ]->getProp($name, $service);
            if( $temp ){
                $temp['service']                            = $this->serviceTypes[ $type ];
                $this->services[$name]                      = $temp;
                $failed                                     = false;
            }
        }

        if( $failed ){
            $this->addError('failedSetService', '__push', array(
                'name'                                      => $name
            ));
        }
        
        return $this;
    }

    public function setField($name, $field){
        if( $this->state == 'nodata' ){
            if( !is_array($field) ){
                if( is_string($field) ){
                    $field                                  = array(
                        'title'                                 => $field
                    );
                }else{
                    $field                                  = array();
                }
            }
            $type                                           = isset($field['type']) && $this->dataTypes[ strtolower($field['type']) ]  ? strtolower($field['type']) : $this->defaultDataType;
            $temp                                           = $this->dataTypes[ $type ]->getFieldProp($name, $field);
            $temp['dataType']                               = $this->dataTypes[ $type ];
            $this->fields[$name]                            = $temp;
        }
        
        return $this;
    }

    public function getData($validate = false){
        if( !$this->hasData() ){
            $this->state                                    = 'hasData';
            foreach ($this->fields as $name => $field) {
                $this->data[$name]                          = $field['dataType']->parseValue(
                    $this,
                    isset($field['isFile']) && $field['isFile'] ? $this->getFile( $field['name'] ) : $this->dataProxy->get( $field['name'], null ),
                    $field
                );
            }
        }
        if( $validate ){
            $this->validate();
        }
        return $this->data;
    }

    public function setStep($step){
        if( is_integer($step) ){
            $this->step                                     = $step;
        }
        return $this;
    }

    public function getStep($step){
        return $this->step;
    }

    public function getProxyValue($name, $default = null){
        return $this->dataProxy->get($name, $default);
    }

    public static function isEmail($email){
        return !!(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$email));
    }

    /**
     * Get IP functionality
     */
    public function setUseProxy($useProxy = true)
    {
        $this->useProxy                                     = !!$useProxy;
        return $this;
    }
    public function getUseProxy()
    {
        return $this->useProxy;
    }

    public function setTrustedProxies($trustedProxies)
    {
        $this->trustedProxies = array();
        $this->addTrustedProxies($trustedProxies);
        return $this;
    }
    public function getTrustedProxies()
    {
        return $this->trustedProxies;
    }
    public function addTrustedProxies($trustedProxies)
    {
        if( is_array($trustedProxies) ){
            $this->trustedProxies                           = array_unique(array_merge($this->trustedProxies, array_map('trim', $trustedProxies)));
        }else if( is_string($trustedProxies) ){
            $trustedProxies                                 = trim($trustedProxies);
            if( !in_array($trustedProxies, $this->trustedProxies) ){
                array_push($this->trustedProxies, $trustedProxies);
            }
        }
        return $this;
    }

    public function setProxyHeader($header = 'X-Forwarded-For')
    {
        $this->proxyHeader                                  = $this->normalizeProxyHeader($header);
        return $this;
    }
    public function getProxyHeader()
    {
        return $this->proxyHeader;
    }
    protected function normalizeProxyHeader($header)
    {
        $header                                             = strtoupper($header);
        $header                                             = str_replace('-', '_', $header);
        if (0 !== strpos($header, 'HTTP_')) {
            $header                                         = 'HTTP_' . $header;
        }
        return $header;
    }

    public function getIP(){
        $ip = $this->getIpAddressFromProxy();
        if($ip){
            return $ip;
        }
        // direct IP address
        if(isset($_SERVER['REMOTE_ADDR'])){
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    protected function getIpAddressFromProxy()
    {
        if (!$this->useProxy
            || (isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], $this->trustedProxies))
        ) {
            return false;
        }

        $header                                             = $this->proxyHeader;
        if (!isset($_SERVER[$header]) || empty($_SERVER[$header])) {
            return false;
        }

        // Extract IPs
        $ips                                                = explode(',', $_SERVER[$header]);
        // trim, so we can compare against trusted proxies properly
        $ips                                                = array_map('trim', $ips);
        // remove trusted proxy IPs
        $ips                                                = array_diff($ips, $this->trustedProxies);

        // Any left?
        if (empty($ips)) {
            return false;
        }

        // Since we've removed any known, trusted proxy servers, the right-most
        // address represents the first IP we do not know about -- i.e., we do
        // not know if it is a proxy server, or a client. As such, we treat it
        // as the originating IP.
        // @see http://en.wikipedia.org/wiki/X-Forwarded-For
        return array_pop($ips);
    }
}