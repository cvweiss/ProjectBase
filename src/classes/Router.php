<?php

namespace Project\Supply;

class Router
{
    public function route($jade, $view)
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
        $call = "do$method";

        if ($uri == '/')
        {
            $controller = new Controller\index();
            $controller->$call($this, $jade, $view, $args);
        }

        $ex = explode('?', $uri);
        $uri = $ex[0];
        $args = [];

        $ex = explode('/', $uri);
        foreach ($ex as $key=>$value) if ($ex[$key] == '') unset($ex[$key]);
        while (sizeof($ex) > 0)
        {
            $class = '\\Project\\Supply\\Controller\\' . implode('\\', $ex);
            if (class_exists($class)) $class::$call($this, $jade, $view, $args);
            array_unshift($args, array_pop($ex));
        }

        die("404 $uri");
    }

    public function redirect($url, $code = 302)
    {
        header("Location: $url", $code);
        exit();
    }
}
