<?php
session_start();
require_once "sql.php";

 if ($_SESSION['auth']) {

    $userInfo = array(
        'id' => $_SESSION['userID'], // ID пользователя
        'username' => $_SESSION['login'] ?? $_SESSION['email'], // Имя пользователя
        'userEmail' => $_SESSION['email'],
        // Другие данные, которые вы хотите вернуть
    );
    
    // Отправляем информацию о пользователе в виде JSON
    header('Content-Type: application/json');
    return json_encode($userInfo);


 }