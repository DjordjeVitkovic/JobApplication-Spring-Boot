<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 * Uses https://github.com/google/google-api-php-client/tree/v1-master for work with Google API
 *
 */

require_once('formsPlusBaseService.php');

class formsPlusGoogleAPIService extends formsPlusBaseService
{
    protected $client;
    protected $service;

    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //authFile is not set, properties: {name}
                'googleAPIAuthorizationFileNotSet'          => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Authorization File is not specified (<strong>{name}</strong> service).</div>",
                //keyFile is not set, properties: {name}
                'googleAPIKeyFileNotSet'                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API key File is not specified (<strong>{name}</strong> service).</div>",
                //appName is not set, properties: {name}
                'googleAPIAppNameNotSet'                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Application Name is not specified (<strong>{name}</strong> service).</div>",
                //credentialsFile is not set, properties: {name}
                'googleAPICredentialsFileNotSet'            => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Credentials File Path is not specified (<strong>{name}</strong> service).</div>",
                //clientId is not set, properties: {name}
                'googleAPIClientIdNotSet'                   => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API OAuth Client Id is not specified (<strong>{name}</strong> service).</div>",
                //serviceEmail is not set, properties: {name}
                'googleAPIServiceEmailNotSet'               => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Service Email is not specified (<strong>{name}</strong> service).</div>",
                //failed to get credentials is not set, properties: {name}, {msg}
                'googleAPICredentialsError'                 => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API failed to get credentials (<strong>{name}</strong> service) - {msg}.</div>",
                //autorization required, properties: {name}, {url}, {path}
                'googleAPICredentialsRequired'              => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Google API Authorization required (<strong>{name}</strong> service).<br/>Please open this <a href=\"{url}\" target=\"_blank\"><strong>link</strong></a> in your browser, follow authorization and save code at <strong>{path}</strong></div>",
            )
        );
    }

    public function getProp($name, $service){
        return array_merge(parent::getProp($name, $service), array(
            'appName'                                       => isset($service['appName']) ? $service['appName'] : null,
            'authType'                                      => isset($service['authType']) ? $service['authType'] : null,
            'clientId'                                      => isset($service['clientId']) ? $service['clientId'] : null,
            'serviceEmail'                                  => isset($service['serviceEmail']) ? $service['serviceEmail'] : null,
            'authFile'                                      => isset($service['authFile']) ? $service['authFile'] : null,
            'keyFile'                                       => isset($service['keyFile']) ? $service['keyFile'] : null,
            'credentialsFile'                               => isset($service['credentialsFile']) ? $service['credentialsFile'] : __DIR__ . '/.credentials/credentials.json',
        ));
    }

    public function check($form, $service, $data){
        if( !$this->isAvailable($service) ){
            $this->addError($form, $service, 'serviceNotAvailable', array(
                'name'                                      => $service['name']
            ));
            return false;
        }

        if( !$this->checkConnection($form, $service) ){
            return false;
        }

        return true;
    }

    protected function checkConnection($form, $service){
        $ret                                        = true;
        if( is_null($service['appName']) ){
            $this->addError($form, $service, 'googleAPIAppNameNotSet', array(
                'name'                              => $service['name']
            ));
            $ret                                    = false;
        }
        if( $ret ){
            $this->createClient($form, $service);
            if( !$this->getCredentials($form, $service) ){
                $ret                                = false;
            }
        }
        
        return $ret;
    }

    protected function checkOAuth($form, $service){
        $ret                                        = true;
        if( is_null($service['authFile']) || !file_exists($service['authFile']) ){
            $this->addError($form, $service, 'googleAPIAuthorizationFileNotSet', array(
                'name'                              => $service['name']
            ));
            $ret                                    = false;
        }
        if( is_null($service['credentialsFile']) ){
            $this->addError($form, $service, 'googleAPICredentialsFileNotSet', array(
                'name'                              => $service['name']
            ));
            $ret                                    = false;
        }
        return $ret;
    }

    protected function checkService($form, $service){
        $ret                                        = $this->checkOAuth($form, $service);
        if( is_null($service['keyFile']) ){
            $this->addError($form, $service, 'googleAPIKeyFileNotSet', array(
                'name'                              => $service['name']
            ));
            $ret                                    = false;
        }
        if( is_null($service['clientId']) ){
            $this->addError($form, $service, 'googleAPIClientIdNotSet', array(
                'name'                              => $service['name']
            ));
            $ret                                    = false;
        }
        if( is_null($service['serviceEmail']) ){
            $this->addError($form, $service, 'googleAPIServiceEmailNotSet', array(
                'name'                              => $service['name']
            ));
            $ret                                    = false;
        }
        return $ret;
    }

    protected function getCredentials($form, $service){
        $ret                                        = false;
        switch ($service['authType']) {
            case 'service':
                $ret                                = $this->getCredentialsService($form, $service);
                break;
            default:
                $ret                                = $this->getCredentialsOAuth($form, $service);
                break;
        }
        return $ret;
    }

    protected function getCredentialsService($form, $service){
        if( $this->checkOAuth($form, $service) ){
            try {
                $this->client->setAuthConfigFile($service['authFile']);
                $this->client->setClientId($service['clientId']);
                if (file_exists($service['credentialsFile'])) {
                    $this->client->setAccessToken($service['credentialsFile']);
                }
                $cred                                   = new Google_Auth_AssertionCredentials(
                    $service['serviceEmail'], 
                    $this->getScopes($form, $service),
                    file_get_contents($service['keyFile']),
                    'notasecret'
                );
                $this->client->setAssertionCredentials($cred);
                if ($this->client->getAuth()->isAccessTokenExpired()) {
                    $this->client->getAuth()->refreshTokenWithAssertion($cred);
                    file_put_contents($service['credentialsFile'], $this->client->getAccessToken());
                }
                return true;
            } catch (Exception $e) {
                $this->addError($form, $service, 'googleAPICredentialsError', array(
                    'name'                              => $service['name'],
                    'msg'                               => $e->getMessage()
                ));
            }
        }
        return false;
    }

    protected function getCredentialsOAuth($form, $service){
        if( $this->checkOAuth($form, $service) ){
            try {
                $this->client->setAuthConfigFile($service['authFile']);
                if (file_exists($service['credentialsFile'])) {
                    $accessToken = file_get_contents($service['credentialsFile']);
                    if( !json_decode($accessToken) ){
                        $accessToken = $this->client->authenticate(trim($accessToken));
                        file_put_contents($service['credentialsFile'], $accessToken);
                    }
                } else {
                    // Request authorization from the user.
                    $authUrl = $this->client->createAuthUrl();
                    $this->addError($form, $service, 'googleAPICredentialsRequired', array(
                        'name'                          => $service['name'],
                        'url'                           => $authUrl,
                        'path'                          => $service['credentialsFile']
                    ));
                    return false;
                }
                $this->client->setAccessToken($accessToken);

                // Refresh the token if it's expired.
                if ($this->client->isAccessTokenExpired()) {
                    $this->client->refreshToken($this->client->getRefreshToken());
                    file_put_contents($service['credentialsFile'], $this->client->getAccessToken());
                }
                return true;
            } catch (Exception $e) {
                $this->addError($form, $service, 'googleAPICredentialsError', array(
                    'name'                              => $service['name'],
                    'msg'                               => $e->getMessage()
                ));
            }
        }
        return false;
    }

    protected function getScopes($form, $service){
        return null;
    }

    protected function createClient($form, $service){
        $this->clearClient($form, $service);
        $this->client                                       = new Google_Client();
        $this->client->setApplicationName($service['appName']);
        $this->client->setScopes( implode(' ', $this->getScopes($form, $service)) );
        $this->client->setAccessType('offline');
        return $this;
    }

    protected function clearClient($form, $service){
        if( $this->service ){
            $this->clearService($form, $service);
        }
        $this->client                                       = null;
        return $this;
    }

    protected function createService($form, $service){
        if( !$this->client ){
            $this->createClient($form, $service);
        }
        return $this;
    }

    protected function clearService($form, $service){
        $this->service                                      = null;
        return $this;
    }

    protected function setupConnection($form, $service){
        $this
            ->clearClient($form, $service)
            ->clearService($form, $service)
            ->createClient($form, $service)
        ;
        if( $this->getCredentials($form, $service) ){
            $this->createService($form, $service);
            return true;
        };
        return false;
    }

    protected function closeConnection($form, $service){
        $this
            ->clearService($form, $service)
            ->clearClient($form, $service)
        ;
        return $this;
    }
}