<?php

namespace App\Libraries;

use App\Libraries\Config;

/**
 * Meta Class
 */
class Meta
{
    // Слияние данных из конфига и новых мета-значений
    public static function mergeWith(array $new_meta_values)
    {
        return array_merge(Config::get('meta', []), $new_meta_values);
    }
}