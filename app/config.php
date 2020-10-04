<?php

return [

  'app_key'       => 'b64:wl24o{lr;4$21)gwq',
  'app_url'       => 'test.loc/contacts',
  'app_protocol'  => 'http',

  'dir_uploads'   => __DIR__ . '/../public/uploads/',

  'DB' => [
    'host'      => 'localhost',
    'database'  => 'contacts',
    'user'      => 'root',
    'password'  => 'root',
  ],

  'meta' => [
    'subtitle'      => 'Home',
    'title'         => 'Телефонная книга',
    'keywords'      => 'phone, phonebook, телефонная, книга',
    'description'   => 'Сервис Contacts позволяет вести личную телефонную книгу онлайн. Мы поможем сохранить ваши контакты в облаке и предоставим удобный доступ с любого устройства!',
  ],

  'controller' => [
    'default' => 'Auth',
  ]

];