<?php
class Controller_Login extends Controller { 

    function action_index() { 

        $this->view->generate('auth/login_view.php', 'template_view.php'); 

    } 

    
}
?>