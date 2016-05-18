<?php

namespace Project\Base\Controller;

use Project\Base\Session;

class logout
{
    public function doGet($view, $params)
    {   
        Session::destroy();
        $view->redirect('/');
    }
}
