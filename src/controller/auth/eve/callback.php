<?php

namespace cvweiss\projectbase\Controller\auth\eve;

use zkillboard\crestsso\CrestSSO;
use cvweiss\projectbase\Config;
use cvweiss\projectbase\Mongo;
use cvweiss\projectbase\Session;

class callback
{
    public function doGet($view, $params)
    { 
        unset($params);
        $auth = Config::getInstance()->get("oauth2");
        $eve = $auth['eve'];

        $sso = new CrestSSO($eve['client_id'], $eve['client_secret'], $eve['redirect_uris'][0], $eve['scopes'], '/');
        $code = filter_input(INPUT_GET, 'code');
        $state = filter_input(INPUT_GET, 'state');
        $userInfo = $sso->handleCallback($code, $state, Session::getSession());

        $charID = $userInfo['characterID'];
        $id = "auth:eve:" . $charID;
        $user = Mongo::get()->findDoc("users", ['id' => $id], null, true);

        $user->setAll([
                "id" => $id,
                "name" => $userInfo['characterName'],
                "email" => null,
                "image" => "https://imageserver.eveonline.com/Character/${charID}_256.jpg",
                "oauth2" => "eve",
                "refresh_token" => $userInfo['refreshToken']
        ]);
        $user->save();

        Session::getSession()->set("userID", $id);
        $view->redirect('/', 302);
    }
}
