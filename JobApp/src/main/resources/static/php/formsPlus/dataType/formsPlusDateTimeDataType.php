<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 * Uses DateTime class http://php.net/manual/en/class.datetime.php
 *
 */

class formsPlusDateTimeDataType extends formsPlusTextDataType
{
    public static $DATE_FORMAT                              = 'Y-m-d';
    public static $TIME_FORMAT                              = 'H:i:s';
    public static $DATE_TIME_FORMAT                         = 'Y-m-d H:i:s';

    public static function getMsgTemplates(){
        return array_merge( parent::getMsgTemplates(),
            array(
                //date-time is less than 'minTime', properties: {name}, {time}
                'fieldDateTimeMinError'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> -  should not be less than {time}.</div>",
                //date-time is greater than 'maxTime', properties: {name}, {time}
                'fieldDateTimeMinError'                     => "<div class=\"alert alert-error\"><strong><i class=\"fa fa-times\"></i> Error:</strong> <strong>{name}</strong> -  should not be greater than {time}.</div>",
            )
        );
    }

    public function getFieldProp($name, $field){
        $params                                             = parent::getFieldProp($name, $field);
        //format http://php.net/manual/en/datetime.createfromformat.php, default 'd.m.Y h:i a' - 26.05.2016 12:00 am
        $params['format']                                   = isset($field['format']) && is_string($field['format']) ? $field['format'] : 'd.m.Y h:i a';
        $params['storeFormat']                              = isset($field['storeFormat']) && is_string($field['storeFormat']) ? $field['storeFormat'] : 'Y-m-d H:i:s';
        $parseFormats                                       = isset($field['parseFormats']) ? array($field['parseFormats']) : array();
        $parseFormats[]                                     = $params['format'];
        $parseFormats[]                                     = $params['storeFormat'];

        $params['parseFormats']                             = formsPlusDateTimeDataType::walkFormats($parseFormats);

        $params['minTime']                                  = isset($field['minTime']) ? formsPlusDateTimeDataType::getDateTimeObject($field['minTime'], $params['parseFormats']) : null;
        $params['maxTime']                                  = isset($field['maxTime']) ? formsPlusDateTimeDataType::getDateTimeObject($field['maxTime'], $params['parseFormats']) : null;

        return $params;
    }

    public static function walkFormats($formats){
        $ret                                                = array();
        $formats                                            = is_array($formats) ? $formats : array($formats);
        foreach ($formats as $value) {
            if( is_string($value) ){
                $tmp                                        = trim($value);
                if( $tmp ){
                    $ret[]                                  = $tmp;
                }
            }else if( is_array($value) ){
                $tmp                                        = formsPlusDateTimeDataType::walkFormats($value);
                $ret                                        = array_merge($ret, $tmp);
            }
        }
        return $ret;
    }

    public static function getDateTimeObject($value, $formats){
        if( $value instanceof DateTime ){
            return $value;
        }
        $ret                                                = null;
        if( is_string($value) ){
            $formats                                        = formsPlusDateTimeDataType::walkFormats($formats);
            foreach ($formats as $format) {
                $tmp                                        = DateTime::createFromFormat($format, $value);
                if( $tmp instanceof DateTime ){
                    $ret                                    = $tmp;
                    break;
                }
            }
        }else if( is_int($value) && $value > 0 ){
            $ret                                            = new DateTime();
            $ret->setTimestamp($value);
        }
        
        return $ret;
    }

    public function parseValue($form, $value, $field){
        if( is_null($value) && !is_null($field['default']) ){
            $value                                          = $field['default'];
        }
        if( is_string($value) && trim($value) ){
            $value                                          = formsPlusDateTimeDataType::getDateTimeObject($value, $field['parseFormats']);
        }else{
            $value                                          = false;
        }
        return $value ? $value : null;
    }

    public function checkRequired($value, $field){
        return parent::checkRequired($value, $field) || ($value && !($value instanceof DateTime));
    }

    public function validate($form, $value, $field){
        if( $this->checkRequired($value, $field) ){
            $this->addError( $form, $field, 'fieldRequired', array(
                'name'                                      => $field['title']
            ));
        }else if($value){
            if( ($field['minTime'] instanceof DateTime) && $value < $field['minTime'] ){
                $this->addError( $form, $field, 'fieldDateTimeMinError', array(
                    'name'                                      => $field['title'],
                    'time'                                      => $field['minTime']->format($field['format'])
                ));
            }

            if( ($field['maxTime'] instanceof DateTime) && $value > $field['maxTime']  ){
                $this->addError( $form, $field, 'fieldDateTimeMaxError', array(
                    'name'                                      => $field['title'],
                    'time'                                      => $field['maxTime']->format($field['format'])
                ));
            }
        }
    }

    public function valueToString($value, $field){
        return $value && ($value instanceof DateTime) ? $value->format($field['format']) : '-';
    }

    public function getStoreValue($value, $field){
        return $value && ($value instanceof DateTime) ? $value->format($field['storeFormat']) : null;
    }
}