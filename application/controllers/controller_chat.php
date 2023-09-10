<?php
class Controller_chat extends Controller
{

    function __construct()
    {

        $this->model = new Model_Chat();
        $this->view = new View();

    }


    function action_index()
    {
        if ($_SESSION['auth']) {

            $data = $this->model->print_contacts();

            $this->view->generate('chat/index.php', 'template_view.php',$data);
        } else {
            header('Location: ?url=main');
        }
    }
}
