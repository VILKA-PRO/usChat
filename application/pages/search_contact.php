<?php
session_start();

ini_set('display_errors', 0);

require_once '../../application/config.php';
require_once CORE . "sql.php";
$conn = sqlQerries::dbConnect();

// Проверка, был ли получен POST-запрос
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($_POST["action"] == 'contact_search') {

        // Получение данных из POST-запроса
        $contact_to_search = $_POST["contact_to_search"];
        $result = \sqlQerries::searchContact($contact_to_search);

        if ($result) {
            echo json_encode(["success" => true, "FoundUser" => $result]);
        } else {
            echo json_encode(["error" => "Пользователь не найден"]);
        }
    }
    if ($_POST["action"] == 'create_connection') {


        // Получение данных из POST-запроса
        $current_user_id = $_POST["current_user_id"];
        $new_contact_id = $_POST["new_contact_id"];
        $msg_text = $_POST["msg_text"];
        $timestamp = date("Y-m-d H:i:s", strtotime("now"));
        $current_user_data = \sqlQerries::get_user_data($current_user_id);

        $last_inserted_msg_id = \sqlQerries::insertMessage($current_user_id, $new_contact_id, $msg_text, $timestamp, 'contact');

        if ($current_user_data) {
            echo json_encode(["success" => true, "current_user_data" => $current_user_data, "last_inserted_msg_id" => $last_inserted_msg_id]);
        } else {
            echo json_encode(["error" => "Пользователь не найден"]);
        }
    }
} else {
    echo json_encode(["error" => "Недопустимый метод запроса"]);
}
// Закрытие соединения с базой данных
$conn->close();
