<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 * Uses https://github.com/google/google-api-php-client/tree/v1-master for work with Google API
 * Documentation can be found at https://developers.google.com/sheets/quickstart/php
 *
 */

require_once('formsPlusGoogleAPIService.php');

class formsPlusGoogleSheetsService extends formsPlusGoogleAPIService
{
    public static $isEnabled                                = true;

    protected $client;

    public function getServiceName(){
        return 'Google Sheets';
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
            'trackData'                                     => isset($service['trackData']) ? !!$service['trackData'] : false,
            'createHeaders'                                 => isset($service['createHeaders']) ? !!$service['createHeaders'] : true,
            'spreadsheetId'                                 => isset($service['spreadsheetId']) ? $service['spreadsheetId'] : null,
            'worksheetId'                                   => isset($service['worksheetId']) ? $service['worksheetId'] : null,
            'fields'                                        => $fields
        ));
    }

    public function isAvailable($service){
        return self::$isEnabled;
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
        if( $this->insertData($form, $service, $insertData, $headers) ){
            $ret['status']                                  = true;
        }
        return $ret;
    }

    protected function insertData($form, $service, $rows, $headers = false){
        if( !$this->setupConnection($form, $service) ){
            $this->addError($form, $service, 'serviceStoreError', array(
                'name'                                      => $service['name'],
                'msg'                                       => 'Failed to setup connection.'
            ));
            return false;
        }
        try {
            $response                                       = $this->service->spreadsheets_values->get($service['spreadsheetId'], $service['worksheetId'] . '!A:A');
            $values                                         = $response->getValues();
            $insertRowID                                    = $values ? count($values) + 1 : 1;
            if( $service['createHeaders'] && $insertRowID === 1 ){
                $this->updateRow($form, $service, $insertRowID, $headers);
                $insertRowID++;
            }
            $this->updateRow($form, $service, $insertRowID, $rows);
        } catch (Exception $e) {
            $this->addError($form, $service, 'serviceStoreError', array(
                'name'                                      => $service['name'],
                'msg'                                       => $e->getMessage()
            ));
            return false;
        }
        
        return true;
    }

    protected function updateRow($form, $service, $rowId, $data){
        if( !is_array($data) || !count($data) ){
            return $this;
        }
        $colTo                                              = '';
        $countCols                                          = count($data);
        $lastLetter                                         = $countCols%26;
        for ($i=1; $i < $countCols/26; $i++) {
            $colTo                                          .= 'A';
        }
        if( $lastLetter ){
            $colTo                                          .= chr(64 + $lastLetter);
        }
        $range                                              = $service['worksheetId'] . '!' . 'A' . $rowId . ':' . $colTo . $rowId;
        $valueRange                                         = new Google_Service_Sheets_ValueRange();
        $valueRange->setRange($range);
        $valueRange->setValues(array(array_values($data)));
        $this->service->spreadsheets_values->update($service['spreadsheetId'], $range, $valueRange, array( "valueInputOption"=>"RAW"));

        return $this;
    }

    protected function getScopes($form, $service){
        return array( Google_Service_Sheets::SPREADSHEETS );
    }

    protected function createService($form, $service){
        parent::createService($form, $service);
        $this->service                                      = new Google_Service_Sheets($this->client);
        return $this;
    }
}