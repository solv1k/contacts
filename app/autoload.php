<?php

function includeEngineFirst()
{
    include_once __DIR__ . '/controllers/Controller.php';
    include_once __DIR__ . '/models/Model.php';
}

function includeAll(array $folders) {
  foreach ($folders as $folder) {
    foreach (glob(__DIR__ . "/$folder/*.php") as $filename)
    {
        include_once $filename;
    }
  }
}

includeEngineFirst();
includeAll(['libraries', 'controllers', 'models']);