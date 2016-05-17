<?php

namespace Project\Base\Controller;

class logout
{
    public function doGet($view, $params)
    {   
        session_destroy();
        $view->redirect('/');
    }
}
