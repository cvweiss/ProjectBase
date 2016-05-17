<?php

namespace Project\Base\Controller;

class logout
{
    public function doGet($view, $values)
    {   
        session_destroy();
        $view->redirect('/');
    }
}
