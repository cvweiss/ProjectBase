<?php

namespace Project\Base;

class Render
{
    private $jade;

    public function __construct($jade)
    {
        $this->jade = $jade;
    }

    public function render($file, $values = [])
    {
        $values = array_merge($values, Config::getAll());
        echo $this->jade->render($file, $values);
        exit(); // Exit cleanly, ensures nothing else runs after the page has been rendered
    }
}
