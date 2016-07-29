<?php

namespace cvweiss\projectbase\controller\auth\eve;

use cvweiss\projectbase\Session;
use cvweiss\projectbase\Config;

class login
{
    public function doGet($view, $params)
    {
        unset($params);
        $auth = Config::getInstance()->get("oauth2");

        $eve = $auth['eve'];

        $ccpClientID = $eve['client_id'];
        $redirectUri = $eve['redirect_uris'][0];
        $scopes = $eve['scopes'] ?? "";

        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';

        $url = "https://login.eveonline.com/oauth/authorize/?response_type=code&redirect_uri=$redirectUri&client_id=$ccpClientID&scope=$scopes&state=redirect:$referrer";
        $view->redirect($url);
    }
}
