<?php

namespace App\Controllers;

use App\Models\User;
use App\Libraries\Auth;
use App\Libraries\Validator;

/**
 * Auth Controller
 */
class AuthController extends Controller
{
    // Форма регистрации (авторизации)
    public function index()
    {
        return $this->login();
    }

    // Регистрация нового пользователя
    public function register()
    {
        // Если POST-запрос
        if ($this->dataPost()) { 

            session_start();

            // Сначала сверим капчи
            if ($this->data('captcha') !== $_SESSION['captcha']) {
                $this->error('captcha', 'Неверная каптча');
                return $this->view('auth.register');
            }

            // Валидируем данные для модели
            $validator = Validator::check($this->data(), [
                'email' => 'required|email',
                'password' => 'required|alphaNum|minLength:8|maxLength:100',
            ]);

            // Если есть ошибки валидации, возвращаем форму регистрации и сообщение об ошибке
            if ($validator->hasErrors()) {
                $this->setErrors($validator->errors());
                return $this->view('auth.register');
            }

            // Дополнительно проверим, чтобы пользователя с похожим e-mail не было в БД
            $email_already_has = User::where(['email' => $this->data('email')])->first();
            if ($email_already_has) {
                $this->error('email_already_has', 'Пользователь с указанным e-mail уже существует');
                return $this->view('auth.register');
            }            

            // Cохраняем пользователя в БД
            $user = new User();
            $user->email = $this->data('email');
            $user->password = md5($this->data('password'));
            $user->save();

            // Делаем редирект на страницу успешной регистрации
            return $this->redirect(301)->with(['reg' => 'success'])->to('auth', 'index');

        }

        return $this->view('auth.register');
    }

    // Авторизация пользователя
    public function login()
    {
        // Если пользователь уже авторизован, делаем редирект на главную страницу личного кабинета
        if (Auth::user()) {
            return $this->redirect(301)->to('cabinet', 'index');
        }

        // Если POST-запрос
        if ($this->dataPost()) { 

            // Ищем юзера по данным из формы авторизации
            $user = User::where([
                'email' => $this->data('email'),
                'password' => md5($this->data('password')),
            ])->first();

            // Если не нашли, редиректим с ошибкой входа
            if (!$user) {
              return $this->redirect(301)->with(['err' => 'login'])->to('auth', 'index');
            }

            // Стартуем сессию
            Auth::setUser($user);

            // Делаем редирект на главную страницу личного кабинета
            return $this->redirect(301)->to('cabinet', 'index');
        }

        return $this->view('auth.login', $this->data());
    }

    // Выход из кабинета
    public function logout()
    {
        // Закрываем сессию пользователя
        Auth::logoutUser();

        // Делаем редирект на главную страницу входа
        return $this->redirect(301)->to('auth', 'index');
    }

    // Генератор капчи (ответ в виде изображения)
    public function captcha()
    {
        session_start();

        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $char = chr(rand(97, 122));
            $upper = rand(0, 1);
            if ($upper) {
                $char = strtoupper($char);
            }
            $code .= $char;
        }

        $_SESSION['captcha'] = $code;

        $image = imagecreatetruecolor(170, 60);
        $black = imagecolorallocate($image, 0, 0, 0);
        $color = imagecolorallocate($image, 200, 130, 90); // red
        $white = imagecolorallocate($image, 255, 255, 255);
         
        imagefilledrectangle($image, 0, 0, 399, 99,$white);
        imagettftext ($image, 33, 0, 30, 40, $color, __DIR__ . '/../resources/fonts/arial.ttf', $_SESSION['captcha']);
         
        header('Content-type: image/png');
        imagepng($image);
    }
}