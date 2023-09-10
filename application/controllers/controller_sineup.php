<?php
class Controller_Sineup extends Controller { 

    function action_index() { 

        $this->view->generate('auth/sineup_view.php', 'template_view.php'); 

    } 

    
}
?>
