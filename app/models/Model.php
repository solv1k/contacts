<?php

namespace App\Models;

use App\Libraries\DB;
use App\Libraries\Validator;

/**
 * Model - класс дефолтной модели
 */
class Model
{
  // Имя таблицы в БД (если отсутвует, то множественное число имени класса модели)
  // Можно переопределить в классе конкретной модели
  protected   $table;

  // Имя столбца ID
  // Можно переопределить в классе конкретной модели
  protected   $id_field_name = 'id';

  // Правила валидации модели, перед сохранением в БД
  // Можно переопределить в классе конкретной модели
  protected   $validation_rules = [];

  // Флаг наличия текущей модели в БД, по умолчанию - false
  // Если false, то метод save() добавит новую строку в БД
  private     $_HAS_IN_DB = false;

  // Условие для запроса к БД
  private     $_WHERE_CONDITION = [];

  // Сортировка для запроса к БД
  private     $_ORDER_CONDITION = [];


  // Конструктор дефолтной модели
  function __construct()
  {
      // Привязываем имя таблицы в БД к модели
      if (!$this->table) {
          $this->table = strtolower((new \ReflectionClass($this))->getShortName()) . 's';
      }
  }

  // Метод для получения имени таблицы в БД, привязанной к текущей модели
  public function getTableName()
  {
      return $this->table;
  }

  // Метод для установки флага наличия текущей модели в БД
  public function setHasInDB(bool $has_in_db)
  {
      $this->_HAS_IN_DB = $has_in_db;
  }

  // Метод для загрузки данных конкретной модели из БД по ID
  public static function findById(int $id)
  {
      // Создаем модель
      $model_classname = get_called_class();
      $model = new $model_classname;

      // Ищем данные в БД
      $db = DB::getInstance();
      $model_data = $db->getModelById($model->table, $id, $model->id_field_name);

      // Если данные модели не найдены в БД, возвращаем false
      if (!$model_data) {
          return false;
      }

      // Обновляем поля модели
      foreach ($model_data as $key => $value) {
        $model->$key = $value;
      }

      $model->setHasInDB(true);

      // Возвращаем модель с данными
      return $model;
  }

  // Метод гибкого поиска по ID и по условию
  public static function find($expression)
  {
      if (is_integer($expression)) {
          return self::findById((int)$expression);
      }

      if (is_array($expression)) {
          return self::where($expression)->get();
      }
  }

  // Метод установки условия для запроса
  public static function where(array $where_condition)
  {
      // Создаем модель
      $model_classname = get_called_class();
      $model = new $model_classname;

      // Устанавливаем условия запроса
      $model->_WHERE_CONDITION = array_merge($model->_WHERE_CONDITION, $where_condition);

      // Возвращаем модель с установленными условиями
      return $model;
  }

  // Метод добавления условия для запроса
  public function addWhere(array $where_condition)
  {
      // Устанавливаем условия запроса
      $this->_WHERE_CONDITION = array_merge($this->_WHERE_CONDITION, $where_condition);

      // Возвращаем модель с установленными условиями
      return $this;
  }

  // Метод добавления сортировки для запроса
  public function addOrder(array $order_condition)
  {
      // Устанавливаем условия запроса
      $this->_ORDER_CONDITION = array_merge($this->_ORDER_CONDITION, $order_condition);

      // Возвращаем модель с установленной сортировкой
      return $this;
  }

  // Метод создания новой модели с заполнением полей из массива
  public static function new(array $model_data)
  {
      // Создаем модель
      $model_classname = get_called_class();
      $model = new $model_classname;

      // Заполняем поля
      foreach ($model_data as $key => $value) {
        $model->$key = $value;
      }

      // Возвращаем модель с установленными условиями
      return $model;
  }

  // Метод извлечения из БД данных о выбранных моделях текущего класса
  // $fields - массив полей, по умолчанию * - все поля
  public function get(array $fields = ['*'])
  {
      // Загружаем данные из БД
      $db = DB::getInstance();
      // Получаем данные о найденных моделях
      $models_data = $db->getModels($this->table, $fields, $this->_WHERE_CONDITION, $this->_ORDER_CONDITION);

      // Записываем в массив
      $models = [];
      foreach ($models_data as $md) {
        $model = self::new($md);
        $model->setHasInDB(true);
        array_push($models, $model);
      }

      // Возвращаем массив с моделями
      return count($models) ? $models : false;
  }

  // Метод извлечения из БД данных о  первой выбранной модели текущего класса
  // $fields - массив полей, по умолчанию * - все поля
  public function first(array $fields = ['*'])
  {
      $all = $this->get();

      return $all[0] ?? false;
  }

  // Получение всех данных модели в виде массива
  public function toArray()
  {
      return $this->getClearModelProperties();
  }

  // Метод извлечения из БД данных о всех моделях текущего класса
  // $fields - массив полей, по умолчанию * - все поля
  public static function all(array $fields = ['*'])
  {
      // Создаем модель
      $model_classname = get_called_class();
      $model = new $model_classname;

      // Возвращаем данные о найденных моделях
      return $model->get($fields);
  }

  private function getClearModelProperties()
  {
      // Получаем все поля модели
      $fields = get_object_vars($this);

      // Ансетим вспомогательные поля
      unset($fields['table']);
      unset($fields['id_field_name']);
      unset($fields['validation_rules']);
      unset($fields['_HAS_IN_DB']);
      unset($fields['_WHERE_CONDITION']);
      unset($fields['_ORDER_CONDITION']);
      unset($fields['_VALIDATION_ERRORS']);

      return $fields;
  }

  public function save()
  {
      // Получаем все поля модели
      $fields = $this->getClearModelProperties();

      // Загружаем БД
      $db = DB::getInstance();

      if (!$this->_HAS_IN_DB) {
          // Если нет в БД, добавлям новые данные 
          $result = $db->insertModel($this->table, $fields);
      } else {
          // Если есть в БД, обновляем данные 
          $id_field_name = $this->id_field_name;
          unset($fields[$id_field_name]);
          $result = $db->updateModel($this->table, $this->$id_field_name, $id_field_name, $fields);
      }

      return $result;
  }

  public function delete()
  {
      // Загружаем БД
      $db = DB::getInstance();

      // Удаляем данные из БД
      $id_field_name = $this->id_field_name;
      $result = $db->deleteModel($this->table, $this->$id_field_name, $id_field_name);

      return $result;
  }

  // Валидация модели через $validation_rules
  public static function validate(array $data)
  {
      // Создаем модель
      $model_classname = get_called_class();
      $model = new $model_classname;

      // Возвращаем результат валидации
      return Validator::isValid($data, $model->validation_rules);
  }
}