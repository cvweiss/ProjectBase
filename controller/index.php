<?php

namespace Project\Supply\Controller;

class index
{
    function doPage($app, $jade, $view, $values)
    {
        $content = ['content' => 'Hello World!', 'title' => 'Home Page'];

        $view->render("index", $content);
    }
}
