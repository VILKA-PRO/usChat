<?php
session_start();

ini_set('display_errors', 0);


require_once '../../application/config.php';
require_once CORE . "sql.php";
$conn = sqlQerries::dbConnect();

// Проверка, был ли получен POST-запрос
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Получение данных из POST-запроса
    $groupName = $_POST["groupName"];
    $userID = $_POST["userID"];
    $contactsToGroup = $_POST["contactsToGroup"];

    // Добавление данных в таблицу (здесь вы должны создать имена таблиц в вашей базе данных)
    $sql = "INSERT INTO chat_groups (group_name, created_by) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $groupName, $userID);

    if ($stmt->execute() === TRUE) {
        $lastInsertID = $conn->insert_id;
        foreach ($contactsToGroup as $contactID) {
            $sql = "INSERT INTO group_members (group_id, user_id) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("ii", $lastInsertID, $contactID);
            $stmt2->execute();
        }
        echo json_encode(["success" => true, "createdGroupId" => $lastInsertID]);
    } else {
        echo json_encode(["error" => "Ошибка добавления в базу данных: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Недопустимый метод запроса"]);
}

// Закрытие соединения с базой данных
$conn->close();
