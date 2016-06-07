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
        $values = array_merge($values, Config::getInstance()->getAll());
        echo $this->jade->render($file, $values);
        $this->finish();
    }

    public function redirect($url, $code = 302)
    {
        header("Location: $url", $code);
        $this->finish();
    }

    public function error($errorCode, $errorMessage, $params)
    {   
        $params['errorCode'] = $errorCode;
        $params['errorMessage'] = $errorMessage;

        http_response_code($errorCode);
        echo $this->jade->render("error", $params);
        $this->finish();
    }

    public function finish()
    {
        Session::commit();
        exit();
    }
}
