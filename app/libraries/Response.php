<?php

namespace App\Libraries;

use App\Libraries\Config;

/**
 * Response Class
 */
class Response
{
    // Ответ в формате JSON
    public static function json(array $data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

}