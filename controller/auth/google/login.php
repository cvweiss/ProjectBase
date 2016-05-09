<?php

namespace Project\Supply\Controller\auth\google;

class login
{
    function doPage($app, $jade, $view, $values)
    {
        $auth = \Project\Supply\Config::get("oauth2");

        $google = $auth['google'];

        $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $google['client_id'],
                'clientSecret' => $google['client_secret'],
                'redirectUri'  => $google['redirect_uris'][0]
        ]);

        // If we don't have an authorization code then get one
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authUrl);
    }
}
