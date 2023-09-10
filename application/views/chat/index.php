<!-- Left bar -->
<div class="col-2 leftbarMain contacts">
    <div class="row">

        <div class="col leftbar-title d-flex align-items-center ">
            <div class="">
                <span>Контакты</span>
            </div>
        </div>
        <div class="col-auto  d-flex align-items-center ">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle " data-bs-toggle="modal" data-bs-target="#addContactModal">
                <i class="fas fa-plus"></i>
            </button>

        </div>
    </div>

    <!-- CONTACT SEARCH -->

    <div class="containerSearch mb-2">
        <div class="row justify-content-center">
            <div class="col">
                <div class="input-group search-input-group">
                    <input type="text" class="form-control search-input" id="search-input" placeholder="&#128269;  Поиск">

                </div>
            </div>
        </div>
    </div>
    <!-- //CONTACT SEARCH -->


    <div class="row sidebar sidebar-sticky">
        <div class="col leftbar">
            <div class="contacts___" id="contact-list">

                <?php
                foreach ($data as $row) {
                    if ($row['type'] === 'contact') {
                ?>
                        <!-- CONTACTS BLOCK -->
                        <div class="row py-2 flex-row flex-wrap contact noChosen " contLogin="<?= $row['contact_login'] ?>" is_active_chat="No" data-contact-id="<?= $row['contact_id'] ?>" isChatMuted="<?= $row['muted_status'] ?>">

                            <div class="col-auto ps-3 pe-0 me-2 align-self-center ">
                                <div class="avatar-container">
                                    <img src="<?= $row['contact_avatar'] ?>" class="avatarLeftBar" />
                                    <span id="mute-icon" style="display: block;" class="iconInAvatar"><i class="fas fa-volume-mute"></i></span> <!-- Здесь ваша иконка -->
                                </div>
                            </div>
                            <div class="col ps-0">
                                <span class="nameInList align-middle"><?= $row['contact_login'] ?></span>
                            </div>
                        </div>
                        <!-- //CONTACTS BLOCK -->
                <?php }
                } ?>

            </div>
        </div>
    </div>
    <div class="row align-items-between border-top border-2 border-secondary">
        <div class="col leftbar-title d-flex align-items-center ">
            <div class="">
                <span>Групповые чаты</span>
            </div>
        </div>
        <div class="col-auto  d-flex align-items-center ">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle " data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="row sidebar sidebar-sticky2 ">
        <div class="col leftbar2">
            <div class="groups" id="group-list">

                <!-- GROUP CHAT BLOCK -->
                <?php
                foreach ($data as $row) {
                    if ($row['type'] === 'group') {
                ?>
                        <div class="row py-2 flex-row flex-wrap contact group noChosen" is_active_chat="No" data-group-id="<?= $row['contact_id'] ?>" isChatMuted="<?= $row['muted_status'] ?>">

                            <!-- <div class="col-auto ps-3 pe-0 me-2 align-self-center ">
                            <img src="<?= $row['contact_avatar'] ?>" class="avatarLeftBar" />
                        </div> -->

                            <div class="col ">
                                <div class="avatar-container">
                                    <span class="nameInList align-middle"><?= $row['contact_login'] ?></span>
                                    <span id="mute-icon" style="display: block;" class="iconInAvatar2"> <i class="fas fa-volume-mute"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>

                <!-- //GROUP CHAT BLOCK -->
            </div>
        </div>
    </div>
</div>

<!-- Main -->
<div class="col main mx-3">
    <!-- MESSAGE WINDOW -->
    <div class="row message-container" id="message-container">
        <div class="col message-window " id="message-box">
            <div class="d-flex align-items-center justify-content-center">
                Чтобы начать общение, выберите контакт из списка
            </div>

        </div>
    </div>

    <!-- INPUT MESSAGE -->
    <div class="row input-message border-top border-2 border-secondary ">
        <div class="col">
            <div class="">
                <form class="">
                    <div class="col">
                        <div class="input-group my-3">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
                                </svg>
                            </span>
                            <textarea class="form-control" aria-label="With textarea" id="message"></textarea>
                            <button class="btn btn-outline-secondary" type="button" id="send-message">
                                Отправить
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- RIGHT BAR -->
<div class="col-2 border-start border-2 border-secondary">
    <div class="row">
        <div class="col righttbar-title d-flex align-items-center">
            <div class="">
                <span>Настройки</span>
            </div>
        </div>
    </div>
    <div class="row sidebar rightbar-sticky flex-column ">
        <div class="col ">
            <div class=""><a href="?url=profile">Редактировать <br>Профиль</a></div>
        </div>
    </div>
</div>



<!-- MODAL ADD GROUP -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Выберите контакты</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- CONTACT SEARCH -->
                <div class="containerSearch mb-2">
                    <div class="row justify-content-center">
                        <div class="col">
                            <div class="input-group search-input-group">
                                <input type="text" class="form-control search-input" id="search-input-modal" placeholder="&#128269;  Поиск">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- //CONTACT SEARCH -->

                <div class="row myModal myModal-sticky">
                    <div class="col myModalbar">
                        <div class="contactsModal" id="contact-list">

                            <?php
                            foreach ($data as $row) {
                                if ($row['type'] === 'contact') {

                            ?>
                                    <!-- CONTACTS BLOCK -->
                                    <div class="row d-flex contactModal noChosen justify-content-center align-items-center" contLogin="<?= $row['contact_login'] ?>" is_active_chat="No" data-contact-id="<?= $row['contact_id'] ?>" id="contactModal">

                                        <div class="col-auto ">
                                            <img src="<?= $row['contact_avatar'] ?>" class="avatarModal" />
                                        </div>
                                        <div class="col ">
                                            <span class="nameInModalList"><?= $row['contact_login'] ?>
                                            </span>
                                        </div>
                                        <div class="col-2 justify-content-end">
                                            <i id="checkIconModal" class="far fa-check-circle" style="display: none;"></i>
                                        </div>
                                    </div>

                                    <!-- //CONTACTS BLOCK -->
                            <?php }
                            }

                            ?>
                        </div>
                    </div>
                </div>

                <!-- Group Name Input -->
                <div class="containerSearch mb-2">
                    <div class="row justify-content-center">
                        <div class="col">
                            <div class="input-group search-input-group">
                                <input type="text" class="form-control search-input" id="groupName" placeholder="Введите назваени группы">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- //Group Name Input -->


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="createGroup">Создать группу</button>
            </div>
        </div>
    </div>
</div>
<!-- // MODAL ADD GROUP -->



<!-- MODAL FORWARD -->
<div class="modal fade" id="forwardModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Кому пересылаем?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- FORWARD MODAL CONTACT SEARCH -->
                <div class="containerSearch mb-2">
                    <div class="row justify-content-center">
                        <div class="col">
                            <div class="input-group search-input-group">
                                <input type="text" class="form-control search-input" id="search-input-forward-modal" placeholder="&#128269;  Поиск">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- //FORWARD MODAL CONTACT SEARCH -->

                <div class="row myModal myModal-sticky">
                    <div class="col myModalbar">
                        <div class="contactsModal" id="contact-list">

                            <?php
                            foreach ($data as $row) {
                                if ($row['type'] === 'contact') {

                            ?>
                                    <!-- CONTACTS BLOCK -->
                                    <div class="row d-flex contactToForward noChosen justify-content-center align-items-center" contLogin="<?= $row['contact_login'] ?>" is_active_chat="No" data-contact-id="<?= $row['contact_id'] ?>" id="contactModal">

                                        <div class="col-auto ">
                                            <img src="<?= $row['contact_avatar'] ?>" class="avatarModal" />
                                        </div>
                                        <div class="col ">
                                            <span class="nameInModalList"><?= $row['contact_login'] ?>
                                            </span>
                                        </div>
                                        <div class="col-2 justify-content-end">
                                            <i id="checkIconModal" class="far fa-check-circle" style="display: none;"></i>
                                        </div>
                                    </div>

                                    <!-- //CONTACTS BLOCK -->
                            <?php }
                            }
                            ?>

                            <!-- GROUP CHAT BLOCK -->
                            <?php
                            foreach ($data as $row) {
                                if ($row['type'] === 'group') {
                            ?>
                                    <div class="row py-2 flex-row flex-wrap contactToForward group noChosen" is_active_chat="No" data-group-id="<?= $row['contact_id'] ?>" isChatMuted="<?= $row['muted_status'] ?>">

                                        <!-- <div class="col-auto ps-3 pe-0 me-2 align-self-center ">
                            <img src="<?= $row['contact_avatar'] ?>" class="avatarLeftBar" />
                        </div> -->

                                        <div class="col ">
                                            <div class="avatar-container">
                                                <span class="nameInModalList align-middle"><?= $row['contact_login'] ?></span>
                                                <span id="mute-icon" style="display: block;" class="iconInAvatar2"> <i class="fas fa-volume-mute"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                            <?php }
                            } ?>

                            <!-- //GROUP CHAT BLOCK -->


                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="forwardMsg">Переслать сообщение</button>
            </div>
        </div>
    </div>
</div>
<!-- // MODAL FORWARD -->


<!-- MODAL ADD CONTACT -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить контакт</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!--  ADD CONTACT SEARCH -->
                <div class="containerSearch mb-2">
                    <div class="row justify-content-center">
                        <div class="col">
                            <div class="input-group search-input-group">
                                <input type="text" class="form-control search-input" id="find-contact-input-modal" placeholder="&#128269;  Поиск">
                                <button type="button" class="btn btn-secondary" id="findContact">Найти</button>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- // ADD CONTACT SEARCH -->

                <div class="row myModal myModal-sticky-add-contact">
                    <div class="col myModalbar">
                        <div class="contactsModal" id="add-contact-container">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<!-- // MODAL  ADD CONTACT -->


<!-- AUDIO -->
<audio id="notificationSound" preload="auto">
    <source src="img/msgNotification.mp3" type="audio/mpeg">
</audio>
<!-- // AUDIO -->