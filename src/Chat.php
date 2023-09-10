<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once 'application/config.php';
require_once CORE . "sql.php";
require_once CORE . "dbConnect.php";


class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {

        // Store the new connection to send messages to later
        echo "Server Started\n";

        $this->clients->attach($conn);

        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);

        if (isset($queryarray['token'])) {

            $user_data = \sqlQerries::update_user_connection_id($conn->resourceId, $queryarray['token']);
        }
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );

        $data = json_decode($msg, true);



        //  отредактировать сообщение
        if ($data['action'] === 'editMessage') {
            $editedMessage = $this->editMessage($data['messageId'], $data['editedText']);

            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'action' => 'messageEdited',
                    'messageId' => $data['messageId'],
                    'editedText' => $editedMessage,
                ]));
            }
        }

        // Удалить сообщение
        if ($data['action'] === 'delete_message') {
            $messageId = $data['messageId'];
            // Удаление сообщения из базы данных
            \sqlQerries::deleteMessage($messageId);
            // Отправка уведомления о удалении всем клиентам
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'action' => 'message_deleted',
                    'messageId' => $messageId
                ]));
            }
        }

        // Mute chat
        if ($data['action'] === 'mute') {
            $chat_To_Mute_Id = $data['chat_To_Mute_Id'];
            $owner_id = $data['sender_id'];
            $chat_Type_To_Mute = $data['chat_Type_To_Mute'];
            $muted_status = $data['mute_Status'];

            // Удаление сообщения из базы данных
            \sqlQerries::muteChat($owner_id, $chat_To_Mute_Id, $chat_Type_To_Mute, $muted_status);
        }


        $sender_id = $data['sender_id'];
        $sender_login = $data['login'];
        $message = $data['message'];
        $recipient_id = $data['recipient'];
        $timestamp = $data['timestamp'] = date('Y-m-d h:i:s');
        $chat_type = $data['chat_type'];
        $recipientGroup_id = $data['recipientGroup_id'];




        if ($data['command'] == 'private') {
            //private chat
            print_r($data);
            // Сохраните сообщение в базе данных
            $chat_message_id = \sqlQerries::insertMessage($sender_id, $recipient_id, $message, $timestamp, $chat_type);
            $data['message_id'] = $chat_message_id;
            print_r($data);
            //Получаем данные отправителя
            $sender_user_data = \sqlQerries::get_user_data($sender_id);

            $receiver_user_data = \sqlQerries::get_user_data($recipient_id);

            $sender_user_name = $sender_user_data['login'];
            $data['sender_avatar'] = $sender_avatar = $sender_user_data['avatar_path'];

            $receiver_user_connection_id = $receiver_user_data['user_connection_id'];
            $data['receiver_avatar'] = $receiver_avatar = $sender_user_data['avatar_path'];


            foreach ($this->clients as $client) {
                if ($from == $client) {
                    $data['from'] = 'Me';
                } else {
                    $data['from'] = $sender_user_name;
                }

                if ($client->resourceId == $receiver_user_connection_id || $from == $client) {
                    $client->send(json_encode($data));
                } else {
                    \sqlQerries::update_chat_status($chat_message_id, 'No');
                }
            }
        }

        if ($data['command'] == 'group') {
            //group chat
            print_r($data);
            // Сохраните сообщение в базе данных
            $chat_message_id = \sqlQerries::insertMessage($sender_id, $recipient_id, $message, $timestamp, $chat_type);
            $data['message_id'] = $chat_message_id;
            print_r($data);
            //Получаем данные отправителя
            $sender_user_data = \sqlQerries::get_user_data($sender_id);

            $receiver_user_data = \sqlQerries::get_user_data($recipient_id);

            $sender_user_name = $sender_user_data['login'];
            $data['sender_avatar'] = $sender_avatar = $sender_user_data['avatar_path'];

            $receiver_user_connection_id = $receiver_user_data['user_connection_id'];
            $data['receiver_avatar'] = $receiver_avatar = $sender_user_data['avatar_path'];

            $clientsInGroup = \sqlQerries::getClientsInGroup($recipientGroup_id); // Получаем клиентов в группе
            // print_r($clientsInGroup); 

            foreach ($this->clients as $client) {
                if (in_array($client->resourceId, array_column($clientsInGroup, 'user_connection_id')) || $from == $client) {
                    // Оставьте остальной код без изменений
                    if ($from == $client) {
                        $data['from'] = 'Me';
                    } else {
                        $data['from'] = $sender_user_name;
                    }
                    $client->send(json_encode($data));
                    echo "Отправлено этому соккету: ". $client->resourceId . "\n";
                } else {
                    \sqlQerries::update_chat_status($chat_message_id, 'No');
                }
            }


        }
    }

    private function editMessage($messageId, $editedText)
    {

        if (\sqlQerries::updateMessage($messageId, $editedText)) {

            return $editedText;
        } else {
            $editedText = "Ошибка обновления сообщения";
            return $editedText;
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $errorMessage = "An error has occurred: {$e->getMessage()}\n";
        $errorTrace = "Trace: \n{$e->getTraceAsString()}\n";

        echo $errorMessage;
        echo $errorTrace;

        $conn->close();
    }
}
