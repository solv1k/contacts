<?php

namespace App\Libraries;

use App\Libraries\Config;
use App\Models\User;

/**
 * Auth Class
 */
class Auth
{
    public static function user()
    {
        $session = $_COOKIE['session'] ?? false;

        if (!$session || strpos($session, '_') < 0) {
          return false;
        }

        $session_arr = explode('_', $session);

        $session_id = $session_arr[0];
        $session_filename = $session_arr[1];

        if (md5($session_id . Config::get('app_key')) !== $session_filename) {
          return false;
        }

        $storage_session_filename = __DIR__ . '/../storage/sessions/' . $session_filename;

        if (!file_exists($storage_session_filename)) {
          return false;
        }

        $user_encoded_data = file_get_contents($storage_session_filename);
        $user_data = json_decode($user_encoded_data);

        return User::find((int)$user_data->id);
    }

    public static function setUserData(array $user_data)
    {
        $session_id = time() . ':::' . rand(1, 100000);
        $session_filename = md5($session_id . Config::get('app_key'));

        // Устанавливаем сессию в куки пользователя
        setcookie('session', $session_id . '_' . $session_filename);

        // Сохраняем сессию в файле
        file_put_contents(__DIR__ . '/../storage/sessions/' . $session_filename, json_encode($user_data));
    }

    public static function setUser(User $user)
    {
        self::setUserData($user->toArray());
    }

    public static function logoutUser()
    {
        $session = $_COOKIE['session'] ?? false;

        if ($session && strpos($session, '_') > 0) {
            $session_arr = explode('_', $session);

            $session_id = $session_arr[0];
            $session_filename = $session_arr[1];

            if (md5($session_id . Config::get('app_key')) == $session_filename) {

                $storage_session_filename = __DIR__ . '/../storage/sessions/' . $session_filename;

                if (file_exists($storage_session_filename)) {
                    unlink($storage_session_filename);
                }
            }
        }

        unset($_COOKIE['session']);
        setcookie('session', null, -1);
    }
}