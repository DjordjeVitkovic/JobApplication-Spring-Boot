<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 *  serviceCharsetError     - failed to load charset, properties: {name} {msg}
 *
 */

require_once('formsPlusBaseService.php');

class formsPlusSimpleMySQLiService extends formsPlusBaseService
{
    public static $isEnabled                                = true;

    protected $connection;

    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //database name is not set, properties: {name}
                'databaseNameNotSet'                        => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Database is not specified (<strong>{name}</strong> service).</div>",
                //table name is not set, properties: {name}
                'tableNameNotSet'                           => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Database table is not specified (<strong>{name}</strong> service).</div>",
                //fields not set, properties: {name}
                'storeFieldsNotSet'                         => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Fields to store is not specified (<strong>{name}</strong> service).</div>",
                //failed to connect to something(database, etc.), properties: {name}, {msg}
                'serviceConnectionError'                    => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Failed to connect (<strong>{name}</strong> service).</div>",
                //failed to set charset, properties: {name} {msg}
                'serviceCharsetError'                       => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> Charset error (<strong>{name}</strong> service).</div>",
            )
        );
    }

    public function getServiceName(){
        return 'Simple MySQLi';
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
            'serverName'                                    => isset($service['serverName']) && is_string($service['serverName']) ? $service['serverName'] : 'localhost',
            'userName'                                      => isset($service['userName']) ? $service['userName'] : 'root',
            'userPassword'                                  => isset($service['userPassword']) ? $service['userPassword'] : '',
            'databaseName'                                  => isset($service['databaseName']) && is_string($service['databaseName']) ? $service['databaseName'] : null,
            'tableName'                                     => isset($service['tableName']) && is_string($service['tableName']) ? $service['tableName'] : null,
            'trackData'                                     => isset($service['trackData']) ? !!$service['trackData'] : false,
            'idColumName'                                   => isset($service['idColumName']) ? (is_string($service['idColumName']) ? $service['idColumName'] : false) : 'id',
            'charset'                                       => isset($service['charset']) && is_string($service['charset']) ? $service['charset'] : false,
            'storingType'                                   => isset($service['storingType']) && is_string($service['storingType']) ? $service['storingType'] : null,
            'fields'                                        => $fields
        ));
    }

    public function isAvailable($service){
        return formsPlusSimpleMySQLiService::$isEnabled;
    }

    public function canFetch($form, $service){
        return $this->checkConnection($form, $service);
    }

    protected function checkConnection($form, $service){
        if( is_null($service['databaseName']) || is_null($service['tableName']) || !count($service['fields']) ){
            if( !$this->canIgnore($service) ){
                if( is_null($service['databaseName']) ){
                    $this->addError($form, $service, 'databaseNameNotSet', array(
                        'name'                              => $service['name']
                    ));
                }
                if( is_null($service['tableName']) ){
                    $this->addError($form, $service, 'tableNameNotSet', array(
                        'name'                              => $service['name']
                    ));
                }
                if( !count($service['fields']) ){
                    $this->addError($form, $service, 'storeFieldsNotSet', array(
                        'name'                              => $service['name']
                    ));
                }
            }
            return false;
        }
        return true;
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

        if( !$this->checkConnection($form, $service) ){
            return false;
        }

        return true;
    }

    protected function closeConnection($form, $service){
        if( $this->connection instanceof mysqli ){
            $this->connection->close();
        }
        $this->connection                                   = null;
        return $this;
    }

    protected function openConnection($form, $service, &$ret){
        $this->closeConnection($form, $service);
        $this->connection                                   = new mysqli(
            $service['serverName'],
            $service['userName'],
            $service['userPassword'],
            $service['databaseName']
        );
        if($this->connection->connect_error){
            $this->addError($form, $service, 'serviceConnectionError', array(
                'name'                                      => $service['name'],
                'msg'                                       => $this->connection->connect_error
            ));
            $ret['error']                                   = $this->connection->connect_error;
            return $this;
        }else if($service['charset'] && !$this->connection->set_charset($service['charset'])) {
            $this->addError($form, $service, 'serviceCharsetError', array(
                'name'                                      => $service['name'],
                'msg'                                       => $this->connection->error
            ));
            $ret['error']                                   = $this->connection->error;
            $this->closeConnection($form, $service);
        }

        return $this;
    }

    protected function insertToTable(){

    }

    public function send($form, $service, $data){
        $ret                                                = array(
            'status'                                            => false
        );
        if( !$this->check($form, $service, $data) ){
            return $ret;
        }

        $this->openConnection($form, $service, $ret);
        if( isset($ret['error']) ){
            return $ret;
        }
        
        $insertData                                         = array();
        $trackData                                          = array();
        $trackColumns                                       = array();
        $where                                              = array();

        $sql                                                = '';

        switch ($service['storingType']) {
            case 'update' :
                foreach ($service['fields'] as $key => $field) {
                    $value                                          = formsPlusBaseService::smartFieldContent($field, $data, 'storeValue');

                    if( $field['required'] && !$value ){
                        $error                                      = $key['title'] . " is required.";
                        $this->addError($form, $service, 'serviceStoreError', array(
                            'name'                                  => $service['name'],
                            'msg'                                   => $error
                        ));
                        $this->closeConnection($form, $service);
                        return $ret;
                    }

                    $cName                                          = $this->connection->real_escape_string($field['name']);

                    if( $service['trackData'] && $field['trackData'] ){
                        $trackData[$key]                            = $value;
                        if( $service['idColumName'] ){
                            $trackColumns[$key]                     = $cName;
                        }
                    }
                    
                    if( is_string($value) ){
                        $value                                      = "'" . $this->connection->real_escape_string($value) . "'";
                    }
                    if( $field['checkOnUpdate'] ){
                        $where[]                                    = $cName . ' = ' . $value;
                    }
                    if( $field['canUpdate'] ){
                        $insertData[$cName]                         = $value;
                    }
                }
                $sql                                        = "UPDATE " .  $this->connection->real_escape_string($service['tableName']) . " SET ";
                $data                                       = array();
                foreach ($insertData as $key => $value) {
                    $data[]                                 = $key . ' = ' . $value;
                }
                if( count($data) ){
                    $sql                                    .= implode(', ', $data);
                }
                if( count($where) ){
                    $sql                                    .= " WHERE " . implode(' AND ', $where);
                }
                $sql                                        .= ';';
                break;
            default:
                foreach ($service['fields'] as $key => $field) {
                    if( !$field['canInsert'] ){
                        continue;
                    }
                    $value                                          = formsPlusBaseService::smartFieldContent($field, $data, 'storeValue');

                    if( $field['required'] && !$value ){
                        $error                                      = $key['title'] . " is required.";
                        $this->addError($form, $service, 'serviceStoreError', array(
                            'name'                                  => $service['name'],
                            'msg'                                   => $error
                        ));
                        $this->closeConnection($form, $service);
                        return $ret;
                    }

                    $cName                                          = $this->connection->real_escape_string($field['name']);

                    if( $service['trackData'] && $field['trackData'] ){
                        $trackData[$key]                            = $value;
                        if( $service['idColumName'] ){
                            $trackColumns[$key]                     = $cName;
                        }
                    }
                    
                    if( is_string($value) ){
                        $value                                      = "'" . $this->connection->real_escape_string($value) . "'";
                    }
                    $insertData[$cName]                             = $value;
                }
                $sql                                                = "
                    INSERT INTO " .  $this->connection->real_escape_string($service['tableName']) . " (" . implode(", ", array_keys($insertData)) . ")
                    VALUES (" . implode(", ", array_values($insertData)) . ");
                ";
                break;
        }

        if( $service['trackData'] ){
            $ret['trackData']                               = array(
                'raw'                                           => $trackData
            );
        }

        if( $this->connection->query($sql) === true && $this->connection->affected_rows ){
            $ret['status']                                  = true;

            $ret['id']                                      = $this->connection->insert_id;
            if( $service['trackData'] && $service['idColumName'] ){
                $sql                                        = "
                    SELECT " . implode(", ", $trackColumns) . "
                    FROM " .  $this->connection->real_escape_string($service['tableName']) . "
                    WHERE " .  $this->connection->real_escape_string($service['idColumName']) . " = " . $this->connection->insert_id . ";
                ";
                $result                                     = $this->connection->query($sql);
                if( $result->num_rows > 0 ){
                    $ret['trackData']['columns']            = $this->formatResult( $trackColumns, $result->fetch_assoc() );
                }else{
                    $ret['error']                           = "Failed to get row from database.";
                }
            }
        }else{
            $error                                          = $this->connection->error || 'Database store failed';
            $this->addError($form, $service, 'serviceStoreError', array(
                'name'                                      => $service['name'],
                'msg'                                       => $this->connection->error
            ));
            $ret['error']                                   = $this->connection->error;
        }

        $this->closeConnection($form, $service);
        return $ret;
    }

    public function formatResult($by, $result, $fields = null){
        $ret                                                = array();
        foreach ($by as $key => $column) {
            $value                                          = isset($result[$column]) ? $result[$column] : null;
            if( $fields && isset($fields[$key]) && isset($fields[$key]['type']) ){
                switch ($fields[$key]['type']) {
                    case 'date':
                        if( class_exists('formsPlusDateTimeDataType') ){
                            $value                          = array(
                                'raw'                           => $value,
                                'date'                          => formsPlusDateTimeDataType::getDateTimeObject($value, array(
                                    formsPlusDateTimeDataType::$DATE_TIME_FORMAT,
                                    formsPlusDateTimeDataType::$DATE_FORMAT,
                                    formsPlusDateTimeDataType::$TIME_FORMAT,
                                ))
                            );
                        }
                        break;
                }
            }
            $ret[$key]                                      = $value;
        }
        return $ret;
    }

    public function fetch($form, $service, $filters = null){
        $ret                                                = array(
            'count'                                             => 0,
            'results'                                           => null,
        );

        $this->openConnection($form, $service, $ret);
        if( isset($ret['error']) ){
            return $ret;
        }
        
        $cols                                               = array();
        $where                                              = array();
        if( is_array($filters) ){
            foreach ($filters as $key => $value) {
                if( !isset($service['fields'][$key]) ){
                    return $ret;
                }
                if( is_string($value) ){
                    $value                                  = "'" . $this->connection->real_escape_string($value) . "'";
                }
                $where[]                                    = $this->connection->real_escape_string($service['fields'][$key]['name']) . ' = ' . $value;
            }
        }
        foreach ($service['fields'] as $key => $field) {
            if( $field['canFetch'] ){
                $cols[$key]                                 = $this->connection->real_escape_string($field['name']);
            }
            if( isset($field['fetchWhere']) ){
                $value                                      = $field['fetchWhere'];
                if( is_string($value) ){
                    $value                                  = "'" . $this->connection->real_escape_string($value) . "'";
                }
                $where[]                                    = $this->connection->real_escape_string($field['name']) . ' = ' . $value;
            }
        }
        if( !count($cols[$key]) ){
            return $ret;
        }
        $sql                                                = "SELECT " . implode(', ', $cols) . " FROM ". $this->connection->real_escape_string($service['tableName']);

        if( count($where) ){
            $sql                                            .= " WHERE " . implode(' AND ', $where);
        }
        $sql                                                .= ";";

        $result                                             = $this->connection->query($sql);

        if( $result->num_rows > 0 ){
            $ret['count']                                   = $result->num_rows;
            $ret['results']                                 = array();
            for ($i=0; $i < $ret['count']; $i++) { 
                $ret['results'][]                           = $this->formatResult( $cols, $result->fetch_assoc(), $service['fields'] );
            }
        }

        $this->closeConnection($form, $service);
        return $ret;
    }
}