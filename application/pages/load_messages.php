<?php
session_start();

ini_set('display_errors', 0);

require_once '../../application/config.php';
require_once CORE . "sql.php";
$link = sqlQerries::dbConnect();

$sender_id = $_SESSION['userID'];
$recipient_id = $_GET['contact_id'];
$chat_type = $_GET['chat_type'];

$sql = "";

if ($chat_type === "contact") {
    $sql =
        "SELECT
            m.id,
            m.sender_id,
            sender.login AS sender_login,
            sender.avatar_path AS sender_avatar,
            sender.email AS sender_email,
            m.recipient_id,
            recipient.login AS recipient_login,
            recipient.avatar_path AS recipient_avatar,
            recipient.email AS recipient_email,
            m.message_text,
            m.timestamp,
            m.group_id
        FROM
            messages m
        JOIN
            users sender ON m.sender_id = sender.id
        JOIN
            users recipient ON m.recipient_id = recipient.id
        WHERE
            (m.sender_id = $sender_id AND m.recipient_id = $recipient_id) OR
            (m.sender_id = $recipient_id AND m.recipient_id = $sender_id)
        ORDER BY
            m.timestamp;
    ";
}

if ($chat_type === "group") {
    $sql =
        "SELECT
            m.id,
            m.sender_id,
            sender.login AS sender_login,
            sender.avatar_path AS sender_avatar,
            sender.email AS sender_email,
            m.recipient_id,
            m.message_text,
            m.timestamp,
            m.group_id
        FROM
            messages m
        JOIN
            users sender ON m.sender_id = sender.id
        WHERE 
            m.group_id = $recipient_id
        ORDER BY
            m.timestamp;
    ";
}



$result = $link->query($sql);

$messages = array();

if ($result) { // Проверяем успешность выполнения запроса
    while ($row = $result->fetch_assoc()) {
        $sender_login = $row['sender_login'] ?? $row['sender_email'];
        $recipient_login = $row['recipient_login'] ?? $row['recipient_email'];

        $messages[] = array(
            'currentUser_id' => $sender_id,
            'message_id' => $row['id'],
            'sender_id' => $row['sender_id'],
            'sender_login' => $sender_login,
            'sender_avatar' => $row['sender_avatar'],
            'sender_email' => $row['sender_email'],
            'recipient_id' => $row['recipient_id'],
            'recipient_login' => $recipient_login,
            'recipient_avatar' => $row['recipient_avatar'],
            'recipient_email' => $row['recipient_email'],
            'message' => $row['message_text'],
            'timestamp' => $row['timestamp'],
            'group_id' => $row['group_id']
        );
    }
} else {
    // Обработка ошибки, например:
    die('Ошибка выполнения запроса: ' . $link->error);
}

// Освобождаем ресурсы результата запроса
$link->close();

// Отправляем данные в формате JSON
$response = ['success' => true, 'messages' => $messages];
header('Content-Type: application/json');
echo json_encode($response);
