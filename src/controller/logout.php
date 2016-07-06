<?php

namespace cvweiss\projectbase\Controller;

use cvweiss\projectbase\Session;

class logout
{
    public function doGet($view, $params)
    {   
        Session::destroy();
        $view->redirect('/');
    }
}
