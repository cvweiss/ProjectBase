<?php

namespace cvweiss\projectbase\controller\auth\eve;

use zkillboard\crestsso\CrestSSO;
use cvweiss\projectbase\Session;
use cvweiss\projectbase\Config;

class login
{
    public function doGet($view, $params)
    {
        unset($params);
        $auth = Config::getInstance()->get("oauth2");
        $eve = $auth['eve'];

        $sso = new CrestSSO($eve['client_id'], $eve['client_secret'], $eve['redirect_uris'][0], $eve['scopes'], '/');
        $view->redirect($sso->getLoginURL(Session::getSession()));
    }
}
