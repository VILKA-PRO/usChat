<?php
session_start();
require_once '../core/sql.php';


$curentUser = $_POST['login'] ?? null;
$curentPass = $_POST['password'] ?? null;
$authTime = $_SESSION['authTime'] ?? time(); // Записали в сессию текущее время

if (password_verify($curentPass, $userPass)) {

    // Пишем в сессию об авторизации и логин и др пользователя
    $_SESSION['auth'] = true;
    $_SESSION['login'] = $curentUser;
    $_SESSION['authTime'] = $authTime;
    $_SESSION['count'] = 0;
} else {

    $_SESSION['msg'] = true;
    header('Location: login.php');
}

$auth = $_SESSION['auth'] ?? null;

// если авторизованы
if ($auth) {
    header('Location: ../../index.php');
}


// Так я получил хеши паролей и записал их в БД
// $hash_123 = password_hash("123", PASSWORD_BCRYPT);
// echo $hash_123;
// $hash_321 = password_hash("321", PASSWORD_BCRYPT);
// $hash_111 = password_hash("111", PASSWORD_BCRYPT);

// var_dump($hash_123);
// var_dump($hash_321);
// var_dump($hash_111);
