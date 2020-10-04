<?php

namespace App\Controllers;

use App\Libraries\ViewMaker;
use App\Libraries\Meta;
use App\Libraries\Redirect;
use App\Libraries\Config;

/**
 * Default Controller Class
 */
class Controller
{
  // Ошибки валидации, будут доступны в отображении (view)
  protected $errors = [];

  // Загрузка отображения (view)
  // string $path - путь к файлу шаблона
  // array $data - массив с данными, которые будут переданы в шаблон
  protected function view(string $path, array $data = [], array $meta = [], bool $return_as_html = false)
  {
      // Обновляем метаданные, если требуется
      $meta = Meta::mergeWith($meta);
      // Берём входящие данные до рендеринга
      $old_data = $_POST; 
      // Возвращаем рендер отображения (view)
      return ViewMaker::render($path, $meta, $data, $this->errors, $old_data, $return_as_html);
  }
    
  // Редирект из контроллера
  protected function redirect(int $code = 301)
  {
      $current_controller = $_GET['c'] ?? Config::get('controller.default', false);
      $current_method = $_GET['m'] ?? 'index';

      return new Redirect($code, $current_controller, $current_method);
  }

  // Получение GET-параметров + POST-параметров запроса
  protected function data(string $key = '', $default = null)
  {
      if (!$key) {
          return $_GET + $_POST;
      }

      return $_GET[$key] ?? $_POST[$key] ?? $default;
  }

  protected function files(string $key = '')
  {
      if (!$key) {
          return $_FILES['files'] ?? null;
      }

      if (isset($_FILES['files']['name'][$key])) {
        $file_data = [
          'name' => $_FILES['files']['name'][$key],
          'type' => $_FILES['files']['type'][$key],
          'tmp_name' => $_FILES['files']['tmp_name'][$key],
          'error' => $_FILES['files']['error'][$key],
          'size' => $_FILES['files']['size'][$key],
        ];
      }

      return $file_data ?? null;
  }

  // Получение GET-параметров запроса
  protected function dataGet(string $key = '', $default = null)
  {
      if (!$key) {
          return $_GET;
      }

      return $_GET[$key] ?? $default;
  }

  // Получение POST-параметров запроса
  protected function dataPost(string $key = '', $default = null)
  {
      if (!$key) {
          return $_POST;
      }

      return $_POST[$key] ?? $default;
  }

  // Метод для помещения ошибки в общий стэк, будут доступны в отображении (view)
  protected function error(string $key, $value = null)
  {
      $this->errors[$key] = [$value];
  }

  public function setErrors(array $errors)
  {
      $this->errors = $errors;
  }

}