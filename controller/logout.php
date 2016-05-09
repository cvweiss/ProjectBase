<?php

namespace Project\Supply\Controller;

class logout
{
    function doPage($app, $jade, $view, $values)
    {   
        session_destroy();
        $app->redirect('/');
    }
}
