<?php

class Model_Profile extends Model
{


    public function get_profile_data()
    {
        require_once CORE . "sql.php"; // Подключаем sql
        $user_id = $_SESSION['userID'];
        $user_data = sqlQerries::get_user_data($user_id);
        return $user_data;
    }
}
