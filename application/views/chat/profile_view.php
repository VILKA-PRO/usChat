<?php

$hideEmailText = 'Скрыть email';
$hideEmail = "";
if ($data['hide_email']) {
    $hideEmail = 'checked';
    $hideEmailText = 'Ваш email скрыт!';
}

?>
<!-- Регистрация  -->
<div class="row justify-content-center">
    <div class="col-md-8">
        <h3 class="pb-4 mb-4 mt-5 border-bottom text-center">
            Редактирование профиля
        </h3>
        <?php
        if ($_SESSION['verifyMessage']) {
            echo $_SESSION['verifyMessage'];
            unset($_SESSION['verifyMessage']);
        } ?>
        <div class="col">
            <form class="row g-3 needs-validation " enctype="multipart/form-data" validate method="post" id="profile_form" action="?url=update_profile">
                <div class="col-md-1">
                    <!-- Отступ  -->
                </div>
                <div class="col-md-6">

                    <div class="mb-3 ">

                        <label for="email" class="form-label">Ваш email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="<?= $data['email'] ?>" disabled>

                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="hide_email" name="hide_email" <?= $hideEmail ?> value="1">
                        <label class="form-check-label" for="hide_email"><?= $hideEmailText ?></label>
                        <!-- <input type="hidden" name="hidden_hide_email" value="<?= $hideEmailBool ?>" /> -->

                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Изменить логин</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="<?= $data['login'] ?>" value="<?= $data['login'] ?>">
                    </div>

                    <?php
                    if ($_SESSION['loginBusyMsg']) {
                        echo $_SESSION['loginBusyMsg'];
                        unset($_SESSION['loginBusyMsg']);
                    } ?>

                    <div class="mb-3">
                        <label for="user_profile" class="form-label">Изменить аватар</label>
                        <input class="form-control" type="file" id="user_profile" name="user_profile" accept="image/*">

                    </div>
                    <div class="mb-3">
                        <input type="submit" name="edit" class="btn btn-primary" value="Сохранить изменения" />
                    </div>
                </div>

                <div class="col-md-1">
                    <!-- Отступ -->
                </div>

                <div class="col-md-4">
                    <div class="mb-4">
                        <label for="username" class="form-label">Ваш аватар</label>
                        <img src="<?= $data['avatar_path'] ?>" class="rounded d-block avatarProfile" alt="...">
                        <input type="hidden" name="hidden_user_profile" value="<?= $data['avatar_path'] ?>" />

                    </div>


                </div>


            </form>


        </div>
    </div>
</div><!-- /.row Регистрация-->


<script>
    $(document).ready(function() {

        $('#profile_form').parsley();

        $('#user_profile').change(function() {
            var extension = $('#user_profile').val().split('.').pop().toLowerCase();
            if (extension != '') {
                if (jQuery.inArray(extension, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    alert("Можно выбрать только картинку");
                    $('#user_profile').val('');
                    return false;
                }
            }
        });

        $("#profile_form").submit(function(event) {
            if ($("#hide_email").is(":checked") && $("#username").val().trim() === "") {
                alert("Логин не может быть пустым, если выбрано «Скрыть e-mail");
                event.preventDefault(); // Отменяет отправку формы
            }
        });

    });
</script>