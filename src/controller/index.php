<?php

namespace Project\Base\Controller;

class index
{
    public function doGet($render, $values)
    {
        $content = ['content' => 'Hello World!', 'title' => 'Home Page'];

        $render->render("index", $content);
    }
}
