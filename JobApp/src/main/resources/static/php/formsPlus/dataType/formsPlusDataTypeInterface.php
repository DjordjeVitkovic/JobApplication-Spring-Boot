<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

interface formsPlusDataTypeInterface
{
    public static function getMsgTemplates();
    public function getFieldProp($name, $field);
    public function parseValue($form, $value, $field);
    public function validate($form, $value, $field);
    public function getTitle($field, $value = false);
    public function valueToString($value, $field);
    public function isIgnored($field, $value = false);
    public function getStoreValue($value, $field);
}