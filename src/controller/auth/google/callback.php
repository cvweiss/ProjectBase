<?php

namespace Project\Base\Controller\auth\google;

use Project\Base\Session;
use Project\Base\Db;

class callback
{
    public function doGet($view, $params)
    { 
        $auth = \Project\Base\Config::get("oauth2");

        $google = $auth['google'];

        $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $google['client_id'],
                'clientSecret' => $google['client_secret'],
                'redirectUri'  => $google['redirect_uris'][0]
        ]);

        $session = Session::getSession();
        if (!empty($_GET['error'])) {
            // Got an error, probably user denied access
            $view->redirect('/logout/');

        } elseif (empty($_GET['state']) || ($_GET['state'] !== $session->get('oauth2state'))) {
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

            // Details
            $id = $ownerDetails->getID();
            $email = $ownerDetails->getEmail();
            $name = $ownerDetails->getName();
            $image = $ownerDetails->getAvatar();

            $user = Db::get()->users->findOne(['id' => $id]);
            if ($user == null) $user = new \MongoDB\Model\BSONDocument;
            $user->id = $id;
            $user->name = $name;
            $user->email = $email;
            $user->image = $image;
            $user->oauth2 = "google";
            $options = isset($user->_id) ? [] : ['upsert' => true];
            Db::get()->users->replaceOne($user, $user, $options);

            $session->set("userID", $user->id);
            $view->redirect('/', 302);
        }
    }
}
