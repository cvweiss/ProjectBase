<?php

namespace Project\Base\Controller\auth\google;

use Project\Base\Session;

class callback
{
    public function doGet($view, $params)
    { 
        unset($params);
        $this->validate($view);

        $ownerDetails = $this->getOwnerDetails();

        $id = $ownerDetails->getID();
        $user = \Project\Base\Mongo::getConn()->findDoc("users", ['id' => $id]);
        if ($user == null) {
            $user = new \Project\Base\MongoDoc("users");
        }

        $user->setAll([
            "id" => $id,
            "name" => $ownerDetails->getName(),
            "email" => $ownerDetails->getEmail(),
            "image" => $ownerDetails->getAvatar(),
            "oauth2" => "google"
        ]);
        $user->save();

        Session::getSession()->set("userID", $id);
        $view->redirect('/', 302);
    }

    private function validate($view)
    {
        $session = Session::getSession();
        $error = filter_input(INPUT_GET, 'error');
        $state = filter_input(INPUT_GET, 'state');
        if (!empty($error) || (empty($state) || ($state !== $session->get('oauth2state')))) {
            $view->redirect('/logout/');
        }
    }

    private function getProvider()
    {
        $auth = \Project\Base\Config::get("oauth2");
        $google = $auth['google'];
        $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $google['client_id'],
                'clientSecret' => $google['client_secret'],
                'redirectUri'  => $google['redirect_uris'][0]
        ]);

        return $provider;
    }

    private function getOwnerDetails()
    {
        $code = filter_input(INPUT_GET, 'code');
        $provider = $this->getProvider();
        $token = $provider->getAccessToken('authorization_code', ['code' => $code]);

        return $provider->getResourceOwner($token);
    }
}
