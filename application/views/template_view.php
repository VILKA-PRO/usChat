<?php
ini_set('display_errors', 'off');
$auth = $_SESSION['auth'] ?? null;
$login = $_SESSION['login'] ?? $_SESSION['email'];
$token = $_SESSION['token'];
$count = $_SESSION['count'] ?? 0;
$currentUserId = $_SESSION['userID'];
$count++;
$_SESSION['count'] = $count;

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>usMessanger</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="vendor-front/parsley/parsley.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="https://i.ibb.co/7SQVS44/favicon.jpg">
    <link href="CSS/style.css" rel="stylesheet" />
    <link href="CSS/chat.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript" src="vendor-front/parsley/dist/parsley.min.js"></script>


</head>

<body>

    <!-- HEADER  -->
    <div class="navbar navbar-dark sticky-top bg-dark">
        <div class="row container-fluid">
            <div class="col-4">
                <a class="navbar-brand" href="?url=main">
                    <img src="img/US-Logo-1.png" alt="" width="30" height="30" class="d-inline-block align-text-center" />
                    usMessanger
                </a>
            </div>
            <div class="col-4 navbar-nav">
                <div class="nav-item d-flex justify-content-end text-nowrap">
                    <?php
                    if (!$auth) {
                    ?>
                        <!-- <p>текст</p> -->
                        <a class="nav-link pe-4" href="?url=login">Вход</a>
                        <a class="nav-link btn btn-sm btn-outline-secondary custom-btn btn-16" href="?url=sineup">Регистрация</a>
                    <?php } else {

                    ?>
                        <span style="color:aliceblue;" id="user-info" data-user-id="<?= $_SESSION['userID'] ?>"><strong><?= $login ?></strong>,<br> добро пожаловать. </span>
                        <a class="custom-btn btn-16 " href="application/pages/logout.php?exit">Выход</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- // HEADER  -->



    <main class="container-fluid">
        <div class="row">
            <?php include $content_view; ?>
        </div>

    </main><!-- /.container -->


    <!-- MENU -->
    <div id="custom-context-menu" class="context-menu">
        <div class="content">
            <ul class="menu">
                <li class="item">
                    <i class="uil uil-eye"></i>
                    <span data-bs-toggle="modal" data-bs-target="#forwardModal" id="context-menu-forward">Переслать</span>
                </li>
                <li class="item">
                    <i class="uil uil-link-alt"></i>
                    <span id="context-menu-edit">Редактировать</span>
                </li>
                <li class="item">
                    <i class="uil uil-edit"></i>
                    <span id="context-menu-del">Удалить</span>
                </li>
            </ul>
        </div>
    </div>
    <!-- / MENU -->

    <!-- MENU CONTACT -->
    <div id="contact-context-menu" class="context-menu">
        <div class="content">
            <ul class="menu">
                <li class="item">
                    <i class="uil uil-eye"></i>
                    <span id="context-menu-mute">Включить/Отключить оповещения</span>
                </li>
            </ul>
        </div>
    </div>
    <!-- / MENU CONTACT-->


    <script type="text/javascript">
        $(document).ready(function() {
            var conn = new WebSocket("ws://localhost:9050?token=<?= $token ?>");

            const messageBox = $("#message-box");
            var chatType;

            let recipientUserId;
            let chosenContact;
            let recipientGroupId;

            let currentUserId = "<?= $currentUserId ?>";

            conn.onopen = function(e) {
                console.log("WebSocket connection opened.");
                scrollToBottom();
            };


            conn.onmessage = function(e) {

                const data = JSON.parse(e.data);
                console.log("JSON.parse(e.data):");
                console.log(data);

                const res_type = data.chat_type;
                const res_message = data.message;
                const res_login = data.login;
                const res_timestamp = data.timestamp;
                const res_recipient = data.recipient_id;
                const res_sender_id = data.sender_id;
                const res_avatar = data.sender_avatar;
                const res_message_id = data.message_id;
                var isActiveChat;

                // Проверка активен ли чат, в который пришло сообщение
                if (res_type == "group") {
                    var group = $("[data-group-id='" + data.recipientGroup_id + "']");
                    isActiveChat = group.attr('is_active_chat');
                }

                if (res_type == "contact" && data.from != "Me") {
                    var group = $("[data-contact-id='" + data.sender_id + "']");
                    isActiveChat = group.attr('is_active_chat');
                }

                if (res_type == "contact" && data.from == "Me") {
                    var group = $("[data-contact-id='" + data.recipient + "']");
                    isActiveChat = group.attr('is_active_chat');
                }

                console.log("isActiveChat: " + isActiveChat);

                // Проверка на mute
                if (res_type == "group" && data.from != "Me") {
                    var group = $("[data-group-id='" + data.recipientGroup_id + "']");
                    console.log("isChatMuted гр до: " + group.attr('isChatMuted'));
                    if (group.attr('isChatMuted') == "No") {
                        playNotificationSound();
                        console.log("isChatMuted гр в: " + group.attr('isChatMuted'));
                    }
                }

                if (res_type == "contact" && data.from != "Me") {
                    var contact = $("[data-contact-id='" + data.sender_id + "']");

                    if (contact.attr('isChatMuted') == "No") {
                        playNotificationSound();
                    }
                }


                var divClass = "";
                var justify = ""

                if (data.from == 'Me') {
                    divClass = "outcome-message";
                    justify = "end"
                } else {
                    divClass = "income-message";
                    justify = "start"
                }

                if (isActiveChat == 'Yes') {
                    var html_data = `
                            <!--` + divClass + `-->
                                <div class="row d-flex justify-content-` + justify + `">
                                    <div class="col-7">
                                        <div class="border ` + divClass + ` rounded-3 mt-4 bg-light" message_id="` + res_message_id + `"> 
                                            <img src="` + res_avatar + `" class="avatar" alt="user." />
                                            <span class="nameInChat align-text-center">` +
                        res_login + `
                                            </span><br /><span class="msgText">` +
                        res_message +
                        `</span><span class="timestamp"><br /> ` +
                        res_timestamp +
                        `</span>
                                        </div>
                                    </div>
                                </div>
                            <!-- // ` + divClass + ` -->
                            `;
                    messageBox.append(html_data);
                    scrollToBottom();

                } else {
                    // Это для статуса
                    // var count_chat = $('#userid' + data.userId).text();

                    // if (count_chat == '') {
                    //     count_chat = 0;
                    // }

                    // count_chat++;

                    // $('#userid_' + data.userId).html('<span class="badge badge-danger badge-pill">' + count_chat + '</span>');
                }
                // }


            };

            conn.onerror = function(e) {
                console.log(e.data);
            };

            conn.onclose = function() {
                console.log("WebSocket connection closed.");
                scrollToBottom();
            };

            $("#send-message").click(function() {

                if (isEditingMsg) {
                    editMessage(messageId, editedText)
                    isEditingMsg = false;
                } else {
                    send_message();
                }

                scrollToBottom();
            });

            $(".contact").click(function() {
                recipientUserId = $(this).data("contact-id"); // Получаем ID контакта
                recipientGroupId = $(this).data("group-id"); // Получаем ID контакта
                chosenContact = recipientUserId ?? recipientGroupId;
                commandVar = 'private';
                if (recipientUserId) {
                    chatType = "contact";
                }
                if (recipientGroupId) {
                    chatType = commandVar = "group";
                }
                console.log("chatClick - " + chosenContact + chatType);

            })

            function send_message() {
                const message_input = $("#message");
                const login = "<?= $login ?>"; // Полученное имя пользователя
                const currentTimestamp = new Date().getTime();

                if (message_input.val() === "") {
                    alert("You need to input your message!");
                    return;
                }

                var activeContact = $("div[is_active_chat='Yes']");

                recipientUserId = activeContact.data("contact-id");
                recipientGroupId = activeContact.data("group-id");

                chosenContact = recipientUserId ?? recipientGroupId;
                commandVar = 'private';

                if (recipientUserId) {
                    chatType = "contact";
                }
                if (recipientGroupId) {
                    chatType = commandVar = "group";
                }
                console.log("Перед отправкой сообщения - " + chosenContact + chatType);

                var activeContact = $("div[is_active_chat='Yes']");

                const result_message = {
                    message: message_input.val(),
                    login: login,
                    timestamp: currentTimestamp,
                    recipient: chosenContact,
                    sender_id: currentUserId,
                    chat_type: chatType,
                    command: commandVar,
                    recipientGroup_id: recipientGroupId,
                    action: "",
                };
                conn.send(JSON.stringify(result_message));

                message_input.val("");
            }


            // Контекстное меню
            // ================

            //Инициализация 
            var editedName;
            var editedText;
            var messageId;
            var isEditingMsg = false;
            var commandVar;


            $(document).on('contextmenu', '.outcome-message', function(event) {
                event.preventDefault();
                console.log("contextClick");
                let type = "#custom-context-menu";
                showContextMenu(event.clientX, event.clientY, type);

                // Получаем значения каждого <span>
                editedName = $(this).find('.nameInChat').text();
                editedText = $(this).find('.msgText').text();
                // Получаем значение атрибута message_id
                messageId = $(this).attr('message_id');

            });

            // Контекстное меню на контактах
            var chatToMuteId;
            var chatTypeToMute;
            $(document).on('contextmenu', '.contact', function(event) {

                event.preventDefault();

                let type = "#contact-context-menu";
                // Получаем значения каждого аттрибутов
                contlogin = $(this).attr('contlogin');
                contId = $(this).data('contact-id');
                var isChatMuted = $(this).attr('ischatmuted');
                console.log("contextContactClick " + isChatMuted);
                var soundAction = (isChatMuted == "Yes") ? "Включить" : "Выключить";
                $("#context-menu-mute").text(soundAction + " звук");

                chatToMuteId = $(this).data("contact-id") || $(this).data("group-id"); // Получаем ID контакта

                if ($(this).data("contact-id")) {
                    chatTypeToMute = "contact";
                }
                if ($(this).data("group-id")) {
                    chatTypeToMute = commandVar = "group";
                }


                showContextMenu(event.clientX, event.clientY, type);

            });

            $(document).on('click', function(event) {
                hideContextMenu();
            });

            $(window).on('resize', function() {
                hideContextMenu();
            });

            // Функция для отображения контекстного меню
            function showContextMenu(x, y, type) {
                const contextMenu = $(type);
                contextMenu.css({
                    display: 'block',
                    left: x + 'px',
                    top: y + 'px'
                });

                // Проверка на горизонтальный и вертикальный скролл
                const windowHeight = $(window).height();
                const windowWidth = $(window).width();
                const contextMenuHeight = contextMenu.height();
                const contextMenuWidth = contextMenu.width();

                if (y + contextMenuHeight > windowHeight) {
                    contextMenu.css('top', windowHeight - contextMenuHeight + 'px');
                }

                if (x + contextMenuWidth > windowWidth) {
                    contextMenu.css('left', windowWidth - contextMenuWidth + 'px');
                }
            }

            function hideContextMenu() {
                $('#custom-context-menu').css('display', 'none');
                $('#contact-context-menu').css('display', 'none');

            }
            // ^^^ Контекстное меню ^^^



            // Добавляем прослушиватель событий для опции "Mute"
            $("#context-menu-mute").on("click", function() {
                console.log("#context-menu-mute");
                console.log("chatToMuteId " + chatToMuteId);
                console.log("chatTypeToMute " + chatTypeToMute);
                console.log("currentUserId " + currentUserId);

                muteChat(currentUserId, chatToMuteId, chatTypeToMute);

            });


            // Добавляем прослушиватель событий для опции "Переслать"
            $("#context-menu-forward").on("click", function() {
                console.log("#context-menu-forward");
                console.log('Message:', editedText);
                console.log('Message ID:', messageId);

            });

            // прослушка при выборе контакта для пересылки
            $(".contactToForward").on("click", function() {
                console.log("contactToForward");
                recipientUserId = $(this).data("contact-id"); // Получаем ID контакта
                recipientGroupId = $(this).data("group-id"); // Получаем ID контакта
                chosenContact = recipientUserId ?? recipientGroupId;

                if (recipientUserId) {
                    chatType = "contact";
                }
                if (recipientGroupId) {
                    chatType = "group";
                }

                console.log("chatClick - " + chosenContact + chatType);
                // Удаление класса у всех элементов
                $(".contactToForward").removeClass("chosen");

                // Добавление класса к текущему элементу
                $(this).addClass("chosen");

            });

            $("#forwardMsg").click(function() {
                var message_input = `<p class = "forwardedText">Пересланное сообщение:</p>` + editedText;
                const login = "<?= $login ?>";
                const currentTimestamp = new Date().getTime();

                const result_message = {

                    message: message_input,
                    login: login,
                    timestamp: currentTimestamp,
                    recipient: chosenContact,
                    sender_id: currentUserId,
                    chat_type: chatType,
                    command: 'private',
                    recipientGroup_id: recipientGroupId,
                    action: "",
                };
                // console.log(result_message);
                conn.send(JSON.stringify(result_message));
                $("#forwardModal").modal("hide"); // закрыть модальное окно

            });



            // Добавляем прослушиватель событий для опции "удалить"
            $("#context-menu-del").on("click", function() {
                console.log("#context-menu-del:");
                console.log('Message:', editedText);
                console.log('Message ID:', messageId);

                deleteMessage(messageId);
            });


            // Добавляем прослушиватель событий для опции "Редактировать"
            $("#context-menu-edit").on("click", function() {
                console.log("#context-menu-edit");
                console.log('Name:', editedName);
                console.log('Message:', editedText);
                console.log('Message ID:', messageId);

                $("#message").val(editedText);
                isEditingMsg = true;
            });

            conn.addEventListener('message', (event) => {
                const data = JSON.parse(event.data);
                // console.log('Received an edited message:', data);

                if (data.action === 'messageEdited') {
                    console.log('data.messageId: ', data.messageId);
                    console.log('data.messageId: ', data.editedText);

                    // Находим элемент с нужным message_id
                    const messageElement = document.querySelector(`[message_id="${data.messageId}"]`);

                    if (messageElement) {
                        // Находим внутри элемента span с классом msgText
                        const msgTextElement = messageElement.querySelector('.msgText');

                        if (msgTextElement) {
                            // Обновляем содержимое span с новым отредактированным текстом
                            msgTextElement.textContent = data.editedText;
                        }
                    }
                }
                if (data.action === 'message_deleted') {
                    var deletedMessageId = data.messageId;
                    // Удалить сообщение с указанным идентификатором на клиентской стороне
                    const messageElement = document.querySelector(`[message_id="${data.messageId}"]`);
                    messageElement.remove();
                }
            });

            // Отправка запроса на редактирование сообщения
            function editMessage(messageId, editedText) {
                const message_input = $("#message");
                const login = "<?= $login ?>"; // Полученное имя пользователя
                const currentTimestamp = new Date().getTime();

                const data = {
                    action: 'editMessage',
                    messageId: messageId,
                    editedText: message_input.val(),
                    login: login,
                    timestamp: currentTimestamp,
                    recipient: recipientUserId,
                    sender_id: currentUserId,
                    command: 'private-edit',
                };
                conn.send(JSON.stringify(data));
                message_input.val("");

            }


            function deleteMessage(messageId) {
                var deleteData = {
                    action: 'delete_message',
                    messageId: messageId
                };
                conn.send(JSON.stringify(deleteData));
            }


            function muteChat(currentUserId, chatToMuteId, chatTypeToMute) {
                //  объект данных для отправки на сервер
                var chatElement = $("[data-" + chatTypeToMute + "-id='" + chatToMuteId + "']");
                var isChatMuted = chatElement.attr('ischatmuted');
                var muteIcon = chatElement.find('#mute-icon');
                var muteStatus;
                if (isChatMuted == "No") {
                    muteStatus = "Yes";
                    chatElement.attr("ischatmuted", muteStatus);
                    muteIcon.css('display', 'block');
                } else {
                    muteStatus = "No";
                    chatElement.attr("ischatmuted", muteStatus);
                    muteIcon.css('display', 'none');
                }
                var data = {
                    action: 'mute',
                    chat_To_Mute_Id: chatToMuteId,
                    sender_id: currentUserId,
                    command: 'private-edit',
                    chat_Type_To_Mute: chatTypeToMute,
                    mute_Status: muteStatus,
                };

                conn.send(JSON.stringify(data));


                chatToMuteId = "";
                chatTypeToMute = "";
            }




            // Функция для автоматической прокрутки вниз
            function scrollToBottom() {
                var chatBox = document.getElementById("message-container");
                chatBox.scrollTop = chatBox.scrollHeight;
            }
            // Звуковые оповещения

            // В этом коде мы получаем ссылку на аудио-элемент
            const notificationSound = document.getElementById("notificationSound");

            // При поступлении нового сообщения, воспроизводим звук оповещения
            function playNotificationSound() {
                notificationSound.currentTime = 0; // Сбросим время аудио, чтобы воспроизвести звук заново
                notificationSound.play();
            }

        })
    </script>

    <script src="JS/script.js"></script>

</body>

</html>