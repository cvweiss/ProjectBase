<?php

namespace Project\Base;

class Router
{
    private $view = null;

    public function route($jade, $view)
    {
        $this->view = $view;

        $uri = $_SERVER['REQUEST_URI'];
        $method = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
        $call = "do$method";
        $args = [];

        $ex = explode('?', $uri);
        $uri = $ex[0];
        if ($uri == '/') $uri = 'index';

        $ex = explode('/', $uri);
        foreach ($ex as $key=>$value) if ($ex[$key] == '') unset($ex[$key]);
        while (sizeof($ex) > 0)
        {
            $className = '\\Project\\Base\\Controller\\' . implode('\\', $ex);
            $this->routeCall($className, $call, $jade, $view, $args);
            array_unshift($args, array_pop($ex));
        }

        $this->error(404, "$uri could not be found");
    }

    protected function routeCall($className, $call, $jade, $view, $args)
    {
        if (class_exists($className))
        {   
            $className::$call($this, $jade, $view, $args);
            exit();
        }
    }

    public function redirect($url, $code = 302)
    {
        header("Location: $url", $code);
        exit();
    }

    public function error($errorCode, $errorMessage)
    {
        http_response_code($errorCode);
        $this->view->render("error", ['errorCode' => $errorCode, 'errorMessage' => $errorMessage]);
        exit();
    }
}
