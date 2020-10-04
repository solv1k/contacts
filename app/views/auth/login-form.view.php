<div class="form-auth-container">
  <div class="text-center">
    <form class="form-auth form-login" method="POST">
      <div class="logo">Ct</div>
      <h1 class="h3 mb-3 font-weight-normal">Вход в кабинет</h1>
      <?php if ($_view->data('reg') == 'success') { ?>
        <div class="alert alert-success">Успешная регистрация!</div>
      <?php } ?>
      <?php if ($_view->data('err') == 'login') { ?>
        <div class="alert alert-danger">Ошибка входа!</div>
      <?php } ?>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email" required="" autofocus="">
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required="">
      <div class="mt-3">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
        <a class="btn btn-lg btn-secondary btn-block" href="<?php echo $_view->link('auth.register'); ?>">Регистрация</a>
      </div>
      <p class="mt-5 mb-3 text-muted"><?php echo $_view->meta('title') ?> © 2020</p>
    </form>
  </div>
</div>