<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Libraries\Auth;
use App\Libraries\Response;
use App\Libraries\Validator;
use App\Libraries\Config;

/**
 * Cabinet Controller
 */
class CabinetController extends Controller
{
    // Текущий пользователь сессии
    private $auth_user;

    // Конструктор
    function __construct()
    {
        // Получаем текущего пользователя
        $this->auth_user = Auth::user();

        // Если сессия или пользователь не найдены, делаем редирект на страницу входа
        if (!$this->auth_user) {
            return $this->redirect(301)->to('auth', 'index');
        }
    }

    // Контроллер главной страницы личного кабинета
    public function index()
    {
        return $this->view('cabinet.index', ['user' => $this->auth_user], ['subtitle' => 'Контакты']);
    }

    // Контроллер для AJAX-запросов
    public function api()
    {
        // Собираем данные
        $action = $this->data('action');
        $params = json_decode($this->data('params'), true);
        $labels = json_decode($this->data('labels'), true);

        // Определяем действие
        switch ($action) {

            // Добавление нового контакта
            case 'contact.add':
                // Валидируем данные для модели
                $validator = Validator::check($params, [
                    'contactName' => 'required|alphaNumSpaces|minLength:2|maxLength:100',
                    'contactPhone' => 'required|phone',
                ], $labels);

                // Если есть ошибки, возвращаем
                if ($validator->hasErrors()) {
                    return Response::json(['error' => ['validation' => $validator->errors()]]);
                }

                // Валидируем загруженный файл (если имеется)
                $photo = $this->files('contactPhoto');
                if ($photo) {
                    // Максимальный размер 2 Мб
                    if ($photo['size'] > 2097152) {
                        return Response::json(['error' => ['validation' => ['contactPhoto' => 'filesize']]]);
                    }
                    // Только JPG и PNG
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    if (false === $ext = array_search($finfo->file($photo['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png'], true)) {
                        return Response::json(['error' => ['validation' => ['contactPhoto' => 'filetype']]]);
                    }
                    // Пробуем загрузить файл или возвращаем ошибку
                    $photo_filename = md5($photo['tmp_name']) . '.' . $ext;
                    if (!move_uploaded_file($photo['tmp_name'], Config::get('dir_uploads').$photo_filename)) {
                        return Response::json(['error' => ['upload' => ['contactPhoto' => $photo_filename]]]);
                    }
                    // Здесь можно добавить обрезку картинки или ещё какие-либо манипуляции,
                    // Но так как это тестовое задание, пока не буду усложнять
                    // :)
                }

                // Если все ок, создаем новый контакт и сохраняем в БД
                $contact = new Contact();
                $contact->user_id = $this->auth_user->id;
                $contact->phone = $params['contactPhone'];
                $contact->name = $params['contactName'];
                $contact->photo = $photo_filename ?? null;
                $contact->save();

                // Возвращаем информацию об успешном завершении
                return Response::json(['success' => 'Создан новый контакт: ' . $contact->name . ' [' . $contact->phone . ']']);
            break;


            // Просмотр контакта (модальное окно)
            case 'contact.view':
                $contact_id = $this->data('id');
                $contact = Contact::where([
                    'user_id' => $this->auth_user->id,
                    'id' => (int)$contact_id
                ])->first();

                if ($contact) {
                    return Response::json(['html' => $this->view('cabinet.single-contact-info', [
                        'user' => $this->auth_user,
                        'contact' => $contact
                    ], [], true)]);
                } else {
                    return Response::json(['error' => 'Контакт не найден или не принадлежит текущему пользователю.']);
                }
            break;


            // Форма редактирования контакта (модальное окно)
            case 'contact.edit':
                $contact_id = $this->data('id');
                $contact = Contact::where([
                    'user_id' => $this->auth_user->id,
                    'id' => (int)$contact_id
                ])->first();

                if (!$contact) {
                    return Response::json(['error' => 'Контакт не найден или не принадлежит текущему пользователю.']);
                }

                return Response::json(['html' => $this->view('cabinet.single-contact-edit', [
                    'user' => $this->auth_user,
                    'contact' => $contact
                ], [], true)]);
            break;

            // Редактирование контакта (подтвержденное пользователем)
            case 'contact.edit.accept':
                $contact_id = $params['contactEditId'];
                $contact = Contact::where([
                    'user_id' => $this->auth_user->id,
                    'id' => (int)$contact_id
                ])->first();

                if (!$contact) {
                    return Response::json(['error' => 'Контакт не найден или не принадлежит текущему пользователю.']);
                }

                // Валидируем данные для модели
                $validator = Validator::check($params, [
                    'contactEditName' => 'required|alphaNumSpaces|minLength:2|maxLength:100',
                    'contactEditPhone' => 'required|phone',
                ], $labels);

                // Если есть ошибки, возвращаем
                if ($validator->hasErrors()) {
                    return Response::json(['error' => ['validation' => $validator->errors()]]);
                }

                // Валидируем загруженный файл (если имеется)
                $photo = $this->files('contactEditPhoto');
                if ($photo) {
                    // Максимальный размер 2 Мб
                    if ($photo['size'] > 2097152) {
                        return Response::json(['error' => ['validation' => ['contactEditPhoto' => 'filesize']]]);
                    }
                    // Только JPG и PNG
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    if (false === $ext = array_search($finfo->file($photo['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png'], true)) {
                        return Response::json(['error' => ['validation' => ['contactEditPhoto' => 'filetype']]]);
                    }
                    // Пробуем загрузить файл или возвращаем ошибку
                    $photo_filename = md5($photo['tmp_name']) . '.' . $ext;
                    if (!move_uploaded_file($photo['tmp_name'], Config::get('dir_uploads').$photo_filename)) {
                        return Response::json(['error' => ['upload' => ['contactEditPhoto' => $photo_filename]]]);
                    }
                    // Здесь можно добавить обрезку картинки или ещё какие-либо манипуляции,
                    // Но так как это тестовое задание, пока не буду усложнять
                    // :)
                }

                $contact->phone = $params['contactEditPhone'];
                $contact->name = $params['contactEditName'];
                $contact->photo = $photo_filename ?? $contact->photo;
                $contact->save();

                // Возвращаем информацию об успешном завершении
                return Response::json(['success' => 'Контакт успешно изменён!']);
            break;

            // Удаление контакта (модальное окно)
            case 'contact.remove':
                $contact_id = $this->data('id');
                $contact = Contact::where([
                    'user_id' => $this->auth_user->id,
                    'id' => (int)$contact_id
                ])->first();

                if ($contact) {
                    return Response::json(['html' => $this->view('cabinet.single-contact-remove', [
                        'user' => $this->auth_user,
                        'contact' => $contact
                    ], [], true)]);
                } else {
                    return Response::json(['error' => 'Контакт не найден или не принадлежит текущему пользователю.']);
                }
            break;

            // Удаление контакта (подтвержденное пользователем)
            case 'contact.remove.accept':
                $contact_id = $this->data('id');
                $contact = Contact::where([
                    'user_id' => $this->auth_user->id,
                    'id' => (int)$contact_id
                ])->first();

                if ($contact) {
                    $contact->delete();
                    return Response::json(['status' => 'success']);
                }
            break;


            // Список контактов для обновления отображения
            case 'contact.list':
                $user = $this->auth_user;
                $sort = $this->data('sort');
                $contacts = $user->contacts()->customSort($sort)->get();
                return Response::json(['html' => $this->view('cabinet.contacts-list', compact('user', 'contacts', 'sort'), [], true)]);
            break;


            default:
                // Если действие не найдено, то показываем ошибку
                return Response::json(['error' => ['wrong' => 'action']]);
            break;
        }
    }
}