<?php
ini_set('display_errors', 'On');

class Controller_regProcess extends Controller
{

    function action_index()
    {
        // Проверка паролей на совпадение
        if ($_POST['password'] !== $_POST['password2']) {
            $_SESSION['passNoCheck'] = true;
            header('Location: ?url=sineup');
            die();
        }
        
        require_once CORE . 'sql.php';

        $curentPass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $curentEmail = $_POST['email'];
        $curentToken = password_hash($_POST['email'], PASSWORD_BCRYPT);
        $authTime = $_SESSION['authTime'] ?? time(); // Записали в сессию текущее время
        new sqlQerries;
        sqlQerries::sineUp($curentPass, $curentEmail, $curentToken); // запускаем метод регистрации


        $auth = $_SESSION['auth'] ?? null;

        // если авторизованы
        if ($auth) {
            header('Location: ../../index.php');
        }

    }
}
