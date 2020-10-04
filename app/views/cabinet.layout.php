<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $_view->meta('subtitle') ? $_view->meta('subtitle') . ' - ' . $_view->meta('title') : $_view->meta('title'); ?></title>
  <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/fontawesome.min.css">
  <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/regular.min.css">
  <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/solid.min.css">
  <link rel="stylesheet" href="public/css/theme.css">
  <link rel="stylesheet" href="public/css/cabinet.css?v=1">
  <script src="vendor/components/jquery/jquery.min.js"></script>
  <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <?php $_view->section('menu'); ?>
      </div>
      <div class="card-body">
        <div class="card-text"><?php $_view->section('body'); ?></div>
      </div>
    </div>
  </div>
  <?php $_view->section('scripts'); ?>
</body>
</html>