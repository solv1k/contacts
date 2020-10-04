<?php

namespace App\Libraries;

use App\Libraries\Config;

/**
 * View Maker Class
 */
class ViewMaker
{
    public $path = '';
    public $meta = [];
    public $data = [];
    public $errors = [];
    public $old = [];

    private static $_view = null;
    private static $_sections = [];

    function __construct(string $path, array $meta = [], array $data = [], array $errors = [], array $old = [])
    {
        $this->path = $path;
        $this->meta = $meta;
        $this->data = $data;
        $this->errors = $errors;
        $this->old = $old;
    }

    public function meta(string $key = '', $default = null)
    {
        return $this->getValueOrArray($this->meta, $key, $default);
    }

    public function data(string $key = '', $default = null)
    {
        return $this->getValueOrArray($this->data, $key, $default);
    }

    public function errors(string $key = '', $default = null)
    {
        return $this->getValueOrArray($this->errors, $key, $default);
    }

    public function error(string $key = '', $default = null)
    {
        return $this->errors($key, $default);
    }

    public function old(string $key = '', $default = null)
    {
        return $this->getValueOrArray($this->old, $key, $default);
    }

    public function link(string $controller_with_method, array $data = [])
    {
        $controller_with_method = str_replace('/', '.', $controller_with_method);

        if (strpos($controller_with_method, '.') < 0) {
            die("Неверный путь к методу контроллера [$controller_with_method]");
        }

        $arr = explode('.', $controller_with_method);

        $controller_name = $arr[0];
        $controller_method = $arr[1];

        return Config::get('app_protocol') . '://' . Config::get('app_url') . '/?' . http_build_query(array_merge([
            'c' => $controller_name,
            'm' => $controller_method
        ], $data));
    }

    public function renderInLayout(string $path) 
    {
        $_view = $this;
        $view_filename_path = __DIR__ . '/../views/' . str_replace('.', '/', $path) . '.layout.php';

        if (!file_exists($view_filename_path)) {
          die("Не удалось найти файл для отображения [$view_path]");
        }

        include $view_filename_path;
    }

    public function setSectionView(string $section_name, string $view_path)
    {
        self::$_sections[$section_name] = $view_path;
    }

    public function section(string $section_name)
    {
        $_view = $this;
        $view_path = self::$_sections[$section_name] ?? 'empty';
        $view_filename_path = __DIR__ . '/../views/' . str_replace('.', '/', $view_path) . '.view.php';

        if (file_exists($view_filename_path)) {
          include $view_filename_path;
        }
    }

    // Рендеринг конечного шаблона
    // string $view_path - путь к файлу шаблона
    // array $data - массив с данными, которые будут переданы в шаблон
    public static function render(string $view_path, array $meta = [], array $data = [], array $errors = [], array $old = [], bool $return_as_html = false)
    {
        $_view = new self($view_path, $meta, $data, $errors, $old);

        $view_filename_path = __DIR__ . '/../views/' . str_replace('.', '/', $view_path) . '.view.php';

        if (!file_exists($view_filename_path)) {
          die("Не удалось найти файл для отображения [$view_path]");
        }

        if ($return_as_html) {
            
            ob_start();

            include $view_filename_path;
            
            $rendered_view_html = str_replace(["\r","\n"],'',trim(ob_get_clean()));

            return $rendered_view_html;
        }

        include $view_filename_path;
    }

    // Вспомогательная функция для получения элемента массива по ключу (или массива целиком)
    private function getValueOrArray(array $arr, string $key = '', $default = null)
    {
        if (!$key) {
          return $arr;
        }

        return $arr[$key] ?? $default;
    }
}