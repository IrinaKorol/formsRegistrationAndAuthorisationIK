<?php
/*** Registration ***/
function signUpUser($signUpForm)
{ // регистрация пользователя
    global $errorsValidate;
    validateSignUp($signUpForm); // вызов функции валидации полей
    if ($errorsValidate !== null) { //если есть ошибки валидации
        return $errorsValidate;
    } else {  // если ошибок нет то добавляем поля в объект БД и сохраняем файл
        $xmlDbUsers = simplexml_load_file('users.xml');
        $newUser = $xmlDbUsers->addChild('user');
        $newUser->addChild('login', $signUpForm['login']);
        $newUser->addChild('email', $signUpForm['email']);
        $newUser->addChild('name', $signUpForm['name']);
        $newUser->addChild('salt', generateSalt()); //соль
        $newUser->addChild('password_hash', SaltyPassword($signUpForm['password'], $newUser->salt));
        // сохраняем нового юзера
        $xmlDbUsers->asXML('users.xml');
        return true;
    }
}

function validateSignUp($signUpForm)
{
    validateNotNull($signUpForm);  // проверка на длину и пустоту
    validateUniqueEmail($signUpForm['email']); // проверяем уникальность емайл
    validateUniqueLogin($signUpForm['login']);   // проверяем уникальность логина
    validatePassword($signUpForm['password'], $signUpForm['confirm_password']);   // сравниваем пароль и подтверждение
    validateEmaile($signUpForm['email']);  // проверяем формат email

}

function validateNotNull($fields)
{
    global $errorsValidate;
    $arrFields = [];
    $flag = false;
    foreach ($fields as $key => $value) {
        $value = trim($value); // удаляем пробелы с начала и конца строки
        if ($value === '') {
            $flag = true;
        } elseif (strlen($value) < 2) {
            $errorsValidate [] = 'Поле "' . $key . '" не должно быть короче 2-х символов';
        } elseif (strlen($value) > 20) {
            $errorsValidate[] = 'Поле "' . $key . '" не должно быть длинее 20 символов';
        }
        $arrFields[$key] = $value;
    }
    if ($flag) {
        $errorsValidate[] = 'Все поля обязательны для заполнения';
    }
    return $arrFields;
}

function validateUniqueEmail($email)
{
    global $errorsValidate;
    $user = searchByEmail($email);
    if ($user !== false) {
        $errorsValidate[] = 'Пользователь с таким email уже есть';
        return false;
    }
    return true;
}

function searchByEmail($email)
{
    $resultObj = false;
    $xmlDbUsers = simplexml_load_file('users.xml');
    foreach ($xmlDbUsers as $value) {
        if (trim($email) == trim($value->email)) {
            $resultObj = $value;
            break;
        }
    }
    return $resultObj;
}

function validatePassword($password, $confirm_password)
{
    global $errorsValidate;
    if ($password !== $confirm_password) {
        $errorsValidate['confirm'] = 'Пароль и подтверждение не совпадают';
        return false;
    }
    return true;
}

function validateUniqueLogin($login)
{
    global $errorsValidate;
    $user = searchByLogin($login);
    if ($user !== false) {
        $errorsValidate[] = 'Пользователь с логином "' . $login . '"" уже есть';
        return false;
    }
    return true;
}

function searchByLogin($login)
{
    $resultObj = false;
    $xmlDbUsers = simplexml_load_file('users.xml');
    foreach ($xmlDbUsers as $value) {
        if (htmlspecialchars_decode(trim($login)) == trim($value->login)) {
            $resultObj = $value;
            break;
        }
    }
    return $resultObj;
}

function validateEmaile($email)
{
    global $errorsValidate;
    if (preg_match('/.+@.+\..+/i', $email) == 0) {
        $errorsValidate[] = 'Данные поля " ' . $email . '" не соответствует формату email';
        return false;
    }
    return true;
}

function generateSalt() // генерация соли
{
    return substr(md5(mt_rand()), 0, 10); // генерирует случайное целое число, сгенерировали хеш для него, вырезали от начала 10 символов
}

function SaltyPassword($password, $salt)
{
    return md5($salt . md5($password));
}

/*** Authorization ***/
function signInUser($signInForm)
{
    validateSignIn($signInForm);
    // если есть ошибоки валидации, передаем массив ошибок
    global $errorsValidate;
    if ($errorsValidate !== null) {
        return $errorsValidate;
    } else {
        return true;
    }
}

function validateSignIn($signInForm)
{

    validateNotNull($signInForm); // проверка на длину и пустоту
    // ищем пользователя в базе и проверяем совпадение паролей
    $user = searchByLogin($signInForm['login']); // получили объект из базы
    global $errorsValidate;
    if ($user === false || !equalityPassword($user, $signInForm['password'])) { // если usera такого нет пароли не совпадают
        $errorsValidate[] = 'Ошибка в логине или пароле';
    }
}

function equalityPassword($user, $password)
{
    $saltyPassword = SaltyPassword($password, $user->salt);
    if ($saltyPassword == $user->password_hash) {
        return true;
    }
    return false;
}

function searchObjectNumberByLogin($login)
{
    $objectNumber = false;
    $users = (array)simplexml_load_file('users.xml'); // simplexml_load_file('inc/users.xml') - объект
    for ($i = 0; $i <= count($users['user']); $i++) {
        if ($login == (string)$users['user'][$i]->login) {
            $objectNumber = $i;
            break;
        }
    }
    return $objectNumber;
}

function createSession($number)
{
    // создаем сессию и пишем в базу
    $sessionKey = generateSalt(20);
    $users = addSessionKey($number, $sessionKey);

    //Пишем в сессию информацию о том, что мы авторизовались:
    $_SESSION['auth'] = true;
    $_SESSION['login'] = (string)$users->user[$number]->login;
    $_SESSION['name'] = (string)$users->user[$number]->name;
    $_SESSION['sessionKey'] = (string)$users->user[$number]->session_key;
    return $users->user[$number];
}

function addSessionKey($number, $sessionKey)
{
    $users = simplexml_load_file('users.xml');
    $users->user[$number]->session_key = $sessionKey;
    $users->asXML('users.xml');
    return $users;
}

function equalitySessionKey($login, $sessionKey)
{

    $user = searchByLogin($login);
    if ($user->session_key == $sessionKey) {
        return true;
    }
    return false;
}

function createCookie($number)
{
    define("MONTH", 60 * 60 * 24 * 30);
    // создаем куки и пишем в базу
    $cookieKey = generateSalt(20);
    $users = addCookieKey($number, $cookieKey);
    // создаём куки на месяц
    setcookie("login", (string)$users->user[$number]->login, time() + MONTH, '/');
    setcookie("cookieKey", $cookieKey, time() + MONTH, '/');
    header("Refresh:0");
}

function addCookieKey($number, $cookieKey)
{
    $users = simplexml_load_file('users.xml');
    $users->user[$number]->cookie_key = $cookieKey;
    $users->asXML('inc/users.xml');
    return $users;
}

function equalityCookieKey($login, $cookieKey)
{
    $user = searchByLogin($login);
    if ($user->cookie_key == $cookieKey) {
        return true;
    }
    return false;
}

/*** Logout***/
function logout()
{
    // сломаем сессию
    delete();

    // сломаем куки
    destroy();

    return true;

}

function delete()
{    // сломать сессию
    unset($_SESSION['auth']);
    unset($_SESSION['login']);
    unset($_SESSION['name']);
    unset($_SESSION['sessionKey']);
    session_destroy();
}

function destroy()
{
    // сломать куки
    setcookie("login", '', time(), '/');
    setcookie("cookieKey", '', time(), '/');
}

