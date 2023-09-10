<?php
namespace dbConnect;
class dbConnect
{
    public static function dbConnect()
    {
        //Устанавливаем доступы к базе данных:

        $host = 'localhost'; //имя хоста, на локальном компьютере это localhost
        $userDB = 'root'; //имя пользователя, по умолчанию это root
        $password = 'root'; //пароль, по умолчанию пустой
        $db_name = '33-chat'; //имя базы данных

        //Соединяемся с базой данных используя наши доступы:
        $link = mysqli_connect($host, $userDB, $password, $db_name)
            or die(mysqli_error($link));
        mysqli_query($link, "SET NAMES 'utf8'");

        return $link;
    }
}
