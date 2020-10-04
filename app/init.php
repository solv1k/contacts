<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

include_once 'autoload.php';

use App\Libraries\Config;

$controller_name    = $_GET['c'] ?? Config::get('controller.default', 'auth');
$controller_method  = $_GET['m'] ?? 'index';

function makeController(string $controller_name, string $controller_method)
{
    $controller_classname = ucfirst(strtolower($controller_name)) . 'Controller';
    $controller_class = "\App\Controllers\\$controller_classname";

    if (!class_exists($controller_class)) {
      die("Controller [$controller_classname] not found!");
    }

    $controller = new $controller_class;

    if (!method_exists($controller, $controller_method)) {
      die("Method [$controller_method] has no exists in [$controller_classname]");
    }

    return $controller->$controller_method();
}

if ($controller_name && $controller_method) {
  return makeController($controller_name, $controller_method);
}