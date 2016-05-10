<?php

namespace Project\Supply\Controller\auth\google;

class callback
{
    function doGet($app, $jade, $view, $values)
    { 
        $auth = \Project\Supply\Config::get("oauth2");

        $google = $auth['google'];

        $provider = new \League\OAuth2\Client\Provider\Google([
                'clientId'     => $google['client_id'],
                'clientSecret' => $google['client_secret'],
                'redirectUri'  => $google['redirect_uris'][0]
        ]);

        if (!empty($_GET['error']))
        {

            // Got an error, probably user denied access
            exit('Got error: ' . $_GET['error']);

        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state']))
        {

            // State is invalid, possible CSRF attack in progress
            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else 
        {
            // Try to get an access token (using the authorization code grant)
            try 
            {
                $token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
            } catch (\Exception $ex) 
            {
                $app->redirect('/auth/google/login', 302);
                exit();
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

                $user = \Project\Supply\Mongo::findDoc("users", ['id' => $id]);
                if ($user == null) $user = new \Project\Supply\MongoDoc("users");
                $user->set("id", $id);
                $user->set("name", $name);
                $user->set("email", $email);
                $user->set("image", $image);
                $user->set("oauth2", "google");
                $user->save();

                $_SESSION["user_id"] = $user->get("id");
                $app->redirect('/', 302);
            } catch (\Exception $e) {

                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());
            }
        }
    }
}
