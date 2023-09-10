<?php


class Controller_verify extends Controller
{


    function action_index()
    {
        
        require_once CORE . "sql.php";
        sqlQerries::veryfyEmail();
        $this->view->generate('auth/verify_view.php', 'template_view.php');
    }
}