<?php

namespace Project\Base\Controller;

class index
{
    public function doGet($render, $params)
    {
        $params['content'] => 'Hello World';
        $params['title'] = 'Home Page';

        $render->render("index", $params);
    }
}
