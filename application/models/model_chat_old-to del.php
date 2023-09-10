<?php

class Model_Chat extends Model
{

    public function get_contacts()
    {

        require_once CORE . "dbConnect.php"; // Подключаем sql
        $link = sqlQerries::dbConnect();


        $user_id = $_SESSION['userID'] ?? null; // ID текущего пользователя

        $sql = "SELECT
                CASE
                    WHEN m.sender_id = '$user_id' THEN m.recipient_id
                    ELSE m.sender_id
                END AS contact_id,
                u.login AS contact_login,
                u.email AS contact_email,
                u.avatar_path AS contact_avatar
                FROM
                messages m
                INNER JOIN
                users u ON u.id = CASE
                    WHEN m.sender_id = '$user_id' THEN m.recipient_id
                    ELSE m.sender_id
                END
                WHERE
                m.sender_id = '$user_id' OR m.recipient_id = '$user_id'
                GROUP BY
                contact_id, contact_login, contact_email, contact_avatar
                ORDER BY
                contact_login";


        $result = $link->query($sql);


        $contacts = array();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $contact = $row["contact_login"] ?? $row["contact_email"];
                $contacts[] = array(
                    'contact_id' => $row['contact_id'],
                    'contact_login' => $contact,
                    'contact_avatar' => $row['contact_avatar']
                );
            }
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
}
