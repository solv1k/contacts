<?php

namespace App\Libraries;

/**
 * Класс для валидации данных
 */
class Validator
{
  // Массив с ошибками
  private $validation_errors = [];

  /**
   * Метод пакетной валидации данных
   * @param  array   $data             входные данные для валидации
   * @param  array   $validation_rules правила валидации
   * @return boolean                   результат валидации
   */
  public static function isValid(array $data, array $validation_rules, array $labels = [], bool $return_validator_obj = false)
  {
      if (empty($validation_rules)) {
          return true;
      }

      $validator = new self;

      foreach ($validation_rules as $field_name => $rules_string) {

          $value = $data[$field_name] ?? null;
          $label = $labels[$field_name] ?? '';
          $rules = $validator->prepareRulesFromString($rules_string);
          $value_validated = $validator->doValidate($field_name, $value, $rules, $label);

          if (!$value_validated['is_valid']) {

            if (!$return_validator_obj) {
                return false;
            }

            $validator->validation_errors[$field_name] = $value_validated['errors'];
          }

      }

      if (!$return_validator_obj) {
        return true;
      }

      return $validator;
  }


  public static function check(array $data, array $validation_rules, array $labels = []) : Validator
  {
      return self::isValid($data, $validation_rules, $labels, true);
  }


  public function hasErrors() : bool
  {
      return count($this->validation_errors) > 0;
  }


  public function errors() : array
  {
      return $this->validation_errors;
  }

  private function prepareRulesFromString(string $rules_string) : array
  {
      $rules_arr = explode('|', $rules_string);
      $prepared_rules = [];

      foreach ($rules_arr as $rule) {
          
          if (strpos($rule, ':') > 0) {
              $rule_params = explode(':', $rule);
              $prepared_rules[$rule_params[0]] = $rule_params[1];
          } else {
              $prepared_rules[$rule] = $rule;
          }

      }

      return $prepared_rules;
  }


  private function doValidate(string $field_name, $value, array $rules, string $label = '') : array
  {
      $response = [
        'is_valid' => true,
        'errors' => []
      ];

      foreach ($rules as $rule_method_name => $params) {

          $rule_method_fullname = $rule_method_name . 'Validate';

          if (!method_exists($this, $rule_method_fullname)) {
              die("Отсутсвует метод валидации [$rule_method_fullname]");
          }

          if (!is_array($params)) {
              $params = [$params];
          }

          $params = array_merge([$value], $params);

          $value_validated = call_user_func_array([$this, $rule_method_fullname], $params);

          if (!$value_validated) {
            $response['is_valid'] = false;
            $response['errors'][] = (!empty($label) ? $label : $field_name) . ' ' . $rule_method_name;
          }
      }

      return $response;
  }

  private function requiredValidate($value = null) : bool
  {
      return !empty($value);
  }

  private function emailValidate($value) : bool
  {
      return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  private function alphaNumValidate($value) : bool
  {
      return preg_match('/^[a-zA-Z0-9]+$/', $value);
  }

  private function alphaNumSpacesValidate($value) : bool
  {
      return preg_match('/^[a-zA-Zа-яА-Я0-9\s]+$/', $value);
  }

  private function alphaSpacesValidate($value) : bool
  {
      return preg_match('/^[a-zA-Zа-яА-Я\s]+$/', $value);
  }

  private function minLengthValidate($value, int $min_length) : bool
  {
      return strlen($value) >= $min_length;
  }

  private function maxLengthValidate($value, int $max_length) : bool
  {
      return strlen($value) <= $max_length;
  }

  private function phoneValidate($value = null) : bool
  {
      return preg_match('/^[7-8]{1}[0-9]{10}$/', $value);
  }
}