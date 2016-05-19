<?php

namespace Project\Base\Controller\auth\google;

use Project\Base\Session;
use Project\Base\Db;
use Project\Base\Config;
use Project\Base\Logger;

class callback
{
    public function doGet($view, $params)
    { 
        $auth = Config::get("oauth2");
        $google = $auth['google'];
        $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $google['client_id'],
                'clientSecret' => $google['client_secret'],
                'redirectUri'  => $google['redirect_uris'][0]
        ]);

        $session = Session::getSession();
        if (!empty($_GET['error']) || (empty($_GET['state']) || ($_GET['state'] !== $session->get('oauth2state')))) {
            $view->redirect('/logout/');
        } else {
            // Try to get an access token (using the authorization code grant)
            try {
                $token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
            } catch (\Exception $ex) {
                $view->redirect('/auth/google/login', 302);
            }

            // We got an access token, let's now get the owner details
            $ownerDetails = $provider->getResourceOwner($token);

            // Create (if necessary) and store the user
            $user = Db::get()->users->findOne(['id' => $ownerDetails->getID()]);
            if ($user == null) $user = new \MongoDB\Model\BSONDocument;

            $user->id = $ownerDetails->getID();
            $user->name = $ownerDetails->getName();
            $user->email = $ownerDetails->getEmail();
            $user->image = $ownerDetails->getAvatar();
            $user->oauth2 = "google";
            $options = isset($user->_id) ? [] : ['upsert' => true];
            Db::get()->users->replaceOne($user, $user, $options);

            // User is logged in
            Logger::info($user->name . " (" . $user->email . ") has logged in.");
            $session->set("userID", $user->id);
            $view->redirect('/', 302);
        }
    }
}
