<?php

if (!defined('ABSPATH')) {
    exit;
}

final class MT_WC_Error
{
    public static $field_errors = array();

    public static function has_error($field): bool
    {
        return isset(self::$field_errors[$field]) && !empty(self::$field_errors[$field]);
    }

    public static function get_error($field)
    {
        if (!isset(self::$field_errors[$field])) {
            return '';
        }

        return self::$field_errors[$field];
    }
}