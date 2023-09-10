<?php

$auth = $_SESSION['auth'] ?? null;

?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="col text-center">
            <p>
                <?php echo $_SESSION['verifyMessage'];
                unset($_SESSION['verifyMessage']);
                ?>
            </p>
        </div>
    </div>
</div>