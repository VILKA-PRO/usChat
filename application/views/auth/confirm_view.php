<?php
$auth = $_SESSION['auth'] ?? null;

?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <h3 class="pb-4 mb-4 font-italic border-bottom text-center">
            Почти все готово
        </h3>

        <div class="col text-center">
            <p><?php echo $_SESSION['emailConfirm'];
                unset($_SESSION['emailConfirm']); ?>
            </p>
        </div>
    </div>
</div>