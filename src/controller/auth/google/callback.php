<?php

namespace Project\Base\Controller\auth\google;

use Project\Base\Session;

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
        if (!empty($_GET['error']))
        {
            // Got an error, probably user denied access
            $view->redirect('/logout/');

        } elseif (empty($_GET['state']) || ($_GET['state'] !== $session->get('oauth2state')))
        {
            $view->redirect('/logout/');
        } else 
        {
            // Try to get an access token (using the authorization code grant)
            try 
            {
                $token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
            } catch (\Exception $ex) 
            {
                $view->redirect('/auth/google/login', 302);
            }

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the owner details
                $ownerDetails = $provider->getResourceOwner($token);

                // Details
                $id = $ownerDetails->getID();
                $email = $ownerDetails->getEmail();
                $name = $ownerDetails->getName();
                $image = $ownerDetails->getAvatar();

                $user = \Project\Base\Mongo::findDoc("users", ['id' => $id]);
                if ($user == null) $user = new \Project\Base\MongoDoc("users");
                $user->set("id", $id);
                $user->set("name", $name);
                $user->set("email", $email);
                $user->set("image", $image);
                $user->set("oauth2", "google");
                $user->save();

                $session->set("userID", $user->get("id"));
                $view->redirect('/', 302);
            } catch (\Exception $e) {

                // Failed to get user details
                $view->error(0, 'Something went wrong: ' . $e->getMessage(), $params);
            }
        }
    }
}
