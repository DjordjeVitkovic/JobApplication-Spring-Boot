<?php
/*
 * This file is part of the Forms Plus.
 * http://codecanyon.net/item/forms-plus-js-forms-framework/13202975
 *
 */

interface formsPlusServiceInterface
{
    public static function getMsgTemplates();
    public function getProp($name, $service);
    public function isAvailable($service);
    public function canFetch($form, $service);
    public function fetch($form, $service, $filters = null);
    public function send($form, $service, $data);
}