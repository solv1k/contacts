<?php

namespace App\Libraries;

/**
 * Config Class
 */
class Config
{
    public static function get(string $key, $default = null)
    {
        // Проверяем файл конфига
        if ( !file_exists(__DIR__ . '/../config.php') ) {
           die('File config.php does not exists!');
        }

        // Загружаем данные из конфига в переменную (массивом)
        $config = include __DIR__ . '/../config.php';

        // Если вложенный ключ, то парсим
        if (strpos($key, '.')) {
            $key = explode('.', $key);
        }

        if (is_array($key)) {
            $max_depth = count($key)-1;
            $value = self::getInside($config[$key[0]] ?? $default, 0, $max_depth, $key);
        } else {
            $value = $config[$key] ?? $default;
        }

        // Возвращаем значение конфига
        return $value;
    }

    // Рекурсивная функция для проникания внутрь ключей конфига
    private static function getInside($config_key, int $depth, int $max_depth, array $keys)
    {
        if (is_array($config_key) && $depth < $max_depth) {
          $depth++;
          return self::getInside($config_key[$keys[$depth]], $depth, $max_depth, $keys);
        }

        return $config_key;
    }
}