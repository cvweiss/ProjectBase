<?php

namespace cvweiss\projectbase\Controller\auth\google;

use cvweiss\projectbase\Session;
use cvweiss\projectbase\Config;

class login
{
    public function doGet($view, $params)
    {
        $auth = Config::getInstance()->get("oauth2");

        $google = $auth['google'];

        $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $google['client_id'],
                'clientSecret' => $google['client_secret'],
                'redirectUri'  => $google['redirect_uris'][0]
        ]);

        // If we don't have an authorization code then get one
        $authUrl = $provider->getAuthorizationUrl();
        Session::getSession()->set('oauth2state', $provider->getState());
        $view->redirect($authUrl);
    }
}
