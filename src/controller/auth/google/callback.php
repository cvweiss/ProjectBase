<?php

namespace Project\Base\Controller\auth\google;

use Project\Base\Session;

class callback
{
    public function doGet($view, $params)
    { 
        $this->validate();

        // Try to get an access token (using the authorization code grant)
        $code = filter_input(INPUT_GET, 'code');
        $provider = $this->getProvider();
        $token = $provider->getAccessToken('authorization_code', ['code' => $code]);

        // Optional: Now you have a token you can look up a users profile data
        // We got an access token, let's now get the owner details
        $ownerDetails = $provider->getResourceOwner($token);

        $id = $ownerDetails->getID();
        $user = \Project\Base\Mongo::findDoc("users", ['id' => $id]);
        if ($user == null) $user = new \Project\Base\MongoDoc("users");

        $user->set("id", $id);
        $user->set("name", $ownerDetails->getName());
        $user->set("email", $ownerDetails->getEmail());
        $user->set("image", $ownerDetails->getAvatar());
        $user->set("oauth2", "google");
        $user->save();

        Session::getSession()->set("userID", $id);
        $view->redirect('/', 302);
    }

    private function validate()
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
}
