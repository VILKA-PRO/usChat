<?php
session_start();

require_once "config.php" ;
require 'vendor/autoload.php'; // Путь к файлу autoload.php из Composer

Route::start(); // запускаем маршрутизатор
?>