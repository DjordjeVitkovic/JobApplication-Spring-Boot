<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

class formsPlusPostData implements formsPlusDataProxyInterface{
    public function get($name, $default = null){
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }
}