<?php

// Достаем URI
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Если загружается файл из /public/, то пропускаем дальнейшие действия
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Реквайрим инициализацию приложения
require_once __DIR__.'/app/init.php';