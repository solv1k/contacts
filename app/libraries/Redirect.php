<?php

namespace App\Libraries;

use App\Libraries\Config;

/**
 * Redirect Class
 */
class Redirect
{
    // Код редиректа
    private $code;

    // Контроллер для возврата
    private $back_controller_name;

    // Метод контроллера для возврата
    private $back_controller_method;

    // Массив дополнительных GET-параметров, с которыми следует сделать редирект
    // По умолчанию NULL
    private $with_params = null;



    function __construct(int $code = 301, string $back_controller_name = '', string $back_controller_method = '')
    {
        $this->code = $code;
    }

    // Установка массива дополнительных GET-параметров
    public function with(array $with_params)
    {
        $this->with_params = $with_params;

        return $this;
    }

    // Путь направления редиректа
    public function to(string $controller_name, string $controller_method = 'index')
    {
        $with_params = $this->with_params ? '&' . http_build_query($this->with_params) : '';

        // Прописываем заголовок
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header(
          'Location: ' . Config::get('app_protocol') . '://' . Config::get('app_url') . '/?c=' . $controller_name . '&m=' . $controller_method . $with_params, 
          true, 
          $this->code
        );

        // Прерываем все скрипты и редиректим
        exit;
    }

    public function back()
    {
        return $this->to($this->back_controller_method, $this->back_controller_name);
    }
}