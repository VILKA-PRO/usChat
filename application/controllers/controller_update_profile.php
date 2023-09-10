<?php
ini_set('display_errors', 1);

class Controller_update_profile extends Controller
{

    function action_index()
    {

        // require_once '../../application/config.php';
        require_once CORE . "sql.php"; // Подключаем sql

        echo "<pre>SESSION <br>";
        print_r($_SESSION);
        echo "</pre>";

        echo "<pre>_POST <br>";
        print_r($_POST);
        echo "</pre>";

        echo "<pre>_FILES <br>";
        print_r($_FILES);
        echo "</pre>";

        if (!$_SESSION['auth']) {
            header('location:?url=main');
        }


        $user_id = $_SESSION['userID'];
        $message = '';

        if(isset($_POST['edit']))
        {
            $user_profile = $_POST['hidden_user_profile'];

            if($_FILES['user_profile']['name'] != '')
            {
                $user_profile = sqlQerries::upload_image($_FILES['user_profile']);

                $_SESSION['avatar_path'] = $user_profile;
            }

            $new_login = ($_POST['username'] == "") ? NULL : $_POST['username'];

            $new_avatar_path = $user_profile;
            $hide_email=0;

            if (isset($_POST['hide_email'])) {
                $hide_email = 1;
            } else {
                $hide_email = 0; 
            }

            if(sqlQerries::update_profile_data($user_id, $new_login, $new_avatar_path, $hide_email))
            {
                $_SESSION['verifyMessage'] = '<div class=" alert alert-success">Профиль обновлен</div>';
                unset($_POST);
                unset($_FILES);
                header('location:?url=profile');

            }else{
                $_SESSION['loginBusyMsg'] = '<div class=" alert alert-warning">Такой логин занят, придумайте другой</div>';
                unset($_POST);
                unset($_FILES);
                header('location:?url=profile');
            }
        }

    }
}
