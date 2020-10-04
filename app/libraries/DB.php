<?php

namespace App\Libraries;

use PDO;
use PDOStatement;
use App\Libraries\Config;

/**
 * Класс для работы с базой данных
 */
class DB
{
  // Инстанс для реализации техники Singleton
  private static $_instance = null;

  // Переменная где будем хранить объект PDO
  private $_pdo = null;

  // Конструктор
  private function __construct() {
      // Загружаем конфиг базы
      $config_db = Config::get('DB');

      // Создаем инстанс БД (PDO)
      $this->_pdo = new PDO(
        'mysql:host=' . ($config_db['host'] ?? '') . ';dbname=' . ($config_db['database'] ?? ''),
        $config_db['user'] ?? '',
        $config_db['password'] ?? '',
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
      );
  }

  // Метод для получения (или создания) инстанса класса
  public static function getInstance()
  {
    if (!isset(self::$_instance)) {
      self::$_instance = new static();
    }

    return self::$_instance;
  }

  // Прямой запрос к БД (прокидываем напрямую к PDO)
  public function query(string $query) 
  {
      return call_user_func_array([&$this->$_pdo, 'query'], func_get_args());
  }

  // Получение данных модели по ID
  public function getModelById(string $model_table, int $id, string $id_field_name = 'id')
  {
      $st = $this->_pdo->prepare("SELECT * FROM $model_table WHERE `$id_field_name` = $id");

      if (!$st->execute()) {
          $this->dieWithErrorArray($st->errorInfo());
      }

      $model_data = $st->fetch(PDO::FETCH_ASSOC);

      return $model_data;
  }

  // Вставка новой модели
  public function insertModel(string $model_table, array $fields)
  {
      $keys = implode(',', array_keys($fields));
      $values = [];
      $prepared_values = [];
      foreach ($fields as $key => $value) {
        $values[] = ':' . $key;
        $prepared_values[':' . $key] = $value;
      }
      $values = implode(',', $values);

      $st = $this->_pdo->prepare("INSERT INTO $model_table ($keys) VALUES ($values)");

      if (!$st->execute($prepared_values)) {
          $this->dieWithErrorArray($st);
      }
  }

  // Обновление данных модели
  public function updateModel(string $model_table, int $id, string $id_field_name = 'id', array $fields)
  {
      $prepared_update = [];
      $prepared_values = [];
      foreach ($fields as $key => $value) {
        $prepared_update[$key] = $key . '=:' . $key;
        $prepared_values[':' . $key] = $value;
      }
      $prepared_update = implode(',', $prepared_update);

      $st = $this->_pdo->prepare("UPDATE $model_table SET $prepared_update WHERE $id_field_name = $id");

      if (!$st->execute($prepared_values)) {
          $this->dieWithErrorArray($st);
      }
  }

  // Удаление данных модели
  public function deleteModel(string $model_table, int $id, string $id_field_name = 'id')
  {
      $st = $this->_pdo->prepare("DELETE FROM $model_table WHERE $id_field_name = $id");

      if (!$st->execute()) {
          $this->dieWithErrorArray($st);
      }
  }

  // Получение данных о моделях с условием
  public function getModels(string $model_table, array $fields = ['*'], $where_condition = [], $order_condition = [])
  {
      $fields = implode(',', $fields);

      $prepared_where = '';
      $prepared_order = '';

      if (is_array($where_condition) && count($where_condition)) {
          $prepared_where = [];
          $prepared_values = [];
          foreach ($where_condition as $key => $value) {
            $prepared_where[$key] = $key . '=:' . $key;
            $prepared_values[':' . $key] = $value;
          }
          $prepared_where = 'WHERE ' . implode(' AND ', $prepared_where);
      }

      if (is_array($order_condition) && count($order_condition)) {
          $prepared_order = [];
          foreach ($order_condition as $key => $value) {
            $prepared_order[$key] = $key . ' ' . $value;
          }
          $prepared_order = 'ORDER BY ' . implode(',', $prepared_order);
      }

      $st = $this->_pdo->prepare("SELECT $fields FROM $model_table $prepared_where $prepared_order");
      $st_exec_result = !empty($prepared_where) ? $st->execute($prepared_values) : $st->execute();

      if (!$st_exec_result) {
          $this->dieWithErrorArray($st->errorInfo());
      }

      $models_data = $st->fetchAll(PDO::FETCH_ASSOC);

      return $models_data;
  }

  // Прерывание скрипта с ошибкой
  private function dieWithErrorArray(PDOStatement $st)
  {
      die('PDO Error: ' . implode(', ', $st->errorInfo()));
  }
}