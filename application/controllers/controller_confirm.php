<?php


class Controller_confirm extends Controller
{


    function action_index()
    {

        $this->view->generate('auth/confirm_view.php', 'template_view.php');
    }
}