<?php

namespace Project\Base\Controller;

class logout
{
    function doGet($view, $values)
    {   
        session_destroy();
        $view->redirect('/');
    }
}
