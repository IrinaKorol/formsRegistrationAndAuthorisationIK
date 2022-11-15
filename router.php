<?php session_start();
include_once('function.php');
//echo 'id сессии = '.session_id();

function index()
{
    if (isGuest()) { // если не авторизован
        return render('main');
    } else {
        return render('hello');
    }
}
function render($view)
{
    $view = trim($view, '/');
    include   'header.php';
    include   'inc/' . $view . '.php';
    include   'footer.php';
    return true;
}

function existSession()
{
    if (isset($_SESSION['auth']) && $_SESSION['auth']) {
        // сравниваем сессионный ключ и ключ в БД
        if (equalitySessionKey($_SESSION['login'], $_SESSION['sessionKey'])) {
            return true;
        }
        return true;
    }
    return false;
}

function isGuest()
{
    if (existSession() ) return false;   // если сеесия есть, то не гость
    //  если куки есть и он не пустой, то не гость
    if (isset($_COOKIE['login']) && $_COOKIE['login'] != '') {
        // если куки-ключ совпадает с ключем в БД
        if (equalityCookieKey($_COOKIE['login'], $_COOKIE['cookieKey'])) {
            // делаем новую сессию
            $numberUser = searchObjectNumberByLogin($_COOKIE['login']);
            createSession( $numberUser);
            return false;
        }
    }
    return true;
}

$errorsValidate = null;
if (isset($_POST['signUp'])) {
    // echo json_encode ($_POST['signUpForm']);
    // echo json_encode($errorsValidate);
        $resSignUpUser = signUpUser($_POST['signUpForm']);
    if ($resSignUpUser === true) {
        //  global $errorsValidate;
        echo json_encode([
            'success' => true,
            'message' => 'Регистрация прошла успешно. Авторизируйтесь',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => implode('<br/>', $resSignUpUser), // сливает массив в строку с указанным разделителем.
            '$errorsValidate' => $errorsValidate,

        ]);
    }
} elseif (isset($_POST['signIn'])) {

    $activUser = signInUser($_POST['signInForm']);
    if ($activUser === true) {
        // получаем номер пользователя в массиве
        $numberUser = searchObjectNumberByLogin($_POST['signInForm']['login']);
        // если чекбокс есть, то создаём куки, если нет то только сессию
        if (isset($_POST['check'])) {
            createCookie($numberUser);
        }
        // создаем сессию и пишем в базу
        $userNew = createSession($numberUser);
       header('Content-type: application/json');

        echo json_encode([
            'success' => true,
            'message' =>'добро пожаловать' ,
            'numberUser' =>  $numberUser,
            'useractive' =>  $userNew
        ]);
        exit;

    } else {
        echo json_encode([
            'success' => false,
            'message' => implode('<br/>',$errorsValidate),
        ]);
    }
} elseif (isset($_POST['logout'])) {
    $logoutUser = logout();
    if ($logoutUser === true) {
        echo json_encode([
            'success' => true
        ]);
    }
}







