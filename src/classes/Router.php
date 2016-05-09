<?php

namespace Project\Supply;

class Router
{
    public function route($jade, $view)
    {
        $uri = $_SERVER['REQUEST_URI'];

        $uri = $_SERVER['REQUEST_URI'];
        $ex = explode('?', $uri);
        $uri = $ex[0];
        $args = [];

        $ex = explode('/', $uri);
        foreach ($ex as $key=>$value) if ($ex[$key] == '') unset($ex[$key]);
        while (sizeof($ex) > 0)
        {
            $class = '\\Project\\Supply\\Controller\\' . implode('\\', $ex);
            if (class_exists($class)) $class::doPage($this, $jade, $view, $args);
            array_unshift($args, array_pop($ex));
        }
        $controller = new Controller\index();
        $controller->doPage($this, $jade, $view, $args);
    }

    public function redirect($url, $code = 302)
    {
        header("Location: $url", $code);
        exit();
    }
}
