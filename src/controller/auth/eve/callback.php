<?php

namespace cvweiss\projectbase\Controller\auth\eve;

use cvweiss\projectbase\Config;
use cvweiss\projectbase\Mongo;
use cvweiss\projectbase\Session;

class callback
{
    public function doGet($view, $params)
    { 
        $auth = Config::getInstance()->get("oauth2");
        $eve = $auth['eve'];

        $clientID = $eve['client_id'];
        $clientSecret = $eve['client_secret'];

        $url = 'https://login.eveonline.com/oauth/token';
        $verify_url = 'https://login.eveonline.com/oauth/verify';
        $header = 'Authorization: Basic '.base64_encode($clientID . ':' . $clientSecret);
        $fields_string = '';
        $fields = array(
                'grant_type' => 'authorization_code',
                'code' => filter_input(INPUT_GET, 'code'),
                );
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, Config::getInstance()->get("siteName"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result, true);
        if (!isset($json['access_token'])) {
            $view->redirect('/');
        }

        $access_token = $json['access_token'];
        $refresh_token = $json['refresh_token'];
        $ch = curl_init();
        // Get the Character details from SSO
        $header = 'Authorization: Bearer '.$access_token;
        curl_setopt($ch, CURLOPT_URL, $verify_url);
        curl_setopt($ch, CURLOPT_USERAGENT, Config::getInstance()->get("siteName"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);

        $json = json_decode($result, true);
        $charID = $json['CharacterID'];
        $id = "eve:sso:" . $json['CharacterID'];
        $user = Mongo::get()->findDoc("users", ['id' => $id], null, true);

        $user->setAll([
                "id" => $id,
                "name" => $json['CharacterName'],
                "email" => null,
                "image" => "https://imageserver.eveonline.com/Character/${charID}_256.jpg",
                "oauth2" => "eve",
                "refresh_token" => $refresh_token
        ]);
        $user->save();

        Session::getSession()->set("userID", $id);
        $view->redirect('/', 302);
    }
}
