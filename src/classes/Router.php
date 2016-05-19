<?php

namespace Project\Base;

class Router
{
    public function route($view)
    {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $method = ucfirst(strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD')));
        $call = "do$method";
        $args = Config::getAll();

        $ex = explode('?', $uri);
        $uri = $ex[0] == '/' ? 'index' : $ex[0];
        $args['title'] = $uri;

        $ex = explode('/', $uri);
        $ex = array_diff($ex, ['']);
        while (sizeof($ex) > 0)
        {
            $className = '\\Project\\Base\\Controller\\' . implode('\\', $ex);
            $this->routeCall($className, $call, $view, $args);
            array_unshift($args, array_pop($ex));
        }

        Logger::debug("404 $uri");
        $view->error(404, "$uri could not be found", $args);
    }

    protected function routeCall($className, $call, $view, $args)
    {
        if (class_exists($className))
        {   
            Logger::debug("200 $className $call");
            $class = new $className();
            $class->$call($view, $args);
            throw new \Exception("Called $className::$call but code did not terminate as expected.");
        }
    }
}
