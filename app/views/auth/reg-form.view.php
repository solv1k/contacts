<div class="form-auth-container">
  <div class="text-center">
    <form class="form-auth form-reg" method="POST">
      <div class="logo">Ct</div>
      <h1 class="h3 mb-3 font-weight-normal">Регистрация</h1>
      <?php if ($_view->errors()) { ?>
        <div class="errors alert alert-danger">
          <?php foreach ($_view->errors() as $error) { ?>
            <div class="error"><i class="fa fa-exclamation-triangle"></i> <?php echo $error[0]; ?></div>
          <?php } ?>
        </div>
      <?php } ?>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email" required="" autofocus="" value="<?php echo $_view->old('email'); ?>">
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required="">
      <img src="<?php echo $_view->link('auth.captcha', ['t' => time()]); ?>" alt="Каптча">
      <input type="text" id="inputCaptcha" name="captcha" class="form-control" placeholder="Введите каптчу" required="">
      <div class="mt-3">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Продолжить</button>
        <a class="btn btn-lg btn-secondary btn-block" href="<?php echo $_view->link('auth.login'); ?>">Войти</a>
      </div>
      <p class="mt-5 mb-3 text-muted"><?php echo $_view->meta('title') ?> © 2020</p>
    </form>
  </div>
</div>