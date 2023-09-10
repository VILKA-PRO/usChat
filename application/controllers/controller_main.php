<?php
class Controller_Main extends Controller
{

    function action_index()
    {
        if (!$_SESSION['auth']) {
            $this->view->generate('main_view.php', 'template_view.php');
        } else {
            header('Location: ?url=chat');
        }
    }
}
