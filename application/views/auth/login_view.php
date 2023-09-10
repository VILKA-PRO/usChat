<?php

$auth = $_SESSION['auth'] ?? null;
$wrongPass = $_SESSION['msg'] ?? null;

?>

<!-- Авторизация  -->
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <h3 class="pb-4 mb-4 font-italic border-bottom text-center">
            Авторизация
        </h3>

        <div class="col">
            <form class="row g-3 needs-validation" validate method="post" action="?url=process">

                <div class="col-md-5">
                    <label for="username" class="form-label">Ваш логин или email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="col-md-5">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="col-2 align-self-end d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit" style="width: -webkit-fill-available">Войти</button>
                </div>
                <div class="col-12 mt-1">
                    <p style="color:brown"><?= $_SESSION['verifyMessage'] ?></p>
                </div>
                <?php
                $_SESSION['verifyMessage'] = "";
                if ($_SESSION['loginError']) { ?>
                    <div class="col-12 mt-1">
                        <p style="color:brown">Логин и/или пароль не найдены. Попробуйте еще раз</p>
                    </div>
                <?php }
                unset($_SESSION['loginError']); ?>
            </form>
        </div>

    </div>

</div><!-- /.row Авторизация-->