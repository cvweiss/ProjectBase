<?php

namespace Project\Supply\Controller;

class logout
{
    function doGet($app, $jade, $view, $values)
    {   
        session_destroy();
        $app->redirect('/');
    }
}
