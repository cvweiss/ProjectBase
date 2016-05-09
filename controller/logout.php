<?php

namespace Project\Supply;

class logout
{
    function doPage($app, $jade, $view, $values)
    {   
        session_destroy();
        $app->redirect('/');
    }
}
