<?php

class Model_Chat extends Model
{

    public function get_contacts()
    {

        require_once CORE . "dbConnect.php"; // Подключаем sql
        $link = sqlQerries::dbConnect();

        $user_id = $_SESSION['userID'] ?? null; // ID текущего пользователя

        // Создаем подготовленный запрос для выборки контактов
        $sqlContacts = "
    SELECT
        CASE
            WHEN m.sender_id = ? THEN m.recipient_id
            ELSE m.sender_id
        END AS contact_id,
        u.login AS contact_login,
        u.email AS contact_email,
        u.avatar_path AS contact_avatar,
        'contact' AS type
    FROM
        messages m
    INNER JOIN
        users u ON u.id = CASE
            WHEN m.sender_id = ? THEN m.recipient_id
            ELSE m.sender_id
        END
    WHERE
        m.sender_id = ? OR m.recipient_id = ?
    GROUP BY
        contact_id, contact_login, contact_email, contact_avatar, type
";

        // Создаем подготовленный запрос для выборки активных групп
        $sqlGroups = "
    SELECT
        gm.group_id AS contact_id,
        cg.group_name AS contact_login,
        cg.created_by AS contact_email,
        NULL AS contact_avatar,
        'group' AS type
    FROM
        group_members gm
    INNER JOIN
        chat_groups cg ON gm.group_id = cg.id
    WHERE
        gm.user_id = ?
";

        // Объединяем результаты двух запросов
        $sql = "
        SELECT
            CASE
                WHEN m.sender_id = ? THEN m.recipient_id
                ELSE m.sender_id
            END AS contact_id,
            u.login AS contact_login,
            u.email AS contact_email,
            u.avatar_path AS contact_avatar,
            'contact' AS type  -- Добавляем столбец для типа контакта
        FROM
            messages m
        INNER JOIN
            users u ON u.id = CASE
                WHEN m.sender_id = ? THEN m.recipient_id
                ELSE m.sender_id
            END
        WHERE
            m.sender_id = ? OR m.recipient_id = ?
        GROUP BY
            contact_id, contact_login, contact_email, contact_avatar, type
        UNION
        SELECT
            gm.group_id AS contact_id,
            cg.group_name AS contact_login,
            cg.created_by AS contact_email,
            NULL AS contact_avatar,
            'group' AS type  -- Добавляем столбец для типа группы
        FROM
            group_members gm
        INNER JOIN
            chat_groups cg ON gm.group_id = cg.id
        WHERE
            gm.user_id = ?
        ORDER BY
            contact_login";

        // Подготавливаем запрос
        $stmt = $link->prepare($sql);

        if ($stmt) {
            // Привязываем параметры к подготовленному запросу
            $stmt->bind_param("sssss", $user_id, $user_id, $user_id, $user_id, $user_id);

            // Выполняем запрос
            $stmt->execute();

            // Получаем результат
            $result = $stmt->get_result();

            $contacts = array();
            $mutedStatus = null; // Инициализируем переменную в начале каждой итерации
            $mutedChats = $this->get_muted_groups($user_id);


            while ($row = $result->fetch_assoc()) {
                $contact = $row["contact_login"] ?? $row["contact_email"];

                // Проходим по массиву $mutedChats и ищем совпадения
                foreach ($mutedChats as $mutedChat) {

                    if ($row['type'] == $mutedChat['muted_chat_type'] && $row['contact_id'] == $mutedChat['muted_chat_id']) {
                        $mutedStatus = $mutedChat['muted_status'];
                        break; // Выходим из цикла, если нашли совпадение
                    } else {
                        $mutedStatus = "No";
                    }
                }

                $contacts[] = array(
                    'contact_id' => $row['contact_id'],
                    'contact_login' => $contact,
                    'contact_avatar' => $row['contact_avatar'],
                    'type' => $row['type'],
                    'muted_status' => $mutedStatus
                );
                // $contacts = array_merge($contacts, $mutedChats);
            }

            $stmt->close();
            $link->close();


            return $contacts;
        } else {
            $link->close();
            $result = "У вас нет контактов.";
            return $result;
        }
    }

    public function print_contacts()
    {
        return $this->get_contacts();
    }


    public function get_muted_groups($user_id)
    {

        require_once CORE . "dbConnect.php"; // Подключаем sql
        $link = sqlQerries::dbConnect();



        // Создаем подготовленный запрос для выборки контактов
        $sql = "
            SELECT
                muted_contact_id,
                muted_group_id,
                muted_status
            FROM
                mutes 
            WHERE
                owner_user_id = ?;
        ";

        // Подготавливаем запрос
        $stmt = $link->prepare($sql);

        if ($stmt) {
            // Привязываем параметры к подготовленному запросу
            $stmt->bind_param("i", $user_id);

            // Выполняем запрос
            $stmt->execute();

            // Получаем результат
            $result = $stmt->get_result();
            $mutedChats = array();

            while ($row = $result->fetch_assoc()) {
                $muted_chat_type = $row['muted_contact_id'] ? "contact" : "group";
                $muted_chat_id = $row['muted_contact_id'] ? $row['muted_contact_id'] : $row['muted_group_id'];

                $mutedChats[] = array(
                    'muted_chat_type' => $muted_chat_type,
                    'muted_chat_id' => $muted_chat_id,
                    'muted_status' => $row['muted_status']
                );
            }

            $stmt->close();
            $link->close();

            return $mutedChats;
        }
    }
}
