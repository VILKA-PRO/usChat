<?php
require VENDOR . 'autoload.php'; // Путь к файлу autoload.php из Composer

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class sqlQerries
{
    public static function dbConnect()
    {
        //Устанавливаем доступы к базе данных:
        $host = '127.0.0.1:8889'; //имя хоста, на локальном компьютере это localhost
        $userDB = 'root'; //имя пользователя, по умолчанию это root
        $password = 'root'; //пароль, по умолчанию пустой
        $db_name = '33-chat'; //имя базы данных


        try {
            // Соединяемся с базой данных используя наши доступы:
            $link = mysqli_connect($host, $userDB, $password, $db_name);
            if (!$link) {
                $errorMessage = "Ошибка подключения к базе данных: " . mysqli_connect_error();
                throw new \Exception($errorMessage);
            }
            mysqli_query($link, "SET NAMES 'utf8'");

            return $link;
        } catch (\Exception $e) {
            // Запись ошибки в лог
            $log = new Logger('database');
            $log->pushHandler(new StreamHandler(ROOT . 'logs/db.log', Logger::ERROR));
            $log->error("Ошибка при подключении к базе данных: " . $e->getMessage());

            // Дальнейшие действия по обработке ошибки, если нужно
            return null;
        }
    }
    public static function sineUp($curentPass, $curentEmail, $curentToken)
    {

        $link = sqlQerries::dbConnect();

        $email = mysqli_query($link, "SELECT * FROM users WHERE email = '$curentEmail'") or die(mysqli_error($link));
        $email = mysqli_fetch_all($email);

        if ($email) {
            $_SESSION['emailExist'] = true;
            header('Location: ?url=sineup');
        }
        // $role = "vkuser";

        $createUser = "INSERT INTO `users` (`password`, `email`, `token`) VALUES ('$curentPass', '$curentEmail', '$curentToken')";
        $createUser = mysqli_query($link, $createUser) or die(mysqli_error($link));
        mysqli_close($link);
        // Пишем в сессию об авторизации и логин и др пользователя
        $_SESSION['auth'] = false;
        $_SESSION['authTime'] = time();
        $_SESSION['email'] = $curentEmail;
        $_SESSION['token'] = $curentToken;
        // Сгенерируйте уникальную ссылку подтверждения, например, используя GUID или случайную строку
        $confirmationLink = 'http://localhost:8888/33chat_r/index.php?url=verify&token=' . $curentToken;
        require_once CONTROLLER . "send_confirmation_email.php"; // Подключаем для email
        $sender = new emailSender;
        $sender->sendEmail($curentEmail, $confirmationLink);
        header('Location: ?url=confirm');
    }

    public static function login($curentLogin, $curentPass)
    {
        require_once CORE . "log.php"; // Подключаем логирование
        $link = self::dbConnect();

        $user = mysqli_query($link, "SELECT login, password, role, email, emailConfirm, id, token FROM users WHERE login = '$curentLogin' OR email = '$curentLogin'") or die(mysqli_error($link));
        $user = mysqli_fetch_all($user);
        $isUserConfirmed = $user[0][4];
        if ($user) {

            if (password_verify($curentPass, $user[0][1])) {
                if ($isUserConfirmed == 1) {
                    // Пишем в сессию об авторизации и логин и др пользователя
                    $_SESSION['auth'] = true;
                    $_SESSION['login'] = $user[0][0];
                    $_SESSION['authTime'] = time();
                    $_SESSION['email'] = $user[0][3];
                    $_SESSION['role'] = $user[0][2];
                    $_SESSION['userID'] = $user[0][5];
                    $_SESSION['token'] = $user[0][6];
                    mysqli_close($link);
                    header('Location: ?url=chat');
                } else {
                    mysqli_close($link);
                    $log->Debug("Юзер: __ $curentLogin __ не подтвердил email"); // Записываем в лог
                    $_SESSION['verifyMessage'] = 'ваш аккаунт не активирован';
                    header('Location: ?url=login');
                }
            } else {
                mysqli_close($link);
                $log->Debug("Юзер: __ $curentLogin __ ввел неверный пароль"); // Записываем в лог, если юзер не найден
                sqlQerries::loginError();
            }
        } else {

            mysqli_close($link);
            $log->Debug("Юзер: __ $curentLogin __ не найден"); // Записываем в лог, если юзер не найден
            sqlQerries::loginError();
        }
    }

    public static function loginError()
    {

        $_SESSION['loginError'] = true;
        header('Location: ?url=login');
    }


    public static function vkSql($curentLogin, $curentPass, $curentEmail, $curentToken)
    {
        $link = sqlQerries::dbConnect();

        $user = mysqli_query($link, "SELECT login, role FROM users WHERE login = '$curentLogin'") or die(mysqli_error($link));
        $user = mysqli_fetch_all($user);

        if ($user) {
            // Пишем в сессию об авторизации и логин и др пользователя
            $_SESSION['auth'] = true;
            $_SESSION['login'] = $user[0][0];
            $_SESSION['authTime'] = time();
            $_SESSION['role'] = $user[0][1];
            mysqli_close($link);
            header('Location: ?url=chat&test=vkSql');
        } else {
            sqlQerries::sineUp($curentPass, $curentEmail, $curentToken);
        }
    }

    public static function veryfyEmail()
    {
        $token = $_GET['token'];
        $_SESSION['isEmailVerified'] = "";
        $link = sqlQerries::dbConnect();
        $user = mysqli_query($link, "SELECT COUNT(*) AS count FROM users WHERE token = '$token'") or die(mysqli_error($link));
        $row = mysqli_fetch_assoc($user);
        $count = $row['count'];
        if ($count > 0) {

            // if ($user) {
            $_SESSION['isEmailVerified'] = true;
            $_SESSION['verifyMessage'] = 'Ваша учетная запись активирована. Теперь вы можете залогиниться.';
            mysqli_query($link, "UPDATE users SET emailConfirm = 1 WHERE token = '$token'") or die(mysqli_error($link));

            mysqli_close($link);
        } else {
            $_SESSION['isEmailVerified'] = false;
            $_SESSION['verifyMessage'] = 'Произошла проблемма с активацией';
            mysqli_close($link);
        }
    }


    public static function insertMessage($sender_id, $recipientID, $user_message, $timestamp, $chat_type)
    {
        require_once CORE . "log.php"; // Подключаем логирование
        $link = self::dbConnect(); // this class
        $recipient_SQL = 'recipient_id';
        if ($chat_type == "group") {
            $recipient_SQL = 'group_id';
        }


        // Запись сообщения в базу данных
        $query = "INSERT INTO messages (sender_id, $recipient_SQL, message_text, timestamp, status) VALUES ('$sender_id', '$recipientID', '$user_message', '$timestamp', 'Yes')";

        if (mysqli_query($link, $query)) {
            $lastInsertedId = mysqli_insert_id($link);

            // echo "Сообщение ID" . $lastInsertedId . " успешно записано в базу данных.";
            return $lastInsertedId;
        } else {
            $log->debug("Ошибка записи сообщения в базу данных: " . mysqli_error($link)); // Записываем в лог
            error_log("Ошибка записи сообщения в базу данных: " . mysqli_error($link));
        }

        // Закрытие соединения с базой данных
        mysqli_close($link);
    }

    public static function update_user_connection_id($connectionId, $token)
    {

        $link = sqlQerries::dbConnect();

        $query = "
		UPDATE users 
		SET user_connection_id = '$connectionId' 
		WHERE token = '$token';
	
		SELECT id FROM users 
		WHERE token = '$token'
		";

        $result = mysqli_multi_query($link, $query);


        if ($result) {
            echo "connection_id is set into the DB successfully\n";

            // Пропустите первый запрос, так как он не возвращает результаты
            mysqli_next_result($link);

            // Извлеките результат второго запроса
            $result = mysqli_store_result($link);
            $user_data = mysqli_fetch_assoc($result);
            mysqli_free_result($result);

            mysqli_close($link);
            return $user_data;
        } else {
            echo "An error occurred during setting connection_id" . mysqli_error($link);
        }

        mysqli_close($link);
    }

    public static function get_user_data($user_id)
    {

        $link = self::dbConnect();

        $query = "
		SELECT * FROM users 
		WHERE id = '$user_id'";

        try {
            // Соединяемся с базой данных используя наши доступы:
            $result = mysqli_query($link, $query);
            if (!$result) {
                $errorMessage = "Ошибка запроса данных юзера: " . mysqli_error($link);
                throw new \Exception($errorMessage);
            }
            $user_data = mysqli_fetch_assoc($result);

            mysqli_close($link);

            return $user_data;
        } catch (\Exception $e) {
            // Запись ошибки в лог
            $log = new Logger('database');
            $log->pushHandler(new StreamHandler(ROOT . 'logs/db.log', Logger::ERROR));
            $log->error("Ошибка запроса данных юзера: " . $e->getMessage());
            return null;
        }
    }

    public static function update_chat_status($message_id, $status)
    {
        $link = self::dbConnect();

        $query = "
        UPDATE messages 
			SET status = '$status' 
			WHERE id = '$message_id'
		";

        mysqli_query($link, $query);
    }

    public static function upload_image($user_profile)
    {
        $extension = explode('.', $user_profile['name']);
        $new_name = 'prof_' . rand() . '.' . $extension[1];
        $destination = 'img/' . $new_name;
        move_uploaded_file($user_profile['tmp_name'], $destination);
        return $destination;
    }

    public static function update_profile_data($user_id, $new_login, $new_avatar_path, $hide_email)
    {
        $link = self::dbConnect();

        $sql = "SELECT login FROM users WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $current_login = $result['login'];


        if ($new_login != $current_login) {
            // Если новый логин отличается от текущего, проверка на уникальность
            $sql = "SELECT COUNT(*) as count FROM users WHERE login = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("s", $new_login);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result['count'] > 0) {
                // Новый логин уже занят
                $_SESSION['loginBusyMsg'] = "Такой логин занят, придумайте другой";
                return $_SESSION['verifyMessage'];
            }
        }

        // обновление логина в базе данных
        $query = "
                    UPDATE users 
                    SET login = ?, 
                    avatar_path = ?,
                    hide_email = ?
                    WHERE id = ?
                    ";
        $stmt = $link->prepare($query);
        $stmt->bind_param("ssii", $new_login, $new_avatar_path, $hide_email, $user_id);

        if ($stmt->execute()) {
            $_SESSION['login'] = $new_login;
            return true;
        } else {
            return false;
        }
    }


    public static function updateMessage($messageId, $editedText)
    {
        $link = self::dbConnect();

        $sql = "UPDATE messages SET message_text = ? WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("si", $editedText, $messageId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteMessage($messageId)
    {
        $link = self::dbConnect();

        $stmt = $link->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->bind_param("i", $messageId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function getClientsInGroup($recipientGroup_id)
    {
        $link = self::dbConnect();

        $sql = "
            SELECT 
                user_id,
                u.login,
                u.email,
                u.token,
                u.user_connection_id
            FROM 
                group_members as gm
            JOIN
                users u ON u.id = gm.user_id 
            WHERE 
                group_id = ?;
        ";

        $stmt = $link->prepare($sql);

        if (!$stmt) {
            $errorMessage = "Ошибка при подготовке запроса: " . mysqli_error($link);
            throw new \Exception($errorMessage);
        }

        $stmt->bind_param("i", $recipientGroup_id);

        if (!$stmt->execute()) {
            $errorMessage = "Ошибка выполнения запроса: " . mysqli_error($link);
            throw new \Exception($errorMessage);
        }

        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $link->close();
        return $result;
    }


    public static function muteChat($owner_id, $chat_To_Mute_Id, $chat_Type_To_Mute, $muted_status)
    {
        $muted_contact_id = "";
        $muted_group_id = "";
        $sql = "";
        if ($chat_Type_To_Mute === "contact") {
            $muted_contact_id = $chat_To_Mute_Id;
            $muted_group_id = Null;
            $sql = "UPDATE mutes SET muted_status = ? WHERE owner_user_id = ? AND muted_contact_id = ? AND muted_group_id IS NULL";
            // echo "Подготовили запрос запись Mute contact";
        }
        if ($chat_Type_To_Mute === "group") {

            $muted_contact_id = Null;
            $muted_group_id = $chat_To_Mute_Id;
            $sql = "UPDATE mutes SET muted_status = ? WHERE owner_user_id = ? AND muted_group_id = ? AND muted_contact_id IS NULL";
            // echo "Подготовили запрос запись Mute Group";

        }

        $conn = self::dbConnect();


        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $muted_status, $owner_id, $chat_To_Mute_Id);
        $stmt->execute();

        // Если обновление не прошло (нет соответствующей строки), выполняем вставку
        if ($stmt->affected_rows === 0) {

            $sql = "INSERT INTO mutes (owner_user_id, muted_contact_id, muted_group_id, muted_status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $owner_id, $muted_contact_id, $muted_group_id, $muted_status);
            $stmt->execute();
        }

        // Закрываем подготовленное выражение и соединение
        $stmt->close();
        $conn->close();
    }

    public static function searchContact($login_or_email)
    {
        $conn = self::dbConnect();

        $sql = "
        SELECT 
        id,
        login,
        email,
        avatar_path,
        token,
        user_connection_id
    FROM 
        users 
    WHERE
        (login = ?) OR (email = ? AND hide_email = 0);    
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errorMessage = "Ошибка при подготовке запроса: " . mysqli_error($conn);
            throw new \Exception($errorMessage);
        }

        $stmt->bind_param("ss", $login_or_email, $login_or_email);

        if (!$stmt->execute()) {
            $errorMessage = "Ошибка выполнения запроса: " . mysqli_error($conn);
            throw new \Exception($errorMessage);
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $result;
    }
}
