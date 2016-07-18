<?php

namespace cvweiss\projectbase;

class Render
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function render($file, $values = [])
    {
        $values = array_merge($values, Config::getInstance()->getAll());
        echo $this->twig->render($file . '.html', $values);
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
        echo $this->twig->render("error", $params);
        $this->finish();
    }

    public function finish()
    {
        Session::commit();
        exit();
    }
}
