<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/fontawesome.min.css">
  <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/regular.min.css">
  <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/solid.min.css">
  <link rel="stylesheet" href="public/css/theme.css">
  <title><?php echo $_view->meta('subtitle') ? $_view->meta('subtitle') . ' - ' . $_view->meta('title') : $_view->meta('title'); ?></title>
  <meta name="keywords" content="<?php echo $_view->meta('keywords'); ?>">
  <meta name="description" content="<?php echo $_view->meta('description'); ?>">
</head>
<body>
    <?php $_view->section('body'); ?>
    <?php $_view->section('scripts'); ?>
</body>
</html>